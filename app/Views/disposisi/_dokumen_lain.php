<?php
/**
 * Read-only list of a disposisi result's extra documents (each with its
 * keterangan), shown under the result's main Dokumentasi button.
 *
 * Expects: $dokumen (rows of ad_disposisi_dokumen; may be empty).
 */
$dokumen = $dokumen ?? [];
?>
<?php if (!empty($dokumen)): ?>
  <div class="mt-3">
    <span class="text-body-secondary small text-uppercase">Dokumen Lain</span>
    <ul class="list-unstyled mb-0 mt-1">
      <?php foreach ($dokumen as $d): ?>
        <li class="mb-1">
          <a href="<?= base_url('disposisi/dokumen/' . $d['id']) ?>" target="_blank" class="btn btn-sm btn-label-secondary">
            <i class="icon-base ti tabler-paperclip me-1"></i><?= esc($d['nama_asli']) ?>
          </a>
          <?php if (!empty($d['keterangan'])): ?>
            <span class="text-body-secondary ms-1"><?= esc($d['keterangan']) ?></span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
