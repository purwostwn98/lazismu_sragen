<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSyaratProgramTable extends Migration
{
    public function up()
    {
        $this->db->query('
            CREATE TABLE `ad_syarat_program` (
                `id_syarat` int(11) NOT NULL AUTO_INCREMENT,
                `id_program` int(11) NOT NULL,
                `syarat_program` varchar(255) NOT NULL,
                PRIMARY KEY (`id_syarat`),
                KEY `id_program` (`id_program`),
                CONSTRAINT `ad_syarat_program_ibfk_1` FOREIGN KEY (`id_program`) REFERENCES `ad_program` (`id_program`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');
    }

    public function down()
    {
        $this->forge->dropTable('ad_syarat_program', true);
    }
}
