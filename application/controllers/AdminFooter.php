<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminFooter extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Footer_model');
        $this->load->helper(array('form', 'url'));

        // Proteksi Ketat: Hanya Admin System (role_id 1) yang berhak mengakses
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role_id') != 1) {
            $this->session->set_flashdata('error', 'Akses ditolak. Halaman ini hanya diperuntukkan bagi Administrator.');
            redirect('login');
        }
    }

    /**
     * Tampilan Halaman Pengaturan Footer
     */
    public function index()
    {
        $data['title']    = 'Pengaturan Footer — Admin Panel';
        $data['settings'] = $this->Footer_model->get_settings();

        $this->load->view('admin/footer_settings', $data);
    }

    /**
     * Proses Pembaruan Konfigurasi Footer
     */
    public function update_settings()
    {
        $brand_badge   = trim($this->input->post('brand_badge', true));
        $brand_title   = trim($this->input->post('brand_title', true));
        $brand_desc    = trim($this->input->post('brand_desc', true));
        $instagram_url = trim($this->input->post('instagram_url', true));
        $youtube_url   = trim($this->input->post('youtube_url', true));
        $linkedin_url  = trim($this->input->post('linkedin_url', true));
        $alamat_kampus = trim($this->input->post('alamat_kampus', true));
        $email_resmi   = trim($this->input->post('email_resmi', true));
        $telepon       = trim($this->input->post('telepon', true));
        $maps_embed_url= trim($this->input->post('maps_embed_url', false)); // Mengizinkan karakter embed Google Maps
        $maps_link_url = trim($this->input->post('maps_link_url', true));
        $copyright_text= trim($this->input->post('copyright_text', true));

        // Jika maps_embed_url diinput lengkap dengan tag <iframe src="...">, ekstrak link src-nya saja
        if (preg_match('/src=["\']([^"\']+)["\']/', $maps_embed_url, $match)) {
            $maps_embed_url = $match[1];
        }

        $data_update = [
            'brand_badge'    => !empty($brand_badge) ? $brand_badge : 'TELKOM UNIVERSITY',
            'brand_title'    => !empty($brand_title) ? $brand_title : 'Fakultas Industri Kreatif',
            'brand_desc'     => $brand_desc,
            'instagram_url'  => $instagram_url,
            'youtube_url'    => $youtube_url,
            'linkedin_url'   => $linkedin_url,
            'alamat_kampus'  => $alamat_kampus,
            'email_resmi'    => $email_resmi,
            'telepon'        => $telepon,
            'maps_embed_url' => $maps_embed_url,
            'maps_link_url'  => $maps_link_url,
            'copyright_text' => $copyright_text
        ];

        if ($this->Footer_model->update_settings($data_update)) {
            $this->session->set_flashdata('success', 'Konfigurasi konten footer berhasil diperbarui!');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui konfigurasi footer.');
        }

        redirect('adminfooter');
    }

    /**
     * Reset Konfigurasi Footer ke Nilai Default Bawaan
     */
    public function reset_defaults()
    {
        if ($this->Footer_model->reset_defaults()) {
            $this->session->set_flashdata('success', 'Konten footer berhasil direset ke pengaturan default bawaan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mereset pengaturan footer.');
        }

        redirect('adminfooter');
    }
}
