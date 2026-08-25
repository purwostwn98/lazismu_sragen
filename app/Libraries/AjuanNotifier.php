<?php

namespace App\Libraries;

use App\Models\JabatanPenjabatModel;
use App\Models\KategoriProgramModel;
use App\Models\PemohonModel;
use App\Models\ProgramModel;
use Config\Services;

/**
 * Sends two emails whenever a new ajuan comes in:
 *  - every jabatan's current officeholder (highest mulai_tahun per
 *    id_jabatan — the same "current officeholder" convention already used
 *    for kuitansi/berita acara signer lookups and the C1 export) gets an
 *    action-needed alert.
 *  - the pemohon themselves gets a confirmation with their nomor ajuan.
 *
 * The two sends are independent: a failure in one (bad address, SMTP
 * hiccup) is logged and swallowed without affecting the other, and neither
 * is allowed to break the ajuan submission they're attached to.
 */
class AjuanNotifier
{
    public function notifikasiAjuanBaru(
        string $nomorAjuan,
        string $nik,
        string $jenisAjuan,
        ?int $idKategoriProgram,
        ?int $idProgram,
        float $nilaiDiajukan,
        string $deskripsiAjuan
    ): void {
        // Wrapped end-to-end: a DB hiccup while resolving pemohon/kategori/
        // program (this ran uncaught before — a transient "MySQL server has
        // gone away" here would have crashed the whole ajuan submission
        // request instead of just skipping the notification) must never
        // propagate past this method.
        try {
            $pemohon      = (new PemohonModel())->withWilayah()->find($nik);
            $namaKategori = $idKategoriProgram ? ((new KategoriProgramModel())->find($idKategoriProgram)['nama_kategori'] ?? null) : null;
            $namaProgram  = $idProgram ? ((new ProgramModel())->find($idProgram)['nama_program'] ?? null) : null;

            $baris = [
                'namaPemohon'   => $pemohon['nama_pemohon'] ?? '-',
                'alamatPemohon' => $this->susunAlamat($pemohon),
                'jenisAjuan'    => $jenisAjuan,
                'namaKategori'  => $namaKategori ?? '-',
                'namaProgram'   => $namaProgram ?? '-',
            ];

            $this->kirimKePenjabat($nomorAjuan, $baris, $nilaiDiajukan, $deskripsiAjuan);
            $this->kirimKePemohon($nomorAjuan, $pemohon, $baris, $nilaiDiajukan, $deskripsiAjuan);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menyiapkan notifikasi ajuan baru (' . $nomorAjuan . '): ' . $e->getMessage());
        }
    }

