<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class LogAjuanModel extends Model
{
    protected $table      = 'ad_log_ajuan';
    protected $primaryKey = 'id_log_ajuan';
    protected $allowedFields = ['nomor_ajuan', 'status_ajuan', 'tanggal_log', 'catatan_log', 'log_created_by'];
    protected $useTimestamps = true;
    protected $createdField  = 'log_created_at';
    protected $updatedField  = 'log_updated_at';

    /**
     * $tanggalLog lets callers backdate the log to when the event actually
     * happened (e.g. a board meeting held a few days ago), rather than
     * always stamping "now".
     */
    public function catat($nomor_ajuan, $status_ajuan, $catatan_log = '', $log_created_by = null, ?string $tanggalLog = null)
    {
        return $this->insert([
            'nomor_ajuan'    => $nomor_ajuan,
            'status_ajuan'   => $status_ajuan,
            'tanggal_log'    => $tanggalLog ?? Time::now()->toDateTimeString(),
            'catatan_log'    => $catatan_log,
            'log_created_by' => $log_created_by,
        ]);
    }
}
