<?php

namespace App\Models;

use CodeIgniter\Model;

class PenghimpunanKtgModel extends Model
{
    protected $table      = 'dt_penghimpunan_ktg';
    protected $primaryKey = 'id_ktg';
    protected $allowedFields = ['keterangan_ktg', 'kode_ktg'];
    public $timestamps = false;
}
