<?php

namespace App\Models;

use CodeIgniter\Model;

class KabupatenModel extends Model
{
    protected $table      = 'dt_kabupaten';
    protected $primaryKey = 'id_kabupaten';
    protected $allowedFields = ['id_kabupaten', 'nama_kabupaten', 'id_provinsi'];
    public $timestamps = false;
}
