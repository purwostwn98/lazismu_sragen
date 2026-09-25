<html>

<head>
    <style>
        body {
            font-size: 10px;
        }

        .body_table td {
            font-size: 10px;
        }
    </style>
</head>

<?php
$ajuan         = $ajuan ?? [];
$namaPilar     = $namaPilar ?? null;
$hasilPerTahap = $hasilPerTahap ?? [];
$terkini       = $terkini ?? null;

$v = static fn ($nilai): string => esc(($nilai !== null && trim((string) $nilai) !== '') ? (string) $nilai : '-');

// Grey heading bar that opens each section.
$judul = static fn (string $teks): string => '<table cellpadding="3" style="width: 100%;"><tr><td style="background-color: #e5e7eb; font-weight: bold;">' . $teks . '</td></tr></table>';

// One label : value row of the identity table.
$baris = static function (string $label, string $isi): string {
    return '<tr>'
        . '<td style="width: 28%; border-bottom: 1px solid #cccccc;">' . $label . '</td>'
        . '<td style="width: 3%; border-bottom: 1px solid #cccccc;">:</td>'
        . '<td style="width: 69%; border-bottom: 1px solid #cccccc;">' . $isi . '</td>'
        . '</tr>';
};

// One of the 5 boxes across the top: a role label, blank space to sign by
// hand, and - only when a real result already exists for that stage - the
// date it happened. "Front Office" has no disposisi stage of its own; it
// stands for the ajuan being received, so it uses tgl_diajukan directly.
$kotakTanggal = [
    'Front Office' => $ajuan['tgl_diajukan'] ?? null,
    'Program'      => $hasilPerTahap['Program']['created_at'] ?? null,
    'Survey'       => $hasilPerTahap['Survey']['created_at'] ?? null,
    'Direktur'     => $hasilPerTahap['Direktur']['created_at'] ?? null,
    'Pengurus'     => $hasilPerTahap['Pengurus']['created_at'] ?? null,
];
$kotakSign = static function (string $label, ?string $tanggal): string {
    $tglCetak = $tanggal ? esc(date('d/m/Y', strtotime($tanggal))) : '&nbsp;';

    return '<td style="width: 20%; border: 1px solid #999; text-align: center; vertical-align: top;">'
        . '<div style="font-weight: bold;">' . esc($label) . '</div>'
        . '<br><br><br>'
        . '<div style="border-top: 1px solid #999; font-size: 8px;">' . $tglCetak . '</div>'
        . '</td>';
};

// Nama tahap yang dipakai di Catatan (istilah aplikasi, bukan istilah kotak
// kertas) supaya jelas mengacu ke tahap disposisi mana - lengkap dengan
// tanggal, rekomendasi, dan nominal seperti kartu hasil di halaman lain,
// lalu deskripsi tinjauan itu sendiri (sudah disanitasi saat disimpan, jadi
// aman ditulis langsung sebagai HTML di sini).
// Chronological workflow order (Surveyor always happens before Kepala
// Divisi Program can even review it, and so on) - not the paper box order
// above, which runs Program before Survey.
$namaTahapCatatan = ['Survey' => 'Surveyor', 'Program' => 'Kepala Divisi Program', 'Direktur' => 'Manager', 'Pengurus' => 'Badan Pengurus'];
$catatan          = '';
foreach ($namaTahapCatatan as $kotak => $namaTahap) {
    $hasil = $hasilPerTahap[$kotak] ?? null;

    if (!$hasil) {
        continue;
    }

    $rekomendasi = (int) $hasil['rekomendasi'] === 1
        ? 'Direkomendasikan Disetujui, Rp ' . number_format((float) $hasil['nominal_rekomendasi'], 0, ',', '.')
        : 'Direkomendasikan Ditolak';

    $catatan .= '<div style="margin-bottom: 6px;">'
        . '<b>' . esc($namaTahap) . '</b> &middot; ' . esc($hasil['nama_petugas'] ?? '-') . ' &middot; ' . esc(format_tanggal_indo($hasil['created_at']))
        . '<br><i>' . esc($rekomendasi) . '</i>'
        . $hasil['deskripsi']
        . '</div>';
}
$catatan = $catatan !== '' ? $catatan : '<span style="color: #999;">Belum ada catatan tinjauan.</span>';
?>

