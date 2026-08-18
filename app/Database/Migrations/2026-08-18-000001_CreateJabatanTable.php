<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJabatanTable extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE `jabatan` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `kode_jabatan` varchar(50) NOT NULL,
                `nama_jabatan` varchar(150) NOT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_jabatan_kode_jabatan` (`kode_jabatan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('jabatan', true);
    }
}
