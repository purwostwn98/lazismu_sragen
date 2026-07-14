<?php

namespace App\Models;

use CodeIgniter\Model;

class PenghimpunanSubktgModel extends Model
{
    protected $table      = 'dt_penghimpunan_subktg';
    protected $primaryKey = 'id_sub_ktg';
    protected $allowedFields = ['id_ktg_himpun', 'kode_subktg', 'keterangan_sub'];
    public $timestamps = false;
}
