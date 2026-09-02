<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFormB2Table extends Migration
{
    public function up()
    {
        // Digitized version of "Formulir Survey Calon Mustahik" (bahan/B2
        // Sragen.xlsx) — a 32-question weighted eligibility assessment,
        // filled by the pemohon themselves while submitting an Individu
        // ajuan. total_skor/kategori_kelayakan are computed server-side
        // (see FormB2Model::totalDanKategori()) and stored so the admin
        // detail page can display the result without recomputing.
        $this->db->query("
            CREATE TABLE `tr_form_b2` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nomor_ajuan` char(8) NOT NULL,

                `q1_tanggungan_keluarga` tinyint(1) NOT NULL,
                `q2_anak_sekolah` tinyint(1) NOT NULL,
                `q3_anak_putus_sekolah` tinyint(1) NOT NULL,
                `q4_pengeluaran_bulanan` tinyint(1) NOT NULL,
                `q5_obat_rutin` tinyint(1) NOT NULL,
                `q6_biaya_pendidikan` tinyint(1) NOT NULL,
                `q7_hutang_berjalan` tinyint(1) NOT NULL,
                `q8_keperluan_hutang` tinyint(1) NOT NULL,
                `q9_pekerjaan_kepala_keluarga` tinyint(1) NOT NULL,
                `q10_merokok` tinyint(1) NOT NULL,
                `q11_pekerjaan_pasangan` tinyint(1) NOT NULL,
                `q12_usia_mustahik` tinyint(1) NOT NULL,
                `q13_kondisi_kepala_keluarga` tinyint(1) NOT NULL,
                `q14_kepemilikan_rumah` tinyint(1) NOT NULL,
                `q15_luas_rumah` tinyint(1) NOT NULL,
                `q16_dinding_rumah` tinyint(1) NOT NULL,
                `q17_lantai` tinyint(1) NOT NULL,
                `q18_atap` tinyint(1) NOT NULL,
                `q19_sumber_air_minum` tinyint(1) NOT NULL,
                `q20_mck` tinyint(1) NOT NULL,
                `q21_penerangan` tinyint(1) NOT NULL,
                `q22_daya_terpasang` tinyint(1) NOT NULL,
                `q23_kelayakan_tidur` tinyint(1) NOT NULL,
                `q24_makan_perhari` tinyint(1) NOT NULL,
                `q25_konsumsi_ayam` tinyint(1) NOT NULL,
                `q26_konsumsi_daging` tinyint(1) NOT NULL,
                `q27_konsumsi_susu` tinyint(1) NOT NULL,
                `q28_belanja_harian` tinyint(1) NOT NULL,
                `q29_aset_tidak_bergerak` tinyint(1) NOT NULL,
                `q30_barang_berharga` tinyint(1) NOT NULL,
                `q31_aset_bergerak` tinyint(1) NOT NULL,
                `q32_bantuan_lembaga_lain` tinyint(1) NOT NULL,

                `elektronik_tv_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_tv_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,
                `elektronik_hp_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_hp_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,
                `elektronik_kulkas_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_kulkas_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,
                `elektronik_magic_com_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_magic_com_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,
                `elektronik_mesin_cuci_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_mesin_cuci_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,
                `elektronik_setrika_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_setrika_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,
                `elektronik_dispenser_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_dispenser_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,
                `elektronik_lainnya_nama` varchar(100) DEFAULT NULL,
                `elektronik_lainnya_jumlah` tinyint(2) unsigned NOT NULL DEFAULT 0,
                `elektronik_lainnya_status` enum('Milik Sendiri','Pemberian','Pinjam') DEFAULT NULL,

                `catatan_tambahan` text DEFAULT NULL,
                `bersedia_dipublikasikan` tinyint(1) NOT NULL DEFAULT 0,

                `total_skor` smallint(4) NOT NULL,
                `kategori_kelayakan` varchar(50) NOT NULL,

                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `nomor_ajuan` (`nomor_ajuan`),
                CONSTRAINT `tr_form_b2_ibfk_1` FOREIGN KEY (`nomor_ajuan`) REFERENCES `tr_ajuan` (`nomor_ajuan`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->forge->dropTable('tr_form_b2', true);
    }
}
