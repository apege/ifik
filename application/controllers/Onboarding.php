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
     * Endpoint aktivasi dari tautan email
     */
    public function activate()
    {
        $email = strtolower(trim((string)$this->input->get('email', true)));
        $token = trim((string)$this->input->get('token', true));

        if (empty($email) || empty($token)) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning" role="alert" style="margin-top:24px;">Tautan aktivasi tidak valid atau parameter tidak lengkap.</div>');
            redirect('auth');
            return;
        }

        $user = $this->db->get_where('user', ['email' => $email])->row();
        if (!$user) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert" style="margin-top:24px;">Akun dengan email tersebut tidak ditemukan.</div>');
            redirect('auth');
            return;
        }

        // Verifikasi token terhadap tabel user_token atau kolom user.token
        $isValidToken = false;
        if ($this->db->table_exists('user_token')) {
            $tokenRow = $this->db->get_where('user_token', ['email' => $email])->row();
            if ($tokenRow && !empty($tokenRow->token)) {
                if (trim($token) === trim($tokenRow->token) || password_verify($token, $tokenRow->token)) {
                    $isValidToken = true;
                }
            }
        }

        if (!$isValidToken && !empty($user->token)) {
            if (trim($token) === trim($user->token) || password_verify($token, $user->token)) {
                $isValidToken = true;
            }
        }

        if (!$isValidToken) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning" role="alert" style="margin-top:24px;">Token aktivasi tidak cocok atau sudah tidak berlaku.</div>');
            redirect('auth');
            return;
        }

        // Buat sesi login sementara khusus untuk proses onboarding
        $session_data = array(
            'id'               => $user->id,
            'user_id'          => $user->id,
            'username'         => !empty($user->username) ? $user->username : (!empty($user->nim) ? $user->nim : $user->email),
            'role_id'          => $user->role_id,
            'name'             => $user->name,
            'email'            => $user->email,
            'nim'              => !empty($user->nim) ? $user->nim : (!empty($user->nip) ? $user->nip : ''),
            'status'           => 'pending',
            'koordinator'      => isset($user->koordinator) ? $user->koordinator : '',
            'dosen_wali'       => isset($user->dosen_wali) ? $user->dosen_wali : '',
            'password_changed' => 0,
            'logged_in'        => TRUE
        );
        $this->session->set_userdata($session_data);

        // Lempar langsung ke layar form onboarding
        redirect('onboarding');
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
            redirect('');
            return;
        }

        $userId = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($userId);

        if (!$user) {
            $this->session->sess_destroy();
            redirect('login');
            return;
        }

        $roleId = (int)$user->role_id;
        $roleName = 'mahasiswa';
        $roleTitle = 'Mahasiswa';
        if ($this->db->table_exists('user_role')) {
            $r = $this->db->get_where('user_role', ['id' => $roleId])->row();
            if ($r) {
                $roleName = strtolower($r->role);
                $roleTitle = $r->role;
            }
        } else if ($this->db->table_exists('roles')) {
            $r = $this->db->get_where('roles', ['id' => $roleId])->row();
            if ($r) {
                $roleName = strtolower($r->role ?? $r->name ?? '');
                $roleTitle = $r->role ?? $r->name ?? 'User';
            }
        }

        // Roles that are students: Mahasiswa, Mahasiswa KP, Mahasiswa S2, etc.
        // All other roles (Dosen, Koordinator TA, Kaur, Admin, Laboran, Ketua KK, etc.) are staff/faculty
        $isMahasiswa = (strpos($roleName, 'mahasiswa') !== false);
        $isDosen = !$isMahasiswa;

        $data['title'] = 'Aktivasi Akun & Lengkapi Biodata — IK Labs Portal';
        $data['user'] = $user;
        $data['role_id'] = $roleId;
        $data['role_title'] = $roleTitle;
        $data['is_dosen'] = $isDosen;
        $data['is_mahasiswa'] = $isMahasiswa;
        $data['role_name'] = $roleName;
        if ($isDosen) {
            // Dosen: prioritas kolom nip, fallback ke nim jika numerik (bukan username string)
            $data['nim'] = !empty($user->nip)
                ? $user->nip
                : (!empty($user->nim) && is_numeric($user->nim) && $user->nim > 0 ? $user->nim : '');
        } else {
            // Mahasiswa: gunakan nim
            $data['nim'] = !empty($user->nim) ? $user->nim : (!empty($user->nidn_nim) ? $user->nidn_nim : '');
        }
        $data['kode_dosen'] = !empty($user->kode_dosen) ? $user->kode_dosen : '';
        $data['no_hp'] = !empty($user->no_hp) ? $user->no_hp : (!empty($user->no_telp) ? $user->no_telp : '');

        // Split existing name into nama_depan and nama_belakang if available
        $nameParts = explode(' ', trim($user->name), 2);
        $data['nama_depan'] = isset($nameParts[0]) ? $nameParts[0] : '';
        $data['nama_belakang'] = isset($nameParts[1]) ? $nameParts[1] : '';

        // Load Dosen Wali list directly from MySQL database `dosen_wali` table or `user` table
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
                        'jurusan' => !empty($dw['jurusan']) ? $dw['jurusan'] : 'Informatika',
                        'email'   => !empty($dw['email']) ? $dw['email'] : ''
                    ];
                }
            }
        }

        // Fetch all real lecturers directly from database `user` table
        if (empty($dosenList)) {
            $this->db->select('u.nip, u.name as nama, u.email, u.prodi as jurusan');
            $this->db->from('user u');
            $this->db->where_in('u.role_id', [2, 3, 6, 9]);
            $this->db->where('u.nip IS NOT NULL', null, false);
            $this->db->where('u.nip !=', '');
            $this->db->order_by('u.name', 'ASC');
            $queryUsers = $this->db->get()->result_array();
            foreach ($queryUsers as $du) {
                $dosenList[] = [
                    'nip'     => $du['nip'],
                    'nama'    => $du['nama'],
                    'jurusan' => !empty($du['jurusan']) ? $du['jurusan'] : 'Informatika',
                    'email'   => !empty($du['email']) ? $du['email'] : ''
                ];
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

        // Fetch Kelompok Keahlian (KK) dynamically from MySQL database
        $kkList = [];
        if ($this->db->table_exists('kelompok_keahlian')) {
            $this->db->order_by('id', 'ASC');
            $kkList = $this->db->get('kelompok_keahlian')->result_array();
        }
        $data['kk_list'] = $kkList;

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
        $kodeDosen          = strtoupper(trim($this->input->post('kode_dosen', true)));
        $noHp               = trim($this->input->post('no_hp', true));
        $tempatLahir        = trim($this->input->post('tempat_lahir', true));
        $tanggalLahir       = trim($this->input->post('tanggal_lahir', true));
        $alamat             = trim($this->input->post('alamat', true));
        $konsentrasi        = trim($this->input->post('konsentrasi', true));
        $dosenWali          = trim($this->input->post('dosen_wali', true));
        $kelompokKeahlian   = trim($this->input->post('kelompok_keahlian', true));
        $idKk               = $this->input->post('id_kk', true);

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

        // Check Role
        $roleId = (int)$user->role_id;
        $roleName = 'mahasiswa';
        if ($this->db->table_exists('user_role')) {
            $r = $this->db->get_where('user_role', ['id' => $roleId])->row();
            if ($r) {
                $roleName = strtolower($r->role);
            }
        } else if ($this->db->table_exists('roles')) {
            $r = $this->db->get_where('roles', ['id' => $roleId])->row();
            if ($r) {
                $roleName = strtolower($r->role ?? $r->name ?? '');
            }
        }

        $isMahasiswa = (strpos($roleName, 'mahasiswa') !== false);
        $isDosen = !$isMahasiswa;

        // Validate Kode Dosen (Strict for Lecturers)
        if ($isDosen && empty($kodeDosen)) {
            $msg = 'Kode dosen (inisial) wajib diisi untuk verifikasi identitas dosen!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        // Validate No Handphone (For all roles)
        if (empty($noHp)) {
            $msg = 'Nomor handphone / WhatsApp aktif wajib diisi!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        // 3. Strict Validation for Demographics & Birth Info
        if (empty($tempatLahir) || empty($tanggalLahir) || empty($alamat)) {
            $msg = 'Tempat lahir, tanggal lahir, dan alamat domisili lengkap wajib diisi!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        // 4. Strict Validation for Academic Info
        if ($isDosen && empty($kelompokKeahlian)) {
            $msg = 'Kelompok Keahlian (KK) wajib dipilih!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        if ($isMahasiswa && empty($dosenWali)) {
            $msg = 'Dosen wali akademik pembimbing wajib dipilih!';
            if ($isAjax) {
                $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['status' => 'error', 'message' => $msg]));
                return;
            }
            $this->session->set_flashdata('error', $msg);
            redirect('onboarding');
            return;
        }

        $fullName = trim($cleanDepan . ' ' . $cleanBelakang);

        // 3. Hash New Password mengikuti format auth pembimbing: hash('sha256', $password . $salt)
        $salt = password_hash("rasmuslerdorf", PASSWORD_DEFAULT);
        $hashedPassword = hash('sha256', $passwordBaru . $salt);

        // 4. Update user / users Table with password, salt, and biodata
        $userUpdate = [
            'name'             => $fullName,
            'password'         => $hashedPassword,
            'salt'             => $salt,
            'is_active'        => 1,
            'status'           => 'active',
            'token'            => null,
            'alamat'           => $alamat,
            'tempatlahir'      => $tempatLahir,
            'tanggal_lahir'    => $tanggalLahir,
            'prodi'            => $konsentrasi,
            'updated_at'       => date('Y-m-d H:i:s')
        ];

        if ($this->db->field_exists('password_changed', 'user')) {
            $userUpdate['password_changed'] = 1;
        }

        if ($this->db->field_exists('kode_dosen', 'user') && !empty($kodeDosen)) {
            $userUpdate['kode_dosen'] = $kodeDosen;
        }

        if ($this->db->field_exists('no_telp', 'user') && !empty($noHp)) {
            $userUpdate['no_telp'] = $noHp;
        }

        if ($this->db->field_exists('no_hp', 'user') && !empty($noHp)) {
            $userUpdate['no_hp'] = $noHp;
        }

        if ($isDosen) {
            $userUpdate['nip'] = $nim;
            if (!empty($kelompokKeahlian)) {
                // Kelompok keahlian dosen disimpan ke field 'koordinator', 'kelompok_keahlian', dan 'id_kk'
                if ($this->db->field_exists('koordinator', 'user')) {
                    $userUpdate['koordinator'] = $kelompokKeahlian;
                }
                if ($this->db->field_exists('kelompok_keahlian', 'user')) {
                    $userUpdate['kelompok_keahlian'] = $kelompokKeahlian;
                }
                if ($this->db->field_exists('id_kk', 'user') && !empty($idKk)) {
                    $userUpdate['id_kk'] = (int)$idKk;
                }
            }
        } else {
            $userUpdate['nim'] = $nim;
            if (!empty($dosenWali)) {
                $userUpdate['dosen_wali'] = $dosenWali;
            }
        }

        if (!empty($nim)) {
            $userUpdate['nidn_nim'] = $nim;
        }

        $this->User_model->update($userId, $userUpdate);

        // Delete temporary activation token so it cannot be reused
        if ($this->db->table_exists('user_token') && !empty($user->email)) {
            $this->db->delete('user_token', ['email' => $user->email]);
        }

        // 5. Update / Insert to mahasiswa Table (ONLY for Mahasiswa role)
        if ($isMahasiswa && $this->db->table_exists('mahasiswa') && !empty($nim)) {
            $mhsFields = $this->db->list_fields('mahasiswa');
            $candidateMhsData = [
                'nim'             => $nim,
                'nama_depan'      => $cleanDepan,
                'nama_belakang'   => $cleanBelakang,
                'email'           => !empty($user->email) ? $user->email : null,
                'no_hp'           => !empty($noHp) ? $noHp : null,
                'alamat'          => $alamat,
                'kota'            => $tempatLahir,
                'prodi'           => $konsentrasi,
                'konsentrasi_dkv' => $konsentrasi
            ];
            $mhsData = [];
            foreach ($candidateMhsData as $k => $v) {
                if (in_array($k, $mhsFields)) {
                    $mhsData[$k] = $v;
                }
            }

            if (!empty($mhsData)) {
                $existingMhs = $this->db->get_where('mahasiswa', ['nim' => $nim])->row();
                if ($existingMhs) {
                    $this->db->where('nim', $nim);
                    $this->db->update('mahasiswa', $mhsData);
                } else {
                    $this->db->insert('mahasiswa', $mhsData);
                }
            }
        }

        // 6. Update Session Data
        $sessionData = [
            'name'             => $fullName,
            'nidn_nim'         => $nim,
            'nim'              => $nim,
            'kode_dosen'       => $kodeDosen,
            'no_hp'            => $noHp,
            'no_telp'          => $noHp,
            'password_changed' => 1
        ];

        if ($isDosen && !empty($kelompokKeahlian)) {
            $sessionData['koordinator'] = $kelompokKeahlian;
            $sessionData['kelompok_keahlian'] = $kelompokKeahlian;
        }

        $this->session->set_userdata($sessionData);

        $this->session->set_flashdata('success', 'Aktivasi akun berhasil! Password Anda telah diperbarui dan profil telah tersimpan.');

        // Determine target dashboard URL based on role
        $targetRedirect = base_url();
        if ($roleId === 4 || strpos($roleName, 'mahasiswa') !== false) {
            $targetRedirect = base_url();
        } elseif ($roleId === 3 || strpos($roleName, 'dosen') !== false) {
            $targetRedirect = base_url();
        } elseif ($roleId === 6 || strpos($roleName, 'koordinator') !== false) {
            $targetRedirect = base_url('koordinatorta');
        } elseif ($roleId === 2 || strpos($roleName, 'kaur') !== false) {
            $targetRedirect = base_url('kaur');
        } elseif ($roleId === 21 || strpos($roleName, 'laboran') !== false) {
            $targetRedirect = base_url('laboran');
        } elseif ($roleId === 5 || strpos($roleName, 'laa') !== false) {
            $targetRedirect = base_url('adminlayanan');
        } elseif ($roleId === 9 || strpos($roleName, 'ketua kk') !== false) {
            $targetRedirect = base_url('ketuakk');
        } elseif ($roleId === 1 || strpos($roleName, 'admin') !== false) {
            $targetRedirect = base_url();
        }

        if ($isAjax) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'   => 'success',
                    'message'  => 'Aktivasi akun berhasil! Password Anda telah diperbarui dan profil telah tersimpan.',
                    'redirect' => $targetRedirect
                ]));
            return;
        }

        // 7. Redirect to dashboard
        redirect($targetRedirect);
    }

    /**
     * Proxy endpoint for Wilayah.id API to eliminate browser CORS restrictions
     */
    public function wilayah_proxy()
    {
        $endpoint = $this->input->get('endpoint', TRUE);
        $code = $this->input->get('code', TRUE);

        $url = '';
        if ($endpoint === 'provinces') {
            $url = 'https://wilayah.id/api/provinces.json';
        } elseif ($endpoint === 'regencies' && !empty($code)) {
            $url = 'https://wilayah.id/api/regencies/' . urlencode($code) . '.json';
        } elseif ($endpoint === 'districts' && !empty($code)) {
            $url = 'https://wilayah.id/api/districts/' . urlencode($code) . '.json';
        } elseif ($endpoint === 'villages' && !empty($code)) {
            $url = 'https://wilayah.id/api/villages/' . urlencode($code) . '.json';
        }

        if (empty($url)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['data' => []]));
            return;
        }

        $opts = [
            'http' => [
                'timeout' => 8,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];
        $context = stream_context_create($opts);
        $json = @file_get_contents($url, false, $context);

        if (!$json) {
            // Fallback to emsifa if needed
            if ($endpoint === 'provinces') {
                $url2 = 'https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json';
            } elseif ($endpoint === 'regencies') {
                $url2 = 'https://emsifa.github.io/api-wilayah-indonesia/api/regencies/' . str_replace('.', '', $code) . '.json';
            } elseif ($endpoint === 'districts') {
                $url2 = 'https://emsifa.github.io/api-wilayah-indonesia/api/districts/' . str_replace('.', '', $code) . '.json';
            } elseif ($endpoint === 'villages') {
                $url2 = 'https://emsifa.github.io/api-wilayah-indonesia/api/villages/' . str_replace('.', '', $code) . '.json';
            }
            if (!empty($url2)) {
                $json = @file_get_contents($url2, false, $context);
                if ($json) {
                    $raw = json_decode($json, true);
                    if (is_array($raw)) {
                        $json = json_encode(['data' => array_map(function($item) {
                            return ['code' => isset($item['id']) ? $item['id'] : '', 'name' => isset($item['name']) ? ucwords(strtolower($item['name'])) : ''];
                        }, $raw)]);
                    }
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output($json ? $json : json_encode(['data' => []]));
    }
}
