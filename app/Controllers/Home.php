<?php

namespace App\Controllers;

use App\Models\AjuanModel;
use App\Models\BeritaAcaraModel;
use App\Models\KategoriPenerimaModel;
use App\Models\KategoriProgramModel;
use App\Models\PilarModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Home extends BaseController
{
    public function index(): string
    {
        $dari   = $this->request->getGet('dari') ?: null;
        $sampai = $this->request->getGet('sampai') ?: null;
        $tahun  = $sampai ? (int) substr($sampai, 0, 4) : (int) date('Y');

        $ajuanModel            = new AjuanModel();
        $beritaAcaraModel      = new BeritaAcaraModel();
        $kategoriPenerimaModel = new KategoriPenerimaModel();
        $kategoriProgramModel  = new KategoriProgramModel();
        $pilarModel            = new PilarModel();

        // Same 5-way status grouping used on the Ajuan Individu/Lembaga tabs,
        // so the dashboard's counts always agree with those pages.
        $statusGroups = [
            'countAjuanBaru'    => [0, 1],
            'countAjuanProses'  => [2, 3, 4, 5],
            'countAjuanRutin'   => [8],
            'countAjuanSelesai' => [7, 9],
            'countAjuanDitolak' => [6],
        ];

        $counts = [];
        foreach ($statusGroups as $key => $statuses) {
            $query = $ajuanModel->whereIn('status_ajuan', $statuses);
            if ($dari) {
                $query->where('tgl_diajukan >=', $dari);
            }
            if ($sampai) {
                $query->where('tgl_diajukan <=', $sampai . ' 23:59:59');
            }
            $counts[$key] = $query->countAllResults();
        }

        $totalDanaQuery = $beritaAcaraModel->selectSum('nilai_penyerahan');
        if ($dari) {
            $totalDanaQuery->where('tanggal_penyerahan >=', $dari);
        }
        if ($sampai) {
            $totalDanaQuery->where('tanggal_penyerahan <=', $sampai);
        }
        $totalDana = (float) ($totalDanaQuery->first()['nilai_penyerahan'] ?? 0);

        // Dana tersalurkan per kategori penerima (asnaf), grouped by fund source.
        $danaBuckets = [];
        foreach (['Zakat', 'Infaq Umum', 'Infaq Terikat', 'Amil'] as $bucket) {
            $nominalPerKategori = [];
            foreach ($kategoriPenerimaModel->where('id_dana_dari', $bucket)->findAll() as $ktg) {
                $query = $beritaAcaraModel->selectSum('nilai_penyerahan')->where('kategori_penerima', $ktg['id_kategori_penerima']);
                if ($dari) {
                    $query->where('tanggal_penyerahan >=', $dari);
                }
                if ($sampai) {
                    $query->where('tanggal_penyerahan <=', $sampai);
                }
                $nominal = (float) ($query->first()['nilai_penyerahan'] ?? 0);
                if ($nominal > 0) {
                    $nominalPerKategori[$ktg['ket_kategori_penerima']] = $nominal;
                }
            }
            arsort($nominalPerKategori);
            $danaBuckets[$bucket] = $nominalPerKategori;
        }

        // Ajuan count per program category.
        $countPerKategori = [];
        foreach ($kategoriProgramModel->findAll() as $ktg) {
            $query = $ajuanModel->where('id_kategori_program', $ktg['id_kategori_program']);
            if ($dari) {
                $query->where('tgl_diajukan >=', $dari);
            }
            if ($sampai) {
                $query->where('tgl_diajukan <=', $sampai . ' 23:59:59');
            }
            $jumlah = $query->countAllResults();
            if ($jumlah > 0) {
                $countPerKategori[$ktg['nama_kategori']] = $jumlah;
            }
        }
        arsort($countPerKategori);

        // Ajuan count per pilar.
        $countPerPilar = [];
        foreach ($pilarModel->findAll() as $pilar) {
            $query = $ajuanModel
                ->join('ad_kategori_program ktg', 'ktg.id_kategori_program = tr_ajuan.id_kategori_program')
                ->where('ktg.id_pilar', $pilar['id_pilar']);
            if ($dari) {
                $query->where('tgl_diajukan >=', $dari);
            }
            if ($sampai) {
                $query->where('tgl_diajukan <=', $sampai . ' 23:59:59');
            }
            $jumlah = $query->countAllResults();
            if ($jumlah > 0) {
                $countPerPilar[$pilar['nama_pilar']] = $jumlah;
            }
        }
        arsort($countPerPilar);

        // Ajuan count per month, for the filtered (or current) year.
        $countPerBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $countPerBulan[$i] = $ajuanModel
                ->where('MONTH(tgl_diajukan)', $i)
                ->where('YEAR(tgl_diajukan)', $tahun)
                ->countAllResults();
        }

        $data = array_merge($counts, [
            'title'            => 'Dashboard',
            'activeMenu'       => 'dashboard',
            'dari'             => $dari,
            'sampai'           => $sampai,
            'tahun'            => $tahun,
            'totalDana'        => $totalDana,
            'danaBuckets'      => $danaBuckets,
            'countPerKategori' => $countPerKategori,
            'countPerPilar'    => $countPerPilar,
            'countPerBulan'    => $countPerBulan,
        ]);

        return view('dashboard', $data);
    }

    /**
     * Downloads an Excel recap of every ajuan filed within the same
     * dari/sampai window as the dashboard's own filter (tgl_diajukan-based,
     * matching Home::index()'s filtering).
     */
    public function exportExcel()
    {
        $dari   = $this->request->getGet('dari') ?: null;
        $sampai = $this->request->getGet('sampai') ?: null;

        $query = (new AjuanModel())
            ->select('
                tr_ajuan.nomor_ajuan, tr_ajuan.tgl_diajukan, tr_ajuan.jenis_ajuan,
                tr_ajuan.nik, tr_ajuan.nilai_diajukan, tr_ajuan.nilai_disetujui, tr_ajuan.sifat_bantuan,
                tr_pemohon.nama_pemohon,
                ms_individu.nama_mustahik, ms_lembaga.nama_lembaga,
                ad_program.nama_program, ad_kategori_program.nama_kategori, dt_pilar.nama_pilar,
                dt_status_ajuan.keterangan_status
            ')
            ->join('tr_pemohon', 'tr_pemohon.nik = tr_ajuan.nik', 'left')
            ->join('tr_individu', 'tr_individu.nomor_ajuan = tr_ajuan.nomor_ajuan', 'left')
            ->join('ms_individu', 'ms_individu.nik = tr_individu.nik', 'left')
            ->join('tr_lembaga', 'tr_lembaga.nomor_ajuan = tr_ajuan.nomor_ajuan', 'left')
            ->join('ms_lembaga', 'ms_lembaga.nomor_legalitas = tr_lembaga.nomor_lembaga', 'left')
            ->join('ad_program', 'ad_program.id_program = tr_ajuan.id_program', 'left')
            ->join('ad_kategori_program', 'ad_kategori_program.id_kategori_program = tr_ajuan.id_kategori_program', 'left')
            ->join('dt_pilar', 'dt_pilar.id_pilar = ad_kategori_program.id_pilar', 'left')
            ->join('dt_status_ajuan', 'dt_status_ajuan.id_status = tr_ajuan.status_ajuan', 'left');

        if ($dari) {
            $query->where('tr_ajuan.tgl_diajukan >=', $dari);
        }
        if ($sampai) {
            $query->where('tr_ajuan.tgl_diajukan <=', $sampai . ' 23:59:59');
        }

        $rows = $query->orderBy('tr_ajuan.tgl_diajukan', 'ASC')->findAll();

        // Disbursed amount is tracked per-ajuan in ad_berita_acara, separately
        // (and possibly more than once), so sum it per nomor_ajuan rather than
        // joining directly (which would multiply the ajuan rows above).
        $danaPerAjuan = [];
        foreach ((new BeritaAcaraModel())->select('nomor_ajuan, SUM(nilai_penyerahan) as total')->groupBy('nomor_ajuan')->findAll() as $d) {
            $danaPerAjuan[$d['nomor_ajuan']] = (float) $d['total'];
        }

        $kolom = [
            'No', 'Nomor Ajuan', 'Tanggal Diajukan', 'Jenis Ajuan', 'Pilar', 'Kategori Program',
            'Kegiatan', 'Nama Pemohon', 'NIK Pemohon', 'Penerima Manfaat', 'Status', 'Sifat Bantuan',
            'Nilai Diajukan (Rp)', 'Nilai Disetujui (Rp)', 'Dana Tersalurkan (Rp)',
        ];
        $lastCol = chr(ord('A') + count($kolom) - 1);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Pengajuan');

        $periode = ($dari || $sampai)
            ? 'Periode: ' . ($dari ? format_tanggal_indo($dari) : 'Awal') . ' s/d ' . ($sampai ? format_tanggal_indo($sampai) : 'Sekarang')
            : 'Periode: Seluruh Data';

        $sheet->mergeCells('A1:' . $lastCol . '1')->setCellValue('A1', 'REKAP PENGAJUAN LAZISMU SRAGEN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:' . $lastCol . '2')->setCellValue('A2', $periode);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:' . $lastCol . '3')->setCellValue('A3', 'Dicetak pada: ' . format_tanggal_indo(date('Y-m-d H:i:s'), true) . ' WIB');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headerRow = 5;
        foreach ($kolom as $i => $label) {
            $sheet->setCellValue(chr(ord('A') + $i) . $headerRow, $label);
        }
        $headerRange = 'A' . $headerRow . ':' . $lastCol . $headerRow;
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FF7F00');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

        $baris             = $headerRow + 1;
        $totalDiajukan     = 0.0;
        $totalDisetujui    = 0.0;
        $totalTersalurkan  = 0.0;

        foreach ($rows as $i => $r) {
            $namaPenerima   = $r['nama_mustahik'] ?? $r['nama_lembaga'] ?? '-';
            $dana           = $danaPerAjuan[$r['nomor_ajuan']] ?? 0.0;
            $nilaiDiajukan  = (float) $r['nilai_diajukan'];
            $nilaiDisetujui = $r['nilai_disetujui'] !== null ? (float) $r['nilai_disetujui'] : null;

            $totalDiajukan    += $nilaiDiajukan;
            $totalDisetujui   += (float) $nilaiDisetujui;
            $totalTersalurkan += $dana;

            $sheet->setCellValue('A' . $baris, $i + 1);
            $sheet->setCellValueExplicit('B' . $baris, $r['nomor_ajuan'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $baris, format_tanggal_indo($r['tgl_diajukan']), DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $baris, $r['jenis_ajuan']);
            $sheet->setCellValue('E' . $baris, $r['nama_pilar'] ?? '-');
            $sheet->setCellValue('F' . $baris, $r['nama_kategori'] ?? '-');
            $sheet->setCellValue('G' . $baris, $r['nama_program'] ?? '-');
            $sheet->setCellValue('H' . $baris, $r['nama_pemohon'] ?? '-');
            $sheet->setCellValueExplicit('I' . $baris, $r['nik'], DataType::TYPE_STRING);
            $sheet->setCellValue('J' . $baris, $namaPenerima);
            $sheet->setCellValue('K' . $baris, $r['keterangan_status'] ?? '-');
            $sheet->setCellValue('L' . $baris, $r['sifat_bantuan'] ?? '-');
            $sheet->setCellValue('M' . $baris, $nilaiDiajukan);
            $sheet->setCellValue('N' . $baris, $nilaiDisetujui);
            $sheet->setCellValue('O' . $baris, $dana);

            $baris++;
        }

        $sheet->mergeCells('A' . $baris . ':L' . $baris)->setCellValue('A' . $baris, 'TOTAL (' . count($rows) . ' ajuan)');
        $sheet->getStyle('A' . $baris)->getFont()->setBold(true);
        $sheet->getStyle('A' . $baris)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('M' . $baris, $totalDiajukan);
        $sheet->setCellValue('N' . $baris, $totalDisetujui);
        $sheet->setCellValue('O' . $baris, $totalTersalurkan);
        $sheet->getStyle('M' . $baris . ':O' . $baris)->getFont()->setBold(true);

        $sheet->getStyle('M' . ($headerRow + 1) . ':O' . $baris)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $baris)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A' . ($headerRow + 1));

        $namaFile = 'Rekap-Pengajuan-' . ($dari ?: 'awal') . '_sd_' . ($sampai ?: 'sekarang') . '.xlsx';

        ob_start();
        IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
        $content = ob_get_clean();

        return $this->response
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $namaFile . '"')
            ->setBody($content);
    }
}
