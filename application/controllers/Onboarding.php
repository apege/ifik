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

        $data['title'] = 'Aktivasi Akun & Lengkapi Biodata — IK Labs Portal';
        $data['user'] = $user;
        $data['nim'] = !empty($user->nidn_nim) ? $user->nidn_nim : '';

        // Split existing name into nama_depan and nama_belakang if available
        $nameParts = explode(' ', trim($user->name), 2);
        $data['nama_depan'] = isset($nameParts[0]) ? $nameParts[0] : '';
        $data['nama_belakang'] = isset($nameParts[1]) ? $nameParts[1] : '';

        // Load Dosen Wali list dynamically from database
        $dosenList = [];
        
        // 1. From dosen_wali table
        if ($this->db->table_exists('dosen_wali')) {
            $queryDW = $this->db->get('dosen_wali')->result_array();
            foreach ($queryDW as $dw) {
                if (!empty($dw['nip']) && !empty($dw['nama_dosen'])) {
                    $dosenList[$dw['nip']] = $dw['nama_dosen'];
                }
            }
        }

        // 2. From users table with role_id = 4 (Dosen)
        $queryDosenUsers = $this->db->get_where('users', ['role_id' => 4, 'status' => 'active'])->result_array();
        foreach ($queryDosenUsers as $du) {
            $nipKey = !empty($du['nidn_nim']) ? $du['nidn_nim'] : 'NIP-' . $du['id'];
            if (!isset($dosenList[$nipKey])) {
                $dosenList[$nipKey] = $du['name'];
            }
        }

        // Fallback default list if database is empty
        if (empty($dosenList)) {
            $dosenList = [
                '19850101' => 'Dr. Ir. Ahmad Yani, M.T.',
                '19880205' => 'Prof. Siti Aminah, Ph.D.',
                '19900312' => 'Hendra Kusuma, S.T., M.T.',
                '19920720' => 'Dra. Nurul Hidayah, M.Ds.',
                '19941108' => 'Rian Pratama, S.Kom., M.T.',
                '19960415' => 'Maya Indriani, S.Ds., M.A.'
            ];
        }

        $data['dosen_wali_list'] = $dosenList;

        $data['konsentrasi_list'] = array(
            'Desain Komunikasi Visual',
            'Informatika (Teknologi Informasi)',
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
        // Must be logged in
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $userId = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($userId);

        if (!$user) {
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
            $this->session->set_flashdata('error', 'Password baru wajib diisi dan minimal 6 karakter!');
            redirect('onboarding');
            return;
        }

        if ($passwordBaru !== $konfirmasiPassword) {
            $this->session->set_flashdata('error', 'Konfirmasi password tidak cocok dengan password baru!');
            redirect('onboarding');
            return;
        }

        // 2. Strict Alphabetical Validation for Names (Only letters and spaces)
        $cleanDepan = trim(preg_replace('/[^a-zA-Z\s]/', '', $namaDepanRaw));
        $cleanBelakang = trim(preg_replace('/[^a-zA-Z\s]/', '', $namaBelakangRaw));

        if (empty($cleanDepan)) {
            $this->session->set_flashdata('error', 'Nama depan wajib diisi dengan huruf tanpa simbol atau angka!');
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
                'nip_dosen_wali'  => $dosenWali,
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

        // 7. Redirect to appropriate dashboard based on role
        if ($user->role_id == 5) {
            redirect('dashboard'); // or redirect('mahasiswa');
        } else {
            redirect('dashboard');
        }
    }
}
