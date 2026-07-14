<?php

if (!function_exists('getFirstLetters')) {
    function getFirstLetters(string $inputString): string
    {
        $firstLetters = '';
        foreach (explode(' ', $inputString) as $word) {
            if ($word !== '') {
                $firstLetters .= $word[0];
            }
        }

        return $firstLetters;
    }
}

if (!function_exists('getRandomNumber001_009')) {
    function getRandomNumber001_009(): string
    {
        return sprintf('%03d', random_int(1, 999));
    }
}

if (!function_exists('ajuan_status_color')) {
    /** Maps a dt_status_ajuan id to a bg-label-* color suffix, kept consistent across all ajuan views. */
    function ajuan_status_color(?int $statusId): string
    {
        $colors = [
            0 => 'secondary', // Perlu Perbaikan
            1 => 'info',      // Diajukan
            2 => 'warning',   // Menunggu Rapat Pengurus
            3 => 'warning',   // Rapat Pengurus
            4 => 'warning',   // Menunggu Survey
            5 => 'warning',   // Survey
            6 => 'danger',    // Ditolak
            7 => 'success',   // Disetujui
            8 => 'primary',   // Disetujui (Rutin)
            9 => 'success',   // Rutin Closed
        ];

        return $colors[$statusId] ?? 'secondary';
    }
}

if (!function_exists('format_tanggal_indo')) {
    /** Formats a 'Y-m-d' or 'Y-m-d H:i:s' value as Indonesian "d Bulan Y[ H:i]". */
    function format_tanggal_indo(?string $value, bool $withTime = false): string
    {
        if (!$value) {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $parts = explode(' ', trim($value));
        $tgl   = explode('-', $parts[0]);

        if (count($tgl) !== 3) {
            return $value;
        }

        $formatted = (int) $tgl[2] . ' ' . $bulan[(int) $tgl[1]] . ' ' . $tgl[0];

        if ($withTime && !empty($parts[1])) {
            $formatted .= ' ' . substr($parts[1], 0, 5);
        }

        return $formatted;
    }
}

if (!function_exists('terbilang')) {
    /** Spells out a number as Indonesian words (e.g. 1500000 -> "satu juta lima ratus ribu"), for "Terbilang: ... rupiah" lines on printed documents. */
    function terbilang(float $angka): string
    {
        $angka    = (int) $angka;
        $bilangan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($angka < 12) {
            return $bilangan[$angka];
        }
        if ($angka < 20) {
            return $bilangan[$angka - 10] . ' belas';
        }
        if ($angka < 100) {
            return $bilangan[intdiv($angka, 10)] . ' puluh ' . $bilangan[$angka % 10];
        }
        if ($angka < 200) {
            return ' seratus ' . terbilang($angka - 100);
        }
        if ($angka < 1000) {
            return $bilangan[intdiv($angka, 100)] . ' ratus ' . terbilang($angka % 100);
        }
        if ($angka < 2000) {
            return ' seribu ' . terbilang($angka - 1000);
        }
        if ($angka < 1000000) {
            return terbilang(intdiv($angka, 1000)) . ' ribu ' . terbilang($angka % 1000);
        }
        if ($angka < 1000000000) {
            return terbilang(intdiv($angka, 1000000)) . ' juta ' . terbilang($angka % 1000000);
        }

        return 'Angka terlalu besar';
    }
}
