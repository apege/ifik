<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminLayanan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Ambil semua daftar pengajuan berkas mahasiswa untuk Admin Layanan
     */
    public function get_all_pengajuan($filter_status = null, $search = null) {
        $has_mhs   = $this->db->table_exists('mahasiswa');
        $has_kk    = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) $select .= ', m.*';
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');

        if ($filter_status && $filter_status !== 'all') {
            $this->db->where('p.status_approval_admin', $filter_status);
        }

        if ($search) {
            $this->db->group_start();
            if ($has_mhs) {
                $this->db->like('m.nama_depan', $search);
                $this->db->or_like('m.nama_belakang', $search);
            }
            $this->db->or_like('p.nim', $search);
            $this->db->or_like('p.judul_1', $search);
            $this->db->group_end();
        }

        $this->db->order_by("CASE 
            WHEN p.status_approval_admin = 'Pending' AND p.status_approval_wali = 'Approved' THEN 1 
            WHEN p.status_approval_admin = 'Rejected' THEN 2 
            WHEN p.status_approval_admin = 'Approved' THEN 3 
            ELSE 4 END", "ASC", false);
        $this->db->order_by('p.created_at', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Hitung statistik berkas untuk kartu metrik Admin Layanan
     */
    public function get_stats() {
        $total = $this->db->count_all_results('pendaftaran_ta');
        
        $pending = $this->db->where('status_approval_admin', 'Pending')
                            ->where('status_approval_wali', 'Approved')
                            ->count_all_results('pendaftaran_ta');
                            
        $approved = $this->db->where('status_approval_admin', 'Approved')
                             ->count_all_results('pendaftaran_ta');
                             
        $rejected = $this->db->where('status_approval_admin', 'Rejected')
                             ->count_all_results('pendaftaran_ta');

        return array(
            'total'    => $total,
            'pending'  => $pending,
            'approved' => $approved,
            'rejected' => $rejected
        );
    }

    /**
     * Ambil detail lengkap pengajuan mahasiswa berdasarkan NIM
     */
    public function get_detail_pengajuan($nim) {
        $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, m.alamat, kk.nama_kk, kk.kode_kk');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'inner');
        $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        $this->db->where('p.nim', $nim);
        
        return $this->db->get()->row_array();
    }

    /**
     * Update hasil validasi berkas dan status approval Admin Layanan
     */
    public function update_verifikasi_berkas($nim, $data_update) {
        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data_update);
    }
}
