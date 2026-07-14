<?php

namespace App\Controllers;

use App\Models\AjuanModel;
use App\Models\BeritaAcaraModel;
use App\Models\KategoriPenerimaModel;
use App\Models\KategoriProgramModel;
use App\Models\PilarModel;

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
}
