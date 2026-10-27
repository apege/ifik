<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KelompokKeahlian extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper(array('url', 'form', 'html', 'text'));
        $this->load->library('session');
        $this->load->model('KelompokKeahlian_model');
        $this->_check_auth();
    }

    /**
     * Autentikasi ketat: Super Admin (22), Admin (1), Ka. Ur (2), LAA (5), Koor TA (6)
     */
    private function _check_auth() {
        if (!$this->session->userdata('logged_in')) {
            $isAjax = $this->input->is_ajax_request() || 
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(401)
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'Sesi login telah berakhir. Silakan login kembali.'
                    ]))
                    ->_display();
                exit;
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu Pengaturan Kelompok Keahlian.');
            redirect('login');
            exit;
        }

        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = [1, 2, 5, 6, 22]; // Super Admin & Master Admins

        if (!in_array($roleId, $allowedRoles)) {
            $isAjax = $this->input->is_ajax_request() || 
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengelola Kelompok Keahlian.'
                    ]))
                    ->_display();
                exit;
            }
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini hanya dapat diakses oleh Administrator & Super Admin.');
            redirect('dashboard');
            exit;
        }

        return true;
    }

    /**
     * Halaman Utama Kelola Dinamis Kelompok Keahlian (KK)
     */
    /**
     * [MODUL DINAMIS KK] Menampilkan daftar seluruh Kelompok Keahlian (KK) / Laboratorium Riset fakultas.
     * Mengambil statistik jumlah dosen anggota dan status keaktifan KK.
     * 
     * @return void Me-render view admin/kelompok_keahlian
     * @affected_table kelompok_keahlian, user
     */
    public function index() {
        $search = trim($this->input->get('q', true) ?? '');
        $status = trim($this->input->get('status', true) ?? 'all');

        $data['title']            = 'Pengaturan Dinamis Kelompok Keahlian (KK)';
        $data['search']           = $search;
        $data['status_filter']    = $status;
        $data['stats']            = $this->KelompokKeahlian_model->get_stats();
        $data['kk_list']          = $this->KelompokKeahlian_model->get_all($search, $status);
        $data['dosen_candidates'] = $this->KelompokKeahlian_model->get_dosen_candidates();

        $this->load->view('admin/kelompok_keahlian', $data);
    }

    /**
     * Simpan / Tambah / Update Kelompok Keahlian
     */
    /**
     * [MODUL DINAMIS KK] Menambah atau memperbarui master data Kelompok Keahlian (KK).
     * Memvalidasi keunikan kode KK dan menugaskan Ketua KK.
     * 
     * @return void Redirect ke kelompokkeahlian
     * @affected_table kelompok_keahlian
     */
    public function simpan() {
        $id          = $this->input->post('id', true);
        $kode_kk     = strtoupper(trim($this->input->post('kode_kk', true) ?? ''));
        $nama_kk     = trim($this->input->post('nama_kk', true) ?? '');
        $deskripsi   = trim($this->input->post('deskripsi', true) ?? '');
        $ketua_kk    = trim($this->input->post('ketua_kk', true) ?? '');
        $nip_ketua   = trim($this->input->post('nip_ketua', true) ?? '');
        $is_active   = $this->input->post('is_active') ? 1 : 0;
        $urutan      = (int)($this->input->post('urutan') ?: 1);

        $isAjax = $this->input->is_ajax_request() || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // Validasi input
        if (empty($kode_kk)) {
            return $this->_handle_response($isAjax, false, 'Kode KK wajib diisi (contoh: DKV, DMI, CPI).');
        }

        if (empty($nama_kk)) {
            return $this->_handle_response($isAjax, false, 'Nama Kelompok Keahlian wajib diisi.');
        }

        // Cek duplikasi kode_kk
        $existing = $this->KelompokKeahlian_model->get_by_kode($kode_kk);
        if ($existing && (empty($id) || (int)$existing['id'] !== (int)$id)) {
            return $this->_handle_response($isAjax, false, "Kode KK '{$kode_kk}' sudah digunakan oleh Kelompok Keahlian '{$existing['nama_kk']}'. Harap gunakan kode unik lain.");
        }

        $payload = [
            'kode_kk'   => $kode_kk,
            'nama_kk'   => $nama_kk,
            'deskripsi' => $deskripsi,
            'ketua_kk'  => $ketua_kk,
            'nip_ketua' => $nip_ketua,
            'is_active' => $is_active,
            'urutan'    => max(1, $urutan)
        ];

        $save_result = $this->KelompokKeahlian_model->save($payload, $id ?: null);

        if ($save_result) {
            $msg = !empty($id) ? "Kelompok Keahlian [{$kode_kk}] berhasil diperbarui." : "Kelompok Keahlian baru [{$kode_kk}] berhasil ditambahkan.";
            return $this->_handle_response($isAjax, true, $msg);
        } else {
            return $this->_handle_response($isAjax, false, 'Gagal menyimpan data Kelompok Keahlian ke database.');
        }
    }

    /**
     * Toggle status aktif / non-aktif KK
     */
    /**
     * [MODUL DINAMIS KK] Mengubah status keaktifan (aktif/nonaktif) KK secara cepat.
     * 
     * @param  int $id ID Kelompok Keahlian
     * @return void Redirect dengan flashdata success
     * @affected_table kelompok_keahlian
     */
    public function toggle_status($id = null) {
        if (empty($id)) {
            $id = $this->input->post('id', true);
        }

        $isAjax = $this->input->is_ajax_request() || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (empty($id)) {
            return $this->_handle_response($isAjax, false, 'ID Kelompok Keahlian tidak valid.');
        }

        $result = $this->KelompokKeahlian_model->toggle_status($id);
        if ($result) {
            $kk = $this->KelompokKeahlian_model->get_by_id($id);
            $statusText = ($kk['is_active'] == 1) ? 'diaktifkan' : 'dinonaktifkan';
            return $this->_handle_response($isAjax, true, "Status KK [{$kk['kode_kk']}] berhasil {$statusText}.", ['is_active' => $kk['is_active']]);
        } else {
            return $this->_handle_response($isAjax, false, 'Gagal memperbarui status Kelompok Keahlian.');
        }
    }

    /**
     * Hapus Kelompok Keahlian
     */
    /**
     * [MODUL DINAMIS KK] Menghapus data Kelompok Keahlian jika belum memiliki riwayat dosen/mahasiswa terkait.
     * 
     * @param  int $id ID Kelompok Keahlian
     * @return void
     * @affected_table kelompok_keahlian
     */
    public function hapus($id = null) {
        if (empty($id)) {
            $id = $this->input->post('id', true);
        }

        $isAjax = $this->input->is_ajax_request() || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (empty($id)) {
            return $this->_handle_response($isAjax, false, 'ID Kelompok Keahlian tidak valid.');
        }

        $res = $this->KelompokKeahlian_model->delete($id);

        return $this->_handle_response($isAjax, $res['status'], $res['message']);
    }

    /**
     * AJAX endpoint: Ambil detail KK untuk modal edit
     */
    public function get_json($id) {
        $kk = $this->KelompokKeahlian_model->get_by_id($id);
        if (!$kk) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['status' => false, 'message' => 'Data tidak ditemukan']));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'data' => $kk]));
    }

    /**
     * AJAX endpoint: Ambil data anggota (Dosen & Mahasiswa TA) per KK
     */
    public function members($id) {
        $data = $this->KelompokKeahlian_model->get_members_by_kk($id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'data' => $data]));
    }

    /**
     * Helper response fleksibel (AJAX JSON atau HTTP Flashdata + Redirect)
     */
    private function _handle_response($isAjax, $success, $message, $extra = []) {
        if ($isAjax) {
            $status_code = $success ? 200 : 400;
            $payload = array_merge([
                'status'  => $success ? 'success' : 'error',
                'message' => $message
            ], $extra);

            $this->output
                ->set_content_type('application/json')
                ->set_status_header($status_code)
                ->set_output(json_encode($payload));
            return;
        }

        if ($success) {
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', $message);
        }
        redirect('superadmin/kelompok_keahlian');
    }
}
