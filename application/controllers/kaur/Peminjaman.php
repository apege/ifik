<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peminjaman extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'loan_progress', 'scm_ajax', 'scm_date']);
        $this->load->model('PeminjamanBarang_model');
        // $this->guard_kaur(); // Temporarily disabled for open access
    }   

    private function guard_kaur() {
        if (!$this->session->userdata('logged_in')) {
            if (scm_is_ajax()) {
                scm_json_abort(['success' => false, 'message' => 'Sesi Anda berakhir. Silakan login kembali.', 'redirect' => site_url('auth')], 401);
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('auth');
        }

       if ((int) $this->session->userdata('role_id') !== 2) {
            if (scm_is_ajax()) {
                scm_json_abort(['success' => false, 'message' => 'Anda tidak memiliki izin untuk memproses approval Kaur.'], 403);
            }
            $this->session->set_flashdata('error', 'Akses ditolak. Approval ini khusus Kaur Laboratorium.');
            redirect('dashboard');
        }
    }

    public function index() {
        $data['title'] = 'Approval Peminjaman - Kaur';
        $data['pengajuan'] = $this->PeminjamanBarang_model->get_visible_peminjaman(['action_role' => 'kaur']);
        $this->load->view('kaur/dashboard', $data);
    }

    public function setujui($id_peminjaman) {
        $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id_peminjaman);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data peminjaman tidak ditemukan.');
            redirect('kaur/dashboard/peminjaman');
        }

        if (!scm_loan_can_act($peminjaman, 'kaur')) {
            $this->session->set_flashdata('error', 'Pengajuan belum menyelesaikan tahap Kaprodi dan Laboran.');
            redirect('kaur/dashboard/peminjaman');
        }

        $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
        $stock_shortages = $this->PeminjamanBarang_model->get_group_stock_shortages($group_id);
        if (!empty($stock_shortages)) {
            $this->session->set_flashdata('error', $this->stock_shortage_message($stock_shortages));
            redirect('kaur/dashboard/peminjaman');
        }
        $is_external = (($peminjaman->jenis_peminjaman ?? '') === 'luar_kampus' || ($peminjaman->jenis_peminjaman ?? '') === 'external');
        if ($is_external) {
            $update = [
                'status' => 'Menunggu ACC Wadek',
                'status_kaur' => 'Disetujui',
                'catatan_kaur' => $this->input->post('catatan_kaur', true),
                'tgl_approve_kaur' => date('Y-m-d H:i:s'),
                'id_approver_kaur' => $this->session->userdata('id_user'),
                'status_wadek1' => 'Pending',
            ];
        } else {
            $update = [
                'status' => 'Disetujui (Menunggu Pengambilan)',
                'status_kaur' => 'Disetujui',
                'catatan_kaur' => $this->input->post('catatan_kaur', true),
                'tgl_approve_kaur' => date('Y-m-d H:i:s'),
                'id_approver_kaur' => $this->session->userdata('id_user'),
                'qr_locked' => 1,
                'qr_finalized_at' => date('Y-m-d H:i:s'),
                'qr_finalized_by' => $this->session->userdata('id_user'),
            ];
        }

        $ok = $this->PeminjamanBarang_model->approve_group_with_reservation($group_id, ['Menunggu ACC Kaur'], $update);
        if ($ok && !empty($peminjaman->id_user)) {
            $notif_pesan = $is_external
                ? 'Peminjaman luar kampus Anda telah disetujui Kaur dan diteruskan untuk persetujuan Wakil Dekan (Wadek).'
                : 'Peminjaman Anda telah disetujui resmi oleh Kaur. QR Code transaksi sudah aktif, silakan lakukan pengambilan barang di laboratorium.';
            $this->PeminjamanBarang_model->create_notifikasi(
                null,
                $peminjaman->id_user,
                'Peminjaman disetujui Kaur',
                $notif_pesan,
                site_url('peminjaman_barang/riwayat')
            );
        }
        if ($ok) {
            if ($is_external) {
                $this->PeminjamanBarang_model->create_notifikasi(
                    'wadek',
                    null,
                    'Pengajuan Eksternal Menunggu ACC Wadek',
                    'Pengajuan peminjaman barang luar kampus dari ' . ($peminjaman->nama_peminjam ?? 'Peminjam') . ' menunggu persetujuan Anda.',
                    site_url('wadek/peminjaman')
                );
            } else {
                $this->PeminjamanBarang_model->create_notifikasi(
                    'laboran',
                    null,
                    'Barang Siap Diserahterimakan',
                    ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah disetujui Kaur dan siap untuk serah terima fisik barang.',
                    site_url('admin/peminjaman')
                );
            }
        }

        $flash_msg = $ok 
            ? ($is_external ? 'Pengajuan berhasil disetujui Kaur dan diteruskan ke Wakil Dekan (Wadek).' : 'Pengajuan berhasil disetujui. QR Code sudah aktif untuk pengambilan barang.')
            : 'Gagal menyetujui pengajuan.';
        $this->session->set_flashdata($ok ? 'success' : 'error', $flash_msg);
        redirect('kaur/dashboard/peminjaman');
    }

    public function export_pengajuan_acc() {
        $filters = [
            'status' => $this->input->get('status', true),
            'pencarian' => $this->input->get('q', true),
            'tanggal' => $this->input->get('tanggal', true),
        ];

        $data['title'] = 'Laporan Pengajuan Sampai Tahap ACC';
        $data['rows'] = $this->PeminjamanBarang_model->get_pengajuan_sampai_acc_report($filters);
        $filename = 'laporan_pengajuan_sampai_acc_' . date('Ymd_His') . '.xlsx';

        if ($this->input->get('download') !== '1' && $this->input->get('inline') !== '1') {
            $query = $this->input->get();
            $this->load->view('shared/export_preview', [
                'title' => 'Preview Laporan Pengajuan Sampai ACC',
                'download_url' => current_url() . '?' . http_build_query(array_merge($query, ['download' => 1])),
                'iframe_url' => current_url() . '?' . http_build_query(array_merge($query, ['inline' => 1])),
                'back_url' => site_url('kaur/dashboard/peminjaman'),
            ]);
            return;
        }

        if ($this->input->get('download') === '1') {
            $this->load->helper('scm_xlsx');
            scm_download_xlsx($filename, $this->load->view('admin/export_pengajuan_acc', $data, true));
            return;
        }
        $this->load->view('admin/export_pengajuan_acc', $data);
    }

    public function tolak($id_peminjaman) {
        $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id_peminjaman);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data peminjaman tidak ditemukan.');
            redirect('kaur/dashboard/peminjaman');
        }

        if (!scm_loan_can_act($peminjaman, 'kaur')) {
            $this->session->set_flashdata('error', 'Pengajuan belum berada di tahap ACC Kaur.');
            redirect('kaur/dashboard/peminjaman');
        }

        $catatan = trim((string) $this->input->post('catatan_kaur', true));
        if ($catatan === '') {
            $this->session->set_flashdata('error', 'Alasan penolakan wajib diisi.');
            redirect('kaur/dashboard/peminjaman');
        }

        $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
        $ok = $this->PeminjamanBarang_model->reject_group_and_release($group_id, [
            'status' => 'Ditolak',
            'status_kaur' => 'Ditolak',
            'catatan_kaur' => $catatan,
            'tgl_approve_kaur' => date('Y-m-d H:i:s'),
            'id_approver_kaur' => $this->session->userdata('id_user'),
        ], ['Menunggu ACC Kaur']);

        if ($ok && !empty($peminjaman->id_user)) {
            $this->PeminjamanBarang_model->create_notifikasi(
                null,
                $peminjaman->id_user,
                'Peminjaman ditolak Kaur',
                'Pengajuan peminjaman Anda ditolak pada tahap ACC Kaur. Catatan: ' . $catatan,
                site_url('peminjaman/riwayat')
            );
        }

        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Pengajuan berhasil ditolak.' : 'Gagal menolak pengajuan.');
        redirect('kaur/dashboard/peminjaman');
    }

    public function bulk() {
        $ajax = scm_is_ajax();
        if (strtoupper((string) $this->input->method()) !== 'POST') {
            if ($ajax) {
                scm_json_response(['success' => false, 'message' => 'Metode request tidak diizinkan.'], 405);
                return;
            }
            redirect('kaur/dashboard/peminjaman');
        }

        $action = strtolower(trim((string) $this->input->post('action', true)));
        $ids = $this->bulk_ids();
        $catatan = trim((string) $this->input->post('bulk_note', true));
        if (!in_array($action, ['approve', 'reject'], true) || empty($ids)) {
            if ($ajax) {
                scm_json_response(['success' => false, 'message' => 'Pilih minimal satu pengajuan yang dapat diproses.'], 422);
                return;
            }
            $this->session->set_flashdata('error', 'Pilih minimal satu pengajuan yang dapat diproses.');
            redirect('kaur/dashboard/peminjaman');
        }
        if ($action === 'reject' && $catatan === '') {
            if ($ajax) {
                scm_json_response(['success' => false, 'message' => 'Alasan penolakan terpilih wajib diisi.'], 422);
                return;
            }
            $this->session->set_flashdata('error', 'Alasan penolakan terpilih wajib diisi.');
            redirect('kaur/dashboard/peminjaman');
        }

        $processed = 0;
        $skipped = 0;
        $processed_ids = [];
        $skipped_ids = [];
        $stock_messages = [];
        foreach ($ids as $id) {
            $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id);
            if (!$peminjaman || !scm_loan_can_act($peminjaman, 'kaur')) {
                $skipped++;
                $skipped_ids[] = $id;
                continue;
            }
            $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
            if ($action === 'approve') {
                $stock_shortages = $this->PeminjamanBarang_model->get_group_stock_shortages($group_id);
                if (!empty($stock_shortages)) {
                    $skipped++;
                    $skipped_ids[] = $id;
                    $stock_messages[] = $this->stock_shortage_message($stock_shortages);
                    continue;
                }
                $is_external = (($peminjaman->jenis_peminjaman ?? '') === 'luar_kampus' || ($peminjaman->jenis_peminjaman ?? '') === 'external');
                if ($is_external) {
                    $ok = $this->PeminjamanBarang_model->approve_group_with_reservation($group_id, ['Menunggu ACC Kaur'], [
                        'status' => 'Menunggu ACC Wadek',
                        'status_kaur' => 'Disetujui',
                        'catatan_kaur' => '',
                        'tgl_approve_kaur' => date('Y-m-d H:i:s'),
                        'id_approver_kaur' => $this->session->userdata('id_user'),
                        'status_wadek1' => 'Pending',
                    ]);
                    if ($ok && !empty($peminjaman->id_user)) {
                        $this->PeminjamanBarang_model->create_notifikasi(null, $peminjaman->id_user, 'Peminjaman disetujui Kaur',
                            'Peminjaman luar kampus Anda telah disetujui Kaur dan diteruskan untuk persetujuan Wakil Dekan (Wadek).',
                            site_url('peminjaman_barang/riwayat'));
                    }
                    if ($ok) {
                        $this->PeminjamanBarang_model->create_notifikasi('wadek', null, 'Pengajuan Eksternal Menunggu ACC Wadek',
                            'Pengajuan peminjaman barang luar kampus dari ' . ($peminjaman->nama_peminjam ?? 'Peminjam') . ' menunggu persetujuan Anda.',
                            site_url('wadek/peminjaman'));
                    }
                } else {
                    $ok = $this->PeminjamanBarang_model->approve_group_with_reservation($group_id, ['Menunggu ACC Kaur'], [
                        'status' => 'Disetujui (Menunggu Pengambilan)',
                        'status_kaur' => 'Disetujui',
                        'catatan_kaur' => '',
                        'tgl_approve_kaur' => date('Y-m-d H:i:s'),
                        'id_approver_kaur' => $this->session->userdata('id_user'),
                        'qr_locked' => 1,
                        'qr_finalized_at' => date('Y-m-d H:i:s'),
                        'qr_finalized_by' => $this->session->userdata('id_user'),
                    ]);
                    if ($ok && !empty($peminjaman->id_user)) {
                        $this->PeminjamanBarang_model->create_notifikasi(null, $peminjaman->id_user, 'Peminjaman disetujui Kaur',
                            'Peminjaman Anda telah disetujui resmi oleh Kaur. QR Code transaksi sudah aktif, silakan lakukan pengambilan barang di laboratorium.',
                            site_url('peminjaman_barang/riwayat'));
                    }
                    if ($ok) {
                        $this->PeminjamanBarang_model->create_notifikasi('laboran', null, 'Barang Siap Diserahterimakan',
                            ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah disetujui Kaur dan siap untuk serah terima fisik barang.',
                            site_url('admin/peminjaman'));
                    }
                }
            } else {
                $ok = $this->PeminjamanBarang_model->reject_group_and_release($group_id, [
                    'status' => 'Ditolak',
                    'status_kaur' => 'Ditolak',
                    'catatan_kaur' => $catatan,
                    'tgl_approve_kaur' => date('Y-m-d H:i:s'),
                    'id_approver_kaur' => $this->session->userdata('id_user'),
                ], ['Menunggu ACC Kaur']);
                if ($ok && !empty($peminjaman->id_user)) {
                    $this->PeminjamanBarang_model->create_notifikasi(null, $peminjaman->id_user, 'Peminjaman ditolak Kaur',
                        'Pengajuan peminjaman Anda ditolak pada tahap ACC Kaur. Catatan: ' . $catatan,
                        site_url('peminjaman/riwayat'));
                }
            }
            if ($ok) {
                $processed++;
                $processed_ids[] = $id;
            } else {
                $skipped++;
                $skipped_ids[] = $id;
            }
        }

        $label = $action === 'approve' ? 'disetujui' : 'ditolak';
        $message = $processed . ' pengajuan berhasil ' . $label . '.';
        if ($skipped > 0) $message .= ' ' . $skipped . ' tidak diproses karena statusnya berubah, bukan kewenangan Kaur, atau stok belum mencukupi.';
        if (!empty($stock_messages)) $message .= ' ' . implode(' ', array_values(array_unique($stock_messages)));
        if ($ajax) {
            scm_json_response([
                'success' => $processed > 0,
                'partial' => $processed > 0 && $skipped > 0,
                'message' => $message,
                'action' => $action,
                'status' => $action === 'approve' ? 'Disetujui (Menunggu Pengambilan)' : 'Ditolak',
                'processed' => $processed,
                'skipped' => $skipped,
                'processed_ids' => $processed_ids,
                'skipped_ids' => $skipped_ids,
                'actionable_remaining' => $this->PeminjamanBarang_model->count_actionable_peminjaman('kaur'),
            ], $processed > 0 ? 200 : 409);
            return;
        }
        $this->session->set_flashdata($processed > 0 ? 'success' : 'error', $message);
        redirect('kaur/dashboard/peminjaman');
    }

    private function bulk_ids() {
        $ids = $this->input->post('loan_ids', true);
        if (!is_array($ids)) return [];
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static function ($id) {
            return $id > 0;
        })));
        return array_slice($ids, 0, 25);
    }

    private function stock_shortage_message(array $shortages) {
        $items = [];
        foreach (array_slice($shortages, 0, 3) as $shortage) {
            $name = trim((string) ($shortage['kode_aset'] ?? ''));
            if ($name === '') $name = trim((string) ($shortage['nama_aset'] ?? 'Aset'));
            $items[] = $name . ' membutuhkan ' . (int) ($shortage['required'] ?? 0) . ' unit, tersedia ' . (int) ($shortage['available'] ?? 0) . ' unit';
        }
        return 'Pengajuan belum dapat diteruskan ke Laboran karena stok tidak cukup: ' . implode('; ', $items) . '.';
    }
}
