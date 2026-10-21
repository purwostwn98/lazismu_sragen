<?php

namespace App\Models;

use CodeIgniter\Model;

class AjuanModel extends Model
{
    protected $table      = 'tr_ajuan';
    protected $primaryKey = 'id_ajuan';
    protected $allowedFields = [
        'nomor_ajuan', 'nik', 'id_kategori_program', 'id_program', 'nilai_diajukan', 'deskripsi_ajuan',
        'jenis_ajuan', 'is_internal', 'file_formulir', 'file_proposal', 'file_memo', 'deskripsi_memo',
        'status_ajuan', 'status_tersalurkan', 'nilai_disetujui', 'sifat_bantuan', 'edit_ajuan',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'tgl_diajukan';
    protected $updatedField  = 'last_updated_at';

    /**
     * Creates a new Ajuan with the initial "Diajukan" status and a
     * nomor_ajuan in "JJYYYYMMDDNNN" form (13 chars):
     *   - JJ    : "01" Individu, "02" Lembaga.
     *   - YYYYMMDD : today's date.
     *   - NNN  : this jenis's running count for today, "001"-"999" -
     *     Individu and Lembaga each keep their own daily count, so both
     *     can have a "001" on the same day.
     *
     * Ajuan saved before this format existed keep their old 8-digit random
     * nomor_ajuan untouched - this method only runs for new ajuan, never to
     * rewrite existing rows. Both lengths coexist fine since nomor_ajuan is
     * only ever compared/stored as an opaque string everywhere in the app,
     * never parsed or cast to a number.
     */
    public function simpan(array $data)
    {
        $jenisKode = ($data['jenis_ajuan'] ?? '') === 'Lembaga' ? '02' : '01';

        // The app runs on Asia/Jakarta (see Config\App::$appTimezone), so
        // this is already the real Indonesian calendar day the daily
        // counter resets on.
        $prefix = $jenisKode . date('Ymd');

        // Starting guess from today's count so far, then walked forward
        // (and re-checked against the table) until a free number is found -
        // self-corrects even if the count undershoots (e.g. a past entry
        // was deleted), and the table's UNIQUE KEY is the final backstop
        // against ever inserting a duplicate.
        $urutan  = $this->like('nomor_ajuan', $prefix, 'after')->countAllResults() + 1;
        $noAjuan = null;

        while ($urutan <= 999) {
            $kandidat = $prefix . str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);

            if ($this->where('nomor_ajuan', $kandidat)->countAllResults() === 0) {
                $noAjuan = $kandidat;
                break;
            }

            $urutan++;
        }

        // 999 ajuan of the same jenis in one day has never happened here;
        // fail cleanly rather than silently invent a 14th digit.
        if ($noAjuan === null) {
            return false;
        }

        $data['nomor_ajuan']  = $noAjuan;
        $data['status_ajuan'] = $data['status_ajuan'] ?? 1;

        $this->transBegin();
        $this->insert($data);
        $ok = $this->transStatus() !== false;
        $ok ? $this->transCommit() : $this->transRollback();

        return $ok ? $noAjuan : false;
    }

    public function withRelasi()
    {
        return $this->select('
                tr_ajuan.*,
                tr_pemohon.nama_pemohon,
                tr_pemohon.telepon,
                ad_program.nama_program,
                dt_status_ajuan.keterangan_status
            ')
            ->join('tr_pemohon', 'tr_pemohon.nik = tr_ajuan.nik', 'left')
            ->join('ad_program', 'ad_program.id_program = tr_ajuan.id_program', 'left')
            ->join('dt_status_ajuan', 'dt_status_ajuan.id_status = tr_ajuan.status_ajuan', 'left');
    }
}
