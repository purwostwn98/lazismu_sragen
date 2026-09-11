<?php

namespace App\Models;

use CodeIgniter\Model;

class LembagaModel extends Model
{
    protected $table      = 'tr_lembaga';
    protected $primaryKey = 'id_lembaga';
    protected $allowedFields = ['nomor_ajuan', 'nomor_lembaga'];
    public $timestamps = false;

    /**
     * The lembaga profile itself lives in ms_lembaga (the master registry);
     * this pulls it in so callers get the same shape as before normalization.
     * alamat_lembaga is kept as an explicit alias since existing callers
     * (PDF exports, disposisi views) already read $lembaga['alamat_lembaga'].
     */
    public function withLembaga()
    {
        return $this->select('
                tr_lembaga.id_lembaga, tr_lembaga.nomor_ajuan, tr_lembaga.nomor_lembaga,
                ms_lembaga.*, ms_lembaga.alamat AS alamat_lembaga,
                dt_provinsi.nama_provinsi, dt_kabupaten.nama_kabupaten,
                dt_kecamatan.nama_kecamatan, dt_kelurahan.nama_kelurahan
            ')
            ->join('ms_lembaga', 'ms_lembaga.nomor_legalitas = tr_lembaga.nomor_lembaga')
            ->join('dt_provinsi', 'dt_provinsi.id_provinsi = ms_lembaga.provinsi', 'left')
            ->join('dt_kabupaten', 'dt_kabupaten.id_kabupaten = ms_lembaga.kabupaten', 'left')
            ->join('dt_kecamatan', 'dt_kecamatan.id_kecamatan = ms_lembaga.kecamatan', 'left')
            ->join('dt_kelurahan', 'dt_kelurahan.id_kelurahan = ms_lembaga.desa', 'left');
    }
}
