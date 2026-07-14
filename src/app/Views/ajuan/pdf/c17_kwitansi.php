<html>

<head>
    <style>
        body {
            font-size: 13px;
        }

        p {
            font-size: 13px;
        }
    </style>
</head>

<?php
$beritaAcara = $beritaAcara ?? [];
$ajuan       = $ajuan ?? [];
$terbilang   = $terbilang ?? '';

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
$tanggalFmt    = $tglPenyerahan[2] . ' ' . $bulan[(int) $tglPenyerahan[1]] . ' ' . $tglPenyerahan[0];
?>

<body>
    <?php for ($salinan = 0; $salinan < 2; $salinan++): ?>
        <br>
        <table border="1" style="width:100%; border-collapse: collapse;">
            <tr>
                <td align="center" rowspan="2" style="width: 25%; text-align: center; border: 1px solid #000;">
                    <br>
                    <img align="center" width="90px" src="<?= FCPATH . 'assets/img/logo/logo.png' ?>" alt="">
                    <br>
                    <span style="font-size: 9px;">LAZISMU SRAGEN<br>Lembaga Amil Zakat</span>
                </td>
                <td style="width: 75%; text-align: right; font-size: 26px; border: 1px solid #000;">KUITANSI</td>
            </tr>
            <tr>
                <td style="text-align: left; border: 1px solid #000;">
                    <br>
                    <table cellpadding="4" style="width: 100%; font-size: 13px;">
                        <tr>
                            <td colspan="3" style="width: 100%; text-align: right; font-size: 16px;">
                                Nomor Reff: <span style="color: red; font-size: 16px;"><b>C17 - <?= esc($ajuan['nomor_ajuan'] ?? '-') ?></b></span>
                            </td>
                        </tr>
                        <tr style="font-size: 12px;">
                            <td style="width: 35%; font-size: 12px;">Telah Dibayarkan Kepada</td>
                            <td style="width: 5%;">:</td>
                            <td style="width: 60%;"><b><?= esc($beritaAcara['yang_menerima']) ?></b></td>
                        </tr>
                        <tr style="font-size: 12px;">
                            <td style="width: 35%;">Uang Sejumlah</td>
                            <td style="width: 5%;">:</td>
                            <td style="background-color: antiquewhite; width: 60%;"><i><?= esc(strtoupper($terbilang)) ?> RUPIAH</i></td>
                        </tr>
                        <tr style="font-size: 12px;">
                            <td style="width: 35%;">Untuk Pembayaran</td>
                            <td style="width: 5%;">:</td>
                            <td style="width: 60%;"><?= esc($ajuan['deskripsi_ajuan'] ?? '-') ?></td>
                        </tr>
                        <tr style="font-size: 12px;">
                            <td style="width: 35%;">Via</td>
                            <td style="width: 5%;">:</td>
                            <td style="width: 60%;"><?= esc($beritaAcara['ket_bentuk_penyerahan']) ?>
                                <?php if ((int) $beritaAcara['bentuk_penyerahan'] === 2 && !empty($beritaAcara['rekening_penyerahan'])): ?>
                                    , melalui rekening <b><?= esc($beritaAcara['rekening_penyerahan']) ?></b>
                                <?php elseif ((int) $beritaAcara['bentuk_penyerahan'] >= 3 && !empty($beritaAcara['nama_barang'])): ?>
                                    berupa: <b><?= esc($beritaAcara['nama_barang']) ?></b>
                                <?php endif; ?>
                                <br>
                                <br>
                                <span style="background-color: antiquewhite; padding: 20px;">
                                    <b style="font-size: 18px;">Rp <?= number_format((float) $beritaAcara['nilai_penyerahan'], 0, ',', '.') ?></b>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="width: 50%;"></td>
                            <td style="width: 50%;"><br><span>Sragen, <?= $tanggalFmt ?></span></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;">
                    <br><br>
                    <span style="font-size: 9px; text-align: center;">
                        Widoro, RT.37/RW.11, Dusun Kebayanan Widodo 1<br>
                        Sragen Wetan, Kec. Sragen, Kabupaten Sragen<br>
                        Jawa Tengah 57214<br>
                        Telepon: 0851-0000-0098
                    </span>
                </td>
                <td style="border: 1px solid #000;">
                    <table style="width: 100%; font-size: 13px;">
                        <tr>
                            <td style="width: 50%;">
                                Penerima,
                                <br><br><br><br><br>
                                (______________________)<br>
                                <i style="font-size: 10px;">nama jelas</i>
                            </td>
                            <td style="width: 50%;">
                                Keuangan,
                                <br><br><br><br><br>
                                (______________________)
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
    <?php endfor; ?>
</body>

</html>