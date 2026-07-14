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
$suratTugas = $suratTugas ?? [];
$ajuan      = $ajuan ?? [];
$delegasi   = $delegasi ?? [];
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
            <td style="text-align: center;">
                <br><br><strong style="font-size: 16px;">SURAT TUGAS</strong>
                <br><span style="font-size: 10px;">Nomor Ajuan: <?= esc($ajuan['nomor_ajuan'] ?? '-') ?></span>
            </td>
        </tr>
    </table>
    <br>

    <p>Yang bertanda tangan di bawah ini:</p>
    <table class="body_table" cellpadding="2" style="width:100%; margin-top: 2px;">
        <tr>
            <td style="width: 30%;">Nama</td>
            <td style="width: 2%">:</td>
            <td style="width: 68%;"><?= esc($suratTugas['nama_penanggung_jawab']) ?></td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td><?= esc($suratTugas['jabatan']) ?></td>
        </tr>
        <tr>
            <td>Berdasarkan</td>
            <td>:</td>
            <td>
                <?= esc($suratTugas['berdasarkan']) ?>
                <?php if ($suratTugas['berdasarkan'] === 'Rapat Pengurus' && !empty($tanggalRapat)): ?>
                    pada tanggal <b><?= format_tanggal_indo($tanggalRapat) ?></b>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Memberikan tugas kepada</td>
            <td>:</td>
            <td>
                <?php if ($delegasi): ?>
                    <ol style="margin: 0; padding-left: 16px;">
                        <?php foreach ($delegasi as $d): ?>
                            <li><?= esc($d['nama_delegasi']) ?></li>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Dengan agenda</td>
            <td>:</td>
            <td><?= esc($suratTugas['agenda']) ?></td>
        </tr>
        <tr>
            <td colspan="3">
                Pada tanggal <b><?= format_tanggal_indo($suratTugas['tanggal_mulai'], true) ?></b>
                s/d <b><?= format_tanggal_indo($suratTugas['tanggal_selesai'], true) ?></b>
                di <b><?= esc($suratTugas['lokasi']) ?></b>
            </td>
        </tr>
    </table>

    <br>
    <p style="font-size: 13px;">Demikian Surat Tugas ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <table style="font-size: 13px; width: 100%;">
        <tr>
            <td style="width: 40%;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 40%; text-align: center;">Sragen, _________________</td>
        </tr>
        <tr>
            <td><br><br><br><br></td>
            <td></td>
            <td><br><br><br><br></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td style="text-align: center;"><?= esc($suratTugas['nama_penanggung_jawab']) ?></td>
        </tr>
    </table>
</body>

</html>
