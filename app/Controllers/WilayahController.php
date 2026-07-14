<?php

namespace App\Controllers;

use App\Models\KabupatenModel;
use App\Models\KecamatanModel;
use App\Models\KelurahanModel;
use App\Models\ProvinsiModel;

/**
 * JSON endpoints backing the cascading provinsi/kabupaten/kecamatan/kelurahan
 * dropdowns used on the Pemohon form.
 */
class WilayahController extends BaseController
{
    public function provinsi()
    {
        return $this->response->setJSON(
            (new ProvinsiModel())->orderBy('nama_provinsi', 'ASC')->findAll()
        );
    }

    public function kabupaten(int $idProvinsi)
    {
        return $this->response->setJSON(
            (new KabupatenModel())->where('id_provinsi', $idProvinsi)->orderBy('nama_kabupaten', 'ASC')->findAll()
        );
    }

    public function kecamatan(int $idKabupaten)
    {
        return $this->response->setJSON(
            (new KecamatanModel())->where('id_kabupaten', $idKabupaten)->orderBy('nama_kecamatan', 'ASC')->findAll()
        );
    }

    public function kelurahan(int $idKecamatan)
    {
        return $this->response->setJSON(
            (new KelurahanModel())->where('id_kecamatan', $idKecamatan)->orderBy('nama_kelurahan', 'ASC')->findAll()
        );
    }
}
