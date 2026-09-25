<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // $this->_ensure_columns_exist(); // Disabled auto alter table
    }

    private function _ensure_columns_exist() {
        return;
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
        if (empty($nim) || empty($file_type)) return false;

        if ($this->db->table_exists('file_pendaftaran')) {
            $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
            $this->db->group_start()
                ->where_in('id_mhs', array_unique($target_ids))
                ->or_like('id_mhs', $nim)
            ->group_end()
            ->where('nama', $file_type)
            ->update('file_pendaftaran', ['view_doswal' => 1]);
        }

        return true;
    }

    // Update status approval per-file (Approved / Rejected / Pending) oleh Dosen Wali
    public function update_file_approval($nim, $file_type, $status, $comment = '') {
        $ver = ($status === 'Approved') ? 'Approved' : (($status === 'Rejected') ? 'Rejected' : 'Pending');
        $pb_ver = ($status === 'Approved') ? 'Valid' : (($status === 'Rejected') ? 'Invalid' : 'Pending');

        // 1. Update juga file_pendaftaran jika ada
        if ($this->db->table_exists('file_pendaftaran')) {
            $this->_update_file_approval_file_pendaftaran($nim, $file_type, $ver, $comment);
        }

        return true;
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

        return true;
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

        return true;
    }

    // Ambil detail pendaftaran banyak mahasiswa sekaligus untuk Cek Masal (Batch Modal)
    public function get_batch_details_by_nims($nims = array()) {
        if (empty($nims)) return array();
        $batch_res = [];
        foreach ($nims as $n) {
            $d = $this->_get_detail_from_file_pendaftaran($n);
            if ($d) $batch_res[] = $d;
        }
        return $batch_res;
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

        return true;
    }

    // Direct Batch Approve Dosen Wali untuk beberapa NIM sekaligus
    public function batch_approve_wali($nims = array()) {
        if (empty($nims)) {
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

        // Note: pendaftaran_berkas status_verifikasi belongs strictly to Admin LAA verification

        // Sync to file_pendaftaran table
        if ($this->db->table_exists('file_pendaftaran')) {
            $fp_fields = $this->db->list_fields('file_pendaftaran');
            $fp_up = array('status_doswal' => 'Approved');
            if (in_array('komentar', $fp_fields)) $fp_up['komentar'] = '';
            if (in_array('date_edit', $fp_fields)) $fp_up['date_edit'] = date('Y-m-d H:i:s');
            if (in_array('view_doswal', $fp_fields)) $fp_up['view_doswal'] = 1;

            foreach ($nims as $nim) {
                $this->db->group_start()
                    ->where('id_mhs', $nim)
                    ->or_where('id_mhs', 'usr_mhs_' . $nim)
                    ->or_where('id_mhs', 'mhs_' . $nim)
                    ->or_like('id_mhs', $nim)
                    ->group_end()
                    ->update('file_pendaftaran', $fp_up);
            }
        }

        // Sync to guidance table
        if ($this->db->table_exists('guidance')) {
            $g_fields = $this->db->list_fields('guidance');
            $g_up = array();
            if (in_array('keterangan', $g_fields)) $g_up['keterangan'] = 'Approved';
            if (in_array('komentar', $g_fields)) $g_up['komentar'] = '';
            if (in_array('date_edit', $g_fields)) $g_up['date_edit'] = date('Y-m-d H:i:s');

            if (!empty($g_up)) {
                foreach ($nims as $nim) {
                    $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
                    $this->db->where_in('id_mhs', $target_ids)->update('guidance', $g_up);
                }
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

                if ($this->db->table_exists('file_pendaftaran')) {
                    $this->_update_file_approval_file_pendaftaran($nim, $fk, $fStatus, $fNote);
                }

                // Note: pendaftaran_berkas status_verifikasi is reserved for Admin LAA verification

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
            if ($status_judul === 'Draft') continue; // Mahasiswa masih simpan draft, belum submit ("Kirim Pendaftaran")
            $catatan_judul = $g_row['komentar'] ?? '';
            $konsentrasi = !empty($g_row['peminatan']) ? $g_row['peminatan'] : $prodiMhs;
            $tgl_daftar = $g_row['date'] ?? ($info['date'] ?? date('Y-m-d H:i:s'));

            // Cek status persetujuan berkas oleh Dosen Wali
            $all_approved = true;
            $has_rejected = false;
            $has_file = false;

            $file_ksm = ''; $st_ksm = 'Pending'; $cat_ksm = '';
            $file_transkrip = ''; $st_transkrip = 'Pending'; $cat_transkrip = '';
            $file_pernyataan = ''; $st_pernyataan = 'Pending'; $cat_pernyataan = '';
            $file_bebas_lab = ''; $st_bebas_lab = 'Pending'; $cat_bebas_lab = '';

            $bMap = [];
            foreach ($info['files'] as $namaDoc => $fData) {
                $has_file = true;
                $st = $fData['status_doswal'] ?? 'Pending';
                if ($st !== 'Approved' && $st !== 'Valid') $all_approved = false;
                if ($st === 'Rejected' || $st === 'Invalid') $has_rejected = true;

                $cleanSt = ($st === 'Approved' || $st === 'Valid') ? 'Approved' : (($st === 'Rejected' || $st === 'Invalid') ? 'Rejected' : 'Pending');
                $kom = $fData['komentar'] ?? '';

                if (strpos($namaDoc, 'ksm') !== false) {
                    $file_ksm = $fData['file']; $st_ksm = $cleanSt; $cat_ksm = $kom;
                } elseif (strpos($namaDoc, 'transkrip') !== false) {
                    $file_transkrip = $fData['file']; $st_transkrip = $cleanSt; $cat_transkrip = $kom;
                } elseif (strpos($namaDoc, 'pernyataan') !== false) {
                    $file_pernyataan = $fData['file']; $st_pernyataan = $cleanSt; $cat_pernyataan = $kom;
                } elseif (strpos($namaDoc, 'bebas_lab') !== false || strpos($namaDoc, 'lab') !== false) {
                    $file_bebas_lab = $fData['file']; $st_bebas_lab = $cleanSt; $cat_bebas_lab = $kom;
                }

                $bMap[$namaDoc] = [
                    'file_name'         => $fData['file'],
                    'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                    'catatan'           => $kom
                ];
            }

            // Gabungkan dengan pendaftaran_berkas jika ada berkas yang tersimpan di sana
            if (isset($pb_map[$nim])) {
                foreach ($pb_map[$nim] as $kb => $pbRow) {
                    $has_file = true;
                    $st_ver = $pbRow['status_verifikasi'] ?? 'Pending';
                    $cleanSt = ($st_ver === 'Valid' || $st_ver === 'Approved') ? 'Approved' : (($st_ver === 'Invalid' || $st_ver === 'Rejected') ? 'Rejected' : 'Pending');
                    $pbCat = $pbRow['catatan'] ?? '';

                    if (empty($bMap[$kb])) {
                        $bMap[$kb] = [
                            'file_name'         => $pbRow['file_name'],
                            'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                            'catatan'           => $pbCat
                        ];
                    }

                    if ($kb === 'ksm' && empty($file_ksm)) {
                        $file_ksm = $pbRow['file_name']; $st_ksm = $cleanSt; if (empty($cat_ksm)) $cat_ksm = $pbCat;
                    } elseif ($kb === 'transkrip' && empty($file_transkrip)) {
                        $file_transkrip = $pbRow['file_name']; $st_transkrip = $cleanSt; if (empty($cat_transkrip)) $cat_transkrip = $pbCat;
                    } elseif ($kb === 'pernyataan' && empty($file_pernyataan)) {
                        $file_pernyataan = $pbRow['file_name']; $st_pernyataan = $cleanSt; if (empty($cat_pernyataan)) $cat_pernyataan = $pbCat;
                    } elseif ($kb === 'bebas_lab' && empty($file_bebas_lab)) {
                        $file_bebas_lab = $pbRow['file_name']; $st_bebas_lab = $cleanSt; if (empty($cat_bebas_lab)) $cat_bebas_lab = $pbCat;
                    }

                    // Note: pendaftaran_berkas status_verifikasi belongs to Admin LAA, so do not alter Dosen Wali $all_approved
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

            $st_admin  = 'Pending';
            $st_koor   = 'Pending';
            $st_kk     = 'Pending';
            $cur_stage = ($status_wali === 'Approved') ? 'Admin Layanan' : ($status_wali === 'Rejected' ? 'Dosen Wali (Ditolak)' : 'Dosen Wali');

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
                'catatan_file_ksm'       => $cat_ksm,
                'file_transkrip'         => $file_transkrip,
                'status_file_transkrip'  => $st_transkrip,
                'catatan_file_transkrip' => $cat_transkrip,
                'file_pernyataan'        => $file_pernyataan,
                'status_file_pernyataan' => $st_pernyataan,
                'catatan_file_pernyataan'=> $cat_pernyataan,
                'file_bebas_lab'         => $file_bebas_lab,
                'status_file_bebas_lab'  => $st_bebas_lab,
                'catatan_file_bebas_lab' => $cat_bebas_lab,
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

        $mhs_tbl = $this->db->table_exists('mahasiswa') ? 'mahasiswa' : null;
        $mhs_row = null;
        if ($mhs_tbl) {
            $mhs_row = $this->db->get_where('mahasiswa', ['nim' => $nim])->row_array();
        }

        $target_ids = array_values(array_unique(array_filter([$user_row['id'] ?? null, 'usr_mhs_' . $nim, 'mhs_' . $nim, $nim])));
        $files = [];
        if ($this->db->table_exists('file_pendaftaran')) {
            $files = $this->db->where_in('id_mhs', $target_ids)->get('file_pendaftaran')->result_array();
        }

        $guidance = null;
        if ($this->db->table_exists('guidance')) {
            $guidance = $this->db->where_in('id_mhs', $target_ids)->order_by('date', 'DESC')->get('guidance')->row_array();
            if (!$guidance) {
                $guidance = $this->db->get_where('guidance', ['id' => 'gdn_' . $nim])->row_array();
            }
        }

        $namaMhs = !empty($user_row['name'])
            ? $user_row['name']
            : (!empty($mhs_row['nama_depan'])
                ? trim($mhs_row['nama_depan'] . ' ' . ($mhs_row['nama_belakang'] ?? ''))
                : ('Mahasiswa ' . $nim));
        $emailMhs = $user_row['email'] ?? ($mhs_row['email'] ?? '-');
        $prodiMhs = $user_row['prodi'] ?? ($mhs_row['prodi'] ?? ($mhs_row['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual'));
        $noHpMhs  = $user_row['no_telp'] ?? ($user_row['no_hp'] ?? ($mhs_row['no_hp'] ?? '-'));

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

        $file_ksm = ''; $st_ksm = 'Pending'; $cat_ksm = '';
        $file_transkrip = ''; $st_transkrip = 'Pending'; $cat_transkrip = '';
        $file_pernyataan = ''; $st_pernyataan = 'Pending'; $cat_pernyataan = '';
        $file_bebas_lab = ''; $st_bebas_lab = 'Pending'; $cat_bebas_lab = '';

        $bMap = [];
        foreach ($files as $fData) {
            $has_file = true;
            $namaDoc = $fData['nama'];
            if (!empty($fData['date']) && empty($guidance['date'])) $tgl_daftar = $fData['date'];

            $st = $fData['status_doswal'] ?? 'Pending';
            if ($st !== 'Approved' && $st !== 'Valid') $all_approved = false;
            if ($st === 'Rejected' || $st === 'Invalid') $has_rejected = true;

            $cleanSt = ($st === 'Approved' || $st === 'Valid') ? 'Approved' : (($st === 'Rejected' || $st === 'Invalid') ? 'Rejected' : 'Pending');
            $kom = $fData['komentar'] ?? '';

            if (strpos($namaDoc, 'ksm') !== false) {
                $file_ksm = $fData['file']; $st_ksm = $cleanSt; $cat_ksm = $kom;
            } elseif (strpos($namaDoc, 'transkrip') !== false) {
                $file_transkrip = $fData['file']; $st_transkrip = $cleanSt; $cat_transkrip = $kom;
            } elseif (strpos($namaDoc, 'pernyataan') !== false) {
                $file_pernyataan = $fData['file']; $st_pernyataan = $cleanSt; $cat_pernyataan = $kom;
            } elseif (strpos($namaDoc, 'bebas_lab') !== false || strpos($namaDoc, 'lab') !== false) {
                $file_bebas_lab = $fData['file']; $st_bebas_lab = $cleanSt; $cat_bebas_lab = $kom;
            }

            $bMap[$namaDoc] = [
                'file_name'         => $fData['file'],
                'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                'catatan'           => $kom
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
                $pbCat = $pbRow['catatan'] ?? '';

                if (empty($bMap[$kb])) {
                    $bMap[$kb] = [
                        'file_name'         => $pbRow['file_name'],
                        'status_verifikasi' => ($cleanSt === 'Approved') ? 'Valid' : (($cleanSt === 'Rejected') ? 'Invalid' : 'Pending'),
                        'catatan'           => $pbCat
                    ];
                }

                if ($kb === 'ksm' && empty($file_ksm)) {
                    $file_ksm = $pbRow['file_name']; $st_ksm = $cleanSt; if (empty($cat_ksm)) $cat_ksm = $pbCat;
                } elseif ($kb === 'transkrip' && empty($file_transkrip)) {
                    $file_transkrip = $pbRow['file_name']; $st_transkrip = $cleanSt; if (empty($cat_transkrip)) $cat_transkrip = $pbCat;
                } elseif ($kb === 'pernyataan' && empty($file_pernyataan)) {
                    $file_pernyataan = $pbRow['file_name']; $st_pernyataan = $cleanSt; if (empty($cat_pernyataan)) $cat_pernyataan = $pbCat;
                } elseif ($kb === 'bebas_lab' && empty($file_bebas_lab)) {
                    $file_bebas_lab = $pbRow['file_name']; $st_bebas_lab = $cleanSt; if (empty($cat_bebas_lab)) $cat_bebas_lab = $pbCat;
                }

                // Note: pendaftaran_berkas status_verifikasi belongs to Admin LAA, so do not alter Dosen Wali $all_approved
            }
        }

        if ($status_judul === 'Rejected') {
            $status_wali = 'Rejected';
        } elseif ($all_approved && $has_file && $status_judul === 'Approved') {
            $status_wali = 'Approved';
        } else {
            $status_wali = $has_rejected ? 'Rejected' : 'Pending';
        }

        $st_admin  = 'Pending';
        $st_koor   = 'Pending';
        $st_kk     = 'Pending';
        $cur_stage = ($status_wali === 'Approved') ? 'Admin Layanan' : ($status_wali === 'Rejected' ? 'Dosen Wali (Ditolak)' : 'Dosen Wali');

        return [
            'id'                     => 'usr_mhs_' . $nim,
            'nim'                    => $nim,
            'nama_depan'             => $namaMhs,
            'nama_belakang'          => '',
            'mhs_konsentrasi'        => $konsentrasi,
            'prodi'                  => $prodiMhs,
            'email'                  => $emailMhs,
            'no_hp'                  => $noHpMhs,
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
            'catatan_file_ksm'       => $cat_ksm,
            'file_transkrip'         => $file_transkrip,
            'status_file_transkrip'  => $st_transkrip,
            'catatan_file_transkrip' => $cat_transkrip,
            'file_pernyataan'        => $file_pernyataan,
            'status_file_pernyataan' => $st_pernyataan,
            'catatan_file_pernyataan'=> $cat_pernyataan,
            'file_bebas_lab'         => $file_bebas_lab,
            'status_file_bebas_lab'  => $st_bebas_lab,
            'catatan_file_bebas_lab' => $cat_bebas_lab,
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

