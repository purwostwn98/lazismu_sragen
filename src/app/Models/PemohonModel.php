<?php

namespace App\Models;

use CodeIgniter\Model;

class PemohonModel extends Model
{
    protected $table      = 'tr_pemohon';
    protected $primaryKey = 'nik';
    protected $allowedFields = [
        'nik', 'nama_pemohon', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
        'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan',
        'alamat_detail', 'agama', 'telepon', 'email',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'pemohon_created_at';
    protected $updatedField  = 'pemohon_updated_at';

    public function withWilayah()
    {
        return $this->select('
                tr_pemohon.*,
                dt_provinsi.nama_provinsi,
                dt_kabupaten.nama_kabupaten,
                dt_kecamatan.nama_kecamatan,
                dt_kelurahan.nama_kelurahan
            ')
            ->join('dt_provinsi', 'dt_provinsi.id_provinsi = tr_pemohon.id_provinsi', 'left')
            ->join('dt_kabupaten', 'dt_kabupaten.id_kabupaten = tr_pemohon.id_kabupaten', 'left')
            ->join('dt_kecamatan', 'dt_kecamatan.id_kecamatan = tr_pemohon.id_kecamatan', 'left')
            ->join('dt_kelurahan', 'dt_kelurahan.id_kelurahan = tr_pemohon.id_kelurahan', 'left');
    }
}
