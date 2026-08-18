<?php

namespace App\Models;

use CodeIgniter\Model;

class JabatanPenjabatModel extends Model
{
    protected $table         = 'jabatan_penjabat';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['id_jabatan', 'nama_jabatan', 'nama_penjabat', 'email', 'mulai_tahun'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
