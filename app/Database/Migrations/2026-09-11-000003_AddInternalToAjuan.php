<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInternalToAjuan extends Migration
{
    public function up()
    {
        // Marks an ajuan as submitted by internal staff (via the admin
        // "Tambah Ajuan" form) rather than the public self-service form -
        // independent of jenis_ajuan, so it applies to Individu and
        // Lembaga ajuan alike. file_memo/deskripsi_memo are optional,
        // matching every other admin-only ajuan field.
        $this->forge->addColumn('tr_ajuan', [
            'is_internal' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
                'after'      => 'jenis_ajuan',
            ],
        ]);

        $this->forge->addColumn('tr_ajuan', [
            'file_memo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'file_proposal',
            ],
        ]);

        $this->forge->addColumn('tr_ajuan', [
            'deskripsi_memo' => [
                'type'    => 'TEXT',
                'null'    => true,
                'after'   => 'file_memo',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tr_ajuan', ['is_internal', 'file_memo', 'deskripsi_memo']);
    }
}
