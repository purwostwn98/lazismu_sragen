<?php

namespace App\Models;

use CodeIgniter\Model;

class KecamatanModel extends Model
{
    protected $table      = 'dt_kecamatan';
    protected $primaryKey = 'id_kecamatan';
    protected $allowedFields = ['id_kecamatan', 'nama_kecamatan', 'id_kabupaten'];
    public $timestamps = false;
}
