<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public
$routes->get('/', 'LandingController::index');
$routes->get('login', 'AuthController::index');
$routes->post('login/attempt', 'AuthController::attempt');
$routes->get('logout', 'AuthController::logout');

// Wilayah (cascading region dropdowns, JSON) — used by both the public
// pengajuan forms and the admin Pemohon/Ajuan forms, so it must stay public.
$routes->get('wilayah/kabupaten/(:num)', 'WilayahController::kabupaten/$1');
$routes->get('wilayah/kecamatan/(:num)', 'WilayahController::kecamatan/$1');
$routes->get('wilayah/kelurahan/(:num)', 'WilayahController::kelurahan/$1');

// Pengajuan (public aid-program submission flow)
$routes->get('pengajuan', 'PengajuanController::index');
$routes->post('pengajuan/cek', 'PengajuanController::cekNik');
$routes->get('pengajuan/biodata', 'PengajuanController::biodata');
$routes->post('pengajuan/biodata/store', 'PengajuanController::storeBiodata');
$routes->get('pengajuan/formulir', 'PengajuanController::formulir');
$routes->post('pengajuan/formulir/store', 'PengajuanController::storeFormulir');
$routes->post('pengajuan/cek-individu', 'PengajuanController::cekIndividu');
$routes->post('pengajuan/cek-lembaga', 'PengajuanController::cekLembaga');
$routes->get('pengajuan/status', 'PengajuanController::statusForm');
$routes->post('pengajuan/status/cek', 'PengajuanController::cekStatus');
$routes->get('pengajuan/sukses/(:segment)', 'PengajuanController::sukses/$1');
$routes->get('pengajuan/sukses/(:segment)/cetak', 'PengajuanController::pdfBukti/$1');
$routes->get('pengajuan/verifikasi/(:segment)', 'PengajuanController::verifikasi/$1');

