<?php $rows = $rows ?? []; $from = $from ?? null; $baseUrl = $baseUrl ?? 'ajuan/'; ?>
<?php foreach ($rows as $i => $a): ?>
  <?php
    $statusColor = ajuan_status_color(isset($a['status_ajuan']) ? (int) $a['status_ajuan'] : null);
    $detailUrl   = base_url($baseUrl . $a['nomor_ajuan']) . ($from ? '?from=' . urlencode($from) : '');
  ?>
  <tr>
    <td><?= $i + 1 ?></td>
    <td>
      <a href="<?= $detailUrl ?>"><?= esc($a['nomor_ajuan']) ?></a>
    </td>
    <td><?= esc($a['nama_pemohon'] ?? '-') ?></td>
    <td><?= esc($a['nama_program'] ?? '-') ?></td>
    <td><span class="badge bg-label-secondary"><?= esc($a['jenis_ajuan']) ?></span></td>
    <td>Rp <?= number_format((float) $a['nilai_diajukan'], 0, ',', '.') ?></td>
    <td><span class="badge bg-label-<?= $statusColor ?>"><?= esc($a['keterangan_status'] ?? '-') ?></span></td>
    <td class="text-end text-nowrap">
      <a href="<?= $detailUrl ?>" class="btn btn-icon btn-text-secondary rounded-pill">
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
