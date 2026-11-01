<?php
/**
 * Shared "current stage" review form: hidden by default once a result
 * already exists (summary card + Edit button shown instead), pre-filled
 * for editing. Pair with disposisi/_form_tinjauan_script.php in
 * pageScripts to wire up Quill + the summary/form toggle.
 *
 * Expects: $latest (ad_disposisi row array or null), $actionUrl (string),
 * $judulForm (string), $judulSudah (string), $placeholder (string, optional).
 */
$latest      = $latest ?? null;
$actionUrl   = $actionUrl ?? '';
$judulForm   = $judulForm ?? 'Formulir Tinjauan';
$judulSudah  = $judulSudah ?? 'Tinjauan sudah diisi';
$placeholder = $placeholder ?? 'Tuliskan hasil tinjauan...';
?>

<?php if ($latest): ?>
  <div class="card mb-4" id="reviewSummaryCard">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h6 class="mb-1"><?= esc($judulSudah) ?></h6>
        <p class="text-body-secondary mb-0">
          Direkomendasikan <?= (int) $latest['rekomendasi'] === 1 ? 'disetujui' : 'ditolak' ?>
          oleh <?= esc($latest['nama_petugas'] ?? '-') ?> pada <?= esc($latest['created_at']) ?>.
        </p>
      </div>
      <button type="button" class="btn btn-label-primary btn-sm" id="btnEditReview">
        <i class="icon-base ti tabler-edit me-1"></i>Edit Tinjauan
      </button>
    </div>
  </div>
<?php endif; ?>

<div class="card mb-4 <?= $latest ? 'd-none' : '' ?>" id="formulirReviewCard">
  <div class="card-header">
    <h5 class="mb-0"><?= esc($judulForm) ?></h5>
  </div>
  <div class="card-body">
    <form id="reviewForm" action="<?= $actionUrl ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <?php if ($latest): ?>
        <input type="hidden" name="id" value="<?= (int) $latest['id'] ?>" />
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Deskripsi Hasil Tinjauan</label>
        <div id="deskripsiEditor"><?= $latest['deskripsi'] ?? '' ?></div>
        <textarea name="deskripsi" id="deskripsiInput" class="d-none"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Dokumentasi</label>
        <?php if ($latest && !empty($latest['dokumentasi'])): ?>
          <div class="mb-2">
            <a href="<?= base_url('disposisi/dokumentasi/' . $latest['id']) ?>" target="_blank" class="btn btn-sm btn-label-secondary">
              <i class="icon-base ti tabler-paperclip me-1"></i>Dokumentasi saat ini
            </a>
          </div>
        <?php endif; ?>
        <input type="file" name="dokumentasi" class="form-control" accept=".pdf,.jpg,.jpeg,application/pdf,image/jpeg" />
        <div class="form-text">
          Format PDF atau JPEG, maksimal 2 MB.
          <?= $latest ? ' Kosongkan jika tidak ingin mengubah dokumentasi.' : '' ?>
        </div>
      </div>

      <?php
      $dokumenLain = $latest['dokumen_lain'] ?? [];
      ?>
      <div class="mb-3" id="dokumenLainWrapper" data-maks="5" data-maks-ukuran="1048576">
        <label class="form-label">Dokumen Lain <span class="text-body-secondary fw-normal">(opsional)</span></label>

        <?php if ($dokumenLain): ?>
          <div class="mb-2" id="dokumenLainTersimpan">
            <?php foreach ($dokumenLain as $d): ?>
              <div class="d-flex align-items-center gap-2 flex-wrap mb-1 dokumen-tersimpan">
                <a href="<?= base_url('disposisi/dokumen/' . $d['id']) ?>" target="_blank" class="btn btn-sm btn-label-secondary">
                  <i class="icon-base ti tabler-paperclip me-1"></i><?= esc($d['nama_asli']) ?>
                </a>
                <?php if (!empty($d['keterangan'])): ?>
                  <span class="text-body-secondary"><?= esc($d['keterangan']) ?></span>
                <?php endif; ?>
                <div class="form-check ms-auto mb-0">
                  <input class="form-check-input cek-hapus-dokumen" type="checkbox" name="hapus_dokumen[]" value="<?= (int) $d['id'] ?>" id="hapusDok<?= (int) $d['id'] ?>" />
                  <label class="form-check-label text-danger" for="hapusDok<?= (int) $d['id'] ?>">Hapus</label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div id="dokumenLainBaris"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="btnTambahDokumen">
          <i class="icon-base ti tabler-plus me-1"></i>Tambah Dokumen
        </button>
        <div class="form-text" id="infoDokumenLain">
          Maksimal 5 dokumen, masing-masing PDF/JPG/PNG maksimal 1 MB.
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label d-block">Rekomendasi</label>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="rekomendasi" id="rekomendasiSetuju" value="1" required <?= $latest && (int) $latest['rekomendasi'] === 1 ? 'checked' : '' ?> />
          <label class="form-check-label" for="rekomendasiSetuju">Disetujui</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="rekomendasi" id="rekomendasiTolak" value="0" required <?= $latest && (int) $latest['rekomendasi'] === 0 ? 'checked' : '' ?> />
          <label class="form-check-label" for="rekomendasiTolak">Ditolak</label>
        </div>
      </div>

      <div class="mb-3 <?= $latest && (int) $latest['rekomendasi'] === 1 ? '' : 'd-none' ?>" id="nominalWrapper">
        <label class="form-label">Nominal Rekomendasi</label>
        <div class="input-group">
          <span class="input-group-text">Rp</span>
          <input type="number" name="nominal_rekomendasi" id="nominalInput" class="form-control" min="0" step="1000" value="<?= $latest ? (float) $latest['nominal_rekomendasi'] : '' ?>" <?= $latest && (int) $latest['rekomendasi'] === 1 ? 'required' : '' ?> />
        </div>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="icon-base ti tabler-check me-1"></i><?= $latest ? 'Perbarui Tinjauan' : 'Simpan Tinjauan' ?>
      </button>
      <?php if ($latest): ?>
        <button type="button" class="btn btn-label-secondary" id="btnBatalEditReview">Batal</button>
      <?php endif; ?>
    </form>
  </div>
</div>
