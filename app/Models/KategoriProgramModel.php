<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriProgramModel extends Model
{
    protected $table      = 'ad_kategori_program';
    protected $primaryKey = 'id_kategori_program';
    protected $allowedFields = ['id_pilar', 'nama_kategori', 'deskripsi_kategori', 'status_kategori'];
    public $timestamps = false;
}
