<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramTables extends Migration
{
    public function up()
    {
        $this->db->query('
            CREATE TABLE `dt_pilar` (
                `id_pilar` int(11) NOT NULL,
                `nama_pilar` varchar(255) NOT NULL,
                `deskripsi_pilar` varchar(255) NOT NULL,
                PRIMARY KEY (`id_pilar`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `ad_kategori_program` (
                `id_kategori_program` int(11) NOT NULL AUTO_INCREMENT,
                `nama_kategori` varchar(255) NOT NULL,
                `deskripsi_kategori` varchar(255) NOT NULL,
                `id_pilar` int(11) NOT NULL,
                `status_kategori` int(11) NOT NULL DEFAULT 1,
                PRIMARY KEY (`id_kategori_program`),
                KEY `id_pilar` (`id_pilar`),
                CONSTRAINT `ad_kategori_program_ibfk_1` FOREIGN KEY (`id_pilar`) REFERENCES `dt_pilar` (`id_pilar`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query("
            CREATE TABLE `ad_program` (
                `id_program` int(11) NOT NULL AUTO_INCREMENT,
                `id_kategori_program` int(11) NOT NULL,
                `nama_program` varchar(255) NOT NULL,
                `deskripsi_program` varchar(255) DEFAULT NULL,
                `jenis_formulir` int(11) DEFAULT NULL COMMENT '0 - Lainnya, 1 - Individu, 2 - Sekolah, 3 - Usaha, 4 - Masjid',
                `status_program` tinyint(4) NOT NULL DEFAULT 1,
                PRIMARY KEY (`id_program`),
                KEY `id_kategori_program` (`id_kategori_program`),
                CONSTRAINT `ad_program_ibfk_1` FOREIGN KEY (`id_kategori_program`) REFERENCES `ad_kategori_program` (`id_kategori_program`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('ad_program', true);
        $this->forge->dropTable('ad_kategori_program', true);
        $this->forge->dropTable('dt_pilar', true);
    }
}
