<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvinsiModel extends Model
{
    protected $table      = 'dt_provinsi';
    protected $primaryKey = 'id_provinsi';
    protected $allowedFields = ['id_provinsi', 'nama_provinsi', 'status_provinsi'];
    public $timestamps = false;
}
