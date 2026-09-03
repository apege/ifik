<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_log_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->_ensure_table();
    }

    /**
     * Pastikan tabel log_approval_history ada di database
     */
    private function _ensure_table() {
        if (!$this->db->table_exists('log_approval_history')) {
            $sql = "CREATE TABLE IF NOT EXISTS `log_approval_history` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `modul` VARCHAR(100) NOT NULL,
                `ref_id` VARCHAR(100) NOT NULL,
                `target_name` VARCHAR(255) NULL,
                `action` VARCHAR(50) NOT NULL,
                `actor_id` INT NULL,
                `actor_name` VARCHAR(150) NOT NULL,
                `actor_role` VARCHAR(100) NOT NULL,
                `actor_nip_nim` VARCHAR(50) NULL,
                `catatan` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        }
    }

    /**
     * Catat log persetujuan / penolakan baru
     * 
     * @param array $data 
     *   - modul (string, misal: 'Dosen Wali', 'Admin Layanan', 'Koordinator TA', 'Ketua KK', 'Peminjaman Ruangan')
     *   - ref_id (string, misal: NIM mahasiswa atau ID booking)
     *   - target_name (string, opsional, nama mahasiswa atau nama peminjam/ruangan)
     *   - action (string, misal: 'Approved', 'Rejected', 'Reset', 'Approve All')
     *   - actor_id (int, opsional)
     *   - actor_name (string, opsional)
     *   - actor_role (string, opsional)
     *   - actor_nip_nim (string, opsional)
     *   - catatan (string, opsional)
     * @return bool
     */
    public function log($data) {
        $this->_ensure_table();

        $modul       = $data['modul'] ?? 'Umum';
        $ref_id      = $data['ref_id'] ?? '';
        $target_name = $data['target_name'] ?? NULL;
        $action      = $data['action'] ?? 'Approved';
        $catatan     = $data['catatan'] ?? NULL;

        // Ambil data aktor dari session jika tidak disediakan secara eksplisit
        $actor_id      = $data['actor_id'] ?? $this->session->userdata('user_id');
        $actor_name    = $data['actor_name'] ?? $this->session->userdata('name');
        $actor_nip_nim = $data['actor_nip_nim'] ?? ($this->session->userdata('nidn_nim') ?: ($this->session->userdata('nip') ?: $this->session->userdata('nim')));
        
        $actor_role    = $data['actor_role'] ?? NULL;
        if (empty($actor_role)) {
            $role_id = $this->session->userdata('role_id');
            switch ($role_id) {
                case 1: $actor_role = 'Admin Panel'; break;
                case 2: $actor_role = 'Laboran'; break;
                case 3: $actor_role = 'Ka. Ur'; break;
                case 4: $actor_role = 'Dosen'; break;
                case 5: $actor_role = 'Mahasiswa'; break;
                case 6: $actor_role = 'Koordinator TA'; break;
                default: $actor_role = $modul; break;
            }
        }

        if (empty($actor_name)) {
            $actor_name = $actor_role;
        }

        $insert_data = array(
            'modul'         => $modul,
            'ref_id'        => (string)$ref_id,
            'target_name'   => $target_name,
            'action'        => $action,
            'actor_id'      => $actor_id ? (int)$actor_id : NULL,
            'actor_name'    => $actor_name,
            'actor_role'    => $actor_role,
            'actor_nip_nim' => $actor_nip_nim,
            'catatan'       => $catatan,
            'created_at'    => date('Y-m-d H:i:s')
        );

        return $this->db->insert('log_approval_history', $insert_data);
    }

    /**
     * Ambil semua data log history dengan pagination dan filter
     */
    public function get_all_logs($filter_modul = null, $filter_action = null, $search = null, $limit = 20, $offset = 0) {
        $this->_ensure_table();

        if (!empty($filter_modul) && $filter_modul !== 'all') {
            $this->db->where('modul', $filter_modul);
        }

        if (!empty($filter_action) && $filter_action !== 'all') {
            $this->db->where('action', $filter_action);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ref_id', $search);
            $this->db->or_like('target_name', $search);
            $this->db->or_like('actor_name', $search);
            $this->db->or_like('actor_role', $search);
            $this->db->or_like('actor_nip_nim', $search);
            $this->db->or_like('catatan', $search);
            $this->db->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get('log_approval_history')->result_array();
    }

    /**
     * Hitung total data log untuk pagination
     */
    public function count_logs($filter_modul = null, $filter_action = null, $search = null) {
        $this->_ensure_table();

        if (!empty($filter_modul) && $filter_modul !== 'all') {
            $this->db->where('modul', $filter_modul);
        }

        if (!empty($filter_action) && $filter_action !== 'all') {
            $this->db->where('action', $filter_action);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ref_id', $search);
            $this->db->or_like('target_name', $search);
            $this->db->or_like('actor_name', $search);
            $this->db->or_like('actor_role', $search);
            $this->db->or_like('actor_nip_nim', $search);
            $this->db->or_like('catatan', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results('log_approval_history');
    }

    /**
     * Ambil riwayat log spesifik berdasarkan referensi (misal NIM mahasiswa atau ID Booking)
     */
    public function get_logs_by_ref($ref_id, $modul = null) {
        $this->_ensure_table();

        $this->db->where('ref_id', (string)$ref_id);
        if (!empty($modul)) {
            $this->db->where('modul', $modul);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('log_approval_history')->result_array();
    }

    /**
     * Autocomplete search untuk Log History Approval
     */
    public function autocomplete_search($term) {
        $this->_ensure_table();
        if (empty($term)) return array();

        $this->db->group_start();
        $this->db->like('ref_id', $term);
        $this->db->or_like('target_name', $term);
        $this->db->or_like('actor_name', $term);
        $this->db->or_like('actor_role', $term);
        $this->db->or_like('actor_nip_nim', $term);
        $this->db->or_like('catatan', $term);
        $this->db->group_end();

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(8);
        return $this->db->get('log_approval_history')->result_array();
    }

    /**
     * Hitung metrik statistik ringkas untuk Log History
     */
    public function get_log_stats() {
        $this->_ensure_table();
        
        $total    = $this->db->count_all_results('log_approval_history');
        $approved = $this->db->where('action', 'Approved')->count_all_results('log_approval_history');
        $rejected = $this->db->where('action', 'Rejected')->count_all_results('log_approval_history');
        $reset    = $this->db->where('action', 'Reset')->count_all_results('log_approval_history');

        return array(
            'total'    => $total,
            'approved' => $approved,
            'rejected' => $rejected,
            'reset'    => $reset
        );
    }
}
