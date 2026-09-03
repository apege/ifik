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

    /**
     * Halaman Riwayat Log Approval System
     */
    public function log_history() {
        $this->load->model('Approval_log_model');

        $modul  = $this->input->get('modul', true);
        $action = $this->input->get('action', true);
        $search = $this->input->get('search', true);
        $page   = (int)($this->input->get('page') ?: 1);
        if ($page < 1) $page = 1;
        $per_page = 20;

        $total_logs   = $this->Approval_log_model->count_logs($modul, $action, $search);
        $total_pages  = max(1, ceil($total_logs / $per_page));
        $offset       = ($page - 1) * $per_page;

        $data['title']         = 'Riwayat Log Approval System';
        $data['logs']          = $this->Approval_log_model->get_all_logs($modul, $action, $search, $per_page, $offset);
        $data['stats']         = $this->Approval_log_model->get_log_stats();
        $data['total_logs']    = $total_logs;
        $data['total_pages']   = $total_pages;
        $data['current_page']  = $page;
        $data['page']          = $page;
        $data['per_page']      = $per_page;
        $data['filter_modul']  = $modul;
        $data['filter_action'] = $action;
        $data['search']        = $search;

        $this->load->view('admin/log_history', $data);
    }
}
