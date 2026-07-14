<?php $rows = $rows ?? []; ?>
<?php foreach ($rows as $i => $a): ?>
  <?php $statusColor = ajuan_status_color(isset($a['status_ajuan']) ? (int) $a['status_ajuan'] : null); ?>
  <tr>
    <td><?= $i + 1 ?></td>
    <td>
      <a href="<?= base_url('ajuan/' . $a['nomor_ajuan']) ?>"><?= esc($a['nomor_ajuan']) ?></a>
    </td>
    <td><?= esc($a['nama_pemohon'] ?? '-') ?></td>
    <td><?= esc($a['nama_program'] ?? '-') ?></td>
    <td><span class="badge bg-label-secondary"><?= esc($a['jenis_ajuan']) ?></span></td>
    <td>Rp <?= number_format((float) $a['nilai_diajukan'], 0, ',', '.') ?></td>
    <td><span class="badge bg-label-<?= $statusColor ?>"><?= esc($a['keterangan_status'] ?? '-') ?></span></td>
    <td class="text-end text-nowrap">
      <a href="<?= base_url('ajuan/' . $a['nomor_ajuan']) ?>" class="btn btn-icon btn-text-secondary rounded-pill">
        <i class="icon-base ti tabler-eye"></i>
      </a>
      <form
        action="<?= base_url('ajuan/delete/' . $a['nomor_ajuan']) ?>"
        method="post"
        class="d-inline"
        onsubmit="return confirm('Hapus ajuan ini beserta seluruh data terkait?')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-icon btn-text-danger rounded-pill">
          <i class="icon-base ti tabler-trash"></i>
        </button>
      </form>
    </td>
  </tr>
<?php endforeach; ?>
