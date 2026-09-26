<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KetuaKK_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Helper internal: Cek apakah tabel legacy pendaftaran_ta tersedia dan berisi data
     */
    private function _use_pendaftaran_ta() {
        return ($this->db->table_exists('pendaftaran_ta') && $this->db->count_all('pendaftaran_ta') > 0);
    }

    /**
     * Ambil daftar Kelompok Keahlian
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
        if ($this->_use_pendaftaran_ta()) {
            $this->db->from('pendaftaran_ta p');
            $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
            $this->db->where('p.is_submitted', 1);

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

        // --- DECOUPLED ARCHITECTURE (user + file_pendaftaran + guidance) ---
        $list = $this->get_mahasiswa_by_kk($id_kk, $filter_status, $search, 0, 0);
        return count($list);
    }

    /**
     * Ambil daftar mahasiswa pendaftar TA berdasarkan KK dan status (dengan Paging / Limit Offset)
     */
    public function get_mahasiswa_by_kk($id_kk = null, $filter_status = null, $search = null, $limit = 5, $offset = 0) {
        if ($this->_use_pendaftaran_ta()) {
            $has_kk_table  = $this->db->table_exists('kelompok_keahlian');
            $has_ketua_col = $has_kk_table && $this->db->field_exists('ketua_kk', 'kelompok_keahlian');

            if ($has_kk_table) {
                $select_str = 'p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, kk.nama_kk, kk.kode_kk';
                if ($has_ketua_col) {
                    $select_str .= ', kk.ketua_kk';
                }
                $this->db->select($select_str);
            } else {
                $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp');
            }

            $this->db->from('pendaftaran_ta p');
            $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
            $this->db->where('p.is_submitted', 1);
            
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

        // --- DECOUPLED ARCHITECTURE (user + file_pendaftaran + guidance) ---
        $this->db->select('
            u.id as user_id,
            u.username as nim,
            u.name,
            u.email,
            u.no_telp as no_hp,
            u.prodi,
            g.id as guidance_id,
            g.judul_1,
            g.judul_2,
            g.judul_3,
            g.keterangan as status_approval_koor,
            g.komentar as catatan_koor,
            g.peminatan
        ');
        $this->db->from('user u');
        $this->db->join('guidance g', 'g.id_mhs = u.id OR g.id_mhs = u.username', 'left');
        $this->db->where('u.role_id', 4); // Mahasiswa

        if ($search) {
            $this->db->group_start();
            $this->db->like('u.name', $search);
            $this->db->or_like('u.username', $search);
            $this->db->or_like('g.judul_1', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        if (!$query || $query->num_rows() === 0) {
            return array();
        }

        $mhs_rows = $query->result_array();
        $user_ids = array_filter(array_column($mhs_rows, 'user_id'));

        // Load file_pendaftaran details
        $fp_map = array();
        if (!empty($user_ids) && $this->db->table_exists('file_pendaftaran')) {
            $fp_rows = $this->db->where_in('id_mhs', $user_ids)->get('file_pendaftaran')->result_array();
            foreach ($fp_rows as $f) {
                $uId = $f['id_mhs'];
                if (!isset($fp_map[$uId])) {
                    $fp_map[$uId] = array(
                        'status_doswal' => 'Pending',
                        'status_adminlaa' => 'Pending',
                        'status_kk' => 'Pending',
                        'catatan_kk' => '',
                    );
                }
                if (isset($f['status_doswal']) && $f['status_doswal'] === 'Approved') {
                    $fp_map[$uId]['status_doswal'] = 'Approved';
                }
                if (isset($f['status_adminlaa']) && $f['status_adminlaa'] === 'Approved') {
                    $fp_map[$uId]['status_adminlaa'] = 'Approved';
                }
            }
        }

        // Map Kelompok Keahlian list
        $all_kk = $this->get_all_kk();
        $default_kk = !empty($all_kk[0]) ? $all_kk[0] : array('id' => 1, 'kode_kk' => 'DKV', 'nama_kk' => 'Visual Communication');

        $results = array();
        foreach ($mhs_rows as $row) {
            $uId = $row['user_id'];
            $fData = $fp_map[$uId] ?? array('status_doswal' => 'Pending', 'status_adminlaa' => 'Pending', 'status_kk' => 'Pending', 'catatan_kk' => '');

            $nameParts = explode(' ', trim($row['name'] ?? 'Mahasiswa'));
            $nama_depan = array_shift($nameParts);
            $nama_belakang = !empty($nameParts) ? implode(' ', $nameParts) : '';

            $kk_item = $default_kk;
            if (!empty($row['peminatan']) && !empty($all_kk)) {
                foreach ($all_kk as $k) {
                    if (strpos(strtolower($row['peminatan']), strtolower($k['kode_kk'])) !== false) {
                        $kk_item = $k;
                        break;
                    }
                }
            }

            $item = array(
                'id'                   => $uId,
                'nim'                  => $row['nim'] ?? '',
                'id_kk'                => $kk_item['id'] ?? 1,
                'kode_kk'              => $kk_item['kode_kk'] ?? 'DKV',
                'nama_kk'              => $kk_item['nama_kk'] ?? 'Visual Communication',
                'nama_depan'           => $nama_depan,
                'nama_belakang'        => $nama_belakang,
                'prodi'                => $row['prodi'] ?? 'Informatika',
                'email'                => $row['email'] ?? '',
                'no_hp'                => $row['no_hp'] ?? '',
                'judul_1'              => !empty($row['judul_1']) ? $row['judul_1'] : 'Perancangan Antarmuka dan Pengalaman Pengguna Platform Layanan Akademik',
                'status_approval_wali' => $fData['status_doswal'] ?? 'Pending',
                'status_approval_admin'=> $fData['status_adminlaa'] ?? 'Pending',
                'status_approval_koor' => !empty($row['status_approval_koor']) ? $row['status_approval_koor'] : 'Pending',
                'status_approval_kk'   => $fData['status_kk'] ?? 'Pending',
                'is_bimbingan_unlocked'=> (($fData['status_kk'] ?? '') === 'Approved') ? 1 : 0,
                'is_submitted'         => 1
            );

            // Filter KK
            if ($id_kk && $id_kk !== 'all' && (int)$item['id_kk'] !== (int)$id_kk) {
                continue;
            }

            // Filter Status
            if ($filter_status && $filter_status !== 'all' && $item['status_approval_kk'] !== $filter_status) {
                continue;
            }

            $results[] = $item;
        }

        // Limit & Offset
        if ($limit > 0) {
            return array_slice($results, $offset, $limit);
        }

        return $results;
    }

    /**
     * Hitung statistik pengajuan untuk Ketua KK
     */
    public function get_stats($id_kk = null) {
        $all = $this->get_mahasiswa_by_kk($id_kk, 'all', null, 0, 0);
        $total = count($all);
        $ready = 0;
        $approved = 0;

        foreach ($all as $item) {
            if (($item['status_approval_wali'] ?? '') === 'Approved' &&
                ($item['status_approval_admin'] ?? '') === 'Approved' &&
                ($item['status_approval_koor'] ?? '') === 'Approved' &&
                ($item['status_approval_kk'] ?? '') === 'Pending') {
                $ready++;
            }
            if (($item['status_approval_kk'] ?? '') === 'Approved') {
                $approved++;
            }
        }

        return array(
            'total'    => $total,
            'ready'    => $ready,
            'approved' => $approved
        );
    }

    /**
     * Autocomplete Search
     */
    public function autocomplete_search($term, $id_kk = null) {
        return $this->get_mahasiswa_by_kk($id_kk, 'all', $term, 8, 0);
    }

    /**
     * Detail mahasiswa untuk Ketua KK
     */
    public function get_detail_mahasiswa($nim) {
        $list = $this->get_mahasiswa_by_kk('all', 'all', $nim, 1, 0);
        if (!empty($list)) {
            return $list[0];
        }
        return null;
    }

    /**
     * Update Approval Ketua KK & Unlock Tahap Bimbingan
     */
    public function update_approval_kk($nim, $status, $catatan = '') {
        // 1. Update pendaftaran_ta dynamically if table & fields exist
        if ($this->db->table_exists('pendaftaran_ta')) {
            $data = array();
            if ($this->db->field_exists('status_approval_kk', 'pendaftaran_ta')) {
                $data['status_approval_kk'] = $status;
            }
            if ($this->db->field_exists('catatan_kk', 'pendaftaran_ta')) {
                $data['catatan_kk'] = $catatan;
            }
            if ($this->db->field_exists('is_bimbingan_unlocked', 'pendaftaran_ta')) {
                $data['is_bimbingan_unlocked'] = ($status === 'Approved') ? 1 : 0;
            }
            if ($this->db->field_exists('current_stage', 'pendaftaran_ta')) {
                $data['current_stage'] = ($status === 'Approved') ? 'Selesai Approval' : 'Ketua KK';
            }

            if (!empty($data)) {
                $this->db->where('nim', $nim);
                $this->db->update('pendaftaran_ta', $data);
            }
        }

        // 2. Sync to file_pendaftaran & guidance / thesis_lecturers if present
        if ($this->db->table_exists('file_pendaftaran')) {
            $fp_update = array('date_edit' => date('Y-m-d H:i:s'));
            if ($this->db->field_exists('status_kk', 'file_pendaftaran')) {
                $fp_update['status_kk'] = $status;
            }
            if (!empty($catatan) && $this->db->field_exists('komentar', 'file_pendaftaran')) {
                $fp_update['komentar'] = $catatan;
            }

            if (count($fp_update) > 1) {
                $this->db->where('id_mhs', $nim)->or_where('id_mhs', 'usr_mhs_' . $nim);
                $this->db->update('file_pendaftaran', $fp_update);
            }
        }

        if ($this->db->table_exists('thesis_lecturers') && $this->db->field_exists('status', 'thesis_lecturers')) {
            $this->db->where('id_guidance', 'gdn_' . $nim)->or_where('id_guidance', $nim);
            $this->db->update('thesis_lecturers', array('status' => $status));
        }

        return true;
    }
}

