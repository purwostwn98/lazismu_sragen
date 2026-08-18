<html>

<head>
    <style>
        body {
            font-size: 10px;
        }

        p {
            font-size: 10px;
            margin: 1px 0;
        }
    </style>
</head>

<?php
$beritaAcara = $beritaAcara ?? [];
$ajuan       = $ajuan ?? [];
$individu    = $individu ?? null;
$lembaga     = $lembaga ?? null;
$b3          = $b3 ?? null;
$terbilang   = $terbilang ?? '';
$barangKeterangan = $barangKeterangan ?? '';
$nilaiPenyerahan = $beritaAcara['nilai_penyerahan'] ?? 0;
$namaManajer      = $namaManajer ?? '';
$namaKadivProgram = $namaKadivProgram ?? '';
$namaPenandaTanganKetiga = $nilaiPenyerahan >= 5000000 ? $namaManajer : $namaKadivProgram;

$programKegiatan = '';
if (!empty($b3['nama_program'])) {
    $programKegiatan = $b3['nama_program'];
    if (!empty($b3['nama_kategori'])) {
        $programKegiatan .= ' (' . $b3['nama_kategori'] . ')';
    }
}

$tglPenyerahan = explode('-', $beritaAcara['tanggal_penyerahan']);
$dd = $tglPenyerahan[2] ?? '';
$mm = $tglPenyerahan[1] ?? '';
$yy = isset($tglPenyerahan[0]) ? substr($tglPenyerahan[0], 2, 2) : '';

$alamatPenerima  = $individu['alamat'] ?? $lembaga['alamat_lembaga'] ?? '';
$teleponPenerima = $lembaga['nomor_telepon'] ?? '';
$hpPenerima      = $individu['no_handphone'] ?? '';

$bentuk   = (int) $beritaAcara['bentuk_penyerahan'];
$isDana   = in_array($bentuk, [1, 2], true);
$isBarang = in_array($bentuk, [3, 4, 5], true);

$danaKeterangan = '';
if ($isDana) {
    $danaKeterangan = 'Rp ' . number_format((float) $beritaAcara['nilai_penyerahan'], 0, ',', '.')
        . ' (' . esc($beritaAcara['ket_bentuk_penyerahan']) . ')';
    if ($bentuk === 2 && !empty($beritaAcara['rekening_penyerahan'])) {
        $danaKeterangan .= ' - Rek. ' . esc($beritaAcara['rekening_penyerahan']);
    }
}

/** Small bordered box used both as a checkbox and as a date-digit cell. */
$box = static function ($content = '', bool $bold = false): string {
    $inner = $content === '' ? '&nbsp;' : ($bold ? '<b>' . esc((string) $content) . '</b>' : esc((string) $content));

    return '<table cellpadding="0" cellspacing="0"><tr><td style="width:12px;height:12px;text-align:center;vertical-align:middle;border:1px solid #000;font-size:8px;">' . $inner . '</td></tr></table>';
};
?>

