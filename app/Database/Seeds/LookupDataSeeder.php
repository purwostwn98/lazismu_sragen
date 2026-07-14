<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Loads reference/lookup data (Indonesia region codes, program catalog,
 * fundraising categories, application statuses, etc.) copied from the
 * lazismu_reborn application. No transactional/personal data is included.
 */
class LookupDataSeeder extends Seeder
{
    public function run()
    {
        // Order matters: parents before children (FK dependencies).
        $files = [
            'dt_provinsi.sql',
            'dt_kabupaten.sql',
            'dt_kecamatan.sql',
            'dt_kelurahan.sql',
            'dt_pilar.sql',
            'ad_kategori_program.sql',
            'ad_program.sql',
            'ad_syarat_program.sql',
            'dt_penghimpunan_ktg.sql',
            'dt_penghimpunan_subktg.sql',
            'dt_status_ajuan.sql',
            'dt_pekerjaan.sql',
            'dt_penghasilan.sql',
            'dt_kategori_penerima.sql',
            'dt_bentuk_penyerahan.sql',
        ];

        foreach ($files as $file) {
            $path = __DIR__ . '/sql/' . $file;
            $table = basename($file, '.sql');

            if ($this->db->table($table)->countAllResults() > 0) {
                continue;
            }

            $sql = file_get_contents($path);
            foreach (array_filter(array_map('trim', explode(";\n", $sql))) as $statement) {
                $this->db->query($statement);
            }
        }
    }
}
