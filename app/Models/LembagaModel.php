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
     */
    public function withLembaga()
    {
        return $this->select('tr_lembaga.id_lembaga, tr_lembaga.nomor_ajuan, tr_lembaga.nomor_lembaga, ms_lembaga.nama_lembaga, ms_lembaga.alamat AS alamat_lembaga, ms_lembaga.nomor_telepon')
            ->join('ms_lembaga', 'ms_lembaga.nomor_legalitas = tr_lembaga.nomor_lembaga');
    }
}
