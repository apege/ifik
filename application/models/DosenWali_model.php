<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_ensure_columns_exist();
    }

    private function _ensure_columns_exist() {
        if (!$this->db->table_exists('pendaftaran_ta')) return;
        $fields = $this->db->list_fields('pendaftaran_ta');
        $new_cols = array(
            'status_file_ksm'        => "VARCHAR(20) DEFAULT 'Pending'",
            'status_file_transkrip'  => "VARCHAR(20) DEFAULT 'Pending'",
            'status_file_pernyataan' => "VARCHAR(20) DEFAULT 'Pending'",
            'status_file_bebas_lab'   => "VARCHAR(20) DEFAULT 'Pending'",
            'review_file_ksm'        => "TINYINT(1) DEFAULT 0",
            'review_file_transkrip'  => "TINYINT(1) DEFAULT 0",
            'review_file_pernyataan' => "TINYINT(1) DEFAULT 0",
            'review_file_bebas_lab'   => "TINYINT(1) DEFAULT 0",
        );
        foreach ($new_cols as $col => $type) {
            if (!in_array($col, $fields)) {
                $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col}` {$type}");
            }
        }
    }

    // Get List Mahasiswa Bimbingan Wali (Fetch REAL Submitted Pendaftaran TA Data ONLY)
    public function get_mahasiswa_bimbingan($nip_dosen = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }
        $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as mhs_konsentrasi, m.alamat, p.created_at as tgl_daftar');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $query = $this->db->get();
        return $query->result_array();
    }

    // Get Detail Mahasiswa dan Pendaftaran TA (Real Data from MySQL)
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $this->db->select('p.*, m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as mhs_konsentrasi, m.alamat, m.kota, m.provinsi');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->where('p.nim', $nim);
        $query = $this->db->get();
        $row = $query->row_array();

        // If not found in pendaftaran_ta yet, check mahasiswa table
        if (!$row && $this->db->table_exists('mahasiswa')) {
            $this->db->where('nim', $nim);
            $row = $this->db->get('mahasiswa')->row_array();
        }

        return $row;
    }

    // Get Info Dosen Wali (Kode, Nama, Kejuruan)
    public function get_dosen_wali_info($nip) {
        if (!$this->db->table_exists('dosen_wali')) {
            return array(
                'nip' => '19850101',
                'kode_dosen' => 'DW-001',
                'nama_dosen' => 'Alif Dosen, S.T., M.T.',
                'kejuruan' => 'Informatika / DKV'
            );
        }
        $this->db->where('nip', $nip);
        $row = $this->db->get('dosen_wali')->row_array();
        if (!$row) {
            return array(
                'nip' => $nip,
                'kode_dosen' => 'DW-001',
                'nama_dosen' => 'Alif Dosen, S.T., M.T.',
                'kejuruan' => 'Informatika / DKV'
            );
        }
        return $row;
    }

    // Log ketika Dosen Wali membuka/meninjau file PDF
    public function log_file_review($nim, $file_type) {
        if (!$this->db->table_exists('pendaftaran_ta')) return false;

        $valid_cols = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        if (!in_array($file_type, $valid_cols)) return false;

        $col_name = 'review_file_' . $file_type;
        $data = array(
            $col_name => 1,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    // Update status approval per-file (Approved / Rejected / Pending) oleh Dosen Wali
    public function update_file_approval($nim, $file_type, $status, $comment = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) return false;

        $valid_cols = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        if (!in_array($file_type, $valid_cols)) return false;

        $col_name = 'status_file_' . $file_type;
        $data = array(
            $col_name => $status,
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($status !== 'Pending') {
            $data['review_file_' . $file_type] = 1;
        }

        if (!empty($comment)) {
            $current = $this->db->get_where('pendaftaran_ta', array('nim' => $nim))->row_array();
            $existing_notes = $current['catatan_wali'] ?? '';
            $prefix = "[" . strtoupper($file_type) . " - " . $status . "]: " . $comment;
            $new_notes = !empty($existing_notes) ? $existing_notes . "\n" . $prefix : $prefix;
            $data['catatan_wali'] = $new_notes;
        }

        $this->db->where('nim', $nim);
        $this->db->update('pendaftaran_ta', $data);

        // Auto-sinkronisasi status keseluruhan & tahap pendaftaran di DB
        $row = $this->db->get_where('pendaftaran_ta', array('nim' => $nim))->row_array();
        if ($row) {
            $s_ksm = $row['status_file_ksm'] ?? 'Pending';
            $s_trn = $row['status_file_transkrip'] ?? 'Pending';
            $s_prn = $row['status_file_pernyataan'] ?? 'Pending';
            $s_lab = $row['status_file_bebas_lab'] ?? 'Pending';

            $all_files = array($s_ksm, $s_trn, $s_prn, $s_lab);

            $overall_data = array('updated_at' => date('Y-m-d H:i:s'));
            if ($s_ksm === 'Approved' && $s_trn === 'Approved' && $s_prn === 'Approved' && $s_lab === 'Approved') {
                $overall_data['status_approval_wali'] = 'Approved';
                $overall_data['current_stage'] = 'Admin Layanan';
            } else if (in_array('Rejected', $all_files)) {
                $overall_data['status_approval_wali'] = 'Rejected';
                $overall_data['current_stage'] = 'Dosen Wali (Revisi)';
            } else {
                $overall_data['status_approval_wali'] = 'Pending';
                $overall_data['current_stage'] = 'Dosen Wali';
            }

            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', $overall_data);
        }

        return true;
    }

    // Update status approval semua berkas sekaligus (Approve Semua / Tolak Semua / Reset)
    public function update_all_files_approval($nim, $status) {
        if (!$this->db->table_exists('pendaftaran_ta')) return false;

        $data = array(
            'status_file_ksm'        => $status,
            'status_file_transkrip'  => $status,
            'status_file_pernyataan' => $status,
            'status_file_bebas_lab'   => $status,
            'updated_at'             => date('Y-m-d H:i:s')
        );

        if ($status === 'Approved') {
            $data['status_approval_wali'] = 'Approved';
            $data['current_stage'] = 'Admin Layanan';
            $data['review_file_ksm'] = 1;
            $data['review_file_transkrip'] = 1;
            $data['review_file_pernyataan'] = 1;
            $data['review_file_bebas_lab'] = 1;
        } else if ($status === 'Rejected') {
            $data['status_approval_wali'] = 'Rejected';
            $data['current_stage'] = 'Dosen Wali (Revisi)';
            $data['review_file_ksm'] = 1;
            $data['review_file_transkrip'] = 1;
            $data['review_file_pernyataan'] = 1;
            $data['review_file_bebas_lab'] = 1;
        } else if ($status === 'Pending') {
            $data['status_approval_wali'] = 'Pending';
            $data['current_stage'] = 'Dosen Wali';
        }

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    // Approval / Reject Pendaftaran TA oleh Dosen Wali
    public function update_approval_wali($nim, $status, $catatan = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) return true;
        $data = array(
            'status_approval_wali' => $status, // 'Approved' / 'Rejected'
            'catatan_wali'         => $catatan,
            'updated_at'           => date('Y-m-d H:i:s')
        );

        // Jika disetujui, lanjut ke status berikutnya (Admin Layanan)
        if ($status === 'Approved') {
            $data['current_stage'] = 'Admin Layanan';
        } else if ($status === 'Rejected') {
            $data['current_stage'] = 'Dosen Wali (Revisi)';
        }

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }
}
