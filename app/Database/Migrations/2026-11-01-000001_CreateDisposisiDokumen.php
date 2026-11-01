<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Extra supporting documents attached to a disposisi result (up to 5 per
 * result, each with an optional keterangan) - on top of the single
 * ad_disposisi.dokumentasi file, which stays as it is. Purely additive: a
 * new table, no existing column or row is touched.
 */
class CreateDisposisiDokumen extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `ad_disposisi_dokumen` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_disposisi` int(11) NOT NULL,
                `nama_file` varchar(255) NOT NULL,
                `nama_asli` varchar(255) NOT NULL,
                `keterangan` varchar(255) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `id_disposisi` (`id_disposisi`),
                CONSTRAINT `ad_disposisi_dokumen_ibfk_1` FOREIGN KEY (`id_disposisi`) REFERENCES `ad_disposisi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('ad_disposisi_dokumen', true);
    }
}
