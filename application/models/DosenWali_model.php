<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // $this->_ensure_columns_exist(); // Disabled auto alter table
    }

    private function _ensure_columns_exist() {
        return; // Disabled auto alter table
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
        if ($this->db->table_exists('guidance')) {
            $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
            $g_fields = $this->db->list_fields('guidance');
            $g_up = array();
            if (in_array('keterangan', $g_fields)) $g_up['keterangan'] = ($status_judul === 'Approved') ? 'Approved' : (($status_judul === 'Rejected') ? 'Rejected' : 'Pending');
            if (in_array('komentar', $g_fields)) $g_up['komentar'] = $catatan_judul;
            if (in_array('date_edit', $g_fields)) $g_up['date_edit'] = date('Y-m-d H:i:s');
            if (!empty($g_up)) {
                $this->db->where_in('id_mhs', $target_ids)->update('guidance', $g_up);
            }
        }

        if (!$this->db->table_exists('pendaftaran_ta')) return true;

        $fields = $this->db->list_fields('pendaftaran_ta');
        $data = array('updated_at' => date('Y-m-d H:i:s'));
        if (in_array('status_judul', $fields)) $data['status_judul'] = $status_judul;
        if (in_array('catatan_judul', $fields)) $data['catatan_judul'] = $catatan_judul;

        if (count($data) > 1) {
            $this->db->where('nim', $nim)->update('pendaftaran_ta', $data);
        }
        return true;
    }

    public function get_mahasiswa_bimbingan($nip_dosen = null) {
        return $this->_get_mahasiswa_bimbingan_from_file_pendaftaran($nip_dosen);
    }

    // Get Detail Mahasiswa dan Pendaftaran TA (Real Data from MySQL)
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if ($this->db->table_exists('file_pendaftaran') || $this->db->table_exists('guidance')) {
            return $this->_get_detail_from_file_pendaftaran($nim);
        }
        return null;
    }


    // Get Info Dosen Wali (Kode, Nama, Kejuruan) Dinamis dari tabel user / session
    public function get_dosen_wali_info($nip = null) {
        $userId = $this->session->userdata('user_id');
        $user = null;

        if ($this->db->table_exists('user')) {
            if (!empty($userId)) {
                $user = $this->db->get_where('user', ['id' => $userId])->row_array();
            }
            if (!$user && !empty($nip)) {
                $this->db->group_start();
                $this->db->where('nip', $nip);
                if ($this->db->field_exists('nidn_nim', 'user')) $this->db->or_where('nidn_nim', $nip);
                $this->db->or_where('username', $nip);
                $this->db->group_end();
                $user = $this->db->get('user')->row_array();
            }
        }

        $namaDosen = !empty($user['name']) ? $user['name'] : ($this->session->userdata('name') ?: 'Dosen Wali');
        $kodeDosen = !empty($user['kode_dosen']) ? $user['kode_dosen'] : ($this->session->userdata('kode_dosen') ?: 'DWL');
        $nipDosen  = !empty($user['nip']) ? $user['nip'] : ($user['nidn_nim'] ?? ($nip ?: '-'));
        $prodi     = !empty($user['prodi']) ? $user['prodi'] : ($this->session->userdata('selected_prodi') ?: 'Desain Komunikasi Visual');

        return array(
            'nip'        => $nipDosen,
            'kode_dosen' => $kodeDosen,
            'nama_dosen' => $namaDosen,
            'kejuruan'   => $prodi,
            'prodi'      => $prodi
        );
    }

    // Log ketika Dosen Wali membuka/meninjau file PDF
    public function log_file_review($nim, $file_type) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($file_type)) return false;

        $col_name = 'review_file_' . $file_type;
        $fields = $this->db->list_fields('pendaftaran_ta');
        if (!in_array($col_name, $fields)) {
            return false;
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
        $ver = ($status === 'Approved') ? 'Approved' : (($status === 'Rejected') ? 'Rejected' : 'Pending');
        $pb_ver = ($status === 'Approved') ? 'Valid' : (($status === 'Rejected') ? 'Invalid' : 'Pending');

        // 1. Update juga file_pendaftaran jika ada
        if ($this->db->table_exists('file_pendaftaran')) {
            $this->_update_file_approval_file_pendaftaran($nim, $file_type, $ver, $comment);
        }

        // 2. Update pendaftaran_berkas (hanya jika ada kolom status_doswal khusus doswal)
        if ($this->db->table_exists('pendaftaran_berkas') && $this->db->field_exists('status_doswal', 'pendaftaran_berkas')) {
            $berkasUpdate = [
                'status_doswal' => $ver,
                'updated_at'    => date('Y-m-d H:i:s')
            ];
            if ($this->db->field_exists('catatan_doswal', 'pendaftaran_berkas')) {
                $berkasUpdate['catatan_doswal'] = ($status === 'Rejected') ? $comment : '';
            }
            $this->db->where('nim', $nim)->where('kode_berkas', $file_type)->update('pendaftaran_berkas', $berkasUpdate);
        }

        // 3. Update ke pendaftaran_ta jika tabel ada
        if ($this->db->table_exists('pendaftaran_ta')) {
            $col_status  = 'status_file_' . $file_type;
            $col_review  = 'review_file_' . $file_type;
            $col_catatan = 'catatan_file_' . $file_type;

            $fields = $this->db->list_fields('pendaftaran_ta');
            $data = array('updated_at' => date('Y-m-d H:i:s'));
            if (in_array($col_status, $fields)) $data[$col_status] = $ver;
            if (in_array($col_catatan, $fields)) $data[$col_catatan] = $comment;
            if (in_array($col_review, $fields) && $status !== 'Pending') $data[$col_review] = 1;

            foreach ($data as $dk => $dv) {
                if (!in_array($dk, $fields)) {
                    unset($data[$dk]);
                }
            }

            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', $data);

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
                $has_rejected = false;
                foreach ($active_syarat as $asb) {
                    $k = $asb['kode_berkas'];
                    $s = $row['status_file_' . $k] ?? 'Pending';
                    if ($s !== 'Approved') $all_approved = false;
                    if ($s === 'Rejected') $has_rejected = true;
                }

                $overall_data = array('updated_at' => date('Y-m-d H:i:s'));
                if ($all_approved && ($row['status_judul'] ?? 'Pending') === 'Approved') {
                    $overall_data['status_approval_wali'] = 'Approved';
                    $overall_data['current_stage'] = 'Admin Layanan';
                } else if ($has_rejected || ($row['status_judul'] ?? 'Pending') === 'Rejected') {
                    $overall_data['status_approval_wali'] = 'Rejected';
                    $overall_data['current_stage'] = 'Dosen Wali (Revisi)';
                } else {
                    $overall_data['status_approval_wali'] = 'Pending';
                    $overall_data['current_stage'] = 'Dosen Wali';
                }

                $this->db->where('nim', $nim);
                $this->db->update('pendaftaran_ta', $overall_data);
            }
        }

        return true; // Berhasil diupdate
    }

    // Update status approval semua berkas sekaligus (Approve Semua / Tolak Semua / Reset)
    public function update_all_files_approval($nim, $status) {
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

        // Sync ke file_pendaftaran jika ada
        if ($this->db->table_exists('file_pendaftaran')) {
            foreach ($active_syarat as $asb) {
                $this->_update_file_approval_file_pendaftaran($nim, $asb['kode_berkas'], $status);
            }
        }

        // Sync ke pendaftaran_berkas jika ada (hanya jika ada kolom status_doswal)
        if ($this->db->table_exists('pendaftaran_berkas') && $this->db->field_exists('status_doswal', 'pendaftaran_berkas')) {
            $pb_ver = ($status === 'Approved') ? 'Approved' : (($status === 'Rejected') ? 'Rejected' : 'Pending');
            $this->db->where('nim', $nim)->update('pendaftaran_berkas', [
                'status_doswal' => $pb_ver,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }

        if (!$this->db->table_exists('pendaftaran_ta')) return true;

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
    public function update_approval_wali($nim, $status, $catatan = '', $sync_files = false) {
        // Sync ke tabel guidance jika ada
        if ($this->db->table_exists('guidance')) {
            $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
            $g_status = ($status === 'Approved') ? 'Approved' : (($status === 'Rejected') ? 'Rejected' : 'Pending');
            $g_fields = $this->db->list_fields('guidance');
            $g_up = array();
            if (in_array('keterangan', $g_fields)) $g_up['keterangan'] = $g_status;
            if (in_array('komentar', $g_fields)) $g_up['komentar'] = $catatan;
            if (in_array('date_edit', $g_fields)) $g_up['date_edit'] = date('Y-m-d H:i:s');
            if (!empty($g_up)) {
                $this->db->where_in('id_mhs', $target_ids)->update('guidance', $g_up);
            }
        }

        // Sync ke tabel file_pendaftaran & pendaftaran_berkas jika Approved atau eksplisit sync_files
        if ($status === 'Approved' || $sync_files) {
            $this->update_all_files_approval($nim, $status);
        }

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
        if (empty($nims)) return array();

        if (!$this->db->table_exists('pendaftaran_ta')) {
            $batch_res = [];
            foreach ($nims as $n) {
                $d = $this->_get_detail_from_file_pendaftaran($n);
                if ($d) $batch_res[] = $d;
            }
            return $batch_res;
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

        foreach ($results as &$row) {
            $st_wali = $row['status_approval_wali'] ?? '';
            $st_jud  = $row['status_judul'] ?? '';
            $st_jen  = $row['status_jenis_ta'] ?? '';
            
            if ($st_jud === 'Rejected' || $st_jen === 'Rejected') {
                // Biarkan status tetap Rejected
            } elseif ($st_wali === 'Approved' || $st_jud === 'Approved' || $st_jen === 'Approved') {
                $row['status_judul']    = 'Approved';
                $row['status_jenis_ta'] = 'Approved';
            }
        }
        unset($row);

        return $results;
    }

    // Update status approval Jenis TA oleh Dosen Wali
    public function approve_jenis_ta($nim, $status, $catatan = '') {
        if ($this->db->table_exists('guidance')) {
            $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
            $g_fields = $this->db->list_fields('guidance');
            $g_up = array();
            if (in_array('date_edit', $g_fields)) {
                $g_up['date_edit'] = date('Y-m-d H:i:s');
            }
            if (in_array('keterangan', $g_fields)) {
                $g_up['keterangan'] = ($status === 'Approved') ? 'Approved' : 'Rejected';
            }
            if (in_array('komentar', $g_fields) && $status === 'Rejected' && !empty($catatan)) {
                $g_up['komentar'] = $catatan;
            }
            if (!empty($g_up)) {
                $this->db->where_in('id_mhs', $target_ids)->update('guidance', $g_up);
            }
        }

        if (!$this->db->table_exists('pendaftaran_ta')) return true;

        $fields = $this->db->list_fields('pendaftaran_ta');
        $data = array('updated_at' => date('Y-m-d H:i:s'));
        if (in_array('status_jenis_ta', $fields)) $data['status_jenis_ta'] = $status;
        if (in_array('catatan_jenis_ta', $fields)) $data['catatan_jenis_ta'] = ($status === 'Approved') ? '' : $catatan;

        if (count($data) > 1) {
            $this->db->where('nim', $nim)->update('pendaftaran_ta', $data);
        }
        return true;
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

        foreach ($data as $k => $v) {
            if (!in_array($k, $fields)) {
                unset($data[$k]);
            }
        }

        $this->db->where_in('nim', $nims);
        $this->db->update('pendaftaran_ta', $data);

        // Sync to pendaftaran_berkas table
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $berkasData = ['status_verifikasi' => 'Valid', 'updated_at' => date('Y-m-d H:i:s')];
            if ($this->db->field_exists('catatan', 'pendaftaran_berkas')) {
                $berkasData['catatan'] = '';
            }
            $this->db->where_in('nim', $nims)->update('pendaftaran_berkas', $berkasData);
        }

        // Sync to file_pendaftaran table
        if ($this->db->table_exists('file_pendaftaran')) {
            foreach ($nims as $nim) {
                $this->db->group_start()
                    ->where('id_mhs', $nim)
                    ->or_where('id_mhs', 'usr_mhs_' . $nim)
                    ->or_where('id_mhs', 'mhs_' . $nim)
                    ->or_like('id_mhs', $nim)
                    ->group_end()
                    ->update('file_pendaftaran', [
                        'status_doswal' => 'Approved',
                        'komentar'      => '',
                        'date_edit'     => date('Y-m-d H:i:s'),
                        'view_doswal'   => 1
                    ]);
            }
        }

        return count($nims);
    }

    // Simpan keputusan massal detail per section dari popup review
    public function update_batch_decisions($decisions = array()) {
        if (empty($decisions)) {
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

        $fields = $this->db->table_exists('pendaftaran_ta') ? $this->db->list_fields('pendaftaran_ta') : [];
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
            $raw_jen = $d['status_jenis_ta'] ?? 'Pending';
            $updateData['status_jenis_ta'] = in_array($raw_jen, ['Approved', 'Rejected', 'Pending']) ? $raw_jen : 'Pending';
            $updateData['catatan_jenis_ta'] = ($updateData['status_jenis_ta'] === 'Rejected') ? ($d['catatan_jenis_ta'] ?? '') : '';

            // 2. Section Usulan Judul TA
            $raw_jud = $d['status_judul'] ?? 'Pending';
            $updateData['status_judul'] = in_array($raw_jud, ['Approved', 'Rejected', 'Pending']) ? $raw_jud : 'Pending';
            $updateData['catatan_judul'] = ($updateData['status_judul'] === 'Rejected') ? ($d['catatan_judul'] ?? '') : '';

            // 3. Section Berkas Dokumen Persyaratan Dinamis
            $hasAnyFileReject = false;
            $allFilesApproved = true;

            foreach ($file_keys as $fk) {
                $raw_f = $d['status_file_' . $fk] ?? 'Pending';
                $fStatus = in_array($raw_f, ['Approved', 'Rejected', 'Pending']) ? $raw_f : 'Pending';
                $fNote   = ($fStatus === 'Rejected') ? ($d['catatan_file_' . $fk] ?? '') : '';

                $col_status  = 'status_file_' . $fk;
                $col_review  = 'review_file_' . $fk;
                $col_catatan = 'catatan_file_' . $fk;
                if (in_array($col_status, $fields))  $updateData[$col_status]  = $fStatus;
                if (in_array($col_catatan, $fields)) $updateData[$col_catatan] = $fNote;
                if (in_array($col_review, $fields))  $updateData[$col_review]  = ($fStatus !== 'Pending') ? 1 : 0;

                if ($this->db->table_exists('file_pendaftaran')) {
                    $this->_update_file_approval_file_pendaftaran($nim, $fk, $fStatus, $fNote);
                }

                if ($this->db->table_exists('pendaftaran_berkas') && $this->db->field_exists('status_doswal', 'pendaftaran_berkas')) {
                    $ver = ($fStatus === 'Approved') ? 'Approved' : (($fStatus === 'Rejected') ? 'Rejected' : 'Pending');
                    $berkasUpdate = [
                        'status_doswal' => $ver,
                        'updated_at'    => date('Y-m-d H:i:s')
                    ];
                    if ($this->db->field_exists('catatan_doswal', 'pendaftaran_berkas')) {
                        $berkasUpdate['catatan_doswal'] = $fNote;
                    }
                    $this->db->where('nim', $nim)->where('kode_berkas', $fk)->update('pendaftaran_berkas', $berkasUpdate);
                }

                if ($fStatus === 'Rejected') {
                    $hasAnyFileReject = true;
                    $allFilesApproved = false;
                } elseif ($fStatus !== 'Approved') {
                    $allFilesApproved = false;
                }
            }

            // Tentukan status keseluruhan pendaftaran
            if ($action === 'reject' || $updateData['status_jenis_ta'] === 'Rejected' || $updateData['status_judul'] === 'Rejected' || $hasAnyFileReject) {
                $updateData['status_approval_wali'] = 'Rejected';
                $updateData['current_stage'] = 'Dosen Wali (Revisi)';
                $updateData['catatan_wali'] = $catatan;
                $rejCount++;
            } elseif ($action === 'approve' || ($allFilesApproved && $updateData['status_jenis_ta'] === 'Approved' && $updateData['status_judul'] === 'Approved')) {
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
            } else {
                $updateData['status_approval_wali'] = 'Pending';
                $updateData['current_stage'] = 'Dosen Wali';
            }

            // Sync ke guidance
            if ($this->db->table_exists('guidance')) {
                $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
                $g_fields = $this->db->list_fields('guidance');
                $g_up = array();
                if (in_array('keterangan', $g_fields)) {
                    $g_up['keterangan'] = $updateData['status_approval_wali'];
                }
                if (in_array('komentar', $g_fields)) {
                    $g_up['komentar'] = $updateData['catatan_wali'] ?: $updateData['catatan_judul'];
                }
                if (in_array('date_edit', $g_fields)) {
                    $g_up['date_edit'] = date('Y-m-d H:i:s');
                }
                if (!empty($g_up)) {
                    $this->db->where_in('id_mhs', $target_ids)->update('guidance', $g_up);
                }
            }

            if ($this->db->table_exists('pendaftaran_ta')) {
                $this->db->where('nim', $nim);
                $this->db->update('pendaftaran_ta', $updateData);
            }
        }

        return array('approved' => $appCount, 'rejected' => $rejCount);
    }

    // Ambil nama file tanda tangan digital dosen
    public function get_tanda_tangan($nip = null) {
        $userId = $this->session->userdata('user_id');

        if ($this->db->table_exists('user')) {
            if (!empty($userId)) {
                $row = $this->db->get_where('user', ['id' => $userId])->row_array();
                if ($row) {
                    if (!empty($row['ttd'])) return $row['ttd'];
                    if (!empty($row['tanda_tangan'])) return $row['tanda_tangan'];
                }
            }
            if (!empty($nip)) {
                $this->db->group_start();
                if ($this->db->field_exists('nip', 'user')) $this->db->where('nip', $nip);
                if ($this->db->field_exists('nidn_nim', 'user')) $this->db->or_where('nidn_nim', $nip);
                $this->db->or_where('username', $nip);
                $this->db->or_where('id', $nip);
                $this->db->group_end();
                $row = $this->db->get('user')->row_array();
                if ($row) {
                    if (!empty($row['ttd'])) return $row['ttd'];
                    if (!empty($row['tanda_tangan'])) return $row['tanda_tangan'];
                }
            }
        }

        if ($this->db->table_exists('users')) {
            if (!empty($userId)) {
                $row = $this->db->get_where('users', ['id' => $userId])->row_array();
                if ($row) {
                    if (!empty($row['ttd'])) return $row['ttd'];
                    if (!empty($row['tanda_tangan'])) return $row['tanda_tangan'];
                }
            }
            if (!empty($nip)) {
                $row = $this->db->get_where('users', ['nidn_nim' => $nip])->row_array();
                if ($row) {
                    if (!empty($row['ttd'])) return $row['ttd'];
                    if (!empty($row['tanda_tangan'])) return $row['tanda_tangan'];
                }
            }
        }

        if ($this->db->table_exists('dosen_wali') && $this->db->field_exists('tanda_tangan', 'dosen_wali')) {
            $row = $this->db->get_where('dosen_wali', ['nip' => $nip])->row_array();
            if (!empty($row['tanda_tangan'])) {
                return $row['tanda_tangan'];
            }
        }

        return null;
    }

    // Simpan file tanda tangan digital dosen ke database
    public function save_tanda_tangan($nip, $filename) {
        $saved = false;
        $userId = $this->session->userdata('user_id');

        if ($this->db->table_exists('user')) {
            $updateData = [];
            if ($this->db->field_exists('ttd', 'user')) $updateData['ttd'] = $filename;
            if ($this->db->field_exists('tanda_tangan', 'user')) $updateData['tanda_tangan'] = $filename;

            if (!empty($updateData)) {
                if (!empty($userId)) {
                    $this->db->where('id', $userId)->update('user', $updateData);
                    $saved = true;
                }
                if (!empty($nip)) {
                    $this->db->group_start();
                    if ($this->db->field_exists('nip', 'user')) $this->db->where('nip', $nip);
                    if ($this->db->field_exists('nidn_nim', 'user')) $this->db->or_where('nidn_nim', $nip);
                    $this->db->or_where('username', $nip);
                    $this->db->or_where('id', $nip);
                    $this->db->group_end();
                    $this->db->update('user', $updateData);
                    $saved = true;
                }
            }
        }

        if ($this->db->table_exists('users')) {
            $updateData = [];
            if ($this->db->field_exists('ttd', 'users')) $updateData['ttd'] = $filename;
            if ($this->db->field_exists('tanda_tangan', 'users')) $updateData['tanda_tangan'] = $filename;

            if (!empty($updateData)) {
                if (!empty($userId)) {
                    $this->db->where('id', $userId)->update('users', $updateData);
                    $saved = true;
                }
                if (!empty($nip)) {
                    $this->db->where('nidn_nim', $nip)->update('users', $updateData);
                    $saved = true;
                }
            }
        }

        if ($this->db->table_exists('dosen_wali') && $this->db->field_exists('tanda_tangan', 'dosen_wali')) {
            $this->db->where('nip', $nip)->update('dosen_wali', ['tanda_tangan' => $filename]);
            $saved = true;
        }

        return $saved;
    }

    // Hapus tanda tangan digital dosen dari database
    public function delete_tanda_tangan($nip) {
        $userId = $this->session->userdata('user_id');

        if ($this->db->table_exists('user')) {
            $updateData = [];
            if ($this->db->field_exists('ttd', 'user')) $updateData['ttd'] = null;
            if ($this->db->field_exists('tanda_tangan', 'user')) $updateData['tanda_tangan'] = null;

            if (!empty($updateData)) {
                if (!empty($userId)) {
                    $this->db->where('id', $userId)->update('user', $updateData);
                }
                if (!empty($nip)) {
                    $this->db->group_start();
                    if ($this->db->field_exists('nip', 'user')) $this->db->where('nip', $nip);
                    if ($this->db->field_exists('nidn_nim', 'user')) $this->db->or_where('nidn_nim', $nip);
                    $this->db->or_where('username', $nip);
                    $this->db->or_where('id', $nip);
                    $this->db->group_end();
                    $this->db->update('user', $updateData);
                }
            }
        }

        if ($this->db->table_exists('users')) {
            $updateData = [];
            if ($this->db->field_exists('ttd', 'users')) $updateData['ttd'] = null;
            if ($this->db->field_exists('tanda_tangan', 'users')) $updateData['tanda_tangan'] = null;

            if (!empty($updateData)) {
                if (!empty($userId)) {
                    $this->db->where('id', $userId)->update('users', $updateData);
                }
                if (!empty($nip)) {
                    $this->db->where('nidn_nim', $nip)->update('users', $updateData);
                }
            }
        }

        if ($this->db->table_exists('dosen_wali') && $this->db->field_exists('tanda_tangan', 'dosen_wali')) {
            $this->db->where('nip', $nip)->update('dosen_wali', ['tanda_tangan' => null]);
        }

        return true;
    }

    /**
     * Helper: Ambil data mahasiswa bimbingan langsung dari tabel file_pendaftaran
     */
    private function _get_mahasiswa_bimbingan_from_file_pendaftaran($nip_dosen = null) {
        $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
        $student_users = [];
        $target_ids = [];

        if ($user_tbl && !empty($nip_dosen)) {
            $u_rows = $this->db->select('id, username, name, nim, email, dosen_wali, prodi')
                ->group_start()
                    ->where('dosen_wali', $nip_dosen)
                    ->or_where('dosen_wali LIKE', '%' . $nip_dosen . '%')
                    ->or_where("'$nip_dosen' LIKE CONCAT('%', dosen_wali, '%')", null, false)
                ->group_end()
                ->get($user_tbl)
                ->result_array();
            foreach ($u_rows as $ur) {
                $nim = !empty($ur['nim']) ? $ur['nim'] : $ur['username'];
                $target_ids[] = $ur['id'];
                $target_ids[] = 'usr_mhs_' . $nim;
                $target_ids[] = $nim;
                $student_users[$nim] = $ur;
                $student_users[$ur['id']] = $ur;
                $student_users['usr_mhs_' . $nim] = $ur;
            }
        }

        // Ambil data guidance terbaru untuk student yang sudah mendaftar TA
        $guidance_map = [];
        if ($this->db->table_exists('guidance') && !empty($target_ids)) {
            $distinct_ids = array_slice(array_unique($target_ids), 0, 25);
            $g_rows = $this->db->select('id, id_mhs, judul_1, judul_en, peminatan, keterangan, date')
                ->where_in('id_mhs', $distinct_ids)
                ->limit(25)
                ->get('guidance')
                ->result_array();
            if (!empty($g_rows)) {
                foreach ($g_rows as $gr) {
                    $c_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $gr['id_mhs']);
                    $guidance_map[$gr['id_mhs']] = $gr;
                    $guidance_map[$c_nim] = $gr;
                    $guidance_map['usr_mhs_' . $c_nim] = $gr;
                }
            }
        }

        // Ambil juga berkas dari pendaftaran_berkas
        $pb_map = [];
        if ($this->db->table_exists('pendaftaran_berkas') && !empty($target_ids)) {
            $target_nims = array_slice(array_unique(array_map(function($id) {
                return preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $id);
            }, $target_ids)), 0, 15);
            $pb_rows = $this->db->select('id, nim, kode_berkas, file_name, status_verifikasi, catatan')
                ->where_in('nim', $target_nims)
                ->limit(40)
                ->get('pendaftaran_berkas')
                ->result_array();
            if (!empty($pb_rows)) {
                foreach ($pb_rows as $pbr) {
                    $pb_map[$pbr['nim']][$pbr['kode_berkas']] = $pbr;
                }
            }
        }

        if (!empty($target_ids)) {
            $distinct_fp_ids = array_slice(array_unique($target_ids), 0, 25);
            $this->db->where_in('id_mhs', $distinct_fp_ids);
        } else {
            $this->db->limit(30);
        }

        $files = $this->db->select('id, id_mhs, nama, file, status_doswal, komentar, date')
            ->order_by('id', 'DESC')
            ->limit(40)
            ->get('file_pendaftaran')
            ->result_array();
        if (empty($files) && empty($guidance_map)) return array();

        // Kelompokkan file berdasarkan id_mhs
        $grouped = [];
        foreach ($files as $f) {
            $idMhs = $f['id_mhs'];
            if (!isset($grouped[$idMhs])) {
                $grouped[$idMhs] = [
                    'id_mhs' => $idMhs,
                    'files'  => [],
                    'date'   => $f['date']
                ];
            }
            $grouped[$idMhs]['files'][$f['nama']] = $f;
        }

        // Pastikan mahasiswa yang ada di guidance_map tetap ada di grouped
        foreach ($guidance_map as $gKey => $gInfo) {
            $gIdMhs = $gInfo['id_mhs'];
            if (!isset($grouped[$gIdMhs])) {
                $grouped[$gIdMhs] = [
                    'id_mhs' => $gIdMhs,
                    'files'  => [],
                    'date'   => $gInfo['date']
                ];
            }
        }

        $results = [];
        foreach ($grouped as $idMhs => $info) {
            // Bersihkan NIM dari id_mhs (misal: 'usr_mhs_1301210001' -> '1301210001')
            $nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $idMhs);
            $user_row = $student_users[$nim] ?? ($student_users[$idMhs] ?? null);

            if (!$user_row && $user_tbl) {
                $user_row = $this->db->group_start()
                    ->where('id', $idMhs)
                    ->or_where('id', $nim)
                    ->or_where('username', $nim)
                    ->group_end()
                    ->get($user_tbl)
                    ->row_array();
                if ($user_row) {
                    $student_users[$nim] = $user_row;
                }
            }

            $namaMhs = $user_row['name'] ?? ('Mahasiswa ' . $nim);
            $emailMhs = $user_row['email'] ?? '';
            $prodiMhs = $user_row['prodi'] ?? '';

            $g_row = $guidance_map[$nim] ?? ($guidance_map[$idMhs] ?? null);
            $judul_1 = !empty($g_row['judul_1']) ? $g_row['judul_1'] : 'Usulan Judul Tugas Akhir';
            $status_judul = !empty($g_row['keterangan']) ? $g_row['keterangan'] : 'Pending';
            $catatan_judul = $g_row['komentar'] ?? '';
            $konsentrasi = !empty($g_row['peminatan']) ? $g_row['peminatan'] : $prodiMhs;
            $tgl_daftar = $g_row['date'] ?? ($info['date'] ?? date('Y-m-d H:i:s'));

            // Cek status persetujuan berkas oleh Dosen Wali
            $all_approved = true;
            $has_rejected = false;
            $has_file = false;

            $file_ksm = ''; $st_ksm = 'Pending';
            $file_transkrip = ''; $st_transkrip = 'Pending';
            $file_pernyataan = ''; $st_pernyataan = 'Pending';
            $file_bebas_lab = ''; $st_bebas_lab = 'Pending';

            $bMap = [];
            foreach ($info['files'] as $namaDoc => $fData) {
                $has_file = true;
                $st = $fData['status_doswal'] ?? 'Pending';
                if ($st !== 'Approved' && $st !== 'Valid') $all_approved = false;
                if ($st === 'Rejected' || $st === 'Invalid') $has_rejected = true;

                $cleanSt = ($st === 'Approved' || $st === 'Valid') ? 'Approved' : (($st === 'Rejected' || $st === 'Invalid') ? 'Rejected' : 'Pending');

                if (strpos($namaDoc, 'ksm') !== false) {
                    $file_ksm = $fData['file']; $st_ksm = $cleanSt;
                } elseif (strpos($namaDoc, 'transkrip') !== false) {
                    $file_transkrip = $fData['file']; $st_transkrip = $cleanSt;
                } elseif (strpos($namaDoc, 'pernyataan') !== false) {
                    $file_pernyataan = $fData['file']; $st_pernyataan = $cleanSt;
                } elseif (strpos($namaDoc, 'bebas_lab') !== false || strpos($namaDoc, 'lab') !== false) {
                    $file_bebas_lab = $fData['file']; $st_bebas_lab = $cleanSt;
                }

                $bMap[$namaDoc] = [
                    'file_name'         => $fData['file'],
                    'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                    'catatan'           => $fData['komentar'] ?? ''
                ];
            }

            // Gabungkan dengan pendaftaran_berkas jika ada berkas yang tersimpan di sana
            if (isset($pb_map[$nim])) {
                foreach ($pb_map[$nim] as $kb => $pbRow) {
                    $has_file = true;
                    $st_ver = $pbRow['status_verifikasi'] ?? 'Pending';
                    $cleanSt = ($st_ver === 'Valid' || $st_ver === 'Approved') ? 'Approved' : (($st_ver === 'Invalid' || $st_ver === 'Rejected') ? 'Rejected' : 'Pending');

                    if (empty($bMap[$kb])) {
                        $bMap[$kb] = [
                            'file_name'         => $pbRow['file_name'],
                            'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                            'catatan'           => $pbRow['catatan'] ?? ''
                        ];
                    }

                    if ($kb === 'ksm' && empty($file_ksm)) {
                        $file_ksm = $pbRow['file_name']; $st_ksm = $cleanSt;
                    } elseif ($kb === 'transkrip' && empty($file_transkrip)) {
                        $file_transkrip = $pbRow['file_name']; $st_transkrip = $cleanSt;
                    } elseif ($kb === 'pernyataan' && empty($file_pernyataan)) {
                        $file_pernyataan = $pbRow['file_name']; $st_pernyataan = $cleanSt;
                    } elseif ($kb === 'bebas_lab' && empty($file_bebas_lab)) {
                        $file_bebas_lab = $pbRow['file_name']; $st_bebas_lab = $cleanSt;
                    }

                    if ($cleanSt !== 'Approved') $all_approved = false;
                    if ($cleanSt === 'Rejected') $has_rejected = true;
                }
            }

            // Status wali
            if ($status_judul === 'Rejected') {
                $status_wali = 'Rejected';
            } elseif ($all_approved && $has_file && $status_judul === 'Approved') {
                $status_wali = 'Approved';
            } else {
                $status_wali = $has_rejected ? 'Rejected' : 'Pending';
            }

            $pt_info = null;
            if ($this->db->table_exists('pendaftaran_ta')) {
                $pt_info = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
            }
            $st_admin  = $pt_info['status_approval_admin'] ?? 'Pending';
            $st_koor   = $pt_info['status_approval_koor'] ?? 'Pending';
            $st_kk     = $pt_info['status_approval_kk'] ?? 'Pending';
            $cur_stage = !empty($pt_info['current_stage']) ? $pt_info['current_stage'] : (($status_wali === 'Approved') ? 'Admin Layanan' : ($status_wali === 'Rejected' ? 'Dosen Wali (Ditolak)' : 'Dosen Wali'));

            $results[] = [
                'id'                     => $idMhs,
                'nim'                    => $nim,
                'nama_depan'             => $namaMhs,
                'nama_belakang'          => '',
                'mhs_konsentrasi'        => $konsentrasi,
                'email'                  => $emailMhs,
                'judul_1'                => $judul_1,
                'status_judul'           => $status_judul,
                'catatan_judul'          => $catatan_judul,
                'status_approval_wali'   => $status_wali,
                'status_approval_admin'  => $st_admin,
                'status_approval_koor'   => $st_koor,
                'status_approval_kk'     => $st_kk,
                'current_stage'          => $cur_stage,
                'tgl_daftar'             => $tgl_daftar,
                'created_at'             => $tgl_daftar,
                'file_ksm'               => $file_ksm,
                'status_file_ksm'        => $st_ksm,
                'file_transkrip'         => $file_transkrip,
                'status_file_transkrip'  => $st_transkrip,
                'file_pernyataan'        => $file_pernyataan,
                'status_file_pernyataan' => $st_pernyataan,
                'file_bebas_lab'         => $file_bebas_lab,
                'status_file_bebas_lab'  => $st_bebas_lab,
                'berkas_map'             => $bMap,
                'total_berkas'           => count($bMap)
            ];
        }

        return $results;
    }

    /**
     * Helper: Ambil detail pendaftaran mahasiswa dari tabel file_pendaftaran
     */
    private function _get_detail_from_file_pendaftaran($nim) {
        $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
        $user_row = null;
        if ($user_tbl) {
            $user_row = $this->db->group_start()
                ->where('id', 'usr_mhs_' . $nim)
                ->or_where('id', $nim)
                ->or_where('username', $nim)
                ->or_where('nim', $nim)
                ->group_end()
                ->get($user_tbl)
                ->row_array();
        }

        $target_ids = array_unique(['usr_mhs_' . $nim, 'mhs_' . $nim, $nim]);
        $files = $this->db->where_in('id_mhs', $target_ids)->get('file_pendaftaran')->result_array();

        $guidance = null;
        if ($this->db->table_exists('guidance')) {
            $guidance = $this->db->where_in('id_mhs', $target_ids)->order_by('date', 'DESC')->get('guidance')->row_array();
        }

        if (empty($files) && empty($user_row) && empty($guidance)) return null;

        $namaMhs = $user_row['name'] ?? ('Mahasiswa ' . $nim);
        $emailMhs = $user_row['email'] ?? '';
        $prodiMhs = $user_row['prodi'] ?? '';

        $judul_1 = !empty($guidance['judul_1']) ? $guidance['judul_1'] : 'Usulan Judul Tugas Akhir';
        $judul_en = $guidance['judul_en'] ?? '';
        $jenis_ta = !empty(trim($guidance['jenis_TA'] ?? '')) ? trim($guidance['jenis_TA']) : (!empty(trim($guidance['jenis_ta'] ?? '')) ? trim($guidance['jenis_ta']) : '');
        $status_judul = !empty($guidance['keterangan']) ? $guidance['keterangan'] : 'Pending';
        $catatan_judul = $guidance['komentar'] ?? '';
        $konsentrasi = !empty($guidance['peminatan']) ? $guidance['peminatan'] : $prodiMhs;
        $tgl_daftar = $guidance['date'] ?? date('Y-m-d H:i:s');

        $all_approved = true;
        $has_rejected = false;
        $has_file = false;

        $file_ksm = ''; $st_ksm = 'Pending';
        $file_transkrip = ''; $st_transkrip = 'Pending';
        $file_pernyataan = ''; $st_pernyataan = 'Pending';
        $file_bebas_lab = ''; $st_bebas_lab = 'Pending';

        $bMap = [];
        foreach ($files as $fData) {
            $has_file = true;
            $namaDoc = $fData['nama'];
            if (!empty($fData['date']) && empty($guidance['date'])) $tgl_daftar = $fData['date'];

            $st = $fData['status_doswal'] ?? 'Pending';
            if ($st !== 'Approved' && $st !== 'Valid') $all_approved = false;
            if ($st === 'Rejected' || $st === 'Invalid') $has_rejected = true;

            $cleanSt = ($st === 'Approved' || $st === 'Valid') ? 'Approved' : (($st === 'Rejected' || $st === 'Invalid') ? 'Rejected' : 'Pending');

            if (strpos($namaDoc, 'ksm') !== false) {
                $file_ksm = $fData['file']; $st_ksm = $cleanSt;
            } elseif (strpos($namaDoc, 'transkrip') !== false) {
                $file_transkrip = $fData['file']; $st_transkrip = $cleanSt;
            } elseif (strpos($namaDoc, 'pernyataan') !== false) {
                $file_pernyataan = $fData['file']; $st_pernyataan = $cleanSt;
            } elseif (strpos($namaDoc, 'bebas_lab') !== false || strpos($namaDoc, 'lab') !== false) {
                $file_bebas_lab = $fData['file']; $st_bebas_lab = $cleanSt;
            }

            $bMap[$namaDoc] = [
                'file_name'         => $fData['file'],
                'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                'catatan'           => $fData['komentar'] ?? ''
            ];
        }

        // Ambil juga dari pendaftaran_berkas jika ada
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $pb_rows = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim])->result_array();
            foreach ($pb_rows as $pbRow) {
                $has_file = true;
                $kb = $pbRow['kode_berkas'];
                $st_ver = $pbRow['status_verifikasi'] ?? 'Pending';
                $cleanSt = ($st_ver === 'Valid' || $st_ver === 'Approved') ? 'Approved' : (($st_ver === 'Invalid' || $st_ver === 'Rejected') ? 'Rejected' : 'Pending');

                if (empty($bMap[$kb])) {
                    $bMap[$kb] = [
                        'file_name'         => $pbRow['file_name'],
                        'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                        'catatan'           => $pbRow['catatan'] ?? ''
                    ];
                }

                if ($kb === 'ksm' && empty($file_ksm)) {
                    $file_ksm = $pbRow['file_name']; $st_ksm = $cleanSt;
                } elseif ($kb === 'transkrip' && empty($file_transkrip)) {
                    $file_transkrip = $pbRow['file_name']; $st_transkrip = $cleanSt;
                } elseif ($kb === 'pernyataan' && empty($file_pernyataan)) {
                    $file_pernyataan = $pbRow['file_name']; $st_pernyataan = $cleanSt;
                } elseif ($kb === 'bebas_lab' && empty($file_bebas_lab)) {
                    $file_bebas_lab = $pbRow['file_name']; $st_bebas_lab = $cleanSt;
                }

                if ($cleanSt !== 'Approved') $all_approved = false;
                if ($cleanSt === 'Rejected') $has_rejected = true;
            }
        }

        if ($status_judul === 'Rejected') {
            $status_wali = 'Rejected';
        } elseif ($all_approved && $has_file && $status_judul === 'Approved') {
            $status_wali = 'Approved';
        } else {
            $status_wali = $has_rejected ? 'Rejected' : 'Pending';
        }

        $pt_info = null;
        if ($this->db->table_exists('pendaftaran_ta')) {
            $pt_info = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
        }
        $st_admin  = $pt_info['status_approval_admin'] ?? 'Pending';
        $st_koor   = $pt_info['status_approval_koor'] ?? 'Pending';
        $st_kk     = $pt_info['status_approval_kk'] ?? 'Pending';
        $cur_stage = !empty($pt_info['current_stage']) ? $pt_info['current_stage'] : (($status_wali === 'Approved') ? 'Admin Layanan' : ($status_wali === 'Rejected' ? 'Dosen Wali (Ditolak)' : 'Dosen Wali'));

        return [
            'id'                     => 'usr_mhs_' . $nim,
            'nim'                    => $nim,
            'nama_depan'             => $namaMhs,
            'nama_belakang'          => '',
            'mhs_konsentrasi'        => $konsentrasi,
            'email'                  => $emailMhs,
            'judul_1'                => $judul_1,
            'judul_en'               => $judul_en,
            'jenis_ta'               => $jenis_ta,
            'status_judul'           => $status_judul,
            'catatan_judul'          => $catatan_judul,
            'status_approval_wali'   => $status_wali,
            'status_approval_admin'  => $st_admin,
            'status_approval_koor'   => $st_koor,
            'status_approval_kk'     => $st_kk,
            'current_stage'          => $cur_stage,
            'tgl_daftar'             => $tgl_daftar,
            'created_at'             => $tgl_daftar,
            'file_ksm'               => $file_ksm,
            'status_file_ksm'        => $st_ksm,
            'file_transkrip'         => $file_transkrip,
            'status_file_transkrip'  => $st_transkrip,
            'file_pernyataan'        => $file_pernyataan,
            'status_file_pernyataan' => $st_pernyataan,
            'file_bebas_lab'         => $file_bebas_lab,
            'status_file_bebas_lab'  => $st_bebas_lab,
            'berkas_map'             => $bMap,
            'total_berkas'           => count($bMap)
        ];
    }

    /**
     * Helper: Update status berkas di tabel file_pendaftaran
     */
    private function _update_file_approval_file_pendaftaran($nim, $file_type, $status, $comment = '') {
        $ver = ($status === 'Approved') ? 'Approved' : (($status === 'Rejected') ? 'Rejected' : 'Pending');

        // Cari record yang cocok di file_pendaftaran
        $this->db->group_start()
            ->where('id_mhs', $nim)
            ->or_where('id_mhs', 'usr_mhs_' . $nim)
            ->or_where('id_mhs', 'mhs_' . $nim)
            ->or_like('id_mhs', $nim)
            ->group_end();
        $this->db->like('nama', $file_type);

        $update = $this->db->update('file_pendaftaran', [
            'status_doswal' => $ver,
            'komentar'      => $comment,
            'date_edit'     => date('Y-m-d H:i:s'),
            'view_doswal'   => 1
        ]);

        return $update;
    }
}

