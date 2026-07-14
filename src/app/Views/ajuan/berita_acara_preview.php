<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$beritaAcara  = $beritaAcara ?? [];
$ajuan        = $ajuan ?? [];
$pemohon      = $pemohon ?? [];
$lembaga      = $lembaga ?? [];
$tanggalRapat = $tanggalRapat ?? null;

$bulan = [
  1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];
$fmtTgl = static fn (array $t) => $t[2] . ' ' . $bulan[(int) $t[1]] . ' ' . $t[0];

$tglPenyerahan = explode('-', $beritaAcara['tanggal_penyerahan']);
?>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
  <div>
    <h4 class="mb-1">Berita Acara <?= esc($beritaAcara['nomor_ajuan']) ?></h4>
    <p class="text-body-secondary mb-0">Pratinjau dokumen serah terima bantuan.</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="<?= base_url('ajuan/' . $beritaAcara['nomor_ajuan']) ?>" class="btn btn-label-secondary btn-sm">
      <i class="icon-base ti tabler-arrow-left me-1"></i>Kembali
    </a>
    <a href="<?= base_url('ajuan/berita-acara/' . $beritaAcara['id_berita_acara'] . '/cetak') ?>" target="_blank" class="btn btn-label-danger btn-sm">
      <i class="icon-base ti tabler-file-type-pdf me-1"></i>Cetak C1, C2, B3
    </a>
    <a href="<?= base_url('ajuan/berita-acara/' . $beritaAcara['id_berita_acara'] . '/kuitansi') ?>" target="_blank" class="btn btn-label-secondary btn-sm">
      <i class="icon-base ti tabler-receipt me-1"></i>Cetak C17 Kuitansi
    </a>
    <?php if (!empty($beritaAcara['file_berita_acara'])): ?>
      <a href="<?= base_url('ajuan/berita-acara/' . $beritaAcara['id_berita_acara'] . '/bukti') ?>" target="_blank" class="btn btn-label-success btn-sm">
        <i class="icon-base ti tabler-file-check me-1"></i>Lihat Bukti
      </a>
    <?php endif; ?>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadBuktiModal">
      <i class="icon-base ti tabler-upload me-1"></i>Upload Berita Acara
    </button>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <p align="justify">
      Pada tanggal <strong><?= $fmtTgl($tglPenyerahan) ?></strong> bertempat di <strong><?= esc($beritaAcara['lokasi_penyerahan']) ?></strong>
      berdasarkan <strong><?= esc($beritaAcara['berdasarkan']) ?></strong>
      <?php if ($beritaAcara['berdasarkan'] === 'Rapat Pengurus' && $tanggalRapat): ?>
        <?php $tglRapat = explode('-', explode(' ', $tanggalRapat)[0]); ?>
        pada tanggal <strong><?= $fmtTgl($tglRapat) ?></strong>.
      <?php endif; ?>
      <br><br>
      Telah disalurkan bantuan LAZISMU Sragen selaku Pihak Pertama berupa: <strong><?= esc($beritaAcara['ket_bentuk_penyerahan']) ?></strong>
      senilai <strong>Rp <?= number_format((float) $beritaAcara['nilai_penyerahan'], 0, ',', '.') ?></strong>
      <?php if ((int) $beritaAcara['bentuk_penyerahan'] === 2 && !empty($beritaAcara['rekening_penyerahan'])): ?>
        melalui rekening <strong><?= esc($beritaAcara['rekening_penyerahan']) ?></strong>
      <?php endif; ?>
      <?php if ((int) $beritaAcara['bentuk_penyerahan'] >= 3 && !empty($beritaAcara['nama_barang'])): ?>
        dengan nama barang: <strong><?= esc($beritaAcara['nama_barang']) ?></strong>
      <?php endif; ?>
      <br>
      dana dari <strong><?= esc($beritaAcara['dana_dari']) ?></strong> diberikan kepada penerima dengan kategori
      <strong><?= esc($beritaAcara['ket_kategori_penerima']) ?></strong>.
    </p>

    <p class="mb-2">Kepada pihak kedua:</p>
    <table class="table table-borderless mb-3" style="max-width: 640px;">
      <tbody>
        <tr>
          <td class="text-body-secondary" style="width: 220px;">Nama Pengaju</td>
          <td class="text-body-secondary" style="width: 16px;">:</td>
          <td><strong><?= esc($ajuan['nama_pemohon'] ?? '-') ?></strong></td>
        </tr>
        <tr>
          <td class="text-body-secondary">NIK</td>
          <td class="text-body-secondary">:</td>
          <td><strong><?= esc($ajuan['nik'] ?? '-') ?></strong></td>
        </tr>
        <tr>
          <td class="text-body-secondary">Alamat</td>
          <td class="text-body-secondary">:</td>
          <td>
            <strong>
              <?= esc($pemohon['alamat_detail'] ?? '-') ?>,
              <?= esc($pemohon['nama_kelurahan'] ?? '-') ?>, <?= esc($pemohon['nama_kecamatan'] ?? '-') ?>,
              <?= esc($pemohon['nama_kabupaten'] ?? '-') ?>, <?= esc($pemohon['nama_provinsi'] ?? '-') ?>
            </strong>
          </td>
        </tr>
        <tr>
          <td class="text-body-secondary">Keperuntukan Dana</td>
          <td class="text-body-secondary">:</td>
          <td><strong><?= esc($ajuan['deskripsi_ajuan'] ?? '-') ?></strong></td>
        </tr>
        <?php if ($ajuan['jenis_ajuan'] === 'Lembaga' && $lembaga): ?>
          <tr>
            <td class="text-body-secondary">Nama Lembaga</td>
            <td class="text-body-secondary">:</td>
            <td><strong><?= esc($lembaga['nama_lembaga']) ?></strong></td>
          </tr>
          <tr>
            <td class="text-body-secondary">Alamat Lembaga</td>
            <td class="text-body-secondary">:</td>
            <td><strong><?= esc($lembaga['alamat_lembaga']) ?></strong></td>
          </tr>
          <tr>
            <td class="text-body-secondary">Nomor Lembaga</td>
            <td class="text-body-secondary">:</td>
            <td><strong><?= esc($lembaga['nomor_lembaga']) ?></strong></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <p>
      Adapun bantuan ini bersifat <strong><?= esc($ajuan['sifat_bantuan'] ?? '-') ?></strong>.
      <br>
      Demikian Berita Acara ini dibuat untuk dipergunakan sebagaimana mestinya.
    </p>

    <div class="row mt-4">
      <div class="col-md-4">
        <p class="mb-0">Pihak Kedua/Yang Menerima</p>
        <div style="height: 4.5rem;"></div>
        <p class="mb-0 fw-medium"><?= esc($beritaAcara['yang_menerima']) ?></p>
      </div>
      <div class="col-md-4"></div>
      <div class="col-md-4">
        <p class="mb-0">Pembuat Berita Acara/Pihak Pertama/Yang Menyerahkan</p>
        <div style="height: 4.5rem;"></div>
        <p class="mb-0 fw-medium"><?= esc($beritaAcara['yang_bertandatangan']) ?></p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="uploadBuktiModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('ajuan/berita-acara/' . $beritaAcara['id_berita_acara'] . '/upload') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Upload Berita Acara</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">File Berita Acara (pdf/jpg/png)</label>
          <input type="file" name="file_berita_acara" class="form-control" required />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Unggah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
