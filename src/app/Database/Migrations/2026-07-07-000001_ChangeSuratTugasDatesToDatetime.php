<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Surat Tugas assignments can start/end at a specific time of day (e.g. a
 * survey visit scheduled for the afternoon), not just a calendar date.
 */
class ChangeSuratTugasDatesToDatetime extends Migration
{
    public function up()
    {
        $this->db->query('
            ALTER TABLE `ad_surat_tugas`
                MODIFY COLUMN `tanggal_mulai` datetime NOT NULL,
                MODIFY COLUMN `tanggal_selesai` datetime NOT NULL
        ');
    }

    public function down()
    {
        $this->db->query('
            ALTER TABLE `ad_surat_tugas`
                MODIFY COLUMN `tanggal_mulai` date NOT NULL,
                MODIFY COLUMN `tanggal_selesai` date NOT NULL
        ');
    }
}
