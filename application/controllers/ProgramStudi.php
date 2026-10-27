<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProgramStudi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper(array('url', 'form', 'html', 'text', 'fik_prodi'));
        $this->load->library('session');
        $this->load->model('ProgramStudi_model');
        $this->_check_auth();
    }

    /**
     * Autentikasi ketat: Super Admin (22) dan Master Admins (1, 2, 5, 6)
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
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu Pengaturan Program Studi.');
            redirect('login');
            exit;
        }

        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = [1, 2, 5, 6, 22];

        if (!in_array($roleId, $allowedRoles)) {
            $isAjax = $this->input->is_ajax_request() || 
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status'  => 'error',
                        'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengelola Program Studi.'
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
     * Halaman Utama Pengaturan Dinamis Program Studi
     */
    /**
     * [MODUL DINAMIS PRODI] Menampilkan master data Program Studi di Fakultas Industri Kreatif.
     * 
     * @return void Me-render view admin/program_studi
     * @affected_table program_studi, user
     */
    public function index() {
        $search = trim($this->input->get('q', true) ?? '');
        $status = trim($this->input->get('status', true) ?? 'all');

        $data['title']         = 'Pengaturan Dinamis Program Studi (Prodi)';
        $data['search']        = $search;
        $data['status_filter'] = $status;
        $data['stats']         = $this->ProgramStudi_model->get_stats();
        $data['prodi_list']    = $this->ProgramStudi_model->get_all($search, $status);

        $this->load->view('admin/program_studi', $data);
    }

    /**
     * Simpan / Tambah / Update Program Studi
     */
    /**
     * [MODUL DINAMIS PRODI] Menyimpan entri baru atau update Program Studi beserta jenjang (S1/D3/S2) dan Kaprodi.
     * 
     * @return void
     * @affected_table program_studi
     */
    public function simpan() {
        $id          = $this->input->post('id', true);
        $kode_prodi  = strtoupper(trim($this->input->post('kode_prodi', true) ?? ''));
        $nama_prodi  = trim($this->input->post('nama_prodi', true) ?? '');
        $jenjang     = trim($this->input->post('jenjang', true) ?? 'S1');
        $keterangan  = trim($this->input->post('keterangan', true) ?? '');
        $is_active   = $this->input->post('is_active') ? 1 : 0;
        $urutan      = (int)($this->input->post('urutan') ?: 1);

        $isAjax = $this->input->is_ajax_request() || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (empty($kode_prodi)) {
            return $this->_handle_response($isAjax, false, 'Kode Program Studi wajib diisi (contoh: DKV, DI, IF).');
        }
        if (empty($nama_prodi)) {
            return $this->_handle_response($isAjax, false, 'Nama Program Studi lengkap wajib diisi.');
        }

        // Cek duplikasi kode_prodi
        $this->db->where('kode_prodi', $kode_prodi);
        if (!empty($id)) {
            $this->db->where('id !=', (int)$id);
        }
        if ($this->db->count_all_results('master_prodi') > 0) {
            return $this->_handle_response($isAjax, false, "Kode Program Studi [{$kode_prodi}] sudah digunakan oleh prodi lain.");
        }

        $payload = [
            'kode_prodi' => $kode_prodi,
            'nama_prodi' => $nama_prodi,
            'jenjang'    => $jenjang,
            'keterangan' => $keterangan,
            'is_active'  => $is_active,
            'urutan'     => max(1, $urutan)
        ];

        $savedId = $this->ProgramStudi_model->save($id ?: null, $payload);
        $actionText = !empty($id) ? "diperbarui" : "ditambahkan";
        $msg = "Program Studi [{$nama_prodi}] berhasil {$actionText}.";

        return $this->_handle_response($isAjax, true, $msg, ['id' => $savedId]);
    }

    /**
     * Toggle status aktif / non-aktif
     */
    /**
     * [MODUL DINAMIS PRODI] Mengaktifkan atau menonaktifkan visibilitas Program Studi pada sistem.
     * 
     * @param  int $id ID Program Studi
     * @return void
     * @affected_table program_studi
     */
    public function toggle_status($id) {
        $id = (int)$id;
        $new_status = $this->ProgramStudi_model->toggle_status($id);

        $isAjax = $this->input->is_ajax_request() || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($new_status === false) {
            return $this->_handle_response($isAjax, false, 'Data Program Studi tidak ditemukan.');
        }

        $stText = ($new_status == 1) ? 'diaktifkan' : 'dinonaktifkan';
        $msg = "Status Program Studi berhasil {$stText}.";

        return $this->_handle_response($isAjax, true, $msg, ['is_active' => $new_status]);
    }

    /**
     * Hapus Program Studi
     */
    /**
     * [MODUL DINAMIS PRODI] Menghapus master data program studi dari database.
     * 
     * @param  int $id ID Program Studi
     * @return void
     * @affected_table program_studi
     */
    public function hapus($id) {
        $id = (int)$id;
        $row = $this->ProgramStudi_model->get_by_id($id);

        $isAjax = $this->input->is_ajax_request() || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (!$row) {
            return $this->_handle_response($isAjax, false, 'Program Studi tidak ditemukan.');
        }

        // Cek apakah ada mahasiswa atau user yang terkait
        $mhs_count = 0;
        if ($this->db->table_exists('mahasiswa')) {
            $this->db->group_start();
            $this->db->where('prodi', $row['nama_prodi']);
            $this->db->or_like('prodi', $row['kode_prodi']);
            $this->db->group_end();
            $mhs_count = $this->db->count_all_results('mahasiswa');
        }

        if ($mhs_count > 0) {
            return $this->_handle_response($isAjax, false, "Tidak dapat menghapus! Terdapat {$mhs_count} data mahasiswa yang terdaftar pada prodi ini. Anda dapat menonaktifkan statusnya saja.");
        }

        $this->ProgramStudi_model->delete($id);
        $msg = "Program Studi [{$row['nama_prodi']}] berhasil dihapus.";

        return $this->_handle_response($isAjax, true, $msg);
    }

    /**
     * Ambil 1 record prodi dalam format JSON untuk populate form modal edit
     */
    public function get_json($id) {
        $row = $this->ProgramStudi_model->get_by_id((int)$id);
        if (!$row) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['status' => false, 'message' => 'Data tidak ditemukan']));
        }

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'data' => $row]));
    }

    /**
     * Helper response AJAX vs Redirect Flashdata
     */
    private function _handle_response($isAjax, $success, $message, $extra = []) {
        if ($isAjax) {
            $payload = array_merge([
                'status'  => $success ? 'success' : 'error',
                'success' => $success,
                'message' => $message
            ], $extra);

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($payload));
        }

        $this->session->set_flashdata($success ? 'success' : 'error', $message);
        redirect('superadmin/program_studi');
    }
}
