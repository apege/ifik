<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_hook {

    /**
     * Check if user is logged in with token password (password_changed == 0)
     * Enforce redirection to /onboarding until password is changed and biodata is submitted.
     */
    public function check_onboarding_status()
    {
        $CI =& get_instance();
        
        // Ensure session library is accessible
        if (!isset($CI->session)) {
            $CI->load->library('session');
        }

        if ($CI->session->userdata('logged_in')) {
            $passwordChanged = $CI->session->userdata('password_changed');
            $class = strtolower($CI->router->fetch_class());
            $method = strtolower($CI->router->fetch_method());

            // If user still uses initial token password (password_changed == 0)
            if ($passwordChanged === 0 || $passwordChanged === '0' || $passwordChanged === false) {
                // Allowed controllers/methods while on token password:
                $allowed = (
                    ($class === 'onboarding') ||
                    ($class === 'login' && in_array($method, ['onboarding', 'logout', 'process_biodata', 'process_password']))
                );

                if (!$allowed) {
                    $CI->session->set_flashdata('warning', 'Akun Anda masih menggunakan token sementara. Wajib ubah password dan lengkapi biodata terlebih dahulu.');
                    redirect('onboarding');
                }
            } else {
                // If password IS ALREADY changed, prevent them from accessing onboarding page
                if ($class === 'onboarding' || ($class === 'login' && $method === 'onboarding')) {
                    redirect('dashboard');
                }
            }
        }
    }
}
