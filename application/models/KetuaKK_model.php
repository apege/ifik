<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KetuaKK_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Ambil daftar 4 Kelompok Keahlian
     */
    public function get_all_kk() {
        return $this->db->get('kelompok_keahlian')->result_array();
    }

    /**
     * Ambil daftar mahasiswa pendaftar TA berdasarkan KK dan status
     */
    public function get_mahasiswa_by_kk($id_kk = null, $filter_status = null, $search = null) {
        $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, kk.nama_kk, kk.kode_kk, kk.ketua_kk');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'inner');
        $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');

        if ($id_kk && $id_kk !== 'all') {
            $this->db->where('p.id_kk', (int)$id_kk);
        }

        if ($filter_status && $filter_status !== 'all') {
            $this->db->where('p.status_approval_kk', $filter_status);
        }

        if ($search) {
            $this->db->group_start();
            $this->db->like('m.nama_depan', $search);
            $this->db->or_like('m.nama_belakang', $search);
            $this->db->or_like('p.nim', $search);
            $this->db->or_like('p.judul_1', $search);
            $this->db->group_end();
        }

        $this->db->order_by("CASE 
            WHEN p.status_approval_kk = 'Pending' AND p.status_approval_wali = 'Approved' AND p.status_approval_admin = 'Approved' AND p.status_approval_koor = 'Approved' THEN 1 
            WHEN p.status_approval_kk = 'Approved' THEN 2 
            WHEN p.status_approval_kk = 'Rejected' THEN 3 
            ELSE 4 END", "ASC", false);
        $this->db->order_by('p.created_at', 'DESC');

        return $this->db->get()->result_array();
    }

    /**
     * Hitung statistik pengajuan untuk Ketua KK
     */
    public function get_stats($id_kk = null) {
        if ($id_kk && $id_kk !== 'all') {
            $this->db->where('id_kk', (int)$id_kk);
        }
        $total = $this->db->count_all_results('pendaftaran_ta');

        if ($id_kk && $id_kk !== 'all') {
            $this->db->where('id_kk', (int)$id_kk);
        }
        $ready = $this->db->where('status_approval_wali', 'Approved')
                          ->where('status_approval_admin', 'Approved')
                          ->where('status_approval_koor', 'Approved')
                          ->where('status_approval_kk', 'Pending')
                          ->count_all_results('pendaftaran_ta');

        if ($id_kk && $id_kk !== 'all') {
            $this->db->where('id_kk', (int)$id_kk);
        }
        $approved = $this->db->where('status_approval_kk', 'Approved')
                             ->count_all_results('pendaftaran_ta');

        return array(
            'total'    => $total,
            'ready'    => $ready,
            'approved' => $approved
        );
    }

    /**
     * Detail mahasiswa untuk Ketua KK
     */
    public function get_detail_mahasiswa($nim) {
        $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, m.alamat, kk.nama_kk, kk.kode_kk, kk.ketua_kk, kk.nip_ketua');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'inner');
        $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        $this->db->where('p.nim', $nim);
        
        return $this->db->get()->row_array();
    }

    /**
     * Update Approval Ketua KK & Unlock Tahap Bimbingan
     */
    public function update_approval_kk($nim, $status, $catatan) {
        $is_unlocked = ($status === 'Approved') ? 1 : 0;
        $current_stage = ($status === 'Approved') ? 'Selesai Approval' : 'Ketua KK';

        $data = array(
            'status_approval_kk'   => $status,
            'catatan_kk'           => $catatan,
            'is_bimbingan_unlocked'=> $is_unlocked,
            'current_stage'        => $current_stage
        );

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }
}
