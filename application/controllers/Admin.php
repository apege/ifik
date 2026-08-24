<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AdminLayanan_model');
        $this->load->model('Booking_model');
        $this->load->model('News_model');
        $this->load->model('KetuaKK_model');
        $this->load->helper(array('form', 'url', 'text'));
        $this->load->library('session');
    }

    /**
     * Central Admin Panel Hub
     */
    public function index() {
        $data['title']       = 'Central Admin Panel - IFIK Portal';
        
        // Metrik LAA
        $data['laa_stats']   = $this->db->table_exists('pendaftaran_ta') 
                               ? $this->AdminLayanan_model->get_stats() 
                               : ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        
        // Metrik Berita
        $data['total_news']  = $this->db->table_exists('berita') ? $this->db->count_all_results('berita') : 0;
        
        // Metrik Booking
        $data['total_booking'] = $this->db->table_exists('peminjaman') ? $this->db->count_all_results('peminjaman') : 0;
        $data['total_ruangan'] = $this->db->table_exists('ruangan') ? $this->db->count_all_results('ruangan') : 0;
        
        // Metrik Mahasiswa TA
        $data['total_mhs_ta']  = $this->db->table_exists('pendaftaran_ta') ? $this->db->count_all_results('pendaftaran_ta') : 0;
        $data['ta_unlocked']   = ($this->db->table_exists('pendaftaran_ta') && $this->db->field_exists('is_bimbingan_unlocked', 'pendaftaran_ta')) 
                               ? $this->db->where('is_bimbingan_unlocked', 1)->count_all_results('pendaftaran_ta') 
                               : 0;

        // Quick recent activities
        if ($this->db->table_exists('pendaftaran_ta')) {
            $data['recent_pengajuan'] = $this->AdminLayanan_model->get_all_pengajuan('all', null);
            $data['recent_pengajuan'] = array_slice($data['recent_pengajuan'], 0, 5);
        } else {
            $data['recent_pengajuan'] = [];
        }

        $this->load->view('admin/dashboard', $data);
    }
}
