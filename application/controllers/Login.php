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

		// Validate domain @telkomuniversity.ac.id or @student.telkomuniversity.ac.id
		if (!preg_match('/@(student\.)?telkomuniversity\.ac\.id$/i', $identity)) {
			$this->session->set_flashdata('error', 'Email harus menggunakan domain @telkomuniversity.ac.id');
			redirect('login');
			return;
		}

		// Fetch user from database
		$user = $this->User_model->get_by_email($identity);

		if ($user && $user->status === 'active') {
			// Verify bcrypt password hash
			if (password_verify($password, $user->password)) {
				// Set session data
				$session_data = array(
					'user_id'          => $user->id,
					'role_id'          => $user->role_id,
					'name'             => $user->name,
					'email'            => $user->email,
					'nidn_nim'         => $user->nidn_nim,
					'nim'              => $user->nidn_nim,
					'status'           => $user->status,
					'password_changed' => (int)$user->password_changed,
					'logged_in'        => TRUE
				);
				$this->session->set_userdata($session_data);

				// If user still uses temporary token (password_changed == 0)
				if ((int)$user->password_changed === 0) {
					$this->session->set_flashdata('warning', 'Akun Anda masih menggunakan password sementara (token). Wajib buat password baru dan lengkapi biodata Anda.');
					redirect('onboarding');
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

	public function logout()
	{
		$this->session->unset_userdata(array('user_id', 'role_id', 'name', 'email', 'nidn_nim', 'status', 'logged_in'));
		$this->session->sess_destroy();
		redirect('login');
	}

	public function forgot_password()
	{
		$this->load->view('auth/forgot_password');
	}

	public function send_reset_link()
	{
		$email = trim($this->input->post('email', true));

		if (!empty($email)) {
			$data['success'] = 'Instruksi reset password telah dikirim ke email ' . htmlspecialchars($email);
			$this->load->view('auth/forgot_password', $data);
		} else {
			$data['error'] = 'Silakan masukkan email yang valid.';
			$this->load->view('auth/forgot_password', $data);
		}
	}

	public function onboarding()
	{
		redirect('onboarding');
	}
}
