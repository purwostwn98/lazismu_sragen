<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWilayahDusunRtRwToMasterLembaga extends Migration
{
    public function up()
    {
        // Mirrors ms_individu's address shape onto ms_lembaga: region
        // (provinsi/kabupaten/kecamatan/desa) plus dusun/RT/RW, so the
        // institution's address can be captured the same way. `alamat`
        // stays a single composed string for every existing view/PDF that
        // already reads ms_lembaga.alamat as one field.
        $this->forge->addColumn('ms_lembaga', [
            'provinsi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'alamat',
            ],
            'kabupaten' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'provinsi',
            ],
            'kecamatan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'kabupaten',
            ],
            'desa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'kecamatan',
            ],
            'dusun' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'desa',
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
        $this->forge->dropColumn('ms_lembaga', ['provinsi', 'kabupaten', 'kecamatan', 'desa', 'dusun', 'rt', 'rw']);
    }
}
