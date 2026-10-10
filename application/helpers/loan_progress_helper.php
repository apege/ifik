<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('scm_loan_progress')) {
    /**
     * Mengubah status internal/eksternal peminjaman menjadi progres tahap yang terstandarisasi.
     * Untuk Peminjaman Internal: 7 tahap (Pengajuan -> Kaprodi -> Laboran -> Kaur -> Pengambilan -> Pinjam -> Selesai).
     * Untuk Peminjaman Eksternal: 8 tahap (Pengajuan -> Kaprodi -> Laboran -> Kaur -> Wadek -> Pengambilan -> Pinjam -> Selesai).
     */
    function scm_loan_progress($loan)
    {
        $status = trim((string) ($loan->status ?? ''));
        $kaprodi = (string) ($loan->status_kaprodi ?? 'Pending');
        $laboran = (string) ($loan->status_laboran ?? 'Pending');
        $kaur = (string) ($loan->status_kaur ?? 'Pending');
        $wadek = (string) ($loan->status_wadek1 ?? 'Pending');
        $is_external = (($loan->jenis_peminjaman ?? '') === 'luar_kampus' || ($loan->jenis_peminjaman ?? '') === 'external');

        if ($is_external) {
            $steps = [
                ['label' => 'Pengajuan Dibuat', 'description' => 'Peminjam mengirim pengajuan barang luar kampus.'],
                ['label' => 'Persetujuan Kaprodi', 'description' => 'Kaprodi memeriksa dan menyetujui pengajuan.'],
                ['label' => 'Verifikasi Laboran', 'description' => 'Laboran memeriksa barang, stok, dan kelayakan peminjaman.'],
                ['label' => 'Persetujuan Kaur', 'description' => 'Kaur memeriksa dan menyetujui pengajuan.'],
                ['label' => 'Persetujuan Wadek', 'description' => 'Wakil Dekan memberikan persetujuan peminjaman eksternal.'],
                ['label' => 'Pengambilan Barang', 'description' => 'Barang diserahterimakan kepada peminjam melalui scan QR oleh laboran.'],
                ['label' => 'Peminjaman & Pengembalian', 'description' => 'Barang sedang digunakan di luar kampus dan menunggu dikembalikan.'],
                ['label' => 'Selesai', 'description' => 'Barang telah diterima kembali melalui scan QR pengembalian.'],
            ];
        } else {
            $steps = [
                ['label' => 'Pengajuan Dibuat', 'description' => 'Peminjam mengirim pengajuan barang.'],
                ['label' => 'Persetujuan Kaprodi', 'description' => 'Kaprodi memeriksa dan menyetujui pengajuan.'],
                ['label' => 'Verifikasi Laboran', 'description' => 'Laboran memeriksa barang, stok, dan kelayakan peminjaman.'],
                ['label' => 'Persetujuan Kaur', 'description' => 'Kaur memberikan persetujuan akhir peminjaman.'],
                ['label' => 'Pengambilan Barang', 'description' => 'Barang diserahterimakan kepada peminjam melalui scan QR oleh laboran.'],
                ['label' => 'Peminjaman & Pengembalian', 'description' => 'Barang sedang digunakan dan menunggu dikembalikan.'],
                ['label' => 'Selesai', 'description' => 'Barang telah diterima kembali melalui scan QR pengembalian.'],
            ];
        }

        $current_index = 0;
        $stage_label = 'Diajukan';
        $status_label = 'Pengajuan dibuat';
        $tone = 'current';
        $rejected_index = null;

        switch ($status) {
            case 'Menunggu ACC Kaprodi':
                $current_index = 1;
                $stage_label = 'Kaprodi';
                $status_label = 'Menunggu Persetujuan Kaprodi';
                break;

            case 'Menunggu Verifikasi Laboran':
            case 'Menunggu Pengecekan Laboran':
            case 'Menunggu Persetujuan':
                $current_index = 2;
                $stage_label = 'Laboran';
                $status_label = 'Menunggu Verifikasi Laboran';
                break;

            case 'Menunggu ACC Kaur':
                $current_index = 3;
                $stage_label = 'Kaur';
                $status_label = 'Menunggu Persetujuan Kaur';
                break;

            case 'Menunggu ACC Wadek':
            case 'Menunggu Persetujuan Wadek':
                $current_index = $is_external ? 4 : 3;
                $stage_label = 'Wadek';
                $status_label = 'Menunggu Persetujuan Wakil Dekan (Wadek)';
                break;

            case 'Disetujui (Menunggu Finalisasi QR)':
            case 'Disetujui (Menunggu Pengambilan)':
                $current_index = $is_external ? 5 : 4;
                $stage_label = 'Pengambilan';
                $status_label = 'QR Aktif — Menunggu Pengambilan';
                $tone = 'ready';
                break;

            case 'Sedang Dipinjam':
            case 'Dipinjam':
                $current_index = $is_external ? 6 : 5;
                $stage_label = 'Pengembalian';
                $status_label = 'Sedang Dipinjam — Menunggu Pengembalian';
                $tone = 'active';
                break;

            case 'Dikembalikan':
            case 'Selesai':
                $current_index = $is_external ? 7 : 6;
                $stage_label = 'Selesai';
                $status_label = 'Selesai — Barang Dikembalikan';
                $tone = 'complete';
                break;

            case 'Ditolak':
                if ($kaprodi === 'Ditolak') {
                    $rejected_index = 1;
                    $stage_label = 'Kaprodi';
                    $status_label = 'Ditolak oleh Kaprodi';
                } elseif ($laboran === 'Ditolak') {
                    $rejected_index = 2;
                    $stage_label = 'Laboran';
                    $status_label = 'Ditolak oleh Laboran';
                } elseif ($kaur === 'Ditolak') {
                    $rejected_index = 3;
                    $stage_label = 'Kaur';
                    $status_label = 'Ditolak oleh Kaur';
                } elseif ($wadek === 'Ditolak') {
                    $rejected_index = $is_external ? 4 : 3;
                    $stage_label = 'Wadek';
                    $status_label = 'Ditolak oleh Wakil Dekan';
                } else {
                    $rejected_index = 1;
                    $stage_label = 'Persetujuan';
                    $status_label = 'Pengajuan Ditolak';
                }
                $current_index = $rejected_index;
                $tone = 'rejected';
                break;

            case 'Kedaluwarsa / Ditolak Otomatis':
                $current_index = 1;
                $rejected_index = 1;
                $stage_label = 'Kaprodi';
                $status_label = 'Kedaluwarsa — Ditolak Otomatis';
                $tone = 'rejected';
                break;

            default:
                if ($status !== '') {
                    $status_label = $status;
                }
                break;
        }

        $progress_steps = [];
        foreach ($steps as $index => $step) {
            if ($tone === 'complete') {
                $state = 'is-complete';
            } elseif ($rejected_index !== null && $index === $rejected_index) {
                $state = 'is-rejected';
            } elseif ($index < $current_index) {
                $state = 'is-complete';
            } elseif ($index === $current_index) {
                $state = 'is-current';
            } else {
                $state = 'is-pending';
            }
            $state_label = [
                'is-complete' => 'Selesai',
                'is-current' => 'Sedang diproses',
                'is-pending' => 'Belum dimulai',
                'is-rejected' => 'Ditolak',
            ][$state];
            $progress_steps[] = [
                'number' => $index + 1,
                'label' => $step['label'],
                'description' => $step['description'],
                'state' => $state,
                'state_label' => $state_label,
            ];
        }

        return [
            'raw_status' => $status,
            'status_label' => $status_label,
            'stage_label' => $stage_label,
            'current_index' => $current_index,
            'total_steps' => count($steps),
            'tone' => $tone,
            'is_external' => $is_external,
            'kaprodi_deadline_at' => $loan->kaprodi_deadline_at ?? null,
            'steps' => $progress_steps,
        ];
    }
}

