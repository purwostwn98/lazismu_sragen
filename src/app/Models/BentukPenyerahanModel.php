<?php

namespace App\Models;

use CodeIgniter\Model;

class BentukPenyerahanModel extends Model
{
    protected $table      = 'dt_bentuk_penyerahan';
    protected $primaryKey = 'id_bentuk_penyerahan';
    protected $allowedFields = ['id_bentuk_penyerahan', 'ket_bentuk_penyerahan'];
    public $timestamps = false;
}
