<?php
/**
 * List of every ad_disposisi entry ever recorded for one (ajuan, stage)
 * pair. Under normal use there's only ever one (edits upsert in place), but
 * this stays as a visible audit trail rather than silently hiding history.
 *
 * Expects: $judul (string), $riwayat (array of ad_disposisi rows).
 */
$judul   = $judul ?? 'Riwayat';
$riwayat = $riwayat ?? [];
?>
<?php if (!empty($riwayat)): ?>
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0"><?= esc($judul) ?></h5>
    </div>
    <div class="card-body">
      <?php foreach ($riwayat as $i => $r): ?>
        <div class="border rounded p-3 <?= $i < count($riwayat) - 1 ? 'mb-3' : '' ?>">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div>
              <?php if ((int) $r['rekomendasi'] === 1): ?>
                <span class="badge bg-label-success">Direkomendasikan Disetujui</span>
                <span class="text-body-secondary ms-1">Rp <?= number_format((float) $r['nominal_rekomendasi'], 0, ',', '.') ?></span>
              <?php else: ?>
                <span class="badge bg-label-danger">Direkomendasikan Ditolak</span>
              <?php endif; ?>
            </div>
            <span class="text-body-secondary small"><?= esc($r['nama_petugas'] ?? '-') ?> &middot; <?= esc($r['created_at']) ?></span>
          </div>
          <div class="ql-editor p-0"><?= $r['deskripsi'] ?></div>
          <?php if (!empty($r['dokumentasi'])): ?>
            <a href="<?= base_url('disposisi/dokumentasi/' . $r['id']) ?>" target="_blank" class="btn btn-sm btn-label-secondary mt-2">
              <i class="icon-base ti tabler-paperclip me-1"></i>Lihat Dokumentasi
            </a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
