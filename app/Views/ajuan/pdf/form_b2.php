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
use App\Models\FormB2Model;

$ajuan    = $ajuan ?? [];
$individu = $individu ?? [];
$b2       = $b2 ?? [];
$model    = new FormB2Model();

// Blank/null fields print as "-" rather than an empty gap in the form.
$v = static fn ($nilai): string => esc(($nilai !== null && trim((string) $nilai) !== '') ? (string) $nilai : '-');

$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$tglSurvei = $b2['updated_at'] ?? $b2['created_at'] ?? null;
$hariTanggal = $tglSurvei
    ? ($hariIndo[date('l', strtotime($tglSurvei))] ?? '') . ', ' . format_tanggal_indo($tglSurvei)
    : '-';

$alamatLengkap = implode(', ', array_filter([
    trim((string) ($individu['alamat'] ?? '')),
    $individu['nama_kelurahan'] ?? null,
    $individu['nama_kecamatan'] ?? null,
    $individu['nama_kabupaten'] ?? null,
]));

// Grey heading bar as the first row of a 3-column section table.
$bar = static fn (string $teks): string => '<tr><td colspan="3" style="background-color: #e5e7eb; font-weight: bold;">' . $teks . '</td></tr>';

// One label : value row of the "Data Survei" / "Hasil Skor" tables.
$baris = static function (string $label, string $isi): string {
    return '<tr>'
        . '<td style="width: 30%; border-bottom: 1px solid #cccccc;">' . $label . '</td>'
        . '<td style="width: 3%; border-bottom: 1px solid #cccccc;">:</td>'
        . '<td style="width: 67%; border-bottom: 1px solid #cccccc;">' . $isi . '</td>'
        . '</tr>';
};

// TCPDF's built-in Helvetica has no glyph for these (q29's "≤ 500 m²" would
// print as "?"), so spell them the way the historical answer table does.
$transliterasi = static fn (string $teks): string => strtr($teks, ['≤' => '<=', '≥' => '>=']);

// A group of scored questions: the grey bar doubles as the column header.
$kelompokPertanyaan = static function (string $judulKelompok, array $keys, int $nomorAwal) use ($b2, $model, $transliterasi): string {
    $html = '<table cellpadding="3" style="width: 100%;">'
        . '<tr style="background-color: #e5e7eb; font-weight: bold;">'
        . '<td style="width: 52%;" colspan="2">' . $judulKelompok . '</td>'
        . '<td style="width: 36%;">Jawaban</td>'
        . '<td style="width: 12%; text-align: center;">Skor</td>'
        . '</tr>';

    $no = $nomorAwal;
    foreach ($keys as $key) {
        $html .= '<tr>'
            . '<td style="width: 6%; border-bottom: 1px solid #cccccc;">' . $no . '.</td>'
            . '<td style="width: 46%; border-bottom: 1px solid #cccccc;">' . esc(FormB2Model::LABEL_PERTANYAAN[$key] ?? $key) . '</td>'
            . '<td style="width: 36%; border-bottom: 1px solid #cccccc;">' . esc($transliterasi($model->jawabanLabel($b2, $key))) . '</td>'
            . '<td style="width: 12%; text-align: center; border-bottom: 1px solid #cccccc;">' . (int) ($b2[$key] ?? 0) . '</td>'
            . '</tr>';
        $no++;
    }

    return $html . '</table>';
};

$semua = FormB2Model::PERTANYAAN_SKOR;

