<?php

namespace App\Controllers;

use App\Models\KategoriProgramModel;
use App\Models\PilarModel;
use App\Models\ProgramModel;
use App\Models\SyaratModel;

class LandingController extends BaseController
{
    public function index()
    {
        $pilar    = (new PilarModel())->orderBy('id_pilar', 'ASC')->findAll();
        $kategori = (new KategoriProgramModel())->where('status_kategori', 1)->orderBy('nama_kategori', 'ASC')->findAll();
        $program  = (new ProgramModel())->where('status_program', 1)->orderBy('nama_program', 'ASC')->findAll();
        $syarat   = (new SyaratModel())->findAll();

        // Group children by their parent id so the tree can be built without
        // an N+1 query per pilar/kategori/program.
        $syaratByProgram = [];
        foreach ($syarat as $s) {
            $syaratByProgram[$s['id_program']][] = $s;
        }

        $programByKategori = [];
        foreach ($program as $p) {
            $p['syarat']                                   = $syaratByProgram[$p['id_program']] ?? [];
            $programByKategori[$p['id_kategori_program']][] = $p;
        }

        $kategoriByPilar = [];
        foreach ($kategori as $k) {
            $k['program']                    = $programByKategori[$k['id_kategori_program']] ?? [];
            $kategoriByPilar[$k['id_pilar']][] = $k;
        }

        foreach ($pilar as &$pl) {
            $pl['kategori'] = $kategoriByPilar[$pl['id_pilar']] ?? [];
        }
        unset($pl);

        return view('landing/index', [
            'title'         => 'Beranda',
            'pilar'         => $pilar,
            'totalPilar'    => count($pilar),
            'totalKategori' => count($kategori),
            'totalProgram'  => count($program),
        ]);
    }
}
