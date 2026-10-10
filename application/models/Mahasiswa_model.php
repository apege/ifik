<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // =================================================================
    // HELPERS
    // =================================================================

    private function _get_user_id_by_nim($nim) {
        if (empty($nim)) return $nim;
        if (!$this->db->table_exists('user')) return $nim;

        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $u = null;
        try {
            $u = $this->db->select('id, nim, username')
                ->group_start()
                    ->where('nim', $nim)
                    ->or_where('username', $nim)
                ->group_end()
                ->limit(1)
                ->get('user')->row_array();
        } catch (Throwable $e) {
            $u = null;
        }
        $this->db->db_debug = $prev_debug;

        return $u ? $u['id'] : $nim;
    }

    private function _get_target_ids_by_nim($nim) {
        if (empty($nim)) return [];
        $userId = $this->_get_user_id_by_nim($nim);
        return array_values(array_unique(array_filter([$userId, $nim, 'usr_mhs_' . $nim, 'mhs_' . $nim])));
    }

    private function _get_guidance_id_by_nim($nim) {
        if (empty($nim)) return null;
        if (!$this->db->table_exists('guidance')) return null;

        $target_ids = $this->_get_target_ids_by_nim($nim);

        $g = $this->db->select('id')
            ->where_in('id_mhs', $target_ids)
            ->order_by('date', 'DESC')
            ->limit(1)
            ->get('guidance')->row_array();

        if (!$g) {
            $g = $this->db->select('id')
                ->get_where('guidance', ['id' => 'gdn_' . $nim])
                ->row_array();
        }

        return $g ? $g['id'] : null;
    }

    private function _tahap_to_enum($tahap) {
        $map = [
            'Preview 1' => 'preview1',
            'Preview 2' => 'preview2',
            'Preview 3' => 'preview3',
            'Sidang'    => 'sidang',
            'preview1'  => 'preview1',
            'preview2'  => 'preview2',
            'preview3'  => 'preview3',
            'sidang'    => 'sidang',
        ];
        return $map[$tahap] ?? 'preview1';
    }

    // =================================================================
    // MAHASISWA
    // =================================================================

    public function get_mahasiswa($nim) {
        $session_name = $this->session->userdata('name');
        $session_nim  = $this->session->userdata('nim') ?: $this->session->userdata('nidn_nim');

        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $user_row = null;
        try {
            if (!empty($nim) && $this->db->table_exists('user')) {
                $user_row = $this->db
                    ->select('id, name, username, email')
                    ->group_start()
                        ->where('nim', $nim)
                        ->or_where('username', $nim)
                    ->group_end()
                    ->limit(1)
                    ->get('user')
                    ->row_array();
            }
            if (!$user_row && $this->session->userdata('user_id') && $this->db->table_exists('user')) {
                $user_row = $this->db
                    ->select('id, name, username, email')
                    ->where('id', $this->session->userdata('user_id'))
                    ->limit(1)
                    ->get('user')
                    ->row_array();
            }
        } catch (Throwable $e) {
            $user_row = null;
        }

        $full_name = !empty($user_row['name']) ? $user_row['name'] : ($session_name ?: 'Mahasiswa');
        $parts = explode(' ', trim($full_name), 2);
        $nama_depan_default    = $parts[0] ?? 'Mahasiswa';
        $nama_belakang_default = $parts[1] ?? '';

        $data_mhs = null;
        try {
            if ($this->db->table_exists('mahasiswa') && !empty($nim)) {
                $data_mhs = $this->db->get_where('mahasiswa', ['nim' => $nim])->row_array();
            }
        } catch (Throwable $e) {
            $data_mhs = null;
        }
        $this->db->db_debug = $prev_debug;

        if ($data_mhs) {
            if (empty($data_mhs['nama_depan'])) {
                $data_mhs['nama_depan']    = $nama_depan_default;
                $data_mhs['nama_belakang'] = $nama_belakang_default;
            }
            if (empty($data_mhs['nim'])) {
                $data_mhs['nim'] = $nim ?: $session_nim;
            }
            return $data_mhs;
        }

        return [
            'nim'             => $nim ?: ($session_nim ?: ''),
            'nama_depan'      => $nama_depan_default,
            'nama_belakang'   => $nama_belakang_default,
            'alamat'          => '',
            'kota'            => '',
            'provinsi'        => '',
            'latitude'        => '',
            'longitude'       => '',
            'konsentrasi_dkv' => 'Desain Komunikasi Visual',
            'prodi'           => 'Desain Komunikasi Visual',
        ];
    }

    public function update_geodata($nim, $data_geodata) {
        if (!$this->db->table_exists('mahasiswa')) return true;
        $this->db->where('nim', $nim);
        return $this->db->update('mahasiswa', $data_geodata);
    }

    // =================================================================
    // PENDAFTARAN TA (guidance + file_pendaftaran)
    // =================================================================

    public function save_pendaftaran_ta($data_ta) {
        $nim = $data_ta['nim'] ?? null;
        if (!$nim) return false;

        $userId = $this->_get_user_id_by_nim($nim);
        $target_ids = $this->_get_target_ids_by_nim($nim);

        // 1. guidance
        if ($this->db->table_exists('guidance')) {
            $existing_g = $this->db->where_in('id_mhs', $target_ids)
                ->order_by('date', 'DESC')
                ->limit(1)
                ->get('guidance')->row_array();
            if (!$existing_g) {
                $existing_g = $this->db->get_where('guidance', ['id' => 'gdn_' . $nim])->row_array();
            }

            $prev_dbg = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            @$this->db->query("ALTER TABLE `guidance` MODIFY COLUMN `jenis_TA` VARCHAR(100) DEFAULT 'TA Reguler'");
            $this->db->db_debug = $prev_dbg;

            $g_fields = $this->db->list_fields('guidance');
            $g_data   = [];

            if (in_array('judul_1', $g_fields)) {
                $g_data['judul_1'] = (isset($data_ta['judul_1']) && trim($data_ta['judul_1']) !== '')
                    ? trim($data_ta['judul_1'])
                    : ($existing_g['judul_1'] ?? '');
            }
            if (in_array('judul_2', $g_fields)) {
                $g_data['judul_2'] = (isset($data_ta['judul_2']) && trim($data_ta['judul_2']) !== '')
                    ? trim($data_ta['judul_2'])
                    : ($existing_g['judul_2'] ?? '');
            }
            if (in_array('judul_3', $g_fields)) {
                $g_data['judul_3'] = (isset($data_ta['judul_3']) && trim($data_ta['judul_3']) !== '')
                    ? trim($data_ta['judul_3'])
                    : ($existing_g['judul_3'] ?? '');
            }
            if (in_array('judul_en', $g_fields)) {
                $g_data['judul_en'] = (isset($data_ta['judul_en']) && trim($data_ta['judul_en']) !== '')
                    ? trim($data_ta['judul_en'])
                    : ($existing_g['judul_en'] ?? '');
            }
            if (in_array('jenis_TA', $g_fields)) {
                $g_data['jenis_TA'] = !empty(trim($data_ta['jenis_ta'] ?? ''))
                    ? trim($data_ta['jenis_ta'])
                    : (!empty(trim($existing_g['jenis_TA'] ?? '')) ? trim($existing_g['jenis_TA']) : '');
            }
            if (in_array('peminatan', $g_fields)) {
                $g_data['peminatan'] = !empty(trim($data_ta['konsentrasi_dkv'] ?? ''))
                    ? trim($data_ta['konsentrasi_dkv'])
                    : (!empty(trim($existing_g['peminatan'] ?? '')) ? trim($existing_g['peminatan']) : 'Informatika');
            }
            if (in_array('tahun', $g_fields)) $g_data['tahun'] = date('Y');
            $is_sub_flag = !empty($data_ta['is_submitted']);
            if (in_array('keterangan', $g_fields)) {
                $g_data['keterangan'] = $is_sub_flag ? ($data_ta['status_approval_wali'] ?? 'Pending') : ($existing_g['keterangan'] ?? 'Draft');
            }
            if (in_array('date',       $g_fields) && empty($existing_g)) $g_data['date'] = date('Y-m-d H:i:s');
            if (in_array('date_edit',  $g_fields)) $g_data['date_edit'] = date('Y-m-d H:i:s');

            if ($existing_g) {
                $this->db->where('id', $existing_g['id'])->update('guidance', $g_data);
            } else {
                if (in_array('id',     $g_fields)) $g_data['id']     = 'gdn_' . $nim;
                if (in_array('id_mhs', $g_fields)) $g_data['id_mhs'] = $userId ?: ('usr_mhs_' . $nim);
                $this->db->insert('guidance', $g_data);
            }
        }

        // 2. file_pendaftaran
        if ($this->db->table_exists('file_pendaftaran')) {
            $files = [
                'ksm'        => $data_ta['file_ksm']        ?? null,
                'transkrip'  => $data_ta['file_transkrip']  ?? null,
                'pernyataan' => $data_ta['file_pernyataan'] ?? null,
                'bebas_lab'  => $data_ta['file_bebas_lab']  ?? null,
                'bap'        => $data_ta['file_bap']        ?? null,
            ];
            $fp_fields = $this->db->list_fields('file_pendaftaran');

            foreach ($files as $kode => $fileName) {
                if (empty($fileName)) continue;
                $relPath = (strpos($fileName, 'uploads/') === 0) ? $fileName : ('uploads/persyaratan_ta/' . $fileName);
                $targetFpId = 'fp_' . $nim . '_' . $kode;

                $exFp = $this->db->get_where('file_pendaftaran', ['id' => $targetFpId])->row_array();
                if (!$exFp) {
                    $exFp = $this->db->where_in('id_mhs', $target_ids)
                        ->where('nama', $kode)
                        ->get('file_pendaftaran')->row_array();
                }

                $fpData = [];
                if (in_array('file',            $fp_fields)) $fpData['file']            = $relPath;
                if (in_array('status_doswal',   $fp_fields)) $fpData['status_doswal']   = 'Pending';
                if (in_array('status_adminlaa', $fp_fields)) $fpData['status_adminlaa'] = 'Pending';
                if (in_array('date_edit',       $fp_fields)) $fpData['date_edit']       = date('Y-m-d H:i:s');

                if ($exFp) {
                    $this->db->where('id', $exFp['id'])->update('file_pendaftaran', $fpData);
                } else {
                    if (in_array('id',            $fp_fields)) $fpData['id']            = $targetFpId;
                    if (in_array('id_mhs',        $fp_fields)) $fpData['id_mhs']        = $userId ?: ('usr_mhs_' . $nim);
                    if (in_array('nama',          $fp_fields)) $fpData['nama']          = $kode;
                    if (in_array('view_adminlaa', $fp_fields)) $fpData['view_adminlaa'] = 0;
                    if (in_array('view_doswal',   $fp_fields)) $fpData['view_doswal']   = 0;
                    if (in_array('komentar',      $fp_fields)) $fpData['komentar']      = '';
                    if (in_array('date',          $fp_fields)) $fpData['date']          = date('Y-m-d H:i:s');

                    $checkFpAgain = $this->db->get_where('file_pendaftaran', ['id' => $targetFpId])->row_array();
                    if ($checkFpAgain) {
                        $this->db->where('id', $targetFpId)->update('file_pendaftaran', $fpData);
                    } else {
                        $this->db->insert('file_pendaftaran', $fpData);
                    }
                }
            }
        }

        return true;
    }

    public function get_status_pendaftaran($nim) {
        $pt_data = array();
        $target_ids = $this->_get_target_ids_by_nim($nim);

        $guidance = null;
        if ($this->db->table_exists('guidance')) {
            $prev_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            try {
                $q_g = $this->db->select('id, id_mhs, judul_1, judul_2, judul_3, judul_en, jenis_TA, peminatan, komentar, keterangan, date, lulus_preview1, lulus_preview2, lulus_preview3, lulus_sidang, status_preview')
                    ->where_in('id_mhs', $target_ids)
                    ->order_by('date', 'DESC')
                    ->limit(1)
                    ->get('guidance');
                if ($q_g) {
                    $guidance = $q_g->row_array();
                }
                if (!$guidance) {
                    $guidance = $this->db->get_where('guidance', ['id' => 'gdn_' . $nim])->row_array();
                }
            } catch (Throwable $e) {
                log_message('error', 'Error fetching guidance: ' . $e->getMessage());
                $guidance = null;
            }
            $this->db->db_debug = $prev_debug;
        }

        $files = [];
        if ($this->db->table_exists('file_pendaftaran')) {
            $prev_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            try {
                $q_f = $this->db->select('id, id_mhs, nama, file, status_doswal, status_adminlaa, komentar')
                    ->where_in('id_mhs', $target_ids)
                    ->get('file_pendaftaran');
                if ($q_f) {
                    $fileRows = $q_f->result_array();
                    foreach ($fileRows as $fr) {
                        $files[$fr['nama']] = $fr;
                    }
                }
            } catch (Throwable $e) {
                log_message('error', 'Error fetching file_pendaftaran: ' . $e->getMessage());
            }
            $this->db->db_debug = $prev_debug;
        }

        $berkas_rows = array();
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            if ($this->db->table_exists('pendaftaran_berkas')) {
                $pb_rows = $this->db->get_where('pendaftaran_berkas', array('nim' => $nim))->result_array();
                foreach ($pb_rows as $pbr) {
                    $berkas_rows[$pbr['kode_berkas']] = $pbr;
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'Error fetching pendaftaran_berkas: ' . $e->getMessage());
        }

        $active_keys = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        try {
            if ($this->db->table_exists('syarat_berkas_ta')) {
                $sb_rows = $this->db->get_where('syarat_berkas_ta', array('is_active' => 1))->result_array();
                if (!empty($sb_rows)) {
                    $active_keys = array_column($sb_rows, 'kode_berkas');
                }
            }
        } catch (Throwable $e) {
            log_message('error', 'Error fetching syarat_berkas_ta: ' . $e->getMessage());
        }
        $this->db->db_debug = $prev_debug;

        $g_ket = $guidance['keterangan'] ?? 'Draft';
        $is_submitted = 0;
        if (!empty($pt_data['is_submitted'])) {
            $is_submitted = 1;
        } elseif ($guidance && !empty($g_ket) && strtolower($g_ket) !== 'draft') {
            $is_submitted = 1;
        }

        // Safety fallback: jika semua berkas fisik kosong (karena baru direset Admin LAA atau belum upload),
        // formulir harus berstatus belum diajukan (draft) agar mahasiswa dapat mengunggah berkas.
        $has_any_file_uploaded = false;
        foreach ($active_keys as $k) {
            if (!empty($files[$k]['file']) || !empty($berkas_rows[$k]['file_name']) || !empty($pt_data['file_' . $k])) {
                $has_any_file_uploaded = true;
                break;
            }
        }
        if (!$has_any_file_uploaded) {
            $is_submitted = 0;
        }

        $status_judul = $pt_data['status_judul'] ?? null;
        $catatan_judul = $pt_data['catatan_judul'] ?? null;

        if (empty($status_judul) && $guidance) {
            $g_ket = $guidance['keterangan'] ?? 'Pending';
            if ($g_ket === 'Approved' || $g_ket === 'Rejected') {
                $status_judul = $g_ket;
                if (empty($catatan_judul)) $catatan_judul = $guidance['komentar'] ?? '';
            } else {
                $status_judul = 'Pending';
            }
        }
        if (empty($status_judul)) $status_judul = 'Pending';
        if ($catatan_judul === null) $catatan_judul = $guidance['komentar'] ?? '';

        $status_jenis_ta = $pt_data['status_jenis_ta'] ?? null;
        $catatan_jenis_ta = $pt_data['catatan_jenis_ta'] ?? null;
        if (empty($status_jenis_ta) || $status_jenis_ta === 'Pending') {
            if ($status_judul === 'Approved' || ($pt_data['status_approval_wali'] ?? '') === 'Approved') {
                $status_jenis_ta = 'Approved';
            } else {
                $status_jenis_ta = 'Pending';
            }
        }
        if ($catatan_jenis_ta === null) $catatan_jenis_ta = '';

        $files_result = array();
        $has_any_file_rej_wali = false;
        $has_any_file_rej_admin = false;
        $all_files_app_wali = true;
        $all_files_app_admin = true;
        $has_any_file_app_admin = false;
        $file_count = 0;

        $catatan_wali = !empty($pt_data['catatan_wali']) ? $pt_data['catatan_wali'] : ($guidance['komentar'] ?? '');
        $catatan_admin = !empty($pt_data['catatan_admin']) ? $pt_data['catatan_admin'] : '';

        foreach ($active_keys as $k) {
            $file_count++;
            $f_obj = $files[$k] ?? null;
            $b_obj = $berkas_rows[$k] ?? null;

            $fname = '';
            if (!empty($pt_data['file_' . $k])) {
                $fname = $pt_data['file_' . $k];
            } elseif ($f_obj && !empty($f_obj['file'])) {
                $fname = basename($f_obj['file']);
            } elseif ($b_obj && !empty($b_obj['file_name'])) {
                $fname = $b_obj['file_name'];
            }
            $files_result['file_' . $k] = $fname;

            $st_dw = $pt_data['status_file_' . $k] ?? null;
            if (empty($st_dw)) {
                if ($f_obj && !empty($f_obj['status_doswal'])) {
                    $st_dw = $f_obj['status_doswal'];
                } elseif (!empty($guidance['keterangan']) && $guidance['keterangan'] === 'Approved') {
                    $st_dw = 'Approved';
                } elseif (!empty($guidance['keterangan']) && $guidance['keterangan'] === 'Rejected') {
                    $st_dw = 'Rejected';
                } else {
                    $st_dw = 'Pending';
                }
            }
            $files_result['status_file_' . $k] = $st_dw;

            if ($st_dw === 'Rejected') {
                $has_any_file_rej_wali = true;
                $all_files_app_wali = false;
            } elseif ($st_dw !== 'Approved') {
                $all_files_app_wali = false;
            }

            $c_dw = $pt_data['catatan_file_' . $k] ?? null;
            if (empty($c_dw)) {
                if ($f_obj && !empty($f_obj['komentar'])) {
                    $c_dw = $f_obj['komentar'];
                } else {
                    $c_dw = '';
                }
            }
            $files_result['catatan_file_' . $k] = $c_dw;

            // Ambil status & komentar Admin Layanan murni dari table file_pendaftaran
            $st_laa = !empty($f_obj['status_adminlaa']) ? $f_obj['status_adminlaa'] : 'Pending';
            $files_result['status_' . $k] = $st_laa;
            $files_result['catatan_admin_' . $k] = $f_obj['komentar'] ?? '';
            
            $st_laa_clean = strtolower(trim((string)$st_laa));
            if ($st_laa_clean === 'invalid' || strpos($st_laa_clean, 'revisi') !== false || strpos($st_laa_clean, 'tolak') !== false || strpos($st_laa_clean, 'rejected') !== false) {
                $has_any_file_rej_admin = true;
                $all_files_app_admin = false;
            } elseif ($st_laa_clean === 'valid' || strpos($st_laa_clean, 'setuju') !== false || strpos($st_laa_clean, 'approved') !== false || $st_laa_clean === 'acc') {
                $has_any_file_app_admin = true;
            } else {
                $all_files_app_admin = false;
            }
        }

        if (!empty($pt_data['status_approval_wali'])) {
            $status_doswal = $pt_data['status_approval_wali'];
        } else {
            if ($has_any_file_rej_wali || $status_judul === 'Rejected' || $status_jenis_ta === 'Rejected') {
                $status_doswal = 'Rejected';
            } elseif ($all_files_app_wali && $file_count > 0 && $status_judul === 'Approved') {
                $status_doswal = 'Approved';
            } else {
                $status_doswal = 'Pending';
            }
        }

        if (!empty($pt_data['status_approval_admin'])) {
            $status_laa = $pt_data['status_approval_admin'];
        } else {
            if ($has_any_file_rej_admin) {
                $status_laa = 'Rejected';
            } elseif ($all_files_app_admin && $file_count > 0 && $has_any_file_app_admin) {
                $status_laa = 'Approved';
            } else {
                $status_laa = 'Pending';
            }
        }

        $status_koor = $pt_data['status_approval_koor'] ?? null;
        $status_kk   = $pt_data['status_approval_kk'] ?? null;
        if (empty($status_koor) || empty($status_kk)) {
            $gid = $this->_get_guidance_id_by_nim($nim);
            $tl = ($gid && $this->db->table_exists('thesis_lecturers')) 
                ? $this->db->get_where('thesis_lecturers', ['id_guidance' => $gid])->row_array() 
                : null;
            if (empty($status_koor)) {
                if ($tl && !empty($tl['dosen_pembimbing1']) && !empty($tl['dosen_pembimbing2'])) {
                    $status_koor = 'Approved';
                } else {
                    $status_koor = 'Pending';
                }
            }
            if (empty($status_kk)) {
                if ($tl && !empty($tl['status'])) {
                    $status_kk = $tl['status'];
                } else {
                    $status_kk = 'Pending';
                }
            }
        }

        $current_stage = $pt_data['current_stage'] ?? (
            ($status_doswal !== 'Approved') 
                ? ($status_doswal === 'Rejected' ? 'Dosen Wali (Revisi)' : 'Dosen Wali') 
                : (($status_laa !== 'Approved') 
                    ? ($status_laa === 'Rejected' ? 'Admin Layanan (Revisi)' : 'Admin Layanan') 
                    : (($status_koor !== 'Approved') 
                        ? 'Koordinator TA' 
                        : (($status_kk !== 'Approved') ? 'Ketua KK' : 'Selesai')))
        );

        $res = array_merge(array(
            'nim'                   => $nim,
            'is_submitted'          => $is_submitted,
            'jenis_ta'              => !empty(trim($guidance['jenis_TA'] ?? '')) 
                                        ? trim($guidance['jenis_TA']) 
                                        : (!empty(trim($guidance['jenis_ta'] ?? '')) 
                                            ? trim($guidance['jenis_ta']) 
                                            : (!empty(trim($pt_data['jenis_ta'] ?? '')) ? trim($pt_data['jenis_ta']) : '')),
            'judul_1'               => $guidance['judul_1'] ?? ($pt_data['judul_1'] ?? ''),
            'judul_2'               => $guidance['judul_2'] ?? ($pt_data['judul_2'] ?? ''),
            'judul_3'               => $guidance['judul_3'] ?? ($pt_data['judul_3'] ?? ''),
            'judul_en'              => $guidance['judul_en'] ?? ($pt_data['judul_en'] ?? ''),
            'konsentrasi_dkv'       => $guidance['peminatan'] ?? ($pt_data['konsentrasi_dkv'] ?? 'Informatika'),
            'status_approval_wali'  => $status_doswal,
            'status_approval_admin' => $status_laa,
            'status_approval_koor'  => $status_koor,
            'status_approval_kk'    => $status_kk,
            'status_judul'          => $status_judul,
            'catatan_judul'         => $catatan_judul,
            'status_jenis_ta'       => $status_jenis_ta,
            'catatan_jenis_ta'      => $catatan_jenis_ta,
            'current_stage'         => $current_stage,
            'catatan_wali'          => trim($catatan_wali),
            'catatan_admin'         => trim($catatan_admin),
            'catatan_koor'          => $pt_data['catatan_koor'] ?? '',
            'berkas_kurang'         => $pt_data['berkas_kurang'] ?? null,
            'created_at'            => $pt_data['created_at'] ?? ($guidance['date'] ?? null),
            'updated_at'            => $pt_data['updated_at'] ?? ($guidance['date'] ?? null)
        ), $files_result);

        // =========================================================
        // BAP PUBLISHED MARKER
        // =========================================================
        if (isset($files['bap']) && !empty($files['bap']['file'])) {
            $res['bap_published']    = true;
            $res['bap_published_at'] = $files['bap']['date_edit'] ?? ($files['bap']['date'] ?? null);
        } else {
            $res['bap_published']    = false;
            $res['bap_published_at'] = null;
        }

        return $res;
    }

    // =================================================================
    // PASSWORD
    // =================================================================

    public function update_password($nim, $hashed_password, $salt = null) {
        $user_table = $this->db->table_exists('user') ? 'user' : 'users';

        $this->db->group_start();
        if ($this->db->field_exists('nim',      $user_table)) $this->db->where('nim', $nim);
        if ($this->db->field_exists('username', $user_table)) $this->db->or_where('username', $nim);
        if ($this->db->field_exists('nip',      $user_table)) $this->db->or_where('nip', $nim);
        $this->db->group_end();
        $user = $this->db->get($user_table)->row();

        $updateData = ['password' => $hashed_password];
        if ($salt !== null && $this->db->field_exists('salt', $user_table)) {
            $updateData['salt'] = $salt;
        }
        if ($this->db->field_exists('password_changed', $user_table)) $updateData['password_changed'] = 1;
        if ($this->db->field_exists('updated_at',       $user_table)) $updateData['updated_at']       = date('Y-m-d H:i:s');

        $this->db->group_start();
        if ($this->db->field_exists('nim',      $user_table)) $this->db->where('nim', $nim);
        if ($this->db->field_exists('username', $user_table)) $this->db->or_where('username', $nim);
        if ($this->db->field_exists('nip',      $user_table)) $this->db->or_where('nip', $nim);
        $this->db->group_end();
        $res = $this->db->update($user_table, $updateData);

        if ($user && !empty($user->email) && $this->db->table_exists('user_token')) {
            $this->db->delete('user_token', ['email' => $user->email]);
        }
        return $res;
    }

    // =================================================================
    // RESET PENDAFTARAN
    // =================================================================

    public function reset_pendaftaran_ta($nim) {
        $upload_path = FCPATH . 'uploads/persyaratan_ta/';
        $userId = $this->_get_user_id_by_nim($nim);
        $gid    = $this->_get_guidance_id_by_nim($nim);

        if ($gid && $this->db->table_exists('thesis')) {
            $rows = $this->db->get_where('thesis', ['id_guidance' => $gid])->result_array();
            foreach ($rows as $r) {
                foreach (['pdf_file', 'file_sitasi', 'file_bimbingan', 'file_persyaratan', 'file_sidang'] as $col) {
                    if (!empty($r[$col])) {
                        $fp1 = FCPATH . 'uploads/preview_ta/' . $r[$col];
                        if (file_exists($fp1) && is_file($fp1)) @unlink($fp1);
                        $fp2 = FCPATH . 'uploads/sidang/' . $r[$col];
                        if (file_exists($fp2) && is_file($fp2)) @unlink($fp2);
                    }
                }
            }
            $this->db->where('id_guidance', $gid)->delete('thesis');
        }

        if ($gid && $this->db->table_exists('thesis_lecturers')) {
            $this->db->where('id_guidance', $gid)->delete('thesis_lecturers');
        }

        $target_ids = $this->_get_target_ids_by_nim($nim);

        if ($this->db->table_exists('guidance')) {
            $this->db->where_in('id_mhs', $target_ids)->or_where('id', 'gdn_' . $nim)->delete('guidance');
        }

        if ($this->db->table_exists('file_pendaftaran')) {
            $fp_rows = $this->db->where_in('id_mhs', $target_ids)->get('file_pendaftaran')->result_array();

            foreach ($fp_rows as $fpr) {
                if (!empty($fpr['file'])) {
                    $fp = FCPATH . $fpr['file'];
                    if (file_exists($fp) && is_file($fp)) @unlink($fp);
                }
            }
            $this->db->where_in('id_mhs', $target_ids)->delete('file_pendaftaran');
        }

        if ($this->db->table_exists('pendaftaran_berkas')) {
            $berkas_rows = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim])->result_array();
            foreach ($berkas_rows as $br) {
                if (!empty($br['file_name'])) {
                    $fp = $upload_path . $br['file_name'];
                    if (file_exists($fp) && is_file($fp)) @unlink($fp);
                }
            }
            $this->db->where('nim', $nim)->delete('pendaftaran_berkas');
        }

        return true;
    }

    // =================================================================
    // RIWAYAT PREVIEW (thesis)
    // =================================================================

    public function get_riwayat_preview($nim, $tahap = 'Preview 1') {
        if (!$this->db->table_exists('thesis')) return [];

        $gid = $this->_get_guidance_id_by_nim($nim);
        if (!$gid) return [];

        $tahap_enum = $this->_tahap_to_enum($tahap);

        $select_cols = [
            't.id',
            't.id_guidance',
            't.pdf_file          AS file_draft',
            't.file_sitasi',
            't.file_bimbingan',
            't.file_persyaratan',
            't.file_sidang',
            't.created_at',
            't.date',
            't.status            AS status_pembimbing',
            't.correction1       AS catatan_pembimbing',
            't.correction2       AS catatan_pembimbing_2',
            't.correction3       AS catatan_penguji_1',
            't.correction4       AS catatan_penguji_2',
            't.keterangan        AS catatan_mahasiswa',
            't.tahapan_preview',
            'g.nilaisidang_pembimbing1',
            'g.nilaisidang_pembimbing2',
            'g.nilaisidang_penguji1',
            'g.nilaisidang_penguji2',
        ];

        $optional_thesis = [
            'status_file_draft', 'status_file_sitasi', 'status_file_bimbingan',
            'status_file_persyaratan', 'status_file_sidang',
            'catatan_file_draft', 'catatan_file_sitasi', 'catatan_file_bimbingan',
            'catatan_file_persyaratan', 'catatan_file_sidang',
            'reviewed_by_role', 'reviewed_at',
        ];
        foreach ($optional_thesis as $col) {
            if ($this->db->field_exists($col, 'thesis')) {
                $select_cols[] = 't.' . $col;
            }
        }

        $optional_guidance = [
            'lulus_preview1', 'lulus_preview2', 'lulus_preview3', 'lulus_sidang',
            'lulus_by_role', 'lulus_at',
        ];
        foreach ($optional_guidance as $col) {
            if ($this->db->field_exists($col, 'guidance')) {
                $select_cols[] = 'g.' . $col;
            }
        }

        $this->db->select(implode(",\n", $select_cols), FALSE);
        $this->db->from('thesis t');
        $this->db->join('guidance g', 'g.id = t.id_guidance', 'left');
        $this->db->where('t.id_guidance', $gid);
        $this->db->where('t.tahapan_preview', $tahap_enum);
        $this->db->order_by('t.created_at', 'DESC');
        $this->db->order_by('t.date', 'DESC');
        return $this->db->get()->result_array();
    }

    public function save_upload_preview($data) {
        if (!$this->db->table_exists('thesis')) return false;

        $nim = $data['nim'] ?? null;
        if (!$nim) return false;

        $gid = $this->_get_guidance_id_by_nim($nim);
        if (!$gid) return false;

        $tahap_enum = $this->_tahap_to_enum($data['tahap_preview'] ?? 'Preview 1');

        $insert = [
            'id'                => 'ths_' . $nim . '_' . $tahap_enum . '_' . time(),
            'id_guidance'       => $gid,
            'send_to'           => '',
            'pdf_file'          => $data['file_draft']       ?? '',
            'file_sitasi'       => $data['file_sitasi']      ?? null,
            'file_bimbingan'    => $data['file_bimbingan']   ?? null,
            'file_persyaratan'  => $data['file_persyaratan'] ?? null,
            'file_sidang'       => $data['file_sidang']      ?? null,
            'link_project'      => '',
            'keterangan'        => $data['catatan_mahasiswa'] ?? '',
            'catatan_mahasiswa' => $data['catatan_mahasiswa'] ?? '',
            'date'              => date('Y-m-d'),
            'correction1'       => '',
            'correction2'       => '',
            'correction3'       => '',
            'correction4'       => '',
            'status'            => $data['status_pembimbing'] ?? 'Pending',
            'tahapan_preview'   => $tahap_enum,
        ];

        $thesis_fields = $this->db->list_fields('thesis');
        if (in_array('status_file_draft', $thesis_fields))       $insert['status_file_draft']       = 'Pending';
        if (in_array('status_file_sitasi', $thesis_fields))      $insert['status_file_sitasi']      = 'Pending';
        if (in_array('status_file_bimbingan', $thesis_fields))   $insert['status_file_bimbingan']   = 'Pending';
        if (in_array('status_file_persyaratan', $thesis_fields)) $insert['status_file_persyaratan'] = 'Pending';
        if (in_array('status_file_sidang', $thesis_fields))      $insert['status_file_sidang']      = 'Pending';

        $inserted = $this->db->insert('thesis', $insert);

        if ($inserted && $this->db->table_exists('guidance') && $this->db->field_exists('status_preview', 'guidance')) {
            $currG = $this->db->get_where('guidance', ['id' => $gid])->row_array();
            $currPrev = strtolower(trim($currG['status_preview'] ?? ''));
            $stageHierarchy = ['preview1' => 1, 'preview2' => 2, 'preview3' => 3, 'sidang' => 4, 'lulus' => 5, 'selesai' => 5];
            $currRank = $stageHierarchy[$currPrev] ?? 0;
            $newRank  = $stageHierarchy[$tahap_enum] ?? 0;
            if ($newRank > $currRank) {
                $this->db->where('id', $gid)->update('guidance', ['status_preview' => $tahap_enum]);
            }
        }

        return $inserted;
    }

    public function count_upload_preview($nim, $tahap = 'Preview 1') {
        return count($this->get_riwayat_preview($nim, $tahap));
    }

    public function get_latest_preview_status($nim, $tahap = 'Preview 1') {
        $riwayat = $this->get_riwayat_preview($nim, $tahap);
        return !empty($riwayat) ? $riwayat[0] : null;
    }

    public function update_review_preview($id, $data) {
        if (!$this->db->table_exists('thesis')) return false;

        $update = [];
        if (isset($data['status_pembimbing']))    $update['status']      = $data['status_pembimbing'];
        if (isset($data['status_file_draft']))    $update['status_file_draft'] = $data['status_file_draft'];
        if (isset($data['status_file_sitasi']))   $update['status_file_sitasi'] = $data['status_file_sitasi'];
        if (isset($data['status_file_bimbingan'])) $update['status_file_bimbingan'] = $data['status_file_bimbingan'];
        if (isset($data['status_file_persyaratan'])) $update['status_file_persyaratan'] = $data['status_file_persyaratan'];
        if (isset($data['status_file_sidang']))   $update['status_file_sidang'] = $data['status_file_sidang'];
        if (isset($data['catatan_pembimbing']))   $update['correction1'] = $data['catatan_pembimbing'];
        if (isset($data['catatan_pembimbing_2'])) $update['correction2'] = $data['catatan_pembimbing_2'];
        if (isset($data['catatan_penguji_1']))    $update['correction3'] = $data['catatan_penguji_1'];
        if (isset($data['catatan_penguji_2']))    $update['correction4'] = $data['catatan_penguji_2'];
        if (isset($data['catatan_mahasiswa']))    $update['keterangan']  = $data['catatan_mahasiswa'];

        if (empty($update)) return false;

        $thesis_fields = $this->db->list_fields('thesis');
        foreach ($update as $k => $v) {
            if (!in_array($k, $thesis_fields)) unset($update[$k]);
        }
        if (empty($update)) return false;

        $this->db->where('id', $id);
        $res = $this->db->update('thesis', $update);

        return $res;
    }

    public function lulus_tahap($id_guidance, $tahap_sekarang) {
        if (!$this->db->table_exists('guidance') || !$this->db->field_exists('status_preview', 'guidance')) return false;

        $thp = strtolower($tahap_sekarang);
        $nextStage = null;
        if ($thp === 'preview1' || $thp === 'preview 1') {
            $nextStage = 'preview2';
        } elseif ($thp === 'preview2' || $thp === 'preview 2') {
            $nextStage = 'preview3';
        } elseif ($thp === 'preview3' || $thp === 'preview 3') {
            $nextStage = 'sidang';
        } elseif ($thp === 'sidang') {
            $nextStage = 'lulus';
        }

        if ($nextStage) {
            $this->db->where('id', $id_guidance)->update('guidance', ['status_preview' => $nextStage]);
            return true;
        }
        return false;
    }

    public function revert_tahap($id_guidance, $tahap_sekarang) {
        if (!$this->db->table_exists('guidance') || !$this->db->field_exists('status_preview', 'guidance')) return false;

        $thp = strtolower($tahap_sekarang);
        $prevStage = null;
        if ($thp === 'preview2' || $thp === 'preview 2') {
            $prevStage = 'preview1';
        } elseif ($thp === 'preview3' || $thp === 'preview 3') {
            $prevStage = 'preview2';
        } elseif ($thp === 'sidang') {
            $prevStage = 'preview3';
        }

        if ($prevStage) {
            $this->db->where('id', $id_guidance)->update('guidance', ['status_preview' => $prevStage]);
            return true;
        }
        return false;
    }

    // =================================================================
    // DOSEN PEMBIMBING / PENGUJI (thesis_lecturers)
    // =================================================================

    public function get_pembimbing_penguji($nim) {
        $result = [
            'pembimbing_1' => '',
            'pembimbing_2' => '',
            'penguji_1'    => '',
            'penguji_2'    => '',
        ];

        $gid = $this->_get_guidance_id_by_nim($nim);
        if (!$gid) return $result;

        $tl = $this->db->get_where('thesis_lecturers', ['id_guidance' => $gid])->row_array();
        if (!$tl) return $result;

        $result['pembimbing_1'] = $this->_get_dosen_name($tl['dosen_pembimbing1'] ?? '');
        $result['pembimbing_2'] = $this->_get_dosen_name($tl['dosen_pembimbing2'] ?? '');
        $result['penguji_1']    = $this->_get_dosen_name($tl['dosen_penguji1']    ?? '');
        $result['penguji_2']    = $this->_get_dosen_name($tl['dosen_penguji2']    ?? '');

        return $result;
    }

    private function _get_dosen_name($val) {
        if (empty($val)) return '';
        $user_table = $this->db->table_exists('user') ? 'user' : 'users';
        if ($this->db->table_exists($user_table)) {
            $this->db->group_start();
            if ($this->db->field_exists('id',   $user_table)) $this->db->or_where('id',   $val);
            if ($this->db->field_exists('nip',  $user_table)) $this->db->or_where('nip',  $val);
            if ($this->db->field_exists('nim',  $user_table)) $this->db->or_where('nim',  $val);
            if ($this->db->field_exists('name', $user_table)) $this->db->or_where('name', $val);
            $this->db->group_end();
            $u = $this->db->get($user_table)->row_array();
            if ($u && !empty($u['name'])) return $u['name'];
        }
        return $val;
    }

    public function get_students_by_dosen($dosen_id, $posisi = 1) {
        if (!$this->db->table_exists('thesis_lecturers')) return [];

        $role_id = (int) $this->session->userdata('role_id');

        $dosen_id_val = '';
        $nip_dosen    = '';
        $name_dosen   = '';
        if ($this->db->table_exists('user')) {
            $u = $this->db->get_where('user', ['id' => $dosen_id])->row_array();
            if ($u) {
                $dosen_id_val = $u['id']   ?? '';
                $nip_dosen    = $u['nip']  ?? '';
                $name_dosen   = $u['name'] ?? '';
            }
        }

        $this->db->select("
            COALESCE(u.nim, g.id_mhs)   AS nim,
            g.judul_1                    AS judul,
            g.peminatan                  AS konsentrasi_dkv,
            COALESCE(u.name, m.nama_depan, g.id_mhs) AS nama_mahasiswa
        ", FALSE);
        $this->db->from('thesis_lecturers tl');
        $this->db->join('guidance g',  'g.id = tl.id_guidance', 'inner');
        $this->db->join('user u',      '(u.id = g.id_mhs OR u.nim = g.id_mhs)', 'left');
        $this->db->join('mahasiswa m', 'm.nim = u.nim', 'left');


        if ($role_id !== 1) {
            $col_map = [
                1 => 'tl.dosen_pembimbing1',
                2 => 'tl.dosen_pembimbing2',
                3 => 'tl.dosen_penguji1',
                4 => 'tl.dosen_penguji2',
            ];
            $col = $col_map[$posisi] ?? $col_map[1];

            $this->db->group_start();
            if ($dosen_id_val) $this->db->or_where($col, $dosen_id_val);
            if ($nip_dosen)    $this->db->or_where($col, $nip_dosen);
            if ($name_dosen)   $this->db->or_like($col, $name_dosen);
            $this->db->group_end();
        }

        return $this->db->get()->result_array();
    }

    public function get_preview_by_id($id_preview)
    {
        if (empty($id_preview)) return null;
        if (!$this->db->table_exists('thesis')) return null;

        $this->db->select("
            t.id,
            t.id_guidance,
            t.pdf_file          AS file_draft,
            t.file_sitasi,
            t.file_bimbingan,
            t.file_persyaratan,
            t.file_sidang,
            t.link_project,
            t.created_at,
            t.date,
            t.status            AS status_pembimbing,
            t.correction1       AS catatan_pembimbing,
            t.correction2       AS catatan_pembimbing_2,
            t.correction3       AS catatan_penguji_1,
            t.correction4       AS catatan_penguji_2,
            t.keterangan        AS catatan_mahasiswa,
            t.tahapan_preview,
            g.id_mhs,
            COALESCE(u.nim, u.username, m.nim, g.id_mhs) AS nim,
            COALESCE(u.name, m.nama_depan, g.id_mhs)     AS nama_mahasiswa,
            g.judul_1                                    AS judul,
            g.peminatan                                  AS konsentrasi_dkv
        ", FALSE);

        $this->db->from('thesis t');
        $this->db->join('guidance g',  'g.id = t.id_guidance', 'left');
        $this->db->join('user u',      '(u.id = g.id_mhs OR u.nim = g.id_mhs)', 'left');
        $this->db->join('mahasiswa m', 'm.nim = u.nim', 'left');
        $this->db->where('t.id', $id_preview);
        $this->db->limit(1);

        $row = $this->db->get()->row_array();
        if (!$row) return null;

        $thesis_opt = [
            'status_file_draft','status_file_sitasi','status_file_bimbingan',
            'status_file_persyaratan','status_file_sidang',
            'catatan_file_draft','catatan_file_sitasi','catatan_file_bimbingan',
            'catatan_file_persyaratan','catatan_file_sidang',
            'reviewed_by_role','reviewed_at',
        ];
        $want_thesis = [];
        foreach ($thesis_opt as $c) {
            if ($this->db->field_exists($c, 'thesis')) $want_thesis[] = $c;
        }
        if (!empty($want_thesis) && !empty($row['id'])) {
            $extra = $this->db->select(implode(',', $want_thesis))
                ->get_where('thesis', ['id' => $row['id']])
                ->row_array();
            if ($extra) $row = array_merge($row, $extra);
        }

        $guidance_opt = ['lulus_preview1','lulus_preview2','lulus_preview3','lulus_sidang','lulus_by_role','lulus_at'];
        $want_g = [];
        foreach ($guidance_opt as $c) {
            if ($this->db->field_exists($c, 'guidance')) $want_g[] = $c;
        }
        if (!empty($want_g) && !empty($row['id_guidance'])) {
            $extra_g = $this->db->select(implode(',', $want_g))
                ->get_where('guidance', ['id' => $row['id_guidance']])
                ->row_array();
            if ($extra_g) $row = array_merge($row, $extra_g);
        }

        return $row;
    }

    // =================================================================
    // PENILAIAN SIDANG (Preview 4)
    // =================================================================

    public function get_guidance_by_nim($nim) {
        if (empty($nim)) return null;
        if (!$this->db->table_exists('guidance')) return null;

        $userId = $this->_get_user_id_by_nim($nim);

        $userNims = array_unique(array_filter([$nim, $userId]));
        if ($this->db->table_exists('user')) {
            $u = $this->db->select('id, nim, username')
                ->group_start()
                    ->where('nim', $nim)
                    ->or_where('username', $nim)
                    ->or_where('id', $nim)
                ->group_end()
                ->get('user')->row_array();
            if ($u) {
                if (!empty($u['id'])) $userNims[] = $u['id'];
                if (!empty($u['nim'])) $userNims[] = $u['nim'];
                if (!empty($u['username'])) $userNims[] = $u['username'];
            }
        }
        $userNims = array_unique($userNims);

        $this->db->group_start();
        foreach ($userNims as $val) {
            $this->db->or_where('id_mhs', $val);
            $this->db->or_where('id', $val);
            $this->db->or_where('id', 'gdn_' . $val);
        }
        $this->db->group_end();
        $this->db->limit(1);

        return $this->db->get('guidance')->row_array() ?: null;
    }

    public function save_nilai_sidang_by_posisi($nim, $posisi, $nilai, $catatan = '', $detail = null) {
        if (!$this->db->table_exists('guidance')) {
            return ['status' => false, 'message' => 'Tabel guidance tidak ditemukan.'];
        }

        $g = $this->get_guidance_by_nim($nim);
        if (!$g) {
            return ['status' => false, 'message' => 'Data bimbingan mahasiswa (' . $nim . ') tidak ditemukan di tabel guidance.'];
        }

        $map_nilai_options = [
            1 => ['nilaisidang_pembimbing1', 'nilaisidang_pembimbing_1', 'nilai_sidang_pembimbing1', 'nilai_sidang_pembimbing_1'],
            2 => ['nilaisidang_pembimbing2', 'nilaisidang_pembimbing_2', 'nilai_sidang_pembimbing2', 'nilai_sidang_pembimbing_2'],
            3 => ['nilaisidang_penguji1', 'nilaisidang_penguji_1', 'nilai_sidang_penguji1', 'nilai_sidang_penguji_1'],
            4 => ['nilaisidang_penguji2', 'nilaisidang_penguji_2', 'nilai_sidang_penguji2', 'nilai_sidang_penguji_2'],
        ];

        $map_catatan_options = [
            1 => ['penilaiansidang_pembimbing1', 'penilaiansidang_pembimbing_1', 'evaluasi_pembimbing1'],
            2 => ['penilaiansidang_pembimbing2', 'penilaiansidang_pembimbing_2', 'evaluasi_pembimbing2'],
            3 => ['penilaiansidang_penguji1', 'penilaiansidang_penguji_1', 'evaluasi_penguji1'],
            4 => ['penilaiansidang_penguji2', 'penilaiansidang_penguji_2', 'evaluasi_penguji2'],
        ];

        $map_label = [1 => 'Pembimbing 1', 2 => 'Pembimbing 2', 3 => 'Penguji 1', 4 => 'Penguji 2'];

        if (!isset($map_nilai_options[$posisi])) {
            return ['status' => false, 'message' => 'Posisi tidak valid.'];
        }

        $nilai = max(0, min(100, (float)$nilai));

        $update = [];
        
        foreach ($map_nilai_options[$posisi] as $colName) {
            if ($this->db->field_exists($colName, 'guidance')) {
                $update[$colName] = $nilai;
            }
        }

        foreach ($map_catatan_options[$posisi] as $colCatatan) {
            if ($this->db->field_exists($colCatatan, 'guidance')) {
                $payload = [
                    'catatan'  => $catatan,
                    'detail'   => $detail,
                    'posisi'   => $posisi,
                    'label'    => $map_label[$posisi],
                    'nilai'    => $nilai,
                    'saved_at' => date('Y-m-d H:i:s'),
                ];
                $update[$colCatatan] = json_encode($payload, JSON_UNESCAPED_UNICODE);
            }
        }

        if (empty($update)) {
            return ['status' => false, 'message' => 'Tidak ada kolom yang bisa diupdate di tabel guidance.'];
        }

        $this->db->where('id', $g['id'])->update('guidance', $update);

        if ($this->db->table_exists('thesis')) {
            $this->db->where('id_guidance', $g['id']);
            $this->db->where('tahapan_preview', 'sidang');
            $this->db->update('thesis', ['status' => 'Approved']);
        }

        if ($this->db->table_exists('log_approval_history')) {
            $this->db->insert('log_approval_history', [
                'modul'         => 'Penilaian Sidang',
                'ref_id'        => (string)$nim,
                'target_name'   => null,
                'action'        => 'Saved',
                'actor_id'      => $this->session->userdata('user_id'),
                'actor_name'    => $this->session->userdata('name') ?: $map_label[$posisi],
                'actor_role'    => $map_label[$posisi],
                'actor_nip_nim' => $this->session->userdata('nip') ?: $this->session->userdata('username'),
                'catatan'       => json_encode([
                    'posisi'      => $posisi,
                    'nilai_akhir' => $nilai,
                    'catatan'     => $catatan,
                ], JSON_UNESCAPED_UNICODE),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return [
            'status'  => true,
            'message' => 'Nilai sidang (' . $map_label[$posisi] . ') berhasil disimpan.',
            'nilai'   => $nilai,
            'posisi'  => $posisi,
        ];
    }

    public function get_detail_nilai_sidang($nim) {
        $g = $this->get_guidance_by_nim($nim);
        if (!$g) return null;

        $mhs = $this->db->select('u.name AS nama_mahasiswa, u.nim AS nim_mhs, g.judul_1, g.peminatan')
            ->from('guidance g')
            ->join('user u', '(u.id = g.id_mhs OR u.nim = g.id_mhs)', 'left')
            ->where('g.id', $g['id'])
            ->limit(1)
            ->get()->row_array();

        $tl = $this->db->table_exists('thesis_lecturers')
            ? $this->db->get_where('thesis_lecturers', ['id_guidance' => $g['id']])->row_array()
            : null;

        $parse_detail = function ($raw) {
            if (empty($raw)) return ['catatan' => '', 'detail' => null, 'saved_at' => null];
            $j = json_decode($raw, true);
            if (is_array($j) && (isset($j['catatan']) || isset($j['detail']))) {
                return $j;
            }
            return ['catatan' => (string)$raw, 'detail' => null, 'saved_at' => null];
        };

        return [
            'nim'              => $mhs['nim_mhs'] ?? $nim,
            'nama'             => $mhs['nama_mahasiswa'] ?? '-',
            'judul'            => $g['judul_1'] ?? '-',
            'peminatan'        => $g['peminatan'] ?? '-',
            'prodi'            => 'Desain Komunikasi Visual',
            'tgl_sidang'       => $g['tanggal_sidang'] ?? null,
            'waktu_sidang'     => $g['waktu_sidang'] ?? null,
            'ruangan_sidang'   => $g['ruang_sidang'] ?? null,
            'pembimbing_1'     => $this->_get_dosen_name($tl['dosen_pembimbing1'] ?? ''),
            'pembimbing_2'     => $this->_get_dosen_name($tl['dosen_pembimbing2'] ?? ''),
            'penguji_1'        => $this->_get_dosen_name($tl['dosen_penguji1'] ?? ''),
            'penguji_2'        => $this->_get_dosen_name($tl['dosen_penguji2'] ?? ''),
            'nilai'            => [
                1 => (float)($g['nilaisidang_pembimbing1'] ?? 0),
                2 => (float)($g['nilaisidang_pembimbing2'] ?? 0),
                3 => (float)($g['nilaisidang_penguji1'] ?? 0),
                4 => (float)($g['nilaisidang_penguji2'] ?? 0),
            ],
            'detail'           => [
                1 => $parse_detail($g['penilaiansidang_pembimbing1'] ?? ''),
                2 => $parse_detail($g['penilaiansidang_pembimbing2'] ?? ''),
                3 => $parse_detail($g['penilaiansidang_penguji1'] ?? ''),
                4 => $parse_detail($g['penilaiansidang_penguji2'] ?? ''),
            ],
        ];
    }

    // =================================================================
    // RESTORE: STATUS SIDANG & PENILAIAN (untuk view mahasiswa)
    // =================================================================

    public function get_rekap_nilai_sidang($nim) {
        $empty = [
            'has_data'          => false,
            'semua_terisi'      => false,
            'jumlah_terisi'     => 0,
            'nilai_akhir'       => null,
            'grade'             => '-',
            'status_kelulusan'  => 'Belum Dinilai',
            'nilai_per_posisi'  => ['p1'=>0,'p2'=>0,'u1'=>0,'u2'=>0],
            'detail_per_posisi' => [],
            'kriteria_referensi'=> [],
        ];

        if (!$this->db->table_exists('guidance')) return $empty;

        $g = $this->get_guidance_by_nim($nim);
        if (!$g) return $empty;

        $n1 = (float)($g['nilaisidang_pembimbing1'] ?? 0);
        $n2 = (float)($g['nilaisidang_pembimbing2'] ?? 0);
        $n3 = (float)($g['nilaisidang_penguji1']     ?? 0);
        $n4 = (float)($g['nilaisidang_penguji2']     ?? 0);

        $filled = 0;
        if ($n1 > 0) $filled++;
        if ($n2 > 0) $filled++;
        if ($n3 > 0) $filled++;
        if ($n4 > 0) $filled++;

        $parse = function ($raw) {
            if (empty($raw)) return ['catatan' => '', 'detail' => [], 'saved_at' => null];
            $j = json_decode($raw, true);
            if (!is_array($j)) return ['catatan' => (string)$raw, 'detail' => [], 'saved_at' => null];
            return [
                'catatan'  => $j['catatan']  ?? '',
                'detail'   => $j['detail']   ?? [],
                'saved_at' => $j['saved_at'] ?? null,
            ];
        };

        $detail_per_posisi = [
            1 => $parse($g['penilaiansidang_pembimbing1'] ?? ''),
            2 => $parse($g['penilaiansidang_pembimbing2'] ?? ''),
            3 => $parse($g['penilaiansidang_penguji1']     ?? ''),
            4 => $parse($g['penilaiansidang_penguji2']     ?? ''),
        ];

        $kriteria_ref = [];
        foreach ([1,2,3,4] as $pos) {
            if (!empty($detail_per_posisi[$pos]['detail'])) {
                $kriteria_ref = $detail_per_posisi[$pos]['detail'];
                break;
            }
        }

        if ($filled < 4) {
            return [
                'has_data'          => $filled > 0,
                'semua_terisi'      => false,
                'jumlah_terisi'     => $filled,
                'nilai_akhir'       => null,
                'grade'             => '-',
                'status_kelulusan'  => 'Belum Lengkap',
                'nilai_per_posisi'  => ['p1'=>$n1,'p2'=>$n2,'u1'=>$n3,'u2'=>$n4],
                'detail_per_posisi' => $detail_per_posisi,
                'kriteria_referensi'=> $kriteria_ref,
            ];
        }

        $avg = ($n1 + $n2 + $n3 + $n4) / 4;

        if     ($avg >= 85)   { $grade='A';  $status='Lulus'; }
        elseif ($avg >= 77.5) { $grade='AB'; $status='Lulus'; }
        elseif ($avg >= 70)   { $grade='B';  $status='Lulus'; }
        elseif ($avg >= 62.5) { $grade='BC'; $status='Lulus dengan Revisi'; }
        elseif ($avg >= 55)   { $grade='C';  $status='Lulus dengan Revisi'; }
        elseif ($avg >= 45)   { $grade='D';  $status='Tidak Lulus'; }
        else                  { $grade='E';  $status='Tidak Lulus'; }

        return [
            'has_data'          => true,
            'semua_terisi'      => true,
            'jumlah_terisi'     => 4,
            'nilai_akhir'       => $avg,
            'grade'             => $grade,
            'status_kelulusan'  => $status,
            'nilai_per_posisi'  => ['p1'=>$n1,'p2'=>$n2,'u1'=>$n3,'u2'=>$n4],
            'detail_per_posisi' => $detail_per_posisi,
            'kriteria_referensi'=> $kriteria_ref,
        ];
    }

    // =================================================================
    // PER-FILE STATUS, LULUS TAHAP, & DOWNGRADE (2026-10-05)
    // =================================================================

    public function get_responsible_role_for_tahap($tahap) {
        $tahap = trim((string)$tahap);
        if ($tahap === 'Preview 1') return 'p1';
        if (in_array($tahap, ['Preview 2', 'Preview 3', 'Sidang'], true)) return 'u1';
        return null;
    }

    private function _required_files_for_tahap($tahap_key) {
        if ($tahap_key === 'preview3') return ['sitasi', 'bimbingan', 'persyaratan'];
        if ($tahap_key === 'sidang')   return ['sidang'];
        return ['draft'];
    }

    /**
     * Update status 1 file spesifik (ACC/Revisi/Reset per file).
     * ─── FIX: hapus typo '$role;x' yang menyebabkan parse error ───
     */
    public function update_file_status($id_preview, $file_type, $status, $catatan = '', $role = null) {
        if (!$this->db->table_exists('thesis')) {
            return ['status' => false, 'message' => 'Tabel thesis tidak ditemukan.'];
        }

        $allowed_types  = ['draft', 'sitasi', 'bimbingan', 'persyaratan', 'sidang'];
        $allowed_status = ['Approved', 'Revision', 'Pending'];

        $file_type = strtolower(trim($file_type));
        if (!in_array($file_type, $allowed_types, true)) {
            return ['status' => false, 'message' => 'Tipe file tidak valid: ' . $file_type];
        }
        if (!in_array($status, $allowed_status, true)) {
            return ['status' => false, 'message' => 'Status tidak valid: ' . $status];
        }

        $col_status  = 'status_file_' . $file_type;
        $col_catatan = 'catatan_file_' . $file_type;

        // 1. Update status file
        $update = ['status' => $status];
        if ($this->db->field_exists($col_status, 'thesis')) {
            $update[$col_status] = $status;
        }
        if ($this->db->field_exists($col_catatan, 'thesis')) {
            $update[$col_catatan] = $catatan;
        }
        if ($this->db->field_exists('reviewed_by_role', 'thesis')) {
            $update['reviewed_by_role'] = $role;
        }
        if ($this->db->field_exists('reviewed_at', 'thesis')) {
            $update['reviewed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $id_preview)->update('thesis', $update);

        // 2. Sync `status` agregat dari semua file di preview ini
        $this->_sync_thesis_status_from_files($id_preview);

        $label_map = ['draft'=>'Draft','sitasi'=>'Sitasi','bimbingan'=>'Bimbingan','persyaratan'=>'Persyaratan','sidang'=>'Sidang'];
        $label = $label_map[$file_type] ?? $file_type;
        $action_label = ($status === 'Approved') ? 'di-ACC' : (($status === 'Revision') ? 'diminta revisi' : 'direset ke Pending');
        $msg = 'File ' . $label . ' berhasil ' . $action_label . '.';

        if ($this->db->table_exists('log_approval_history')) {
            $preview = $this->db->select('id_guidance')->get_where('thesis', ['id' => $id_preview])->row_array();
            $g = $preview ? $this->db->select('id_mhs')->get_where('guidance', ['id' => $preview['id_guidance']])->row_array() : null;

            $this->db->insert('log_approval_history', [
                'modul'         => 'Review File Preview',
                'ref_id'        => (string)$id_preview,
                'target_name'   => $g['id_mhs'] ?? null,
                'action'        => $status,
                'actor_id'      => $this->session->userdata('user_id'),
                'actor_name'    => $this->session->userdata('name') ?: '-',
                'actor_role'    => strtoupper((string)$role),
                'actor_nip_nim' => $this->session->userdata('nip') ?: $this->session->userdata('username'),
                'catatan'       => json_encode([
                    'file_type' => $file_type,
                    'status'    => $status,
                    'catatan'   => $catatan,
                ], JSON_UNESCAPED_UNICODE),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return ['status' => true, 'message' => $msg, 'file_type' => $file_type, 'new_status' => $status];
    }

    /**
     * Update HANYA catatan file (komentar tanpa ubah status).
     */
    public function update_file_comment($id_preview, $file_type, $catatan = '', $role = null) {
        if (!$this->db->table_exists('thesis')) {
            return ['status' => false, 'message' => 'Tabel thesis tidak ditemukan.'];
        }

        $allowed_types = ['draft', 'sitasi', 'bimbingan', 'persyaratan', 'sidang'];
        $file_type = strtolower(trim($file_type));
        if (!in_array($file_type, $allowed_types, true)) {
            return ['status' => false, 'message' => 'Tipe file tidak valid: ' . $file_type];
        }

        $col_catatan = 'catatan_file_' . $file_type;
        if (!$this->db->field_exists($col_catatan, 'thesis')) {
            return ['status' => false, 'message' => 'Kolom catatan file belum tersedia di database.'];
        }

        $update = [$col_catatan => $catatan];
        if ($this->db->field_exists('reviewed_by_role', 'thesis') && $role) {
            $update['reviewed_by_role'] = $role;
        }
        if ($this->db->field_exists('reviewed_at', 'thesis')) {
            $update['reviewed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $id_preview)->update('thesis', $update);

        $label_map = ['draft'=>'Draft','sitasi'=>'Sitasi','bimbingan'=>'Bimbingan','persyaratan'=>'Persyaratan','sidang'=>'Sidang'];
        $label = $label_map[$file_type] ?? $file_type;

        if ($this->db->table_exists('log_approval_history')) {
            $preview = $this->db->select('id_guidance')->get_where('thesis', ['id' => $id_preview])->row_array();
            $g = $preview ? $this->db->select('id_mhs')->get_where('guidance', ['id' => $preview['id_guidance']])->row_array() : null;

            $this->db->insert('log_approval_history', [
                'modul'         => 'Review File Preview',
                'ref_id'        => (string)$id_preview,
                'target_name'   => $g['id_mhs'] ?? null,
                'action'        => 'Comment',
                'actor_id'      => $this->session->userdata('user_id'),
                'actor_name'    => $this->session->userdata('name') ?: '-',
                'actor_role'    => strtoupper((string)$role),
                'actor_nip_nim' => $this->session->userdata('nip') ?: $this->session->userdata('username'),
                'catatan'       => json_encode([
                    'file_type' => $file_type,
                    'catatan'   => $catatan,
                ], JSON_UNESCAPED_UNICODE),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return ['status' => true, 'message' => 'Komentar file ' . $label . ' berhasil disimpan.', 'file_type' => $file_type];
    }

    private function _sync_thesis_status_from_files($id_preview) {
        if (!$this->db->table_exists('thesis')) return;

        $preview = $this->db->select('id, id_guidance, tahapan_preview')
            ->get_where('thesis', ['id' => $id_preview])->row_array();
        if (!$preview) return;

        $thesis_fields = $this->db->list_fields('thesis');
        if (!in_array('status', $thesis_fields)) return;

        $tahap = strtolower($preview['tahapan_preview'] ?? 'preview1');

        if ($tahap === 'preview3')     $required = ['sitasi', 'bimbingan', 'persyaratan'];
        elseif ($tahap === 'sidang')   $required = ['sidang'];
        else                           $required = ['draft'];

        $has_any_status_col = false;
        foreach ($required as $ft) {
            if (in_array('status_file_' . $ft, $thesis_fields)) {
                $has_any_status_col = true;
                break;
            }
        }
        if (!$has_any_status_col) return;

        $row = $this->db->get_where('thesis', ['id' => $id_preview])->row_array();
        if (!$row) return;

        $has_revision  = false;
        $all_approved  = true;
        $counted       = 0;

        foreach ($required as $ft) {
            $col_status = 'status_file_' . $ft;
            $col_file   = 'file_' . $ft;

            if (!in_array($col_status, $thesis_fields)) continue;
            if (empty($row[$col_file])) continue;

            $counted++;
            $st = $row[$col_status] ?? 'Pending';
            if ($st === 'Revision') $has_revision = true;
            if ($st !== 'Approved') $all_approved = false;
        }

        if ($counted === 0) return;

        $new_status = 'Pending';
        if ($has_revision)         $new_status = 'Revision';
        elseif ($all_approved)     $new_status = 'Approved';

        if (($row['status'] ?? '') !== $new_status) {
            $this->db->where('id', $id_preview)->update('thesis', ['status' => $new_status]);
        }
    }

    public function set_stage_lulus($nim, $tahap, $role = null, $id_preview = null) {
        if (!$this->db->table_exists('guidance')) {
            return ['status' => false, 'message' => 'Tabel guidance tidak ditemukan.'];
        }

        $tahap_map = [
            'Preview 1' => 'preview1', 'Preview 2' => 'preview2', 'Preview 3' => 'preview3', 'Sidang' => 'sidang',
            'preview1' => 'preview1', 'preview2' => 'preview2', 'preview3' => 'preview3', 'sidang' => 'sidang',
        ];
        $tahap_key = $tahap_map[$tahap] ?? null;
        if (!$tahap_key) {
            return ['status' => false, 'message' => 'Tahap tidak valid: ' . $tahap];
        }

        $g = $this->get_guidance_by_nim($nim);
        if (!$g) {
            return ['status' => false, 'message' => 'Data bimbingan mahasiswa tidak ditemukan.'];
        }

        if (!$id_preview) {
            $last = $this->db->select('id')
                ->where('id_guidance', $g['id'])
                ->where('tahapan_preview', $tahap_key)
                ->order_by('created_at', 'DESC')
                ->limit(1)
                ->get('thesis')->row_array();
            if (!$last) {
                return ['status' => false, 'message' => 'Belum ada berkas untuk tahap ini.'];
            }
            $id_preview = $last['id'];
        }

        $preview = $this->get_preview_by_id($id_preview);
        if (!$preview) {
            return ['status' => false, 'message' => 'Preview tidak ditemukan.'];
        }

        $col_lulus = 'lulus_' . $tahap_key;
        if (!$this->db->field_exists($col_lulus, 'guidance')) {
            return ['status' => false, 'message' => 'Kolom ' . $col_lulus . ' belum ada. Jalankan migrasi SQL dulu.'];
        }

        $update = [$col_lulus => 1];
        if ($this->db->field_exists('lulus_by_role', 'guidance')) $update['lulus_by_role'] = $role;
        if ($this->db->field_exists('lulus_at', 'guidance'))      $update['lulus_at']      = date('Y-m-d H:i:s');

        $this->db->where('id', $g['id'])->update('guidance', $update);

        if ($this->db->field_exists('status_preview', 'guidance')) {
            $hierarchy = ['preview1' => 1, 'preview2' => 2, 'preview3' => 3, 'sidang' => 4];
            $curr = strtolower(trim($g['status_preview'] ?? 'pending'));
            $curr_rank = $hierarchy[$curr] ?? 0;

            $next = null;
            if     ($tahap_key === 'preview1') $next = 'preview2';
            elseif ($tahap_key === 'preview2') $next = 'preview3';
            elseif ($tahap_key === 'preview3') $next = 'sidang';

            if ($next && isset($hierarchy[$next])) {
                if ($hierarchy[$next] > $curr_rank) {
                    $this->db->where('id', $g['id'])->update('guidance', ['status_preview' => $next]);
                }
            } elseif ($tahap_key === 'sidang' && $curr_rank < 4) {
                $this->db->where('id', $g['id'])->update('guidance', ['status_preview' => 'sidang']);
            }
        }

        if ($this->db->table_exists('log_approval_history')) {
            $this->db->insert('log_approval_history', [
                'modul'         => 'Lulus Tahap',
                'ref_id'        => (string)$nim,
                'target_name'   => $nim,
                'action'        => 'Lulus ' . ucfirst($tahap_key),
                'actor_id'      => $this->session->userdata('user_id'),
                'actor_name'    => $this->session->userdata('name') ?: '-',
                'actor_role'    => strtoupper((string)$role),
                'actor_nip_nim' => $this->session->userdata('nip') ?: $this->session->userdata('username'),
                'catatan'       => json_encode(['tahap' => $tahap_key, 'id_preview' => $id_preview], JSON_UNESCAPED_UNICODE),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return [
            'status'  => true,
            'message' => 'Mahasiswa dinyatakan LULUS di tahap ' . $tahap . '.',
            'tahap'   => $tahap_key,
        ];
    }

    public function downgrade_stage($nim, $dari_tahap, $ke_tahap, $role = null, $alasan = '') {
        if (!$this->db->table_exists('guidance')) {
            return ['status' => false, 'message' => 'Tabel guidance tidak ditemukan.'];
        }

        $normalize = function ($t) {
            $map = [
                'Preview 1' => 'preview1', 'Preview 2' => 'preview2',
                'Preview 3' => 'preview3', 'Sidang' => 'sidang',
                'preview1' => 'preview1', 'preview2' => 'preview2',
                'preview3' => 'preview3', 'sidang' => 'sidang',
            ];
            return $map[$t] ?? null;
        };

        $dari = $normalize($dari_tahap);
        $ke   = $normalize($ke_tahap);
        if (!$dari || !$ke) {
            return ['status' => false, 'message' => 'Parameter tahap tidak valid.'];
        }

        $hierarchy = ['preview1' => 1, 'preview2' => 2, 'preview3' => 3, 'sidang' => 4];
        if ($hierarchy[$ke] >= $hierarchy[$dari]) {
            return ['status' => false, 'message' => 'Downgrade hanya boleh ke tahap yang lebih rendah.'];
        }

        $g = $this->get_guidance_by_nim($nim);
        if (!$g) {
            return ['status' => false, 'message' => 'Data bimbingan tidak ditemukan.'];
        }

        $target_rank = $hierarchy[$ke];

        if ($this->db->table_exists('thesis')) {
            $thesis_fields = $this->db->list_fields('thesis');

            $status_cols = array_values(array_filter($thesis_fields, function ($c) {
                return strpos($c, 'status_file_') === 0;
            }));
            $catatan_cols = array_values(array_filter($thesis_fields, function ($c) {
                return strpos($c, 'catatan_file_') === 0;
            }));

            foreach ($hierarchy as $stage => $rank) {
                if ($rank > $target_rank) {
                    $this->db->where('id_guidance', $g['id']);
                    $this->db->where('tahapan_preview', $stage);
                    $this->db->delete('thesis');
                } elseif ($rank === $target_rank) {
                    $update_thesis = [];
                    foreach ($status_cols as $c) $update_thesis[$c] = 'Pending';
                    foreach ($catatan_cols as $c) $update_thesis[$c] = '';
                    if (in_array('status', $thesis_fields)) $update_thesis['status'] = 'Pending';

                    if (!empty($update_thesis)) {
                        $this->db->where('id_guidance', $g['id']);
                        $this->db->where('tahapan_preview', $stage);
                        $this->db->update('thesis', $update_thesis);
                    }
                }
            }
        }

        $update_g = [];
        foreach ($hierarchy as $t => $rank) {
            if ($rank >= $target_rank) {
                $col = 'lulus_' . $t;
                if ($this->db->field_exists($col, 'guidance')) $update_g[$col] = 0;
            }
        }
        if ($this->db->field_exists('status_preview', 'guidance')) {
            $update_g['status_preview'] = $ke;
        }
        if ($this->db->field_exists('lulus_by_role', 'guidance')) $update_g['lulus_by_role'] = $role;
        if ($this->db->field_exists('lulus_at', 'guidance'))      $update_g['lulus_at']      = date('Y-m-d H:i:s');

        if (!empty($update_g)) {
            $this->db->where('id', $g['id'])->update('guidance', $update_g);
        }

        if ($this->db->field_exists('downgrade_log', 'guidance')) {
            $existing = $g['downgrade_log'] ?? '';
            $log_arr = [];
            if (!empty($existing)) {
                $dec = json_decode($existing, true);
                if (is_array($dec)) $log_arr = $dec;
            }
            $log_arr[] = [
                'dari'   => $dari,
                'ke'     => $ke,
                'alasan' => $alasan,
                'role'   => $role,
                'at'     => date('Y-m-d H:i:s'),
                'by'     => $this->session->userdata('name') ?: '-',
            ];
            $this->db->where('id', $g['id'])->update('guidance', [
                'downgrade_log' => json_encode($log_arr, JSON_UNESCAPED_UNICODE)
            ]);
        }

        if ($this->db->table_exists('log_approval_history')) {
            $this->db->insert('log_approval_history', [
                'modul'         => 'Downgrade Tahap',
                'ref_id'        => (string)$nim,
                'target_name'   => $nim,
                'action'        => 'Downgrade',
                'actor_id'      => $this->session->userdata('user_id'),
                'actor_name'    => $this->session->userdata('name') ?: '-',
                'actor_role'    => strtoupper((string)$role),
                'actor_nip_nim' => $this->session->userdata('nip') ?: $this->session->userdata('username'),
                'catatan'       => json_encode(['dari' => $dari, 'ke' => $ke, 'alasan' => $alasan], JSON_UNESCAPED_UNICODE),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return [
            'status'  => true,
            'message' => 'Mahasiswa berhasil di-downgrade ke ' . ucfirst($ke) . '. Semua berkas di-reset ke Pending.',
            'dari'    => $dari,
            'ke'      => $ke,
        ];
    }
}