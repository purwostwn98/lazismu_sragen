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
                tr_pemohon.nama_pemohon, tr_pemohon.telepon AS telepon_pemohon,
                ms_individu.nama_mustahik, ms_lembaga.nama_lembaga,
                COALESCE(ms_individu.no_handphone, ms_lembaga.nomor_telepon) AS telepon_mustahik,
                COALESCE(ms_individu.alamat, ms_lembaga.alamat) AS alamat_mustahik,
                COALESCE(kel_i.nama_kelurahan, kel_l.nama_kelurahan) AS kelurahan_mustahik,
                COALESCE(kec_i.nama_kecamatan, kec_l.nama_kecamatan) AS kecamatan_mustahik,
                COALESCE(kab_i.nama_kabupaten, kab_l.nama_kabupaten) AS kabupaten_mustahik,
                ad_program.nama_program, ad_kategori_program.nama_kategori, dt_pilar.nama_pilar,
                dt_status_ajuan.keterangan_status
            ')
            ->join('tr_pemohon', 'tr_pemohon.nik = tr_ajuan.nik', 'left')
            ->join('tr_individu', 'tr_individu.nomor_ajuan = tr_ajuan.nomor_ajuan', 'left')
            ->join('ms_individu', 'ms_individu.nik = tr_individu.nik', 'left')
            ->join('tr_lembaga', 'tr_lembaga.nomor_ajuan = tr_ajuan.nomor_ajuan', 'left')
            // Same blank-legalitas guard as LembagaModel::withLembaga(): an
            // ajuan saved with an empty Nomor Legalitas must not pick up
            // whichever ms_lembaga row also has an empty one.
            ->join('ms_lembaga', "ms_lembaga.nomor_legalitas = tr_lembaga.nomor_lembaga AND tr_lembaga.nomor_lembaga != ''", 'left')
            ->join('dt_kelurahan kel_i', 'kel_i.id_kelurahan = ms_individu.desa', 'left')
            ->join('dt_kecamatan kec_i', 'kec_i.id_kecamatan = ms_individu.kecamatan', 'left')
            ->join('dt_kabupaten kab_i', 'kab_i.id_kabupaten = ms_individu.kabupaten', 'left')
            ->join('dt_kelurahan kel_l', 'kel_l.id_kelurahan = ms_lembaga.desa', 'left')
            ->join('dt_kecamatan kec_l', 'kec_l.id_kecamatan = ms_lembaga.kecamatan', 'left')
            ->join('dt_kabupaten kab_l', 'kab_l.id_kabupaten = ms_lembaga.kabupaten', 'left')
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
            'Kegiatan', 'Nama Pemohon', 'NIK Pemohon', 'No. HP Pemohon', 'Penerima Manfaat',
            'No. HP Mustahik', 'Alamat Mustahik', 'Status', 'Sifat Bantuan',
            'Nilai Diajukan (Rp)', 'Nilai Disetujui (Rp)', 'Dana Tersalurkan (Rp)',
        ];
        // Column letter by header label, so adding a column never leaves the
        // nominal/TOTAL cells below pointing at the wrong letter.
        $col     = static fn (string $label): string => chr(ord('A') + array_search($label, $kolom, true));
        $lastCol = chr(ord('A') + count($kolom) - 1);
        $teks    = static fn ($v): string => ($v !== null && trim((string) $v) !== '') ? trim((string) $v) : '-';

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

            $alamat = implode(', ', array_filter([
                trim((string) ($r['alamat_mustahik'] ?? '')),
                $r['kelurahan_mustahik'] ?? null,
                $r['kecamatan_mustahik'] ?? null,
                $r['kabupaten_mustahik'] ?? null,
            ]));

            $sheet->setCellValue($col('No') . $baris, $i + 1);
            $sheet->setCellValueExplicit($col('Nomor Ajuan') . $baris, $r['nomor_ajuan'], DataType::TYPE_STRING);
            $sheet->setCellValueExplicit($col('Tanggal Diajukan') . $baris, format_tanggal_indo($r['tgl_diajukan']), DataType::TYPE_STRING);
            $sheet->setCellValue($col('Jenis Ajuan') . $baris, $r['jenis_ajuan']);
            $sheet->setCellValue($col('Pilar') . $baris, $r['nama_pilar'] ?? '-');
            $sheet->setCellValue($col('Kategori Program') . $baris, $r['nama_kategori'] ?? '-');
            $sheet->setCellValue($col('Kegiatan') . $baris, $r['nama_program'] ?? '-');
            $sheet->setCellValue($col('Nama Pemohon') . $baris, $r['nama_pemohon'] ?? '-');
            $sheet->setCellValueExplicit($col('NIK Pemohon') . $baris, $r['nik'], DataType::TYPE_STRING);
            // Phone numbers as text so Excel keeps the leading 0.
            $sheet->setCellValueExplicit($col('No. HP Pemohon') . $baris, $teks($r['telepon_pemohon'] ?? null), DataType::TYPE_STRING);
            $sheet->setCellValue($col('Penerima Manfaat') . $baris, $namaPenerima);
            $sheet->setCellValueExplicit($col('No. HP Mustahik') . $baris, $teks($r['telepon_mustahik'] ?? null), DataType::TYPE_STRING);
            $sheet->setCellValue($col('Alamat Mustahik') . $baris, $teks($alamat));
            $sheet->setCellValue($col('Status') . $baris, $r['keterangan_status'] ?? '-');
            $sheet->setCellValue($col('Sifat Bantuan') . $baris, $r['sifat_bantuan'] ?? '-');
            $sheet->setCellValue($col('Nilai Diajukan (Rp)') . $baris, $nilaiDiajukan);
            $sheet->setCellValue($col('Nilai Disetujui (Rp)') . $baris, $nilaiDisetujui);
            $sheet->setCellValue($col('Dana Tersalurkan (Rp)') . $baris, $dana);

            $baris++;
        }

        $colDiajukan    = $col('Nilai Diajukan (Rp)');
        $colTersalurkan = $col('Dana Tersalurkan (Rp)');
        $colSebelumNilai = chr(ord($colDiajukan) - 1);

        $sheet->mergeCells('A' . $baris . ':' . $colSebelumNilai . $baris)->setCellValue('A' . $baris, 'TOTAL (' . count($rows) . ' ajuan)');
        $sheet->getStyle('A' . $baris)->getFont()->setBold(true);
        $sheet->getStyle('A' . $baris)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue($colDiajukan . $baris, $totalDiajukan);
        $sheet->setCellValue($col('Nilai Disetujui (Rp)') . $baris, $totalDisetujui);
        $sheet->setCellValue($colTersalurkan . $baris, $totalTersalurkan);
        $sheet->getStyle($colDiajukan . $baris . ':' . $colTersalurkan . $baris)->getFont()->setBold(true);

        $sheet->getStyle($colDiajukan . ($headerRow + 1) . ':' . $colTersalurkan . $baris)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $baris)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', $lastCol) as $huruf) {
            $sheet->getColumnDimension($huruf)->setAutoSize(true);
        }
        // Addresses can run long - fixed width + wrap instead of one huge column.
        $colAlamat = $col('Alamat Mustahik');
        $sheet->getColumnDimension($colAlamat)->setAutoSize(false)->setWidth(45);
        $sheet->getStyle($colAlamat . ($headerRow + 1) . ':' . $colAlamat . $baris)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
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
