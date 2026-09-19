<html>

<head>
    <style>
        body {
            font-size: 11px;
        }

        .body_table td {
            font-size: 11px;
        }
    </style>
</head>

<?php
$ajuan    = $ajuan ?? [];
$individu = $individu ?? [];
$lembaga  = $lembaga ?? null;

// One template serves both jenis ajuan; only the identity/address/document
// sections and the mustahik signature differ.
$untukLembaga = $lembaga !== null;

// Blank/null fields print as "-" rather than an empty gap in the form.
$v = static fn ($nilai): string => esc(($nilai !== null && trim((string) $nilai) !== '') ? (string) $nilai : '-');

$tempatTglLahir = trim(trim((string) ($individu['tempat_lahir'] ?? '')) . ', ' . format_tanggal_indo($individu['tgl_lahir'] ?? null), ', ');
$ada            = static fn ($file): string => !empty($file) ? 'Ada' : 'Belum diunggah';

// One label : value row of a section table.
$baris = static function (string $label, string $isi): string {
    return '<tr>'
        . '<td style="width: 30%; border-bottom: 1px solid #cccccc;">' . $label . '</td>'
        . '<td style="width: 3%; border-bottom: 1px solid #cccccc;">:</td>'
        . '<td style="width: 67%; border-bottom: 1px solid #cccccc;">' . $isi . '</td>'
        . '</tr>';
};

// Grey heading bar that opens each section.
$judul = static fn (string $teks): string => '<table cellpadding="3" style="width: 100%;"><tr><td style="background-color: #e5e7eb; font-weight: bold;">' . $teks . '</td></tr></table>';

