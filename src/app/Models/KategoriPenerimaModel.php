<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriPenerimaModel extends Model
{
    protected $table      = 'dt_kategori_penerima';
    protected $primaryKey = 'id_kategori_penerima';
    protected $allowedFields = ['id_dana_dari', 'ket_kategori_penerima'];
    public $timestamps = false;
}
