<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->db->query('
            CREATE TABLE `users` (
                `iduser` int(11) NOT NULL AUTO_INCREMENT,
                `username` varchar(200) NOT NULL,
                `password` varchar(255) NOT NULL,
                `privuser` int(11) NOT NULL DEFAULT 1,
                `idlembaga` tinyint(4) NOT NULL DEFAULT 1,
                `nama_user` varchar(255) NOT NULL,
                `u_created_at` datetime DEFAULT NULL,
                `u_updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`iduser`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ');
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
