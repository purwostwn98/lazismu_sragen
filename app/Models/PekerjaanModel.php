<?php

namespace App\Models;

use CodeIgniter\Model;

class PekerjaanModel extends Model
{
    protected $table      = 'dt_pekerjaan';
    protected $primaryKey = 'id_pekerjaan';
    protected $allowedFields = ['nama_pekerjaan'];
    public $timestamps = false;
}
