<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWilayahTables extends Migration
{
    public function up()
    {
        $this->db->query('
            CREATE TABLE `dt_provinsi` (
                `id_provinsi` int(11) NOT NULL,
                `nama_provinsi` varchar(200) NOT NULL,
                `status_provinsi` int(11) NOT NULL DEFAULT 1,
                PRIMARY KEY (`id_provinsi`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `dt_kabupaten` (
                `id_kabupaten` int(11) NOT NULL,
                `nama_kabupaten` varchar(200) NOT NULL,
                `id_provinsi` int(11) NOT NULL,
                PRIMARY KEY (`id_kabupaten`),
                KEY `id_provinsi` (`id_provinsi`),
                CONSTRAINT `dt_kabupaten_ibfk_1` FOREIGN KEY (`id_provinsi`) REFERENCES `dt_provinsi` (`id_provinsi`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `dt_kecamatan` (
                `id_kecamatan` int(11) NOT NULL,
                `nama_kecamatan` varchar(255) NOT NULL,
                `id_kabupaten` int(11) NOT NULL,
                PRIMARY KEY (`id_kecamatan`),
                KEY `id_kabupaten` (`id_kabupaten`),
                CONSTRAINT `dt_kecamatan_ibfk_1` FOREIGN KEY (`id_kabupaten`) REFERENCES `dt_kabupaten` (`id_kabupaten`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `dt_kelurahan` (
                `id_kelurahan` int(11) NOT NULL,
                `nama_kelurahan` varchar(255) NOT NULL,
                `id_kecamatan` int(11) NOT NULL,
                PRIMARY KEY (`id_kelurahan`),
                KEY `id_kecamatan` (`id_kecamatan`),
                CONSTRAINT `dt_kelurahan_ibfk_1` FOREIGN KEY (`id_kecamatan`) REFERENCES `dt_kecamatan` (`id_kecamatan`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');
    }

    public function down()
    {
        $this->forge->dropTable('dt_kelurahan', true);
        $this->forge->dropTable('dt_kecamatan', true);
        $this->forge->dropTable('dt_kabupaten', true);
        $this->forge->dropTable('dt_provinsi', true);
    }
}
