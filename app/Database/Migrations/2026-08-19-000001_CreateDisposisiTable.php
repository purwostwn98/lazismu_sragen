<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * ad_disposisi stores the review/recommendation submitted at each stage of
 * the disposisi workflow (Surveyor, Kepala Divisi Program, Manager, Badan
 * Pengurus). Every stage writes its own row here, distinguished by `oleh`,
 * so an ajuan accumulates a full history of reviews as it moves through the
 * workflow (append-only, like ad_berita_acara/ad_surat_tugas — not upserted).
 */
class CreateDisposisiTable extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE `ad_disposisi` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` char(8) NOT NULL,
                `deskripsi` text NOT NULL,
                `dokumentasi` varchar(255) DEFAULT NULL,
                `rekomendasi` tinyint(1) NOT NULL,
                `nominal_rekomendasi` decimal(15,2) NOT NULL DEFAULT 0,
                `oleh` enum('Surveyor','Kepala Divisi Program','Manager','Badan Pengurus') NOT NULL,
                `nama_petugas` varchar(150) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `nomor_ajuan` (`nomor_ajuan`),
                CONSTRAINT `ad_disposisi_ibfk_1` FOREIGN KEY (`nomor_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('ad_disposisi', true);
    }
}
