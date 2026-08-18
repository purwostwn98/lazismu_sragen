<html>

<head>
    <style>
        body {
            font-size: 12px;
        }

        p {
            font-size: 12px;
        }
    </style>
</head>

<?php
$beritaAcara      = $beritaAcara ?? [];
$ajuan            = $ajuan ?? [];
$pemohon          = $pemohon ?? [];
$individu         = $individu ?? null;
$lembaga          = $lembaga ?? null;
$terbilang        = $terbilang ?? '';
$namaKadivProgram = $namaKadivProgram ?? '';

$hariIndo = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
];
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

$tglPenyerahan = explode('-', $beritaAcara['tanggal_penyerahan']);
$hari          = $hariIndo[date('l', strtotime($beritaAcara['tanggal_penyerahan']))] ?? '';
$tgl           = $tglPenyerahan[2] ?? '';
$namaBulan     = $bulan[(int) $tglPenyerahan[1]];
$tahun         = (string) $tglPenyerahan[0];

$alamatPenerima = $individu['alamat'] ?? $lembaga['alamat_lembaga'] ?? '';

$bentuk = (int) $beritaAcara['bentuk_penyerahan'];
if ($bentuk === 2) {
    $caraPenyerahan = 'transfer' . (!empty($beritaAcara['rekening_penyerahan']) ? ' melalui rekening ' . $beritaAcara['rekening_penyerahan'] : '');
} elseif ($bentuk >= 3) {
    $caraPenyerahan = 'barang' . (!empty($beritaAcara['nama_barang']) ? ' berupa ' . $beritaAcara['nama_barang'] : '');
} else {
    $caraPenyerahan = 'tunai';
}
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
    <hr style="margin-top: 2px; margin-bottom: 12px;">

    <p style="text-align:center; font-weight:bold; text-decoration:underline; margin-bottom:0;">BERITA ACARA</p>
    <p style="text-align:center; font-weight:bold; text-decoration:underline; margin-top:2px; margin-bottom:14px;">SERAH TERIMA PENTASYARUFAN LAZISMU SRAGEN</p>

    <p align="justify">
        Pada hari ini <b><?= esc($hari) ?></b> tanggal <b><?= esc($tgl) ?></b> bulan <b><?= esc($namaBulan) ?></b>
        tahun <b><?= esc($tahun) ?></b> kami yang bertanda tangan di bawah ini:
    </p>

    <p style="margin-bottom:2px;">Pihak I.</p>
    <table cellpadding="2" style="width:100%; margin-left:12px;">
        <tr>
            <td style="width:5%;">1.</td>
            <td style="width:15%;">Nama</td>
            <td style="width:2%;">:</td>
            <td style="width:78%;"><?= $namaKadivProgram !== '' ? esc($namaKadivProgram) : '&nbsp;' ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>Jabatan</td>
            <td>:</td>
            <td>Kepala Divisi Program Lazismu Sragen</td>
        </tr>
    </table>

    <p style="margin-bottom:2px; margin-top:10px;">Pihak II.</p>
    <table cellpadding="2" style="width:100%; margin-left:12px;">
        <tr>
            <td style="width:5%;">2.</td>
            <td style="width:15%;">Nama</td>
            <td style="width:2%;">:</td>
            <td style="width:78%; border-bottom:1px solid #000;"><?= esc($beritaAcara['yang_menerima']) ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>Alamat</td>
            <td>:</td>
            <td style="border-bottom:1px solid #000;"><?= $alamatPenerima !== '' ? esc($alamatPenerima) : '&nbsp;' ?></td>
        </tr>
    </table>

    <p align="justify" style="margin-top:12px;">
        Dengan ini Lazismu Kabupaten Sragen telah menyerahkan uang pentasyarufan sebesar
        Rp <b><?= number_format((float) $beritaAcara['nilai_penyerahan'], 0, ',', '.') ?></b>
        (<i><?= esc(strtoupper($terbilang)) ?> RUPIAH</i>)
        melalui perantara Pihak I secara <b><?= esc($caraPenyerahan) ?></b> kepada mustahik atau Pihak II tersebut
        diperuntukkan untuk <b><?= esc($ajuan['deskripsi_ajuan'] ?? '-') ?></b>.
    </p>

    <p align="justify">
        Demikian surat serah terima ini dibuat dan ditandatangani oleh kedua belah pihak dengan sebenarnya
        untuk dipergunakan sebagaimana mestinya.
    </p>

    <table style="width:100%; margin-top:20px;" cellpadding="2">
        <tr>
            <td style="width:50%; text-align:center;">Penerima Tasyaruf</td>
            <td style="width:50%; text-align:center;">Kepala Divisi<br>Program Lazismu Sragen</td>
        </tr>
        <tr>
            <td><br><br><br></td>
            <td></td>
        </tr>
        <tr>
            <td style="text-align:center;">
                _____________________<br>
                <?= esc($beritaAcara['yang_menerima']) ?>
            </td>
            <td style="text-align:center;">
                _____________________<br>
                <?= $namaKadivProgram !== '' ? esc($namaKadivProgram) : '&nbsp;' ?>
            </td>
        </tr>
    </table>

    <table style="width:100%; margin-top:20px;" cellpadding="2">
        <tr>
            <td colspan="2"><br><br></td>
        </tr>
        <tr>
            <td style="width:50%; text-align:center;">SAKSI I</td>
            <td style="width:50%; text-align:center;">SAKSI II</td>
        </tr>
        <tr>
            <td><br><br><br></td>
            <td></td>
        </tr>
        <tr>
            <td style="text-align:center;">
                _____________________<br>
                <!-- ( &nbsp; ) -->
            </td>
            <td style="text-align:center;">
                _____________________<br>
                <!-- ( &nbsp; ) -->
            </td>
        </tr>
    </table>

    <table style="width:100%; margin-top:20px;" cellpadding="2">
        <tr>
            <td style="text-align:center;">Mengetahui</td>
        </tr>
        <tr>
            <td><br><br><br></td>
        </tr>
        <tr>
            <td style="text-align:center;">
                _____________________<br>
                <!-- ( &nbsp; ) -->
            </td>
        </tr>
    </table>
</body>

</html>