<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Onboarding extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper(array('url', 'form', 'html', 'security'));
        $this->load->library(array('session', 'form_validation'));
        $this->load->model('User_model');
    }

    /**
     * Display the 2-step onboarding page (Change Password & Biodata)
     */
    public function index()
    {
        // Must be logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        // If user already changed password, no need to onboard again
        if ((int)$this->session->userdata('password_changed') === 1) {
            redirect('dashboard');
            return;
        }

        $userId = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($userId);

        if (!$user) {
            $this->session->sess_destroy();
            redirect('login');
            return;
        }

        $isDosen = ((int)$user->role_id === 4);
        $data['title'] = 'Aktivasi Akun & Lengkapi Biodata — IK Labs Portal';
        $data['user'] = $user;
        $data['role_id'] = (int)$user->role_id;
        $data['is_dosen'] = $isDosen;
        $data['role_name'] = $isDosen ? 'dosen' : 'mahasiswa';
        $data['nim'] = !empty($user->nidn_nim) ? $user->nidn_nim : '';

        // Split existing name into nama_depan and nama_belakang if available
        $nameParts = explode(' ', trim($user->name), 2);
        $data['nama_depan'] = isset($nameParts[0]) ? $nameParts[0] : '';
        $data['nama_belakang'] = isset($nameParts[1]) ? $nameParts[1] : '';

        // Load Dosen Wali list directly from MySQL database `dosen_wali` table
        $dosenList = [];
        if ($this->db->table_exists('dosen_wali')) {
            $this->db->order_by('jurusan', 'ASC');
            $this->db->order_by('nama_dosen', 'ASC');
            $queryDW = $this->db->get('dosen_wali')->result_array();
            foreach ($queryDW as $dw) {
                if (!empty($dw['nip']) && !empty($dw['nama_dosen'])) {
                    $dosenList[] = [
                        'nip'     => $dw['nip'],
                        'nama'    => $dw['nama_dosen'],
                        'jurusan' => !empty($dw['jurusan']) ? $dw['jurusan'] : 'Fakultas Industri Kreatif',
                        'email'   => !empty($dw['email']) ? $dw['email'] : ''
                    ];
                }
            }
        }

        $data['dosen_wali_list'] = $dosenList;

        $data['konsentrasi_list'] = array(
            'Desain Komunikasi Visual',
            'Informatika',
            'Rekayasa Perangkat Lunak',
            'Desain Produk',
            'Desain Interior',
            'Kriya Tekstil & Fashion'
        );

        $this->load->view('auth/onboarding', $data);
    }

    /**
     * Process Onboarding Submission (Password Change + Biodata)
     */
    public function process_biodata()
    {
        $isAjax = $this->input->is_ajax_request() || 
                  $this->input->get_request_header('X-Requested-With') === 'XMLHttpRequest' || 
                  $this->input->post('is_ajax') ||
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // Must be logged in
        if (!$this->session->userdata('logged_in')) {
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(401)
                    ->set_output(json_encode([
                        'status'   => 'error',
                        'message'  => 'Sesi login telah berakhir. Silakan login kembali.',
                        'redirect' => base_url('login')
                    ]));
                return;
            }
            redirect('login');
            return;
        }

        $userId = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($userId);

        if (!$user) {
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode([
                        'status'   => 'error',
                        'message'  => 'Akun pengguna tidak ditemukan.',
                        'redirect' => base_url('login')
                    ]));
                return;
            }
            redirect('login');
            return;
        }

        // Get Input Data
        $passwordBaru       = $this->input->post('password_baru');
        $konfirmasiPassword = $this->input->post('konfirmasi_password');
        $nim                = trim($this->input->post('nim', true));
        $namaDepanRaw       = $this->input->post('nama_depan', true);
        $namaBelakangRaw    = $this->input->post('nama_belakang', true);
        $tempatLahir        = trim($this->input->post('tempat_lahir', true));
        $tanggalLahir       = trim($this->input->post('tanggal_lahir', true));
        $alamat             = trim($this->input->post('alamat', true));
        $konsentrasi        = trim($this->input->post('konsentrasi', true));
        $dosenWali          = trim($this->input->post('dosen_wali', true));

        // 1. Validate Password
        if (empty($passwordBaru) || strlen($passwordBaru) < 6) {
            $msg = 'Password baru wajib diisi dan minimal 6 karakter!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        if ($passwordBaru !== $konfirmasiPassword) {
            $msg = 'Konfirmasi password tidak cocok dengan password baru!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        // 2. Strict Alphabetical Validation for Names (Only letters and spaces)
        $cleanDepan = trim(preg_replace('/[^a-zA-Z\s]/', '', $namaDepanRaw));
        $cleanBelakang = trim(preg_replace('/[^a-zA-Z\s]/', '', $namaBelakangRaw));

        if (empty($cleanDepan)) {
            $msg = 'Nama depan wajib diisi dengan huruf tanpa simbol atau angka!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        $fullName = trim($cleanDepan . ' ' . $cleanBelakang);

        // 3. Hash New Password
        $hashedPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);

        // 4. Update users Table
        $userUpdate = [
            'name'             => $fullName,
            'password'         => $hashedPassword,
            'password_changed' => 1,
            'token'            => null,
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        if (!empty($nim)) {
            $userUpdate['nidn_nim'] = $nim;
        }

        $this->User_model->update($userId, $userUpdate);

        // 5. Update / Insert to mahasiswa Table (if applicable)
        if ($this->db->table_exists('mahasiswa') && !empty($nim)) {
            $mhsData = [
                'nim'             => $nim,
                'nip_dosen_wali'  => !empty($dosenWali) ? $dosenWali : null,
                'nama_depan'      => $cleanDepan,
                'nama_belakang'   => $cleanBelakang,
                'alamat'          => $alamat,
                'kota'            => $tempatLahir,
                'konsentrasi_dkv' => $konsentrasi
            ];

            $existingMhs = $this->db->get_where('mahasiswa', ['nim' => $nim])->row();
            if ($existingMhs) {
                $this->db->where('nim', $nim);
                $this->db->update('mahasiswa', $mhsData);
            } else {
                $this->db->insert('mahasiswa', $mhsData);
            }
        }

        // 6. Update Session Data
        $this->session->set_userdata([
            'name'             => $fullName,
            'nidn_nim'         => $nim,
            'nim'              => $nim,
            'password_changed' => 1
        ]);

        $this->session->set_flashdata('success', 'Aktivasi akun berhasil! Password Anda telah diperbarui dan profil telah tersimpan.');

        if ($isAjax) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'   => 'success',
                    'message'  => 'Aktivasi akun berhasil! Password Anda telah diperbarui dan profil telah tersimpan.',
                    'redirect' => base_url('dashboard')
                ]));
            return;
        }

        // 7. Redirect to dashboard
        redirect('dashboard');
    }
}
