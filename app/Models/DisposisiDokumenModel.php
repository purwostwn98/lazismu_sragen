<?php

namespace App\Models;

use CodeIgniter\Model;

class DisposisiDokumenModel extends Model
{
    protected $table         = 'ad_disposisi_dokumen';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['id_disposisi', 'nama_file', 'nama_asli', 'keterangan'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /** Max extra documents per disposisi result, and max size of each (bytes). */
    public const MAKS_DOKUMEN = 5;
    public const MAKS_UKURAN  = 1048576; // 1 MB

    /** Allowed extensions => the mime types each may really be. */
    public const FORMAT = [
        'pdf'  => ['application/pdf'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
    ];
}
