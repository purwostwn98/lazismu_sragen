<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterLembagaAndIndividuTables extends Migration
{
    public function up()
    {
        // Master registry of institutions applying on behalf of mustahik,
        // keyed by nomor_legalitas so the same institution isn't re-entered
        // from scratch on every application (mirrors lazismu_reborn's
        // ms_lembaga + MasterLembagaModel).
        $this->db->query("
            CREATE TABLE `ms_lembaga` (
                `id_ms_lembaga` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `nama_lembaga` varchar(200) NOT NULL,
                `bidang` varchar(200) NOT NULL,
                `tahun_berdiri` year(4) DEFAULT NULL,
                `nomor_legalitas` varchar(100) NOT NULL,
                `npwp` varchar(30) DEFAULT NULL,
                `alamat` text NOT NULL,
                `nomor_telepon` varchar(20) NOT NULL,
                `email` varchar(100) NOT NULL,
                `website` varchar(150) DEFAULT NULL,
                `nama_pj` varchar(150) NOT NULL,
                `jabatan_pj` varchar(100) NOT NULL,
                `sumber_pendanaan` varchar(200) DEFAULT NULL,
                `nomor_rekening` varchar(50) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_ms_lembaga`),
                UNIQUE KEY `uq_ms_lembaga_nomor_legalitas` (`nomor_legalitas`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // Analogous master registry for individual mustahik, keyed by NIK.
        // lazismu_reborn has no equivalent table — tr_individu there is
        // purely per-ajuan. This adds the same "check before fill, upsert
        // after submit" convenience reborn only offers for institutions.
        $this->db->query("
            CREATE TABLE `ms_individu` (
                `nik` varchar(100) NOT NULL,
                `nama_mustahik` varchar(255) NOT NULL,
                `kelamin_mustahik` enum('Laki-laki','Perempuan') NOT NULL,
                `agama_mustahik` enum('Islam','Protestan','Katolik','Hindhu','Budha') DEFAULT NULL,
                `tempat_lahir` varchar(255) DEFAULT NULL,
                `tgl_lahir` date DEFAULT NULL,
                `alamat` varchar(255) NOT NULL,
                `provinsi` int(11) DEFAULT NULL,
                `kabupaten` int(11) DEFAULT NULL,
                `kecamatan` int(11) DEFAULT NULL,
                `desa` int(11) DEFAULT NULL,
                `status_pendidikan` enum('SD','SMP','SMA/SMK','Diploma','Sarjana','Pascasarjana','tidak tamat SD','lainnya') DEFAULT NULL,
                `status_marital` enum('Lajang','Menikah','Cerai','Lainnya') DEFAULT NULL,
                `pekerjaan` int(11) DEFAULT NULL,
                `penghasilan` int(11) DEFAULT NULL,
                `jml_keluarga` int(11) DEFAULT NULL,
                `no_handphone` varchar(16) DEFAULT NULL,
                `email` varchar(100) DEFAULT NULL,
                `foto_ktp` varchar(255) DEFAULT NULL,
                `kk` varchar(100) DEFAULT NULL,
                `foto_kk` varchar(255) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`nik`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('ms_individu', true);
        $this->forge->dropTable('ms_lembaga', true);
    }
}
