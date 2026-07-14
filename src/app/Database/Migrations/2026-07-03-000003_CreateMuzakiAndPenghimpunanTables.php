<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMuzakiAndPenghimpunanTables extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE `dt_muzaki` (
                `id_muzaki` varchar(255) NOT NULL,
                `nama_muzaki` varchar(255) NOT NULL,
                `alamat_muzaki` text DEFAULT NULL,
                `tlp_muzaki` varchar(20) NOT NULL,
                `email_muzaki` varchar(100) NOT NULL,
                `jenis_muzaki` enum('Laki-laki','Perempuan','Lembaga') DEFAULT NULL,
                `mzk_crat` datetime DEFAULT NULL,
                `mzk_uat` datetime DEFAULT NULL,
                `is_dosen` int(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id_muzaki`),
                UNIQUE KEY `email_muzaki` (`email_muzaki`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->db->query('
            CREATE TABLE `dt_penghimpunan_ktg` (
                `id_ktg` int(11) NOT NULL AUTO_INCREMENT,
                `keterangan_ktg` varchar(255) NOT NULL,
                `kode_ktg` varchar(100) DEFAULT NULL,
                PRIMARY KEY (`id_ktg`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `dt_penghimpunan_subktg` (
                `id_sub_ktg` int(11) NOT NULL AUTO_INCREMENT,
                `id_ktg_himpun` int(11) NOT NULL,
                `kode_subktg` varchar(100) DEFAULT NULL,
                `keterangan_sub` varchar(255) NOT NULL,
                PRIMARY KEY (`id_sub_ktg`),
                KEY `id_ktg_himpun` (`id_ktg_himpun`),
                CONSTRAINT `dt_penghimpunan_subktg_ibfk_1` FOREIGN KEY (`id_ktg_himpun`) REFERENCES `dt_penghimpunan_ktg` (`id_ktg`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `tr_penghimpunan` (
                `id_himpun` varchar(255) NOT NULL,
                `email_muzaki` varchar(100) DEFAULT NULL,
                `tanggal_himpun` date DEFAULT NULL,
                `ktg_himpun` int(11) DEFAULT NULL,
                `sub_ktg_himpun` int(11) DEFAULT NULL,
                `jumlah_himpun` double DEFAULT NULL,
                `via_himpun` varchar(255) DEFAULT NULL,
                `tgl_setor_bank` date DEFAULT NULL,
                `kwitansi_bank` varchar(255) DEFAULT NULL,
                `nm_bank` varchar(255) DEFAULT NULL,
                `himpun_crat` datetime DEFAULT NULL,
                `himpun_upat` datetime DEFAULT NULL,
                PRIMARY KEY (`id_himpun`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');
    }

    public function down()
    {
        $this->forge->dropTable('tr_penghimpunan', true);
        $this->forge->dropTable('dt_penghimpunan_subktg', true);
        $this->forge->dropTable('dt_penghimpunan_ktg', true);
        $this->forge->dropTable('dt_muzaki', true);
    }
}
