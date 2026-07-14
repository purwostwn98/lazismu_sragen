<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class PenghimpunanModel extends Model
{
    protected $table      = 'tr_penghimpunan';
    protected $primaryKey = 'id_himpun';
    protected $allowedFields = [
        'id_himpun', 'email_muzaki', 'tanggal_himpun', 'ktg_himpun',
        'sub_ktg_himpun', 'jumlah_himpun', 'via_himpun', 'tgl_setor_bank',
        'kwitansi_bank', 'nm_bank',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'himpun_crat';
    protected $updatedField  = 'himpun_upat';

    public function simpan(
        $email_muzaki,
        $tanggal_himpun,
        $ktg_himpun,
        $sub_ktg_himpun,
        $jumlah_himpun,
        $via_himpun,
        $tgl_setor_bank,
        $kwitansi_bank,
        $nm_bank
    ) {
        $kode      = $via_himpun === 'transfer' ? 'B17' : 'A17';
        $timestamp = Time::now()->getTimestamp();
        $prefix    = strtoupper(substr($email_muzaki, 0, 3));
        $id_himpun = $kode . '-' . $prefix . $timestamp;

        $data = [
            'id_himpun'      => $id_himpun,
            'email_muzaki'   => $email_muzaki,
            'tanggal_himpun' => $tanggal_himpun,
            'ktg_himpun'     => $ktg_himpun,
            'sub_ktg_himpun' => $sub_ktg_himpun,
            'jumlah_himpun'  => $jumlah_himpun,
            'via_himpun'     => $via_himpun,
            'tgl_setor_bank' => $tgl_setor_bank,
            'kwitansi_bank'  => $kwitansi_bank,
            'nm_bank'        => $nm_bank,
        ];

        $this->transBegin();
        $this->insert($data);
        $ok = $this->transStatus() !== false;
        $ok ? $this->transCommit() : $this->transRollback();

        return $ok;
    }

    public function hapus($id)
    {
        $this->transBegin();
        $this->delete($id);
        $ok = $this->transStatus() !== false;
        $ok ? $this->transCommit() : $this->transRollback();

        return $ok;
    }

    public function withMuzaki()
    {
        return $this->select('tr_penghimpunan.*, dt_muzaki.nama_muzaki')
            ->join('dt_muzaki', 'dt_muzaki.email_muzaki = tr_penghimpunan.email_muzaki', 'left');
    }
}