<body>
    <?php for ($salinan = 0; $salinan < 2; $salinan++): ?>
        <table style="width:100%;" cellpadding="2" cellspacing="0">
            <tr>
                <td style="width:26%; text-align:center; vertical-align:top; border-right: 1px solid #000;">
                    <img width="100" src="<?= FCPATH . 'assets/img/logo/logo.png' ?>" alt="">
                    <p style="font-size:6.5px; text-align:center; line-height:1.35;">
                        Lembaga Amil Zakat Nasional<br>
                        SK. Menteri Agama RI No. 90 Tahun 2022<br>
                        Tanggal 26 Januari 2022
                    </p>
                    <p style="font-size:6.5px; line-height:1.35;">
                        Widoro Rt 37 Rw 11, Sragen Wetan,<br>
                        Sragen (57214)<br>
                        Call &amp; Sms Center: 085 1000 000 98<br>
                        Email: lazismu.sragen@gmail.com<br>
                        WA/FB: 085 1000 000 98 / lazismu sragen<br>
                        Twitter/IG: @lazismu_sragen / lazismu_sragen<br>
                        www.lazismusragen.org
                    </p>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <p style=" font-size:6.5px; font-style:italic; line-height:1.35; margin-top:4px;">
                        Ya Allah, limpahkanlah pahala kepada mereka atas yang telah mereka
                        keluarkan dan jadikanlah bagi mereka suci dan mensucikan serta
                        berkahilah mereka dan sisa hartanya.
                    </p>
                </td>
                <td style="width:74%; vertical-align:top; padding-left: 10px;">
                    <table style="width:100%;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td colspan="7" style="text-align:right; font-size:16px; font-weight:bold;">TANDA BUKTI PENYALURAN</td>
                        </tr>
                        <tr>
                            <td style="width:50%; font-size:8px; color:#666;">Ref: C17-<?= esc($ajuan['nomor_ajuan'] ?? '-') ?></td>
                            <td style="width:19%; font-size:9px; text-align:right;">Tanggal :<span style="color:white;">--</span></td>
                            <td style="width:8%; height:12px; text-align:center; border:1px solid #000; font-size:8px;"><b><?= esc($dd) ?></b></td>
                            <td style="width:4%; text-align:center;">/</td>
                            <td style="width:8%; height:12px; text-align:center; border:1px solid #000; font-size:8px;"><b><?= esc($mm) ?></b></td>
                            <td style="width:4%; text-align:center;">/</td>
                            <td style="width:8%; height:12px; text-align:center; border:1px solid #000; font-size:8px;"><b><?= esc($yy) ?></b></td>
                        </tr>
                    </table>

                    <p style="margin-top:5px;">Telah Terima dari LAZISMU</p>
                    <table style="width:100%; font-size:10px; margin-top:30px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td><b>Penerima</b></td>
                        </tr>
                    </table>

                    <table style="width:100%; font-size:10px; margin-bottom: 10px;" cellpadding="2" cellspacing="0">
                        <tr>
                            <td style="width:18%;">Nama</td>
                            <td style="width:2%;">:</td>
                            <td colspan="3" style="border-bottom: 1px solid #000;"><b><?= esc($beritaAcara['yang_menerima']) ?></b></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td colspan="3" style="border-bottom: 1px solid #000;"><?= esc($alamatPenerima) ?: '&nbsp;' ?></td>
                        </tr>
                        <tr>
                            <td>Telepon/Fax</td>
                            <td>:</td>
                            <td style="width:33%; border-bottom: 1px solid #000;"><?= esc($teleponPenerima) ?: '&nbsp;' ?></td>
                            <td style="width:8%;">Hp :</td>
                            <td style="width:37%; border-bottom: 1px solid #000;"><?= esc($hpPenerima) ?: '&nbsp;' ?></td>
                        </tr>
                    </table>

                    <table style="width:100%; font-size:10px; margin-top:30px;" cellpadding="0" cellspacing="0">
                        <tr>
                            <td><b>Bentuk Penyaluran</b></td>
                        </tr>
                    </table>
                    <table style="width:100%; font-size:10px;" cellpadding="2" cellspacing="0">
                        <tr>
                            <td style="width:5%;"><?= $box() ?></td>
                            <td style="width:13%;">Program</td>
                            <td style="width:2%;">:</td>
                            <td style="border-bottom: 1px solid #000; width:80%;"><?= $programKegiatan !== '' ? esc($programKegiatan) : '&nbsp;' ?></td>
                        </tr>
                        <tr>
                            <td style="width:5%;"><?= $box($isDana ? 'X' : '') ?></td>
                            <td style="width:13%;">Dana</td>
                            <td style="width:2%;">:</td>
                            <td style="border-bottom: 1px solid #000; width:80%;"><?= $isDana ? $danaKeterangan : '&nbsp;' ?></td>
                        </tr>
                        <tr>
                            <td style="width:5%;"><?= $box($isBarang ? 'X' : '') ?></td>
                            <td style="width:13%;">Barang</td>
                            <td style="width:2%;">:</td>
                            <td style="border-bottom: 1px solid #000; width:80%;"><?= $isBarang ? $barangKeterangan : '&nbsp;' ?></td>
                        </tr>
                    </table>

                    <table style="width:100%; font-size:9px; margin-top:1px;" cellpadding="1.5" cellspacing="0">
                        <tr>
                            <th style="width:10%; border: 1px solid #000;">No.</th>
                            <th style="width:65%; border: 1px solid #000;">Jenis Barang</th>
                            <th style="width:25%; border: 1px solid #000;">Jumlah</th>
                        </tr>
                        <?php for ($baris = 1; $baris <= 3; $baris++): ?>
                            <tr>
                                <td style="border: 1px solid #000; text-align:center;"><?= $baris ?></td>
                                <td style="border: 1px solid #000;"><?= ($baris === 1 && $isBarang) ? esc($beritaAcara['nama_barang'] ?? '') : '&nbsp;' ?></td>
                                <td style="border: 1px solid #000;">&nbsp;</td>
                            </tr>
                        <?php endfor; ?>
                    </table>

                    <table style="width:100%; font-size:10px; margin-top:3px;" cellpadding="2" cellspacing="0">
                        <tr>
                            <td style="width:18%;">Terbilang</td>
                            <td style="width:2%;">:</td>
                            <td style="border-bottom: 1px solid #000; width:80%;"><i><?= esc(strtoupper($terbilang)) ?> RUPIAH</i></td>
                        </tr>
                    </table>
                    <table style="width:100%; font-size:10px; margin-top:8px;" cellpadding="2" cellspacing="0">
                        <tr colspan="3">
                            <td style="width:100%; text-align:center;">
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:33%; text-align:center;">
                                Penerima,
                                <br><br><br><br>
                                (______________________)<br>
                                <i style="font-size: 8px;"><?= !empty($beritaAcara['yang_menerima']) ? esc($beritaAcara['yang_menerima']) : 'nama jelas' ?></i>
                            </td>
                            <td style="width:33%; text-align:center;">
                                Admin,
                                <br><br><br><br>
                                (______________________)<br>
                                <!-- <i style="font-size: 8px;">nama jelas</i> -->
                            </td>
                            <td style="width:34%; text-align:center;">
                                <?= $nilaiPenyerahan >= 5000000 ? 'Manajer,' : 'Kepala Divisi Program,'; ?>
                                <br><br><br><br>
                                (______________________)<br>
                                <i style="font-size: 8px;"><?= $namaPenandaTanganKetiga !== '' ? esc($namaPenandaTanganKetiga) : 'nama jelas' ?></i>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <?php if ($salinan === 0): ?>
            <table style="width:100%; margin: 3px 0;" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="border-top: 1px dashed #999; font-size:1px;">&nbsp;</td>
                </tr>
            </table>
        <?php endif; ?>
    <?php endfor; ?>
</body>

</html>