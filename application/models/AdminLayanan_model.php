<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminLayanan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Hitung total pengajuan untuk Paging
     */
    public function get_count_pengajuan($filter_status = null, $search = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return 0;
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->where('p.is_submitted', 1);

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

        return $this->db->count_all_results();
    }

    /**
     * Ambil daftar pengajuan berkas mahasiswa untuk Admin Layanan dengan Paging (Limit & Offset)
     */
    public function get_all_pengajuan($filter_status = null, $search = null, $limit = 5, $offset = 0) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $has_mhs   = $this->db->table_exists('mahasiswa');
        $has_kk    = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) {
            $select .= ', COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.konsentrasi_dkv, m.alamat';
            if ($this->db->field_exists('prodi', 'mahasiswa')) $select .= ', m.prodi';
            if ($this->db->field_exists('email', 'mahasiswa')) $select .= ', m.email';
            if ($this->db->field_exists('no_hp', 'mahasiswa')) $select .= ', m.no_hp';
        }
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        $this->db->where('p.is_submitted', 1);

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

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    /**
     * Autocomplete search untuk Admin Layanan LAA
     */
    public function autocomplete_search($term) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($term)) {
            return array();
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $this->db->select('p.nim, p.judul_1, p.status_approval_wali, p.status_approval_admin, m.nama_depan, m.nama_belakang, m.konsentrasi_dkv');
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) {
            $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        }
        $this->db->where('p.is_submitted', 1);

        $this->db->group_start();
        if ($has_mhs) {
            $this->db->like('m.nama_depan', $term);
            $this->db->or_like('m.nama_belakang', $term);
        }
        $this->db->or_like('p.nim', $term);
        $this->db->or_like('p.judul_1', $term);
        $this->db->group_end();

        $this->db->limit(8);
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    /**
     * Hitung statistik berkas untuk kartu metrik Admin Layanan
     */
    public function get_stats() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0);
        }

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
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $has_kk  = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) {
            $select .= ', COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.konsentrasi_dkv, m.alamat';
            if ($this->db->field_exists('prodi', 'mahasiswa')) $select .= ', m.prodi';
            if ($this->db->field_exists('email', 'mahasiswa')) $select .= ', m.email';
            if ($this->db->field_exists('no_hp', 'mahasiswa')) $select .= ', m.no_hp';
        }
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        $this->db->where('p.nim', $nim);
        
        $query = $this->db->get();
        return $query ? $query->row_array() : null;
    }

    /**
     * Update hasil verifikasi berkas oleh Admin Layanan LAA
     */
    public function update_verifikasi($nim, $action, $berkas_kurang_json = NULL, $catatan_admin = '', $berkas_valid_arr = array(), $berkas_kurang_arr = array()) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return false;
        }

        $status = ($action === 'approve') ? 'Approved' : 'Rejected';
        $current_stage = ($action === 'approve') ? 'Koordinator TA' : 'Admin Layanan';

        $data = array(
            'status_approval_admin' => $status,
            'catatan_admin'         => $catatan_admin,
            'berkas_kurang'         => ($action === 'reject') ? $berkas_kurang_json : NULL,
            'current_stage'         => $current_stage
        );

        $semua_berkas = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        if ($action === 'approve') {
            foreach ($semua_berkas as $b) {
                $data['status_' . $b] = 'Valid';
            }
        } else {
            foreach ($semua_berkas as $b) {
                $col = 'status_' . $b;
                if (in_array($b, $berkas_kurang_arr)) {
                    $data[$col] = 'Invalid';
                } else if (in_array($b, $berkas_valid_arr)) {
                    $data[$col] = 'Valid';
                }
            }
        }

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    /**
     * Reset status pengajuan mahasiswa kembali ke Pending (Batalkan Status Revisi yang Kepencet)
     */
    public function reset_verifikasi_pending($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return false;
        }

        $data = array(
            'status_approval_admin' => 'Pending',
            'catatan_admin'         => NULL,
            'berkas_kurang'         => NULL,
            'current_stage'         => 'Admin Layanan',
            'status_ksm'            => 'Pending',
            'status_transkrip'      => 'Pending',
            'status_pernyataan'     => 'Pending',
            'status_bebas_lab'      => 'Pending'
        );

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    /**
     * Ambil detail lengkap pengajuan beberapa mahasiswa sekaligus (Batch)
     */
    public function get_batch_details_by_nims($nims) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($nims)) {
            return array();
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $has_kk  = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) $select .= ', m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, m.alamat';
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        $this->db->where_in('p.nim', $nims);
        
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }
}
