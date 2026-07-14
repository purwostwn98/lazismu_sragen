<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * B3's fund-source/disbursement classification also needs to record which
 * program the admin has finalized for disbursement, independent of (and
 * possibly differing from) the program the applicant originally requested
 * on tr_ajuan.
 */
class AddProgramToFormB3 extends Migration
{
    public function up()
    {
        $this->db->query('
            ALTER TABLE `tr_form_b3`
                ADD COLUMN `id_kategori_program` int(11) DEFAULT NULL AFTER `nomor_ajuan`,
                ADD COLUMN `id_program` int(11) DEFAULT NULL AFTER `id_kategori_program`,
                ADD KEY `id_program` (`id_program`),
                ADD CONSTRAINT `tr_form_b3_ibfk_2` FOREIGN KEY (`id_program`) REFERENCES `ad_program` (`id_program`) ON UPDATE CASCADE
        ');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `tr_form_b3` DROP FOREIGN KEY `tr_form_b3_ibfk_2`');
        $this->db->query('
            ALTER TABLE `tr_form_b3`
                DROP COLUMN `id_kategori_program`,
                DROP COLUMN `id_program`
        ');
    }
}
