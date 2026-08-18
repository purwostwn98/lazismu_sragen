<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJabatanPenjabatTable extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE `jabatan_penjabat` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_jabatan` int(11) NOT NULL,
                `nama_jabatan` varchar(150) NOT NULL,
                `nama_penjabat` varchar(150) NOT NULL,
                `email` varchar(100) DEFAULT NULL,
                `mulai_tahun` year(4) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `id_jabatan` (`id_jabatan`),
                CONSTRAINT `jabatan_penjabat_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('jabatan_penjabat', true);
    }
}