    private function kirimKePenjabat(string $nomorAjuan, array $b, float $nilaiDiajukan, string $deskripsiAjuan): void
    {
        try {
            $emails = $this->emailPenjabatSaatIni();

            if ($emails === []) {
                return;
            }

            $isi = $this->bungkusKartu(
                '#7367f0',
                'Ajuan Baru Masuk',
                'Ada ajuan baru yang perlu ditindaklanjuti.',
                $this->tabelDetail([
                    ['Nomor Ajuan', $nomorAjuan],
                    ['Pemohon', $b['namaPemohon']],
                    ['Alamat', $b['alamatPemohon']],
                    ['Jenis Ajuan', $b['jenisAjuan']],
                    ['Program', $b['namaKategori']],
                    ['Kegiatan', $b['namaProgram']],
                    ['Tanggal', date('d-m-Y H:i')],
                ]),
                $nilaiDiajukan,
                $deskripsiAjuan,
                base_url('ajuan/' . rawurlencode($nomorAjuan)),
                'Lihat Ajuan'
            );

            $email = Services::email();
            $email->setTo($emails);
            $email->setSubject('Ajuan Baru: ' . $nomorAjuan);
            $email->setMessage($isi);

            // send() fails "quietly" — it returns false rather than
            // throwing, so a failed send here would otherwise leave no
            // trace at all in the logs.
            if (!$email->send()) {
                log_message('error', 'Gagal mengirim notifikasi ajuan baru ke penjabat (' . $nomorAjuan . '): ' . strip_tags($email->printDebugger(['headers'])));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengirim notifikasi ajuan baru ke penjabat: ' . $e->getMessage());
        }
    }

    private function kirimKePemohon(string $nomorAjuan, ?array $pemohon, array $b, float $nilaiDiajukan, string $deskripsiAjuan): void
    {
        if (empty($pemohon['email'])) {
            return;
        }

        try {
            $isi = $this->bungkusKartu(
                '#28c76f',
                'Ajuan Anda Berhasil Diajukan',
                'Terima kasih, ajuan Anda telah kami terima dengan nomor ajuan <strong>' . esc($nomorAjuan) . '</strong>. Simpan nomor ini untuk mengecek status ajuan Anda.',
                $this->tabelDetail([
                    ['Nomor Ajuan', $nomorAjuan],
                    ['Jenis Ajuan', $b['jenisAjuan']],
                    ['Program', $b['namaKategori']],
                    ['Kegiatan', $b['namaProgram']],
                    ['Tanggal', date('d-m-Y H:i')],
                ]),
                $nilaiDiajukan,
                $deskripsiAjuan,
                base_url('pengajuan/sukses/' . rawurlencode($nomorAjuan)),
                'Lihat Bukti Ajuan'
            );

            $email = Services::email();
            $email->setTo($pemohon['email']);
            $email->setSubject('Ajuan Anda Berhasil Diajukan - ' . $nomorAjuan);
            $email->setMessage($isi);

            if (!$email->send()) {
                log_message('error', 'Gagal mengirim konfirmasi ajuan ke pemohon (' . $nomorAjuan . '): ' . strip_tags($email->printDebugger(['headers'])));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengirim konfirmasi ajuan ke pemohon: ' . $e->getMessage());
        }
    }

    /** One email per jabatan: whichever penjabat has the highest mulai_tahun for that id_jabatan. */
    private function emailPenjabatSaatIni(): array
    {
        $semua = (new JabatanPenjabatModel())->orderBy('mulai_tahun', 'DESC')->findAll();

        $terkiniPerJabatan = [];
        foreach ($semua as $row) {
            $terkiniPerJabatan[$row['id_jabatan']] ??= $row;
        }

        $emails = array_column($terkiniPerJabatan, 'email');

        return array_values(array_unique(array_filter($emails)));
    }

    /** Same alamat_detail + kelurahan/kecamatan/kabupaten/provinsi composition used across the app's berita acara / preview views. */
    private function susunAlamat(?array $pemohon): string
    {
        if (!$pemohon) {
            return '-';
        }

        $bagian = array_filter([
            $pemohon['alamat_detail'] ?? null,
            $pemohon['nama_kelurahan'] ?? null,
            $pemohon['nama_kecamatan'] ?? null,
            $pemohon['nama_kabupaten'] ?? null,
            $pemohon['nama_provinsi'] ?? null,
        ]);

        return $bagian === [] ? '-' : implode(', ', $bagian);
    }

    /** Renders [label, value] pairs as the shaded detail table used inside both email cards. */
    private function tabelDetail(array $baris): string
    {
        $rows = array_map(
            static fn (array $b) => '<tr>'
                . '<td style="padding:8px 12px;color:#6b7280;font-size:13px;white-space:nowrap;vertical-align:top;">' . esc($b[0]) . '</td>'
                . '<td style="padding:8px 12px;color:#2b2c40;font-size:13px;font-weight:500;">' . esc($b[1]) . '</td>'
                . '</tr>',
            $baris
        );

        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8f7fa;border-radius:8px;">'
            . implode('', $rows)
            . '</table>';
    }

    /** Shared branded card shell for every notification email — only the accent color, title, intro, detail table, and CTA link differ. */
    private function bungkusKartu(
        string $warnaAksen,
        string $judul,
        string $intro,
        string $tabelDetailHtml,
        float $nilaiDiajukan,
        string $deskripsiAjuan,
        string $tautan,
        string $labelTautan
    ): string {
        $nominal = 'Rp ' . number_format($nilaiDiajukan, 0, ',', '.');

        return '
        <div style="font-family:Arial,Helvetica,sans-serif;background:#f4f5fa;padding:24px;">
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #eceef1;">
            <tr>
              <td style="background:' . $warnaAksen . ';padding:20px 24px;">
                <div style="color:#ffffff;font-size:18px;font-weight:700;">' . esc($judul) . '</div>
                <div style="color:#ffffff;opacity:0.85;font-size:13px;margin-top:2px;">Lazismu Sragen</div>
              </td>
            </tr>
            <tr>
              <td style="padding:20px 24px 4px 24px;color:#2b2c40;font-size:14px;line-height:1.5;">
                ' . $intro . '
              </td>
            </tr>
            <tr>
              <td style="padding:12px 24px;">
                ' . $tabelDetailHtml . '
              </td>
            </tr>
            <tr>
              <td style="padding:0 24px 4px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff2e8;border-radius:8px;">
                  <tr>
                    <td style="padding:10px 12px;color:#6b7280;font-size:13px;">Nilai Diajukan</td>
                    <td style="padding:10px 12px;text-align:right;color:#ff9f43;font-size:16px;font-weight:700;">' . esc($nominal) . '</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td style="padding:16px 24px 4px 24px;color:#6b7280;font-size:13px;">Deskripsi</td>
            </tr>
            <tr>
              <td style="padding:4px 24px 20px 24px;">
                <div style="background:#f8f7fa;border-radius:8px;padding:12px;color:#2b2c40;font-size:13px;line-height:1.5;">' . nl2br(esc($deskripsiAjuan)) . '</div>
              </td>
            </tr>
            <tr>
              <td style="padding:0 24px 24px 24px;">
                <a href="' . esc($tautan) . '" style="display:inline-block;background:' . $warnaAksen . ';color:#ffffff;text-decoration:none;font-size:13px;font-weight:600;padding:10px 18px;border-radius:6px;">' . esc($labelTautan) . '</a>
              </td>
            </tr>
            <tr>
              <td style="padding:14px 24px;background:#f8f7fa;color:#9a9cb5;font-size:11px;">
                Email ini dikirim otomatis oleh sistem Lazismu Sragen, mohon tidak membalas email ini.
              </td>
            </tr>
          </table>
        </div>';
    }
}