$barangElektronik = [
    'tv'         => 'Televisi',
    'hp'         => 'HP',
    'kulkas'     => 'Kulkas',
    'magic_com'  => 'Magic Com',
    'mesin_cuci' => 'Mesin Cuci',
    'setrika'    => 'Setrika Listrik',
    'dispenser'  => 'Dispenser',
];
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
                <strong style="font-size: 15px;">FORM B2</strong>
                <br><strong style="font-size: 12px;">FORMULIR SURVEY CALON MUSTAHIK</strong>
                <br><span style="font-size: 9px;">Nomor Ajuan: <?= $v($ajuan['nomor_ajuan'] ?? null) ?></span>
            </td>
        </tr>
    </table>
    <br>

    <table nobr="true" class="body_table" cellpadding="3" style="width: 100%;">
        <?= $bar('DATA SURVEI MUSTAHIK') ?>
        <?= $baris('Hari dan Tanggal', esc($hariTanggal)) ?>
        <?= $baris('Nama Calon Mustahik', $v($individu['nama_mustahik'] ?? null)) ?>
        <?= $baris('Alamat Lengkap', $v($alamatLengkap)) ?>
        <?= $baris('No. Telp', $v($individu['no_handphone'] ?? null)) ?>
    </table>
    <br>

    <div class="body_table">
        <?= $kelompokPertanyaan('KONDISI EKONOMI', array_slice($semua, 0, 8), 1) ?>
        <br>
        <?= $kelompokPertanyaan('KONDISI KELUARGA', array_slice($semua, 8, 5), 9) ?>
        <br>
        <?= $kelompokPertanyaan('KONDISI TEMPAT TINGGAL', array_slice($semua, 13, 10), 14) ?>
        <br>
        <?= $kelompokPertanyaan('MAKANAN SEHARI-HARI', array_slice($semua, 23, 5), 24) ?>
        <br>
        <?= $kelompokPertanyaan('KEPEMILIKAN ASET', array_slice($semua, 28, 3), 29) ?>
        <br>
    </div>

    <div class="body_table">
    <table nobr="true" cellpadding="3" style="width: 100%;">
        <tr style="background-color: #e5e7eb; font-weight: bold;">
            <td style="width: 52%;">BARANG ELEKTRONIK YANG DIMILIKI</td>
            <td style="width: 12%; text-align: center;">Jumlah</td>
            <td style="width: 36%;">Status</td>
        </tr>
        <?php foreach ($barangElektronik as $key => $nama): ?>
            <?php $jumlah = (int) ($b2['elektronik_' . $key . '_jumlah'] ?? 0); ?>
            <tr>
                <td style="border-bottom: 1px solid #cccccc;"><?= esc($nama) ?></td>
                <td style="text-align: center; border-bottom: 1px solid #cccccc;"><?= $jumlah > 0 ? $jumlah : '-' ?></td>
                <td style="border-bottom: 1px solid #cccccc;"><?= $jumlah > 0 ? $v($b2['elektronik_' . $key . '_status'] ?? null) : '-' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!empty($b2['elektronik_lainnya_nama'])): ?>
            <?php $jumlahLain = (int) ($b2['elektronik_lainnya_jumlah'] ?? 0); ?>
            <tr>
                <td style="border-bottom: 1px solid #cccccc;"><?= esc($b2['elektronik_lainnya_nama']) ?></td>
                <td style="text-align: center; border-bottom: 1px solid #cccccc;"><?= $jumlahLain > 0 ? $jumlahLain : '-' ?></td>
                <td style="border-bottom: 1px solid #cccccc;"><?= $jumlahLain > 0 ? $v($b2['elektronik_lainnya_status'] ?? null) : '-' ?></td>
            </tr>
        <?php endif; ?>
    </table>
    </div>
    <br>

    <div class="body_table">
        <?= $kelompokPertanyaan('BANTUAN DARI LEMBAGA LAIN', array_slice($semua, 31, 1), 32) ?>
    </div>
    <br>

    <table nobr="true" class="body_table" cellpadding="3" style="width: 100%;">
        <?= $bar('HASIL SKOR MUSTAHIK') ?>
        <?= $baris('Total Skor', '<strong>' . (int) ($b2['total_skor'] ?? 0) . '</strong>') ?>
        <?= $baris('Kategori Kelayakan', '<strong>' . $v($b2['kategori_kelayakan'] ?? null) . '</strong>') ?>
        <tr>
            <td colspan="3" style="font-size: 8px;">Total skor 92 ke atas: Sangat Perlu Dibantu &nbsp;|&nbsp; 65 - 91: Layak Dibantu &nbsp;|&nbsp; di bawah 65: Belum Layak Dibantu</td>
        </tr>
    </table>
    <br>

    <table nobr="true" class="body_table" cellpadding="3" style="width: 100%;">
        <?= $bar('LAINNYA') ?>
        <?= $baris('Catatan Tambahan untuk Mustahik', nl2br($v($b2['catatan_tambahan'] ?? null))) ?>
        <?= $baris('Mustahik bersedia data ini dipublikasikan', ((int) ($b2['bersedia_dipublikasikan'] ?? 0) === 1) ? 'Ya' : 'Tidak') ?>
    </table>
    <br>

    <table nobr="true" class="body_table" style="width: 100%;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">Sragen, _________________</td>
        </tr>
        <tr>
            <td style="text-align: center;">Responden,</td>
            <td style="text-align: center;">Surveyor,</td>
        </tr>
        <tr>
            <td><br><br><br><br></td>
            <td><br><br><br><br></td>
        </tr>
        <tr>
            <td style="text-align: center;"><strong><u><?= $v($individu['nama_mustahik'] ?? null) ?></u></strong></td>
            <td style="text-align: center;">(_________________________)</td>
        </tr>
    </table>
</body>

</html>
