<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peminjaman extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'loan_progress', 'scm_ajax', 'scm_date', 'scm_pagination', 'scm_sort']);
        $this->load->model('PeminjamanBarang_model');
        $this->load->model('User_model');
        $this->guard_wadek();
    }

    private function guard_wadek() {
        if (!$this->session->userdata('logged_in')) {
            if (scm_is_ajax()) {
                scm_json_abort(['success' => false, 'message' => 'Sesi Anda berakhir. Silakan login kembali.', 'redirect' => site_url('login')], 401);
            }
            redirect('login');
        }
        $role_id = (int) ($this->session->userdata('role_id') ?? 0);
        $role = strtolower((string) $this->session->userdata('role'));
        // Allow Wadek, Dosen (3), Admin System (1), Super Admin (22), or Kaur/Kaprodi test
        $allowed_roles = [1, 2, 3, 7, 8, 9, 10, 16, 22];
        $is_wadek_role = in_array($role_id, $allowed_roles, true) || strpos($role, 'wadek') !== false || strpos($role, 'dekan') !== false || $role === 'admin';
        if (!$is_wadek_role) {
            if (scm_is_ajax()) {
                scm_json_abort(['success' => false, 'message' => 'Akses ditolak. Halaman ini khusus Wakil Dekan (Wadek).'], 403);
            }
            $this->session->set_flashdata('error', 'Akses ditolak. Approval peminjaman luar kampus khusus Wakil Dekan.');
            redirect('dashboard');
        }
    }

    public function index() {
        $filters = [
            'action_role' => 'wadek',
            'status' => $this->input->get('status', true),
            'pencarian' => $this->input->get('q', true),
            'multi_filters' => $this->read_filters(),
            'sort_by' => $this->input->get('sort_by', true),
            'sort_dir' => $this->input->get('sort_dir', true) ?: 'desc',
        ];

        $per_page = $this->read_per_page();
        $page = max(1, (int) $this->input->get('page'));
        
        $total = $this->PeminjamanBarang_model->count_visible_peminjaman($filters);
        $total_pages = max(1, (int) ceil($total / $per_page));
        if ($page > $total_pages) $page = $total_pages;

        $data = [
            'title' => 'Approval Peminjaman Luar Kampus - Wakil Dekan (Wadek)',
            'filters' => $filters,
            'filter_rows' => $filters['multi_filters'],
            'pengajuan' => $this->PeminjamanBarang_model->get_visible_peminjaman($filters, $per_page, ($page - 1) * $per_page),
            'approval_total' => $total,
            'approval_actionable' => $this->PeminjamanBarang_model->count_actionable_peminjaman('wadek', $filters),
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => $total_pages,
            'notifikasi' => $this->PeminjamanBarang_model->get_notifikasi('wadek', $this->session->userdata('id_user'), 20),
            'unread_notifikasi' => $this->PeminjamanBarang_model->count_notifikasi_unread('wadek', $this->session->userdata('id_user')),
        ];

        $this->load->view('wadek/peminjaman', $data);
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

    private function read_per_page($default = 10) {
        $allowed = [10, 25, 50, 100];
        $val = (int) $this->input->get('per_page', true);
        return in_array($val, $allowed, true) ? $val : $default;
    }

    public function setujui($id_peminjaman) {
        $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id_peminjaman);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data peminjaman tidak ditemukan.');
            redirect('wadek/peminjaman');
        }

        if (!scm_loan_can_act($peminjaman, 'wadek')) {
            $this->session->set_flashdata('error', 'Pengajuan belum menyelesaikan tahap persetujuan Kaur atau bukan peminjaman eksternal.');
            redirect('wadek/peminjaman');
        }

        $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
        $catatan = trim((string) $this->input->post('catatan_wadek', true));

        $update = [
            'status' => 'Disetujui (Menunggu Pengambilan)',
            'status_wadek1' => 'Disetujui',
            'catatan_wadek1' => $catatan,
            'tgl_approve_wadek1' => date('Y-m-d H:i:s'),
            'id_approver_wadek1' => $this->session->userdata('id_user') ?: $this->session->userdata('username'),
            'qr_locked' => 1,
            'qr_finalized_at' => date('Y-m-d H:i:s'),
            'qr_finalized_by' => $this->session->userdata('id_user'),
        ];

        $ok = $this->PeminjamanBarang_model->approve_group_with_reservation($group_id, ['Menunggu ACC Wadek', 'Menunggu Persetujuan Wadek'], $update);
        if ($ok && !empty($peminjaman->id_user)) {
            $this->PeminjamanBarang_model->create_notifikasi(
                null,
                $peminjaman->id_user,
                'Peminjaman Luar Kampus Disetujui Wadek',
                'Pengajuan peminjaman barang luar kampus Anda telah disetujui resmi oleh Wakil Dekan (Wadek). QR Code transaksi sudah aktif, silakan ambil barang di laboratorium.',
                site_url('peminjaman_barang/riwayat')
            );
        }
        if ($ok) {
            $this->PeminjamanBarang_model->create_notifikasi(
                'laboran',
                null,
                'Barang Luar Kampus Siap Diserahterimakan',
                ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah disetujui Wakil Dekan dan siap untuk serah terima fisik barang.',
                site_url('admin/peminjaman')
            );
        }

        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Pengajuan peminjaman luar kampus berhasil disetujui Wadek. QR Code aktif untuk pengambilan.' : 'Gagal menyetujui pengajuan.');
        redirect('wadek/peminjaman');
    }

    public function tolak($id_peminjaman) {
        $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id_peminjaman);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data peminjaman tidak ditemukan.');
            redirect('wadek/peminjaman');
        }

        if (!scm_loan_can_act($peminjaman, 'wadek')) {
            $this->session->set_flashdata('error', 'Pengajuan belum berada di tahap ACC Wadek.');
            redirect('wadek/peminjaman');
        }

        $catatan = trim((string) $this->input->post('catatan_wadek', true));
        if ($catatan === '') {
            $this->session->set_flashdata('error', 'Alasan penolakan wajib diisi.');
            redirect('wadek/peminjaman');
        }

        $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
        $ok = $this->PeminjamanBarang_model->reject_group_and_release($group_id, [
            'status' => 'Ditolak',
            'status_wadek1' => 'Ditolak',
            'catatan_wadek1' => $catatan,
            'tgl_approve_wadek1' => date('Y-m-d H:i:s'),
            'id_approver_wadek1' => $this->session->userdata('id_user') ?: $this->session->userdata('username'),
        ], ['Menunggu ACC Wadek', 'Menunggu Persetujuan Wadek']);

        if ($ok && !empty($peminjaman->id_user)) {
            $this->PeminjamanBarang_model->create_notifikasi(
                null,
                $peminjaman->id_user,
                'Peminjaman Luar Kampus Ditolak Wadek',
                'Pengajuan peminjaman barang luar kampus Anda ditolak pada tahap ACC Wakil Dekan. Catatan: ' . $catatan,
                site_url('peminjaman_barang/riwayat')
            );
        }

        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Pengajuan peminjaman luar kampus berhasil ditolak.' : 'Gagal menolak pengajuan.');
        redirect('wadek/peminjaman');
    }

    public function batch_setujui() {
        $ids = (array) $this->input->post('ids', true);
        $action = (string) $this->input->post('action', true);
        $catatan = trim((string) $this->input->post('catatan', true));
        $ajax = scm_is_ajax();

        if (empty($ids) || !in_array($action, ['approve', 'reject'], true)) {
            if ($ajax) {
                scm_json_response(['success' => false, 'message' => 'Pilih minimal satu pengajuan yang dapat diproses.'], 422);
                return;
            }
            $this->session->set_flashdata('error', 'Pilih minimal satu pengajuan.');
            redirect('wadek/peminjaman');
        }

        $processed = 0;
        $skipped = 0;
        foreach ($ids as $id) {
            $peminjaman = $this->PeminjamanBarang_model->get_peminjaman_by_id($id);
            if (!$peminjaman || !scm_loan_can_act($peminjaman, 'wadek')) {
                $skipped++;
                continue;
            }
            $group_id = $peminjaman->group_id ?: 'single-' . (int) $peminjaman->id_peminjaman;
            if ($action === 'approve') {
                $ok = $this->PeminjamanBarang_model->approve_group_with_reservation($group_id, ['Menunggu ACC Wadek', 'Menunggu Persetujuan Wadek'], [
                    'status' => 'Disetujui (Menunggu Pengambilan)',
                    'status_wadek1' => 'Disetujui',
                    'catatan_wadek1' => '',
                    'tgl_approve_wadek1' => date('Y-m-d H:i:s'),
                    'id_approver_wadek1' => $this->session->userdata('id_user'),
                    'qr_locked' => 1,
                    'qr_finalized_at' => date('Y-m-d H:i:s'),
                    'qr_finalized_by' => $this->session->userdata('id_user'),
                ]);
                if ($ok && !empty($peminjaman->id_user)) {
                    $this->PeminjamanBarang_model->create_notifikasi(null, $peminjaman->id_user, 'Peminjaman Luar Kampus Disetujui Wadek',
                        'Peminjaman luar kampus Anda telah disetujui resmi oleh Wakil Dekan. QR Code transaksi sudah aktif.',
                        site_url('peminjaman_barang/riwayat'));
                }
                if ($ok) {
                    $this->PeminjamanBarang_model->create_notifikasi('laboran', null, 'Barang Luar Kampus Siap Diserahterimakan',
                        ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah disetujui Wakil Dekan dan siap untuk serah terima fisik barang.',
                        site_url('admin/peminjaman'));
                }
            } else {
                $ok = $this->PeminjamanBarang_model->reject_group_and_release($group_id, [
                    'status' => 'Ditolak',
                    'status_wadek1' => 'Ditolak',
                    'catatan_wadek1' => $catatan,
                    'tgl_approve_wadek1' => date('Y-m-d H:i:s'),
                    'id_approver_wadek1' => $this->session->userdata('id_user'),
                ], ['Menunggu ACC Wadek', 'Menunggu Persetujuan Wadek']);
                if ($ok && !empty($peminjaman->id_user)) {
                    $this->PeminjamanBarang_model->create_notifikasi(null, $peminjaman->id_user, 'Peminjaman Luar Kampus Ditolak Wadek',
                        'Pengajuan peminjaman barang luar kampus Anda ditolak pada tahap ACC Wakil Dekan. Catatan: ' . $catatan,
                        site_url('peminjaman_barang/riwayat'));
                }
            }
            if ($ok) $processed++;
            else $skipped++;
        }

        $label = $action === 'approve' ? 'disetujui' : 'ditolak';
        $message = $processed . ' pengajuan luar kampus berhasil ' . $label . '.';
        if ($skipped > 0) $message .= ' ' . $skipped . ' tidak diproses karena statusnya berubah atau bukan kewenangan Wadek.';
        if ($ajax) {
            scm_json_response([
                'success' => $processed > 0,
                'message' => $message,
                'processed' => $processed,
                'skipped' => $skipped,
            ]);
            return;
        }

        $this->session->set_flashdata($processed > 0 ? 'success' : 'error', $message);
        redirect('wadek/peminjaman');
    }

    public function autocomplete() {
        $term = trim((string) $this->input->get('q', true));
        $cat  = trim((string) $this->input->get('cat', true) ?: 'all');
        $results = $this->PeminjamanBarang_model->autocomplete_wadek($term, $cat);

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($results ?: []));
    }
}
