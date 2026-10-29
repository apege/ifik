<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KetuaKK_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Helper internal: Cek ketersediaan sumber data pendaftaran
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
        $has_tl = $this->db->table_exists('thesis_lecturers');
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
            g.peminatan' . ($has_tl ? ', tl.status as status_tl_kk' : '') . '
        ');
        $this->db->from('user u');
        $this->db->join('guidance g', 'g.id_mhs = u.id OR g.id_mhs = u.username', 'left');
        if ($has_tl) {
            $this->db->join('thesis_lecturers tl', 'tl.id_guidance = g.id', 'left');
        }
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
        
        // Build target_ids with all ID variants (uId, nim, usr_mhs_nim, mhs_nim)
        $target_ids = array();
        foreach ($mhs_rows as $r) {
            $uId = $r['user_id'] ?? '';
            $nim = $r['nim'] ?? '';
            if (!empty($uId)) $target_ids[] = $uId;
            if (!empty($nim)) {
                $target_ids[] = $nim;
                $target_ids[] = 'usr_mhs_' . $nim;
                $target_ids[] = 'usr_' . $nim;
                $target_ids[] = 'mhs_' . $nim;
            }
        }
        $target_ids = array_unique(array_filter($target_ids));

        // Load file_pendaftaran details
        $fp_map = array();
        if (!empty($target_ids) && $this->db->table_exists('file_pendaftaran')) {
            $fp_rows = $this->db->where_in('id_mhs', $target_ids)->get('file_pendaftaran')->result_array();
            foreach ($fp_rows as $f) {
                $raw_id = $f['id_mhs'];
                $clean_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $raw_id);
                
                $keys = array($raw_id, $clean_nim, 'usr_mhs_' . $clean_nim);
                foreach ($keys as $k) {
                    if (!isset($fp_map[$k])) {
                        $fp_map[$k] = array(
                            'status_doswal'   => 'Pending',
                            'status_adminlaa' => 'Pending',
                            'status_kk'       => 'Pending',
                            'catatan_kk'      => '',
                        );
                    }

                    $s_dos = strtolower(trim($f['status_doswal'] ?? ''));
                    if (in_array($s_dos, array('approved', 'valid', '1'))) {
                        $fp_map[$k]['status_doswal'] = 'Approved';
                    }

                    $s_adm = strtolower(trim($f['status_adminlaa'] ?? ''));
                    if (in_array($s_adm, array('approved', 'valid', '1'))) {
                        $fp_map[$k]['status_adminlaa'] = 'Approved';
                    }

                    if (isset($f['status_kk']) && in_array(strtolower(trim($f['status_kk'])), array('approved', 'valid', '1', 'disetujui'))) {
                        $fp_map[$k]['status_kk'] = 'Approved';
                    } elseif (isset($f['status_kk']) && in_array(strtolower(trim($f['status_kk'])), array('rejected', 'ditolak', '0'))) {
                        $fp_map[$k]['status_kk'] = 'Rejected';
                    }
                }
            }
        }

        // Map Kelompok Keahlian list
        $all_kk = $this->get_all_kk();
        $default_kk = !empty($all_kk[0]) ? $all_kk[0] : array('id' => 1, 'kode_kk' => 'DKV', 'nama_kk' => 'Visual Communication');

        $results = array();
        foreach ($mhs_rows as $row) {
            $uId = $row['user_id'] ?? '';
            $nim = $row['nim'] ?? '';
            
            $fData = $fp_map[$uId] ?? ($fp_map[$nim] ?? ($fp_map['usr_mhs_' . $nim] ?? array('status_doswal' => 'Pending', 'status_adminlaa' => 'Pending', 'status_kk' => 'Pending', 'catatan_kk' => '')));

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

            // Tentukan status persetujuan Koordinator TA
            $s_koor_raw = strtolower(trim($row['status_approval_koor'] ?? ($row['status_file'] ?? '')));
            $status_koor = (in_array($s_koor_raw, array('approved', 'disetujui', 'valid', '1', 'ok'))) ? 'Approved' : 'Pending';

            // Tentukan status approval Ketua KK (cek thesis_lecturers lalu file_pendaftaran)
            $status_kk = 'Pending';
            $s_tl_raw = strtolower(trim($row['status_tl_kk'] ?? ''));
            if (in_array($s_tl_raw, array('approved', 'disetujui', 'valid', '1', 'ok'))) {
                $status_kk = 'Approved';
            } elseif (in_array($s_tl_raw, array('rejected', 'ditolak', '0'))) {
                $status_kk = 'Rejected';
            } elseif (isset($fData['status_kk']) && in_array(strtolower(trim($fData['status_kk'])), array('approved', 'disetujui', 'valid', '1', 'ok'))) {
                $status_kk = 'Approved';
            } elseif (isset($fData['status_kk']) && in_array(strtolower(trim($fData['status_kk'])), array('rejected', 'ditolak', '0'))) {
                $status_kk = 'Rejected';
            }

            $item = array(
                'id'                   => $uId,
                'nim'                  => $nim,
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
                'status_approval_koor' => $status_koor,
                'status_approval_kk'   => $status_kk,
                'is_bimbingan_unlocked'=> ($status_kk === 'Approved') ? 1 : 0,
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

        // Sort results: Put 'Ready for KK' students at the top
        usort($results, function($a, $b) {
            $ready_a = ($a['status_approval_wali'] === 'Approved' && $a['status_approval_admin'] === 'Approved' && $a['status_approval_koor'] === 'Approved');
            $ready_b = ($b['status_approval_wali'] === 'Approved' && $b['status_approval_admin'] === 'Approved' && $b['status_approval_koor'] === 'Approved');
            if ($ready_a !== $ready_b) {
                return $ready_a ? -1 : 1;
            }
            return 0;
        });

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
     * Update approval Ketua KK, sync ke thesis_lecturers & buka akses modul bimbingan
     */
    public function update_approval_kk($nim, $status, $catatan = '') {
        // 1. Sinkronisasi data pendaftaran jika tabel terhubung
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

        // 2. Kumpulkan semua format ID mahasiswa (NIM, usr_mhs, user id)
        $target_mhs_ids = array($nim, 'usr_mhs_' . $nim, 'mhs_' . $nim, 'usr_' . $nim);
        if ($this->db->table_exists('user')) {
            $u = $this->db->group_start()
                ->where('username', $nim)
                ->or_where('id', $nim)
                ->group_end()
                ->get('user')->row_array();
            if ($u && !empty($u['id'])) {
                $target_mhs_ids[] = $u['id'];
            }
            if ($u && !empty($u['username'])) {
                $target_mhs_ids[] = $u['username'];
            }
        }
        $target_mhs_ids = array_unique(array_filter($target_mhs_ids));

        // 3. Ambil data bimbingan mahasiswa dari tabel guidance
        $guidance_ids = array();
        if ($this->db->table_exists('guidance')) {
            $g_rows = $this->db->select('id')
                ->from('guidance')
                ->where_in('id_mhs', $target_mhs_ids)
                ->get()->result_array();
            foreach ($g_rows as $g) {
                $guidance_ids[] = $g['id'];
            }
        }

        // 4. Upsert/Sync thesis_lecturers (plotting status / status KK)
        if (!empty($guidance_ids) && $this->db->table_exists('thesis_lecturers')) {
            foreach ($guidance_ids as $gId) {
                $tl = $this->db->get_where('thesis_lecturers', array('id_guidance' => $gId))->row_array();
                if ($tl) {
                    $tl_up = array('status' => $status);
                    if ($this->db->field_exists('date_edit', 'thesis_lecturers')) {
                        $tl_up['date_edit'] = date('Y-m-d H:i:s');
                    }
                    $this->db->where('id', $tl['id'])->update('thesis_lecturers', $tl_up);
                } else {
                    $tl_data = array(
                        'id_guidance' => $gId,
                        'status'      => $status
                    );
                    if ($this->db->field_exists('created_at', 'thesis_lecturers')) {
                        $tl_data['created_at'] = date('Y-m-d H:i:s');
                    }
                    if ($this->db->field_exists('date', 'thesis_lecturers')) {
                        $tl_data['date'] = date('Y-m-d H:i:s');
                    }
                    if ($this->db->field_exists('date_edit', 'thesis_lecturers')) {
                        $tl_data['date_edit'] = date('Y-m-d H:i:s');
                    }
                    $this->db->insert('thesis_lecturers', $tl_data);
                }
            }
        }

        // 5. Sync to file_pendaftaran
        if ($this->db->table_exists('file_pendaftaran')) {
            $fp_update = array('date_edit' => date('Y-m-d H:i:s'));
            if ($this->db->field_exists('status_kk', 'file_pendaftaran')) {
                $fp_update['status_kk'] = $status;
            }
            if (!empty($catatan) && $this->db->field_exists('komentar', 'file_pendaftaran')) {
                $fp_update['komentar'] = $catatan;
            }

            $this->db->group_start()
                     ->where_in('id_mhs', $target_mhs_ids)
                     ->or_like('id_mhs', $nim)
                     ->group_end();
            $this->db->update('file_pendaftaran', $fp_update);
        }

        return true;
    }
}

