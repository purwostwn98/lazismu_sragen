<html>

<head>
    <style>
        body {
            font-size: 13px;
        }

        .body_table td {
            font-size: 12px;
        }

        p {
            font-size: 12px;
        }
    </style>
</head>

<?php
$beritaAcara  = $beritaAcara ?? [];
$ajuan        = $ajuan ?? [];
$pemohon      = $pemohon ?? [];
$lembaga      = $lembaga ?? [];
$tanggalRapat = $tanggalRapat ?? null;
$terbilang    = $terbilang ?? '';

$bulan = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];
$fmtTgl = static fn (array $t) => $t[2] . ' ' . $bulan[(int) $t[1]] . ' ' . $t[0];

$tglPenyerahan = explode('-', $beritaAcara['tanggal_penyerahan']);
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

    <!-- C1: Permohonan Pencairan Dana -->
    <table style="width: 100%;">
        <tr>
            <td rowspan="2" style="width: 20%;"></td>
            <td style="text-align: center; font-size: 14px; width: 60%;"><br><br><strong>PERMOHONAN PENCAIRAN DANA</strong></td>
            <td rowspan="2" style="text-align: right; width: 20%; font-size: 11px;"><b>C1</b></td>
        </tr>
    </table>
    <br><br>

    <table class="body_table" cellpadding="2" style="width:100%">
        <tr>
            <td style="width: 15%;">No. Pengaju</td>
            <td style="width: 2%">:</td>
            <td style="width: 33%;"><?= esc($ajuan['nomor_ajuan'] ?? '-') ?></td>
            <td style="width: 15%;">Nama Mustahik</td>
            <td style="width: 2%">:</td>
            <td style="width: 33%;"><?= esc($beritaAcara['yang_menerima']) ?></td>
        </tr>
    </table>
    <br>
    <table border="1" style="width: 100%; font-size: 12px; margin: 0; border-collapse: collapse;">
        <tr>
            <th style="width: 10%; border: 1px solid #000;"><b>No</b></th>
            <th style="width: 65%; border: 1px solid #000;"><b>Keterangan</b></th>
            <th style="width: 25%; border: 1px solid #000;"><b>Jumlah</b></th>
        </tr>
        <tr>
            <td style="border: 1px solid #000;">1</td>
            <td style="border: 1px solid #000;"><?= esc($ajuan['deskripsi_ajuan'] ?? '-') ?></td>
            <td style="border: 1px solid #000;">Rp <?= number_format((float) $beritaAcara['nilai_penyerahan'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th align="center" colspan="2" style="border: 1px solid #000;"><b>Total</b></th>
            <th style="border: 1px solid #000;"><b>Rp <?= number_format((float) $beritaAcara['nilai_penyerahan'], 0, ',', '.') ?></b></th>
        </tr>
        <tr>
            <th colspan="3" style="border: 1px solid #000;">Terbilang: <i><?= esc($terbilang) ?> rupiah</i></th>
        </tr>
    </table>
    <span style="font-size: 12px;">
        Pencairan Dana: <b><?= esc($beritaAcara['yang_menerima']) ?></b> |
        Golongan Asnaf: <b><?= esc($beritaAcara['ket_kategori_penerima']) ?></b> |
        Sumber Dana: <b><?= esc($beritaAcara['dana_dari']) ?></b>
    </span>
    <p style="text-align: right;">Sragen, <?= $fmtTgl($tglPenyerahan) ?></p>
    <table style="font-size: 12px; width: 100%;">
        <tr>
            <td style="width: 33%; text-align: center;">Disetujui,</td>
            <td style="width: 33%; text-align: center;">Diperiksa,</td>
            <td style="width: 33%; text-align: center;">Pemohon,</td>
        </tr>
        <tr>
            <td><br><br></td>
            <td><br><br></td>
            <td><br><br></td>
        </tr>
        <tr>
            <td style="text-align: center;">..........................<br>Manajer</td>
            <td style="text-align: center;">..........................<br>Keuangan</td>
            <td style="text-align: center;">..........................<br><?= esc($beritaAcara['yang_menerima']) ?></td>
        </tr>
    </table>
    <p>
        <b>Catatan lain-lain:</b><br>
        <?php if ((int) $beritaAcara['bentuk_penyerahan'] === 2 && !empty($beritaAcara['rekening_penyerahan'])): ?>
            Pembayaran ditransfer melalui rekening <b><?= esc($beritaAcara['rekening_penyerahan']) ?></b>
        <?php elseif ((int) $beritaAcara['bentuk_penyerahan'] >= 3 && !empty($beritaAcara['nama_barang'])): ?>
            Nama barang yang diserahkan: <b><?= esc($beritaAcara['nama_barang']) ?></b>
        <?php endif; ?>
    </p>
    <hr>

    <!-- B3, C2: Berita Acara dan Penetapan Kategori Bantuan -->
    <table style="width: 100%;">
        <tr>
            <td rowspan="2" style="width: 20%;"></td>
            <td style="text-align: center; font-size: 14px; width: 60%;"><br><br><strong>BERITA ACARA<br>DAN<br>PENETAPAN KATEGORI BANTUAN</strong></td>
            <td rowspan="2" style="text-align: right; width: 20%; font-size: 11px;"><b>B3, C2</b></td>
        </tr>
        <tr>
            <td style="font-size: 11px; text-align: center; width: 60%;">Nomor Ajuan: <?= esc($ajuan['nomor_ajuan'] ?? '-') ?></td>
        </tr>
    </table>

    <p align="justify">
        Pada tanggal <b><?= $fmtTgl($tglPenyerahan) ?></b> bertempat di <b><?= esc($beritaAcara['lokasi_penyerahan']) ?></b>
        berdasarkan <b><?= esc($beritaAcara['berdasarkan']) ?></b>
        <?php if ($beritaAcara['berdasarkan'] === 'Rapat Pengurus' && (int) $ajuan['status_ajuan'] >= 3 && $tanggalRapat): ?>
            <?php $tglRapat = explode('-', explode(' ', $tanggalRapat)[0]); ?>
            pada tanggal <b><?= $fmtTgl($tglRapat) ?></b>.
        <?php endif; ?>
        <br>Telah disalurkan bantuan LAZISMU Sragen selaku Pihak Pertama berupa <b><?= esc($beritaAcara['ket_bentuk_penyerahan']) ?></b>
        <br>senilai <b>Rp <?= number_format((float) $beritaAcara['nilai_penyerahan'], 0, ',', '.') ?></b>
        <?php if ((int) $beritaAcara['bentuk_penyerahan'] === 2 && !empty($beritaAcara['rekening_penyerahan'])): ?>
            melalui rekening <b><?= esc($beritaAcara['rekening_penyerahan']) ?></b>
        <?php elseif ((int) $beritaAcara['bentuk_penyerahan'] >= 3 && !empty($beritaAcara['nama_barang'])): ?>
            dengan nama barang: <b><?= esc($beritaAcara['nama_barang']) ?></b>
        <?php endif; ?>
        dana dari <b><?= esc($beritaAcara['dana_dari']) ?></b> diberikan kepada penerima dengan kategori <b><?= esc($beritaAcara['ket_kategori_penerima']) ?></b>.
        <br>Kepada pihak kedua:
    </p>
    <table class="body_table" cellpadding="2" style="width:100%">
        <tr>
            <td style="width: 30%;">Nama Pengaju</td>
            <td style="width: 2%">:</td>
            <td style="width: 68%;"><?= esc($ajuan['nama_pemohon'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td><?= esc($ajuan['nik'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td><?= esc($pemohon['alamat_detail'] ?? '-') ?>, <?= esc($pemohon['nama_kelurahan'] ?? '-') ?>, <?= esc($pemohon['nama_kecamatan'] ?? '-') ?>, <?= esc($pemohon['nama_kabupaten'] ?? '-') ?>, <?= esc($pemohon['nama_provinsi'] ?? '-') ?></td>
        </tr>
        <tr>
            <td>Keperuntukan dana</td>
            <td>:</td>
            <td><?= esc($ajuan['deskripsi_ajuan'] ?? '-') ?></td>
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
    </table>
    <p>
        Adapun bantuan ini bersifat <b><?= esc($ajuan['sifat_bantuan'] ?? '-') ?></b> dan pihak kedua/yang menerima
        berkewajiban membuat laporan pertanggungjawaban (LPJ) sesuai ketentuan yang berlaku apabila dipersyaratkan.
        Demikian Berita Acara ini dibuat untuk dipergunakan sebagaimana mestinya.
    </p>
    <br>
    <table style="font-size: 12px; width: 100%;">
        <tr>
            <td style="width: 40%; text-align: center;">Pihak Kedua/Yang Menerima</td>
            <td style="width: 20%;"></td>
            <td style="width: 40%; text-align: center;">Pihak Pertama/Yang Menyerahkan</td>
        </tr>
        <tr>
            <td><br><br><br></td>
            <td></td>
            <td><br><br><br></td>
        </tr>
        <tr>
            <td style="text-align: center;"><?= esc($beritaAcara['yang_menerima']) ?></td>
            <td></td>
            <td style="text-align: center;"><?= esc($beritaAcara['yang_bertandatangan']) ?></td>
        </tr>
    </table>
</body>

</html>
