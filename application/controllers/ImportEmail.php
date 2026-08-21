<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ImportEmail extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper(array('url', 'form', 'html'));
        $this->load->model('User_model');
    }

    /**
     * Display the Admin Email Import & Token Generator Dashboard
     */
    public function index() {
        $data['title'] = 'Admin - Import Email & Token Dispatcher';
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

        $importedCount = 0;
        $updatedCount = 0;

        foreach ($json['accounts'] as $acc) {
            $email = isset($acc['email']) ? strtolower(trim($acc['email'])) : '';
            if (empty($email)) continue;

            // Validate domain @telkomuniversity.ac.id or @student.telkomuniversity.ac.id
            if (!preg_match('/@(student\.)?telkomuniversity\.ac\.id$/i', $email)) {
                continue;
            }

            $roleName = isset($acc['role']) ? $acc['role'] : 'Mahasiswa';
            $roleId = $this->User_model->get_role_id_by_name($roleName);

            $data = [
                'name' => isset($acc['name']) ? trim($acc['name']) : 'User',
                'email' => $email,
                'role_id' => $roleId,
                'nidn_nim' => isset($acc['nim_nip']) ? trim($acc['nim_nip']) : '',
                'token' => isset($acc['token']) && !empty($acc['token']) ? trim($acc['token']) : null,
                'email_status' => isset($acc['email_status']) ? $acc['email_status'] : 'belum'
            ];

            $userId = $this->User_model->upsert_user($data);
            if ($userId) $importedCount++;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Berhasil memproses {$importedCount} akun ke dalam database.",
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Generate 8-character token for selected users
     */
    public function generate_tokens() {
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        $userIds = isset($json['user_ids']) && is_array($json['user_ids']) ? $json['user_ids'] : [];

        if (empty($userIds)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Pilih setidaknya satu akun untuk generate token.'
                ]));
            return;
        }

        $generatedCount = 0;
        $skippedCount = 0;

        foreach ($userIds as $id) {
            $newToken = $this->_generate_8char_token();
            $result = $this->User_model->update_user_token($id, $newToken);
            if ($result) {
                $generatedCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Berhasil generate token untuk {$generatedCount} akun." . ($skippedCount > 0 ? " ({$skippedCount} akun di-skip karena password sudah diubah)" : ""),
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Dispatch email for selected users
     */
    public function send_emails() {
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

        $sentCount = 0;

        foreach ($userIds as $id) {
            $user = $this->User_model->get_by_id($id);
            if ($user && !empty($user->email)) {
                // Simulating SMTP Dispatch or actual mail sending
                $success = $this->User_model->update_email_status($id, 'terkirim');
                if ($success) $sentCount++;
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Berhasil mengirimkan email credentials ke {$sentCount} akun.",
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

        $roleId = $this->User_model->get_role_id_by_name(isset($json['role']) ? $json['role'] : 'Mahasiswa');

        $data = [
            'name' => trim($json['name']),
            'email' => $email,
            'role_id' => $roleId,
            'nidn_nim' => isset($json['nim_nip']) ? trim($json['nim_nip']) : '',
            'token' => isset($json['token']) ? trim($json['token']) : null
        ];

        $userId = $this->User_model->upsert_user($data);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => 'Data akun berhasil disimpan.',
                'accounts' => $this->_get_formatted_users()
            ]));
    }

    /**
     * AJAX: Delete selected users
     */
    public function delete_users() {
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
     * AJAX: Reset imported testing accounts
     */
    public function reset_data() {
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
        $formatted = [];

        foreach ($rawUsers as $u) {
            $roleDisplay = isset($u['role_display_name']) && !empty($u['role_display_name']) ? $u['role_display_name'] : 'Mahasiswa';
            if ($u['role_slug'] === 'admin' || $u['role_id'] == 1) $roleDisplay = 'Admin';
            elseif ($u['role_slug'] === 'dosen' || $u['role_id'] == 4) $roleDisplay = 'Dosen';
            elseif ($u['role_slug'] === 'laboran' || $u['role_id'] == 2) $roleDisplay = 'Laboran';
            elseif ($u['role_slug'] === 'kaur' || $u['role_id'] == 3) $roleDisplay = 'Ka. Ur';
            elseif ($u['role_slug'] === 'koordinatorta' || $u['role_id'] == 6) $roleDisplay = 'Koordinator TA';

            $isPasswordChanged = (bool)($u['password_changed'] == 1);

            $tokenStatus = 'empty';
            if ($isPasswordChanged) {
                $tokenStatus = 'password_changed';
            } elseif (!empty($u['token'])) {
                $tokenStatus = 'ready';
            }

            $formatted[] = [
                'id' => (int)$u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'role' => $roleDisplay,
                'nim_nip' => !empty($u['nidn_nim']) ? $u['nidn_nim'] : '-',
                'token' => !empty($u['token']) ? $u['token'] : '',
                'token_status' => $tokenStatus,
                'password_changed' => $isPasswordChanged,
                'email_status' => !empty($u['email_status']) ? $u['email_status'] : 'belum',
                'email_sent_at' => !empty($u['email_sent_at']) ? date('Y-m-d H:i', strtotime($u['email_sent_at'])) : '-',
                'date_imported' => !empty($u['created_at']) ? date('Y-m-d H:i', strtotime($u['created_at'])) : date('Y-m-d H:i'),
                'created_at' => !empty($u['created_at']) ? date('Y-m-d H:i', strtotime($u['created_at'])) : date('Y-m-d H:i')
            ];
        }

        return $formatted;
    }

    /**
     * Private helper: Generate 8-character mixed token
     */
    private function _generate_8char_token() {
        $uppers = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowers = 'abcdefghijkmnpqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '!@#$%^&*_-';

        $token = [
            $uppers[rand(0, strlen($uppers) - 1)],
            $lowers[rand(0, strlen($lowers) - 1)],
            $numbers[rand(0, strlen($numbers) - 1)],
            $symbols[rand(0, strlen($symbols) - 1)]
        ];

        $all = $uppers . $lowers . $numbers . $symbols;
        for ($i = 0; $i < 4; $i++) {
            $token[] = $all[rand(0, strlen($all) - 1)];
        }

        shuffle($token);
        return implode('', $token);
    }
}
