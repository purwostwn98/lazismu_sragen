<?php

namespace App\Models;

use CodeIgniter\Model;

class DisposisiModel extends Model
{
    protected $table         = 'ad_disposisi';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nomor_ajuan', 'deskripsi', 'dokumentasi', 'rekomendasi',
        'nominal_rekomendasi', 'oleh', 'nama_petugas',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Every disposisi row read through this model carries its extra
    // documents in 'dokumen_lain' - so every page that shows a result (the
    // review pages, the earlier-stage cards, ajuan detail) gets them without
    // each controller having to load them separately.
    protected $afterFind = ['lampirkanDokumenLain'];

    protected function lampirkanDokumenLain(array $eventData): array
    {
        $data = $eventData['data'] ?? null;

        if (empty($data)) {
            return $eventData;
        }

        $baris = !empty($eventData['singleton']) ? [$data] : $data;
        $ids   = array_values(array_filter(array_map(static fn ($r) => is_array($r) ? ($r['id'] ?? null) : null, $baris)));

        // Partial selects (no id) have nothing to attach to.
        if ($ids === []) {
            return $eventData;
        }

        $perDisposisi = [];
        foreach ((new DisposisiDokumenModel())->whereIn('id_disposisi', $ids)->orderBy('id', 'ASC')->findAll() as $d) {
            $perDisposisi[$d['id_disposisi']][] = $d;
        }

        if (!empty($eventData['singleton'])) {
            $eventData['data']['dokumen_lain'] = $perDisposisi[$data['id']] ?? [];

            return $eventData;
        }

        foreach ($eventData['data'] as &$r) {
            if (isset($r['id'])) {
                $r['dokumen_lain'] = $perDisposisi[$r['id']] ?? [];
            }
        }
        unset($r);

        return $eventData;
    }
}
