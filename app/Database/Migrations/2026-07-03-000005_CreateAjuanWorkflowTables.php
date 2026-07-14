<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAjuanWorkflowTables extends Migration
{
    public function up()
    {
        // --- Lookup tables ---
        $this->db->query('
            CREATE TABLE `dt_status_ajuan` (
                `id_status` int(11) NOT NULL,
                `keterangan_status` varchar(100) NOT NULL,
                PRIMARY KEY (`id_status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `dt_pekerjaan` (
                `id_pekerjaan` int(11) NOT NULL AUTO_INCREMENT,
                `nama_pekerjaan` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id_pekerjaan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query('
            CREATE TABLE `dt_penghasilan` (
                `id_penghasilan` int(11) NOT NULL AUTO_INCREMENT,
                `label_penghasilan` varchar(255) NOT NULL,
                PRIMARY KEY (`id_penghasilan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        $this->db->query("
            CREATE TABLE `dt_kategori_penerima` (
                `id_kategori_penerima` int(11) NOT NULL AUTO_INCREMENT,
                `id_dana_dari` enum('Zakat','Infak Sedekah','Amil','Sosial Keagaamaan Lainnya','Infaq Umum','Infaq Terikat','DSKL') NOT NULL,
                `ket_kategori_penerima` varchar(100) NOT NULL,
                PRIMARY KEY (`id_kategori_penerima`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        $this->db->query('
            CREATE TABLE `dt_bentuk_penyerahan` (
                `id_bentuk_penyerahan` tinyint(4) NOT NULL,
                `ket_bentuk_penyerahan` varchar(100) NOT NULL,
                PRIMARY KEY (`id_bentuk_penyerahan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // --- Core ajuan (proposal/application) ---
        $this->db->query("
            CREATE TABLE `tr_ajuan` (
                `id_ajuan` int(11) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` char(8) NOT NULL,
                `nik` varchar(100) NOT NULL,
                `id_kategori_program` int(11) NOT NULL,
                `id_program` int(11) NOT NULL,
                `nilai_diajukan` double NOT NULL,
                `deskripsi_ajuan` text NOT NULL,
                `jenis_ajuan` enum('Individu','Lembaga') NOT NULL,
                `file_formulir` varchar(255) DEFAULT NULL,
                `file_proposal` varchar(255) DEFAULT NULL,
                `status_ajuan` int(11) NOT NULL,
                `status_tersalurkan` enum('--','belum','tersalurkan sebagian','tersalurkan semua') DEFAULT '--',
                `tgl_diajukan` datetime NOT NULL,
                `nilai_disetujui` double DEFAULT NULL,
                `sifat_bantuan` enum('Insidental','Rutin','Rutin Closed') DEFAULT 'Insidental',
                `edit_ajuan` enum('Open','Closed') NOT NULL DEFAULT 'Closed',
                `last_updated_at` datetime NOT NULL,
                PRIMARY KEY (`id_ajuan`),
                UNIQUE KEY `nomor_ajuan` (`nomor_ajuan`),
                KEY `id_program` (`id_program`),
                KEY `nik` (`nik`),
                KEY `status_ajuan` (`status_ajuan`),
                KEY `id_kategori_program` (`id_kategori_program`),
                CONSTRAINT `tr_ajuan_ibfk_1` FOREIGN KEY (`id_program`) REFERENCES `ad_program` (`id_program`) ON UPDATE CASCADE,
                CONSTRAINT `tr_ajuan_ibfk_2` FOREIGN KEY (`nik`) REFERENCES `tr_pemohon` (`nik`) ON UPDATE CASCADE,
                CONSTRAINT `tr_ajuan_ibfk_3` FOREIGN KEY (`status_ajuan`) REFERENCES `dt_status_ajuan` (`id_status`) ON UPDATE CASCADE,
                CONSTRAINT `tr_ajuan_ibfk_4` FOREIGN KEY (`id_kategori_program`) REFERENCES `ad_kategori_program` (`id_kategori_program`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // --- Individu (mustahik) detail for jenis_ajuan = 'Individu' ---
        $this->db->query("
            CREATE TABLE `tr_individu` (
                `id_individu` bigint(20) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` varchar(255) NOT NULL,
                `nik` varchar(100) NOT NULL,
                `foto_ktp` varchar(255) DEFAULT NULL,
                `kk` varchar(100) NOT NULL,
                `foto_kk` varchar(255) DEFAULT NULL,
                `nama_mustahik` varchar(255) NOT NULL,
                `tempat_lahir` varchar(255) DEFAULT NULL,
                `tgl_lahir` date DEFAULT NULL,
                `kelamin_mustahik` enum('Laki-laki','Perempuan') NOT NULL,
                `agama_mustahik` enum('Islam','Protestan','Katolik','Hindhu','Budha') DEFAULT NULL,
                `alamat` varchar(255) NOT NULL,
                `provinsi` int(11) NOT NULL,
                `kabupaten` int(11) NOT NULL,
                `kecamatan` int(11) NOT NULL,
                `desa` int(11) NOT NULL,
                `status_pendidikan` enum('SD','SMP','SMA/SMK','Diploma','Sarjana','Pascasarjana','tidak tamat SD','lainnya') DEFAULT NULL,
                `status_marital` enum('Lajang','Menikah','Cerai','Lainnya') DEFAULT NULL,
                `pekerjaan` int(11) NOT NULL,
                `penghasilan` int(11) NOT NULL,
                `jml_keluarga` int(11) NOT NULL,
                `no_handphone` varchar(16) DEFAULT NULL,
                `email` varchar(100) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_individu`),
                KEY `nomor_ajuan` (`nomor_ajuan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // --- Lembaga (institution) detail for jenis_ajuan = 'Lembaga' ---
        $this->db->query('
            CREATE TABLE `tr_lembaga` (
                `id_lembaga` int(11) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` char(8) NOT NULL,
                `nama_lembaga` varchar(255) NOT NULL,
                `alamat_lembaga` varchar(255) NOT NULL,
                `nomor_lembaga` varchar(255) NOT NULL,
                PRIMARY KEY (`id_lembaga`),
                KEY `nomor_ajuan` (`nomor_ajuan`),
                CONSTRAINT `tr_lembaga_ibfk_1` FOREIGN KEY (`nomor_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // --- Dokumentasi (supporting documentation uploads) ---
        $this->db->query('
            CREATE TABLE `tr_dokumentasi` (
                `id_dokumentasi` bigint(20) NOT NULL AUTO_INCREMENT,
                `no_ajuan` char(8) NOT NULL,
                `nama_file` varchar(255) NOT NULL,
                `status` varchar(255) NOT NULL,
                `catatan` text DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_dokumentasi`),
                KEY `no_ajuan` (`no_ajuan`),
                CONSTRAINT `tr_dokumentasi_ibfk_1` FOREIGN KEY (`no_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // --- Kuitansi (receipts) ---
        $this->db->query('
            CREATE TABLE `tr_kuitansi` (
                `id_kuitansi` bigint(20) NOT NULL AUTO_INCREMENT,
                `no_ajuan` char(8) NOT NULL,
                `nama` varchar(255) NOT NULL,
                `status` varchar(255) NOT NULL,
                `catatan` text DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_kuitansi`),
                KEY `no_ajuan` (`no_ajuan`),
                CONSTRAINT `tr_kuitansi_ibfk_1` FOREIGN KEY (`no_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');

        // --- Form B3 (disbursement form) ---
        $this->db->query("
            CREATE TABLE `tr_form_b3` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` varchar(50) NOT NULL,
                `dana_dari` enum('Zakat','Infak Sedekah','Amil','Sosial Keagamaan Lainnya','Infaq Umum','Infaq Terikat','DSKL') NOT NULL,
                `kategori_penerima` int(11) NOT NULL,
                `bentuk_penyerahan` tinyint(4) NOT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `nomor_ajuan` (`nomor_ajuan`),
                CONSTRAINT `tr_form_b3_ibfk_1` FOREIGN KEY (`nomor_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");

        // --- Status change log ---
        $this->db->query('
            CREATE TABLE `ad_log_ajuan` (
                `id_log_ajuan` int(11) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` char(8) NOT NULL,
                `status_ajuan` int(11) NOT NULL,
                `tanggal_log` datetime NOT NULL,
                `catatan_log` varchar(255) DEFAULT NULL,
                `log_created_at` datetime NOT NULL,
                `log_updated_at` datetime NOT NULL,
                `log_created_by` varchar(100) DEFAULT NULL,
                PRIMARY KEY (`id_log_ajuan`),
                KEY `nomor_ajuan` (`nomor_ajuan`),
                KEY `status_ajuan` (`status_ajuan`),
                CONSTRAINT `ad_log_ajuan_ibfk_1` FOREIGN KEY (`nomor_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `ad_log_ajuan_ibfk_2` FOREIGN KEY (`status_ajuan`) REFERENCES `dt_status_ajuan` (`id_status`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');
    }

    public function down()
    {
        $this->forge->dropTable('ad_log_ajuan', true);
        $this->forge->dropTable('tr_form_b3', true);
        $this->forge->dropTable('tr_kuitansi', true);
        $this->forge->dropTable('tr_dokumentasi', true);
        $this->forge->dropTable('tr_lembaga', true);
        $this->forge->dropTable('tr_individu', true);
        $this->forge->dropTable('tr_ajuan', true);
        $this->forge->dropTable('dt_bentuk_penyerahan', true);
        $this->forge->dropTable('dt_kategori_penerima', true);
        $this->forge->dropTable('dt_penghasilan', true);
        $this->forge->dropTable('dt_pekerjaan', true);
        $this->forge->dropTable('dt_status_ajuan', true);
    }
}
