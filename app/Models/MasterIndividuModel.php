<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterIndividuModel extends Model
{
    protected $table      = 'ms_individu';
    protected $primaryKey = 'nik';
    protected $allowedFields = [
        'nik', 'nama_mustahik', 'kelamin_mustahik', 'agama_mustahik', 'tempat_lahir', 'tgl_lahir',
        'alamat', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'status_pendidikan', 'status_marital',
        'pekerjaan', 'penghasilan', 'jml_keluarga', 'no_handphone', 'email', 'foto_ktp', 'kk', 'foto_kk',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /** Insert a new master record, or refresh an existing one by NIK. */
    public function upsert(array $data): void
    {
        if ($this->find($data['nik'])) {
            $this->update($data['nik'], $data);
        } else {
            $this->insert($data);
        }
    }

    public function withWilayah()
    {
        return $this->select('
                ms_individu.*,
                dt_provinsi.nama_provinsi,
                dt_kabupaten.nama_kabupaten,
                dt_kecamatan.nama_kecamatan,
                dt_kelurahan.nama_kelurahan,
                dt_pekerjaan.nama_pekerjaan,
                dt_penghasilan.label_penghasilan
            ')
            ->join('dt_provinsi', 'dt_provinsi.id_provinsi = ms_individu.provinsi', 'left')
            ->join('dt_kabupaten', 'dt_kabupaten.id_kabupaten = ms_individu.kabupaten', 'left')
            ->join('dt_kecamatan', 'dt_kecamatan.id_kecamatan = ms_individu.kecamatan', 'left')
            ->join('dt_kelurahan', 'dt_kelurahan.id_kelurahan = ms_individu.desa', 'left')
            ->join('dt_pekerjaan', 'dt_pekerjaan.id_pekerjaan = ms_individu.pekerjaan', 'left')
            ->join('dt_penghasilan', 'dt_penghasilan.id_penghasilan = ms_individu.penghasilan', 'left');
    }
}
