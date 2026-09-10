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

        $has_mhs = $this->db->table_exists('mahasiswa');
        $has_depan = $has_mhs && $this->db->field_exists('nama_depan', 'mahasiswa');
        $has_users = $this->db->table_exists('users');

        if ($has_depan) {
            $select = 'p.*, COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.konsentrasi_dkv as mhs_konsentrasi, m.alamat, p.created_at as tgl_daftar';
        } else if ($has_users) {
            $select = 'p.*, COALESCE(u.name, p.nim) as nama_depan, "" as nama_belakang, "" as mhs_konsentrasi, "" as alamat, p.created_at as tgl_daftar';
        } else {
            $select = 'p.*, p.nim as nama_depan, "" as nama_belakang, "" as mhs_konsentrasi, "" as alamat, p.created_at as tgl_daftar';
        }

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_depan) {
            $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        } else if ($has_users) {
            $this->db->join('users u', 'u.nidn_nim = p.nim', 'left');
        }

        if ($this->db->field_exists('is_submitted', 'pendaftaran_ta')) {
            $this->db->where('p.is_submitted', 1);
        }

        if (!empty($nip_dosen)) {
            $this->db->group_start();
            if ($has_depan && $this->db->field_exists('nip_dosen_wali', 'mahasiswa')) {
                $this->db->where('m.nip_dosen_wali', $nip_dosen);
                $this->db->or_where('m.nip_dosen_wali IS NULL', null, false);
                $this->db->or_where('m.nip_dosen_wali', '');
            }
            if ($this->db->field_exists('id_dosen_wali', 'pendaftaran_ta')) {
                $this->db->or_where('p.id_dosen_wali', 1);
            }
            $this->db->or_where('1=1', null, false);
            $this->db->group_end();
        }
        $this->db->order_by('p.id', 'DESC');
        $query = $this->db->get();
        $results = $query ? $query->result_array() : array();

        if (!empty($results) && $this->db->table_exists('pendaftaran_berkas')) {
            $nims = array_column($results, 'nim');
            $this->db->where_in('nim', $nims);
            $berkas_rows = $this->db->get('pendaftaran_berkas')->result_array();
            $berkas_by_nim = [];
            foreach ($berkas_rows as $br) {
                $berkas_by_nim[$br['nim']][$br['kode_berkas']] = $br;
            }
            foreach ($results as &$row) {
                $nim = $row['nim'];
                $row['berkas_map'] = $berkas_by_nim[$nim] ?? [];
                if (!empty($row['berkas_map'])) {
                    foreach ($row['berkas_map'] as $kb => $bdata) {
                        if (empty($row['file_' . $kb])) {
                            $row['file_' . $kb] = $bdata['file_name'];
                        }
                        if (empty($row['status_file_' . $kb])) {
                            $row['status_file_' . $kb] = ($bdata['status_verifikasi'] === 'Valid') ? 'Approved' : (($bdata['status_verifikasi'] === 'Invalid') ? 'Rejected' : 'Pending');
                        }
                        if (empty($row['catatan_file_' . $kb]) && !empty($bdata['catatan'])) {
                            $row['catatan_file_' . $kb] = $bdata['catatan'];
                        }
                    }
                }
            }
            unset($row);
        }

        return $results;
    }

    // Get Detail Mahasiswa dan Pendaftaran TA (Real Data from MySQL)
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $has_depan = $has_mhs && $this->db->field_exists('nama_depan', 'mahasiswa');
        $has_users = $this->db->table_exists('users');

        if ($has_depan) {
            $select = 'p.*, m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as mhs_konsentrasi, m.alamat, m.kota, m.provinsi';
        } else if ($has_users) {
            $select = 'p.*, COALESCE(u.name, p.nim) as nama_depan, "" as nama_belakang, "" as mhs_konsentrasi, "" as alamat, "" as kota, "" as provinsi';
        } else {
            $select = 'p.*, p.nim as nama_depan, "" as nama_belakang, "" as mhs_konsentrasi, "" as alamat, "" as kota, "" as provinsi';
        }

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_depan) {
            $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        } else if ($has_users) {
            $this->db->join('users u', 'u.nidn_nim = p.nim', 'left');
        }
        $this->db->where('p.nim', $nim);
        $query = $this->db->get();
        $row = $query ? $query->row_array() : null;

        if (!$row && $has_mhs) {
            $this->db->where('nim', $nim);
            $row = $this->db->get('mahasiswa')->row_array();
        }

        if ($row && $this->db->table_exists('pendaftaran_berkas')) {
            $berkas_rows = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim])->result_array();
            $berkas_map = [];
            foreach ($berkas_rows as $br) {
                $kb = $br['kode_berkas'];
                $berkas_map[$kb] = $br;
                if (empty($row['file_' . $kb])) {
                    $row['file_' . $kb] = $br['file_name'];
                }
                if (empty($row['status_file_' . $kb])) {
                    $row['status_file_' . $kb] = ($br['status_verifikasi'] === 'Valid') ? 'Approved' : (($br['status_verifikasi'] === 'Invalid') ? 'Rejected' : 'Pending');
                }
                if (empty($row['catatan_file_' . $kb]) && !empty($br['catatan'])) {
                    $row['catatan_file_' . $kb] = $br['catatan'];
                }
            }
            $row['berkas_map'] = $berkas_map;
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
        if (!$this->db->table_exists('pendaftaran_ta') || empty($file_type)) return false;

        $col_name = 'review_file_' . $file_type;
        $fields = $this->db->list_fields('pendaftaran_ta');
        if (!in_array($col_name, $fields)) {
            $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col_name}` TINYINT(1) DEFAULT 0");
        }

        $data = array(
            $col_name => 1,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }

    // Update status approval per-file (Approved / Rejected / Pending) oleh Dosen Wali
    public function update_file_approval($nim, $file_type, $status, $comment = '') {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($file_type)) return false;

        $col_status  = 'status_file_' . $file_type;
        $col_review  = 'review_file_' . $file_type;
        $col_catatan = 'catatan_file_' . $file_type;

        $fields = $this->db->list_fields('pendaftaran_ta');
        if (!in_array($col_status, $fields)) {
            $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col_status}` VARCHAR(20) DEFAULT 'Pending'");
        }
        if (!in_array($col_review, $fields)) {
            $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col_review}` TINYINT(1) DEFAULT 0");
        }
        if (!in_array($col_catatan, $fields)) {
            $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col_catatan}` TEXT NULL");
        }

        $data = array(
            $col_status  => $status,
            $col_catatan => $comment,
            'updated_at' => date('Y-m-d H:i:s')
        );

        if ($status !== 'Pending') {
            $data[$col_review] = 1;
        }

        $this->db->where('nim', $nim);
        $this->db->update('pendaftaran_ta', $data);

        // Update juga pendaftaran_berkas jika ada
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $ver = ($status === 'Approved') ? 'Valid' : (($status === 'Rejected') ? 'Invalid' : 'Pending');
            $berkasUpdate = [
                'status_verifikasi' => $ver,
                'updated_at'        => date('Y-m-d H:i:s')
            ];
            if ($this->db->field_exists('catatan', 'pendaftaran_berkas')) {
                $berkasUpdate['catatan'] = ($status === 'Rejected') ? $comment : '';
            }
            $this->db->where('nim', $nim)->where('kode_berkas', $file_type)->update('pendaftaran_berkas', $berkasUpdate);
        }

        // Auto-sinkronisasi status keseluruhan & tahap pendaftaran di DB
        $row = $this->db->get_where('pendaftaran_ta', array('nim' => $nim))->row_array();
        if ($row) {
            $active_syarat = [];
            if ($this->db->table_exists('syarat_berkas_ta')) {
                $active_syarat = $this->db->get_where('syarat_berkas_ta', ['is_active' => 1])->result_array();
            }
            if (empty($active_syarat)) {
                $active_syarat = [
                    ['kode_berkas' => 'ksm'],
                    ['kode_berkas' => 'transkrip'],
                    ['kode_berkas' => 'pernyataan'],
                    ['kode_berkas' => 'bebas_lab']
                ];
            }

            $all_approved = true;
            $all_rejected = true;
            foreach ($active_syarat as $asb) {
                $k = $asb['kode_berkas'];
                $s = $row['status_file_' . $k] ?? 'Pending';
                if ($s !== 'Approved') $all_approved = false;
                if ($s !== 'Rejected') $all_rejected = false;
            }

            $overall_data = array('updated_at' => date('Y-m-d H:i:s'));
            if ($all_approved) {
                $overall_data['status_approval_wali'] = 'Approved';
                $overall_data['current_stage'] = 'Admin Layanan';
            } else if ($all_rejected) {
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

        $active_syarat = [];
        if ($this->db->table_exists('syarat_berkas_ta')) {
            $active_syarat = $this->db->get_where('syarat_berkas_ta', ['is_active' => 1])->result_array();
        }
        if (empty($active_syarat)) {
            $active_syarat = [
                ['kode_berkas' => 'ksm'],
                ['kode_berkas' => 'transkrip'],
                ['kode_berkas' => 'pernyataan'],
                ['kode_berkas' => 'bebas_lab']
            ];
        }

        $fields = $this->db->list_fields('pendaftaran_ta');
        $data = array('updated_at' => date('Y-m-d H:i:s'));

        foreach ($active_syarat as $asb) {
            $k = $asb['kode_berkas'];
            $col_s = 'status_file_' . $k;
            $col_r = 'review_file_' . $k;
            if (in_array($col_s, $fields)) {
                $data[$col_s] = $status;
            }
            if (in_array($col_r, $fields)) {
                $data[$col_r] = 1;
            }
        }

        if ($status === 'Approved') {
            $data['status_approval_wali'] = 'Approved';
            $data['current_stage'] = 'Admin Layanan';
        } else if ($status === 'Rejected') {
            $data['status_approval_wali'] = 'Rejected';
            $data['current_stage'] = 'Dosen Wali (Revisi)';
        } else if ($status === 'Pending') {
            $data['status_approval_wali'] = 'Pending';
            $data['current_stage'] = 'Dosen Wali';
        }

        $this->db->where('nim', $nim);
        $res = $this->db->update('pendaftaran_ta', $data);



        return $res;
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
            $data['current_stage']   = 'Admin Layanan';
            $data['status_judul']    = 'Approved';
            $data['status_jenis_ta'] = 'Approved';
            $data['catatan_wali']    = '';
            $data['catatan_judul']   = '';
            $data['catatan_jenis_ta']= '';

            $active_syarat = [];
            if ($this->db->table_exists('syarat_berkas_ta')) {
                $active_syarat = $this->db->get_where('syarat_berkas_ta', ['is_active' => 1])->result_array();
            }
            if (empty($active_syarat)) {
                $active_syarat = [
                    ['kode_berkas' => 'ksm'],
                    ['kode_berkas' => 'transkrip'],
                    ['kode_berkas' => 'pernyataan'],
                    ['kode_berkas' => 'bebas_lab']
                ];
            }

            $fields = $this->db->list_fields('pendaftaran_ta');
            foreach ($active_syarat as $asb) {
                $k = $asb['kode_berkas'];
                if (in_array('status_file_' . $k, $fields)) $data['status_file_' . $k] = 'Approved';
                if (in_array('review_file_' . $k, $fields)) $data['review_file_' . $k] = 1;
                if (in_array('catatan_file_' . $k, $fields)) $data['catatan_file_' . $k] = '';
            }


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

        $has_mhs   = $this->db->table_exists('mahasiswa');
        $has_depan = $has_mhs && $this->db->field_exists('nama_depan', 'mahasiswa');
        $has_users = $this->db->table_exists('users');
        $has_kk    = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_depan) {
            $select .= ', COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.prodi, m.konsentrasi_dkv as mhs_konsentrasi, m.email, m.no_hp';
        } else if ($has_users) {
            $select .= ', COALESCE(u.name, p.nim) as nama_depan, "" as nama_belakang, "" as prodi, "" as mhs_konsentrasi, u.email, "" as no_hp';
        }
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_depan) {
            $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        } else if ($has_users) {
            $this->db->join('users u', 'u.nidn_nim = p.nim', 'left');
        }
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');


        $this->db->where_in('p.nim', $nims);
        $this->db->where('p.is_submitted', 1);
        $this->db->order_by('p.id', 'DESC');

        $query = $this->db->get();
        $results = $query ? $query->result_array() : array();

        if (!empty($results) && $this->db->table_exists('pendaftaran_berkas')) {
            $this->db->where_in('nim', $nims);
            $berkas_rows = $this->db->get('pendaftaran_berkas')->result_array();
            $berkas_by_nim = [];
            foreach ($berkas_rows as $br) {
                $berkas_by_nim[$br['nim']][$br['kode_berkas']] = $br;
            }
            foreach ($results as &$row) {
                $nim = $row['nim'];
                $row['berkas_map'] = $berkas_by_nim[$nim] ?? [];
                if (!empty($row['berkas_map'])) {
                    foreach ($row['berkas_map'] as $kb => $bdata) {
                        if (empty($row['file_' . $kb])) {
                            $row['file_' . $kb] = $bdata['file_name'];
                        }
                        if (empty($row['status_file_' . $kb])) {
                            $row['status_file_' . $kb] = ($bdata['status_verifikasi'] === 'Valid') ? 'Approved' : (($bdata['status_verifikasi'] === 'Invalid') ? 'Rejected' : 'Pending');
                        }
                        if (empty($row['catatan_file_' . $kb]) && !empty($bdata['catatan'])) {
                            $row['catatan_file_' . $kb] = $bdata['catatan'];
                        }
                    }
                }
            }
            unset($row);
        }

        return $results;
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

        $active_syarat = [];
        if ($this->db->table_exists('syarat_berkas_ta')) {
            $active_syarat = $this->db->get_where('syarat_berkas_ta', ['is_active' => 1])->result_array();
        }
        if (empty($active_syarat)) {
            $file_keys = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        } else {
            $file_keys = array_column($active_syarat, 'kode_berkas');
        }

        $fields = $this->db->list_fields('pendaftaran_ta');
        $data = array(
            'status_approval_wali' => 'Approved',
            'status_jenis_ta'      => 'Approved',
            'status_judul'         => 'Approved',
            'catatan_wali'         => '',
            'catatan_judul'        => '',
            'catatan_jenis_ta'     => '',
            'current_stage'        => 'Admin Layanan',
            'updated_at'           => date('Y-m-d H:i:s')
        );

        foreach ($file_keys as $fk) {
            if (in_array('status_file_' . $fk, $fields)) $data['status_file_' . $fk] = 'Approved';
            if (in_array('review_file_' . $fk, $fields)) $data['review_file_' . $fk] = 1;
            if (in_array('catatan_file_' . $fk, $fields)) $data['catatan_file_' . $fk] = '';
        }

        $this->db->where_in('nim', $nims);
        if ($this->db->field_exists('is_submitted', 'pendaftaran_ta')) {
            $this->db->where('is_submitted', 1);
        }
        $this->db->update('pendaftaran_ta', $data);



        return $this->db->affected_rows();
    }

    // Simpan keputusan massal detail per section dari popup review
    public function update_batch_decisions($decisions = array()) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($decisions)) {
            return array('approved' => 0, 'rejected' => 0);
        }

        $active_syarat = [];
        if ($this->db->table_exists('syarat_berkas_ta')) {
            $active_syarat = $this->db->get_where('syarat_berkas_ta', ['is_active' => 1])->result_array();
        }
        if (empty($active_syarat)) {
            $file_keys = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        } else {
            $file_keys = array_column($active_syarat, 'kode_berkas');
        }

        $fields = $this->db->list_fields('pendaftaran_ta');
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

            // 3. Section Berkas Dokumen Persyaratan Dinamis
            $hasAnyFileReject = false;

            foreach ($file_keys as $fk) {
                $fStatus = (isset($d['status_file_' . $fk]) && $d['status_file_' . $fk] === 'Rejected') ? 'Rejected' : 'Approved';
                $fNote   = ($fStatus === 'Rejected') ? ($d['catatan_file_' . $fk] ?? '') : '';

                $col_status  = 'status_file_' . $fk;
                $col_review  = 'review_file_' . $fk;
                $col_catatan = 'catatan_file_' . $fk;

                if (!in_array($col_status, $fields)) {
                    $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col_status}` VARCHAR(20) DEFAULT 'Pending'");
                    $fields[] = $col_status;
                }
                if (!in_array($col_review, $fields)) {
                    $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col_review}` TINYINT(1) DEFAULT 0");
                    $fields[] = $col_review;
                }
                if (!in_array($col_catatan, $fields)) {
                    $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col_catatan}` TEXT NULL");
                    $fields[] = $col_catatan;
                }

                $updateData[$col_status]  = $fStatus;
                $updateData[$col_catatan] = $fNote;
                $updateData[$col_review]  = 1;

                if ($this->db->table_exists('pendaftaran_berkas')) {
                    $ver = ($fStatus === 'Approved') ? 'Valid' : 'Invalid';
                    $berkasUpdate = [
                        'status_verifikasi' => $ver,
                        'updated_at'        => date('Y-m-d H:i:s')
                    ];
                    if ($this->db->field_exists('catatan', 'pendaftaran_berkas')) {
                        $berkasUpdate['catatan'] = $fNote;
                    }
                    $this->db->where('nim', $nim)->where('kode_berkas', $fk)->update('pendaftaran_berkas', $berkasUpdate);
                }

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
                foreach ($file_keys as $fk) {
                    $col_catatan = 'catatan_file_' . $fk;
                    if (in_array($col_catatan, $fields)) $updateData[$col_catatan] = '';
                }
                if ($this->db->table_exists('pendaftaran_berkas') && $this->db->field_exists('catatan', 'pendaftaran_berkas')) {
                    $this->db->where('nim', $nim)->update('pendaftaran_berkas', ['catatan' => '']);
                }
                $appCount++;
            }

            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', $updateData);
        }

        return array('approved' => $appCount, 'rejected' => $rejCount);
    }

    // Ambil nama file tanda tangan digital dosen
    public function get_tanda_tangan($nip) {
        if ($this->db->table_exists('dosen_wali') && $this->db->field_exists('tanda_tangan', 'dosen_wali')) {
            $row = $this->db->get_where('dosen_wali', ['nip' => $nip])->row_array();
            if (!empty($row['tanda_tangan'])) {
                return $row['tanda_tangan'];
            }
        }
        if ($this->db->table_exists('users') && $this->db->field_exists('tanda_tangan', 'users')) {
            $row = $this->db->get_where('users', ['nidn_nim' => $nip])->row_array();
            if (!empty($row['tanda_tangan'])) {
                return $row['tanda_tangan'];
            }
        }
        return null;
    }

    // Simpan file tanda tangan digital dosen ke database
    public function save_tanda_tangan($nip, $filename) {
        $saved = false;
        if ($this->db->table_exists('dosen_wali') && $this->db->field_exists('tanda_tangan', 'dosen_wali')) {
            $this->db->where('nip', $nip)->update('dosen_wali', ['tanda_tangan' => $filename]);
            $saved = true;
        }
        if ($this->db->table_exists('users') && $this->db->field_exists('tanda_tangan', 'users')) {
            $this->db->where('nidn_nim', $nip)->update('users', ['tanda_tangan' => $filename]);
            $saved = true;
        }
        return $saved;
    }

    // Hapus tanda tangan digital dosen dari database
    public function delete_tanda_tangan($nip) {
        if ($this->db->table_exists('dosen_wali') && $this->db->field_exists('tanda_tangan', 'dosen_wali')) {
            $this->db->where('nip', $nip)->update('dosen_wali', ['tanda_tangan' => null]);
        }
        if ($this->db->table_exists('users') && $this->db->field_exists('tanda_tangan', 'users')) {
            $this->db->where('nidn_nim', $nip)->update('users', ['tanda_tangan' => null]);
        }
        return true;
    }
}
