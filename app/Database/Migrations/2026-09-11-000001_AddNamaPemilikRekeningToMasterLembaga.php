<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNamaPemilikRekeningToMasterLembaga extends Migration
{
    public function up()
    {
        $this->forge->addColumn('ms_lembaga', [
            'nama_pemilik_rekening' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'nomor_rekening',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('ms_lembaga', ['nama_pemilik_rekening']);
    }
}
