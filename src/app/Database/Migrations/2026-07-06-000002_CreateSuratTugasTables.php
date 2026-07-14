<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Surat Tugas: an internal assignment letter authorizing named staff to
 * carry out a task (survey, disbursement, monitoring, coordination) for a
 * specific ajuan. An ajuan can have several of these issued over its
 * lifecycle, each with its own list of delegated staff (ad_delegasi_st).
 */
class CreateSuratTugasTables extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE `ad_surat_tugas` (
                `id_surat_tugas` int(11) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` char(8) NOT NULL,
                `nama_penanggung_jawab` varchar(150) NOT NULL,
                `jabatan` varchar(100) NOT NULL,
                `berdasarkan` enum('Rapat Pengurus','Surat/Proposal') NOT NULL,
                `agenda` enum('Survey','Menyalurkan','Monev','Koordinasi','Lainnya') NOT NULL,
                `tanggal_mulai` date NOT NULL,
                `tanggal_selesai` date NOT NULL,
                `lokasi` varchar(255) NOT NULL,
                `file_surat_tugas` varchar(255) DEFAULT NULL,
                `st_created_at` datetime DEFAULT NULL,
                `st_updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_surat_tugas`),
                KEY `nomor_ajuan` (`nomor_ajuan`),
                CONSTRAINT `ad_surat_tugas_ibfk_1` FOREIGN KEY (`nomor_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->db->query('
            CREATE TABLE `ad_delegasi_st` (
                `id_delegasi` int(11) NOT NULL AUTO_INCREMENT,
                `id_surat_tugas` int(11) NOT NULL,
                `nama_delegasi` varchar(150) NOT NULL,
                PRIMARY KEY (`id_delegasi`),
                KEY `id_surat_tugas` (`id_surat_tugas`),
                CONSTRAINT `ad_delegasi_st_ibfk_1` FOREIGN KEY (`id_surat_tugas`) REFERENCES `ad_surat_tugas` (`id_surat_tugas`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `ad_delegasi_st`');
        $this->db->query('DROP TABLE IF EXISTS `ad_surat_tugas`');
    }
}
