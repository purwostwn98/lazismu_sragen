<?= $this->extend('layouts/main') ?>

<?= $this->section('pageStyles') ?>
<style>
  .ajuan-field-label {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--bs-body-color-secondary, #6b7280);
    margin-bottom: 0.25rem;
  }

  .ajuan-field-value {
    font-weight: 500;
  }

  .ajuan-value-box {
    border: 1px solid var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 0.9rem 1rem;
    height: 100%;
  }

  .ajuan-file-icon {
    width: 38px;
    height: 38px;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?= $this->include('partials/alerts') ?>

<?php
$ajuan = $ajuan ?? [];
$statusList = $statusList ?? [];
$b3 = $b3 ?? null;
$pilarList = $pilarList ?? [];
$bentukPenyerahan = $bentukPenyerahan ?? [];
$kategoriPenerima = $kategoriPenerima ?? [];
$kategoriProgramList = $kategoriProgramList ?? [];
$programList = $programList ?? [];
$b3Initial = $b3Initial ?? [];
$suratTugas = $suratTugas ?? [];
$individu = $individu ?? null;
$lembaga = $lembaga ?? null;
$dokumentasi = $dokumentasi ?? [];
$kuitansi = $kuitansi ?? [];
$logs = $logs ?? [];
$beritaAcara = $beritaAcara ?? [];

$statusColor  = ajuan_status_color((int) $ajuan['status_ajuan']);
$statusLabels = array_column($statusList, 'keterangan_status', 'id_status');
?>

<div class="card mb-4">
  <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-lg flex-shrink-0">
        <span class="avatar-initial rounded bg-label-<?= $statusColor ?>">
          <i class="icon-base ti tabler-file-description icon-lg"></i>
        </span>
      </div>
      <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <h4 class="mb-0">Ajuan #<?= esc($ajuan['nomor_ajuan']) ?></h4>
          <span class="badge bg-label-<?= $statusColor ?>"><?= esc($ajuan['keterangan_status'] ?? '-') ?></span>
          <span class="badge bg-label-secondary"><?= esc($ajuan['jenis_ajuan']) ?></span>
        </div>
        <p class="text-body-secondary mb-0">
          <?= esc($ajuan['nama_pemohon'] ?? '-') ?> &middot; <?= esc($ajuan['nama_program'] ?? '-') ?>
        </p>
      </div>
    </div>
    <a href="<?= base_url('ajuan') ?>" class="btn btn-label-secondary btn-sm">
      <i class="icon-base ti tabler-arrow-left me-1"></i>Kembali
    </a>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Informasi Ajuan</h5>
      </div>
      <div class="card-body">
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <div class="ajuan-value-box">
              <span class="ajuan-field-label">Nilai Diajukan</span>
              <span class="ajuan-field-value fs-5">Rp <?= number_format((float) $ajuan['nilai_diajukan'], 0, ',', '.') ?></span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="ajuan-value-box">
              <span class="ajuan-field-label">Nilai Disetujui</span>
              <span class="ajuan-field-value fs-5">
                <?= $ajuan['nilai_disetujui'] !== null ? 'Rp ' . number_format((float) $ajuan['nilai_disetujui'], 0, ',', '.') : '-' ?>
              </span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="ajuan-value-box">
              <span class="ajuan-field-label">Tanggal Diajukan</span>
              <span class="ajuan-field-value fs-5"><?= esc($ajuan['tgl_diajukan']) ?></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <span class="ajuan-field-label">Pemohon</span>
            <span class="ajuan-field-value"><?= esc($ajuan['nama_pemohon'] ?? '-') ?> (<?= esc($ajuan['nik']) ?>)</span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="ajuan-field-label">No. Telepon Pemohon</span>
            <span class="ajuan-field-value">
              <?php
              $teleponPemohon = $ajuan['telepon'] ?? '';
              $teleponPemohon = is_array($teleponPemohon) ? implode('', $teleponPemohon) : (string) $teleponPemohon;
              $nomorWaPemohon = !empty($teleponPemohon) ? preg_replace('/^0/', '62', preg_replace('/\D/', '', $teleponPemohon)) : '';
              ?>
              <?php if (!empty($teleponPemohon)): ?>
                <a href="https://wa.me/<?= esc($nomorWaPemohon) ?>" target="_blank" rel="noopener">
                  <?= esc($teleponPemohon) ?>
                </a>
              <?php else: ?>
                -
              <?php endif; ?>
            </span>
          </div>
          <div class="col-md-6 mb-3">
            <span class="ajuan-field-label">Kegiatan</span>
            <span class="ajuan-field-value"><?= esc($ajuan['nama_program'] ?? '-') ?></span>
          </div>
          <div class="col-12">
            <span class="ajuan-field-label">Deskripsi</span>
            <span class="ajuan-field-value"><?= nl2br(esc($ajuan['deskripsi_ajuan'])) ?></span>
          </div>
        </div>
      </div>
    </div>

    <?php if ($individu): ?>
      <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0">Data Mustahik (Individu)</h5>
          <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#editMustahikModal">
            <i class="icon-base ti tabler-pencil me-1"></i>Edit
          </button>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nama</span><span class="ajuan-field-value"><?= esc($individu['nama_mustahik']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Jenis Kelamin</span><span class="ajuan-field-value"><?= esc($individu['kelamin_mustahik']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Tempat, Tanggal Lahir</span><span class="ajuan-field-value"><?= esc($individu['tempat_lahir']) ?>, <?= esc($individu['tgl_lahir']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Agama</span><span class="ajuan-field-value"><?= esc($individu['agama_mustahik']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Status Pendidikan</span><span class="ajuan-field-value"><?= esc($individu['status_pendidikan']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Status Marital</span><span class="ajuan-field-value"><?= esc($individu['status_marital']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Jumlah Keluarga</span><span class="ajuan-field-value"><?= esc($individu['jml_keluarga']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">No. Handphone</span><span class="ajuan-field-value"><?= esc($individu['no_handphone']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Email</span><span class="ajuan-field-value"><?= esc($individu['email']) ?></span></div>
            <div class="col-md-6 mb-3"><span class="ajuan-field-label">Alamat</span><span class="ajuan-field-value"><?= esc($individu['alamat']) ?></span></div>
            <div class="col-md-6">
              <span class="ajuan-field-label">Dokumen KTP</span>
              <?php if (!empty($individu['foto_ktp'])): ?>
                <a href="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/mustahik/ktp') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
                  <i class="icon-base ti tabler-id me-1"></i>Lihat KTP
                </a>
              <?php else: ?>
                <span class="ajuan-field-value text-body-secondary d-block">Belum diunggah</span>
              <?php endif; ?>
            </div>
            <div class="col-md-6">
              <span class="ajuan-field-label">Dokumen KK</span>
              <?php if (!empty($individu['foto_kk'])): ?>
                <a href="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/mustahik/kk') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
                  <i class="icon-base ti tabler-file-text me-1"></i>Lihat KK
                </a>
              <?php else: ?>
                <span class="ajuan-field-value text-body-secondary d-block">Belum diunggah</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="editMustahikModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form action="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/mustahik') ?>" method="post">
              <?= csrf_field() ?>
              <div class="modal-header">
                <h5 class="modal-title">Edit Data Mustahik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama_mustahik" class="form-control" value="<?= esc($individu['nama_mustahik']) ?>" required />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="kelamin_mustahik" class="form-select" required>
                      <?php foreach (['Laki-laki', 'Perempuan'] as $kl): ?>
                        <option value="<?= $kl ?>" <?= $individu['kelamin_mustahik'] === $kl ? 'selected' : '' ?>><?= $kl ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="<?= esc($individu['tempat_lahir']) ?>" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" class="form-control" value="<?= esc($individu['tgl_lahir']) ?>" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Agama</label>
                    <select name="agama_mustahik" class="form-select">
                      <?php foreach (['Islam', 'Protestan', 'Katolik', 'Hindhu', 'Budha'] as $ag): ?>
                        <option value="<?= $ag ?>" <?= $individu['agama_mustahik'] === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Jumlah Keluarga</label>
                    <input type="number" name="jml_keluarga" class="form-control" min="0" value="<?= esc($individu['jml_keluarga']) ?>" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Dusun / Nama Jalan</label>
                    <input type="text" name="dusun" class="form-control" value="<?= esc($individu['dusun'] ?? '') ?>" />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">RT</label>
                    <input type="number" name="rt" class="form-control" min="0" value="<?= esc($individu['rt'] ?? '') ?>" />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label">RW</label>
                    <input type="number" name="rw" class="form-control" min="0" value="<?= esc($individu['rw'] ?? '') ?>" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Status Pendidikan</label>
                    <select name="status_pendidikan" class="form-select">
                      <?php foreach (['SD', 'SMP', 'SMA/SMK', 'Diploma', 'Sarjana', 'Pascasarjana', 'tidak tamat SD', 'lainnya'] as $sp): ?>
                        <option value="<?= $sp ?>" <?= $individu['status_pendidikan'] === $sp ? 'selected' : '' ?>><?= $sp ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Status Marital</label>
                    <select name="status_marital" class="form-select">
                      <?php foreach (['Lajang', 'Menikah', 'Cerai', 'Lainnya'] as $sm): ?>
                        <option value="<?= $sm ?>" <?= $individu['status_marital'] === $sm ? 'selected' : '' ?>><?= $sm ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">No. Handphone</label>
                    <input type="text" name="no_handphone" class="form-control" value="<?= esc($individu['no_handphone']) ?>" />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= esc($individu['email']) ?>" />
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($lembaga): ?>
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Data Lembaga</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-2"><span class="ajuan-field-label">Nama Lembaga</span><span class="ajuan-field-value"><?= esc($lembaga['nama_lembaga']) ?></span></div>
            <div class="col-md-4 mb-2"><span class="ajuan-field-label">Nomor Lembaga</span><span class="ajuan-field-value"><?= esc($lembaga['nomor_lembaga']) ?></span></div>
            <div class="col-md-4 mb-2"><span class="ajuan-field-label">Alamat</span><span class="ajuan-field-value"><?= esc($lembaga['alamat_lembaga']) ?></span></div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Ubah Status</h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <span class="ajuan-field-label">Status Saat Ini</span>
          <span class="badge bg-label-<?= $statusColor ?>"><?= esc($ajuan['keterangan_status'] ?? '-') ?></span>
        </div>
        <form action="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/status') ?>" method="post">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Status Baru</label>
            <select name="status_ajuan" id="statusAjuanSelect" class="form-select" required>
              <?php foreach ($statusList as $s): ?>
                <option value="<?= $s['id_status'] ?>" <?= (int) $ajuan['status_ajuan'] === (int) $s['id_status'] ? 'selected' : '' ?>>
                  <?= esc($s['keterangan_status']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Sifat Bantuan</label>
            <select name="sifat_bantuan" class="form-select" required>
              <?php foreach (['Insidental', 'Rutin'] as $sifat): ?>
                <option value="<?= $sifat ?>" <?= ($ajuan['sifat_bantuan'] ?? 'Insidental') === $sifat ? 'selected' : '' ?>><?= $sifat ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3 d-none" id="statusTanggalWrap">
            <label class="form-label" id="statusTanggalLabel">Tanggal</label>
            <input type="date" name="tanggal_log" class="form-control" />
          </div>
          <div class="mb-3 d-none" id="statusNilaiWrap">
            <label class="form-label">Nilai Bantuan Disetujui</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="text" inputmode="numeric" class="form-control" placeholder="0" data-currency-display="nilai_disetujui" />
            </div>
            <input type="hidden" name="nilai_disetujui" value="<?= esc((string) ($ajuan['nilai_disetujui'] ?? '')) ?>" />
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="catatan_log" class="form-control" rows="2"></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-100">Simpan Status</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Riwayat Status</h5>
      </div>
      <div class="card-body">
        <ul class="timeline mb-0">
          <?php foreach ($logs as $log): ?>
            <?php $logColor = ajuan_status_color((int) $log['status_ajuan']); ?>
            <li class="timeline-item timeline-item-transparent">
              <span class="timeline-point timeline-point-<?= $logColor ?>"></span>
              <div class="timeline-event">
                <div class="timeline-header mb-1">
                  <span class="badge bg-label-<?= $logColor ?> mb-1"><?= esc($statusLabels[$log['status_ajuan']] ?? '-') ?></span>
                  <small class="text-body-secondary d-block"><?= esc($log['tanggal_log']) ?></small>
                </div>
                <?php if ($log['catatan_log']): ?>
                  <p class="mb-0 text-body-secondary"><?= esc($log['catatan_log']) ?></p>
                <?php endif; ?>
              </div>
            </li>
          <?php endforeach; ?>
          <?php if (empty($logs)): ?>
            <li class="text-body-secondary">Belum ada riwayat.</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <!-- Form B3 -->
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Form B3</h5>
        <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#formB3Modal">
          <i class="icon-base ti tabler-<?= $b3 ? 'pencil' : 'plus' ?> me-1"></i><?= $b3 ? 'Edit Form B3' : 'Isi Form B3' ?>
        </button>
      </div>
      <div class="card-body">
        <?php if (!$b3): ?>
          <p class="text-body-secondary mb-0">Belum ada data B3.</p>
        <?php else: ?>
          <div class="row mb-3">
            <div class="col-md-4 mb-3 mb-md-0">
              <span class="ajuan-field-label">Pilar</span>
              <span class="ajuan-field-value"><?= esc($b3['nama_pilar'] ?? '-') ?></span>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <span class="ajuan-field-label">Kategori Program</span>
              <span class="ajuan-field-value"><?= esc($b3['nama_kategori'] ?? '-') ?></span>
            </div>
            <div class="col-md-4">
              <span class="ajuan-field-label">Kegiatan Disepakati</span>
              <span class="ajuan-field-value"><?= esc($b3['nama_program'] ?? '-') ?></span>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
              <span class="ajuan-field-label">Sumber Dana</span>
              <span class="ajuan-field-value"><?= esc($b3['dana_dari']) ?></span>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
              <span class="ajuan-field-label">Asnaf/Kategori Penerima</span>
              <span class="ajuan-field-value"><?= esc($b3['ket_kategori_penerima']) ?></span>
            </div>
            <div class="col-md-4">
              <span class="ajuan-field-label">Bentuk Penyerahan</span>
              <span class="ajuan-field-value"><?= esc($b3['ket_bentuk_penyerahan']) ?></span>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="modal fade" id="formB3Modal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/b3') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-header">
              <h5 class="modal-title">Form B3</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Pilar</label>
                  <select id="pilarSelect" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Pilar --</option>
                    <?php foreach ($pilarList as $pl): ?>
                      <option value="<?= $pl['id_pilar'] ?>"><?= esc($pl['nama_pilar']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Kategori Program</label>
                  <select name="id_kategori_program" id="kategoriSelect" class="form-select" required>
                    <option value="" disabled selected>-- Pilih pilar terlebih dahulu --</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label">Kegiatan</label>
                  <select name="id_program" id="programSelect" class="form-select" required>
                    <option value="" disabled selected>-- Pilih kategori terlebih dahulu --</option>
                  </select>
                </div>
              </div>
              <hr class="my-1 mb-3" />
              <div class="mb-3">
                <label class="form-label">Sumber Dana</label>
                <select name="dana_dari" id="b3DanaDari" class="form-select" required>
                  <option value="" disabled <?= empty($b3['dana_dari']) ? 'selected' : '' ?>>-- Pilih Sumber Dana --</option>
                  <?php foreach (['Zakat', 'Infaq Umum', 'Amil', 'Infaq Terikat', 'DSKL'] as $dd): ?>
                    <option value="<?= $dd ?>" <?= ($b3['dana_dari'] ?? '') === $dd ? 'selected' : '' ?>><?= $dd ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Asnaf/Kategori Penerima</label>
                <select name="kategori_penerima" id="b3Asnaf" class="form-select" required>
                  <option value="" disabled selected>-- Pilih Sumber Dana terlebih dahulu --</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Bentuk Penyerahan</label>
                <select name="bentuk_penyerahan" class="form-select" required>
                  <option value="" disabled <?= empty($b3['bentuk_penyerahan']) ? 'selected' : '' ?>>-- Pilih Bentuk Penyerahan --</option>
                  <?php foreach ($bentukPenyerahan as $bp): ?>
                    <option value="<?= $bp['id_bentuk_penyerahan'] ?>" <?= (string) ($b3['bentuk_penyerahan'] ?? '') === (string) $bp['id_bentuk_penyerahan'] ? 'selected' : '' ?>>
                      <?= esc($bp['ket_bentuk_penyerahan']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan B3</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script type="application/json" id="b3AsnafData">
      <?= json_encode($kategoriPenerima) ?>
    </script>
    <script type="application/json" id="programCascadeData">
      <?= json_encode(['kategori' => $kategoriProgramList, 'program' => $programList, 'initial' => $b3Initial]) ?>
    </script>

    <!-- Modul surat tugas -->
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Surat Tugas</h5>
        <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#createSuratTugasModal">
          <i class="icon-base ti tabler-plus me-1"></i>Buat Surat Tugas
        </button>
      </div>
      <div class="card-body p-0">
        <?php if (empty($suratTugas)): ?>
          <p class="text-body-secondary mb-0 p-3">Belum ada surat tugas.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama Penanggung Jawab</th>
                  <th>Tanggal Mulai</th>
                  <th>Tanggal Selesai</th>
                  <th class="text-end text-nowrap">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($suratTugas as $i => $st): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($st['nama_penanggung_jawab']) ?></td>
                    <td><?= esc(format_tanggal_indo($st['tanggal_mulai'], true)) ?></td>
                    <td><?= esc(format_tanggal_indo($st['tanggal_selesai'], true)) ?></td>
                    <td class="text-end text-nowrap">
                      <button type="button" class="btn btn-icon btn-text-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#suratTugasModal<?= $st['id_surat_tugas'] ?>">
                        <i class="icon-base ti tabler-eye"></i>
                      </button>
                      <a href="<?= base_url('ajuan/surat-tugas/' . $st['id_surat_tugas'] . '/cetak') ?>" target="_blank" class="btn btn-icon btn-text-secondary rounded-pill">
                        <i class="icon-base ti tabler-printer"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="modal fade" id="createSuratTugasModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/surat-tugas') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-header">
              <h5 class="modal-title">Buat Surat Tugas</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nama Penanggung Jawab</label>
                  <input type="text" name="nama_penanggung_jawab" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Jabatan</label>
                  <input type="text" name="jabatan" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Berdasarkan</label>
                  <select name="berdasarkan" class="form-select" required>
                    <option value="" disabled selected>-- Pilih --</option>
                    <option value="Rapat Pengurus">Rapat Pengurus</option>
                    <option value="Surat/Proposal">Surat/Proposal</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Dengan Agenda</label>
                  <select name="agenda" class="form-select" required>
                    <option value="" disabled selected>-- Pilih --</option>
                    <?php foreach (['Survey', 'Menyalurkan', 'Monev', 'Koordinasi', 'Lainnya'] as $ag): ?>
                      <option value="<?= $ag ?>"><?= $ag ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal &amp; Waktu Mulai</label>
                  <input type="datetime-local" name="tanggal_mulai" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal &amp; Waktu Selesai</label>
                  <input type="datetime-local" name="tanggal_selesai" class="form-control" required />
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Tempat/Lokasi</label>
                  <input type="text" name="lokasi" class="form-control" required />
                </div>
                <div class="col-12 mb-2">
                  <label class="form-label mb-0">Memberikan Tugas Kepada (Delegasi)</label>
                </div>
                <div class="col-12" id="delegasiWrap">
                  <div class="input-group mb-2">
                    <span class="input-group-text">1</span>
                    <input type="text" name="nama_delegasi[]" class="form-control" placeholder="Nama delegasi" required />
                  </div>
                </div>
                <div class="col-12">
                  <button type="button" class="btn btn-sm btn-label-secondary" id="btnAddDelegasi">
                    <i class="icon-base ti tabler-plus me-1"></i>Tambah Delegasi
                  </button>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Surat Tugas</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php foreach ($suratTugas as $st): ?>
      <div class="modal fade" id="suratTugasModal<?= $st['id_surat_tugas'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Surat Tugas #<?= $st['id_surat_tugas'] ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nama Penanggung Jawab</span><span class="ajuan-field-value"><?= esc($st['nama_penanggung_jawab']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Jabatan</span><span class="ajuan-field-value"><?= esc($st['jabatan']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Berdasarkan</span><span class="ajuan-field-value"><?= esc($st['berdasarkan']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Agenda</span><span class="ajuan-field-value"><?= esc($st['agenda']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Tanggal Mulai</span><span class="ajuan-field-value"><?= esc(format_tanggal_indo($st['tanggal_mulai'], true)) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Tanggal Selesai</span><span class="ajuan-field-value"><?= esc(format_tanggal_indo($st['tanggal_selesai'], true)) ?></span></div>
                <div class="col-12 mb-3"><span class="ajuan-field-label">Lokasi</span><span class="ajuan-field-value"><?= esc($st['lokasi']) ?></span></div>
                <div class="col-12 mb-3">
                  <span class="ajuan-field-label">Delegasi</span>
                  <?php if ($st['delegasi']): ?>
                    <ul class="ajuan-field-value mb-0 ps-3">
                      <?php foreach ($st['delegasi'] as $nama): ?>
                        <li><?= esc($nama) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <span class="ajuan-field-value text-body-secondary d-block">-</span>
                  <?php endif; ?>
                </div>
              </div>
              <hr />
              <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                <a href="<?= base_url('ajuan/surat-tugas/' . $st['id_surat_tugas'] . '/cetak') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
                  <i class="icon-base ti tabler-printer me-1"></i>Cetak Surat Tugas
                </a>
                <?php if (!empty($st['file_surat_tugas'])): ?>
                  <a href="<?= base_url('ajuan/surat-tugas/' . $st['id_surat_tugas'] . '/bukti') ?>" target="_blank" class="btn btn-sm btn-label-success">
                    <i class="icon-base ti tabler-file-check me-1"></i>Lihat Bukti
                  </a>
                <?php endif; ?>
              </div>
              <form action="<?= base_url('ajuan/surat-tugas/' . $st['id_surat_tugas'] . '/upload') ?>" method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
                <?= csrf_field() ?>
                <div class="col-md-8">
                  <label class="form-label">Unggah Bukti Surat Tugas / Hasil Survey</label>
                  <input type="file" name="file_surat_tugas" class="form-control" required />
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-primary w-100">Unggah</button>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <?php $initialBentuk = $b3 ? (int) $b3['bentuk_penyerahan'] : null; ?>

    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Berita Acara</h5>
        <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#createBeritaAcaraModal">
          <i class="icon-base ti tabler-plus me-1"></i>Buat Berita Acara
        </button>
      </div>
      <div class="card-body p-0">
        <?php if (empty($beritaAcara)): ?>
          <p class="text-body-secondary mb-0 p-3">Belum ada berita acara.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Yang Bertandatangan</th>
                  <th>Tanggal Penyerahan</th>
                  <th>Nilai</th>
                  <th class="text-end text-nowrap">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($beritaAcara as $i => $ba): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($ba['yang_bertandatangan']) ?></td>
                    <td><?= esc(format_tanggal_indo($ba['tanggal_penyerahan'])) ?></td>
                    <td>Rp <?= number_format((float) $ba['nilai_penyerahan'], 0, ',', '.') ?></td>
                    <td class="text-end text-nowrap">
                      <button type="button" class="btn btn-icon btn-text-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#beritaAcaraModal<?= $ba['id_berita_acara'] ?>">
                        <i class="icon-base ti tabler-eye"></i>
                      </button>
                      <a href="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/preview') ?>" target="_blank" class="btn btn-icon btn-text-secondary rounded-pill" title="Preview">
                        <i class="icon-base ti tabler-file-text"></i>
                      </a>
                      <a href="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/cetak') ?>" target="_blank" class="btn btn-icon btn-text-secondary rounded-pill" title="Cetak Berita Acara">
                        <i class="icon-base ti tabler-printer"></i>
                      </a>
                      <a href="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/kuitansi') ?>" target="_blank" class="btn btn-icon btn-text-secondary rounded-pill" title="Cetak C17 Kuitansi">
                        <i class="icon-base ti tabler-receipt"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="modal fade" id="createBeritaAcaraModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/berita-acara') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-header">
              <h5 class="modal-title">Buat Berita Acara</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Yang Bertandatangan</label>
                  <input type="text" name="yang_bertandatangan" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Yang Menerima</label>
                  <input type="text" name="yang_menerima" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Tanggal Penyerahan</label>
                  <input type="date" name="tanggal_penyerahan" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Berdasarkan</label>
                  <select name="berdasarkan" class="form-select" required>
                    <option value="" disabled selected>-- Pilih --</option>
                    <option value="Rapat Pengurus">Rapat Pengurus</option>
                    <option value="Laporan/SPJ">Laporan/SPJ</option>
                  </select>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Tempat/Lokasi</label>
                  <input type="text" name="lokasi_penyerahan" class="form-control" required />
                </div>

                <?php if ($b3): ?>
                  <div class="col-12 mb-3">
                    <div class="alert alert-info py-2 mb-0">
                      Sumber dana, asnaf, dan bentuk penyerahan mengikuti Form B3 yang sudah diisi:
                      <strong><?= esc($b3['dana_dari']) ?></strong> &middot;
                      <strong><?= esc($b3['ket_kategori_penerima']) ?></strong> &middot;
                      <strong><?= esc($b3['ket_bentuk_penyerahan']) ?></strong>
                    </div>
                  </div>
                <?php else: ?>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Sumber Dana</label>
                    <select name="dana_dari" id="baDanaDari" class="form-select" required>
                      <option value="" disabled selected>-- Pilih --</option>
                      <?php foreach (['Zakat', 'Infaq Umum', 'Amil', 'Infaq Terikat', 'DSKL'] as $dd): ?>
                        <option value="<?= $dd ?>"><?= $dd ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Asnaf/Kategori Penerima</label>
                    <select name="kategori_penerima" id="baAsnaf" class="form-select" required>
                      <option value="" disabled selected>-- Pilih Sumber Dana terlebih dahulu --</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Bentuk Penyerahan</label>
                    <select name="bentuk_penyerahan" id="baBentukPenyerahan" class="form-select" required>
                      <option value="" disabled selected>-- Pilih --</option>
                      <?php foreach ($bentukPenyerahan as $bp): ?>
                        <option value="<?= $bp['id_bentuk_penyerahan'] ?>"><?= esc($bp['ket_bentuk_penyerahan']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                <?php endif; ?>

                <div class="col-md-6 mb-3" id="baRekeningWrap" style="<?= $initialBentuk === 2 ? '' : 'display: none;' ?>">
                  <label class="form-label">Rekening Penerima</label>
                  <input type="text" name="rekening_penyerahan" class="form-control" />
                </div>
                <div class="col-md-6 mb-3" id="baBarangWrap" style="<?= $initialBentuk !== null && $initialBentuk >= 3 ? '' : 'display: none;' ?>">
                  <label class="form-label">Nama Barang</label>
                  <input type="text" name="nama_barang" class="form-control" />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Nilai Penyerahan (Rp)</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" inputmode="numeric" class="form-control" placeholder="0" data-currency-display="nilai_penyerahan" required />
                  </div>
                  <input type="hidden" name="nilai_penyerahan" />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Berita Acara</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php foreach ($beritaAcara as $ba): ?>
      <div class="modal fade" id="beritaAcaraModal<?= $ba['id_berita_acara'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Berita Acara #<?= $ba['id_berita_acara'] ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Yang Bertandatangan</span><span class="ajuan-field-value"><?= esc($ba['yang_bertandatangan']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Yang Menerima</span><span class="ajuan-field-value"><?= esc($ba['yang_menerima']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Tanggal Penyerahan</span><span class="ajuan-field-value"><?= esc(format_tanggal_indo($ba['tanggal_penyerahan'])) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Berdasarkan</span><span class="ajuan-field-value"><?= esc($ba['berdasarkan']) ?></span></div>
                <div class="col-12 mb-3"><span class="ajuan-field-label">Lokasi</span><span class="ajuan-field-value"><?= esc($ba['lokasi_penyerahan']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Sumber Dana</span><span class="ajuan-field-value"><?= esc($ba['dana_dari']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Asnaf/Kategori Penerima</span><span class="ajuan-field-value"><?= esc($ba['ket_kategori_penerima']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Bentuk Penyerahan</span><span class="ajuan-field-value"><?= esc($ba['ket_bentuk_penyerahan']) ?></span></div>
                <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nilai Penyerahan</span><span class="ajuan-field-value">Rp <?= number_format((float) $ba['nilai_penyerahan'], 0, ',', '.') ?></span></div>
                <?php if (!empty($ba['rekening_penyerahan'])): ?>
                  <div class="col-md-6 mb-3"><span class="ajuan-field-label">Rekening Penerima</span><span class="ajuan-field-value"><?= esc($ba['rekening_penyerahan']) ?></span></div>
                <?php endif; ?>
                <?php if (!empty($ba['nama_barang'])): ?>
                  <div class="col-md-6 mb-3"><span class="ajuan-field-label">Nama Barang</span><span class="ajuan-field-value"><?= esc($ba['nama_barang']) ?></span></div>
                <?php endif; ?>
              </div>
              <hr />
              <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                <a href="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/preview') ?>" target="_blank" class="btn btn-sm btn-label-primary">
                  <i class="icon-base ti tabler-file-text me-1"></i>Preview
                </a>
                <a href="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/cetak') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
                  <i class="icon-base ti tabler-printer me-1"></i>Cetak C1, C2, B3
                </a>
                <a href="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/kuitansi') ?>" target="_blank" class="btn btn-sm btn-label-secondary">
                  <i class="icon-base ti tabler-receipt me-1"></i>Cetak C17 Kuitansi
                </a>
                <?php if (!empty($ba['file_berita_acara'])): ?>
                  <a href="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/bukti') ?>" target="_blank" class="btn btn-sm btn-label-success">
                    <i class="icon-base ti tabler-file-check me-1"></i>Lihat Bukti
                  </a>
                <?php endif; ?>
                <form action="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus berita acara ini?')">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-sm btn-label-danger">
                    <i class="icon-base ti tabler-trash me-1"></i>Hapus
                  </button>
                </form>
              </div>
              <form action="<?= base_url('ajuan/berita-acara/' . $ba['id_berita_acara'] . '/upload') ?>" method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
                <?= csrf_field() ?>
                <div class="col-md-8">
                  <label class="form-label">Unggah Bukti / Salinan Bertandatangan</label>
                  <input type="file" name="file_berita_acara" class="form-control" required />
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-primary w-100">Unggah</button>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Dokumentasi</h5>
      </div>
      <div class="card-body">
        <ul class="list-group mb-3">
          <?php foreach ($dokumentasi as $d): ?>
            <li class="list-group-item d-flex align-items-center gap-3">
              <div class="ajuan-file-icon bg-label-secondary">
                <i class="icon-base ti tabler-file"></i>
              </div>
              <div class="flex-grow-1">
                <span class="d-block fw-medium"><?= esc($d['nama_file']) ?></span>
                <?php if ($d['catatan']): ?><small class="text-body-secondary"><?= esc($d['catatan']) ?></small><?php endif; ?>
              </div>
              <span class="badge bg-label-warning text-capitalize"><?= esc($d['status']) ?></span>
            </li>
          <?php endforeach; ?>
          <?php if (empty($dokumentasi)): ?>
            <li class="list-group-item text-body-secondary">Belum ada dokumentasi.</li>
          <?php endif; ?>
        </ul>
        <form action="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/dokumentasi') ?>" method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
          <?= csrf_field() ?>
          <div class="col-md-5">
            <label class="form-label">File</label>
            <input type="file" name="nama_file" class="form-control" required />
          </div>
          <div class="col-md-5">
            <label class="form-label">Catatan</label>
            <input type="text" name="catatan" class="form-control" />
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Unggah</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">Kuitansi</h5>
      </div>
      <div class="card-body">
        <ul class="list-group mb-3">
          <?php foreach ($kuitansi as $k): ?>
            <li class="list-group-item d-flex align-items-center gap-3">
              <div class="ajuan-file-icon bg-label-secondary">
                <i class="icon-base ti tabler-receipt"></i>
              </div>
              <div class="flex-grow-1">
                <span class="d-block fw-medium"><?= esc($k['nama']) ?></span>
                <?php if ($k['catatan']): ?><small class="text-body-secondary"><?= esc($k['catatan']) ?></small><?php endif; ?>
              </div>
              <span class="badge bg-label-warning text-capitalize"><?= esc($k['status']) ?></span>
            </li>
          <?php endforeach; ?>
          <?php if (empty($kuitansi)): ?>
            <li class="list-group-item text-body-secondary">Belum ada kuitansi.</li>
          <?php endif; ?>
        </ul>
        <form action="<?= base_url('ajuan/' . $ajuan['nomor_ajuan'] . '/kuitansi') ?>" method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
          <?= csrf_field() ?>
          <div class="col-md-5">
            <label class="form-label">File</label>
            <input type="file" name="nama" class="form-control" required />
          </div>
          <div class="col-md-5">
            <label class="form-label">Catatan</label>
            <input type="text" name="catatan" class="form-control" />
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Unggah</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script src="<?= base_url('assets/js/program-cascade.js') ?>"></script>
<script src="<?= base_url('assets/js/currency-format.js') ?>"></script>
<script>
  (function() {
    window.ProgramCascade.init(document);

    var danaDariSelect = document.getElementById('b3DanaDari');
    var asnafSelect = document.getElementById('b3Asnaf');
    var dataEl = document.getElementById('b3AsnafData');

    if (!danaDariSelect || !asnafSelect || !dataEl) {
      return;
    }

    var asnafList = JSON.parse(dataEl.textContent || '[]');
    var currentAsnaf = <?= json_encode((string) ($b3['kategori_penerima'] ?? '')) ?>;

    function renderAsnaf() {
      var danaDari = danaDariSelect.value;
      var filtered = asnafList.filter(function(a) {
        return a.id_dana_dari === danaDari;
      });

      asnafSelect.innerHTML = '';

      var ph = document.createElement('option');
      ph.value = '';
      ph.disabled = true;
      ph.selected = true;
      ph.textContent = filtered.length ? '-- Pilih Asnaf --' : '-- Pilih Sumber Dana terlebih dahulu --';
      asnafSelect.appendChild(ph);

      filtered.forEach(function(a) {
        var opt = document.createElement('option');
        opt.value = a.id_kategori_penerima;
        opt.textContent = a.ket_kategori_penerima;
        if (String(a.id_kategori_penerima) === String(currentAsnaf)) {
          opt.selected = true;
          ph.selected = false;
        }
        asnafSelect.appendChild(opt);
      });
    }

    danaDariSelect.addEventListener('change', function() {
      currentAsnaf = '';
      renderAsnaf();
    });

    if (danaDariSelect.value) {
      renderAsnaf();
    }
  })();

  (function() {
    var wrap = document.getElementById('delegasiWrap');
    var btnAdd = document.getElementById('btnAddDelegasi');

    if (!wrap || !btnAdd) {
      return;
    }

    var MAX_DELEGASI = 6;

    function renumber() {
      wrap.querySelectorAll('.input-group').forEach(function(group, i) {
        group.querySelector('.input-group-text').textContent = i + 1;
      });
      btnAdd.disabled = wrap.querySelectorAll('input[name="nama_delegasi[]"]').length >= MAX_DELEGASI;
    }

    btnAdd.addEventListener('click', function() {
      if (wrap.querySelectorAll('input[name="nama_delegasi[]"]').length >= MAX_DELEGASI) {
        return;
      }

      var div = document.createElement('div');
      div.className = 'input-group mb-2';
      div.innerHTML =
        '<span class="input-group-text"></span>' +
        '<input type="text" name="nama_delegasi[]" class="form-control" placeholder="Nama delegasi" />' +
        '<button type="button" class="btn btn-outline-danger btn-remove-delegasi"><i class="icon-base ti tabler-x"></i></button>';
      wrap.appendChild(div);
      renumber();
    });

    wrap.addEventListener('click', function(e) {
      var btn = e.target.closest('.btn-remove-delegasi');
      if (!btn) {
        return;
      }
      btn.closest('.input-group').remove();
      renumber();
    });
  })();

  (function() {
    window.CurrencyFormat.init(document);

    var bentukSelect = document.getElementById('baBentukPenyerahan');
    var rekeningWrap = document.getElementById('baRekeningWrap');
    var barangWrap = document.getElementById('baBarangWrap');

    if (bentukSelect && rekeningWrap && barangWrap) {
      bentukSelect.addEventListener('change', function() {
        var v = parseInt(bentukSelect.value, 10);
        rekeningWrap.style.display = v === 2 ? '' : 'none';
        barangWrap.style.display = v >= 3 ? '' : 'none';
      });
    }

    var danaDariSelect = document.getElementById('baDanaDari');
    var asnafSelect = document.getElementById('baAsnaf');
    var dataEl = document.getElementById('b3AsnafData');

    if (danaDariSelect && asnafSelect && dataEl) {
      var asnafList = JSON.parse(dataEl.textContent || '[]');

      danaDariSelect.addEventListener('change', function() {
        var danaDari = danaDariSelect.value;
        var filtered = asnafList.filter(function(a) {
          return a.id_dana_dari === danaDari;
        });

        asnafSelect.innerHTML = '';

        var ph = document.createElement('option');
        ph.value = '';
        ph.disabled = true;
        ph.selected = true;
        ph.textContent = filtered.length ? '-- Pilih Asnaf --' : 'Tidak ada kategori untuk sumber dana ini';
        asnafSelect.appendChild(ph);

        filtered.forEach(function(a) {
          var opt = document.createElement('option');
          opt.value = a.id_kategori_penerima;
          opt.textContent = a.ket_kategori_penerima;
          asnafSelect.appendChild(opt);
        });
      });
    }
  })();

  (function() {
    var statusSelect = document.getElementById('statusAjuanSelect');
    var tanggalWrap = document.getElementById('statusTanggalWrap');
    var tanggalLabel = document.getElementById('statusTanggalLabel');
    var nilaiWrap = document.getElementById('statusNilaiWrap');

    if (!statusSelect || !tanggalWrap || !nilaiWrap) {
      return;
    }

    var TANGGAL_LABELS = {
      2: 'Jadwal Agenda Rapat Pengurus',
      3: 'Tanggal Rapat Pengurus',
      4: 'Jadwal Agenda Survey',
      5: 'Tanggal Survey',
    };

    function toggleFields() {
      var status = parseInt(statusSelect.value, 10);

      if (TANGGAL_LABELS[status]) {
        tanggalLabel.textContent = TANGGAL_LABELS[status];
        tanggalWrap.classList.remove('d-none');
      } else {
        tanggalWrap.classList.add('d-none');
      }

      nilaiWrap.classList.toggle('d-none', status !== 7 && status !== 8);
    }

    statusSelect.addEventListener('change', toggleFields);
    toggleFields();
  })();
</script>
<?= $this->endSection() ?>