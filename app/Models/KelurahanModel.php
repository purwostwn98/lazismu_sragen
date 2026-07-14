<?php

namespace App\Models;

use CodeIgniter\Model;

class KelurahanModel extends Model
{
    protected $table      = 'dt_kelurahan';
    protected $primaryKey = 'id_kelurahan';
    protected $allowedFields = ['id_kelurahan', 'nama_kelurahan', 'id_kecamatan'];
    public $timestamps = false;
}