if (!function_exists('scm_loan_can_act')) {
    /** Hak aksi UI. Controller tetap melakukan validasi yang sama di server. */
    function scm_loan_can_act($loan, $role)
    {
        $status = (string) ($loan->status ?? '');
        $kaprodi = (string) ($loan->status_kaprodi ?? 'Pending');
        $laboran = (string) ($loan->status_laboran ?? 'Pending');
        $kaur = (string) ($loan->status_kaur ?? 'Pending');
        $wadek = (string) ($loan->status_wadek1 ?? 'Pending');
        $is_external = (($loan->jenis_peminjaman ?? '') === 'luar_kampus' || ($loan->jenis_peminjaman ?? '') === 'external');

        if ($role === 'kaprodi') {
            return $status === 'Menunggu ACC Kaprodi' && $kaprodi === 'Pending';
        }
        if ($role === 'laboran') {
            return in_array($status, ['Menunggu Verifikasi Laboran', 'Menunggu Pengecekan Laboran', 'Menunggu Persetujuan'], true)
                && $kaprodi === 'Disetujui' && $laboran === 'Pending';
        }
        if ($role === 'kaur') {
            return $status === 'Menunggu ACC Kaur'
                && $kaprodi === 'Disetujui' && $kaur === 'Pending';
        }
        if ($role === 'wadek') {
            return $is_external
                && in_array($status, ['Menunggu ACC Wadek', 'Menunggu Persetujuan Wadek'], true)
                && $kaprodi === 'Disetujui' && $kaur === 'Disetujui' && $wadek === 'Pending';
        }
        if ($role === 'finalisasi_qr' || $role === 'serah_terima') {
            return in_array($status, ['Disetujui (Menunggu Pengambilan)', 'Disetujui (Menunggu Finalisasi QR)'], true);
        }
        if ($role === 'pengembalian') {
            return in_array($status, ['Sedang Dipinjam', 'Dipinjam'], true);
        }

        return false;
    }
}
