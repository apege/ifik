<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminProdi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper(array('url', 'form', 'html'));
        $this->load->library('session');
        $this->load->model('Perwalian_model');
        $this->load->model('User_model');
        $this->_check_auth();
    }

    /**
     * Autentikasi ketat: Admin Prodi (16), Admin (1), Super Admin (22), Koordinator TA (6), LAA (5)
     */
    private function _check_auth() {
        if (!$this->session->userdata('logged_in')) {
            $isAjax = $this->input->is_ajax_request() || 
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(401)
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Sesi login telah berakhir. Silakan login kembali.']))
                    ->_display();
                exit;
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu Perwalian Program Studi.');
            redirect('login');
            exit;
        }

        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = [1, 5, 6, 16, 22];

        if (!in_array($roleId, $allowedRoles, true)) {
            $isAjax = $this->input->is_ajax_request() || 
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Halaman khusus untuk Admin Program Studi.']))
                    ->_display();
                exit;
            }
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus untuk Program Studi.');
            redirect('dashboard');
            exit;
        }
    }

    /**
     * Halaman Utama: Dashboard & Manajemen Perwalian Prodi
     */
    /**
     * Dashboard monitoring perwalian mahasiswa & data kurikulum prodi
     */
    public function index() {
        $userProdi     = $this->session->userdata('prodi') ?: '';
        $selectedProdi = $this->input->get('prodi', true) ?: 'all';
        $tahun_ajaran  = $this->input->get('tahun', true) ?: '2024/2025';
        $semester      = $this->input->get('semester', true) ?: 'Ganjil';
        $activeTab     = $this->input->get('tab', true) ?: 'matakuliah';

        $data['title']          = 'Manajemen Perwalian & Data Akademik Prodi';
        $data['active_tab']     = $activeTab;
        $data['tahun_ajaran']   = $tahun_ajaran;
        $data['semester']       = $semester;
        $data['user_prodi']     = $userProdi;
        $data['selected_prodi'] = $selectedProdi;

        // Ambil daftar master prodi untuk filter dropdown
        $prodiList = [];
        if ($this->db->table_exists('master_prodi')) {
            $prodiList = $this->db->select('nama_prodi, kode_prodi')->get('master_prodi')->result_array();
        }
        if (empty($prodiList)) {
            $prodiList = [
                ['nama_prodi' => 'Desain Interior (DI)', 'kode_prodi' => 'DI'],
                ['nama_prodi' => 'S1 Informatika', 'kode_prodi' => 'IF'],
                ['nama_prodi' => 'Informatika', 'kode_prodi' => 'IF'],
                ['nama_prodi' => 'Desain Komunikasi Visual (DKV)', 'kode_prodi' => 'DKV'],
                ['nama_prodi' => 'Desain Produk (DP)', 'kode_prodi' => 'DP'],
                ['nama_prodi' => 'Kriya Seni & Mode', 'kode_prodi' => 'KRIYA'],
            ];
        }
        $data['prodi_list'] = $prodiList;

        // 1. Data Mata Kuliah
        $data['mata_kuliah'] = $this->Perwalian_model->get_mata_kuliah([
            'prodi' => $selectedProdi,
        ]);

        // 2. Data Monitoring Mahasiswa
        $data['monitoring'] = $this->Perwalian_model->get_monitoring_list([
            'tahun_ajaran' => $tahun_ajaran,
            'semester'     => $semester,
            'prodi'        => $selectedProdi,
        ]);

        // 3. Statistik Ringkas Monitoring
        $data['stats'] = $this->Perwalian_model->get_monitoring_stats($tahun_ajaran, $semester);

        // 4. Opsi Dosen Wali
        $data['dosen_wali'] = $this->Perwalian_model->get_dosen_wali_options($selectedProdi);

        // 5. Daftar Mahasiswa
        $this->db->select('id, name, nim, username, email, prodi, dosen_wali')
                 ->from('user')
                 ->where('role_id', 4); // Mahasiswa
        if (!empty($selectedProdi) && $selectedProdi !== 'all') {
            $this->db->group_start()
                ->where('prodi', $selectedProdi)
                ->or_like('prodi', $selectedProdi)
            ->group_end();
        }
        $data['list_mahasiswa'] = $this->db->get()->result_array();

        $this->load->view('admin_prodi/perwalian', $data);
    }

    // =========================================================================
    // AJAX: CRUD & IMPORT MATA KULIAH
    // =========================================================================

    /**
     * Simpan atau update data master mata kuliah
     */
    public function simpan_mata_kuliah() {
        $this->output->set_content_type('application/json');

        $kode_mk   = strtoupper(trim($this->input->post('kode_mk', true)));
        $nama_mk   = trim($this->input->post('nama_mk', true));
        $sks       = (int)$this->input->post('sks', true);
        $semester  = (int)$this->input->post('semester', true);
        $prodi     = trim($this->input->post('prodi', true));
        $jenis     = trim($this->input->post('jenis', true)) ?: 'Wajib';
        $kurikulum = trim($this->input->post('kurikulum', true)) ?: '2024';
        $id        = (int)$this->input->post('id', true);

        if (empty($kode_mk) || empty($nama_mk) || $sks <= 0) {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Harap lengkapi Kode MK, Nama Mata Kuliah, dan SKS dengan benar.'
            ]));
            return;
        }

        $savedId = $this->Perwalian_model->save_mata_kuliah([
            'id'        => $id,
            'kode_mk'   => $kode_mk,
            'nama_mk'   => $nama_mk,
            'sks'       => $sks,
            'semester'  => $semester,
            'prodi'     => $prodi,
            'jenis'     => $jenis,
            'kurikulum' => $kurikulum,
        ]);

        $this->output->set_output(json_encode([
            'status'  => 'success',
            'message' => 'Data mata kuliah berhasil disimpan.',
            'id'      => $savedId,
        ]));
    }

    public function hapus_mata_kuliah() {
        $this->output->set_content_type('application/json');
        $id = (int)$this->input->post('id', true);

        if ($id <= 0) {
            $this->output->set_output(json_encode(['status' => 'error', 'message' => 'ID tidak valid.']));
            return;
        }

        $this->Perwalian_model->delete_mata_kuliah($id);
        $this->output->set_output(json_encode([
            'status'  => 'success',
            'message' => 'Mata kuliah berhasil dihapus.'
        ]));
    }

    /**
     * AJAX: Hapus Banyak Mata Kuliah Sekaligus (Multiple Delete)
     */
    public function hapus_mata_kuliah_multiple() {
        $this->output->set_content_type('application/json');
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);
        $ids = $json['ids'] ?? $this->input->post('ids');

        if (is_string($ids)) {
            $decoded = json_decode($ids, true);
            $ids = is_array($decoded) ? $decoded : explode(',', $ids);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Tidak ada mata kuliah yang dipilih.']));
            return;
        }

        $affected = $this->Perwalian_model->delete_mata_kuliah_multiple($ids);
        $this->output->set_output(json_encode([
            'status'  => 'success',
            'message' => count($ids) . ' mata kuliah berhasil dihapus.'
        ]));
    }

    /**
     * AJAX: Import Bulk Mata Kuliah dari Excel JSON
     */
    /**
     * Impor kurikulum mata kuliah dari file Excel
     */
    public function import_mata_kuliah() {
        @set_time_limit(300);
        $this->output->set_content_type('application/json');

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        if (empty($json['courses']) || !is_array($json['courses'])) {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Data mata kuliah kosong atau format tidak sesuai.'
            ]));
            return;
        }

        $allowUpdate = !empty($json['allow_update']);
        $res = $this->Perwalian_model->upsert_mata_kuliah_bulk($json['courses'], ['allow_update' => $allowUpdate]);

        $msgParts = [];
        if ($res['inserted'] > 0) $msgParts[] = "{$res['inserted']} data baru berhasil disimpan";
        if ($res['updated'] > 0) $msgParts[] = "{$res['updated']} data berhasil diperbarui";
        if ($res['skipped_duplicate'] > 0) $msgParts[] = "{$res['skipped_duplicate']} data duplikat dilewati";
        if ($res['skipped_invalid'] > 0) $msgParts[] = "{$res['skipped_invalid']} data salah field/format dilewati";

        $message = !empty($msgParts) ? implode(', ', $msgParts) . '.' : 'Tidak ada data valid yang diproses.';
        $status = ($res['inserted'] > 0 || $res['updated'] > 0) ? 'success' : ($res['skipped_invalid'] > 0 ? 'warning' : 'info');

        $this->output->set_output(json_encode([
            'status'            => $status,
            'message'           => $message,
            'inserted'          => $res['inserted'],
            'updated'           => $res['updated'],
            'skipped_duplicate' => $res['skipped_duplicate'],
            'skipped_invalid'   => $res['skipped_invalid'],
            'errors'            => $res['errors'],
        ]));
    }

    // =========================================================================
    // AJAX: ASSIGN DOSEN WALI & IMPORT MAHASISWA
    // =========================================================================

    /**
     * Plotting dosen wali ke mahasiswa terpilih
     */
    public function update_dosen_wali() {
        $this->output->set_content_type('application/json');
        $nim      = trim($this->input->post('nim', true));
        $nip_wali = trim($this->input->post('nip_dosen_wali', true));

        if (empty($nim)) {
            $this->output->set_output(json_encode(['status' => 'error', 'message' => 'NIM mahasiswa tidak ditemukan.']));
            return;
        }

        $this->Perwalian_model->assign_dosen_wali($nim, $nip_wali);
        $this->output->set_output(json_encode([
            'status'  => 'success',
            'message' => 'Dosen wali berhasil diperbarui.'
        ]));
    }

    // =========================================================================
    // AJAX: IMPORT REKAP KRS KOLEKTIF DARI SIAKAD / I-GRACIAS
    // =========================================================================

    /**
     * Impor kolektif kontrak KRS mahasiswa dari Excel
     */
    public function import_krs_kolektif() {
        @set_time_limit(300);
        $this->output->set_content_type('application/json');

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        if (empty($json['krs_rows']) || !is_array($json['krs_rows'])) {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Data KRS kosong atau format tidak sesuai.'
            ]));
            return;
        }

        $tahun    = trim($json['tahun_ajaran'] ?? '2024/2025');
        $semester = trim($json['semester'] ?? 'Ganjil');

        $res = $this->Perwalian_model->upsert_krs_bulk($json['krs_rows'], $tahun, $semester);

        $msgParts = [];
        if ($res['courses_added'] > 0) $msgParts[] = "{$res['courses_added']} mata kuliah berhasil dimasukkan";
        if ($res['new_perwalian'] > 0) $msgParts[] = "{$res['new_perwalian']} berkas perwalian baru dibuat";
        if ($res['skipped_duplicate'] > 0) $msgParts[] = "{$res['skipped_duplicate']} data duplikat dilewati";
        if ($res['skipped_invalid'] > 0) $msgParts[] = "{$res['skipped_invalid']} baris tidak valid dilewati";

        $message = !empty($msgParts) ? implode(', ', $msgParts) . '.' : 'Tidak ada data valid yang diproses.';
        $status = ($res['courses_added'] > 0) ? 'success' : ($res['skipped_invalid'] > 0 ? 'warning' : 'info');

        $this->output->set_output(json_encode([
            'status'            => $status,
            'message'           => $message,
            'processed'         => $res['processed'],
            'new_perwalian'     => $res['new_perwalian'],
            'courses_added'     => $res['courses_added'],
            'skipped_duplicate' => $res['skipped_duplicate'],
            'skipped_invalid'   => $res['skipped_invalid'],
            'errors'            => $res['errors'],
        ]));
    }

    // =========================================================================
    // AJAX: RESET / BUKA KUNCI PERWALIAN MAHASISWA
    // =========================================================================

    /**
     * Reset status perwalian mahasiswa ke kondisi awal (Draft)
     */
    public function reset_perwalian() {
        $this->output->set_content_type('application/json');
        $id_perwalian = (int)$this->input->post('id_perwalian', true);
        $action       = trim($this->input->post('action', true)) ?: 'unlock';
        $catatan      = trim($this->input->post('catatan', true));

        if ($id_perwalian <= 0) {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => 'ID perwalian tidak valid.'
            ]));
            return;
        }

        $ok = $this->Perwalian_model->reset_perwalian($id_perwalian, $action, $catatan);
        if ($ok) {
            $msg = ($action === 'total')
                ? 'Berkas KRS berhasil direset total. Mahasiswa dapat menginput ulang dari awal.'
                : 'Kunci KRS berhasil dibuka (Status kembali ke Draft/Revisi). Mahasiswa sekarang dapat mengedit kembali mata kuliahnya.';
            $this->output->set_output(json_encode([
                'status'  => 'success',
                'message' => $msg
            ]));
        } else {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Gagal mereset status perwalian mahasiswa.'
            ]));
        }
    }
}