// Same, prefixed with the next section letter (A, B, C, ...) so the lettering
// stays continuous whichever sections the jenis ajuan includes.
$huruf  = 'A';
$bagian = static function (string $teks) use (&$huruf, $judul): string {
    return $judul($huruf++ . '. ' . $teks);
};
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
                <strong style="font-size: 15px;">FORM B1</strong>
                <br><strong style="font-size: 12px;">DATA CALON MUSTAHIK (<?= $untukLembaga ? 'LEMBAGA' : 'INDIVIDU' ?>)</strong>
                <br><span style="font-size: 9px;">Nomor Ajuan: <?= $v($ajuan['nomor_ajuan'] ?? null) ?> &nbsp;|&nbsp; Tanggal Diajukan: <?= esc(format_tanggal_indo($ajuan['tgl_diajukan'] ?? null)) ?></span>
            </td>
        </tr>
    </table>
    <br>

    <?php if ($untukLembaga): ?>
    <?= $bagian('IDENTITAS LEMBAGA') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?= $baris('Nama Lembaga', $v($lembaga['nama_lembaga'] ?? null)) ?>
        <?= $baris('Nomor Legalitas', $v($lembaga['nomor_lembaga'] ?? null)) ?>
        <?= $baris('Bidang', $v($lembaga['bidang'] ?? null)) ?>
        <?= $baris('Sumber Pendanaan', $v($lembaga['sumber_pendanaan'] ?? null)) ?>
    </table>
    <br>

    <?= $bagian('ALAMAT DAN KONTAK') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?= $baris('Alamat', $v($lembaga['alamat_lembaga'] ?? null)) ?>
        <?= $baris('Desa/Kelurahan', $v($lembaga['nama_kelurahan'] ?? null)) ?>
        <?= $baris('Kecamatan', $v($lembaga['nama_kecamatan'] ?? null)) ?>
        <?= $baris('Kabupaten/Kota', $v($lembaga['nama_kabupaten'] ?? null)) ?>
        <?= $baris('Provinsi', $v($lembaga['nama_provinsi'] ?? null)) ?>
        <?= $baris('No. Telepon', $v($lembaga['nomor_telepon'] ?? null)) ?>
        <?= $baris('Email', $v($lembaga['email'] ?? null)) ?>
        <?= $baris('Website', $v($lembaga['website'] ?? null)) ?>
    </table>
    <br>

    <?= $bagian('REKENING DAN PENANGGUNG JAWAB') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?= $baris('Nomor Rekening', $v($lembaga['nomor_rekening'] ?? null)) ?>
        <?= $baris('Nama Pemilik Rekening', $v($lembaga['nama_pemilik_rekening'] ?? null)) ?>
        <?= $baris('Nama Penanggung Jawab', $v($lembaga['nama_pj'] ?? null)) ?>
        <?= $baris('Jabatan Penanggung Jawab', $v($lembaga['jabatan_pj'] ?? null)) ?>
    </table>
    <br>
    <?php else: ?>
    <?= $bagian('IDENTITAS CALON MUSTAHIK') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?= $baris('NIK', $v($individu['nik'] ?? null)) ?>
        <?= $baris('Nama Lengkap', $v($individu['nama_mustahik'] ?? null)) ?>
        <?= $baris('Jenis Kelamin', $v($individu['kelamin_mustahik'] ?? null)) ?>
        <?= $baris('Tempat, Tanggal Lahir', $v($tempatTglLahir)) ?>
        <?= $baris('Agama', $v($individu['agama_mustahik'] ?? null)) ?>
        <?= $baris('Status Pendidikan', $v($individu['status_pendidikan'] ?? null)) ?>
        <?= $baris('Status Marital', $v($individu['status_marital'] ?? null)) ?>
        <?= $baris('Pekerjaan', $v($individu['nama_pekerjaan'] ?? null)) ?>
        <?= $baris('Penghasilan', $v($individu['label_penghasilan'] ?? null)) ?>
        <?= $baris('Jumlah Keluarga', $v($individu['jml_keluarga'] ?? null)) ?>
    </table>
    <br>

    <?= $bagian('ALAMAT DAN KONTAK') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?= $baris('Alamat', $v($individu['alamat'] ?? null)) ?>
        <?= $baris('Desa/Kelurahan', $v($individu['nama_kelurahan'] ?? null)) ?>
        <?= $baris('Kecamatan', $v($individu['nama_kecamatan'] ?? null)) ?>
        <?= $baris('Kabupaten/Kota', $v($individu['nama_kabupaten'] ?? null)) ?>
        <?= $baris('Provinsi', $v($individu['nama_provinsi'] ?? null)) ?>
        <?= $baris('No. Handphone', $v($individu['no_handphone'] ?? null)) ?>
        <?= $baris('Email', $v($individu['email'] ?? null)) ?>
        <?= $baris('Nomor KK', $v($individu['kk'] ?? null)) ?>
    </table>
    <br>
    <?php endif; ?>

    <?= $bagian('DATA PEMOHON') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?= $baris('Nama Pemohon', $v($ajuan['nama_pemohon'] ?? null)) ?>
        <?= $baris('NIK Pemohon', $v($ajuan['nik'] ?? null)) ?>
        <?= $baris('No. Telepon Pemohon', $v(is_array($ajuan['telepon'] ?? null) ? implode('', $ajuan['telepon']) : ($ajuan['telepon'] ?? null))) ?>
    </table>
    <br>

    <?= $bagian('INFORMASI PENGAJUAN') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?= $baris('Kegiatan/Program', $v($ajuan['nama_program'] ?? null)) ?>
        <?= $baris('Nilai Diajukan', 'Rp ' . number_format((float) ($ajuan['nilai_diajukan'] ?? 0), 0, ',', '.')) ?>
        <?= $baris('Deskripsi Kebutuhan', $v($ajuan['deskripsi_ajuan'] ?? null)) ?>
    </table>
    <br>

    <?= $bagian('KELENGKAPAN DOKUMEN') ?>
    <table class="body_table" cellpadding="3" style="width: 100%;">
        <?php if (!$untukLembaga): ?>
            <?= $baris('KTP', $ada($individu['foto_ktp'] ?? null)) ?>
            <?= $baris('Kartu Keluarga (KK)', $ada($individu['foto_kk'] ?? null)) ?>
        <?php endif; ?>
        <?= $baris('Proposal', $ada($ajuan['file_proposal'] ?? null)) ?>
    </table>
    <br>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">Sragen, _________________</td>
        </tr>
        <tr>
            <td style="text-align: center;"><?= $untukLembaga ? 'Penanggung Jawab Lembaga,' : 'Calon Mustahik,' ?></td>
            <td style="text-align: center;">Petugas Lazismu Sragen,</td>
        </tr>
        <tr>
            <td><br><br><br><br></td>
            <td><br><br><br><br></td>
        </tr>
        <tr>
            <td style="text-align: center;"><strong><u><?= $v($untukLembaga ? ($lembaga['nama_pj'] ?? null) : ($individu['nama_mustahik'] ?? null)) ?></u></strong></td>
            <td style="text-align: center;">(_________________________)</td>
        </tr>
    </table>
</body>

</html>
