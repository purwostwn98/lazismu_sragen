<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratTugasModel extends Model
{
    protected $table      = 'ad_surat_tugas';
    protected $primaryKey = 'id_surat_tugas';
    protected $allowedFields = [
        'nomor_ajuan', 'nama_penanggung_jawab', 'jabatan', 'berdasarkan', 'agenda',
        'tanggal_mulai', 'tanggal_selesai', 'lokasi', 'file_surat_tugas',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'st_created_at';
    protected $updatedField  = 'st_updated_at';
}
