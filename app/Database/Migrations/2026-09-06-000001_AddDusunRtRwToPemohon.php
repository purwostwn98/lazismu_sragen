<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDusunRtRwToPemohon extends Migration
{
    public function up()
    {
        // Mirrors ms_individu/ms_lembaga's address shape onto tr_pemohon:
        // splits the free-text "Alamat Detail" into a name/street field plus
        // separate RT/RW numbers. `alamat_detail` stays a single composed
        // string for every existing view/PDF that already reads it as one.
        $this->forge->addColumn('tr_pemohon', [
            'dusun' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'alamat_detail',
            ],
            'rt' => [
                'type'       => 'SMALLINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'dusun',
            ],
            'rw' => [
                'type'       => 'SMALLINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'rt',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tr_pemohon', ['dusun', 'rt', 'rw']);
    }
}
