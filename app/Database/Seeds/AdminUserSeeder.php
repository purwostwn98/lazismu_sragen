<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('users')->countAllResults() > 0) {
            return;
        }

        $this->db->table('users')->insert([
            'username'  => 'admin',
            'password'  => password_hash('lazismu123', PASSWORD_DEFAULT),
            'privuser'  => 1,
            'idlembaga' => 1,
            'nama_user' => 'Admin Lazismu Sragen',
        ]);
    }
}
