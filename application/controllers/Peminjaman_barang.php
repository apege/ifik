<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: Peminjaman_barang
 * Modul Transaksi Peminjaman Barang / Aset Laboratorium untuk IFik
 * Terpisah dari modul Peminjaman Ruangan.
 */
class Peminjaman_barang extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // 1. Proteksi Halaman: Wajib login
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Silakan login terlebih dahulu.');
            redirect('login');
        }

        // 2. Normalisasi session agar kompatibel dengan sistem IFik
        $userId = $this->session->userdata('user_id') ?? $this->session->userdata('id_user');
        $nama   = $this->session->userdata('name') ?? $this->session->userdata('nama');
        $nim    = $this->session->userdata('nim') ?? $this->session->userdata('nidn_nim') ?? $this->session->userdata('username');
        $roleId = (int) ($this->session->userdata('role_id') ?? 4);

        $roleName = 'user';
        if ($roleId === 1) {
            $roleName = 'admin';
        } elseif ($roleId === 2) {
            $roleName = 'kaur';
        } elseif ($roleId === 21) {
            $roleName = 'laboran';
        }

        if (!$this->session->userdata('id_user')) {
            $this->session->set_userdata('id_user', $userId);
        }
        if (!$this->session->userdata('username')) {
            $this->session->set_userdata('username', $nim);
        }
        if (!$this->session->userdata('nama')) {
            $this->session->set_userdata('nama', $nama);
        }
        if (!$this->session->userdata('role')) {
            $this->session->set_userdata('role', $roleName);
        }

        // 3. Load Helper & Model
        $this->load->helper(['loan_progress', 'scm_pagination', 'scm_sort', 'scm_date', 'fik_prodi', 'url']);
        $this->load->model('Peminjaman_barang_model');
        $this->load->model('Aset_model');
        $this->load->model('User_model');
    }

    private function attach_notifikasi(&$data) {
        $role = strtolower((string) $this->session->userdata('role'));
        $roleId = (int) ($this->session->userdata('role_id') ?? 0);
        $userId = $this->session->userdata('id_user') ?: $this->session->userdata('user_id');

        if (in_array($role, ['admin', 'laboran'], true) || in_array($roleId, [1, 21], true)) {
            $data['notifikasi'] = $this->Peminjaman_barang_model->get_notifikasi('laboran', null);
            $data['unread_notifikasi'] = $this->Peminjaman_barang_model->count_notifikasi_unread('laboran', null);
        } elseif ($role === 'kaur' || $roleId === 2) {
            $data['notifikasi'] = $this->Peminjaman_barang_model->get_notifikasi('kaur', null);
            $data['unread_notifikasi'] = $this->Peminjaman_barang_model->count_notifikasi_unread('kaur', null);
        } elseif ($role === 'kaprodi') {
            $data['notifikasi'] = $this->Peminjaman_barang_model->get_notifikasi('kaprodi', null);
            $data['unread_notifikasi'] = $this->Peminjaman_barang_model->count_notifikasi_unread('kaprodi', null);
        } else {
            $data['notifikasi'] = $this->Peminjaman_barang_model->get_notifikasi(null, $userId);
            $data['unread_notifikasi'] = $this->Peminjaman_barang_model->count_notifikasi_unread(null, $userId);
        }
    }

    private function get_active_block() {
        if (strtolower((string) $this->session->userdata('role')) !== 'user') {
            return null;
        }

        return $this->Peminjaman_barang_model->get_active_block_by_user(
            $this->session->userdata('id_user'),
            $this->session->userdata('username')
        );
    }

    private function flash_block_message($block) {
        $until = !empty($block->batas_blokir) ? ' sampai ' . date('d/m/Y', strtotime($block->batas_blokir)) : ' tanpa batas waktu';
        $this->session->set_flashdata('error', 'Akun Anda sedang diblokir' . $until . '. Alasan: ' . ($block->alasan ?? '-'));
    }

    private function read_per_page($value, $default = 10) {
        $allowed = [10, 25, 50, 100];
        if (!is_scalar($value) || $value === '') {
            return $default;
        }
        $per_page = (int) $value;
        return in_array($per_page, $allowed, true) ? $per_page : $default;
    }

    /**
     * Halaman Katalog Barang Laboratorium
     * URL: http://localhost/ifik/peminjaman_barang
     */
    public function index() {
        $id_ruangan = $this->input->get('id_ruangan', true);
        $fields = (array) $this->input->get('filter_field', true);
        $values = (array) $this->input->get('filter_value', true);
        $allowed_fields = ['all', 'nama', 'kode', 'ruangan', 'kondisi', 'stok'];
        
        $filter_rows = [];
        $filters = [];
        if (!empty($fields)) {
            foreach (array_slice($fields, 0, 4) as $index => $field) {
                $field = (string) $field;
                if (!in_array($field, $allowed_fields, true)) continue;
                $value = trim((string) ($values[$index] ?? ''));
                $filter_rows[] = ['field' => $field, 'value' => $value];
                if ($value !== '') {
                    $filters[] = ['field' => $field, 'value' => $value];
                }
            }
        }
        if (empty($filter_rows)) {
            $filter_rows = [['field' => 'all', 'value' => '']];
        }

        $per_page = $this->read_per_page($this->input->get('per_page', true));
        $total = $this->Peminjaman_barang_model->count_katalog_barang($filters, $id_ruangan);
        $total_pages = max(1, (int) ceil($total / $per_page));
        $page = min(max(1, (int) $this->input->get('page', true)), $total_pages);
        $data['barang'] = $this->Peminjaman_barang_model->get_katalog_barang($filters, $per_page, ($page - 1) * $per_page, $id_ruangan);
        $data['filter_rows'] = $filter_rows;
        $data['catalog_total'] = $total;
        $data['catalog_per_page'] = $per_page;
        $data['pagination'] = compact('page', 'per_page', 'total', 'total_pages');
        
        if ($id_ruangan) {
            $data['ruangan_aktif'] = $this->db->get_where('ruangan', ['id' => $id_ruangan])->row();
        } else {
            $data['ruangan_aktif'] = null;
        }
        
        $this->attach_notifikasi($data);
        $this->load->view('peminjaman_barang/index', $data);
    }

    /**
     * Menampilkan Form Pengajuan berdasarkan ID Aset
     * URL: http://localhost/ifik/peminjaman_barang/ajukan/1
     */
    public function ajukan($id_aset) {
        $block = $this->get_active_block();
        if ($block) {
            $this->flash_block_message($block);
            redirect('peminjaman_barang');
        }

        $data['aset'] = $this->Peminjaman_barang_model->get_aset_by_id($id_aset);
        $data['program_studi'] = fik_program_studi();
        $data['jenis_peminjam_options'] = fik_jenis_peminjam();
        $data['user_prodi'] = 'S1 Desain Komunikasi Visual (DKV)';
        $data['user_jenis'] = 'Mahasiswa';
        
        if(!$data['aset']) {
            $this->session->set_flashdata('error', 'Aset tidak ditemukan!');
            redirect('peminjaman_barang');
        }

        $this->attach_notifikasi($data);
        $this->load->view('peminjaman_barang/ajukan', $data);
    }

    /**
     * Memproses Data Pengajuan & Upload Foto Kondisi Awal
     */
    public function proses_pengajuan() {
        $block = $this->get_active_block();
        if ($block) {
            $this->flash_block_message($block);
            redirect('peminjaman_barang');
        }

        $id_aset = $this->input->post('id_aset');
        $jumlah_pinjam = (int) $this->input->post('jumlah_pinjam');
        $tanggal_pinjam = $this->input->post('tanggal_pinjam');
        $tanggal_kembali = $this->input->post('tanggal_kembali_rencana');
        $prodi = fik_normalize_prodi($this->input->post('prodi', true)) ?: 'S1 Desain Komunikasi Visual (DKV)';
        $jenis_peminjam = fik_normalize_jenis_peminjam($this->input->post('jenis_pengguna', true)) ?: 'Mahasiswa';

        $aset = $this->Peminjaman_barang_model->get_aset_by_id($id_aset);
        if (!$aset) {
            $this->session->set_flashdata('error', 'Aset tidak ditemukan.');
            redirect('peminjaman_barang');
        }

        if ($jumlah_pinjam < 1 || $jumlah_pinjam > (int) $aset->jumlah_tersedia) {
            $this->session->set_flashdata('error', 'Gagal: Jumlah pinjam melebihi stok yang tersedia!');
            redirect('peminjaman_barang/ajukan/'.$id_aset);
        }

        if (strtotime($tanggal_kembali) < strtotime($tanggal_pinjam)) {
            $this->session->set_flashdata('error', 'Gagal: Tanggal kembali tidak valid!');
            redirect('peminjaman_barang/ajukan/'.$id_aset);
        }

        // Upload foto kondisi awal
        $uploadDir = FCPATH . 'assets/uploads/bukti_peminjaman/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $config['upload_path']   = $uploadDir;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 4096;
        $config['file_name']     = 'AWAL_'.time().'_'.preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$this->session->userdata('username'));
        
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('foto_kondisi')) {
            $this->session->set_flashdata('error', 'Upload Gagal: ' . $this->upload->display_errors('',''));
            redirect('peminjaman_barang/ajukan/'.$id_aset);
        } else {
            $upload_data = $this->upload->data();
            $nama_peminjam = $this->session->userdata('nama') ?? 'Mahasiswa';
            $nim_nip = $this->session->userdata('username') ?? '123456';
            $id_user = $this->session->userdata('id_user');

            $id_peminjam = $this->Peminjaman_barang_model->get_or_create_peminjam(
                $nim_nip,
                $nama_peminjam,
                $prodi,
                $jenis_peminjam
            );

            $group_id = uniqid('PJM_');

            $raw_jenis_peminjaman = strtolower(trim((string)$this->input->post('jenis_peminjaman', true)));
            $jenis_peminjaman = ($raw_jenis_peminjaman === 'luar_kampus' || $raw_jenis_peminjaman === 'external') ? 'luar_kampus' : 'dalam_kampus';

            $data_peminjaman = [
                'group_id' => $group_id,
                'id_aset' => $id_aset,
                'id_peminjam' => $id_peminjam,
                'id_user' => $id_user,
                'nama_peminjam' => $nama_peminjam,
                'nim_nip' => $nim_nip,
                'prodi' => $prodi,
                'jumlah_pinjam' => $jumlah_pinjam,
                'tanggal_pinjam' => $tanggal_pinjam,
                'tanggal_kembali_rencana' => $tanggal_kembali,
                'keperluan' => $this->input->post('keperluan'),
                'jenis_peminjaman' => $jenis_peminjaman,
                'kondisi_saat_pinjam' => $this->input->post('kondisi_saat_pinjam') ?: 'Baik',
                'foto_bukti' => $upload_data['file_name'],
                'status' => 'Menunggu ACC Kaprodi',
                'status_kaprodi' => 'Pending',
                'status_laboran' => 'Pending',
                'status_kaur' => 'Pending',
                'status_wadek1' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $id_peminjaman = $this->Peminjaman_barang_model->create_with_stock_reservation($data_peminjaman);
            if (!$id_peminjaman) {
                if (!empty($upload_data['full_path']) && is_file($upload_data['full_path'])) {
                    @unlink($upload_data['full_path']);
                }
                $this->session->set_flashdata('error', 'Stok baru saja dialokasikan oleh pengajuan lain atau sudah tidak mencukupi.');
                redirect('peminjaman_barang/ajukan/'.$id_aset);
            }
            
            $this->session->set_flashdata('success', 'Berhasil! Pengajuan peminjaman terkirim dan stok sudah direservasi.');
            redirect('peminjaman_barang/riwayat'); 
        }
    }

    /**
     * Menampilkan Halaman Riwayat Peminjaman User
     * URL: http://localhost/ifik/peminjaman_barang/riwayat
     */
    public function riwayat() {
        $nim_nip = (string) $this->session->userdata('username');
        $id_user = $this->session->userdata('id_user');
        $peminjam = $this->Peminjaman_barang_model->get_peminjam_by_nim_nip($nim_nip);
        if (!$peminjam && $nim_nip) {
            $nama = $this->session->userdata('nama') ?? 'Mahasiswa';
            $id_peminjam_new = $this->Peminjaman_barang_model->get_or_create_peminjam(
                $nim_nip,
                $nama,
                'S1 Desain Komunikasi Visual (DKV)',
                'Mahasiswa'
            );
            $peminjam = $this->Peminjaman_barang_model->get_peminjam_by_id($id_peminjam_new);
        }

        $filter_fields = (array) $this->input->get('filter_field', true);
        $filter_values = (array) $this->input->get('filter_value', true);

        $allowed_filter_fields = ['all', 'barang', 'kode', 'status', 'tanggal'];
        $filter_rows = [];

        $search_q = trim((string) $this->input->get('q', true));
        $get_cat = trim((string) $this->input->get('cat', true));
        $search_cat = in_array($get_cat, $allowed_filter_fields, true) ? $get_cat : '';

        if (!empty($filter_fields)) {
            foreach ($filter_fields as $index => $field) {
                if (count($filter_rows) >= 4) break;
                $field = (string) $field;
                if (!in_array($field, $allowed_filter_fields, true)) continue;
                $val = trim((string) ($filter_values[$index] ?? ''));
                $filter_rows[] = [
                    'field' => $field,
                    'value' => $val,
                ];
            }
        }

        if (empty($filter_rows) && $search_q !== '') {
            $filter_rows[] = [
                'field' => $search_cat ?: 'all',
                'value' => $search_q,
            ];
        }

        if (empty($filter_rows)) {
            $filter_rows = [['field' => $search_cat ?: 'all', 'value' => $search_q]];
        }

        $criteria = array_values(array_filter(
            $filter_rows,
            static function ($row) {
                return isset($row['value']) && $row['value'] !== '';
            }
        ));

        $requested_sort = (string) $this->input->get('sort_by', true);
        $allowed_sort = ['tanggal', 'barang', 'masa', 'status', 'qr'];
        $history_sort = in_array($requested_sort, $allowed_sort, true) ? $requested_sort : '';
        $history_dir = strtolower((string) $this->input->get('sort_dir', true)) === 'asc' ? 'asc' : 'desc';

        $filters = [
            'criteria' => $criteria,
            'sort_by' => $history_sort,
            'sort_dir' => $history_dir,
        ];

        $per_page = $this->read_per_page($this->input->get('per_page', true), 10);
        $requested_page = $this->input->get('page', true);
        $page = is_scalar($requested_page) ? max(1, (int) $requested_page) : 1;

        $total = 0;
        $total_pages = 1;
        $offset = 0;
        $data['riwayat'] = [];

        $idPeminjamVal = $peminjam ? $peminjam->id_peminjam : 0;
        if ($idPeminjamVal || $id_user) {
            $total = (int) $this->Peminjaman_barang_model->count_peminjaman_by_peminjam(
                $idPeminjamVal,
                $filters
            );
            $total_pages = max(1, (int) ceil($total / $per_page));
            $page = min($page, $total_pages);
            $offset = ($page - 1) * $per_page;
            $data['riwayat'] = $this->Peminjaman_barang_model->get_peminjaman_by_peminjam(
                $idPeminjamVal,
                $filters,
                $per_page,
                $offset
            );
        }

        $data['search'] = $filter_rows[0]['value'] ?? $search_q;
        $data['cat'] = $filter_rows[0]['field'] ?? ($search_cat ?: 'all');
        $data['filter_rows'] = $filter_rows;
        $data['history_sort'] = $history_sort;
        $data['history_dir'] = $history_dir;
        $data['pagination'] = [
            'page' => $page,
            'per_page' => $per_page,
            'total' => $total,
            'total_pages' => $total_pages,
        ];

        $this->attach_notifikasi($data);
        $this->load->view('peminjaman_barang/riwayat', $data);
    }

    /**
     * Endpoint Autocomplete Pencarian Katalog & Riwayat Peminjaman Barang (Mirip Admin LAA)
     */
    public function autocomplete() {
        $term = trim((string) $this->input->get('q', true));
        $cat  = trim((string) $this->input->get('cat', true) ?: 'all');
        $type = trim((string) $this->input->get('type', true) ?: 'katalog');

        if ($type === 'riwayat') {
            $nim_nip = (string) $this->session->userdata('username');
            $id_user = $this->session->userdata('id_user');
            $peminjam = $this->Peminjaman_barang_model->get_peminjam_by_nim_nip($nim_nip);
            $id_peminjam = $peminjam ? $peminjam->id_peminjam : 0;
            $results = $this->Peminjaman_barang_model->autocomplete_riwayat($id_peminjam, $term, $cat, $id_user);
        } else {
            $results = $this->Peminjaman_barang_model->autocomplete_katalog($term, $cat);
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($results ?: []));
    }

    public function detail_barang($id_aset) {
        $data['aset'] = $this->Aset_model->get_aset_by_id($id_aset);
        if (!$data['aset']) {
            show_404();
        }
        $this->load->view('peminjaman_barang/detail_barang', $data);
    }

    /**
     * Halaman Kalender & Tabel Jadwal Peminjaman Barang
     * URL: http://localhost/ifik/peminjaman_barang/kalender
     */
    public function kalender() {
        $data['title'] = 'Kalender & Tabel Peminjaman Barang - IFIK';
        $data['jadwal_peminjaman'] = $this->Peminjaman_barang_model->get_calendar_peminjaman_barang();
        $data['kategori_aset'] = $this->Peminjaman_barang_model->get_kategori_aset_list();
        $data['all_ruangan'] = $this->db->get('ruangan')->result();
        
        $this->attach_notifikasi($data);
        $this->load->view('peminjaman_barang/kalender', $data);
    }

    /**
     * Endpoint API JSON untuk Live / Realtime Sync Data Peminjaman Barang
     */
    public function get_updated_peminjaman() {
        header('Content-Type: application/json');
        $data = $this->Peminjaman_barang_model->get_calendar_peminjaman_barang();
        echo json_encode($data ?: []);
    }
}
