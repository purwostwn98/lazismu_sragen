<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * tr_individu/tr_lembaga used to duplicate the mustahik/lembaga profile on
 * every ajuan submission, even though ms_individu/ms_lembaga already hold
 * that same data as the master registry. This drops the duplicated columns
 * and turns both tables into thin link tables (nomor_ajuan -> master record).
 */
class NormalizeIndividuLembagaTables extends Migration
{
    public function up()
    {
        $this->db->query('
            ALTER TABLE `tr_individu`
                DROP COLUMN `foto_ktp`,
                DROP COLUMN `kk`,
                DROP COLUMN `foto_kk`,
                DROP COLUMN `nama_mustahik`,
                DROP COLUMN `tempat_lahir`,
                DROP COLUMN `tgl_lahir`,
                DROP COLUMN `kelamin_mustahik`,
                DROP COLUMN `agama_mustahik`,
                DROP COLUMN `alamat`,
                DROP COLUMN `provinsi`,
                DROP COLUMN `kabupaten`,
                DROP COLUMN `kecamatan`,
                DROP COLUMN `desa`,
                DROP COLUMN `status_pendidikan`,
                DROP COLUMN `status_marital`,
                DROP COLUMN `pekerjaan`,
                DROP COLUMN `penghasilan`,
                DROP COLUMN `jml_keluarga`,
                DROP COLUMN `no_handphone`,
                DROP COLUMN `email`,
                ADD CONSTRAINT `tr_individu_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `ms_individu` (`nik`) ON UPDATE CASCADE
        ');

        $this->db->query('
            ALTER TABLE `tr_lembaga`
                DROP COLUMN `nama_lembaga`,
                DROP COLUMN `alamat_lembaga`,
                ADD CONSTRAINT `tr_lembaga_ibfk_2` FOREIGN KEY (`nomor_lembaga`) REFERENCES `ms_lembaga` (`nomor_legalitas`) ON UPDATE CASCADE
        ');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `tr_individu` DROP FOREIGN KEY `tr_individu_ibfk_1`');
        $this->db->query("
            ALTER TABLE `tr_individu`
                ADD COLUMN `foto_ktp` varchar(255) DEFAULT NULL,
                ADD COLUMN `kk` varchar(100) NOT NULL,
                ADD COLUMN `foto_kk` varchar(255) DEFAULT NULL,
                ADD COLUMN `nama_mustahik` varchar(255) NOT NULL,
                ADD COLUMN `tempat_lahir` varchar(255) DEFAULT NULL,
                ADD COLUMN `tgl_lahir` date DEFAULT NULL,
                ADD COLUMN `kelamin_mustahik` enum('Laki-laki','Perempuan') NOT NULL,
                ADD COLUMN `agama_mustahik` enum('Islam','Protestan','Katolik','Hindhu','Budha') DEFAULT NULL,
                ADD COLUMN `alamat` varchar(255) NOT NULL,
                ADD COLUMN `provinsi` int(11) NOT NULL,
                ADD COLUMN `kabupaten` int(11) NOT NULL,
                ADD COLUMN `kecamatan` int(11) NOT NULL,
                ADD COLUMN `desa` int(11) NOT NULL,
                ADD COLUMN `status_pendidikan` enum('SD','SMP','SMA/SMK','Diploma','Sarjana','Pascasarjana','tidak tamat SD','lainnya') DEFAULT NULL,
                ADD COLUMN `status_marital` enum('Lajang','Menikah','Cerai','Lainnya') DEFAULT NULL,
                ADD COLUMN `pekerjaan` int(11) NOT NULL,
                ADD COLUMN `penghasilan` int(11) NOT NULL,
                ADD COLUMN `jml_keluarga` int(11) NOT NULL,
                ADD COLUMN `no_handphone` varchar(16) DEFAULT NULL,
                ADD COLUMN `email` varchar(100) DEFAULT NULL
        ");

        $this->db->query('ALTER TABLE `tr_lembaga` DROP FOREIGN KEY `tr_lembaga_ibfk_2`');
        $this->db->query('
            ALTER TABLE `tr_lembaga`
                ADD COLUMN `nama_lembaga` varchar(255) NOT NULL,
                ADD COLUMN `alamat_lembaga` varchar(255) NOT NULL
        ');
    }
}
