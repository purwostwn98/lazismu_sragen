<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$program = $program ?? [];
$kategori = $kategori ?? [];
$jenisFormulir = $jenisFormulir ?? [];
?>

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Data Kegiatan</h5>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProgram">
      <i class="icon-base ti tabler-plus me-1"></i>Tambah Kegiatan
    </button>
  </div>
  <hr>
  <div class="table-responsive">
    <table class="table table-hover table-sm datatable w-100">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Kegiatan</th>
          <th>Kategori</th>
          <th>Pilar</th>
          <th>Jenis Formulir</th>
          <th>Status</th>
          <th class="text-end text-nowrap">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($program as $i => $p): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td>
              <?= esc($p['nama_program']) ?>
              <?php if (!empty($p['deskripsi_program'])): ?>
                <br /><small class="text-body-secondary"><?= esc($p['deskripsi_program']) ?></small>
              <?php endif; ?>
            </td>
            <td><?= esc($p['nama_kategori'] ?? '-') ?></td>
            <td><?= esc($p['nama_pilar'] ?? '-') ?></td>
            <td><?= esc($jenisFormulir[$p['jenis_formulir']] ?? '-') ?></td>
            <td>
              <?php if ((int) $p['status_program'] === 1): ?>
                <span class="badge bg-label-success">Aktif</span>
              <?php else: ?>
                <span class="badge bg-label-secondary">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td class="text-end text-nowrap">
              <button
                type="button"
                class="btn btn-icon btn-text-secondary rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#modalEditProgram<?= $p['id_program'] ?>">
                <i class="icon-base ti tabler-edit"></i>
              </button>
              <form
                action="<?= base_url('program/delete/' . $p['id_program']) ?>"
                method="post"
                class="d-inline"
                onsubmit="return confirm('Hapus kegiatan ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!--
  Edit modals are rendered here, OUTSIDE the <table>, one loop per program.
  They must NOT live inside <tbody> (as a sibling of <tr>): browsers only allow
  <tr> as a direct child of <tbody>, so a <div> placed there gets "foster
  parented" out of the table at parse time, silently breaking the <form>/<input>
  relationship for any field added later via JS (e.g. rows added by "Tambah
  Syarat" no longer belong to the form and are dropped from submission).
-->
<?php foreach ($program as $p): ?>
  <div class="modal fade" id="modalEditProgram<?= $p['id_program'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="<?= base_url('program/update/' . $p['id_program']) ?>" method="post">
          <?= csrf_field() ?>
          <div class="modal-header">
            <h5 class="modal-title">Edit Kegiatan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Kategori</label>
              <select name="id_kategori_program" class="form-select" required>
                <?php foreach ($kategori as $k): ?>
                  <option value="<?= $k['id_kategori_program'] ?>" <?= (int) $p['id_kategori_program'] === (int) $k['id_kategori_program'] ? 'selected' : '' ?>>
                    <?= esc($k['nama_kategori']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Kegiatan</label>
              <input type="text" name="nama_program" class="form-control" value="<?= esc($p['nama_program']) ?>" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="deskripsi_program" class="form-control" rows="2"><?= esc($p['deskripsi_program']) ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis Formulir</label>
              <select name="jenis_formulir" class="form-select" required>
                <?php foreach ($jenisFormulir as $val => $label): ?>
                  <option value="<?= $val ?>" <?= (int) $p['jenis_formulir'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status_program" class="form-select" required>
                <option value="1" <?= (int) $p['status_program'] === 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= (int) $p['status_program'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
              </select>
            </div>
            <div class="mb-3 syarat-section">
              <label class="form-label mb-0">Syarat Pengajuan</label>
              <div class="syarat-wrap mt-2">
                <?php $existingSyarat = $syaratByProgram[$p['id_program']] ?? []; ?>
                <?php if ($existingSyarat): ?>
                  <?php foreach ($existingSyarat as $sy): ?>
                    <div class="input-group mb-2">
                      <span class="input-group-text"></span>
                      <input type="text" name="syarat[]" class="form-control" value="<?= esc($sy['syarat_program']) ?>" placeholder="Contoh: Fotokopi KTP" />
                      <button type="button" class="btn btn-outline-danger btn-remove-syarat"><i class="icon-base ti tabler-x"></i></button>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="input-group mb-2">
                    <span class="input-group-text"></span>
                    <input type="text" name="syarat[]" class="form-control" placeholder="Contoh: Fotokopi KTP" />
                    <button type="button" class="btn btn-outline-danger btn-remove-syarat"><i class="icon-base ti tabler-x"></i></button>
                  </div>
                <?php endif; ?>
              </div>
              <button type="button" class="btn btn-sm btn-label-secondary mt-1 btn-add-syarat">
                <i class="icon-base ti tabler-plus me-1"></i>Tambah Syarat
              </button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- Tambah modal -->
<div class="modal fade" id="modalTambahProgram" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="<?= base_url('program/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kegiatan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="id_kategori_program" class="form-select" required>
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($kategori as $k): ?>
                <option value="<?= $k['id_kategori_program'] ?>"><?= esc($k['nama_kategori']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Kegiatan</label>
            <input type="text" name="nama_program" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi_program" class="form-control" rows="2"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Formulir</label>
            <select name="jenis_formulir" class="form-select" required>
              <option value="">-- Pilih Jenis --</option>
              <?php foreach ($jenisFormulir as $val => $label): ?>
                <option value="<?= $val ?>"><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_program" class="form-select" required>
              <option value="1">Aktif</option>
              <option value="0">Nonaktif</option>
            </select>
          </div>
          <div class="mb-3 syarat-section">
            <label class="form-label mb-0">Syarat Pengajuan</label>
            <div class="syarat-wrap mt-2">
              <div class="input-group mb-2">
                <span class="input-group-text"></span>
                <input type="text" name="syarat[]" class="form-control" placeholder="Contoh: Fotokopi KTP" />
                <button type="button" class="btn btn-outline-danger btn-remove-syarat"><i class="icon-base ti tabler-x"></i></button>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-label-secondary mt-1 btn-add-syarat">
              <i class="icon-base ti tabler-plus me-1"></i>Tambah Syarat
            </button>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->section('pageScripts') ?>
<script>
  (function() {
    function renumber(wrap) {
      wrap.querySelectorAll('.input-group').forEach(function(group, i) {
        group.querySelector('.input-group-text').textContent = i + 1;
      });
    }

    document.querySelectorAll('.syarat-wrap').forEach(renumber);

    document.addEventListener('click', function(e) {
      var addBtn = e.target.closest('.btn-add-syarat');
      if (addBtn) {
        var wrap = addBtn.closest('.syarat-section').querySelector('.syarat-wrap');
        var div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML =
          '<span class="input-group-text"></span>' +
          '<input type="text" name="syarat[]" class="form-control" placeholder="Contoh: Fotokopi KTP" />' +
          '<button type="button" class="btn btn-outline-danger btn-remove-syarat"><i class="icon-base ti tabler-x"></i></button>';
        wrap.appendChild(div);
        renumber(wrap);
        return;
      }

      var removeBtn = e.target.closest('.btn-remove-syarat');
      if (removeBtn) {
        var syaratWrap = removeBtn.closest('.syarat-wrap');
        removeBtn.closest('.input-group').remove();
        renumber(syaratWrap);
      }
    });
  })();
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>