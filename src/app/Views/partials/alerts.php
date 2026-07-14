<?php if (session()->getFlashdata('berhasil')): ?>
  <div class="alert alert-success alert-dismissible" role="alert">
    <?= esc(session()->getFlashdata('berhasil')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('gagal')): ?>
  <div class="alert alert-danger alert-dismissible" role="alert">
    <?= esc(session()->getFlashdata('gagal')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
