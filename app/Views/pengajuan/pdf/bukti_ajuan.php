<html>

<head>
    <style>
        body {
            font-size: 13px;
        }

        .body_table td {
            font-size: 13px;
        }
    </style>
</head>

<?php
$bulan = [
    1 => 'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];
$ajuan = $ajuan ?? [];
$individu = $individu ?? [];
$lembaga = $lembaga ?? [];

$time = explode(' ', $ajuan['tgl_diajukan']);
$tgl  = explode('-', $time[0]);
$tanggal = $tgl[2] . ' ' . $bulan[(int) $tgl[1]] . ' ' . $tgl[0] . ' ' . ($time[1] ?? '');
?>

<body>
    <br>
    <table cellpadding="1" style="width:100%">
        <tr>
            <td rowspan="3" style="width: 20%; text-align: left;">
                <img width="70px" src="<?= FCPATH . 'assets/img/logo/logo.png' ?>" alt="">
            </td>
            <td style="width: 80%; text-align: left; font-size: 16px;"><strong>LAZISMU SRAGEN</strong></td>
        </tr>
        <tr>
            <td style="font-size: 9px; text-align: left;">Widoro, RT.37/RW.11, Dusun Kebayanan Widodo 1, Sragen Wetan, Kec. Sragen, Kabupaten Sragen, Jawa Tengah 57214<br>Telepon: 0851-0000-0098</td>
        </tr>
    </table>
    <hr style="margin-top: 2px;">
    <table style="width: 100%;">
        <tr>
            <td style="text-align: center;"><br><br>Bukti Pengajuan Bantuan<br><strong style="font-size: 16px;">Nomor Ajuan: <?= esc($ajuan['nomor_ajuan']) ?></strong></td>
        </tr>
    </table>
    <br>
    <br>

    <table class="body_table" cellpadding="2" style="width:100%; margin-top: 2px;">
        <tr>
            <td style="width: 30%;">Tanggal Ajuan</td>
            <td style="width: 2%">:</td>
            <td style="width: 68%;"><?= esc($tanggal) ?></td>
        </tr>
        <tr>
            <td>Nama Pengaju</td>
            <td>:</td>
            <td><?= esc($ajuan['nama_pemohon'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td><?= esc($ajuan['nik']) ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td><?= esc($pemohon['alamat_detail'] ?? '-') ?>,<br><?= esc($pemohon['nama_kelurahan'] ?? '-') ?>, <?= esc($pemohon['nama_kecamatan'] ?? '-') ?>, <?= esc($pemohon['nama_kabupaten'] ?? '-') ?>, <?= esc($pemohon['nama_provinsi'] ?? '-') ?></td>
        </tr>
        <?php if ($ajuan['jenis_ajuan'] === 'Lembaga' && $lembaga): ?>
            <tr>
                <td>Nama Lembaga</td>
                <td>:</td>
                <td><?= esc($lembaga['nama_lembaga']) ?></td>
            </tr>
            <tr>
                <td>Alamat Lembaga</td>
                <td>:</td>
                <td><?= esc($lembaga['alamat_lembaga']) ?></td>
            </tr>
            <tr>
                <td>Nomor Lembaga</td>
                <td>:</td>
                <td><?= esc($lembaga['nomor_lembaga']) ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td>Kegiatan Bantuan</td>
            <td>:</td>
            <td><?= esc($ajuan['nama_program'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>Jenis Ajuan</td>
            <td>:</td>
            <td><?= esc($ajuan['jenis_ajuan']) ?></td>
        </tr>
        <tr>
            <td>Status Ajuan</td>
            <td>:</td>
            <td><?= esc($ajuan['keterangan_status'] ?? '-') ?></td>
        </tr>
    </table>

    <?php if ($ajuan['jenis_ajuan'] === 'Individu' && $individu): ?>
        <br>
        <p style="margin-bottom: 2px;"><strong>Data Mustahik</strong></p>
        <table class="body_table" cellpadding="2" style="width:100%;">
            <tr>
                <td style="width: 30%;">Nama Mustahik</td>
                <td style="width: 2%">:</td>
                <td style="width: 68%;"><?= esc($individu['nama_mustahik'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td><?= esc($individu['kelamin_mustahik'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td><?= esc($individu['tempat_lahir'] ?? '-') ?>, <?= esc($individu['tgl_lahir'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Agama</td>
                <td>:</td>
                <td><?= esc($individu['agama_mustahik'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td><?= esc($individu['alamat'] ?? '-') ?>,<br><?= esc($individu['nama_kelurahan'] ?? '-') ?>, <?= esc($individu['nama_kecamatan'] ?? '-') ?>, <?= esc($individu['nama_kabupaten'] ?? '-') ?>, <?= esc($individu['nama_provinsi'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Status Pendidikan</td>
                <td>:</td>
                <td><?= esc($individu['status_pendidikan'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Status Marital</td>
                <td>:</td>
                <td><?= esc($individu['status_marital'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Pekerjaan</td>
                <td>:</td>
                <td><?= esc($individu['nama_pekerjaan'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Penghasilan</td>
                <td>:</td>
                <td><?= esc($individu['label_penghasilan'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Jumlah Keluarga</td>
                <td>:</td>
                <td><?= esc((string) ($individu['jml_keluarga'] ?? '-')) ?></td>
            </tr>
            <tr>
                <td>No. Handphone</td>
                <td>:</td>
                <td><?= esc($individu['no_handphone'] ?? '-') ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td>:</td>
                <td><?= esc($individu['email'] ?? '-') ?></td>
            </tr>
        </table>
    <?php endif; ?>

    <br>
    <p><i><strong>Simpan nomor ajuan ini!</strong> Nomor ajuan ini digunakan untuk mengecek status pengajuan Anda melalui halaman Cek Status Ajuan dengan memasukkan NIK dan Nomor Ajuan.</i></p>
    <p style="font-size: 10px;"><i>Dokumen ini dicetak secara elektronik melalui aplikasi resmi Lazismu Sragen dan sah tanpa memerlukan tanda tangan basah. Pindai kode QR di bawah ini untuk memverifikasi keaslian dokumen.</i></p>
</body>

</html>