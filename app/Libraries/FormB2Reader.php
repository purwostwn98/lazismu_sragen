<?php

namespace App\Libraries;

use App\Models\FormB2Model;
use CodeIgniter\HTTP\IncomingRequest;

/**
 * Reads the b2_* fields posted by pengajuan/_form_b2.php into a
 * ready-to-upsert tr_form_b2 row (including the computed total_skor /
 * kategori_kelayakan) — shared by PengajuanController::storeFormulir()
 * and AjuanController::store() so the ~49-field extraction isn't
 * duplicated between the public and internal ajuan-creation forms.
 */
class FormB2Reader
{
    private const BARANG_ELEKTRONIK = ['tv', 'hp', 'kulkas', 'magic_com', 'mesin_cuci', 'setrika', 'dispenser'];

    public static function dariRequest(IncomingRequest $request): array
    {
        $data = [];

        // Every question posts "score|label" (see _form_b2.php's <option
        // value="...">) so both the number that feeds total_skor and the
        // exact answer text picked (for ajuan/show.php's display, where a
        // raw score can't always be traced back to one specific answer -
        // several of q32's 6 answers intentionally share a score) are
        // captured from the single source of truth: the option itself.
        foreach (FormB2Model::PERTANYAAN_SKOR as $key) {
            [$skor, $label] = array_pad(explode('|', (string) $request->getPost('b2_' . $key), 2), 2, null);

            $data[$key]            = (int) $skor;
            $data[$key . '_opsi']  = $label;
        }

        foreach (self::BARANG_ELEKTRONIK as $item) {
            $data['elektronik_' . $item . '_jumlah'] = (int) $request->getPost('b2_elektronik_' . $item . '_jumlah');
            $data['elektronik_' . $item . '_status']  = $request->getPost('b2_elektronik_' . $item . '_status') ?: null;
        }

        $data['elektronik_lainnya_nama']   = $request->getPost('b2_elektronik_lainnya_nama') ?: null;
        $data['elektronik_lainnya_jumlah'] = (int) $request->getPost('b2_elektronik_lainnya_jumlah');
        $data['elektronik_lainnya_status'] = $request->getPost('b2_elektronik_lainnya_status') ?: null;

        $data['catatan_tambahan']        = $request->getPost('b2_catatan_tambahan') ?: null;
        $data['bersedia_dipublikasikan'] = (int) $request->getPost('b2_bersedia_dipublikasikan');

        return array_merge($data, (new FormB2Model())->totalDanKategori($data));
    }
}
