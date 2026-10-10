<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peminjaman extends CI_Controller {
    private $kaprodi_prodi;
    private $uses_legacy_scope = false;

    public function __construct() {
        parent::__construct();
        $this->load->helper(['loan_progress', 'scm_date', 'scm_ajax', 'scm_pagination', 'scm_sort', 'fik_prodi']);
        $this->load->model('PeminjamanBarang_model');
        $this->load->model('User_model');
        $this->guard_kaprodi();
        $account = $this->User_model->get_by_id($this->session->userdata('id_user'));
        $this->kaprodi_prodi = fik_normalize_prodi($account->prodi ?? null);
        // Data peminjaman lama belum selalu mempunyai prodi. Akun Kaprodi
        // legacy tetap dapat menyelesaikan data tersebut, tanpa mendapat
        // akses ke pengajuan yang sudah memiliki prodi.
        $this->uses_legacy_scope = !$this->kaprodi_prodi;
    }

    private function guard_kaprodi() {
        if (!$this->session->userdata('logged_in')) {
            if (scm_is_ajax()) {
                scm_json_abort(['success' => false, 'message' => 'Sesi Anda berakhir. Silakan login kembali.', 'redirect' => site_url('auth')], 401);
            }
            redirect('auth');
        }
        if ((int) $this->session->userdata('role_id') !== 16)  {
            if (scm_is_ajax()) {
                scm_json_abort(['success' => false, 'message' => 'Anda tidak memiliki izin untuk memproses approval Kaprodi.'], 403);
            }
            $this->session->set_flashdata('error', 'Akses approval peminjaman khusus Kaprodi.');
            redirect('dashboard');
        }
    }

    public function index() {
        $approval_sort = in_array($this->input->get('sort_by', true), ['number', 'peminjam', 'barang', 'lab', 'masa', 'status'], true) ? $this->input->get('sort_by', true) : '';
        $approval_dir = strtolower((string) $this->input->get('sort_dir', true)) === 'asc' ? 'asc' : 'desc';
        $filters = array_merge([
            'multi_filters' => $this->read_filters(),
            'action_role' => 'kaprodi',
            'sort_by' => $approval_sort,
            'sort_dir' => $approval_dir,
        ], $this->scope_filters());
        $per_page = $this->read_per_page();
        $page = max(1, (int) $this->input->get('page'));
        $total = $this->PeminjamanBarang_model->count_visible_peminjaman($filters);
        $total_pages = max(1, (int) ceil($total / $per_page));
        if ($page > $total_pages) $page = $total_pages;

        $return_page = max(1, (int) $this->input->get('return_page'));
        $return_sort = in_array($this->input->get('return_sort', true), ['peminjam', 'barang', 'masa', 'status'], true) ? $this->input->get('return_sort', true) : 'masa';
        $return_dir = strtolower((string) $this->input->get('return_dir', true)) === 'asc' ? 'asc' : 'desc';
        $return_filters = array_merge([
            'sort_by' => $return_sort,
            'sort_dir' => $return_dir,
        ], $this->scope_filters());
        $return_total = $this->PeminjamanBarang_model->count_pengembalian_readonly($return_filters);
        $return_total_pages = max(1, (int) ceil($return_total / $per_page));
        if ($return_page > $return_total_pages) $return_page = $return_total_pages;
        $data = [
            'title' => 'Approval Peminjaman - Kaprodi',
            'filters' => $filters,
            'filter_rows' => $filters['multi_filters'],
            'pengajuan' => $this->PeminjamanBarang_model->get_visible_peminjaman($filters, $per_page, ($page - 1) * $per_page),
            'approval_total' => $total,
            'approval_actionable' => $this->PeminjamanBarang_model->count_actionable_peminjaman('kaprodi', $filters),
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => $total_pages,
            'approval_sort' => $approval_sort,
            'approval_dir' => $approval_dir,
            'pengembalian' => $this->PeminjamanBarang_model->get_pengembalian_readonly($return_filters, $per_page, ($return_page - 1) * $per_page),
            'return_total' => $return_total,
            'return_page' => $return_page,
            'return_total_pages' => $return_total_pages,
            'return_sort' => $return_sort,
            'return_dir' => $return_dir,
            'kaprodi_prodi' => $this->kaprodi_prodi,
            'notifikasi' => $this->PeminjamanBarang_model->get_notifikasi(null, $this->session->userdata('id_user'), 20),
            'unread_notifikasi' => $this->PeminjamanBarang_model->count_notifikasi_unread(null, $this->session->userdata('id_user')),
        ];
        $this->load->view('kaprodi/peminjaman', $data);
    }

    private function read_filters() {
        $allowed = ['number', 'peminjam', 'barang', 'lab', 'masa', 'status'];
        $fields = (array) $this->input->get('filter_field', true);
        $values = (array) $this->input->get('filter_value', true);
        $rows = [];
        foreach ($fields as $index => $field) {
            $field = trim((string) $field);
            if (count($rows) >= 4 || !in_array($field, $allowed, true)) continue;
            $rows[] = ['field' => $field, 'value' => trim((string) ($values[$index] ?? ''))];
        }
        return $rows ?: [['field' => 'number', 'value' => '']];
    }

    private function read_per_page() {
        $value = (int) $this->input->get('per_page');
        return in_array($value, [10, 25], true) ? $value : 10;
    }

    public function setujui($id_peminjaman) {
        $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id_peminjaman);
        if (!$peminjaman || !$this->is_loan_in_scope($peminjaman) || !scm_loan_can_act($peminjaman, 'kaprodi')) {
            $this->session->set_flashdata('error', 'Pengajuan tidak ditemukan, bukan dari prodi Anda, atau sudah diproses.');
            redirect('kaprodi/peminjaman');
        }

        $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
        $stock_shortages = $this->PeminjamanBarang_model->get_group_stock_shortages($group_id);
        if (!empty($stock_shortages)) {
            $this->session->set_flashdata('error', $this->stock_shortage_message($stock_shortages));
            redirect('kaprodi/peminjaman');
        }

        $ok = $this->PeminjamanBarang_model->approve_group_with_reservation($group_id, ['Menunggu ACC Kaprodi'], [
            'status' => 'Menunggu ACC Kaur',
            'status_kaprodi' => 'Disetujui',
            'catatan_kaprodi' => trim((string) $this->input->post('catatan_kaprodi', true)),
            'tgl_approve_kaprodi' => date('Y-m-d H:i:s'),
            'id_approver_kaprodi' => $this->session->userdata('id_user'),
        ]);
        if ($ok) {
            $this->PeminjamanBarang_model->create_notifikasi('kaur', null, 'Peminjaman disetujui Kaprodi',
                ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah di-ACC Kaprodi dan menunggu persetujuan Anda sebagai Kaur.',
                site_url('kaur/barang'));
        }
        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Pengajuan disetujui dan diteruskan ke Kaur untuk persetujuan resmi.' : 'Gagal memproses pengajuan atau reservasi stok tidak tersedia.');
        redirect('kaprodi/peminjaman');
    }

    public function tolak($id_peminjaman) {
        $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id_peminjaman);
        if (!$peminjaman || !$this->is_loan_in_scope($peminjaman) || ($peminjaman->status ?? '') !== 'Menunggu ACC Kaprodi'
            || ($peminjaman->status_kaprodi ?? 'Pending') !== 'Pending') {
            $this->session->set_flashdata('error', 'Pengajuan tidak ditemukan atau sudah diproses.');
            redirect('kaprodi/peminjaman');
        }

        $catatan = trim((string) $this->input->post('catatan_kaprodi', true));
        if ($catatan === '') {
            $this->session->set_flashdata('error', 'Alasan penolakan wajib diisi.');
            redirect('kaprodi/peminjaman');
        }
        $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
        $ok = $this->PeminjamanBarang_model->reject_group_and_release($group_id, [
            'status' => 'Ditolak',
            'status_kaprodi' => 'Ditolak',
            'catatan_kaprodi' => $catatan,
            'tgl_approve_kaprodi' => date('Y-m-d H:i:s'),
            'id_approver_kaprodi' => $this->session->userdata('id_user'),
        ], ['Menunggu ACC Kaprodi']);
        if ($ok && !empty($peminjaman->id_user)) {
            $this->PeminjamanBarang_model->create_notifikasi(null, $peminjaman->id_user, 'Peminjaman ditolak Kaprodi',
                'Pengajuan peminjaman Anda ditolak Kaprodi. Catatan: ' . $catatan,
                site_url('peminjaman/riwayat'));
        }
        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Pengajuan ditolak.' : 'Gagal memproses pengajuan.');
        redirect('kaprodi/peminjaman');
    }

    public function bulk() {
        $ajax = scm_is_ajax();
        if (strtoupper((string) $this->input->method()) !== 'POST') {
            if ($ajax) {
                scm_json_response(['success' => false, 'message' => 'Metode request tidak diizinkan.'], 405);
                return;
            }
            redirect('kaprodi/peminjaman');
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
            redirect('kaprodi/peminjaman');
        }
        if ($action === 'reject' && $catatan === '') {
            if ($ajax) {
                scm_json_response(['success' => false, 'message' => 'Alasan penolakan terpilih wajib diisi.'], 422);
                return;
            }
            $this->session->set_flashdata('error', 'Alasan penolakan terpilih wajib diisi.');
            redirect('kaprodi/peminjaman');
        }

        $processed = 0;
        $skipped = 0;
        $processed_ids = [];
        $skipped_ids = [];
        $stock_messages = [];
        foreach ($ids as $id) {
            $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id);
            if (!$peminjaman || !$this->is_loan_in_scope($peminjaman) || !scm_loan_can_act($peminjaman, 'kaprodi')) {
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
                $ok = $this->PeminjamanBarang_model->approve_group_with_reservation($group_id, ['Menunggu ACC Kaprodi'], [
                    'status' => 'Menunggu ACC Kaur',
                    'status_kaprodi' => 'Disetujui',
                    'catatan_kaprodi' => '',
                    'tgl_approve_kaprodi' => date('Y-m-d H:i:s'),
                    'id_approver_kaprodi' => $this->session->userdata('id_user'),
                ]);
                if ($ok) {
                    $this->PeminjamanBarang_model->create_notifikasi('kaur', null, 'Peminjaman disetujui Kaprodi',
                        ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah di-ACC Kaprodi dan menunggu persetujuan Anda sebagai Kaur.',
                        site_url('kaur/barang'));
                }
            } else {
                $ok = $this->PeminjamanBarang_model->reject_group_and_release($group_id, [
                    'status' => 'Ditolak',
                    'status_kaprodi' => 'Ditolak',
                    'catatan_kaprodi' => $catatan,
                    'tgl_approve_kaprodi' => date('Y-m-d H:i:s'),
                    'id_approver_kaprodi' => $this->session->userdata('id_user'),
                ], ['Menunggu ACC Kaprodi']);
                if ($ok && !empty($peminjaman->id_user)) {
                    $this->PeminjamanBarang_model->create_notifikasi(null, $peminjaman->id_user, 'Peminjaman ditolak Kaprodi',
                        'Pengajuan peminjaman Anda ditolak Kaprodi. Catatan: ' . $catatan,
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
        if ($skipped > 0) $message .= ' ' . $skipped . ' tidak diproses karena statusnya berubah, bukan kewenangan Kaprodi, atau stok belum mencukupi.';
        if (!empty($stock_messages)) $message .= ' ' . implode(' ', array_values(array_unique($stock_messages)));
        if ($ajax) {
            scm_json_response([
                'success' => $processed > 0,
                'partial' => $processed > 0 && $skipped > 0,
                'message' => $message,
                'action' => $action,
                'status' => $action === 'approve' ? 'Menunggu Verifikasi Laboran' : 'Ditolak',
                'processed' => $processed,
                'skipped' => $skipped,
                'processed_ids' => $processed_ids,
                'skipped_ids' => $skipped_ids,
                'actionable_remaining' => $this->PeminjamanBarang_model->count_actionable_peminjaman('kaprodi', $this->scope_filters()),
            ], $processed > 0 ? 200 : 409);
            return;
        }
        $this->session->set_flashdata($processed > 0 ? 'success' : 'error', $message);
        redirect('kaprodi/peminjaman');
    }

    private function bulk_ids() {
        $ids = $this->input->post('loan_ids', true);
        if (!is_array($ids)) return [];
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static function ($id) {
            return $id > 0;
        })));
        return array_slice($ids, 0, 25);
    }

    private function is_loan_in_scope($peminjaman) {
        return true; // Mode Demo: Izinkan approval semua data peminjaman tanpa penyaringan prodi yang ketat
    }

    private function scope_filters() {
        return []; // Mode Demo: Menampilkan semua data peminjaman tanpa penyaringan prodi
    }

    private function stock_shortage_message(array $shortages) {
        $items = [];
        foreach (array_slice($shortages, 0, 3) as $shortage) {
            $name = trim((string) ($shortage['kode_aset'] ?? ''));
            if ($name === '') $name = trim((string) ($shortage['nama_aset'] ?? 'Aset'));
            $items[] = $name . ' membutuhkan ' . (int) ($shortage['required'] ?? 0)
                . ' unit, tersedia ' . (int) ($shortage['available'] ?? 0) . ' unit';
        }
        return 'Pengajuan belum dapat diteruskan ke Laboran karena stok tidak cukup: ' . implode('; ', $items) . '.';
    }
}
