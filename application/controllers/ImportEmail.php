<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImportEmail extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper(array('url', 'form', 'html'));
        $this->load->library('session');
        $this->load->model('User_model');
        $this->_check_auth();
    }

    /**
     * Strict Authentication & Role Check
     * Allowed Roles: Admin (1), Kepala Urusan (2), Admin LAA (5), Koordinator TA (6), Admin Prodi (16), Laboran (21), Super Admin (22)
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
                        'status' => 'error',
                        'message' => 'Sesi login telah berakhir. Silakan login kembali.'
                    ]))
                    ->_display();
                exit;
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu Import Email & Dispatcher.');
            redirect('login');
            exit;
        }

        $roleId = (int)$this->session->userdata('role_id');
        // Allowed: 1 = Admin, 2 = Kepala Urusan, 5 = Admin LAA, 6 = Koordinator TA, 16 = Admin Prodi, 21 = Laboran, 22 = Super Admin
        $allowedRoles = [1, 2, 5, 6, 16, 21, 22];

        if (!in_array($roleId, $allowedRoles)) {
            $isAjax = $this->input->is_ajax_request() || 
                      (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengelola data akun.'
                    ]))
                    ->_display();
                exit;
            }
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini hanya dapat diakses oleh Administrator, Ka. Ur, Laboran, LAA, dan Koordinator TA.');
            redirect('dashboard');
            exit;
        }

        return true;
    }

    /**
     * Helper: Allowed import role IDs per user role
     */
    private function _get_allowed_import_roles($userRoleId) {
        $userRoleId = (int)$userRoleId;
        if ($userRoleId === 21) {
            // Laboran: Laboran (21), Dosen (3), Mahasiswa (4)
            return [3, 4, 21];
        } elseif ($userRoleId === 6) {
            // Koordinator TA: Koordinator TA (6) only
            return [6];
        } elseif ($userRoleId === 5) {
            // Admin LAA: Mahasiswa (4) & Admin LAA (5)
            return [4, 5];
        }
        // Super Admin (22), Admin (1), Ka. Ur (2), Admin Prodi (16): Full All roles
        return [1, 2, 3, 4, 5, 6, 7, 9, 21, 22];
    }

    /**
     * Display the Admin / Laboran / LAA / Koordinator TA Email Import & Token Generator Dashboard
     */
    public function index() {
        $roleId = (int)$this->session->userdata('role_id');
        $isLaboran = ($roleId === 21);
        $isLaa = ($roleId === 5);
        $isKoorTa = ($roleId === 6);
        $isSuperAdmin = ($roleId === 22);

        $title = 'Admin - Import Email & Token Dispatcher';
        if ($isLaboran) $title = 'Laboran - Import Email & Token Dispatcher';
        elseif ($isLaa) $title = 'Admin LAA - Import Email & Token Dispatcher';
        elseif ($isKoorTa) $title = 'Koordinator TA - Import Email & Token Dispatcher';
        elseif ($roleId === 2) $title = 'Kepala Urusan - Import Email & Token Dispatcher';
        elseif ($roleId === 1) $title = 'Admin - Import Email & Token Dispatcher';
        elseif ($isSuperAdmin) $title = 'Super Admin - Import Email & Token Dispatcher';

        // Compile full roles map for consistent frontend mapping
        $allRolesMap = [
            1 => 'Admin',
            2 => 'Kepala Urusan',
            3 => 'Dosen',
            4 => 'Mahasiswa',
            5 => 'Admin LAA',
            6 => 'Koordinator TA',
            7 => 'PIC KK',
            8 => 'Reviewer',
            9 => 'Ketua KK',
            10 => 'Pembimbing 1',
            11 => 'Pembimbing 2',
            12 => 'Penguji',
            13 => 'Dosen Wali',
            14 => 'Kaprodi',
            15 => 'Dekan',
            16 => 'Admin Prodi',
            17 => 'Staff LAA',
            18 => 'Tim TA',
            19 => 'Koordinator MK',
            20 => 'Asisten Lab',
            21 => 'Laboran',
            22 => 'Super Admin'
        ];
        if ($this->db->table_exists('user_role')) {
            $dbRoles = $this->db->get('user_role')->result_array();
            foreach ($dbRoles as $dr) {
                $rName = !empty($dr['role']) ? $dr['role'] : (!empty($dr['name']) ? $dr['name'] : '');
                if ($rName) {
                    $allRolesMap[(int)$dr['id']] = $rName;
                }
            }
        }

        $data['title'] = $title;
        $data['user_role_id'] = $roleId;
        $data['is_laboran'] = $isLaboran;
        $data['is_laa'] = $isLaa;
        $data['is_koor_ta'] = $isKoorTa;
        $data['is_super_admin'] = $isSuperAdmin;
        $data['all_roles_map'] = $allRolesMap;
        $data['initial_accounts_json'] = json_encode($this->_get_formatted_users());
        $this->load->view('admin/import_email', $data);
    }

    /**
     * AJAX: Get all users in formatted JSON structure
     */
    public function get_users_json() {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Import bulk accounts from parsed Excel/CSV JSON
     */
    public function import_data() {
        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        if (empty($json['accounts']) || !is_array($json['accounts'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Data import kosong atau format tidak valid.'
                ]));
            return;
        }

        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = $this->_get_allowed_import_roles($roleId);

        $filteredAccounts = [];
        foreach ($json['accounts'] as $acc) {
            $rawRole = isset($acc['role']) ? trim((string)$acc['role']) : (isset($acc['peran']) ? trim((string)$acc['peran']) : (isset($acc['role_id']) ? trim((string)$acc['role_id']) : '4'));
            $targetRoleId = $this->User_model->get_role_id_by_name($rawRole);
            if (in_array((int)$targetRoleId, $allowedRoles)) {
                $acc['role'] = $targetRoleId;
                $filteredAccounts[] = $acc;
            }
        }

        if (empty($filteredAccounts)) {
            $roleMsg = 'Akses ditolak: Anda tidak memiliki izin untuk mengimpor akun dengan role tersebut.';
            if ($roleId === 21) {
                $roleMsg = 'Akses ditolak: Laboran hanya memiliki izin untuk mengimpor akun Laboran (21), Dosen (3), dan Mahasiswa (4).';
            } elseif ($roleId === 6) {
                $roleMsg = 'Akses ditolak: Koordinator TA hanya memiliki izin untuk mengimpor akun Koordinator TA (6).';
            } elseif ($roleId === 5) {
                $roleMsg = 'Akses ditolak: Admin LAA hanya memiliki izin untuk mengimpor akun Mahasiswa (4) dan Admin LAA (5).';
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => $roleMsg
                ]));
            return;
        }
        $json['accounts'] = $filteredAccounts;

        try {
            $res = $this->User_model->upsert_users_bulk($json['accounts']);
            $totalCount = $res['imported'] + $res['updated'];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => "Berhasil memproses {$totalCount} akun ({$res['imported']} baru, {$res['updated']} diperbarui) ke dalam database.",
                    'accounts' => $this->_get_formatted_users()
                ]));
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Gagal memproses import data: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * AJAX: Generate 8-character token for selected users
     */
    public function generate_tokens() {
        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        $updates = [];

        // Format 1: Direct updates array from frontend [{ id, token }, ...]
        if (isset($json['updates']) && is_array($json['updates'])) {
            foreach ($json['updates'] as $item) {
                if (isset($item['id']) && $item['id'] !== '') {
                    $rawTok = isset($item['token']) ? $item['token'] : null;
                    $updates[] = [
                        'id' => (string)$item['id'],
                        'token' => $this->_format_secure_token($rawTok)
                    ];
                }
            }
        }
        // Format 2: Single user_id and token
        elseif (isset($json['user_id']) && $json['user_id'] !== '') {
            $rawTok = isset($json['token']) ? $json['token'] : null;
            $updates[] = [
                'id' => (string)$json['user_id'],
                'token' => $this->_format_secure_token($rawTok)
            ];
        }
        // Format 3: user_ids array
        elseif (isset($json['user_ids']) && is_array($json['user_ids'])) {
            foreach ($json['user_ids'] as $id) {
                if ($id !== '') {
                    $updates[] = [
                        'id' => (string)$id,
                        'token' => $this->_format_secure_token()
                    ];
                }
            }
        }

        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = $this->_get_allowed_import_roles($roleId);

        if (in_array($roleId, [5, 6, 21]) && !empty($updates)) {
            $allowedUpdates = [];
            foreach ($updates as $item) {
                $user = $this->User_model->get_by_id($item['id']);
                if ($user && in_array((int)$user->role_id, $allowedRoles)) {
                    $allowedUpdates[] = $item;
                }
            }
            $updates = $allowedUpdates;
        }

        if (empty($updates)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Tidak ada akun yang valid (atau diizinkan) untuk di-generate tokennya.'
                ]));
            return;
        }

        $generatedCount = $this->User_model->update_user_tokens_bulk($updates);
        $skippedCount = count($updates) - $generatedCount;

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Berhasil generate token untuk {$generatedCount} akun." . ($skippedCount > 0 ? " ({$skippedCount} akun protected dilewati)" : ""),
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Dispatch email for selected users (Bulk / Multi Email)
     */
    public function send_emails() {
        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        $userIds = isset($json['user_ids']) && is_array($json['user_ids']) ? $json['user_ids'] : [];

        if (empty($userIds)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Pilih setidaknya satu akun untuk dikirimkan email.'
                ]));
            return;
        }

        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = $this->_get_allowed_import_roles($roleId);

        $sentCount = 0;
        $skippedProtectedCount = 0;
        $noTokenCount = 0;
        $skippedUnauthorizedCount = 0;
        $templateSubject = isset($json['subject']) && !empty($json['subject']) ? $json['subject'] : '[IFIK Telkom University] Tautan Aktivasi Akun Portal Anda';
        $templateSubject = trim(str_replace([': {TOKEN}', '{TOKEN}'], '', $templateSubject));
        $templateBody = isset($json['body']) ? $json['body'] : '';

        foreach ($userIds as $id) {
            $user = $this->User_model->get_by_id($id);
            if (!$user || empty($user->email)) {
                continue;
            }

            // Role access check
            if (in_array($roleId, [5, 6, 21]) && !in_array((int)$user->role_id, $allowedRoles)) {
                $skippedUnauthorizedCount++;
                continue;
            }

            // Skip accounts that already changed their password
            if ((int)$user->password_changed === 1) {
                $skippedProtectedCount++;
                continue;
            }

            // Strictly require token to exist before sending email
            if (empty($user->token)) {
                $noTokenCount++;
                continue;
            }

            $token = $user->token;

            // Build HTML email message
            $htmlMessage = $this->_build_html_email($user, $token, $templateSubject, $templateBody);
            $subject = str_replace(
                ['{NAMA}', '{ROLE}', '{NIM_NIP}', '{EMAIL}', '{TOKEN}'],
                [$user->name, $this->_get_role_name_by_id($user->role_id), $user->nidn_nim, $user->email, ''],
                $templateSubject
            );
            $subject = trim(preg_replace('/\s+/', ' ', $subject));

            // Send via SMTP
            $this->_send_smtp_email($user->email, $subject, $htmlMessage);

            // Update database status
            $this->User_model->update_email_status($id, 'terkirim');
            $sentCount++;
        }

        $extraInfo = [];
        if ($skippedProtectedCount > 0) $extraInfo[] = "{$skippedProtectedCount} akun protected dilewati";
        if ($noTokenCount > 0) $extraInfo[] = "{$noTokenCount} akun dilewati karena belum di-generate tokennya";
        if ($skippedUnauthorizedCount > 0) $extraInfo[] = "{$skippedUnauthorizedCount} akun dilewati karena di luar wewenang role Anda";
        $extraStr = !empty($extraInfo) ? ' (' . implode(', ', $extraInfo) . ')' : '';

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Berhasil memproses dan mengirimkan email kredensial ke {$sentCount} akun.{$extraStr}",
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Dispatch email for a single user
     */
    public function send_single_email() {
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        $userId = isset($json['user_id']) ? (int)$json['user_id'] : 0;
        $user = $this->User_model->get_by_id($userId);

        if (!$user || empty($user->email)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Akun tidak ditemukan atau alamat email tidak valid.'
                ]));
            return;
        }

        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = $this->_get_allowed_import_roles($roleId);

        if (in_array($roleId, [5, 6, 21]) && !in_array((int)$user->role_id, $allowedRoles)) {
            $deniedMsg = 'Akses ditolak: Anda tidak memiliki izin untuk mengirimkan token email ke role ini.';
            if ($roleId === 21) {
                $deniedMsg = 'Akses ditolak: Laboran hanya dapat mengirimkan token email untuk akun Laboran, Dosen, dan Mahasiswa.';
            } elseif ($roleId === 6) {
                $deniedMsg = 'Akses ditolak: Koordinator TA hanya dapat mengirimkan token email untuk akun Koordinator TA.';
            } elseif ($roleId === 5) {
                $deniedMsg = 'Akses ditolak: Admin LAA hanya dapat mengirimkan token email untuk akun Mahasiswa dan Admin LAA.';
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => $deniedMsg
                ]));
            return;
        }

        if ((int)$user->password_changed === 1) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Akun ini telah mengubah password (Protected), pengiriman token tidak diperlukan.'
                ]));
            return;
        }

        // Strictly require token
        if (empty($user->token)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Token akses belum di-generate untuk akun ini. Silakan generate token terlebih dahulu.'
                ]));
            return;
        }

        $token = $user->token;
        $templateSubject = isset($json['subject']) && !empty($json['subject']) ? $json['subject'] : '[IFIK Telkom University] Tautan Aktivasi Akun Portal Anda';
        $templateSubject = trim(str_replace([': {TOKEN}', '{TOKEN}'], '', $templateSubject));
        $templateBody = isset($json['body']) ? $json['body'] : '';

        $htmlMessage = $this->_build_html_email($user, $token, $templateSubject, $templateBody);
        $subject = str_replace(
            ['{NAMA}', '{ROLE}', '{NIM_NIP}', '{EMAIL}', '{TOKEN}'],
            [$user->name, $this->_get_role_name_by_id($user->role_id), $user->nidn_nim, $user->email, ''],
            $templateSubject
        );
        $subject = trim(preg_replace('/\s+/', ' ', $subject));

        $this->_send_smtp_email($user->email, $subject, $htmlMessage);
        $this->User_model->update_email_status($userId, 'terkirim');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Email aktivasi akun berhasil dikirimkan ke {$user->email}.",
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Save single user (Create / Edit)
     */
    public function save_user() {
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        if (empty($json['name']) || empty($json['email'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Nama dan Email wajib diisi.'
                ]));
            return;
        }

        $email = strtolower(trim($json['email']));
        if (!preg_match('/@(student\.)?telkomuniversity\.ac\.id$/i', $email)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Email harus menggunakan domain @telkomuniversity.ac.id atau @student.telkomuniversity.ac.id'
                ]));
            return;
        }

        $targetUserId = !empty($json['id']) ? trim((string)$json['id']) : null;
        $userTbl = $this->db->table_exists('user') ? 'user' : 'users';

        // Check if another user is already using this email
        $existingUserWithEmail = $this->db->get_where($userTbl, ['email' => $email])->row();
        if ($existingUserWithEmail) {
            if (!$targetUserId || $existingUserWithEmail->id != $targetUserId) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => "Email '{$email}' sudah digunakan oleh akun lain atas nama '{$existingUserWithEmail->name}'. Silakan gunakan email yang berbeda."
                    ]));
                return;
            }
        }

        $nimNip = isset($json['nim_nip']) ? trim($json['nim_nip']) : '';

        // Check if another user is already using this NIM / NIP / ID
        if (!empty($nimNip)) {
            $this->db->group_start();
            if ($this->db->field_exists('nim', $userTbl)) $this->db->or_where('nim', $nimNip);
            if ($this->db->field_exists('nidn_nim', $userTbl)) $this->db->or_where('nidn_nim', $nimNip);
            if ($this->db->field_exists('nip', $userTbl)) $this->db->or_where('nip', $nimNip);
            if ($this->db->field_exists('username', $userTbl)) $this->db->or_where('username', $nimNip);
            $this->db->group_end();

            $existingUserWithNim = $this->db->get($userTbl)->row();
            if ($existingUserWithNim) {
                if (!$targetUserId || $existingUserWithNim->id != $targetUserId) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'error',
                            'message' => "NIM/NIP '{$nimNip}' sudah digunakan oleh akun lain atas nama '{$existingUserWithNim->name}'. Silakan gunakan NIM/NIP yang berbeda."
                        ]));
                    return;
                }
            }
        }

        $targetRoleId = $this->User_model->get_role_id_by_name(isset($json['role']) ? $json['role'] : 'Mahasiswa');
        $currentRoleId = (int)$this->session->userdata('role_id');
        $allowedRoles = $this->_get_allowed_import_roles($currentRoleId);

        if (in_array($currentRoleId, [5, 6, 21]) && !in_array((int)$targetRoleId, $allowedRoles)) {
            $deniedMsg = 'Akses ditolak: Anda tidak memiliki izin untuk mengelola akun dengan role ini.';
            if ($currentRoleId === 21) {
                $deniedMsg = 'Akses ditolak: Laboran hanya memiliki izin untuk menambah atau mengubah akun Laboran, Dosen, dan Mahasiswa.';
            } elseif ($currentRoleId === 6) {
                $deniedMsg = 'Akses ditolak: Koordinator TA hanya memiliki izin untuk menambah atau mengubah akun Koordinator TA.';
            } elseif ($currentRoleId === 5) {
                $deniedMsg = 'Akses ditolak: Admin LAA hanya memiliki izin untuk menambah atau mengubah akun Mahasiswa dan Admin LAA.';
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => $deniedMsg
                ]));
            return;
        }

        $nimNip = isset($json['nim_nip']) ? trim($json['nim_nip']) : '';
        $name = trim($json['name']);
        $rawToken = isset($json['token']) && !empty($json['token']) ? trim($json['token']) : null;

        if ($targetUserId) {
            // EDIT EXISTING USER BY ID (STRICTLY SUPER ADMIN ONLY)
            if ($currentRoleId !== 22) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Akses ditolak: Hanya Super Administrator yang memiliki izin untuk mengubah data akun.'
                    ]));
                return;
            }

            $currentUser = $this->User_model->get_by_id($targetUserId);
            if (!$currentUser) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'error',
                        'message' => 'Akun tidak ditemukan atau telah dihapus.'
                    ]));
                return;
            }

            $username = explode('@', $email)[0];
            $updateData = [
                'name' => $name,
                'email' => $email,
                'role_id' => $targetRoleId
            ];
            if ($this->db->field_exists('username', $userTbl)) $updateData['username'] = $username;
            if ($this->db->field_exists('nidn_nim', $userTbl)) $updateData['nidn_nim'] = $nimNip;
            if ((int)$targetRoleId !== 4) {
                // Non-Mahasiswa (Dosen, Laboran, Kaur, dll): simpan ke nip
                if ($this->db->field_exists('nip', $userTbl)) $updateData['nip'] = $nimNip;
                if ($this->db->field_exists('nim', $userTbl)) $updateData['nim'] = $currentUser->nim ?? '';
            } else {
                // Mahasiswa: simpan ke nim
                if ($this->db->field_exists('nim', $userTbl)) $updateData['nim'] = $nimNip;
            }
            if ($this->db->field_exists('updated_at', $userTbl)) $updateData['updated_at'] = date('Y-m-d H:i:s');

            $isUserProtected = (!empty($currentUser->password_changed) && (int)$currentUser->password_changed === 1) || (!empty($currentUser->is_active) && (int)$currentUser->is_active === 1);

            if ($rawToken && !$isUserProtected) {
                $salt = bin2hex(random_bytes(16));
                if ($this->db->field_exists('salt', $userTbl)) $updateData['salt'] = $salt;
                if ($this->db->table_exists('user_token')) {
                    $this->User_model->sync_user_token($email, $rawToken);
                } elseif ($this->db->field_exists('token', $userTbl)) {
                    $updateData['token'] = $rawToken;
                    $updateData['password'] = password_hash($rawToken, PASSWORD_DEFAULT, ['cost' => 10]);
                }
            }

            $this->User_model->update($targetUserId, $updateData);

            // If email changed, cleanup old user_token entry
            if ($currentUser->email && strtolower(trim($currentUser->email)) !== $email && $this->db->table_exists('user_token')) {
                $this->db->where('email', strtolower(trim($currentUser->email)))->delete('user_token');
            }
        } else {
            // CREATE NEW USER
            $userData = [
                'name' => $name,
                'email' => $email,
                'role_id' => $targetRoleId,
                'nidn_nim' => $nimNip,
                'token' => $rawToken
            ];
            $this->User_model->upsert_user($userData);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Data akun berhasil disimpan.',
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Delete selected users (STRICTLY SUPER ADMIN ONLY)
     */
    public function delete_users() {
        $currentRoleId = (int)$this->session->userdata('role_id');
        if ($currentRoleId !== 22) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Akses ditolak: Hanya Super Administrator yang memiliki hak akses untuk menghapus akun.'
                ]));
            return;
        }

        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        $userIds = isset($json['user_ids']) && is_array($json['user_ids']) ? $json['user_ids'] : [];

        if (empty($userIds)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Pilih setidaknya satu akun untuk dihapus.'
                ]));
            return;
        }

        $this->User_model->delete_users_batch($userIds);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Akun terpilih berhasil dihapus dari database.',
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Reset imported testing accounts (Super Admin only)
     */
    public function reset_data() {
        $currentRoleId = (int)$this->session->userdata('role_id');

        if ($currentRoleId !== 22) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Akses ditolak: Fitur reset database hanya dapat diakses oleh Super Administrator.'
                ]));
            return;
        }

        $this->User_model->reset_imported_users();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Data akun berhasil direset ke 6 akun master awal.',
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * Helper: Get formatted array of users for frontend JSON
     */
    private function _get_formatted_users() {
        $rawUsers = $this->User_model->get_all_users_with_roles();
        $roleId = (int)$this->session->userdata('role_id');
        $allowedRoles = $this->_get_allowed_import_roles($roleId);

        if (in_array($roleId, [5, 6, 21])) {
            $rawUsers = array_filter($rawUsers, function($u) use ($allowedRoles) {
                return in_array((int)$u['role_id'], $allowedRoles);
            });
        }

        $formatted = [];

        foreach ($rawUsers as $u) {
            $roleDisplay = !empty($u['role_display_name']) ? $u['role_display_name'] : (!empty($u['role_slug']) ? $u['role_slug'] : '');
            if (empty($roleDisplay)) {
                $roleDisplay = $this->_get_role_name_by_id($u['role_id']);
            }

            $isMaster = in_array($u['id'], ['admin-01', 'admin-laa-01', 'dsn-wali-01', 'kaur-01', 'koor-ta-01', 'laboran-01', 'ketua-kk-01', 'super-admin-01', 'mhs-1301210001']);
            $isPasswordChanged = $isMaster ? true : (!empty($u['password_changed']) && (int)$u['password_changed'] === 1);

            $tokenStatus = 'empty';
            if ($isPasswordChanged) {
                $tokenStatus = 'password_changed';
            } elseif (!empty($u['token'])) {
                $tokenStatus = 'ready';
            }

            $dateImported = '-';
            if (!empty($u['date_created'])) {
                $ts = is_numeric($u['date_created']) ? (int)$u['date_created'] : strtotime($u['date_created']);
                $dateImported = date('Y-m-d H:i', $ts);
            } elseif (!empty($u['created_at'])) {
                $dateImported = date('Y-m-d H:i', strtotime($u['created_at']));
            } elseif (!empty($u['updated_at'])) {
                $dateImported = date('Y-m-d H:i', strtotime($u['updated_at']));
            }

            $formatted[] = [
                'id' => (string)$u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'role' => $roleDisplay,
                'nim_nip' => !empty($u['nidn_nim']) ? $u['nidn_nim'] : '-',
                'token' => !empty($u['token']) ? $u['token'] : '',
                'token_display' => !empty($u['token']) ? (strlen($u['token']) > 12 ? substr($u['token'], 0, 8) . '...' . substr($u['token'], -4) : $u['token']) : '',
                'token_masked' => !empty($u['token']) ? substr($u['token'], 0, 4) . '••••••••' : '',
                'token_status' => $tokenStatus,
                'password_changed' => $isPasswordChanged,
                'email_status' => !empty($u['email_status']) ? $u['email_status'] : 'belum',
                'email_sent_at' => (!empty($u['email_sent_at']) && $u['email_sent_at'] !== '-') ? date('Y-m-d H:i', strtotime($u['email_sent_at'])) : '-',
                'date_imported' => $dateImported,
                'created_at' => $dateImported
            ];
        }

        return $formatted;
    }

    /**
     * Helper: Get role display name by role_id
     */
    private function _get_role_name_by_id($roleId) {
        $roleId = (int)$roleId;
        if ($this->db->table_exists('user_role')) {
            $r = $this->db->get_where('user_role', ['id' => $roleId])->row();
            if ($r && !empty($r->role)) return $r->role;
            $roles = [
                1 => 'Admin',
                2 => 'Kepala Urusan',
                3 => 'Dosen',
                4 => 'Mahasiswa',
                5 => 'Admin LAA',
                6 => 'Koordinator TA',
                7 => 'PIC KK',
                9 => 'Ketua KK',
                21 => 'Laboran',
                22 => 'Super Admin'
            ];
            return $roles[$roleId] ?? 'Mahasiswa';
        }

        $roles = [
            1 => 'Admin',
            2 => 'Kepala Urusan',
            3 => 'Dosen',
            4 => 'Mahasiswa',
            5 => 'Admin LAA',
            6 => 'Koordinator TA',
            7 => 'PIC KK',
            9 => 'Ketua KK',
            21 => 'Laboran'
        ];
        return isset($roles[$roleId]) ? $roles[$roleId] : 'Mahasiswa';
    }

    /**
     * Helper: Build branded responsive HTML email template (1-Click Direct Activation)
     */
    private function _build_html_email($user, $token, $subject, $bodyTemplate = '') {
        $activationUrl = site_url('login/activate?email=' . urlencode($user->email) . '&token=' . urlencode($token));
        $name = htmlspecialchars($user->name);
        $email = htmlspecialchars($user->email);
        $nim = !empty($user->nidn_nim) ? htmlspecialchars($user->nidn_nim) : '-';
        $role = $this->_get_role_name_by_id($user->role_id);
        $currentYear = date('Y');

        $html = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun Portal IFIK Telkom University</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color: #334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; padding: 35px 12px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01); border: 1px solid #e2e8f0;" cellpadding="0" cellspacing="0">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); padding: 32px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">Portal Layanan IFIK</h1>
                            <p style="color: #ffedd5; margin: 6px 0 0 0; font-size: 13px; font-weight: 500;">Fakultas Industri Kreatif &bull; Telkom University</p>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 36px 30px 30px 30px; line-height: 1.6;">
                            <p style="margin: 0 0 14px 0; font-size: 16px; font-weight: 700; color: #0f172a;">Halo, ' . $name . ' 👋</p>
                            <p style="margin: 0 0 18px 0; font-size: 14px; color: #475569;">Akun Anda telah berhasil didaftarkan ke dalam sistem Portal Layanan IFIK Telkom University sebagai <strong style="color: #ea580c;">' . $role . '</strong>.</p>
                            
                            <!-- Account Details Table -->
                            <table width="100%" style="background-color: #f8fafc; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; border: 1px solid #e2e8f0;" cellpadding="0" cellspacing="0">
                                <tr><td style="color: #64748b; padding: 5px 0; width: 35%;">NIM / NIP:</td><td style="color: #0f172a; font-weight: 600;">' . $nim . '</td></tr>
                                <tr><td style="color: #64748b; padding: 5px 0;">Email Resmi:</td><td style="color: #0f172a; font-weight: 600;">' . $email . '</td></tr>
                                <tr><td style="color: #64748b; padding: 5px 0;">Peran (Role):</td><td style="color: #0f172a; font-weight: 600;">' . $role . '</td></tr>
                            </table>

                            <p style="margin: 0 0 24px 0; font-size: 14px; color: #475569; line-height: 1.5;">Untuk mengaktifkan akun dan membuat kata sandi baru Anda, silakan klik tombol aktivasi langsung di bawah ini tanpa perlu memasukkan token manual:</p>

                            <!-- 1-Click CTA Button -->
                            <div style="text-align: center; margin: 32px 0 20px 0;">
                                <a href="' . $activationUrl . '" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: #ffffff; text-decoration: none; font-weight: 700; font-size: 15px; padding: 14px 38px; border-radius: 12px; box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);">Aktifkan & Masuk ke Akun Saya &rarr;</a>
                            </div>

                            <p style="margin: 20px 0 0 0; font-size: 12px; color: #94a3b8; text-align: center;">Tautan ini bersifat rahasia dan berlaku khusus untuk aktivasi akun Anda.</p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 22px 30px; text-align: center; color: #94a3b8; font-size: 11px; line-height: 1.5; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 4px 0;">Email ini dikirimkan secara otomatis oleh Sistem Dispatcher Portal IFIK Telkom University.</p>
                            <p style="margin: 0;">&copy; ' . $currentYear . ' Fakultas Industri Kreatif &bull; Telkom University. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';

        return $html;
    }

    /**
     * Private helper: Send email via CodeIgniter SMTP Library
     */
    private function _send_smtp_email($to, $subject, $htmlMessage) {
        try {
            $this->load->library('email');
            $this->email->clear(TRUE);

            $this->email->from('apgchannel11@gmail.com', 'Portal Layanan IFIK — Telkom University');
            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->message($htmlMessage);

            // Attempt SMTP Dispatch
            $sent = $this->email->send();
            if (!$sent) {
                log_message('error', 'SMTP Dispatch Error to ' . $to . ': ' . $this->email->print_debugger(['headers']));
            }
            return $sent;
        } catch (Exception $e) {
            log_message('error', 'SMTP Dispatch Exception to ' . $to . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper: Format and ensure token is always a secure Bcrypt hash
     */
    private function _format_secure_token($token = null) {
        if (empty($token)) {
            return $this->_generate_8char_token();
        }
        // If already Bcrypt hash ($2y$...)
        if (strpos($token, '$2y$') === 0 && strlen($token) >= 60) {
            return $token;
        }
        return password_hash($token, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Private helper: Generate secure Bcrypt hash token ($2y$10$...)
     */
    private function _generate_8char_token() {
        // Generate secure 8-character mixed random token as plaintext
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$';
        $plain = '';
        for ($i = 0; $i < 10; $i++) {
            $plain .= $chars[random_int(0, strlen($chars) - 1)];
        }
        // Return standard Bcrypt hash
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}
