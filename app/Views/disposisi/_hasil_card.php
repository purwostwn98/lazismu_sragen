<?php
/**
 * Read-only card showing one disposisi-stage result (Surveyor / Kepala
 * Divisi Program / Manager / Badan Pengurus), for display on a LATER
 * stage's review page so that stage can see what came before it.
 *
 * Expects: $judul (string), $data (ad_disposisi row array or null).
 */
$judul = $judul ?? 'Hasil';
$data  = $data ?? null;
?>
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0"><?= esc($judul) ?></h5>
  </div>
  <div class="card-body">
    <?php if (!$data): ?>
      <p class="text-body-secondary mb-0">Belum ada hasil untuk ajuan ini.</p>
    <?php else: ?>
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <div>
          <?php if ((int) $data['rekomendasi'] === 1): ?>
            <span class="badge bg-label-success">Direkomendasikan Disetujui</span>
            <span class="text-body-secondary ms-1">Rp <?= number_format((float) $data['nominal_rekomendasi'], 0, ',', '.') ?></span>
          <?php else: ?>
            <span class="badge bg-label-danger">Direkomendasikan Ditolak</span>
          <?php endif; ?>
        </div>
        <span class="text-body-secondary small"><?= esc($data['nama_petugas'] ?? '-') ?> &middot; <?= esc($data['created_at']) ?></span>
      </div>
      <div class="ql-editor p-0"><?= $data['deskripsi'] ?></div>
      <?php if (!empty($data['dokumentasi'])): ?>
        <a href="<?= base_url('disposisi/dokumentasi/' . $data['id']) ?>" target="_blank" class="btn btn-sm btn-label-secondary mt-2">
          <i class="icon-base ti tabler-paperclip me-1"></i>Lihat Dokumentasi
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
