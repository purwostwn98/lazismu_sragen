<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDusunRtRwToMasterIndividu extends Migration
{
    public function up()
    {
        // Splits the free-text "Alamat Detail" field into a name/street
        // field plus separate RT/RW numbers, so the pengajuan formulir and
        // ajuan/create forms can capture them individually while `alamat`
        // stays a single composed string for every existing view/PDF that
        // already reads ms_individu.alamat as one field.
        $this->forge->addColumn('ms_individu', [
            'dusun' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'alamat',
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
        $this->forge->dropColumn('ms_individu', ['dusun', 'rt', 'rw']);
    }
}
