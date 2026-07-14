<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePemohonTable extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE `tr_pemohon` (
                `nik` varchar(100) NOT NULL,
                `nama_pemohon` varchar(255) NOT NULL,
                `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
                `tempat_lahir` varchar(255) NOT NULL,
                `tanggal_lahir` date NOT NULL,
                `id_provinsi` int(11) NOT NULL,
                `id_kabupaten` int(11) NOT NULL,
                `id_kecamatan` int(11) NOT NULL,
                `id_kelurahan` int(11) NOT NULL,
                `alamat_detail` varchar(255) NOT NULL,
                `agama` enum('Islam','Protestan','Katolik','Hindhu','Budha') NOT NULL,
                `telepon` varchar(15) NOT NULL,
                `email` varchar(255) NOT NULL,
                `pemohon_created_at` datetime NOT NULL,
                `pemohon_updated_at` datetime NOT NULL,
                PRIMARY KEY (`nik`),
                KEY `id_provinsi` (`id_provinsi`),
                KEY `id_kabupaten` (`id_kabupaten`),
                KEY `id_kecamatan` (`id_kecamatan`),
                KEY `id_kelurahan` (`id_kelurahan`),
                CONSTRAINT `tr_pemohon_ibfk_1` FOREIGN KEY (`id_provinsi`) REFERENCES `dt_provinsi` (`id_provinsi`) ON UPDATE CASCADE,
                CONSTRAINT `tr_pemohon_ibfk_2` FOREIGN KEY (`id_kabupaten`) REFERENCES `dt_kabupaten` (`id_kabupaten`) ON UPDATE CASCADE,
                CONSTRAINT `tr_pemohon_ibfk_3` FOREIGN KEY (`id_kecamatan`) REFERENCES `dt_kecamatan` (`id_kecamatan`) ON UPDATE CASCADE,
                CONSTRAINT `tr_pemohon_ibfk_4` FOREIGN KEY (`id_kelurahan`) REFERENCES `dt_kelurahan` (`id_kelurahan`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('tr_pemohon', true);
    }
}
