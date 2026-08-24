<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_ensure_columns_exist();
    }

    private function _ensure_columns_exist() {
        if (!$this->db->table_exists('pendaftaran_ta')) return;
        $fields = $this->db->list_fields('pendaftaran_ta');
        $new_cols = array(
            'pembimbing_1'         => "VARCHAR(50) DEFAULT NULL",
            'pembimbing_2'         => "VARCHAR(50) DEFAULT NULL",
            'penguji_1'            => "VARCHAR(50) DEFAULT NULL",
            'penguji_2'            => "VARCHAR(50) DEFAULT NULL",
            'tgl_sidang'           => "DATE DEFAULT NULL",
            'jam_mulai_sidang'     => "TIME DEFAULT NULL",
            'jam_selesai_sidang'   => "TIME DEFAULT NULL",
            'ruangan_sidang'       => "VARCHAR(100) DEFAULT NULL",
            'status_approval_koor' => "VARCHAR(50) DEFAULT 'Pending'",
            'catatan_koor'         => "TEXT DEFAULT NULL",
            'current_stage'        => "VARCHAR(100) DEFAULT 'Koordinator TA'",
        );
        foreach ($new_cols as $col => $type) {
            if (!in_array($col, $fields)) {
                $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col}` {$type}");
            }
        }
    }

    // Ambil list semua dosen pembimbing dari database (role: dosen)
    public function get_dosen_list() {
        $list = array();

        // 1. Ambil dari tabel users yang memiliki role 'dosen'
        if ($this->db->table_exists('users') && $this->db->table_exists('roles')) {
            $this->db->select('u.id, u.name as nama_dosen, u.nidn_nim as nip, u.email, u.no_hp');
            $this->db->from('users u');
            $this->db->join('roles r', 'r.id = u.role_id');
            $this->db->where('r.name', 'dosen');
            $this->db->where('u.status', 'active');
            $this->db->order_by('u.name', 'ASC');
            $usersDosen = $this->db->get()->result_array();
            
            foreach ($usersDosen as $ud) {
                if (!empty($ud['nip'])) {
                    $list[$ud['nip']] = array(
                        'nip' => (string)$ud['nip'],
                        'nama_dosen' => $ud['nama_dosen'],
                        'email' => $ud['email'] ?? '',
                        'prodi' => 'Informatika'
                    );
                }
            }
        }

        // 2. Lengkapi juga dari tabel dosen_wali
        if ($this->db->table_exists('dosen_wali')) {
            $dwList = $this->db->get('dosen_wali')->result_array();
            foreach ($dwList as $dw) {
                if (!empty($dw['nip']) && !isset($list[$dw['nip']])) {
                    $list[$dw['nip']] = array(
                        'nip' => (string)$dw['nip'],
                        'nama_dosen' => $dw['nama_dosen'],
                        'email' => $dw['email'] ?? '',
                        'prodi' => $dw['jurusan'] ?? 'Informatika'
                    );
                }
            }
        }

        return array_values($list);
    }

    // Get List All Mahasiswa Mendaftar TA untuk Koordinator TA (Real Database Records)
    public function get_all_mahasiswa_ta() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $this->db->select('m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.alamat, m.kota, m.provinsi, m.email, m.no_hp, p.*, dw1.nama_dosen as nama_pembimbing_1, dw2.nama_dosen as nama_pembimbing_2');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->join('dosen_wali dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('dosen_wali dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();

        // Normalisasi data
        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['konsentrasi_dkv'])) {
                $row['konsentrasi_dkv'] = 'Informatika';
            }
            if (empty($row['status_approval_koor'])) {
                $row['status_approval_koor'] = 'Pending';
            }
            if (empty($row['current_stage'])) {
                $row['current_stage'] = 'Koordinator TA';
            }
        }

        return $result;
    }

    // Get Detail Mahasiswa dan Pendaftaran TA untuk Koordinator TA
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $this->db->select('m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.alamat as mhs_alamat, m.kota, m.provinsi, m.email, m.no_hp, p.*, dw1.nama_dosen as nama_pembimbing_1, dw2.nama_dosen as nama_pembimbing_2');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->join('dosen_wali dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('dosen_wali dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->where('p.nim', $nim);
        $query = $this->db->get();
        $row = $query->row_array();

        if (!$row && $this->db->table_exists('mahasiswa')) {
            $this->db->where('nim', $nim);
            $row = $this->db->get('mahasiswa')->row_array();
        }

        if ($row) {
            if (empty($row['alamat']) && !empty($row['mhs_alamat'])) {
                $row['alamat'] = $row['mhs_alamat'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['status_approval_koor'])) {
                $row['status_approval_koor'] = 'Pending';
            }
            if (empty($row['current_stage'])) {
                $row['current_stage'] = 'Koordinator TA';
            }
        }

        return $row;
    }

    // Approval / Reject Pendaftaran TA oleh Koordinator TA dengan AJAX & Validasi Pembimbing
    public function update_approval_koor_ajax($nim, $status, $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        // Cek data pendaftaran mahasiswa
        $this->db->where('nim', $nim);
        $curr = $this->db->get('pendaftaran_ta')->row_array();
        if (!$curr) {
            return array('status' => false, 'message' => 'Data pendaftaran mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
        }

        // Validasi: Harus sudah disetujui Dosen Wali & Admin LAA sebelum Koordinator TA bisa approve
        if ($status === 'Approved') {
            $statusWali = $curr['status_approval_wali'] ?? 'Pending';
            if ($statusWali !== 'Approved') {
                return array(
                    'status' => false, 
                    'message' => 'Persetujuan ditolak! Berkas mahasiswa harus terlebih dahulu disetujui oleh Dosen Wali.'
                );
            }

            $statusAdmin = $curr['status_approval_admin'] ?? 'Pending';
            if ($statusAdmin !== 'Approved') {
                return array(
                    'status' => false, 
                    'message' => 'Persetujuan ditolak! Berkas mahasiswa harus terlebih dahulu disetujui oleh Admin Layanan Akademik (LAA).'
                );
            }

            // Validasi Dosen Pembimbing 1 & 2 wajib dipilih
            if (empty($pembimbing_1) || empty($pembimbing_2)) {
                return array(
                    'status' => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 wajib dipilih sebelum menyetujui pendaftaran TA!'
                );
            }

            // Validasi: Pembimbing 1 dan Pembimbing 2 TIDAK BOLEH SAMA
            if ($pembimbing_1 === $pembimbing_2) {
                return array(
                    'status' => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 tidak boleh sama! Silakan pilih dua dosen yang berbeda.'
                );
            }
        }

        // Validasi Catatan saat Reject
        if ($status === 'Rejected' && empty(trim($catatan))) {
            return array(
                'status' => false, 
                'message' => 'Catatan revisi / alasan penolakan wajib diisi jika memilih status Reject!'
            );
        }

        $data = array(
            'status_approval_koor' => $status, // 'Approved' / 'Rejected'
            'catatan_koor'         => trim($catatan),
            'updated_at'           => date('Y-m-d H:i:s')
        );

        if ($status === 'Approved') {
            $data['pembimbing_1']  = $pembimbing_1;
            $data['pembimbing_2']  = $pembimbing_2;
            $data['current_stage'] = 'Ketua KK';
        } else {
            $data['current_stage'] = 'Koordinator TA';
        }

        $this->db->where('nim', $nim);
        $update = $this->db->update('pendaftaran_ta', $data);

        if ($update) {
            return array(
                'status'  => true, 
                'message' => ($status === 'Approved') ? 'Pendaftaran TA berhasil disetujui dan Dosen Pembimbing telah ditetapkan!' : 'Pendaftaran TA berhasil ditolak dengan catatan revisi.'
            );
        } else {
            return array('status' => false, 'message' => 'Gagal memperbarui database.');
        }
    }
}
