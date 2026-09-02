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
            'catatan_file_ksm'        => "TEXT NULL",
            'catatan_file_transkrip'  => "TEXT NULL",
            'catatan_file_pernyataan' => "TEXT NULL",
            'catatan_file_bebas_lab'  => "TEXT NULL",
            'judul_disetujui'        => "INT DEFAULT 1",
            'status_judul'           => "VARCHAR(20) DEFAULT 'Pending'",
            'catatan_judul'          => "TEXT NULL",
        );
        foreach ($new_cols as $col => $type) {
            if (!in_array($col, $fields)) {
                $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col}` {$type}");
            }
        }
    }

    // Update Keputusan Usulan Judul TA (Status & Saran/Catatan Revisi)
    public function update_judul_approval($nim, $status_judul, $catatan_judul = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) return false;

        $data = array(
            'status_judul'    => $status_judul,
            'catatan_judul'   => $catatan_judul,
            'updated_at'      => date('Y-m-d H:i:s')
        );

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    // Get List Mahasiswa Bimbingan Wali (Fetch REAL Submitted Pendaftaran TA Data)
    public function get_mahasiswa_bimbingan($nip_dosen = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }
        $this->db->select('p.*, COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.konsentrasi_dkv as mhs_konsentrasi, m.alamat, p.created_at as tgl_daftar');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->where('p.is_submitted', 1);
        if (!empty($nip_dosen)) {
            $this->db->group_start();
            $this->db->where('m.nip_dosen_wali', $nip_dosen);
            $this->db->or_where('p.id_dosen_wali', 1);
            $this->db->or_where('m.nip_dosen_wali IS NULL', null, false);
            $this->db->or_where('m.nip_dosen_wali', '');
            $this->db->group_end();
        }
        $this->db->order_by('p.id', 'DESC');
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

        // Selalu perbarui catatan berkas sesuai input dosen wali
        $data['catatan_file_' . $file_type] = $comment;

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

            $overall_data = array('updated_at' => date('Y-m-d H:i:s'));
            if ($s_ksm === 'Approved' && $s_trn === 'Approved' && $s_prn === 'Approved' && $s_lab === 'Approved') {
                $overall_data['status_approval_wali'] = 'Approved';
                $overall_data['current_stage'] = 'Admin Layanan';
            } else if ($s_ksm === 'Rejected' && $s_trn === 'Rejected' && $s_prn === 'Rejected' && $s_lab === 'Rejected') {
                $overall_data['status_approval_wali'] = 'Rejected';
                $overall_data['current_stage'] = 'Dosen Wali (Ditolak)';
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

        // Jika disetujui, lanjut ke status berikutnya (Admin Layanan) dan hapus catatan revisi lama
        if ($status === 'Approved') {
            $data['current_stage']          = 'Admin Layanan';
            $data['status_file_ksm']        = 'Approved';
            $data['status_file_transkrip']  = 'Approved';
            $data['status_file_pernyataan'] = 'Approved';
            $data['status_file_bebas_lab']  = 'Approved';
            $data['status_judul']           = 'Approved';
            $data['status_jenis_ta']        = 'Approved';
            $data['catatan_wali']           = '';
            $data['catatan_judul']          = '';
            $data['catatan_jenis_ta']       = '';
            $data['catatan_file_ksm']       = '';
            $data['catatan_file_transkrip'] = '';
            $data['catatan_file_pernyataan']= '';
            $data['catatan_file_bebas_lab'] = '';
            $data['review_file_ksm']        = 1;
            $data['review_file_transkrip']  = 1;
            $data['review_file_pernyataan'] = 1;
            $data['review_file_bebas_lab']  = 1;
        } else if ($status === 'Rejected') {
            $data['current_stage'] = 'Dosen Wali (Revisi)';
        }

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    // Ambil detail pendaftaran banyak mahasiswa sekaligus untuk Cek Masal (Batch Modal)
    public function get_batch_details_by_nims($nims = array()) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($nims)) {
            return array();
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $has_kk  = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) $select .= ', COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.prodi, m.konsentrasi_dkv as mhs_konsentrasi, m.email, m.no_hp';
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');

        $this->db->where_in('p.nim', $nims);
        $this->db->where('p.is_submitted', 1);
        $this->db->order_by('p.id', 'DESC');

        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    // Update status approval Jenis TA oleh Dosen Wali
    public function approve_jenis_ta($nim, $status, $catatan = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) return false;

        $data = array(
            'status_jenis_ta'  => $status,
            'catatan_jenis_ta' => ($status === 'Approved') ? '' : $catatan,
            'updated_at'       => date('Y-m-d H:i:s')
        );

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    // Direct Batch Approve Dosen Wali untuk beberapa NIM sekaligus
    public function batch_approve_wali($nims = array()) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($nims)) {
            return 0;
        }

        $data = array(
            'status_approval_wali'    => 'Approved',
            'status_jenis_ta'        => 'Approved',
            'status_judul'           => 'Approved',
            'status_file_ksm'        => 'Approved',
            'status_file_transkrip'  => 'Approved',
            'status_file_pernyataan' => 'Approved',
            'status_file_bebas_lab'   => 'Approved',
            'catatan_wali'           => '',
            'catatan_judul'          => '',
            'catatan_jenis_ta'       => '',
            'catatan_file_ksm'       => '',
            'catatan_file_transkrip' => '',
            'catatan_file_pernyataan'=> '',
            'catatan_file_bebas_lab' => '',
            'review_file_ksm'        => 1,
            'review_file_transkrip'  => 1,
            'review_file_pernyataan' => 1,
            'review_file_bebas_lab'   => 1,
            'current_stage'          => 'Admin Layanan',
            'updated_at'             => date('Y-m-d H:i:s')
        );

        $this->db->where_in('nim', $nims);
        $this->db->where('is_submitted', 1);
        $this->db->update('pendaftaran_ta', $data);

        return $this->db->affected_rows();
    }

    // Simpan keputusan massal detail per section dari popup review
    public function update_batch_decisions($decisions = array()) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($decisions)) {
            return array('approved' => 0, 'rejected' => 0);
        }

        $appCount = 0;
        $rejCount = 0;

        foreach ($decisions as $d) {
            $nim = $d['nim'] ?? '';
            if (empty($nim)) continue;

            $action  = $d['action'] ?? 'approve';
            $catatan = trim($d['catatan_wali'] ?? '');

            $updateData = array(
                'updated_at' => date('Y-m-d H:i:s')
            );

            // 1. Section Jenis TA
            $updateData['status_jenis_ta'] = (isset($d['status_jenis_ta']) && $d['status_jenis_ta'] === 'Rejected') ? 'Rejected' : 'Approved';
            $updateData['catatan_jenis_ta'] = ($updateData['status_jenis_ta'] === 'Rejected') ? ($d['catatan_jenis_ta'] ?? '') : '';

            // 2. Section Usulan Judul TA
            $updateData['status_judul'] = (isset($d['status_judul']) && $d['status_judul'] === 'Rejected') ? 'Rejected' : 'Approved';
            $updateData['catatan_judul'] = ($updateData['status_judul'] === 'Rejected') ? ($d['catatan_judul'] ?? '') : '';

            // 3. Section 4 Berkas Dokumen Persyaratan
            $file_keys = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
            $hasAnyFileReject = false;

            foreach ($file_keys as $fk) {
                $fStatus = (isset($d['status_file_' . $fk]) && $d['status_file_' . $fk] === 'Rejected') ? 'Rejected' : 'Approved';
                $updateData['status_file_' . $fk] = $fStatus;
                $updateData['catatan_file_' . $fk] = ($fStatus === 'Rejected') ? ($d['catatan_file_' . $fk] ?? '') : '';
                $updateData['review_file_' . $fk]  = 1;

                if ($fStatus === 'Rejected') {
                    $hasAnyFileReject = true;
                }
            }

            // Tentukan status keseluruhan pendaftaran
            if ($action === 'reject' || $updateData['status_jenis_ta'] === 'Rejected' || $updateData['status_judul'] === 'Rejected' || $hasAnyFileReject) {
                $updateData['status_approval_wali'] = 'Rejected';
                $updateData['current_stage'] = 'Dosen Wali (Revisi)';
                $updateData['catatan_wali'] = $catatan;
                $rejCount++;
            } else {
                $updateData['status_approval_wali'] = 'Approved';
                $updateData['current_stage'] = 'Admin Layanan';
                $updateData['catatan_wali'] = '';
                $updateData['catatan_judul'] = '';
                $updateData['catatan_jenis_ta'] = '';
                $updateData['catatan_file_ksm'] = '';
                $updateData['catatan_file_transkrip'] = '';
                $updateData['catatan_file_pernyataan'] = '';
                $updateData['catatan_file_bebas_lab'] = '';
                $appCount++;
            }

            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', $updateData);
        }

        return array('approved' => $appCount, 'rejected' => $rejCount);
    }
}
