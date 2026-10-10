<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
	}

	public function index()
	{
		// If user is already logged in, redirect based on password_changed state
		if ($this->session->userdata('logged_in')) {
			if ((int)$this->session->userdata('password_changed') === 0) {
				redirect('onboarding');
			} else {
				redirect('dashboard');
			}
			return;
		}
		$this->load->view('auth/login');
	}

	public function authenticate()
	{
		$identity = trim($this->input->post('identity', true));
		$password = $this->input->post('password');

		if (empty($identity) || empty($password)) {
			$this->session->set_flashdata('error', 'Silakan masukkan Email dan Password yang valid.');
			redirect('login');
			return;
		}

		// Fetch user from database
		$user = $this->User_model->get_by_email($identity);

		if ($user) {
			$isPasswordValid = false;
			$isTokenLogin = false;
			$shouldUpgradeHash = false;

			// 1. Double-Layer Verification (Salt + Bcrypt Combined / Peppered Bcrypt)
			// Sesuai standar keamanan: password diverifikasi bersama salt unik user: password_verify($password . $salt, $hash)
			if (!empty($user->salt) && password_verify($password . $user->salt, $user->password)) {
				$isPasswordValid = true;
			}

			// 2. Standard Bcrypt Hash (Plain Bcrypt)
			if (!$isPasswordValid && password_verify($password, $user->password)) {
				$isPasswordValid = true;
			}

			// 2. Legacy SHA-256 with Salt (database import format: sha256(password + salt))
			if (!$isPasswordValid && !empty($user->salt)) {
				if (hash('sha256', $password . $user->salt) === $user->password || hash('sha256', $user->salt . $password) === $user->password) {
					$isPasswordValid = true;
					$shouldUpgradeHash = true;
				}
			}

			// 3. Legacy SHA-256 Unsalted
			if (!$isPasswordValid && hash('sha256', $password) === $user->password) {
				$isPasswordValid = true;
				$shouldUpgradeHash = true;
			}

			// 4. Fallback if bcrypt hash was stored in salt column (swapped data)
			if (!$isPasswordValid && !empty($user->salt) && (strpos($user->salt, '$2y$') === 0 || strpos($user->salt, '$2a$') === 0 || strpos($user->salt, '$2b$') === 0)) {
				if (password_verify($password, $user->salt)) {
					$isPasswordValid = true;
					$shouldUpgradeHash = true;
				}
			}

			// 5. Direct plaintext match for legacy/unhashed password
			if (!$isPasswordValid && $password === $user->password) {
				$isPasswordValid = true;
				$shouldUpgradeHash = true;
			}

			// 6. Check user_token / user_tokens table for activation token
			if (!$isPasswordValid) {
				$tokenTables = ['user_token', 'user_tokens'];
				foreach ($tokenTables as $tTbl) {
					if ($this->db->table_exists($tTbl)) {
						$tokenRow = $this->db->where('LOWER(email)', strtolower($user->email))->get($tTbl)->row();
						if ($tokenRow && !empty($tokenRow->token)) {
							if (trim($password) === trim($tokenRow->token) || password_verify($password, $tokenRow->token)) {
								$isPasswordValid = true;
								$isTokenLogin = true;
								break;
							}
						}
					}
				}
			}

			// 7. Check user.token property / column
			if (!$isPasswordValid && !empty($user->token)) {
				if (trim($password) === trim($user->token) || password_verify($password, $user->token)) {
					$isPasswordValid = true;
					$isTokenLogin = true;
				}
			}

			if ($isPasswordValid) {
				// Auto-upgrade legacy hashes to modern secure bcrypt in database
				if ($shouldUpgradeHash) {
					$userTbl = $this->db->table_exists('user') ? 'user' : 'users';
					$newHash = password_hash($password, PASSWORD_DEFAULT);
					$newSalt = bin2hex(random_bytes(16));
					$this->db->where('id', $user->id)->update($userTbl, [
						'password' => $newHash,
						'salt'     => $newSalt
					]);
				}
				// Master accounts are strictly identified by their designated seeder IDs
				$masterIds = ['admin-01', 'admin-laa-01', 'dsn-wali-01', 'kaur-01', 'koor-ta-01', 'laboran-01', 'ketua-kk-01', 'mhs-1301210001', 'super-admin-01'];
				$isMasterAccount = in_array($user->id, $masterIds);

				$passwordChanged = $isMasterAccount ? 1 : ($isTokenLogin ? 0 : (int)$user->password_changed);

				// Set session data (Fully synchronized with supervisor session standard)
				$session_data = array(
					'id'               => $user->id,
					'user_id'          => $user->id,
					'username'         => !empty($user->username) ? $user->username : (!empty($user->nidn_nim) ? $user->nidn_nim : $user->email),
					'role_id'          => $user->role_id,
					'name'             => $user->name,
					'email'            => $user->email,
					'nidn_nim'         => $user->nidn_nim,
					'nim'              => $user->nidn_nim,
					'status'           => 'active',
					'koordinator'      => isset($user->koordinator) ? $user->koordinator : '',
					'dosen_wali'       => isset($user->dosen_wali) ? $user->dosen_wali : '',
					'prodi'            => isset($user->prodi) ? $user->prodi : '',
					'password_changed' => $passwordChanged,
					'logged_in'        => TRUE
				);
				$this->session->set_userdata($session_data);

				// If user logged in using temporary token and is NOT a master account
				if (!$isMasterAccount && ($isTokenLogin || $passwordChanged === 0)) {
					$this->session->set_flashdata('warning', 'Akun Anda masih menggunakan password sementara (token). Wajib buat password baru dan lengkapi biodata Anda.');
					redirect('onboarding');
					return;
				}

				// If Super Admin (role 22), redirect directly to Import Email & Token
				if ((int)$user->role_id === 22) {
					redirect('import-email');
					return;
				}

				// Redirect to dedicated unit ticketing panel
				if ((int)$user->role_id === 17) {
					redirect('kemahasiswaan/respon-ticketing');
					return;
				}
				if ((int)$user->role_id === 11) {
					redirect('sekretariat/respon-ticketing');
					return;
				}
				if ((int)$user->role_id === 15) {
					redirect('sdmkeuangan/respon-ticketing');
					return;
				}
				if ((int)$user->role_id === 16) {
					redirect('prodi/respon-ticketing');
					return;
				}

				// Redirect to dashboard
				redirect('dashboard');
				return;
			}
		}

		// Invalid credentials or inactive status
		$this->session->set_flashdata('error', 'Email atau password salah.');
		redirect('login');
	}

	/**
	 * 1-Click Direct Login / Activation Link from Email
	 */
	public function activate()
	{
		$email = strtolower(trim((string)$this->input->get('email', true)));
		$token = trim((string)$this->input->get('token', true));

		// Fallback if webserver (Nginx/Apache proxy) strips query string into $_GET
		if (empty($email) || empty($token)) {
			$queryString = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
			if (empty($queryString) && !empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '?') !== false) {
				$queryString = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') + 1);
			}
			if (!empty($queryString)) {
				parse_str($queryString, $queryParams);
				if (empty($email) && !empty($queryParams['email'])) {
					$email = strtolower(trim($queryParams['email']));
				}
				if (empty($token) && !empty($queryParams['token'])) {
					$token = trim($queryParams['token']);
				}
			}
		}

		if (empty($email) || empty($token)) {
			$this->session->set_flashdata('error', 'Tautan aktivasi tidak valid atau parameter tidak lengkap.');
			redirect('login');
			return;
		}

		$user = $this->User_model->get_by_email($email);
		if (!$user) {
			$this->session->set_flashdata('error', 'Akun dengan email ' . htmlspecialchars($email) . ' tidak ditemukan di sistem.');
			redirect('login');
			return;
		}

		// Verify token against user_token / user_tokens table or user.token or user.password
		$isValidToken = false;
		$tokenTables = ['user_token', 'user_tokens'];
		foreach ($tokenTables as $tTbl) {
			if ($this->db->table_exists($tTbl)) {
				$tokenRow = $this->db->where('LOWER(email)', $email)->get($tTbl)->row();
				if ($tokenRow && !empty($tokenRow->token)) {
					if (trim($token) === trim($tokenRow->token) || password_verify($token, $tokenRow->token)) {
						$isValidToken = true;
						break;
					}
				}
			}
		}
		if (!$isValidToken && !empty($user->token)) {
			if (trim($token) === trim($user->token) || password_verify($token, $user->token)) {
				$isValidToken = true;
			}
		}
		// Additional fallback if user inserted token into password field directly
		if (!$isValidToken && !empty($user->password)) {
			if (trim($token) === trim($user->password) || password_verify($token, $user->password)) {
				$isValidToken = true;
			}
		}

		if (!$isValidToken) {
			$this->session->set_flashdata('error', 'Token aktivasi tidak cocok atau sudah tidak berlaku.');
			redirect('login');
			return;
		}

		// Direct 1-Click Login: set active session!
		$session_data = array(
			'id'               => $user->id,
			'user_id'          => $user->id,
			'username'         => !empty($user->username) ? $user->username : (!empty($user->nidn_nim) ? $user->nidn_nim : $user->email),
			'role_id'          => $user->role_id,
			'name'             => $user->name,
			'email'            => $user->email,
			'nidn_nim'         => $user->nidn_nim,
			'nim'              => $user->nidn_nim,
			'status'           => 'active',
			'koordinator'      => isset($user->koordinator) ? $user->koordinator : '',
			'dosen_wali'       => isset($user->dosen_wali) ? $user->dosen_wali : '',
			'prodi'            => isset($user->prodi) ? $user->prodi : '',
			'password_changed' => 0, // Directs to onboarding to setup password
			'logged_in'        => TRUE
		);
		$this->session->set_userdata($session_data);

		$this->session->set_flashdata('success', 'Aktivasi berhasil! Selamat datang, ' . htmlspecialchars($user->name) . '. Silakan buat password baru dan lengkapi biodata Anda.');
		redirect('onboarding');
	}

	public function logout()
	{
		$this->session->unset_userdata(array('id', 'user_id', 'username', 'role_id', 'name', 'email', 'nidn_nim', 'nim', 'koordinator', 'dosen_wali', 'prodi', 'status', 'password_changed', 'logged_in'));
		$this->session->sess_destroy();
		redirect('login');
	}

	public function forgot_password()
	{
		$this->load->view('auth/forgot_password');
	}

	public function send_reset_link()
	{
		$email = strtolower(trim($this->input->post('email', true)));

		if (empty($email)) {
			$this->session->set_flashdata('error', 'Silakan masukkan alamat email yang valid.');
			redirect('login/forgot_password');
			return;
		}

		// Validate email format
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->session->set_flashdata('error', 'Format email tidak valid.');
			redirect('login/forgot_password');
			return;
		}

		$user = $this->User_model->get_by_email($email);

		if (!$user) {
			$this->session->set_flashdata('error', 'Alamat email "' . htmlspecialchars($email) . '" tidak ditemukan atau belum terdaftar di sistem.');
			redirect('login/forgot_password');
			return;
		}

		if ($user->status !== 'active') {
			$this->session->set_flashdata('error', 'Akun dengan email "' . htmlspecialchars($email) . '" berstatus non-aktif. Silakan hubungi Administrator.');
			redirect('login/forgot_password');
			return;
		}

		// Anti-Spam / Rate Limit Protection (Cooldown 60 detik, Max 3x per 15 menit)
		$rateLimit = $this->User_model->check_reset_rate_limit($email, 60, 3, 900);
		if ($rateLimit['limited']) {
			if ($rateLimit['reason'] === 'cooldown') {
				$this->session->set_flashdata('error', 'Mohon tunggu ' . $rateLimit['remaining'] . ' detik sebelum meminta tautan pemulihan kata sandi kembali.');
			} else {
				$this->session->set_flashdata('error', 'Terlalu banyak permintaan reset password untuk email ini. Harap tunggu beberapa saat sebelum mencoba kembali.');
			}
			redirect('login/forgot_password');
			return;
		}

		// Generate secure 32-character random token
		$token = bin2hex(random_bytes(16));
		$this->User_model->set_reset_token($email, $token);

		// Build secure reset URL
		$resetLink = base_url('login/reset_password?token=' . $token . '&email=' . urlencode($email));

		// Load CodeIgniter Email library
		$this->load->library('email');
		$this->email->clear(TRUE);
		$this->email->from('apgchannel11@gmail.com', 'IFIK Labs Portal — Telkom University');
		$this->email->to($email);
		$this->email->subject('Permintaan Reset Password Akun — IFIK Labs Portal');

		$htmlBody = $this->_build_reset_email_template($user->name, $resetLink);
		$this->email->message($htmlBody);

		if ($this->email->send()) {
			$this->session->set_flashdata('success', 'Tautan pemulihan kata sandi telah berhasil dikirimkan ke ' . htmlspecialchars($email) . ' (berlaku selama 15 menit). Silakan periksa Inbox atau folder Spam Anda.');
		} else {
			log_message('error', 'Gagal mengirim email reset password: ' . $this->email->print_debugger(['headers']));
			$this->session->set_flashdata('error', 'Gagal mengirim email pemulihan. Silakan periksa koneksi internet Anda atau coba beberapa saat lagi.');
		}

		redirect('login/forgot_password');
	}

	public function reset_password()
	{
		$token = trim($this->input->get('token', true));
		$email = strtolower(trim($this->input->get('email', true)));

		if (empty($token) || empty($email)) {
			$this->session->set_flashdata('error', 'Tautan reset password tidak valid atau tidak lengkap.');
			redirect('login/forgot_password');
			return;
		}

		// Verify token against database
		$user = $this->User_model->verify_reset_token($email, $token);

		if (!$user) {
			$this->session->set_flashdata('error', 'Tautan reset password sudah kedaluwarsa atau tidak valid. Silakan ajukan tautan baru.');
			redirect('login/forgot_password');
			return;
		}

		$data['token'] = $token;
		$data['email'] = $email;
		$data['user'] = $user;

		$this->load->view('auth/reset_password', $data);
	}

	public function process_reset_password()
	{
		$token = trim($this->input->post('token', true));
		$email = strtolower(trim($this->input->post('email', true)));
		$passwordBaru = $this->input->post('password_baru');
		$konfirmasiPassword = $this->input->post('konfirmasi_password');

		if (empty($token) || empty($email)) {
			$this->session->set_flashdata('error', 'Permintaan reset password tidak valid.');
			redirect('login/forgot_password');
			return;
		}

		// Verify token against database
		$user = $this->User_model->verify_reset_token($email, $token);

		if (!$user) {
			$this->session->set_flashdata('error', 'Tautan reset password sudah kedaluwarsa atau tidak valid.');
			redirect('login/forgot_password');
			return;
		}

		// Validate password length
		if (empty($passwordBaru) || strlen($passwordBaru) < 6) {
			$this->session->set_flashdata('error', 'Password baru minimal 6 karakter!');
			redirect('login/reset_password?token=' . $token . '&email=' . urlencode($email));
			return;
		}

		// Validate password confirmation
		if ($passwordBaru !== $konfirmasiPassword) {
			$this->session->set_flashdata('error', 'Konfirmasi password tidak cocok dengan password baru!');
			redirect('login/reset_password?token=' . $token . '&email=' . urlencode($email));
			return;
		}

		// Hash new password and update
		$hashedPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);
		$success = $this->User_model->reset_password_by_token($email, $token, $hashedPassword);

		if ($success) {
			$this->session->set_flashdata('success', 'Password akun Anda berhasil diperbarui! Silakan masuk dengan password baru Anda.');
			redirect('login');
		} else {
			$this->session->set_flashdata('error', 'Gagal memperbarui password. Silakan coba kembali.');
			redirect('login/reset_password?token=' . $token . '&email=' . urlencode($email));
		}
	}

	/**
	 * Build responsive HTML Email template for Password Reset
	 */
	private function _build_reset_email_template($userName, $resetLink)
	{
		return '
		<!DOCTYPE html>
		<html lang="id">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>Reset Password — IFIK Labs Portal</title>
		</head>
		<body style="margin:0; padding:0; background-color:#f8f9fa; font-family:\'Plus Jakarta Sans\', Arial, sans-serif; color:#1c0a00;">
			<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f8f9fa; padding:30px 15px;">
				<tr>
					<td align="center">
						<table role="presentation" width="100%" max-width="580" style="max-width:580px; background:#ffffff; border-radius:18px; border:1px solid #fed7aa; box-shadow:0 10px 30px rgba(180,83,9,0.08); overflow:hidden;">
							<!-- Header -->
							<tr>
								<td style="background:linear-gradient(135deg, #78350f 0%, #b45309 100%); padding:28px 30px; text-align:center;">
									<h1 style="color:#ffffff; margin:0; font-size:20px; font-weight:800; letter-spacing:0.5px;">IFIK LABS PORTAL</h1>
									<p style="color:#fed7aa; margin:4px 0 0 0; font-size:13px;">Fakultas Industri Kreatif — Telkom University</p>
								</td>
							</tr>
							<!-- Body -->
							<tr>
								<td style="padding:32px 30px;">
									<h2 style="font-size:18px; color:#1c0a00; margin:0 0 14px 0;">Halo, ' . htmlspecialchars($userName) . '</h2>
									<p style="font-size:14px; color:#4a1c03; line-height:1.6; margin:0 0 20px 0;">
										Kami menerima permintaan untuk mereset kata sandi akun IFIK Labs Portal Anda. Silakan klik tombol di bawah ini untuk membuat kata sandi baru:
									</p>
									<div style="text-align:center; margin:28px 0;">
										<a href="' . $resetLink . '" style="display:inline-block; background:linear-gradient(135deg, #f59e0b 0%, #b45309 100%); color:#ffffff; text-decoration:none; font-size:15px; font-weight:700; padding:14px 32px; border-radius:12px; box-shadow:0 4px 15px rgba(245,158,11,0.4);">
											Atur Ulang Kata Sandi &rarr;
										</a>
									</div>
									<p style="font-size:13px; color:#78350f; line-height:1.5; margin:0 0 12px 0;">
										Jika tombol di atas tidak dapat diklik, salin dan tempel tautan berikut ke browser Anda:
									</p>
									<p style="font-size:12px; color:#b45309; word-break:break-all; background:#fffbeb; padding:10px 14px; border-radius:8px; border:1px dashed #fde68a; margin:0 0 24px 0;">
										<a href="' . $resetLink . '" style="color:#b45309; text-decoration:underline;">' . $resetLink . '</a>
									</p>
									<hr style="border:none; border-top:1px solid #fed7aa; margin:24px 0;">
									<p style="font-size:12px; color:#9a3412; line-height:1.5; margin:0;">
										<strong>⚠️ Catatan Keamanan:</strong> Tautan ini bersifat rahasia dan hanya berlaku selama <strong>15 menit</strong>. Jika Anda tidak pernah meminta perubahan kata sandi, abaikan email ini dan akun Anda akan tetap aman.
									</p>
								</td>
							</tr>
							<!-- Footer -->
							<tr>
								<td style="background:#fff7ed; padding:18px 30px; text-align:center; border-top:1px solid #ffedd5;">
									<p style="font-size:11px; color:#9a3412; margin:0;">
										&copy; 2025 Fakultas Industri Kreatif &mdash; Telkom University. All rights reserved.
									</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
		</html>';
	}

	public function onboarding()
	{
		redirect('onboarding');
	}
}

