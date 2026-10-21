<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Widens every nomor_ajuan (and tr_dokumentasi/tr_kuitansi's no_ajuan)
 * column from char(8) to char(13), for AjuanModel::simpan()'s new
 * "JJYYYYMMDDNNN" format (jenis + tanggal + urutan harian) - the old
 * random 8-digit format no longer fits.
 *
 * Pure widening of a fixed-width CHAR column: every existing 8-char value
 * fits inside 13 chars unchanged (no truncation, no row rewritten, no data
 * touched) - only the column definition changes.
 *
 * This server refuses to CHANGE a column that a FOREIGN KEY still points
 * to at the old width (verified against the live DB: widening tr_ajuan
 * first errors with "used in a foreign key constraint... of table
 * tr_dokumentasi", and the same would happen widening a child first while
 * the parent is still narrower) - even tr_form_b3_ibfk_1 blocks it, despite
 * tr_form_b3.nomor_ajuan (varchar(50)) not needing to change at all. So
 * every FK into tr_ajuan.nomor_ajuan (all 9) is dropped first, the 8
 * columns that actually need it are widened, then the exact same
 * constraints (same name, same ON DELETE/UPDATE CASCADE - verified against
 * every CREATE TABLE migration that defined them) are added back. Dropping
 * a FK only removes that enforcement rule, never any data or row;
 * re-adding it re-validates referential integrity, which still holds since
 * no value changed.
 *
 * Existing ajuan keep their 8-char nomor_ajuan forever (AjuanModel::simpan()
 * only applies the new format to ajuan created from here on) - this
 * migration only makes room for the longer format, it doesn't rewrite any
 * stored value.
 *
 * Every step checks INFORMATION_SCHEMA first and skips anything already
 * done, so this is safe to re-run after a partial failure (MySQL commits
 * each DDL statement immediately regardless of any surrounding PHP
 * transaction, so a mid-way error here can't be rolled back like a normal
 * failed insert/update - this file dealt with exactly that once already).
 */
class WidenNomorAjuanColumns extends Migration
{
    /**
     * [table => [column, constraint name]] for every FK into
     * tr_ajuan.nomor_ajuan - all 9 must be dropped to unblock widening the
     * parent, even tr_form_b3's, whose own column doesn't need to change.
     */
    private const SEMUA_FK = [
        'tr_lembaga'      => ['nomor_ajuan', 'tr_lembaga_ibfk_1'],
        'tr_dokumentasi'  => ['no_ajuan', 'tr_dokumentasi_ibfk_1'],
        'tr_kuitansi'     => ['no_ajuan', 'tr_kuitansi_ibfk_1'],
        'ad_log_ajuan'    => ['nomor_ajuan', 'ad_log_ajuan_ibfk_1'],
        'ad_berita_acara' => ['nomor_ajuan', 'ad_berita_acara_ibfk_1'],
        'ad_surat_tugas'  => ['nomor_ajuan', 'ad_surat_tugas_ibfk_1'],
        'ad_disposisi'    => ['nomor_ajuan', 'ad_disposisi_ibfk_1'],
        'tr_form_b2'      => ['nomor_ajuan', 'tr_form_b2_ibfk_1'],
        'tr_form_b3'      => ['nomor_ajuan', 'tr_form_b3_ibfk_1'],
    ];

    /** Of the above, the tables whose column is actually too narrow and needs widening (tr_form_b3 is varchar(50) already). */
    private const KOLOM_DIPERLEBAR = ['tr_lembaga', 'tr_dokumentasi', 'tr_kuitansi', 'ad_log_ajuan', 'ad_berita_acara', 'ad_surat_tugas', 'ad_disposisi', 'tr_form_b2'];

    public function up()
    {
        $this->ubahLebar(13);
    }

    public function down()
    {
        $this->ubahLebar(8);
    }

    private function ubahLebar(int $ke): void
    {
        foreach (self::SEMUA_FK as $table => [, $constraint]) {
            if ($this->constraintAda($table, $constraint)) {
                $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
            }
        }

        $this->ubahKolom('tr_ajuan', 'nomor_ajuan', $ke);

        foreach (self::KOLOM_DIPERLEBAR as $table) {
            $this->ubahKolom($table, self::SEMUA_FK[$table][0], $ke);
        }

        foreach (self::SEMUA_FK as $table => [$kolom, $constraint]) {
            if (!$this->constraintAda($table, $constraint)) {
                // Same row-size check as ubahKolom() trips on tr_form_b2
                // again here - adding the FK re-validates the table's row
                // size too. Same safe, session-scoped relaxation.
                $this->db->query('SET SESSION innodb_strict_mode = 0');
                $this->db->query("
                    ALTER TABLE `{$table}`
                    ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$kolom}`) REFERENCES `tr_ajuan` (`nomor_ajuan`)
                    ON DELETE CASCADE ON UPDATE CASCADE
                ");
                $this->db->query('SET SESSION innodb_strict_mode = 1');
            }
        }
    }

    private function ubahKolom(string $table, string $kolom, int $ke): void
    {
        $lebarSaatIni = $this->db->query('
                SELECT CHARACTER_MAXIMUM_LENGTH
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
            ', [$table, $kolom])->getRow()->CHARACTER_MAXIMUM_LENGTH ?? null;

        if ((int) $lebarSaatIni !== $ke) {
            // tr_form_b2 has 89 columns (32 scored questions + their
            // companion "_opsi" label columns, electronics, etc.) and is
            // already near InnoDB's calculated max-row-size ceiling, so
            // even widening one CHAR column by a few bytes is refused
            // outright under the server's default innodb_strict_mode.
            // ROW_FORMAT is already Dynamic (confirmed on the live table),
            // which stores long variable-length columns off-page at
            // runtime regardless of this worst-case check, so relaxing
            // strict mode for just this ALTER is the standard, safe fix -
            // session-scoped only, restored right after, never touches the
            // server's actual default.
            $this->db->query('SET SESSION innodb_strict_mode = 0');
            $this->db->query("ALTER TABLE `{$table}` CHANGE `{$kolom}` `{$kolom}` CHAR({$ke}) NOT NULL");
            $this->db->query('SET SESSION innodb_strict_mode = 1');
        }
    }

    private function constraintAda(string $table, string $constraint): bool
    {
        return (int) $this->db->query('
                SELECT COUNT(*) AS n
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?
            ', [$table, $constraint])->getRow()->n > 0;
    }
}
