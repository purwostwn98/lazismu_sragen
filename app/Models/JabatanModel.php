<?php

namespace App\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
    protected $table         = 'jabatan';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['kode_jabatan', 'nama_jabatan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
