<?php

namespace App\Models;

use CodeIgniter\Model;

class FormB3Model extends Model
{
    protected $table      = 'tr_form_b3';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nomor_ajuan', 'id_kategori_program', 'id_program', 'dana_dari', 'kategori_penerima', 'bentuk_penyerahan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /** Insert a new B3 record for this ajuan, or refresh the existing one (one row per nomor_ajuan). */
    public function upsert(string $nomorAjuan, array $data): void
    {
        $existing = $this->where('nomor_ajuan', $nomorAjuan)->first();

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert(array_merge(['nomor_ajuan' => $nomorAjuan], $data));
        }
    }
}