// Everything below requires a logged-in session.
$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
    $routes->get('dashboard', 'Home::index');
    $routes->get('dashboard/analitik', 'AnalitikController::index');

    // Muzaki (donors)
    $routes->get('muzaki', 'MuzakiController::index');
    $routes->post('muzaki/store', 'MuzakiController::store');
    $routes->post('muzaki/update/(:segment)', 'MuzakiController::update/$1');
    $routes->post('muzaki/delete/(:segment)', 'MuzakiController::delete/$1');

    // Pilar (top-level program hierarchy)
    $routes->get('pilar', 'PilarController::index');
    $routes->post('pilar/store', 'PilarController::store');
    $routes->post('pilar/update/(:num)', 'PilarController::update/$1');
    $routes->post('pilar/delete/(:num)', 'PilarController::delete/$1');

    // Kategori Program
    $routes->get('kategori-program', 'KategoriProgramController::index');
    $routes->post('kategori-program/store', 'KategoriProgramController::store');
    $routes->post('kategori-program/update/(:num)', 'KategoriProgramController::update/$1');
    $routes->post('kategori-program/delete/(:num)', 'KategoriProgramController::delete/$1');

    // Program
    $routes->get('program', 'ProgramController::index');
    $routes->post('program/store', 'ProgramController::store');
    $routes->post('program/update/(:num)', 'ProgramController::update/$1');
    $routes->post('program/delete/(:num)', 'ProgramController::delete/$1');

    // Organisasi: Jabatan & Penjabat
    $routes->get('jabatan', 'JabatanController::index');
    $routes->post('jabatan/store', 'JabatanController::store');
    $routes->post('jabatan/update/(:num)', 'JabatanController::update/$1');
    $routes->post('jabatan/delete/(:num)', 'JabatanController::delete/$1');

    $routes->get('penjabat', 'PenjabatController::index');
    $routes->post('penjabat/store', 'PenjabatController::store');
    $routes->post('penjabat/update/(:num)', 'PenjabatController::update/$1');
    $routes->post('penjabat/delete/(:num)', 'PenjabatController::delete/$1');

    // Penghimpunan (fundraising)
    $routes->get('penghimpunan', 'PenghimpunanController::index');
    $routes->post('penghimpunan/store', 'PenghimpunanController::store');
    $routes->post('penghimpunan/delete/(:segment)', 'PenghimpunanController::delete/$1');

    // Pemohon (applicants)
    $routes->get('pemohon', 'PemohonController::index');
    $routes->post('pemohon/store', 'PemohonController::store');
    $routes->post('pemohon/update/(:segment)', 'PemohonController::update/$1');
    $routes->post('pemohon/delete/(:segment)', 'PemohonController::delete/$1');

    // Mustahik (aid recipient master registry) — split into Individu (ms_individu)
    // and Lembaga (ms_lembaga) sub-sections.
    $routes->get('mustahik', static fn () => redirect()->to(base_url('mustahik/individu')));
    $routes->get('mustahik/individu', 'MustahikController::individu');
    $routes->post('mustahik/individu/store', 'MustahikController::storeIndividu');
    $routes->post('mustahik/individu/update/(:segment)', 'MustahikController::updateIndividu/$1');
    $routes->post('mustahik/individu/delete/(:segment)', 'MustahikController::deleteIndividu/$1');
    $routes->get('mustahik/individu/(:segment)/dokumen/(:segment)', 'MustahikController::dokumenIndividu/$1/$2');

    $routes->get('mustahik/lembaga', 'MustahikController::lembaga');
    $routes->post('mustahik/lembaga/store', 'MustahikController::storeLembaga');
    $routes->post('mustahik/lembaga/update/(:num)', 'MustahikController::updateLembaga/$1');
    $routes->post('mustahik/lembaga/delete/(:num)', 'MustahikController::deleteLembaga/$1');

    // Ajuan (applications) workflow
    $routes->get('ajuan', 'AjuanController::index');
    $routes->get('ajuan/individu', 'AjuanController::individu');
    $routes->get('ajuan/lembaga', 'AjuanController::lembaga');
    $routes->get('ajuan/rutin', 'AjuanController::rutin');
    $routes->get('ajuan/create', 'AjuanController::create');
    $routes->post('ajuan/store', 'AjuanController::store');
    $routes->post('ajuan/cek-individu', 'AjuanController::cekIndividu');
    $routes->post('ajuan/cek-lembaga', 'AjuanController::cekLembaga');
    $routes->post('ajuan/delete/(:segment)', 'AjuanController::delete/$1');
    $routes->get('ajuan/(:segment)', 'AjuanController::show/$1');
    $routes->post('ajuan/(:segment)/status', 'AjuanController::updateStatus/$1');
    $routes->post('ajuan/(:segment)/mustahik', 'AjuanController::updateMustahik/$1');
    $routes->get('ajuan/(:segment)/mustahik/(:segment)', 'AjuanController::dokumenMustahik/$1/$2');
    $routes->post('ajuan/(:segment)/b3', 'AjuanController::simpanB3/$1');
    $routes->post('ajuan/(:segment)/surat-tugas', 'AjuanController::simpanSuratTugas/$1');
    $routes->post('ajuan/surat-tugas/(:num)/upload', 'AjuanController::uploadBuktiSuratTugas/$1');
    $routes->get('ajuan/surat-tugas/(:num)/bukti', 'AjuanController::fileSuratTugas/$1');
    $routes->get('ajuan/surat-tugas/(:num)/cetak', 'AjuanController::pdfSuratTugas/$1');
    $routes->post('ajuan/(:segment)/berita-acara', 'AjuanController::simpanBeritaAcara/$1');
    $routes->post('ajuan/berita-acara/(:num)/upload', 'AjuanController::uploadBuktiBeritaAcara/$1');
    $routes->post('ajuan/berita-acara/(:num)/delete', 'AjuanController::deleteBeritaAcara/$1');
    $routes->get('ajuan/berita-acara/(:num)/bukti', 'AjuanController::fileBeritaAcara/$1');
    $routes->get('ajuan/berita-acara/(:num)/cetak', 'AjuanController::pdfBeritaAcara/$1');
    $routes->get('ajuan/berita-acara/(:num)/preview', 'AjuanController::previewBeritaAcara/$1');
    $routes->get('ajuan/berita-acara/(:num)/kuitansi', 'AjuanController::pdfC17Kwitansi/$1');
    $routes->post('ajuan/(:segment)/dokumentasi', 'AjuanController::storeDokumentasi/$1');
    $routes->post('ajuan/(:segment)/kuitansi', 'AjuanController::storeKuitansi/$1');

    // Disposisi (ajuan worklist per role/stage)
    $routes->get('disposisi/dashboard', 'DisposisiController::dashboard');
    $routes->get('disposisi/surveyor', 'DisposisiController::surveyor');
    $routes->get('disposisi/survey/(:segment)', 'DisposisiController::survey/$1');
    $routes->post('disposisi/survey/(:segment)/store', 'DisposisiController::storeSurvey/$1');
    $routes->get('disposisi/dokumentasi/(:num)', 'DisposisiController::fileDokumentasi/$1');
    $routes->get('disposisi/kepala-divisi-program', 'DisposisiController::kepalaDivisiProgram');
    $routes->get('disposisi/kepala-divisi-program/(:segment)', 'DisposisiController::reviewKepalaDivisiProgram/$1');
    $routes->post('disposisi/kepala-divisi-program/(:segment)/store', 'DisposisiController::storeKepalaDivisiProgram/$1');
    $routes->get('disposisi/manager', 'DisposisiController::manager');
    $routes->get('disposisi/manager/(:segment)', 'DisposisiController::reviewManager/$1');
    $routes->post('disposisi/manager/(:segment)/store', 'DisposisiController::storeManager/$1');
    $routes->get('disposisi/badan-pengurus', 'DisposisiController::badanPengurus');
    $routes->get('disposisi/badan-pengurus/(:segment)', 'DisposisiController::reviewBadanPengurus/$1');
    $routes->post('disposisi/badan-pengurus/(:segment)/store', 'DisposisiController::storeBadanPengurus/$1');
});
