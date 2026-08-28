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
        if (!$this->db->table_exists('kelompok_keahlian')) {
            return array();
        }
        $query = $this->db->get('kelompok_keahlian');
        return $query ? $query->result_array() : array();
    }

    /**
     * Hitung total data mahasiswa pendaftar TA berdasarkan KK, status, dan pencarian
     */
    public function get_count_mahasiswa_by_kk($id_kk = null, $filter_status = null, $search = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return 0;
        }

        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');

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

        return $this->db->count_all_results();
    }

    /**
     * Ambil daftar mahasiswa pendaftar TA berdasarkan KK dan status (dengan Paging / Limit Offset)
     */
    public function get_mahasiswa_by_kk($id_kk = null, $filter_status = null, $search = null, $limit = 5, $offset = 0) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $has_kk_table = $this->db->table_exists('kelompok_keahlian');

        if ($has_kk_table) {
            $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, kk.nama_kk, kk.kode_kk, kk.ketua_kk');
        } else {
            $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp');
        }

        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        
        if ($has_kk_table) {
            $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        }

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

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    /**
     * Autocomplete Search untuk pencarian langsung Ketua KK
     */
    public function autocomplete_search($term, $id_kk = null) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($term)) {
            return array();
        }

        $this->db->select('p.nim, p.judul_1, p.status_approval_wali, p.status_approval_admin, p.status_approval_koor, m.nama_depan, m.nama_belakang, m.konsentrasi_dkv');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');

        if ($id_kk && $id_kk !== 'all') {
            $this->db->where('p.id_kk', (int)$id_kk);
        }

        $this->db->group_start();
        $this->db->like('m.nama_depan', $term);
        $this->db->or_like('m.nama_belakang', $term);
        $this->db->or_like('p.nim', $term);
        $this->db->or_like('p.judul_1', $term);
        $this->db->group_end();

        $this->db->limit(8);
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    /**
     * Hitung statistik pengajuan untuk Ketua KK
     */
    public function get_stats($id_kk = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('total' => 0, 'ready' => 0, 'approved' => 0);
        }

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
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $has_kk_table = $this->db->table_exists('kelompok_keahlian');
        $has_nip_ketua = $has_kk_table && $this->db->field_exists('nip_ketua', 'kelompok_keahlian');

        if ($has_kk_table) {
            $select_str = 'p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, m.alamat, kk.nama_kk, kk.kode_kk, kk.ketua_kk';
            if ($has_nip_ketua) {
                $select_str .= ', kk.nip_ketua';
            }
            $this->db->select($select_str);
        } else {
            $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, m.alamat');
        }

        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        
        if ($has_kk_table) {
            $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        }

        $this->db->where('p.nim', $nim);
        
        $query = $this->db->get();
        if (!$query) {
            return null;
        }

        $row = $query->row_array();

        // Fallback jika tidak ditemukan di pendaftaran_ta tetapi ada di tabel mahasiswa
        if (!$row && $this->db->table_exists('mahasiswa')) {
            $this->db->where('nim', $nim);
            $qMhs = $this->db->get('mahasiswa');
            if ($qMhs) {
                $row = $qMhs->row_array();
            }
        }

        return $row;
    }

    /**
     * Update Approval Ketua KK & Unlock Tahap Bimbingan
     */
    public function update_approval_kk($nim, $status, $catatan) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return false;
        }

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
