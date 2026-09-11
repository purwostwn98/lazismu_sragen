<?php

namespace App\Database\Migrations;

use App\Models\FormB2Model;
use CodeIgniter\Database\Migration;

class AddOpsiToFormB2 extends Migration
{
    public function up()
    {
        // Every scored question gets a companion "<question>_opsi" column
        // storing the exact answer label the applicant picked (the form
        // posts "score|label" for each question now), purely for display.
        // The existing score columns are untouched - they still drive
        // total_skor exactly as before. Standardizes what was originally
        // a one-off fix for q32 (whose score alone couldn't identify which
        // of its 6 answers was picked) across all 32 questions, so no
        // question can end up in that ambiguous state again.
        $fields = [];
        foreach (FormB2Model::PERTANYAAN_SKOR as $key) {
            $fields[$key . '_opsi'] = [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => $key,
            ];
        }

        $this->forge->addColumn('tr_form_b2', $fields);
    }

    public function down()
    {
        $columns = array_map(static fn ($key) => $key . '_opsi', FormB2Model::PERTANYAAN_SKOR);
        $this->forge->dropColumn('tr_form_b2', $columns);
    }
}