<body>
    <table cellpadding="1" style="width:100%">
        <tr>
            <td rowspan="2" style="width: 15%; text-align: left;">
                <img width="60px" src="<?= FCPATH . 'assets/img/logo/logo.png' ?>" alt="">
            </td>
            <td style="width: 85%; text-align: left; font-size: 14px;"><strong>LAZISMU SRAGEN</strong></td>
        </tr>
        <tr>
            <td style="font-size: 9px; text-align: left;">Widoro, RT.37/RW.11, Dusun Kebayanan Widodo 1, Sragen Wetan, Kec. Sragen, Kabupaten Sragen, Jawa Tengah 57214<br>Telepon: 0851-0000-0098</td>
        </tr>
    </table>
    <hr style="margin-top: 2px;">

    <table style="width: 100%;">
        <tr>
            <td style="text-align: center;">
                <strong style="font-size: 15px;">LEMBAR DISPOSISI</strong>
            </td>
        </tr>
    </table>
    <br>

    <table style="width: 100%;">
        <tr>
            <td style="width: 70%; vertical-align: top;">
                <table class="body_table" cellpadding="3" style="width: 100%;">
                    <?= $baris('No. Pengajuan', $v($ajuan['nomor_ajuan'] ?? null)) ?>
                    <?= $baris('Pengirim', $v($ajuan['nama_pemohon'] ?? null)) ?>
                    <?= $baris('Jenis', $v($namaPilar)) ?>
                    <?= $baris('Tanggal Terima', $v(format_tanggal_indo($ajuan['tgl_diajukan'] ?? null))) ?>
                    <?= $baris('Kegiatan', $v($ajuan['nama_program'] ?? null)) ?>
                </table>
            </td>
            <td style="width: 30%; vertical-align: top; text-align: right;">
                <table class="body_table" cellpadding="3" style="width: 100%;">
                    <tr><td style="width: 20px; border: 1px solid #333;">&nbsp;</td><td>Segera</td></tr>
                    <tr><td style="width: 20px; border: 1px solid #333;">&nbsp;</td><td>Biasa</td></tr>
                </table>
            </td>
        </tr>
    </table>
    <br>

    <table cellpadding="4" style="width: 100%;">
        <tr>
            <?= $kotakSign('Front Office', $kotakTanggal['Front Office']) ?>
            <?= $kotakSign('Program', $kotakTanggal['Program']) ?>
            <?= $kotakSign('Survey', $kotakTanggal['Survey']) ?>
            <?= $kotakSign('Direktur', $kotakTanggal['Direktur']) ?>
            <?= $kotakSign('Pengurus', $kotakTanggal['Pengurus']) ?>
        </tr>
    </table>
    <br>

    <table cellpadding="6" style="width: 100%;">
        <tr>
            <td style="width: 40%; border: 1px solid #999; vertical-align: top;">
                <div style="font-weight: bold; text-decoration: underline; margin-bottom: 4px;">Catatan</div>
                <?= $catatan ?>
            </td>
            <td style="width: 36%; border: 1px solid #999; vertical-align: top;">
                <div style="font-weight: bold; text-decoration: underline;">Penyelesaian</div>
                <br><br><br><br><br><br><br>
            </td>
            <td style="width: 24%; vertical-align: top; padding: 0;">
                <table cellpadding="4" style="width: 100%;">
                    <?php
                    // A checkmark glyph (e.g. &#10003;) isn't in TCPDF's
                    // default font and would print as "?" (the same issue
                    // Form B2's "≤" had) - a plain bracketed [X] is legible
                    // in any font and reads unambiguously as "this one".
                    $tandaAcc   = $terkini && (int) $terkini['rekomendasi'] === 1 ? ' [X]' : '';
                    $tandaTolak = $terkini && (int) $terkini['rekomendasi'] === 0 ? ' [X]' : '';
                    ?>
                    <tr>
                        <td style="border: 1px solid #999;">
                            <b>ACC<?= $tandaAcc ?></b>
                            <br><br>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #999;">
                            <b>DI TUNDA</b>
                            <br><br>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #999;">
                            <b>DI TOLAK<?= $tandaTolak ?></b>
                            <br><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
