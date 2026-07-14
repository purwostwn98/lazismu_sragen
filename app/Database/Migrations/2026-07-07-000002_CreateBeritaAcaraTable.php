<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Berita Acara: the formal handover/acquittal document recording that aid
 * was actually disbursed for an ajuan (who handed it over, who received it,
 * when/where/how). Like Surat Tugas, an ajuan can have several of these
 * over its lifecycle (e.g. staged disbursements).
 */
class CreateBeritaAcaraTable extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE `ad_berita_acara` (
                `id_berita_acara` int(11) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` char(8) NOT NULL,
                `yang_bertandatangan` varchar(150) NOT NULL,
                `tanggal_penyerahan` date NOT NULL,
                `lokasi_penyerahan` varchar(255) NOT NULL,
                `berdasarkan` enum('Rapat Pengurus','Laporan/SPJ') NOT NULL,
                `bentuk_penyerahan` tinyint(4) NOT NULL,
                `nilai_penyerahan` decimal(15,2) NOT NULL,
                `rekening_penyerahan` varchar(150) DEFAULT NULL,
                `nama_barang` varchar(255) DEFAULT NULL,
                `dana_dari` enum('Zakat','Infak Sedekah','Amil','Sosial Keagamaan Lainnya','Infaq Umum','Infaq Terikat','DSKL') NOT NULL,
                `kategori_penerima` int(11) NOT NULL,
                `yang_menerima` varchar(150) NOT NULL,
                `file_berita_acara` varchar(255) DEFAULT NULL,
                `ba_created_at` datetime DEFAULT NULL,
                `ba_updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_berita_acara`),
                KEY `nomor_ajuan` (`nomor_ajuan`),
                CONSTRAINT `ad_berita_acara_ibfk_1` FOREIGN KEY (`nomor_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `ad_berita_acara`');
    }
}
