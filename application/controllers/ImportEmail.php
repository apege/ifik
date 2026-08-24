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
                if (isset($item['id'])) {
                    $updates[] = [
                        'id' => (int)$item['id'],
                        'token' => !empty($item['token']) ? $item['token'] : $this->_generate_8char_token()
                    ];
                }
            }
        }
        // Format 2: Single user_id and token
        elseif (isset($json['user_id'])) {
            $updates[] = [
                'id' => (int)$json['user_id'],
                'token' => !empty($json['token']) ? $json['token'] : $this->_generate_8char_token()
            ];
        }
        // Format 3: user_ids array
        elseif (isset($json['user_ids']) && is_array($json['user_ids'])) {
            foreach ($json['user_ids'] as $id) {
                $updates[] = [
                    'id' => (int)$id,
                    'token' => $this->_generate_8char_token()
                ];
            }
        }

        if (empty($updates)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Tidak ada akun yang valid untuk di-generate tokennya.'
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

        $sentCount = 0;
        $skippedProtectedCount = 0;
        $noTokenCount = 0;
        $templateSubject = isset($json['subject']) ? $json['subject'] : '[IFIK Telkom University] Token Akses Portal Akun Anda: {TOKEN}';
        $templateBody = isset($json['body']) ? $json['body'] : '';

        foreach ($userIds as $id) {
            $user = $this->User_model->get_by_id($id);
            if (!$user || empty($user->email)) {
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
                [$user->name, $this->_get_role_name_by_id($user->role_id), $user->nidn_nim, $user->email, $token],
                $templateSubject
            );

            // Send via SMTP
            $this->_send_smtp_email($user->email, $subject, $htmlMessage);

            // Update database status
            $this->User_model->update_email_status($id, 'terkirim');
            $sentCount++;
        }

        $extraInfo = [];
        if ($skippedProtectedCount > 0) $extraInfo[] = "{$skippedProtectedCount} akun protected dilewati";
        if ($noTokenCount > 0) $extraInfo[] = "{$noTokenCount} akun dilewati karena belum di-generate tokennya";
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
        $templateSubject = isset($json['subject']) ? $json['subject'] : '[IFIK Telkom University] Token Akses Portal Akun Anda: {TOKEN}';
        $templateBody = isset($json['body']) ? $json['body'] : '';

        $htmlMessage = $this->_build_html_email($user, $token, $templateSubject, $templateBody);
        $subject = str_replace(
            ['{NAMA}', '{ROLE}', '{NIM_NIP}', '{EMAIL}', '{TOKEN}'],
            [$user->name, $this->_get_role_name_by_id($user->role_id), $user->nidn_nim, $user->email, $token],
            $templateSubject
        );

        $this->_send_smtp_email($user->email, $subject, $htmlMessage);
        $this->User_model->update_email_status($userId, 'terkirim');

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'message' => "Email berisi token akses berhasil dikirimkan ke {$user->email}.",
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
     * Helper: Get role display name by role_id
     */
    private function _get_role_name_by_id($roleId) {
        $roles = [
            1 => 'Admin',
            2 => 'Laboran',
            3 => 'Ka. Ur',
            4 => 'Dosen',
            5 => 'Mahasiswa',
            6 => 'Koordinator TA'
        ];
        return isset($roles[(int)$roleId]) ? $roles[(int)$roleId] : 'Mahasiswa';
    }

    /**
     * Helper: Build branded responsive HTML email template
     */
    private function _build_html_email($user, $token, $subject, $bodyTemplate = '') {
        $portalUrl = site_url('login');
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
    <title>Token Akses Akun Portal IFIK</title>
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
                            <p style="margin: 0 0 20px 0; font-size: 14px; color: #475569;">Akun Anda telah berhasil didaftarkan ke dalam sistem Portal Layanan IFIK Telkom University sebagai <strong style="color: #ea580c;">' . $role . '</strong>.</p>
                            <p style="margin: 0 0 18px 0; font-size: 14px; color: #475569;">Berikut adalah <strong>Kode Token Akses 8-Karakter</strong> unik untuk aktivasi awal akun Anda:</p>
                            
                            <!-- Highlight Token Box -->
                            <div style="background-color: #fff7ed; border: 2px dashed #fb923c; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0;">
                                <span style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #9a3412; letter-spacing: 1.5px; margin-bottom: 8px;">KODE TOKEN AKSES ANDA</span>
                                <span style="font-family: Consolas, Monaco, monospace; font-size: 28px; font-weight: 900; color: #ea580c; letter-spacing: 4px; display: inline-block; background-color: #ffffff; padding: 6px 20px; border-radius: 8px; border: 1px solid #fed7aa;">' . $token . '</span>
                            </div>

                            <!-- Account Details Table -->
                            <table width="100%" style="background-color: #f8fafc; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; font-size: 13px; border: 1px solid #e2e8f0;" cellpadding="0" cellspacing="0">
                                <tr><td style="color: #64748b; padding: 5px 0; width: 35%;">NIM / NIP:</td><td style="color: #0f172a; font-weight: 600;">' . $nim . '</td></tr>
                                <tr><td style="color: #64748b; padding: 5px 0;">Email Resmi:</td><td style="color: #0f172a; font-weight: 600;">' . $email . '</td></tr>
                                <tr><td style="color: #64748b; padding: 5px 0;">Peran (Role):</td><td style="color: #0f172a; font-weight: 600;">' . $role . '</td></tr>
                            </table>

                            <p style="margin: 0 0 24px 0; font-size: 13px; color: #64748b; line-height: 1.5;">Gunakan email resmi Anda dan <strong>Kode Token</strong> di atas sebagai password awal saat pertama kali login. Setelah berhasil masuk, Anda akan diarahkan untuk melengkapi biodata dan membuat kata sandi baru.</p>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 30px 0 10px 0;">
                                <a href="' . $portalUrl . '" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 13px 34px; border-radius: 10px; box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);">Masuk & Aktivasi Akun &rarr;</a>
                            </div>
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

            $fromEmail = getenv('SMTP_USER') ? getenv('SMTP_USER') : 'no-reply@telkomuniversity.ac.id';
            $this->email->from($fromEmail, 'Portal Layanan IFIK Telkom University');
            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->message($htmlMessage);

            // Attempt SMTP Dispatch
            $sent = @$this->email->send();
            return $sent;
        } catch (Exception $e) {
            log_message('error', 'SMTP Dispatch Error to ' . $to . ': ' . $e->getMessage());
            return false;
        }
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
