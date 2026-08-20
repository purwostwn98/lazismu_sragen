<?php

namespace App\Models;

use CodeIgniter\Model;

class DisposisiModel extends Model
{
    protected $table         = 'ad_disposisi';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nomor_ajuan', 'deskripsi', 'dokumentasi', 'rekomendasi',
        'nominal_rekomendasi', 'oleh', 'nama_petugas',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
