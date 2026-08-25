<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterLembagaModel extends Model
{
    protected $table      = 'ms_lembaga';
    protected $primaryKey = 'id_ms_lembaga';
    protected $allowedFields = [
        'nama_lembaga', 'bidang', 'tahun_berdiri', 'nomor_legalitas', 'npwp', 'alamat',
        'dusun', 'rt', 'rw', 'provinsi', 'kabupaten', 'kecamatan', 'desa',
        'nomor_telepon', 'email', 'website', 'nama_pj', 'jabatan_pj', 'sumber_pendanaan', 'nomor_rekening',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /** Insert a new master record, or refresh an existing one by nomor_legalitas. */
    public function upsert(array $data): void
    {
        $nomor    = strtoupper(trim($data['nomor_legalitas']));
        $existing = $this->where('nomor_legalitas', $nomor)->first();

        $data['nomor_legalitas'] = $nomor;

        if ($existing) {
            $this->update($existing['id_ms_lembaga'], $data);
        } else {
            $this->insert($data);
        }
    }

    public function withWilayah()
    {
        return $this->select('
                ms_lembaga.*,
                dt_provinsi.nama_provinsi,
                dt_kabupaten.nama_kabupaten,
                dt_kecamatan.nama_kecamatan,
                dt_kelurahan.nama_kelurahan
            ')
            ->join('dt_provinsi', 'dt_provinsi.id_provinsi = ms_lembaga.provinsi', 'left')
            ->join('dt_kabupaten', 'dt_kabupaten.id_kabupaten = ms_lembaga.kabupaten', 'left')
            ->join('dt_kecamatan', 'dt_kecamatan.id_kecamatan = ms_lembaga.kecamatan', 'left')
            ->join('dt_kelurahan', 'dt_kelurahan.id_kelurahan = ms_lembaga.desa', 'left');
    }
}
