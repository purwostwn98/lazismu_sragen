<?php

namespace App\Models;

use CodeIgniter\Model;

class IndividuModel extends Model
{
    protected $table      = 'tr_individu';
    protected $primaryKey = 'id_individu';
    protected $allowedFields = ['nomor_ajuan', 'nik'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * The mustahik profile itself lives in ms_individu (the master registry);
     * this pulls it in so callers get the same shape as before normalization.
     */
    public function withMustahik()
    {
        return $this->select('tr_individu.id_individu, tr_individu.nomor_ajuan, ms_individu.*')
            ->join('ms_individu', 'ms_individu.nik = tr_individu.nik');
    }

    /** Same as withMustahik(), plus resolved wilayah/pekerjaan/penghasilan labels for display. */
    public function withMustahikLengkap()
    {
        return $this->withMustahik()
            ->select('
                dt_provinsi.nama_provinsi, dt_kabupaten.nama_kabupaten,
                dt_kecamatan.nama_kecamatan, dt_kelurahan.nama_kelurahan,
                dt_pekerjaan.nama_pekerjaan, dt_penghasilan.label_penghasilan
            ')
            ->join('dt_provinsi', 'dt_provinsi.id_provinsi = ms_individu.provinsi', 'left')
            ->join('dt_kabupaten', 'dt_kabupaten.id_kabupaten = ms_individu.kabupaten', 'left')
            ->join('dt_kecamatan', 'dt_kecamatan.id_kecamatan = ms_individu.kecamatan', 'left')
            ->join('dt_kelurahan', 'dt_kelurahan.id_kelurahan = ms_individu.desa', 'left')
            ->join('dt_pekerjaan', 'dt_pekerjaan.id_pekerjaan = ms_individu.pekerjaan', 'left')
            ->join('dt_penghasilan', 'dt_penghasilan.id_penghasilan = ms_individu.penghasilan', 'left');
    }
}
