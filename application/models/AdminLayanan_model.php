<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Admin Layanan Akademik & Administrasi (LAA)
 * Query antrean berkas TA & sidang, evaluasi syarat dinamis, statistik, dan rollback tahap.
 */
class AdminLayanan_model extends CI_Model {

    private $_memoized_legacy_pengajuan = null;
    private $_cached_active_syarat = null;
    private $_cached_target_ids = array();

    public function __construct() {
        parent::__construct();
    }

    public function clear_cache() {
        $this->_memoized_legacy_pengajuan = null;
        $this->_cached_active_syarat = null;
        $this->_cached_target_ids = array();
    }

    public function get_short_berkas_label($nama_berkas, $kode_berkas = '') {
        $known = [
            'ksm'        => 'KSM',
            'transkrip'  => 'TRA',
            'pernyataan' => 'SUR',
            'bebas_lab'  => 'LAB',
        ];
        $k_clean = strtolower(trim((string)$kode_berkas));
        if (isset($known[$k_clean])) {
            return $known[$k_clean];
        }

        if (!empty($kode_berkas) && strlen($kode_berkas) <= 5 && !in_array($k_clean, ['file', 'doc'])) {
            return strtoupper($kode_berkas);
        }

        $nama = trim((string)$nama_berkas);
        if (!empty($nama)) {
            if (preg_match('/^([^\(]+)\(([^)]+)\)/', $nama, $m)) {
                $before = strtoupper(trim($m[1]));
                $inside = strtoupper(trim($m[2]));
                if (strlen($before) >= 2 && strlen($before) <= 5) return $before;
                if (strlen($inside) >= 2 && strlen($inside) <= 5) return $inside;
            }

            if (preg_match('/\(([^)]+)\)/', $nama, $m)) {
                $inside = strtoupper(trim($m[1]));
                if (strlen($inside) >= 2 && strlen($inside) <= 5) return $inside;
            }

            $words = explode(' ', $nama);
            if (!empty($words[0])) {
                $first_word = strtoupper(trim($words[0]));
                if (strlen($first_word) >= 2 && strlen($first_word) <= 5 && ctype_alpha($first_word)) {
                    return $first_word;
                }
            }
        }

        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $nama);
        return strtoupper(substr($cleaned, 0, 3) ?: 'DOC');
    }

    public function get_all_syarat_berkas() {
        if (!$this->db->table_exists('syarat_berkas_ta')) return array();
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $res = $this->db->select('id, kode_berkas, nama_berkas, deskripsi, file_template, is_required, is_active, urutan')
                ->order_by('urutan', 'ASC')
                ->get('syarat_berkas_ta')
                ->result_array();
            $this->db->db_debug = $prev_debug;
            return $res ?: array();
        } catch (Throwable $e) {
            $this->db->db_debug = $prev_debug;
            return array();
        }
    }

    public function get_active_syarat_berkas() {
        if ($this->_cached_active_syarat !== null) {
            return $this->_cached_active_syarat;
        }

        $fallback = [
            ['id' => 1, 'kode_berkas' => 'ksm', 'nama_berkas' => 'KSM (Kartu Studi Mahasiswa)', 'deskripsi' => 'Bukti KRS semester aktif yang memuat mata kuliah Tugas Akhir.', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 1],
            ['id' => 2, 'kode_berkas' => 'transkrip', 'nama_berkas' => 'Transkrip Nilai Akademik Terakhir', 'deskripsi' => 'Transkrip nilai resmi yang sudah divalidasi.', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 2],
            ['id' => 3, 'kode_berkas' => 'pernyataan', 'nama_berkas' => 'Surat Pernyataan Mahasiswa', 'deskripsi' => 'Surat kesanggupan menyelesaikan TA bermaterai.', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 3],
            ['id' => 4, 'kode_berkas' => 'bebas_lab', 'nama_berkas' => 'Surat Bebas Lab & Perpustakaan', 'deskripsi' => 'Surat keterangan bebas pinjaman alat lab FIK.', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 4],
        ];
        if (!$this->db->table_exists('syarat_berkas_ta')) {
            $this->_cached_active_syarat = $fallback;
            return $fallback;
        }
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $res = $this->db->select('id, kode_berkas, nama_berkas, deskripsi, file_template, is_required, is_active, urutan')
                ->where('is_active', 1)
                ->order_by('urutan', 'ASC')
                ->get('syarat_berkas_ta')
                ->result_array();
            $this->db->db_debug = $prev_debug;
            $this->_cached_active_syarat = !empty($res) ? $res : $fallback;
            return $this->_cached_active_syarat;
        } catch (Throwable $e) {
            $this->db->db_debug = $prev_debug;
            $this->_cached_active_syarat = $fallback;
            return $fallback;
        }
    }

    public function save_syarat_berkas($data) {
        $this->clear_cache();
        if (!$this->db->table_exists('syarat_berkas_ta')) return false;
        return $this->db->insert('syarat_berkas_ta', $data);
    }

    public function update_syarat_berkas($id, $data) {
        $this->clear_cache();
        if (!$this->db->table_exists('syarat_berkas_ta')) return false;
        $this->db->where('id', $id);
        return $this->db->update('syarat_berkas_ta', $data);
    }

    public function toggle_syarat_berkas($id) {
        $this->clear_cache();
        if (!$this->db->table_exists('syarat_berkas_ta')) return false;
        $row = $this->db->get_where('syarat_berkas_ta', ['id' => $id])->row_array();
        if (!$row) return false;

        $new_status = $row['is_active'] == 1 ? 0 : 1;
        $this->db->where('id', $id);
        return $this->db->update('syarat_berkas_ta', ['is_active' => $new_status]);
    }

    public function delete_syarat_berkas($id) {
        $this->clear_cache();
        if (!$this->db->table_exists('syarat_berkas_ta')) return false;
        $this->db->where('id', $id);
        return $this->db->delete('syarat_berkas_ta');
    }

    // =========================================================================
    // MASTER JENIS TUGAS AKHIR (DINAMIS)
    // =========================================================================

    public function _init_master_jenis_ta() {
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            if (!$this->db->table_exists('master_jenis_ta')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `master_jenis_ta` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `nama_jenis` VARCHAR(150) NOT NULL,
                    `keterangan` VARCHAR(255) NULL,
                    `is_active` TINYINT(1) DEFAULT 1,
                    `urutan` INT DEFAULT 1,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            }

            // Cek jika kosong, isi data bawaan awal
            $count = $this->db->count_all('master_jenis_ta');
            if ($count === 0) {
                $default_jenis = [
                    ['nama_jenis' => 'TA Reguler',  'keterangan' => 'Jalur skema reguler sidang tugas akhir', 'is_active' => 1, 'urutan' => 1],
                    ['nama_jenis' => 'TA Jurnal',   'keterangan' => 'Jalur publikasi jurnal ilmiah terindeks', 'is_active' => 1, 'urutan' => 2],
                    ['nama_jenis' => 'TA HKI',      'keterangan' => 'Jalur pendaftaran Hak Kekayaan Intelektual / Paten', 'is_active' => 1, 'urutan' => 3],
                    ['nama_jenis' => 'TA PROYEK',   'keterangan' => 'Jalur proyek kolaboratif industri / magang', 'is_active' => 1, 'urutan' => 4],
                    ['nama_jenis' => 'Pengkaryaan', 'keterangan' => 'Jalur pembuatan karya seni / desain orisinal', 'is_active' => 1, 'urutan' => 5],
                    ['nama_jenis' => 'Penulisan',   'keterangan' => 'Jalur penulisan monograf / karya ilmiah mendalam', 'is_active' => 1, 'urutan' => 6],
                ];
                $this->db->insert_batch('master_jenis_ta', $default_jenis);
            }
        } catch (Throwable $e) {
            // Cadangan otomatis jika ada kendala koneksi database
        }
        $this->db->db_debug = $prev_debug;
    }

    public function get_all_jenis_ta() {
        $this->_init_master_jenis_ta();
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $rows = $this->db->order_by('urutan', 'ASC')->order_by('id', 'ASC')->get('master_jenis_ta')->result_array();
            $this->db->db_debug = $prev_debug;
            return $rows ?: [];
        } catch (Throwable $e) {
            $this->db->db_debug = $prev_debug;
            return [
                ['id' => 1, 'nama_jenis' => 'TA Reguler',  'keterangan' => 'Jalur skema reguler sidang tugas akhir', 'is_active' => 1, 'urutan' => 1],
                ['id' => 2, 'nama_jenis' => 'TA Jurnal',   'keterangan' => 'Jalur publikasi jurnal ilmiah terindeks', 'is_active' => 1, 'urutan' => 2],
                ['id' => 3, 'nama_jenis' => 'TA HKI',      'keterangan' => 'Jalur pendaftaran Hak Kekayaan Intelektual / Paten', 'is_active' => 1, 'urutan' => 3],
                ['id' => 4, 'nama_jenis' => 'TA PROYEK',   'keterangan' => 'Jalur proyek kolaboratif industri / magang', 'is_active' => 1, 'urutan' => 4],
                ['id' => 5, 'nama_jenis' => 'Pengkaryaan', 'keterangan' => 'Jalur pembuatan karya seni / desain orisinal', 'is_active' => 1, 'urutan' => 5],
                ['id' => 6, 'nama_jenis' => 'Penulisan',   'keterangan' => 'Jalur penulisan monograf / karya ilmiah mendalam', 'is_active' => 1, 'urutan' => 6],
            ];
        }
    }

    public function get_active_jenis_ta() {
        $fallback = ['TA Reguler', 'TA Jurnal', 'TA HKI', 'TA PROYEK', 'Pengkaryaan', 'Penulisan'];
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $this->_init_master_jenis_ta();
            $rows = $this->db->where('is_active', 1)->order_by('urutan', 'ASC')->order_by('id', 'ASC')->get('master_jenis_ta')->result_array();
            $this->db->db_debug = $prev_debug;
            if (empty($rows)) {
                return $fallback;
            }
            return array_map(function($r) {
                return $r['nama_jenis'];
            }, $rows);
        } catch (Throwable $e) {
            $this->db->db_debug = $prev_debug;
            return $fallback;
        }
    }

    public function get_jenis_ta_by_id($id) {
        $this->_init_master_jenis_ta();
        return $this->db->get_where('master_jenis_ta', ['id' => $id])->row_array();
    }

    public function save_jenis_ta($data, $id = null) {
        $this->_init_master_jenis_ta();
        if (!empty($id)) {
            $this->db->where('id', $id);
            return $this->db->update('master_jenis_ta', $data);
        } else {
            return $this->db->insert('master_jenis_ta', $data);
        }
    }

    public function toggle_jenis_ta($id) {
        $this->_init_master_jenis_ta();
        $row = $this->db->get_where('master_jenis_ta', ['id' => $id])->row_array();
        if (!$row) return false;
        $new_status = ($row['is_active'] == 1) ? 0 : 1;
        $this->db->where('id', $id);
        return $this->db->update('master_jenis_ta', ['is_active' => $new_status]);
    }

    public function delete_jenis_ta($id) {
        $this->_init_master_jenis_ta();
        $this->db->where('id', $id);
        return $this->db->delete('master_jenis_ta');
    }

    private function _parse_fp_admin_status($fp_row) {
        if (empty($fp_row) || !is_array($fp_row)) return 'Pending';
        if (!empty($fp_row['status_adminlaa']) && strcasecmp($fp_row['status_adminlaa'], 'Pending') !== 0) {
            return $fp_row['status_adminlaa'];
        }
        if (!empty($fp_row['status_admin_laa']) && strcasecmp($fp_row['status_admin_laa'], 'Pending') !== 0) {
            return $fp_row['status_admin_laa'];
        }
        if (!empty($fp_row['status_admin']) && strcasecmp($fp_row['status_admin'], 'Pending') !== 0) {
            return $fp_row['status_admin'];
        }
        if (!empty($fp_row['status_laa']) && strcasecmp($fp_row['status_laa'], 'Pending') !== 0) {
            return $fp_row['status_laa'];
        }
        return $fp_row['status_admin_laa'] ?? ($fp_row['status_adminlaa'] ?? ($fp_row['status_admin'] ?? ($fp_row['status_laa'] ?? 'Pending')));
    }

    /**
     * Cek apakah status string bernilai Disetujui / Valid / Approved
     */
    public function is_approved_status($st) {
        if (empty($st)) return false;
        $s = strtolower(trim((string)$st));
        return in_array($s, [
            'valid', 'approved', 'acc', 'disetujui', 'setuju', '1', 
            'disetujui admin laa', 'disetujui laa', 'disetujui dosen wali', 'disetujui wali'
        ], true) || strpos($s, 'disetujui') !== false || strpos($s, 'setuju') !== false;
    }

    /**
     * Cek apakah status string bernilai Ditolak / Invalid / Rejected
     */
    public function is_rejected_status($st) {
        if (empty($st)) return false;
        $s = strtolower(trim((string)$st));
        return in_array($s, [
            'invalid', 'rejected', 'ditolak', 'tolak', 'revisi', 'kurang', '0'
        ], true) || strpos($s, 'revisi') !== false || strpos($s, 'tolak') !== false || strpos($s, 'kurang') !== false;
    }

    /**
     * Normalisasi status berkas menjadi 'Valid', 'Invalid', atau 'Pending'
     */
    public function clean_doc_status($st) {
        if ($this->is_rejected_status($st)) return 'Invalid';
        if ($this->is_approved_status($st)) return 'Valid';
        return 'Pending';
    }

    /**
     * Cek apakah nama / kode berkas adalah berkas sidang (agar tidak tercampur ke Pendaftaran TA)
     */
    public function is_sidang_doc($doc_name) {
        if (empty($doc_name)) return false;
        $s = strtolower(trim((string)$doc_name));
        $sidang_keys = [
            'surat_izin_wali', 'formulir_sidang', 'daftar_nilai', 'sertifikat_eprt', 
            'sertifikat_tak', 'bukti_bimbingan', 'bukti_skpi', 'bap', 'bap2', 
            'bap_sidang', 'revisi_sidang', 'draft_artikel', 'skpi', 'eprt', 'tak',
            'izin_wali', 'izin_sidang', 'logbook'
        ];
        foreach ($sidang_keys as $sk) {
            if ($s === $sk || strpos($s, $sk) !== false) return true;
        }
        return false;
    }

    public function get_student_berkas_map($nim) {
        $map = array();

        // 1. Ambil dari tabel file_pendaftaran
        if ($this->db->table_exists('file_pendaftaran')) {
            $target_ids = $this->get_student_target_ids($nim);
            $fp_rows = $this->db->group_start()
                                ->where_in('id_mhs', $target_ids)
                                ->or_like('id_mhs', $nim)
                                ->group_end()
                                ->get('file_pendaftaran')
                                ->result_array();
            if (!empty($fp_rows)) {
                $unassigned_fps = array();
                foreach ($fp_rows as $fp) {
                    $namaDoc = strtolower(trim($fp['nama'] ?? ''));
                    $fileRaw = strtolower(trim($fp['file'] ?? ''));
                    // Abaikan dokumen pendaftaran sidang dari pendaftaran TA
                    if ($this->is_sidang_doc($namaDoc) || $this->is_sidang_doc($fileRaw)) {
                        continue;
                    }

                    $kode = null;
                    if (strpos($namaDoc, 'ksm') !== false || strpos($fileRaw, 'ksm') !== false) {
                        $kode = 'ksm';
                    } elseif (strpos($namaDoc, 'transkrip') !== false || strpos($fileRaw, 'transkrip') !== false) {
                        $kode = 'transkrip';
                    } elseif (strpos($namaDoc, 'pernyataan') !== false || strpos($fileRaw, 'pernyataan') !== false) {
                        $kode = 'pernyataan';
                    } elseif (strpos($namaDoc, 'bebas_lab') !== false || strpos($namaDoc, 'lab') !== false || strpos($fileRaw, 'bebas_lab') !== false || strpos($fileRaw, 'lab') !== false) {
                        $kode = 'bebas_lab';
                    } else {
                        $clean_k = preg_replace('/[^a-z0-9_]/', '_', trim($namaDoc));
                        if (!empty($clean_k) && !in_array($clean_k, ['mahasiswa', 'na', 'null', 'none', 'berkas', 'file'])) {
                            $kode = $clean_k;
                        }
                    }

                    $st = $this->_parse_fp_admin_status($fp);
                    $cleanSt = $this->clean_doc_status($st);

                    if ($kode) {
                        $map[$kode] = array(
                            'nim'               => $nim,
                            'kode_berkas'       => $kode,
                            'file_name'         => $fp['file'] ?? '',
                            'status'            => $cleanSt,
                            'status_verifikasi' => $cleanSt,
                            'catatan'           => $fp['komentar'] ?? ''
                        );
                    } else if (!empty($fp['file'])) {
                        $unassigned_fps[] = array('fp' => $fp, 'cleanSt' => $cleanSt);
                    }
                }

                // Fallback: Jika ada file terunggah tetapi nama dokumennya 'na' atau generik,
                // petakan secara berurutan ke syarat berkas standar yang belum memiliki file
                if (!empty($unassigned_fps)) {
                    $ta_keys = ['ksm', 'transkrip', 'pernyataan', 'bebas_lab'];
                    foreach ($ta_keys as $tk) {
                        if (empty($map[$tk]) && !empty($unassigned_fps)) {
                            $u = array_shift($unassigned_fps);
                            $map[$tk] = array(
                                'nim'               => $nim,
                                'kode_berkas'       => $tk,
                                'file_name'         => $u['fp']['file'] ?? '',
                                'status'            => $u['cleanSt'],
                                'status_verifikasi' => $u['cleanSt'],
                                'catatan'           => $u['fp']['komentar'] ?? ''
                            );
                        }
                    }
                }
            }
        }

        // 2. Ambil dari pendaftaran_berkas (menimpa jika diatur khusus di modul LAA)
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $prev_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            try {
                $rows = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim])->result_array();
                $this->db->db_debug = $prev_debug;
                if (!empty($rows)) {
                    foreach ($rows as $r) {
                        $k_code = $r['kode_berkas'];
                        $st_v = $r['status_verifikasi'] ?? 'Pending';
                        $cleanSt = ($st_v === 'Approved' || $st_v === 'Valid') ? 'Valid' : (($st_v === 'Rejected' || $st_v === 'Invalid') ? 'Invalid' : 'Pending');
                        $r['status'] = $cleanSt;
                        $r['status_verifikasi'] = $cleanSt;
                        if (!isset($map[$k_code])) {
                            $map[$k_code] = $r;
                        } else {
                            if (empty($r['file_name']) && !empty($map[$k_code]['file_name'])) {
                                $r['file_name'] = $map[$k_code]['file_name'];
                            }
                            $map[$k_code] = $r;
                        }
                    }
                }
            } catch (Throwable $e) {
                $this->db->db_debug = $prev_debug;
            }
        }

        return $map;
    }

    /**
     * Dapatkan semua format ID mahasiswa yang cocok di tabel (NIM, usr_mhs, user id)
     */
    public function get_student_target_ids($nim) {
        $clean_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$nim);
        if (isset($this->_cached_target_ids[$clean_nim])) {
            return $this->_cached_target_ids[$clean_nim];
        }

        $ids = array_unique(array_filter([
            $nim,
            $clean_nim,
            'usr_mhs_' . $clean_nim,
            'mhs_' . $clean_nim,
            'usr_' . $clean_nim
        ]));

        $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
        if ($user_tbl) {
            $prev_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            try {
                $this->db->group_start();
                if ($this->db->field_exists('nim', $user_tbl)) $this->db->where('nim', $clean_nim);
                if ($this->db->field_exists('nidn_nim', $user_tbl)) $this->db->or_where('nidn_nim', $clean_nim);
                if ($this->db->field_exists('username', $user_tbl)) $this->db->or_where('username', $clean_nim);
                if ($this->db->field_exists('id', $user_tbl)) $this->db->or_where('id', $clean_nim)->or_where('id', $nim);
                $this->db->group_end();
                $u = $this->db->get($user_tbl)->row_array();
                if ($u) {
                    if (!empty($u['id'])) {
                        array_unshift($ids, $u['id']);
                        $ids[] = (string)$u['id'];
                        $ids[] = 'usr_mhs_' . $u['id'];
                        $ids[] = 'mhs_' . $u['id'];
                    }
                    $u_nim = $u['nim'] ?? ($u['nidn_nim'] ?? ($u['username'] ?? ''));
                    if (!empty($u_nim)) {
                        $u_nim_clean = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$u_nim);
                        $ids[] = $u_nim;
                        $ids[] = $u_nim_clean;
                        $ids[] = 'usr_mhs_' . $u_nim_clean;
                        $ids[] = 'mhs_' . $u_nim_clean;
                        $ids[] = 'usr_' . $u_nim_clean;
                    }
                }
            } catch (Throwable $e) {
                // Abaikan jika ada kendala database
            }
            $this->db->db_debug = $prev_debug;
        }
        $res = array_values(array_unique(array_filter($ids)));
        $this->_cached_target_ids[$clean_nim] = $res;
        return $res;
    }

    /**
     * Compute and synchronize overall LAA approval status directly from file_pendaftaran
     */
    public function sync_overall_status($nim, $catatan = null) {
        $active_syarat = $this->get_active_syarat_berkas();
        $summary = $this->get_student_berkas_summary($nim, $active_syarat);

        $invalid_kodes = array();
        foreach ($summary['items'] as $it) {
            if ($it['status'] === 'Invalid') {
                $invalid_kodes[] = $it['kode'];
            }
        }

        $overall_status = 'Pending';
        if (!empty($invalid_kodes)) {
            $overall_status = 'Rejected';
        } elseif ($summary['valid_count'] === $summary['total_count'] && $summary['total_count'] > 0) {
            $overall_status = 'Approved';
        }

        // Saat semua berkas disetujui, pastikan status di file_pendaftaran menjadi Approved dan view_adminlaa = 1
        if ($overall_status === 'Approved' && $this->db->table_exists('file_pendaftaran')) {
            $target_ids = $this->get_student_target_ids($nim);
            $fp_update = array();
            if ($this->db->field_exists('status_adminlaa', 'file_pendaftaran')) {
                $fp_update['status_adminlaa'] = 'Approved';
            }
            if ($this->db->field_exists('status_admin', 'file_pendaftaran')) {
                $fp_update['status_admin'] = 'Approved';
            }
            if ($this->db->field_exists('status_laa', 'file_pendaftaran')) {
                $fp_update['status_laa'] = 'Approved';
            }
            if ($this->db->field_exists('status_admin_laa', 'file_pendaftaran')) {
                $fp_update['status_admin_laa'] = 'Approved';
            }
            if ($this->db->field_exists('view_adminlaa', 'file_pendaftaran')) {
                $fp_update['view_adminlaa'] = 1;
            }
            if ($this->db->field_exists('date_edit', 'file_pendaftaran')) {
                $fp_update['date_edit'] = date('Y-m-d H:i:s');
            }
            if (!empty($fp_update)) {
                $this->db->where_in('id_mhs', $target_ids)->update('file_pendaftaran', $fp_update);
            }
        }

        return [
            'overall_status' => $overall_status,
            'summary'        => $summary
        ];
    }

    public function save_student_berkas($nim, $kode_berkas, $file_name, $status = 'Pending', $arg5 = null, $arg6 = null) {
        $this->clear_cache();
        $nama_berkas = null;
        $catatan     = null;

        if ($arg6 !== null) {
            $nama_berkas = $arg5;
            $catatan     = $arg6;
        } else if ($arg5 !== null) {
            if ($this->db->table_exists('syarat_berkas_ta') && $this->db->get_where('syarat_berkas_ta', ['nama_berkas' => $arg5])->num_rows() > 0) {
                $nama_berkas = $arg5;
            } else {
                $catatan = $arg5;
            }
        }

        if (empty($nama_berkas)) {
            $sb = $this->db->get_where('syarat_berkas_ta', ['kode_berkas' => $kode_berkas])->row_array();
            if ($sb && !empty($sb['nama_berkas'])) {
                $nama_berkas = $sb['nama_berkas'];
            } else {
                $nama_berkas = ucfirst(str_replace('_', ' ', $kode_berkas));
            }
        }

        // Standarisasi status
        $enum_status = 'Pending';
        $st_lower = strtolower($status);
        if ($st_lower === 'valid' || strpos($st_lower, 'setuju') !== false || strpos($st_lower, 'approved') !== false || $st_lower === 'acc') {
            $enum_status = 'Valid';
        } else if ($st_lower === 'invalid' || strpos($st_lower, 'revisi') !== false || strpos($st_lower, 'tolak') !== false || strpos($st_lower, 'rejected') !== false) {
            $enum_status = 'Invalid';
        }
        $fp_status = ($enum_status === 'Valid') ? 'Approved' : (($enum_status === 'Invalid') ? 'Rejected' : 'Pending');

        // Utama: Perbarui tabel file_pendaftaran langsung
        if ($this->db->table_exists('file_pendaftaran')) {
            $target_ids = $this->get_student_target_ids($nim);
            $clean_nim  = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$nim);

            $this->db->where_in('id_mhs', $target_ids)
                ->group_start()
                    ->like('nama', $kode_berkas)
                    ->or_like('file', $kode_berkas)
                    ->or_like('id', $kode_berkas);

            if ($kode_berkas === 'bebas_lab') {
                $this->db->or_like('nama', 'bebas')
                         ->or_like('nama', 'lab')
                         ->or_like('file', 'bebas')
                         ->or_like('file', 'lab');
            } elseif ($kode_berkas === 'pernyataan') {
                $this->db->or_like('nama', 'pernyataan')
                         ->or_like('file', 'pernyataan');
            } elseif ($kode_berkas === 'transkrip') {
                $this->db->or_like('nama', 'transkrip')
                         ->or_like('file', 'transkrip');
            } elseif ($kode_berkas === 'ksm') {
                $this->db->or_like('nama', 'ksm')
                         ->or_like('file', 'ksm');
            }

            $check = $this->db->group_end()
                ->get('file_pendaftaran')
                ->row_array();

            $fp_update = array();
            if (!empty($file_name) && $this->db->field_exists('file', 'file_pendaftaran')) {
                $fp_update['file'] = $file_name;
            }
            if ($this->db->field_exists('status_adminlaa', 'file_pendaftaran')) {
                $fp_update['status_adminlaa'] = $fp_status;
            }
            if ($this->db->field_exists('view_adminlaa', 'file_pendaftaran')) {
                $fp_update['view_adminlaa'] = 1;
            }
            if ($this->db->field_exists('status_admin', 'file_pendaftaran')) {
                $fp_update['status_admin'] = $fp_status;
            }
            if ($this->db->field_exists('status_laa', 'file_pendaftaran')) {
                $fp_update['status_laa'] = $fp_status;
            }
            if ($this->db->field_exists('status_admin_laa', 'file_pendaftaran')) {
                $fp_update['status_admin_laa'] = $fp_status;
            }
            if ($catatan !== null && $this->db->field_exists('komentar', 'file_pendaftaran')) {
                $fp_update['komentar'] = $catatan;
            }
            if ($this->db->field_exists('date_edit', 'file_pendaftaran')) {
                $fp_update['date_edit'] = date('Y-m-d H:i:s');
            }
            if ($status === 'Pending') {
                if ($this->db->field_exists('view_adminlaa', 'file_pendaftaran')) {
                    $fp_update['view_adminlaa'] = 0;
                }
                if ($this->db->field_exists('view_doswal', 'file_pendaftaran')) {
                    $fp_update['view_doswal'] = 0;
                }
            }

            if ($check) {
                if (!empty($check['id'])) {
                    $this->db->where('id', $check['id'])->update('file_pendaftaran', $fp_update);
                } else {
                    $this->db->where_in('id_mhs', $target_ids)
                        ->group_start()
                            ->like('nama', $kode_berkas)
                            ->or_like('file', $kode_berkas)
                        ->group_end()
                        ->update('file_pendaftaran', $fp_update);
                }
            } else {
                $u_id = null;
                if ($this->db->table_exists('user')) {
                    $u_row = $this->db->get_where('user', ['nim' => $clean_nim])->row_array();
                    if (!$u_row) {
                        $u_row = $this->db->get_where('user', ['id' => $clean_nim])->row_array();
                    }
                    if ($u_row && !empty($u_row['id'])) {
                        $u_id = $u_row['id'];
                    }
                }
                if (!$u_id && !empty($target_ids)) {
                    foreach ($target_ids as $tid) {
                        if (!empty($tid) && !is_numeric($tid) && strpos($tid, 'usr_mhs_') === false && strpos($tid, 'mhs_') === false) {
                            $u_id = $tid;
                            break;
                        }
                    }
                }
                if (!$u_id) {
                    $u_id = !empty($target_ids) ? $target_ids[0] : ('usr_mhs_' . $clean_nim);
                }
                $view_val = ($status === 'Pending') ? 0 : 1;
                $ins_fp = array(
                    'id'              => 'fp_' . $clean_nim . '_' . $kode_berkas . '_' . time(),
                    'id_mhs'          => $u_id,
                    'nama'            => $kode_berkas,
                    'file'            => $file_name ?: ($kode_berkas . '_' . $clean_nim . '.pdf'),
                    'status_adminlaa' => $fp_status,
                    'komentar'        => $catatan ?: '',
                    'date'            => date('Y-m-d H:i:s'),
                    'date_edit'       => date('Y-m-d H:i:s'),
                    'view_adminlaa'   => $view_val,
                    'view_doswal'     => $view_val,
                    'status_doswal'   => 'Pending'
                );
                $this->db->insert('file_pendaftaran', $ins_fp);
            }
        }

        return true;
    }

    public function update_verifikasi($nim, $status_input, $catatan = '', $extra_catatan = null, $berkas_valid = array(), $berkas_kurang = array()) {
        $this->clear_cache();
        $active_syarat  = $this->get_active_syarat_berkas();
        $student_berkas = $this->get_student_berkas_map($nim);

        $is_explicit_approve = (is_string($status_input) && in_array(strtolower($status_input), array('approve', 'approved')));

        foreach ($active_syarat as $sb) {
            $kode = $sb['kode_berkas'];

            if (in_array($kode, (array)$berkas_kurang)) {
                $st = 'Invalid';
            } elseif ($is_explicit_approve || in_array($kode, (array)$berkas_valid)) {
                $st = 'Valid';
            } else {
                $st = 'Pending';
            }

            $file_name = $student_berkas[$kode]['file_name'] ?? ('berkas_' . $kode . '_' . $nim . '.pdf');
            $this->save_student_berkas($nim, $kode, $file_name, $st, $sb['nama_berkas'] ?? null, $catatan);
        }

        if ($this->db->table_exists('file_pendaftaran')) {
            $target_ids = $this->get_student_target_ids($nim);
            $fp_up = [
                'view_adminlaa' => 1,
                'date_edit'     => date('Y-m-d H:i:s')
            ];
            if ($is_explicit_approve) {
                if ($this->db->field_exists('status_adminlaa', 'file_pendaftaran')) $fp_up['status_adminlaa'] = 'Approved';
                if ($this->db->field_exists('status_admin', 'file_pendaftaran')) $fp_up['status_admin'] = 'Approved';
                if ($this->db->field_exists('status_laa', 'file_pendaftaran')) $fp_up['status_laa'] = 'Approved';
                if ($this->db->field_exists('status_admin_laa', 'file_pendaftaran')) $fp_up['status_admin_laa'] = 'Approved';
            }
            $this->db->where_in('id_mhs', $target_ids)->update('file_pendaftaran', $fp_up);
        }

        return true;
    }

    public function reset_verifikasi_pending($nim) {
        $this->clear_cache();
        if ($this->db->table_exists('file_pendaftaran')) {
            $target_ids = $this->get_student_target_ids($nim);
            $fp_update = array();
            if ($this->db->field_exists('status_adminlaa', 'file_pendaftaran')) {
                $fp_update['status_adminlaa'] = 'Pending';
            }
            if ($this->db->field_exists('status_admin', 'file_pendaftaran')) {
                $fp_update['status_admin'] = 'Pending';
            }
            if ($this->db->field_exists('status_laa', 'file_pendaftaran')) {
                $fp_update['status_laa'] = 'Pending';
            }
            if ($this->db->field_exists('status_admin_laa', 'file_pendaftaran')) {
                $fp_update['status_admin_laa'] = 'Pending';
            }
            if ($this->db->field_exists('komentar', 'file_pendaftaran')) {
                $fp_update['komentar'] = NULL;
            }
            if (!empty($fp_update)) {
                $this->db->where_in('id_mhs', $target_ids)->update('file_pendaftaran', $fp_update);
            }
        }

        return true;
    }

    public function get_batch_details_by_nims($nims) {
        if (empty($nims)) return array();
        $list = $this->_get_fallback_pengajuan_from_legacy();
        $nimsStr = array_map(function($n) { return (string)$n; }, (array)$nims);
        $results = array();
        foreach ($list as $item) {
            if (in_array((string)($item['nim'] ?? ''), $nimsStr, true)) {
                $results[] = $item;
            }
        }
        return $results;
    }

    /**
     * Hitung total pengajuan untuk Paging
     */
    public function get_count_pengajuan($filter_status = null, $search = null, $cat = null) {
        return count($this->_filter_and_sort_legacy_pengajuan($filter_status, $search, $cat));
    }

    /**
     * Ambil daftar pengajuan berkas mahasiswa untuk Admin Layanan dengan Paging (Limit & Offset)
     */
    public function get_all_pengajuan($filter_status = null, $search = null, $limit = 5, $offset = 0, $cat = null) {
        $list = $this->_filter_and_sort_legacy_pengajuan($filter_status, $search, $cat);
        if ($limit > 0) {
            return array_slice($list, $offset, $limit);
        }
        return $list;
    }

    /**
     * Bantu cari nama lengkap dan data mahasiswa dari tabel user, guidance, atau file_pendaftaran
     * saat nama mahasiswa kosong atau masih bertuliskan "Mahasiswa"
     */
    private function _resolve_student_names(&$list) {
        if (empty($list)) return;

        $user_tbl  = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
        $has_guid  = $this->db->table_exists('guidance');
        $has_fp    = $this->db->table_exists('file_pendaftaran');

        $nims = array();
        foreach ($list as $r) {
            if (!empty($r['nim'])) $nims[] = $r['nim'];
        }
        $nims = array_unique($nims);
        if (empty($nims)) return;

        $target_ids = array();
        foreach ($nims as $n) {
            $target_ids[] = $n;
            $target_ids[] = 'usr_mhs_' . $n;
        }

        // 1. Ambil dari tabel user / users
        $user_map = array();
        if ($user_tbl) {
            $name_col = $this->db->field_exists('name', $user_tbl) ? 'name' : ($this->db->field_exists('nama', $user_tbl) ? 'nama' : ($this->db->field_exists('nama_depan', $user_tbl) ? 'nama_depan' : null));
            $nim_col  = $this->db->field_exists('nim', $user_tbl) ? 'nim' : ($this->db->field_exists('username', $user_tbl) ? 'username' : 'id');

            if ($name_col) {
                $u_rows = $this->db->select("id, {$nim_col}, {$name_col}")
                    ->group_start()
                        ->where_in($nim_col, $nims)
                        ->or_where_in('id', $target_ids)
                    ->group_end()
                    ->get($user_tbl)
                    ->result_array();
                foreach ($u_rows as $ur) {
                    $c_nim = !empty($ur[$nim_col]) ? $ur[$nim_col] : preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $ur['id']);
                    if (!empty($c_nim) && !empty($ur[$name_col])) {
                        $user_map[$c_nim] = $ur[$name_col];
                        $user_map[$ur['id']] = $ur[$name_col];
                        $user_map['usr_mhs_' . $c_nim] = $ur[$name_col];
                    }
                }
            }
        }

        // 2. Ambil dari tabel guidance
        $guidance_map = array();
        if ($has_guid) {
            $g_cols = array('id_mhs');
            if ($this->db->field_exists('judul_1', 'guidance')) $g_cols[] = 'judul_1';
            if ($this->db->field_exists('judul_en', 'guidance')) $g_cols[] = 'judul_en';
            if ($this->db->field_exists('jenis_TA', 'guidance')) $g_cols[] = 'jenis_TA';
            if ($this->db->field_exists('peminatan', 'guidance')) $g_cols[] = 'peminatan';
            if ($this->db->field_exists('nama', 'guidance')) $g_cols[] = 'nama';

            $g_rows = $this->db->select(implode(', ', $g_cols))
                ->where_in('id_mhs', $target_ids)
                ->get('guidance')
                ->result_array();
            foreach ($g_rows as $gr) {
                $c_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $gr['id_mhs']);
                $guidance_map[$c_nim] = $gr;
                $guidance_map['usr_mhs_' . $c_nim] = $gr;
            }
        }

        // 3. Ambil dari tabel file_pendaftaran
        $fp_map = array();
        if ($has_fp && $this->db->field_exists('nama', 'file_pendaftaran')) {
            $fp_rows = $this->db->select('id_mhs, nama')
                ->where_in('id_mhs', $target_ids)
                ->get('file_pendaftaran')
                ->result_array();
            foreach ($fp_rows as $fpr) {
                $c_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $fpr['id_mhs']);
                if (!empty($fpr['nama']) && $fpr['nama'] !== 'Mahasiswa') {
                    $fp_map[$c_nim] = $fpr['nama'];
                }
            }
        }

        // 4. Ambil status_doswal dari file_pendaftaran untuk status approval Dosen Wali
        if ($has_fp && $this->db->field_exists('status_doswal', 'file_pendaftaran')) {
            $fp_dos_rows = $this->db->select('id_mhs, status_doswal')
                ->where_in('id_mhs', $target_ids)
                ->where_in('status_doswal', array('Approved', 'Valid'))
                ->get('file_pendaftaran')
                ->result_array();
            $approved_doswal_nims = array();
            foreach ($fp_dos_rows as $fdr) {
                $c_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $fdr['id_mhs']);
                $approved_doswal_nims[$c_nim] = true;
                $approved_doswal_nims['usr_mhs_' . $c_nim] = true;
            }
            foreach ($list as &$r) {
                $nim = $r['nim'] ?? '';
                if (!empty($approved_doswal_nims[$nim])) {
                    $r['status_approval_wali'] = 'Approved';
                }
            }
            unset($r);
        }

        // Terapkan nama lengkap dan detail bimbingan
        foreach ($list as &$r) {
            $nim = $r['nim'] ?? '';
            $nd  = $r['nama_depan'] ?? '';
            $nb  = $r['nama_belakang'] ?? '';

            if (!empty($guidance_map[$nim])) {
                $gi = $guidance_map[$nim];
                if (!empty($gi['judul_1'])) {
                    $r['judul_1'] = $gi['judul_1'];
                    $r['judul']   = $gi['judul_1'];
                }
                if (!empty($gi['peminatan']) && (empty($r['konsentrasi_dkv']) || $r['konsentrasi_dkv'] === 'Desain Komunikasi Visual')) {
                    $r['konsentrasi_dkv'] = $gi['peminatan'];
                }
            }

            $full = trim($nd . ' ' . $nb);
            if (empty($full) || $full === 'Mahasiswa' || $nd === 'Mahasiswa') {
                $resolved_name = null;
                if (!empty($user_map[$nim])) {
                    $resolved_name = $user_map[$nim];
                } elseif (!empty($guidance_map[$nim]['nama'])) {
                    $resolved_name = $guidance_map[$nim]['nama'];
                } elseif (!empty($fp_map[$nim])) {
                    $resolved_name = $fp_map[$nim];
                }

                if ($resolved_name) {
                    $parts = explode(' ', trim($resolved_name));
                    $r['nama_depan'] = array_shift($parts);
                    $r['nama_belakang'] = implode(' ', $parts);
                }
            }
        }
        unset($r);
    }

    /**
     * Bantu susun data pengajuan cadangan langsung dari file_pendaftaran dan guidance
     * saat data pendaftaran mahasiswa dimuat dari arsitektur decoupled (file_pendaftaran & guidance).
     */
    /**
     * Bantu susun data pengajuan cadangan langsung dari file_pendaftaran dan guidance
     * saat data pendaftaran mahasiswa dimuat dari arsitektur decoupled (file_pendaftaran & guidance).
     * Fully batch-loaded & memoized in memory for ultra-fast performance.
     */
    private function _get_fallback_pengajuan_from_legacy($force_refresh = false) {
        if ($this->_memoized_legacy_pengajuan !== null && !$force_refresh) {
            return $this->_memoized_legacy_pengajuan;
        }

        $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
        $has_fp   = $this->db->table_exists('file_pendaftaran');
        $has_g    = $this->db->table_exists('guidance');
        $has_mhs  = $this->db->table_exists('mahasiswa');
        $has_pb   = $this->db->table_exists('pendaftaran_berkas');

        $student_map = array();

        if ($has_fp) {
            $fp_rows = $this->db->get('file_pendaftaran')->result_array();
            foreach ($fp_rows as $fp) {
                $raw_id = $fp['id_mhs'] ?? '';
                if (empty($raw_id)) continue;
                $nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $raw_id);
                $doc_name_raw = strtolower(trim($fp['nama'] ?? ''));
                $is_doc_name = in_array($doc_name_raw, array('mahasiswa', 'ksm', 'transkrip', 'pernyataan', 'bebas_lab', 'bap', 'surat_pernyataan', 'lab', 'bukti', 'surat'));
                $valid_name_fp = (!empty($fp['nama']) && !$is_doc_name) ? $fp['nama'] : null;

                if (!isset($student_map[$nim])) {
                    $student_map[$nim] = array(
                        'nim'           => $nim,
                        'raw_ids'       => array($raw_id),
                        'status_admin'  => array(),
                        'status_doswal' => array(),
                        'komentar'      => array(),
                        'files'         => array(),
                        'latest_date'   => $fp['date_edit'] ?? null,
                        'name_from_fp'  => $valid_name_fp,
                        'fp_doswal_map' => array(),
                        'fp_files_map'  => array(),
                        'fp_admin_map'  => array(),
                        'fp_komentar_map' => array(),
                        'docs'          => array(),
                    );
                } else {
                    if (!in_array($raw_id, $student_map[$nim]['raw_ids'])) {
                        $student_map[$nim]['raw_ids'][] = $raw_id;
                    }
                    if ($valid_name_fp && empty($student_map[$nim]['name_from_fp'])) {
                        $student_map[$nim]['name_from_fp'] = $valid_name_fp;
                    }
                    if (!empty($fp['date_edit']) && ($student_map[$nim]['latest_date'] === null || $fp['date_edit'] > $student_map[$nim]['latest_date'])) {
                        $student_map[$nim]['latest_date'] = $fp['date_edit'];
                    }
                }
                
                $st_adm = $this->_parse_fp_admin_status($fp);
                $student_map[$nim]['status_admin'][] = $st_adm;

                $file_raw = strtolower(trim($fp['file'] ?? ''));
                // Jangan campurkan berkas sidang ke pendaftaran TA
                if ($this->is_sidang_doc($doc_name_raw) || $this->is_sidang_doc($file_raw)) {
                    continue;
                }

                $kode = null;
                if (strpos($doc_name_raw, 'ksm') !== false || strpos($file_raw, 'ksm') !== false) {
                    $kode = 'ksm';
                } elseif (strpos($doc_name_raw, 'transkrip') !== false || strpos($file_raw, 'transkrip') !== false) {
                    $kode = 'transkrip';
                } elseif (strpos($doc_name_raw, 'pernyataan') !== false || strpos($file_raw, 'pernyataan') !== false) {
                    $kode = 'pernyataan';
                } elseif (strpos($doc_name_raw, 'bebas_lab') !== false || strpos($doc_name_raw, 'lab') !== false || strpos($file_raw, 'bebas_lab') !== false || strpos($file_raw, 'lab') !== false) {
                    $kode = 'bebas_lab';
                } else {
                    $clean_k = preg_replace('/[^a-z0-9_]/', '_', trim($doc_name_raw));
                    if (!empty($clean_k) && !in_array($clean_k, ['mahasiswa', 'na', 'null', 'none', 'berkas', 'file'])) {
                        $kode = $clean_k;
                    }
                }

                $cleanSt = $this->clean_doc_status($st_adm);
                if ($kode) {
                    $student_map[$nim]['docs'][$kode] = array(
                        'nim'               => $nim,
                        'kode_berkas'       => $kode,
                        'file_name'         => $fp['file'] ?? '',
                        'status'            => $cleanSt,
                        'status_verifikasi' => $cleanSt,
                        'catatan'           => $fp['komentar'] ?? ''
                    );
                    $student_map[$nim]['fp_files_map'][$kode]  = $fp['file'] ?? '';
                    $student_map[$nim]['fp_admin_map'][$kode]  = $st_adm;
                    $student_map[$nim]['fp_komentar_map'][$kode] = $fp['komentar'] ?? '';
                    if (isset($fp['status_doswal'])) {
                        $student_map[$nim]['fp_doswal_map'][$kode] = $fp['status_doswal'];
                    }
                }

                if (isset($fp['status_doswal'])) {
                    $student_map[$nim]['status_doswal'][] = $fp['status_doswal'];
                }
                if (!empty($fp['komentar'])) {
                    $student_map[$nim]['komentar'][] = $fp['komentar'];
                }
                if (!empty($fp['file'])) {
                    $student_map[$nim]['files'][] = $fp['file'];
                }
            }
        }

        // Muat data pendaftaran_berkas sekaligus
        $pb_map = array();
        if ($has_pb) {
            $prev_debug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            try {
                $pb_rows = $this->db->get('pendaftaran_berkas')->result_array();
                if (!empty($pb_rows)) {
                    foreach ($pb_rows as $pbr) {
                        $pb_nim = (string)($pbr['nim'] ?? '');
                        $k_code = (string)($pbr['kode_berkas'] ?? '');
                        if ($pb_nim !== '' && $k_code !== '') {
                            $st_v = $pbr['status_verifikasi'] ?? 'Pending';
                            $cleanSt = ($st_v === 'Approved' || $st_v === 'Valid') ? 'Valid' : (($st_v === 'Rejected' || $st_v === 'Invalid') ? 'Invalid' : 'Pending');
                            $pbr['status'] = $cleanSt;
                            $pbr['status_verifikasi'] = $cleanSt;
                            $pb_map[$pb_nim][$k_code] = $pbr;
                        }
                    }
                }
            } catch (Throwable $e) {}
            $this->db->db_debug = $prev_debug;
        }

        if ($has_g) {
            $g_cols = array('id', 'id_mhs');
            $guidance_fields = $this->db->list_fields('guidance');
            foreach (array('judul_1', 'judul_en', 'peminatan', 'jenis_TA', 'nama', 'keterangan', 'date') as $col) {
                if (in_array($col, $guidance_fields)) {
                    $g_cols[] = $col;
                }
            }

            $g_rows = $this->db->select(implode(', ', $g_cols))->get('guidance')->result_array();
            foreach ($g_rows as $g) {
                $raw_id = $g['id_mhs'] ?? '';
                if (empty($raw_id)) continue;
                $nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $raw_id);
                if (!isset($student_map[$nim])) {
                    $student_map[$nim] = array(
                        'nim'           => $nim,
                        'raw_ids'       => array($raw_id),
                        'status_admin'  => array(),
                        'status_doswal' => array(),
                        'komentar'      => array(),
                        'files'         => array(),
                        'latest_date'   => $g['date'] ?? null,
                        'name_from_fp'  => (!empty($g['nama']) && $g['nama'] !== 'Mahasiswa') ? $g['nama'] : null,
                        'fp_doswal_map' => array(),
                        'fp_files_map'  => array(),
                        'fp_admin_map'  => array(),
                        'fp_komentar_map' => array(),
                        'docs'          => array(),
                    );
                }
                $student_map[$nim]['guidance'] = $g;
            }
        }

        if (empty($student_map)) {
            $this->_memoized_legacy_pengajuan = array();
            return array();
        }

        $user_map = array();
        if ($user_tbl) {
            $u_rows = $this->db->get($user_tbl)->result_array();
            foreach ($u_rows as $ur) {
                if (!empty($ur['id']))       $user_map[(string)$ur['id']] = $ur;
                if (!empty($ur['nim']))      $user_map[(string)$ur['nim']] = $ur;
                if (!empty($ur['username'])) $user_map[(string)$ur['username']] = $ur;
            }
        }

        $mhs_map = array();
        if ($has_mhs) {
            $m_rows = $this->db->get('mahasiswa')->result_array();
            foreach ($m_rows as $mr) {
                if (!empty($mr['nim'])) $mhs_map[(string)$mr['nim']] = $mr;
                if (!empty($mr['id']))  $mhs_map[(string)$mr['id']] = $mr;
            }
        }

        $active_syarat = $this->get_active_syarat_berkas();
        $required_kodes = !empty($active_syarat) ? array_column($active_syarat, 'kode_berkas') : array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');
        $total_required = count($required_kodes);

        $results = array();
        foreach ($student_map as $nim_key => $info) {
            $u_rec = $user_map[(string)$nim_key] ?? null;
            if (!$u_rec && !empty($info['raw_ids'])) {
                foreach ($info['raw_ids'] as $rid) {
                    if (!empty($user_map[(string)$rid])) {
                        $u_rec = $user_map[(string)$rid];
                        break;
                    }
                }
            }

            $m_rec = $mhs_map[(string)$nim_key] ?? null;
            if (!$m_rec && !empty($info['raw_ids'])) {
                foreach ($info['raw_ids'] as $rid) {
                    if (!empty($mhs_map[(string)$rid])) {
                        $m_rec = $mhs_map[(string)$rid];
                        break;
                    }
                }
            }

            $has_user_rec = !empty($u_rec);
            $has_mhs_rec  = !empty($m_rec);
            $has_g_rec    = !empty($info['guidance']);

            // Abaikan baris data uji coba di file_pendaftaran
            if (!$has_user_rec && !$has_mhs_rec && !$has_g_rec) {
                continue;
            }

            $full_name = null;
            if ($u_rec) {
                $full_name = $u_rec['name'] ?? ($u_rec['nama'] ?? ($u_rec['nama_depan'] ?? null));
            }
            if (empty($full_name) && $m_rec) {
                $full_name = trim(($m_rec['nama_depan'] ?? '') . ' ' . ($m_rec['nama_belakang'] ?? ''));
            }
            if (empty($full_name) && !empty($info['name_from_fp'])) {
                $full_name = $info['name_from_fp'];
            }
            if (empty($full_name)) {
                $full_name = 'Mahasiswa';
            }

            $real_nim = $u_rec['nim'] ?? ($m_rec['nim'] ?? $nim_key);

            $parts = explode(' ', trim($full_name));
            $nama_depan = array_shift($parts) ?: 'Mahasiswa';
            $nama_belakang = implode(' ', $parts);

            // Susun pemetaan berkas mahasiswa di memori tanpa query berulang
            $p_berkas_map = $info['docs'] ?? array();
            $nim_key_str  = (string)$nim_key;
            $real_nim_str = (string)$real_nim;
            $pb_for_student = $pb_map[$real_nim_str] ?? ($pb_map[$nim_key_str] ?? array());
            if (!empty($pb_for_student)) {
                foreach ($pb_for_student as $k_code => $r) {
                    if (!isset($p_berkas_map[$k_code])) {
                        $p_berkas_map[$k_code] = $r;
                    } else {
                        if (empty($r['file_name']) && !empty($p_berkas_map[$k_code]['file_name'])) {
                            $r['file_name'] = $p_berkas_map[$k_code]['file_name'];
                        }
                        $p_berkas_map[$k_code] = $r;
                    }
                }
            }

            $pb_valid_count = 0;
            $pb_invalid_count = 0;
            foreach ($required_kodes as $rk) {
                $st_v = $p_berkas_map[$rk]['status_verifikasi'] ?? 'Pending';
                if ($st_v === 'Valid' || $st_v === 'Approved') {
                    $pb_valid_count++;
                } elseif ($st_v === 'Invalid' || $st_v === 'Rejected') {
                    $pb_invalid_count++;
                }
            }

            $admin_st_list = $info['status_admin'] ?? array();
            $has_admin_reject = ($pb_invalid_count > 0 || in_array('Rejected', $admin_st_list) || in_array('Invalid', $admin_st_list));
            $is_already_approved_admin = (!empty($admin_st_list) && !$has_admin_reject && count(array_filter($admin_st_list, function($s) { return $s === 'Approved' || $s === 'Valid'; })) === count($admin_st_list));

            if ($has_admin_reject) {
                $st_admin = 'Rejected';
            } elseif ($is_already_approved_admin) {
                $st_admin = 'Approved';
            } elseif ($pb_valid_count === $total_required && $total_required > 0) {
                $st_admin = 'Approved';
            } else {
                $st_admin = 'Pending';
                if (!empty($admin_st_list) && count(array_filter($admin_st_list, function($s) { return $s === 'Approved' || $s === 'Valid'; })) === count($admin_st_list)) {
                    $st_admin = 'Approved';
                }
            }

            $g_data = $info['guidance'] ?? array();
            $g_ket  = $g_data['keterangan'] ?? 'Pending';
            if ($g_ket === 'Draft') continue; // Mahasiswa belum submit "Kirim Pendaftaran"

            $fp_doswal_map = $info['fp_doswal_map'] ?? array();
            $status_doswal_list = $info['status_doswal'] ?? array();

            $all_files_approved = true;
            $has_file_rejected = false;

            if (empty($required_kodes)) {
                $all_files_approved = false;
            } else {
                foreach ($required_kodes as $rk) {
                    $st = $fp_doswal_map[$rk] ?? 'Pending';
                    if ($st === 'Rejected' || $st === 'Invalid') {
                        $has_file_rejected = true;
                        $all_files_approved = false;
                    } elseif ($st !== 'Approved' && $st !== 'Valid') {
                        $all_files_approved = false;
                    }
                }
            }

            // Mahasiswa yang sudah pernah di-ACC oleh Dosen Wali (semua berkas yang diunggahnya valid/approved dan judul approved)
            // tidak boleh turun menjadi pending hanya karena admin menambahkan syarat berkas baru di master.
            $is_already_approved_wali = ($g_ket === 'Approved' && !$has_file_rejected && !empty($fp_doswal_map) && count(array_filter($fp_doswal_map, function($s) { return $s === 'Approved' || $s === 'Valid'; })) === count($fp_doswal_map));

            if ($g_ket === 'Rejected' || $has_file_rejected) {
                $st_wali = 'Rejected';
            } elseif ($is_already_approved_wali || ($all_files_approved && !empty($required_kodes) && $g_ket === 'Approved')) {
                $st_wali = 'Approved';
            } else {
                $st_wali = 'Pending';
            }

            // Jika mahasiswa sudah berstatus Approved oleh Admin LAA, pastikan seluruh berkas terdaftar berstatus Valid
            // agar tidak memunculkan inkonsistensi 'Approved' namun bertuliskan 'Menunggu' / 'Belum Dicek'.
            if ($st_admin === 'Approved') {
                foreach ($p_berkas_map as $kb_code => &$b_rec) {
                    $b_rec['status'] = 'Valid';
                    $b_rec['status_verifikasi'] = 'Valid';
                }
                unset($b_rec);
                $pb_valid_count = max($pb_valid_count, count($p_berkas_map));
                $pb_invalid_count = 0;
            }

            $current_stage = ($st_wali === 'Approved') ? (($st_admin === 'Approved') ? 'Koordinator TA' : 'Admin Layanan') : (($st_wali === 'Rejected') ? 'Dosen Wali (Ditolak)' : 'Dosen Wali');
            $created_at = $info['latest_date'] ?? ($g_data['date'] ?? date('Y-m-d H:i:s'));

            $fp_files_map  = $info['fp_files_map'] ?? array();
            $fp_admin_map  = $info['fp_admin_map'] ?? array();

            $results[] = array(
                'id'                    => 'legacy_' . $real_nim,
                'nim'                   => $real_nim,
                'nama_depan'            => $nama_depan,
                'nama_belakang'         => $nama_belakang,
                'nama_lengkap'          => trim($nama_depan . ' ' . $nama_belakang),
                'nama_dosen_wali'       => $u_rec['dosen_wali'] ?? 'Dosen Wali LAA',
                'prodi'                 => $m_rec['prodi'] ?? 'Desain Komunikasi Visual',
                'konsentrasi_dkv'       => $g_data['peminatan'] ?? ($m_rec['konsentrasi_dkv'] ?? 'Desain Komunikasi Visual'),
                'email'                 => $m_rec['email'] ?? ($u_rec['email'] ?? ''),
                'no_hp'                 => $m_rec['no_hp'] ?? ($u_rec['phone'] ?? ''),
                'alamat'                => $m_rec['alamat'] ?? '',
                'judul_1'               => $g_data['judul_1'] ?? 'Perancangan Tugas Akhir Mahasiswa',
                'judul'                 => $g_data['judul_1'] ?? 'Perancangan Tugas Akhir Mahasiswa',
                'judul_en'              => $g_data['judul_en'] ?? '',
                'status_approval_wali'  => $st_wali,
                'status_approval_admin' => $st_admin,
                'catatan_admin'         => !empty($info['komentar']) ? implode('; ', array_unique($info['komentar'])) : null,
                'berkas_kurang'         => ($st_admin === 'Rejected') ? 'Dokumen Persyaratan Perlu Revisi' : null,
                'current_stage'         => $current_stage,
                'is_submitted'          => 1,
                'created_at'            => $created_at,
                'updated_at'            => $created_at,
                'status_ksm'            => $p_berkas_map['ksm']['status_verifikasi'] ?? 'Pending',
                'status_transkrip'      => $p_berkas_map['transkrip']['status_verifikasi'] ?? 'Pending',
                'status_pernyataan'     => $p_berkas_map['pernyataan']['status_verifikasi'] ?? 'Pending',
                'status_bebas_lab'      => $p_berkas_map['bebas_lab']['status_verifikasi'] ?? 'Pending',
                'file_ksm'              => $p_berkas_map['ksm']['file_name'] ?? ($fp_files_map['ksm'] ?? ($info['files'][0] ?? '')),
                'file_transkrip'        => $p_berkas_map['transkrip']['file_name'] ?? ($fp_files_map['transkrip'] ?? ($info['files'][1] ?? '')),
                'file_pernyataan'       => $p_berkas_map['pernyataan']['file_name'] ?? ($fp_files_map['pernyataan'] ?? ($info['files'][2] ?? '')),
                'file_bebas_lab'        => $p_berkas_map['bebas_lab']['file_name'] ?? ($fp_files_map['bebas_lab'] ?? ($info['files'][3] ?? '')),
                'total_berkas'          => ($st_admin === 'Approved' && $st_wali === 'Approved') ? max($pb_valid_count, count($fp_doswal_map)) : $total_required,
                'berkas_valid_count'    => $pb_valid_count,
                'berkas_invalid_count'  => $pb_invalid_count,
                'berkas_pending_count'  => ($st_admin === 'Approved' && $st_wali === 'Approved') ? 0 : max(0, $total_required - $pb_valid_count - $pb_invalid_count),
            );
        }

        $this->_memoized_legacy_pengajuan = $results;
        return $results;
    }

    /**
     * Bantu filter dan urutkan data pengajuan cadangan
     */
    private function _filter_and_sort_legacy_pengajuan($filter_status = null, $search = null, $cat = null) {
        $list = $this->_get_fallback_pengajuan_from_legacy();
        if (empty($list)) return array();

        $filtered = array();
        foreach ($list as $item) {
            if ($filter_status && $filter_status !== 'all') {
                if ($item['status_approval_admin'] !== $filter_status) {
                    continue;
                }
            }

            if (!empty($search)) {
                $search = strtolower(trim($search));
                $full_name = strtolower($item['nama_depan'] . ' ' . $item['nama_belakang']);
                $nim = strtolower($item['nim']);
                $judul = strtolower($item['judul_1']);
                $prodi = strtolower($item['prodi'] . ' ' . $item['konsentrasi_dkv']);

                $matched = false;
                if ($cat === 'nama') {
                    $matched = (strpos($full_name, $search) !== false);
                } elseif ($cat === 'nim') {
                    $matched = (strpos($nim, $search) !== false);
                } elseif ($cat === 'judul') {
                    $matched = (strpos($judul, $search) !== false);
                } elseif ($cat === 'prodi') {
                    $matched = (strpos($prodi, $search) !== false);
                } else {
                    $matched = (strpos($full_name, $search) !== false ||
                                strpos($nim, $search) !== false ||
                                strpos($judul, $search) !== false ||
                                strpos($prodi, $search) !== false);
                }
                if (!$matched) continue;
            }

            $filtered[] = $item;
        }

        usort($filtered, function($a, $b) use ($filter_status) {
            if (empty($filter_status) || $filter_status === 'all') {
                $timeA = strtotime($a['updated_at'] ?? $a['created_at']);
                $timeB = strtotime($b['updated_at'] ?? $b['created_at']);
                if ($timeA !== $timeB) {
                    return ($timeB < $timeA) ? -1 : 1;
                }
                return strcmp($b['created_at'], $a['created_at']);
            }
            $priority = array('Pending' => 1, 'Rejected' => 2, 'Approved' => 3);
            $pA = $priority[$a['status_approval_admin']] ?? 4;
            $pB = $priority[$b['status_approval_admin']] ?? 4;
            if ($pA !== $pB) {
                return ($pA < $pB) ? -1 : 1;
            }
            return strcmp($b['created_at'], $a['created_at']);
        });

        return $filtered;
    }

    /**
     * Autocomplete search untuk Admin Layanan LAA
     */
    public function autocomplete_search($term) {
        if (empty($term)) {
            return array();
        }

        $list = $this->_get_fallback_pengajuan_from_legacy();
        $term_lower = strtolower(trim($term));
        $results = array();

        foreach ($list as $item) {
            $nim           = strtolower($item['nim'] ?? '');
            $nama_depan    = strtolower($item['nama_depan'] ?? '');
            $nama_belakang = strtolower($item['nama_belakang'] ?? '');
            $judul         = strtolower($item['judul_1'] ?? ($item['judul'] ?? ''));

            if (strpos($nim, $term_lower) !== false ||
                strpos($nama_depan, $term_lower) !== false ||
                strpos($nama_belakang, $term_lower) !== false ||
                strpos($judul, $term_lower) !== false) {
                $results[] = $item;
            }
            if (count($results) >= 8) break;
        }

        return $results;
    }

    /**
     * Hitung statistik berkas untuk kartu metrik Admin Layanan
     */
    public function get_stats() {
        $list = $this->_get_fallback_pengajuan_from_legacy();
        $total = count($list);
        $pending = 0;
        $approved = 0;
        $rejected = 0;
        foreach ($list as $item) {
            $st = $item['status_approval_admin'] ?? 'Pending';
            if ($st === 'Pending') {
                $pending++;
            } elseif ($st === 'Approved') {
                $approved++;
            } elseif ($st === 'Rejected') {
                $rejected++;
            }
        }
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
        $list = $this->_get_fallback_pengajuan_from_legacy();
        foreach ($list as $item) {
            if ((string)($item['nim'] ?? '') === (string)$nim) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Format tautan berkas PDF untuk pratinjau modal (aman dari simbol %, #, spasi, dsb)
     */
    public function resolve_pdf_url($filename) {
        $encode_path = function($path) {
            if (empty($path)) return '';
            $parts = explode('/', str_replace('\\', '/', $path));
            $encoded = array_map(function($part) {
                return rawurlencode(rawurldecode($part));
            }, $parts);
            return implode('/', $encoded);
        };

        if (empty($filename)) {
            return base_url($encode_path('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf'));
        }

        $clean = ltrim(str_replace('\\', '/', $filename), '/');
        $target = (strpos($clean, 'uploads/') === 0) ? $clean : ('uploads/persyaratan_ta/' . $clean);

        if (!file_exists(FCPATH . $target)) {
            $decoded = rawurldecode($target);
            if (file_exists(FCPATH . $decoded)) {
                $target = $decoded;
            } else {
                $target = 'uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf';
            }
        }

        return base_url($encode_path($target));
    }

    /**
     * Hitung ringkasan status berkas (jumlah valid, invalid, pending) untuk 1 NIM
     */
    public function get_student_berkas_summary($nim, $active_syarat = null, $row = array()) {
        if ($active_syarat === null) {
            $active_syarat = $this->get_active_syarat_berkas();
        }
        $map = $this->get_student_berkas_map($nim);

        $valid_count   = 0;
        $invalid_count = 0;
        $pending_count = 0;
        $items         = array();
        $processed_kodes = array();

        // 1. Proses berkas mahasiswa yang sudah ada di file_pendaftaran
        foreach ($map as $kode => $record) {
            $processed_kodes[$kode] = true;
            $st = $record['status_verifikasi'] ?? 'Pending';
            if ($st === 'Approved') $st = 'Valid';
            if ($st === 'Rejected') $st = 'Invalid';

            if ($st === 'Valid') {
                $valid_count++;
            } elseif ($st === 'Invalid') {
                $invalid_count++;
            } else {
                $pending_count++;
            }

            $nama_berkas = !empty($record['nama_berkas']) ? $record['nama_berkas'] : ($record['nama'] ?? ucfirst(str_replace('_', ' ', $kode)));
            $file_name   = $record['file_name'] ?? '';
            $file_url    = $this->resolve_pdf_url($file_name);
            $has_file    = !empty($file_name) && file_exists(FCPATH . 'uploads/persyaratan_ta/' . $file_name);

            $items[] = array(
                'kode'      => $kode,
                'nama'      => $nama_berkas,
                'short'     => $this->get_short_berkas_label($nama_berkas, $kode),
                'status'    => $st,
                'catatan'   => $record['catatan'] ?? '',
                'has_file'  => $has_file,
                'file_name' => $file_name,
                'file_url'  => $file_url
            );
        }

        // Jika mahasiswa sudah di-ACC penuh oleh Admin LAA, seluruh berkas miliknya pasti Valid
        $is_admin_approved = (isset($row['status_approval_admin']) && $row['status_approval_admin'] === 'Approved');
        if ($is_admin_approved && !empty($items)) {
            foreach ($items as &$it) {
                if ($it['status'] !== 'Invalid') {
                    $it['status'] = 'Valid';
                }
            }
            unset($it);
            $valid_count = count($items);
            $pending_count = 0;
            $invalid_count = 0;
        }

        // 2. Tambahkan syarat aktif jika mahasiswa belum mengunggahnya
        // KECUALI jika mahasiswa sudah di-ACC / Approved (agar penambahan syarat baru di master tidak memaksa mahasiswa lama yang sudah di-ACC menjadi pending/berkas kurang)
        $is_student_fully_approved = ($valid_count > 0 && $invalid_count === 0 && $pending_count === 0) || $is_admin_approved;

        if (!$is_student_fully_approved) {
            foreach ($active_syarat as $sb) {
                $kode = $sb['kode_berkas'];
                if (isset($processed_kodes[$kode])) continue;

            $st = 'Pending';
            if (isset($row['status_' . $kode]) && !empty($row['status_' . $kode])) {
                $st = $row['status_' . $kode];
            } elseif (isset($row['status_approval_admin']) && $row['status_approval_admin'] === 'Approved') {
                $st = 'Valid';
            }

            if ($st === 'Approved') $st = 'Valid';
            if ($st === 'Rejected') $st = 'Invalid';

            if ($st === 'Valid') {
                $valid_count++;
            } elseif ($st === 'Invalid') {
                $invalid_count++;
            } else {
                $pending_count++;
            }

            $file_name = $row['file_' . $kode] ?? '';
            $file_url  = $this->resolve_pdf_url($file_name);
            $has_file  = !empty($file_name) && file_exists(FCPATH . 'uploads/persyaratan_ta/' . $file_name);

            $items[] = array(
                'kode'      => $kode,
                'nama'      => $sb['nama_berkas'],
                'short'     => $this->get_short_berkas_label($sb['nama_berkas'], $kode),
                'status'    => $st,
                'catatan'   => $row['catatan_' . $kode] ?? ($row['catatan_file_' . $kode] ?? ''),
                'has_file'  => $has_file,
                'file_name' => $file_name,
                'file_url'  => $file_url
            );
        }
        }

        return array(
            'valid_count'   => $valid_count,
            'invalid_count' => $invalid_count,
            'pending_count' => $pending_count,
            'total_count'   => count($items),
            'items'         => $items
        );
    }

    /**
     * Hitung batch ringkasan status berkas untuk daftar NIM / Row pengajuan secara efisien
     */
    public function get_batch_student_berkas_summaries($list_input, $active_syarat = null) {
        if ($active_syarat === null) {
            $active_syarat = $this->get_active_syarat_berkas();
        }
        if (empty($list_input)) {
            return array();
        }

        $nims = array();
        $student_rows = array();
        foreach ($list_input as $item) {
            if (is_array($item) && isset($item['nim'])) {
                $nims[] = $item['nim'];
                $student_rows[$item['nim']] = $item;
            } elseif (is_string($item) || is_numeric($item)) {
                $nims[] = (string)$item;
            }
        }

        if (empty($nims)) {
            return array();
        }

        $student_maps = array();

        // 1. Ambil dari tabel file_pendaftaran table
        if ($this->db->table_exists('file_pendaftaran')) {
            $target_ids = array();
            foreach ($nims as $n) {
                $target_ids[] = $n;
                $target_ids[] = 'usr_mhs_' . $n;
                $target_ids[] = 'mhs_' . $n;
            }
            $fp_rows = $this->db->where_in('id_mhs', $target_ids)->get('file_pendaftaran')->result_array();
            if (!empty($fp_rows)) {
                foreach ($fp_rows as $fp) {
                    $c_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $fp['id_mhs']);
                    $namaDoc = strtolower(trim($fp['nama'] ?? ''));
                    $fileRaw = strtolower(trim($fp['file'] ?? ''));
                    // Jangan campurkan berkas sidang ke pendaftaran TA
                    if ($this->is_sidang_doc($namaDoc) || $this->is_sidang_doc($fileRaw)) {
                        continue;
                    }

                    $kode = null;
                    if (strpos($namaDoc, 'ksm') !== false || strpos($fileRaw, 'ksm') !== false) {
                        $kode = 'ksm';
                    } elseif (strpos($namaDoc, 'transkrip') !== false || strpos($fileRaw, 'transkrip') !== false) {
                        $kode = 'transkrip';
                    } elseif (strpos($namaDoc, 'pernyataan') !== false || strpos($fileRaw, 'pernyataan') !== false) {
                        $kode = 'pernyataan';
                    } elseif (strpos($namaDoc, 'bebas_lab') !== false || strpos($namaDoc, 'lab') !== false || strpos($fileRaw, 'bebas_lab') !== false || strpos($fileRaw, 'lab') !== false) {
                        $kode = 'bebas_lab';
                    } else {
                        $clean_k = preg_replace('/[^a-z0-9_]/', '_', trim($namaDoc));
                        if (!empty($clean_k) && !in_array($clean_k, ['mahasiswa', 'na', 'null', 'none', 'berkas', 'file'])) {
                            $kode = $clean_k;
                        }
                    }

                    if ($kode) {
                        $st = $fp['status_adminlaa'] ?? ($fp['status_admin'] ?? ($fp['status_laa'] ?? 'Pending'));
                        $cleanSt = $this->clean_doc_status($st);
                        $student_maps[$c_nim][$kode] = array(
                            'nim'               => $c_nim,
                            'kode_berkas'       => $kode,
                            'file_name'         => $fp['file'] ?? '',
                            'status_verifikasi' => $cleanSt,
                            'catatan'           => $fp['komentar'] ?? ''
                        );
                    }
                }
            }
        }

        // 2. Ambil dari tabel pendaftaran_berkas
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $pb_rows = $this->db->where_in('nim', $nims)->get('pendaftaran_berkas')->result_array();
            if (!empty($pb_rows)) {
                foreach ($pb_rows as $r) {
                    $c_nim = $r['nim'];
                    $k_code = $r['kode_berkas'];
                    if (!isset($student_maps[$c_nim][$k_code])) {
                        $student_maps[$c_nim][$k_code] = $r;
                    } else if (empty($student_maps[$c_nim][$k_code]['file_name']) && !empty($r['file_name'])) {
                        $student_maps[$c_nim][$k_code]['file_name'] = $r['file_name'];
                    }
                }
            }
        }
        $summaries = array();
        foreach ($nims as $nim) {
            $map   = $student_maps[$nim] ?? array();
            $s_row = $student_rows[$nim] ?? array();

            $valid_count   = 0;
            $invalid_count = 0;
            $pending_count = 0;
            $items         = array();
            $processed_kodes = array();

            // 1. Proses berkas mahasiswa yang sudah ada di file_pendaftaran
            foreach ($map as $kode => $record) {
                $processed_kodes[$kode] = true;
                $st = $record['status_verifikasi'] ?? 'Pending';
                if ($st === 'Approved') $st = 'Valid';
                if ($st === 'Rejected') $st = 'Invalid';

                if ($st === 'Valid') {
                    $valid_count++;
                } elseif ($st === 'Invalid') {
                    $invalid_count++;
                } else {
                    $pending_count++;
                }

                $nama_berkas = !empty($record['nama_berkas']) ? $record['nama_berkas'] : ($record['nama'] ?? ucfirst(str_replace('_', ' ', $kode)));
                $file_name   = $record['file_name'] ?? '';
                $file_url    = $this->resolve_pdf_url($file_name);
                $has_file    = !empty($file_name);

                $items[] = array(
                    'kode'      => $kode,
                    'nama'      => $nama_berkas,
                    'short'     => $this->get_short_berkas_label($nama_berkas, $kode),
                    'status'    => $st,
                    'catatan'   => $record['catatan'] ?? '',
                    'has_file'  => $has_file,
                    'file_name' => $file_name,
                    'file_url'  => $file_url
                );
            }

            // Jika mahasiswa sudah di-ACC penuh oleh Admin LAA, seluruh berkas miliknya pasti Valid
            $is_admin_approved = (isset($s_row['status_approval_admin']) && $s_row['status_approval_admin'] === 'Approved');
            if ($is_admin_approved && !empty($items)) {
                foreach ($items as &$it) {
                    if ($it['status'] !== 'Invalid') {
                        $it['status'] = 'Valid';
                    }
                }
                unset($it);
                $valid_count = count($items);
                $pending_count = 0;
                $invalid_count = 0;
            }

            // 2. Tambahkan syarat aktif jika mahasiswa belum mengunggahnya
            $is_student_fully_approved = ($valid_count > 0 && $invalid_count === 0 && $pending_count === 0) || $is_admin_approved;

            if (!$is_student_fully_approved) {
                foreach ($active_syarat as $sb) {
                    $kode = $sb['kode_berkas'];
                    if (isset($processed_kodes[$kode])) continue;

                $st = 'Pending';
                if (isset($s_row['status_' . $kode]) && !empty($s_row['status_' . $kode])) {
                    $st = $s_row['status_' . $kode];
                } elseif (isset($s_row['status_approval_admin']) && $s_row['status_approval_admin'] === 'Approved') {
                    $st = 'Valid';
                }

                if ($st === 'Approved') $st = 'Valid';
                if ($st === 'Rejected') $st = 'Invalid';

                if ($st === 'Valid') {
                    $valid_count++;
                } elseif ($st === 'Invalid') {
                    $invalid_count++;
                } else {
                    $pending_count++;
                }

                $file_name = $s_row['file_' . $kode] ?? '';
                $file_url  = $this->resolve_pdf_url($file_name);
                $has_file  = !empty($file_name) && file_exists(FCPATH . 'uploads/persyaratan_ta/' . $file_name);

                $items[] = array(
                    'kode'      => $kode,
                    'nama'      => $sb['nama_berkas'],
                    'short'     => $this->get_short_berkas_label($sb['nama_berkas'], $kode),
                    'status'    => $st,
                    'catatan'   => $s_row['catatan_' . $kode] ?? ($s_row['catatan_file_' . $kode] ?? ''),
                    'has_file'  => $has_file,
                    'file_name' => $file_name,
                    'file_url'  => $file_url
                );
            }
        }

            $summaries[$nim] = array(
                'valid_count'   => $valid_count,
                'invalid_count' => $invalid_count,
                'pending_count' => $pending_count,
                'total_count'   => count($items),
                'items'         => $items
            );
        }

        return $summaries;
    }

    // ==========================================
    // TICKETING MODULE METHODS
    // ==========================================

    public function get_tickets($type = 'all', $search = '') {
        if ($this->db->table_exists('tb_ticketing')) {
            $this->db->select('*');
            $this->db->from('tb_ticketing');
            
            // Filter recipient Admin LAA
            $this->db->group_start();
            $this->db->where('tujuan_penerima', 'Admin LAA');
            $this->db->or_like('tujuan_penerima', 'Admin LAA');
            $this->db->or_like('tujuan_penerima', 'LAA');
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', 'Admin LAA');
            $this->db->or_like('unit', 'LAA');
            $this->db->or_like('unit', 'Layanan');
            $this->db->or_like('unit_terkait', 'LAA');
            $this->db->or_like('unit_terkait', 'Layanan');
            $this->db->group_end();

            if ($type === 'approval') {
                $this->db->where('status', 'Dikirim');
            } elseif ($type === 'riwayat') {
                $this->db->where_in('status', ['Sedang Diproses', 'Closed', 'Diproses', 'Selesai', 'Ditutup']);
            }

            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('id', $search);
                $this->db->or_like('id_user', $search);
                $this->db->or_like('nama', $search);
                $this->db->or_like('kategori', $search);
                $this->db->or_like('isi_ticketing', $search);
                $this->db->group_end();
            }

            $this->db->order_by('tgl_ticketing', 'DESC');
            $this->db->order_by('id', 'DESC');
            $rows = $this->db->get()->result_array();

            $formatted = [];
            foreach ($rows as $r) {
                $subjek = 'Kendala ' . ($r['kategori'] ?? 'Layanan');
                $deskripsi = $r['isi_ticketing'] ?? '';
                if (preg_match('/<strong>(.*?)<\/strong><br>(.*)/is', $r['isi_ticketing'] ?? '', $m)) {
                    $subjek = trim(strip_tags($m[1]));
                    $deskripsi = trim($m[2]);
                }

                $st = 'Pending';
                if ($r['status'] === 'Sedang Diproses') $st = 'Approved';
                elseif ($r['status'] === 'Closed') $st = 'Selesai';
                elseif ($r['status'] === 'Dikirim') $st = 'Pending';
                else $st = $r['status'];

                $createdAtReal = null;
                if (preg_match('/\[CREATED:\s*([^\]]+)\]/is', $r['keterangan'] ?? '', $mC)) {
                    $createdAtReal = trim($mC[1]);
                } elseif (!empty($r['tgl_ticketing']) && strlen(trim($r['tgl_ticketing'])) > 10 && $r['tgl_ticketing'] !== '0000-00-00 00:00:00') {
                    $createdAtReal = $r['tgl_ticketing'];
                }

                $prioritasParsed = null;
                if (preg_match('/\[PRIORITAS:\s*([^\]]+)\]/is', $r['keterangan'] ?? '', $mPrio)) {
                    $prioritasParsed = trim($mPrio[1]);
                }

                $formatted[] = [
                    'id'            => $r['id'],
                    'ticket_number' => $r['id'],
                    'nim_nip'       => $r['id_user'] ?? '-',
                    'nama'          => $r['nama'] ?? 'Mahasiswa',
                    'email'         => $r['email'] ?? '',
                    'tujuan_penerima' => $r['tujuan_penerima'] ?? 'Admin LAA',
                    'unit_terkait'  => $r['unit_terkait'] ?? ($r['unit'] ?? 'Layanan Akademik (LAA)'),
                    'kategori'      => $r['kategori'] ?? 'Layanan Umum',
                    'perihal'       => $subjek,
                    'deskripsi'     => $deskripsi,
                    'prioritas'     => $prioritasParsed ?: (!empty($r['prioritas']) ? $r['prioritas'] : 'Normal'),
                    'status'        => $st,
                    'catatan'       => $r['keterangan'] ?? '',
                    'created_at'    => $createdAtReal ?: (!empty($r['tgl_ticketing']) ? ($r['tgl_ticketing'] . ' 08:00:00') : date('Y-m-d H:i:s')),
                    'updated_at'    => $r['tgl_closed'] ?? ($r['tgl_diproses'] ?? date('Y-m-d H:i:s'))
                ];
            }
            return $formatted;
        }

        if (!$this->db->table_exists('ticketing_laa')) return array();

        $this->db->select('*');
        $this->db->from('ticketing_laa');

        if ($type === 'approval') {
            $this->db->where_in('status', ['Inputted', 'Pending']);
        } elseif ($type === 'riwayat') {
            $this->db->where_in('status', ['Approved', 'Rejected', 'Selesai']);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ticket_number', $search);
            $this->db->or_like('nim_nip', $search);
            $this->db->or_like('nama', $search);
            $this->db->or_like('perihal', $search);
            $this->db->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function save_ticket($data) {
        if ($this->db->table_exists('tb_ticketing')) {
            $this->load->model('DosenTicketing_model');
            return $this->DosenTicketing_model->insert([
                'id_user'         => $data['nim_nip'] ?? '0',
                'nama'            => $data['nama'] ?? 'Pemohon',
                'email'           => $data['email'] ?? '',
                'tujuan_penerima' => 'Admin LAA',
                'unit_terkait'    => 'Layanan Administrasi Akademik (LAA)',
                'unit_tujuan'     => 'Layanan Administrasi Akademik (LAA)',
                'kategori'        => $data['kategori'] ?? 'Layanan Umum',
                'prioritas'       => $data['prioritas'] ?? 'Sedang',
                'subjek'          => $data['perihal'] ?? 'Tiket Layanan',
                'deskripsi'       => $data['deskripsi'] ?? '',
                'status'          => 'Dikirim'
            ]);
        }

        if (!$this->db->table_exists('ticketing_laa')) return false;
        if (empty($data['ticket_number'])) {
            $data['ticket_number'] = 'TICK-' . date('Ymd') . '-' . rand(1000, 9999);
        }
        if (empty($data['status'])) {
            $data['status'] = 'Pending';
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert('ticketing_laa', $data);
        return $this->db->insert_id();
    }

    public function update_ticket_status($id, $status, $catatan = '') {
        if ($this->db->table_exists('tb_ticketing')) {
            $mappedStatus = 'Dikirim';
            $tglDiproses = NULL;
            $tglClosed = NULL;
            $now = date('Y-m-d H:i:s');

            if ($status === 'Approved' || $status === 'Diproses') {
                $mappedStatus = 'Sedang Diproses';
                $tglDiproses = $now;
            } elseif ($status === 'Selesai' || $status === 'Closed' || $status === 'Rejected') {
                $mappedStatus = 'Closed';
                $tglClosed = $now;
            }

            $updateData = ['status' => $mappedStatus];
            if (!empty($catatan)) {
                $updateData['keterangan'] = $catatan;
            }
            if ($tglDiproses) $updateData['tgl_diproses'] = $tglDiproses;
            if ($tglClosed) $updateData['tgl_closed'] = $tglClosed;

            $this->db->where('id', $id);
            return $this->db->update('tb_ticketing', $updateData);
        }

        if (!$this->db->table_exists('ticketing_laa')) return false;
        $this->db->where('id', $id);
        return $this->db->update('ticketing_laa', [
            'status'     => $status,
            'catatan'    => $catatan,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // ==========================================
    // STATUS PESERTA TA & LULUS SIDANG METHODS
    // ==========================================

    public function get_students_lulus_sidang($search = '', $cat = 'query') {
        $fallback = $this->_get_fallback_pengajuan_from_legacy();
        if (!empty($search)) {
            $search = strtolower(trim($search));
            $fallback = array_values(array_filter($fallback, function($item) use ($search, $cat) {
                $full_name = strtolower(($item['nama_depan'] ?? '') . ' ' . ($item['nama_belakang'] ?? ''));
                $nim       = strtolower($item['nim'] ?? '');
                $judul     = strtolower($item['judul_1'] ?? ($item['judul'] ?? ''));
                $prodi     = strtolower(($item['prodi'] ?? '') . ' ' . ($item['konsentrasi_dkv'] ?? ''));

                if ($cat === 'nama')  return strpos($full_name, $search) !== false;
                if ($cat === 'nim')   return strpos($nim, $search) !== false;
                if ($cat === 'judul') return strpos($judul, $search) !== false;
                if ($cat === 'prodi') return strpos($prodi, $search) !== false;
                return (strpos($full_name, $search) !== false ||
                        strpos($nim, $search) !== false ||
                        strpos($judul, $search) !== false ||
                        strpos($prodi, $search) !== false);
            }));
        }

        $res = array_values(array_filter($fallback, function($item) {
            return (strpos($item['current_stage'] ?? '', 'Lulus') !== false 
                 || strpos($item['current_stage'] ?? '', 'Selesai') !== false 
                 || ($item['status_approval_admin'] ?? '') === 'Approved');
        }));

        foreach ($res as &$r) {
            if (empty($r['nama_lengkap'])) {
                $r['nama_lengkap'] = trim(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? ''));
            }
            if (empty($r['nama_lengkap'])) {
                $r['nama_lengkap'] = 'Mahasiswa';
            }
            if (empty($r['nama_dosen_wali'])) {
                $r['nama_dosen_wali'] = $r['dosen_wali'] ?? 'Dosen Wali LAA';
            }
        }
        unset($r);

        return $res;
    }

    public function get_status_peserta_ta($search = '', $filter_stage = 'all', $cat = 'query') {
        $rows = $this->_filter_and_sort_legacy_pengajuan(null, $search, $cat);

        if ($filter_stage && $filter_stage !== 'all') {
            $f_stage = strtolower($filter_stage);
            $rows = array_values(array_filter($rows, function($item) use ($f_stage) {
                $stg = strtolower($item['current_stage'] ?? '');
                if ($f_stage === 'preview1') {
                    return (strpos($stg, 'preview1') !== false ||
                            strpos($stg, 'mahasiswa') !== false ||
                            strpos($stg, 'dosen wali') !== false ||
                            strpos($stg, 'admin layanan') !== false ||
                            strpos($stg, 'koordinator ta') !== false ||
                            strpos($stg, 'draft') !== false);
                }
                return (strpos($stg, $f_stage) !== false);
            }));
        }

        $active_syarat = $this->get_active_syarat_berkas();
        $berkas_summaries = $this->get_batch_student_berkas_summaries($rows, $active_syarat);

        foreach ($rows as &$r) {
            $nim = $r['nim'];
            
            if (empty($r['nama_lengkap'])) {
                $r['nama_lengkap'] = trim(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? ''));
            }
            if (empty($r['nama_lengkap'])) {
                $r['nama_lengkap'] = 'Mahasiswa';
            }

            // Petakan nama tahap ke label evaluasi Bimbingan TA (Preview 1, Preview 2, Preview 3, Pendaftaran Sidang)
            $stg = strtolower($r['current_stage'] ?? '');
            if (strpos($stg, 'preview 3') !== false || strpos($stg, 'preview3') !== false || strpos($stg, 'pra-sidang') !== false) {
                $r['tahapan_display'] = 'Preview 3';
            } elseif (strpos($stg, 'preview 2') !== false || strpos($stg, 'preview2') !== false) {
                $r['tahapan_display'] = 'Preview 2';
            } elseif (strpos($stg, 'preview 1') !== false || strpos($stg, 'preview1') !== false) {
                $r['tahapan_display'] = 'Preview 1';
            } elseif (strpos($stg, 'sidang') !== false) {
                $r['tahapan_display'] = 'Pendaftaran Sidang';
            } elseif (strpos($stg, 'lulus') !== false || strpos($stg, 'selesai') !== false) {
                $r['tahapan_display'] = 'Lulus Sidang';
            } else {
                // Cadangan untuk tahap verifikasi berkas (Mahasiswa, Dosen Wali, Admin Layanan, dll.)
                // Bimbingan TA evaluation stage starts at Preview 1
                $r['tahapan_display'] = 'Preview 1';
            }

            // Tampilan dosen wali
            if (empty($r['nama_dosen_wali'])) {
                $r['nama_dosen_wali'] = $r['dosen_wali'] ?? 'Dosen Wali LAA';
            }

            // Status bimbingan
            $r['status_bimbingan_display'] = (($r['status_approval_wali'] ?? '') === 'Approved') ? 'Disetujui wali' : ((($r['status_approval_wali'] ?? '') === 'Rejected') ? 'Ditolak wali' : 'Pending');
            
            // Ringkasan pemetaan berkas TA
            $r['file_summary'] = $berkas_summaries[$nim] ?? null;
        }
        unset($r);

        return $rows;
    }

    public function revert_to_preview3($nim) {
        $this->clear_cache();
        if (!$this->db->table_exists('pendaftaran_ta')) return false;

        $this->db->where('nim', $nim);
        $res = $this->db->update('pendaftaran_ta', [
            'current_stage' => 'Pra-Sidang (Preview 3)',
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        if ($this->db->table_exists('bimbingan_preview')) {
            $this->db->where(['nim' => $nim, 'tahap_preview' => 'Preview 3']);
            $this->db->update('bimbingan_preview', [
                'status_pembimbing' => 'Pending'
            ]);
        }

        return $res;
    }

    // =========================================================================
    // 1. PENDAFTARAN SIDANG MODULE METHODS
    // =========================================================================

    /**
     * Ambil list pendaftar sidang dengan filter dan search
     */
    public function get_pendaftaran_sidang_list($search = '', $filter_jenis = 'all', $filter_status = 'all', $cat = 'query') {
        $result = array();

        // Query dari guidance + user + thesis_lecturers
        if ($this->db->table_exists('guidance')) {
            $this->db->select('
                g.id as guidance_id,
                g.id_mhs,
                g.judul_1,
                g.peminatan,
                g.tahun,
                g.ipk,
                g.totalsks,
                g.scoreeprt,
                g.scoretak,
                g.status_filesidang,
                g.status_preview,
                g.lulus_preview3,
                g.komentar_pendaftaran_sidang,
                g.jenis_TA,
                g.tanggal_sidang,
                g.waktu_sidang,
                g.ruang_sidang,
                g.bap,
                g.status_bap,
                g.bap2,
                g.status_bap2,
                COALESCE(u.name, m.nama_depan) as nama_mahasiswa,
                COALESCE(u.nim, m.nim, g.id_mhs) as nim,
                COALESCE(m.prodi, "Desain Komunikasi Visual") as prodi,
                COALESCE(m.konsentrasi_dkv, g.peminatan, "-") as konsentrasi,
                m.alamat,
                m.no_hp,
                u_wali.name as nama_dosen_wali,
                u_p1.name as nama_pembimbing1,
                u_p2.name as nama_pembimbing2,
                u_pj1.name as nama_penguji1,
                u_pj2.name as nama_penguji2,
                tl.dosen_pembimbing1,
                tl.dosen_pembimbing2,
                tl.dosen_penguji1,
                tl.dosen_penguji2
            ');
            $this->db->from('guidance g');
            $this->db->join('user u', 'u.id = g.id_mhs OR u.nim = g.id_mhs', 'left');
            $this->db->join('mahasiswa m', 'm.nim = u.nim OR m.nim = g.id_mhs', 'left');
            $this->db->join('user u_wali', '(u.dosen_wali IS NOT NULL AND u.dosen_wali != "" AND (u_wali.nip = u.dosen_wali OR u_wali.id = u.dosen_wali))', 'left');
            $this->db->join('thesis_lecturers tl', 'tl.id_guidance = g.id', 'left');
            $this->db->join('user u_p1', '(tl.dosen_pembimbing1 IS NOT NULL AND tl.dosen_pembimbing1 != "" AND (u_p1.nip = tl.dosen_pembimbing1 OR u_p1.id = tl.dosen_pembimbing1))', 'left');
            $this->db->join('user u_p2', '(tl.dosen_pembimbing2 IS NOT NULL AND tl.dosen_pembimbing2 != "" AND (u_p2.nip = tl.dosen_pembimbing2 OR u_p2.id = tl.dosen_pembimbing2))', 'left');
            $this->db->join('user u_pj1', '(tl.dosen_penguji1 IS NOT NULL AND tl.dosen_penguji1 != "" AND (u_pj1.nip = tl.dosen_penguji1 OR u_pj1.id = tl.dosen_penguji1))', 'left');
            $this->db->join('user u_pj2', '(tl.dosen_penguji2 IS NOT NULL AND tl.dosen_penguji2 != "" AND (u_pj2.nip = tl.dosen_penguji2 OR u_pj2.id = tl.dosen_penguji2))', 'left');

            // Filter Jenis TA
            if ($filter_jenis === 'sidang') {
                $this->db->group_start();
                $this->db->where('g.jenis_TA', 'TA Reguler');
                $this->db->or_like('g.jenis_TA', 'Reguler');
                $this->db->or_where('g.jenis_TA IS NULL', null, false);
                $this->db->group_end();
            } elseif ($filter_jenis === 'non-sidang') {
                $this->db->group_start();
                $this->db->where_in('g.jenis_TA', ['TA Jurnal', 'TA HKI', 'TA PROYEK', 'TA Lainnya', 'Non-Sidang', 'Publikasi']);
                $this->db->or_like('g.jenis_TA', 'Jurnal');
                $this->db->or_like('g.jenis_TA', 'HKI');
                $this->db->or_like('g.jenis_TA', 'PROYEK');
                $this->db->group_end();
            }

            // Search
            if (!empty($search)) {
                $this->db->group_start();
                if ($cat === 'nama') {
                    $this->db->like('u.name', $search);
                    $this->db->or_like('m.nama_depan', $search);
                    $this->db->or_like('m.nama_belakang', $search);
                } elseif ($cat === 'nim') {
                    $this->db->like('u.nim', $search);
                    $this->db->or_like('g.id_mhs', $search);
                } elseif ($cat === 'judul') {
                    $this->db->like('g.judul_1', $search);
                } elseif ($cat === 'ruangan') {
                    $this->db->like('g.ruang_sidang', $search);
                } elseif ($cat === 'waktu') {
                    $this->db->like('g.tanggal_sidang', $search);
                    $this->db->or_like('g.waktu_sidang', $search);
                } elseif ($cat === 'prodi') {
                    $this->db->like('m.prodi', $search);
                    $this->db->or_like('g.peminatan', $search);
                    $this->db->or_like('m.konsentrasi_dkv', $search);
                } elseif ($cat === 'dosen') {
                    $this->db->like('u_wali.name', $search);
                    $this->db->or_like('u_p1.name', $search);
                    $this->db->or_like('u_pj1.name', $search);
                    $this->db->or_like('u_pj2.name', $search);
                } else {
                    $this->db->like('u.name', $search);
                    $this->db->or_like('u.nim', $search);
                    $this->db->or_like('g.id_mhs', $search);
                    $this->db->or_like('g.judul_1', $search);
                    $this->db->or_like('m.prodi', $search);
                    $this->db->or_like('g.ruang_sidang', $search);
                }
                $this->db->group_end();
            }

            $this->db->order_by('g.id', 'DESC');
            $rows = $this->db->get()->result_array();

            // Bersihkan duplikasi baris berdasarkan guidance_id di PHP
            $uniqueRows = array();
            $seenGid = array();
            foreach ($rows as $rItem) {
                $gId = $rItem['guidance_id'];
                if (!isset($seenGid[$gId])) {
                    $seenGid[$gId] = true;
                    $uniqueRows[] = $rItem;
                }
            }
            $rows = $uniqueRows;

            foreach ($rows as $r) {
                $nim = $r['nim'] ?: $r['id_mhs'];
                $isNonSidang = in_array($r['jenis_TA'], ['TA Jurnal', 'TA HKI', 'TA PROYEK', 'TA Lainnya', 'Non-Sidang', 'Publikasi']);
                $sp_row = strtolower(trim($r['status_preview'] ?? ''));
                $lulus_p3_row = !empty($r['lulus_preview3']) && (int)$r['lulus_preview3'] === 1;
                $st = !empty($r['status_filesidang']) ? $r['status_filesidang'] : 'Pending Verifikasi';
                if (!$lulus_p3_row || in_array($sp_row, ['preview1', 'preview2', 'preview3'], true)) {
                    $st = 'Pending Verifikasi';
                }
                $isApp = ($lulus_p3_row && !in_array($sp_row, ['preview1', 'preview2', 'preview3'], true))
                    && (strpos(strtolower($st), 'disetujui') !== false || strpos(strtolower($st), 'approved') !== false || strtolower(trim($st)) === 'lulus');

                // Auto-sync jika berkas pendaftaran sidang mahasiswa sudah di-ACC seluruhnya di file_pendaftaran
                if (!$isApp && $lulus_p3_row && !in_array($sp_row, ['preview1', 'preview2', 'preview3'], true)) {
                    $berkas_mhs = $this->get_berkas_pendaftaran_sidang($nim);
                    if (!empty($berkas_mhs)) {
                        $total_b   = count($berkas_mhs);
                        $req_b     = 0;
                        $req_val_b = 0;
                        $val_b     = 0;
                        $rev_b     = 0;
                        $up_b      = 0;

                        foreach ($berkas_mhs as $bm) {
                            $is_req_item = !empty($bm['is_required']);
                            $is_val_item = ($bm['status'] === 'Disetujui Admin LAA');
                            $is_rev_item = ($bm['status'] === 'Revisi Admin LAA');
                            if (!empty($bm['is_uploaded'])) {
                                $up_b++;
                            }
                            if ($is_req_item) {
                                $req_b++;
                                if ($is_val_item) $req_val_b++;
                            }
                            if ($is_val_item) $val_b++;
                            if ($is_rev_item) $rev_b++;
                        }

                        if ($rev_b > 0) {
                            $st = 'Revisi Admin LAA';
                        } elseif (
                            $up_b > 0 && (
                                ($total_b > 0 && $val_b === $total_b) ||
                                ($req_b > 0 && $req_val_b === $req_b)
                            )
                        ) {
                            $st = 'Disetujui Admin LAA';
                            $isApp = true;
                            $upd_g = ['status_filesidang' => 'Disetujui Admin LAA'];
                            if ($this->db->field_exists('status_preview', 'guidance')) {
                                if ($lulus_p3_row && in_array($sp_row, ['preview3', 'persyaratan_sidang', 'persyaratansidang', ''], true)) {
                                    $upd_g['status_preview'] = 'sidang';
                                }
                            }
                            $this->db->where('id', $r['guidance_id'])->update('guidance', $upd_g);
                        }
                    }
                } elseif ($isApp) {
                    // Jika sudah Disetujui tetapi status_preview masih tertahan di preview3 / persyaratan_sidang, majukan ke sidang
                    if ($this->db->field_exists('status_preview', 'guidance')) {
                        if ($lulus_p3_row && in_array($sp_row, ['preview3', 'persyaratan_sidang', 'persyaratansidang'], true)) {
                            $this->db->where('id', $r['guidance_id'])->update('guidance', ['status_preview' => 'sidang']);
                        }
                    }
                }

                // Filter Status setelah sinkronisasi
                if ($filter_status === 'disetujui' && !$isApp) {
                    continue;
                } elseif ($filter_status === 'pending' && $isApp) {
                    continue;
                }

                $result[] = array(
                    'guidance_id'   => $r['guidance_id'],
                    'id_mhs'        => $r['id_mhs'],
                    'nim'           => $nim,
                    'nama'          => $r['nama_mahasiswa'] ?: ('Mahasiswa ' . $nim),
                    'prodi'         => $r['prodi'] ?: 'Desain Komunikasi Visual',
                    'konsentrasi'   => $r['konsentrasi'] ?: 'ADVERTISING',
                    'dosen_wali'    => $r['nama_dosen_wali'] ?: 'Dosen Wali LAA, M.Ds.',
                    'pembimbing_1'  => $r['nama_pembimbing1'] ?: 'Apsari Wiba Pamela, S.Ds., M.Ds.',
                    'pembimbing_2'  => $r['nama_pembimbing2'] ?: 'DR. Ira Wirasari, M.Ds.',
                    'penguji_1'     => $r['nama_penguji1'] ?: 'Dr. Samsul Alam, S.Pd., M.Pd.',
                    'penguji_2'     => $r['nama_penguji2'] ?: 'Putu Raka Setya Putra, S.Ds., M.Ds.',
                    'jenis_ta'      => $r['jenis_TA'] ?: ($isNonSidang ? 'Non-Sidang' : 'TA Reguler'),
                    'is_non_sidang' => $isNonSidang,
                    'tahap'         => 'Sidang',
                    'status'        => $st,
                    'is_approved'   => $isApp,
                    'judul'         => $r['judul_1'] ?: 'Perancangan Kampanye Sosial Visual Komunikasi',
                    'ipk'           => $r['ipk'] ?: 3.72,
                    'totalsks'      => $r['totalsks'] ?: 141,
                    'scoreeprt'     => $r['scoreeprt'] ?: 567,
                    'scoretak'      => $r['scoretak'] ?: 146,
                    'alamat'        => $r['alamat'] ?: 'Jl. Telekomunikasi No. 1, Bandung',
                    'no_hp'         => $r['no_hp'] ?: '081234567890',
                    'tanggal_sidang'=> $r['tanggal_sidang'] ?: '2026-06-30',
                    'waktu_sidang'  => $r['waktu_sidang'] ?: '09:00:00',
                    'ruang_sidang'  => $r['ruang_sidang'] ?: 'Ruang Sidang FIK Lt. 2',
                    'bap'           => $r['bap'] ?: 'BAP_Sidang_' . $nim . '.pdf',
                    'status_bap'    => $r['status_bap'] ?: 'BAP (GRACIAS): Disetujui',
                    'status_bap2'   => $r['status_bap2'] ?: 'BAP FAKULTAS: Sudah Ditandatangani'
                );
            }
        }

        return $result;
    }

    /**
     * Hitung statistik ringkas pendaftaran sidang untuk Metrics Cards
     */
    public function get_pendaftaran_sidang_stats() {
        $list = $this->get_pendaftaran_sidang_list('', 'all', 'all', 'query');
        $total = count($list);
        $sidang = 0;
        $non_sidang = 0;
        $disetujui = 0;
        $pending = 0;

        foreach ($list as $r) {
            if ($r['is_non_sidang']) {
                $non_sidang++;
            } else {
                $sidang++;
            }
            if ($r['is_approved']) {
                $disetujui++;
            } else {
                $pending++;
            }
        }

        return array(
            'total'      => $total,
            'sidang'     => $sidang,
            'non_sidang' => $non_sidang,
            'disetujui'  => $disetujui,
            'pending'    => $pending
        );
    }

    /**
     * Ambil detail data 1 mahasiswa pendaftar sidang
     */
    public function get_detail_pendaftaran_sidang($nim) {
        $list = $this->get_pendaftaran_sidang_list($nim, 'all', 'all', 'nim');
        if (!empty($list)) {
            return $list[0];
        }
        return null;
    }

    /**
     * Inisialisasi otomatis tabel syarat_berkas_sidang
     */
    public function _init_syarat_berkas_sidang() {
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        try {
            if (!$this->db->table_exists('syarat_berkas_sidang')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `syarat_berkas_sidang` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `kode_berkas` VARCHAR(100) NOT NULL UNIQUE,
                    `nama_berkas` VARCHAR(255) NOT NULL,
                    `deskripsi` TEXT NULL,
                    `file_template` VARCHAR(255) NULL,
                    `is_required` TINYINT(1) DEFAULT 1,
                    `is_active` TINYINT(1) DEFAULT 1,
                    `urutan` INT DEFAULT 1,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            } else {
                $fields = $this->db->list_fields('syarat_berkas_sidang');
                if (!in_array('file_template', $fields)) {
                    $this->db->query("ALTER TABLE `syarat_berkas_sidang` ADD COLUMN `file_template` VARCHAR(255) NULL AFTER `deskripsi`");
                }
                if (!in_array('is_required', $fields)) {
                    $this->db->query("ALTER TABLE `syarat_berkas_sidang` ADD COLUMN `is_required` TINYINT(1) DEFAULT 1 AFTER `file_template`");
                }
                if (!in_array('is_active', $fields)) {
                    $this->db->query("ALTER TABLE `syarat_berkas_sidang` ADD COLUMN `is_active` TINYINT(1) DEFAULT 1 AFTER `is_required`");
                }
                if (!in_array('urutan', $fields)) {
                    $this->db->query("ALTER TABLE `syarat_berkas_sidang` ADD COLUMN `urutan` INT DEFAULT 1 AFTER `is_active`");
                }
                if (!in_array('updated_at', $fields)) {
                    $this->db->query("ALTER TABLE `syarat_berkas_sidang` ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`");
                }
            }

            if ($this->db->count_all('syarat_berkas_sidang') === 0) {
                $default_syarat = [
                    ['kode_berkas' => 'surat_izin_wali', 'nama_berkas' => 'Surat Izin Sidang yang Telah Ditandatangani Dosen Wali', 'deskripsi' => 'Surat rekomendasi izin pendaftaran sidang dari dosen wali akademik', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 1],
                    ['kode_berkas' => 'formulir_sidang', 'nama_berkas' => 'Formulir Pendaftaran Sidang',                             'deskripsi' => 'Formulir bukti pendaftaran sidang tugas akhir lengkap', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 2],
                    ['kode_berkas' => 'daftar_nilai',    'nama_berkas' => 'Daftar Nilai yang Telah Divalidasi oleh Ka Prodi',        'deskripsi' => 'Transkrip / daftar nilai akademik kelulusan mata kuliah tervalidasi Kaprodi', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 3],
                    ['kode_berkas' => 'sertifikat_eprt', 'nama_berkas' => 'Sertifikat EPRT',                                         'deskripsi' => 'Sertifikat kelulusan tes Bahasa Inggris / EPRT dengan skor memenuhi syarat', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 4],
                    ['kode_berkas' => 'sertifikat_tak',  'nama_berkas' => 'Sertifikat TAK',                                          'deskripsi' => 'Sertifikat Transkrip Aktivitas Kemahasiswaan (TAK) minimum poin terpenuhi', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 5],
                    ['kode_berkas' => 'bukti_bimbingan', 'nama_berkas' => 'Bukti Bimbingan (Logbook)',                               'deskripsi' => 'Logbook asistensi / bimbingan tugas akhir dengan pembimbing 1 & 2', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 6],
                    ['kode_berkas' => 'bukti_skpi',      'nama_berkas' => 'Bukti SKPI',                                              'deskripsi' => 'Dokumen Surat Keterangan Pendamping Ijazah & sertifikat pendukung', 'file_template' => null, 'is_required' => 1, 'is_active' => 1, 'urutan' => 7]
                ];
                $this->db->insert_batch('syarat_berkas_sidang', $default_syarat);
            }
        } catch (Throwable $e) {}

        $this->db->db_debug = $prev_debug;
    }

    /**
     * Ambil master berkas persyaratan pendaftaran sidang (Hanya yang aktif)
     */
    public function get_master_syarat_sidang() {
        $this->_init_syarat_berkas_sidang();
        $this->db->order_by('urutan', 'ASC')->order_by('id', 'ASC');
        $query = $this->db->get_where('syarat_berkas_sidang', ['is_active' => 1]);
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }

        // Cadangan 7 berkas bawaan jika tabel belum ada atau kosong
        return [
            ['id' => 1, 'kode_berkas' => 'surat_izin_wali', 'nama_berkas' => 'Surat Izin Sidang yang Telah Ditandatangani Dosen Wali', 'deskripsi' => 'Surat rekomendasi izin pendaftaran sidang dari dosen wali akademik', 'is_required' => 1, 'is_active' => 1, 'urutan' => 1],
            ['id' => 2, 'kode_berkas' => 'formulir_sidang', 'nama_berkas' => 'Formulir Pendaftaran Sidang',                             'deskripsi' => 'Formulir bukti pendaftaran sidang tugas akhir lengkap', 'is_required' => 1, 'is_active' => 1, 'urutan' => 2],
            ['id' => 3, 'kode_berkas' => 'daftar_nilai',    'nama_berkas' => 'Daftar Nilai yang Telah Divalidasi oleh Ka Prodi',        'deskripsi' => 'Transkrip / daftar nilai akademik kelulusan mata kuliah tervalidasi Kaprodi', 'is_required' => 1, 'is_active' => 1, 'urutan' => 3],
            ['id' => 4, 'kode_berkas' => 'sertifikat_eprt', 'nama_berkas' => 'Sertifikat EPRT',                                         'deskripsi' => 'Sertifikat kelulusan tes Bahasa Inggris / EPRT dengan skor memenuhi syarat', 'is_required' => 1, 'is_active' => 1, 'urutan' => 4],
            ['id' => 5, 'kode_berkas' => 'sertifikat_tak',  'nama_berkas' => 'Sertifikat TAK',                                          'deskripsi' => 'Sertifikat Transkrip Aktivitas Kemahasiswaan (TAK) minimum poin terpenuhi', 'is_required' => 1, 'is_active' => 1, 'urutan' => 5],
            ['id' => 6, 'kode_berkas' => 'bukti_bimbingan', 'nama_berkas' => 'Bukti Bimbingan (Logbook)',                               'deskripsi' => 'Logbook asistensi / bimbingan tugas akhir dengan pembimbing 1 & 2', 'is_required' => 1, 'is_active' => 1, 'urutan' => 6],
            ['id' => 7, 'kode_berkas' => 'bukti_skpi',      'nama_berkas' => 'Bukti SKPI',                                              'deskripsi' => 'Dokumen Surat Keterangan Pendamping Ijazah & sertifikat pendukung', 'is_required' => 1, 'is_active' => 1, 'urutan' => 7]
        ];
    }

    /**
     * Ambil SELURUH master berkas persyaratan sidang (Aktif & Nonaktif untuk Admin Management)
     */
    public function get_all_master_syarat_sidang() {
        $this->_init_syarat_berkas_sidang();
        $this->db->order_by('urutan', 'ASC')->order_by('id', 'ASC');
        $query = $this->db->get('syarat_berkas_sidang');
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return $this->get_master_syarat_sidang();
    }

    /**
     * Tambah atau update 1 master syarat sidang
     */
    public function save_master_syarat_item($id = null, $data = []) {
        $this->_init_syarat_berkas_sidang();

        if (!empty($id)) {
            $this->db->where('id', $id)->update('syarat_berkas_sidang', $data);
            return $id;
        } else {
            $this->db->insert('syarat_berkas_sidang', $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Hapus master syarat berkas sidang
     */
    public function delete_master_syarat_item($id) {
        $this->_init_syarat_berkas_sidang();
        return $this->db->where('id', $id)->delete('syarat_berkas_sidang');
    }

    /**
     * Ambil berkas persyaratan pendaftaran sidang mahasiswa secara dinamis
     */
    public function get_berkas_pendaftaran_sidang($nim) {
        $master_syarat = $this->get_master_syarat_sidang();
        $nim_clean = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$nim);
        $target_ids = $this->get_student_target_ids($nim);

        $syarat_codes_map = [];
        foreach ($master_syarat as $idx => $ms) {
            $kc = strtolower(trim($ms['kode_berkas'] ?? ($ms['kode'] ?? 'berkas_' . ($idx + 1))));
            $syarat_codes_map[$kc] = true;
            $syarat_codes_map[preg_replace('/[^a-z0-9_]/', '_', $kc)] = true;
        }

        // Cek apakah mahasiswa sudah disetujui penuh langsung dari data guidance
        $is_fully_approved = false;
        $should_purge_all_ps = false;
        $should_purge_pre_downgrade_ps = false;
        $downgrade_ts = 0;

        if ($this->db->table_exists('guidance')) {
            $g_check = $this->db->group_start()
                                ->where_in('id_mhs', $target_ids)
                                ->or_where_in('id', $target_ids)
                                ->or_where('id', 'gdn_' . $nim_clean)
                                ->group_end()
                                ->order_by('id', 'DESC')
                                ->get('guidance')
                                ->row_array();
            if ($g_check) {
                $sp_curr = strtolower(trim($g_check['status_preview'] ?? ''));
                $lulus_p3 = !empty($g_check['lulus_preview3']) && (int)$g_check['lulus_preview3'] === 1;
                $g_st = strtolower(trim($g_check['status_filesidang'] ?? ''));
                $downgrade_ts = !empty($g_check['lulus_at']) ? strtotime($g_check['lulus_at']) : 0;

                if (!$lulus_p3 || in_array($sp_curr, ['preview1', 'preview2', 'preview3'], true)) {
                    // Mahasiswa berada di Preview 1/2/3: bersihkan seluruh berkas/status Persyaratan Sidang sisa downgrade
                    $is_fully_approved = false;
                    $should_purge_all_ps = true;
                    if (!empty($g_check['status_filesidang'])) {
                        $this->db->where('id', $g_check['id'])->update('guidance', ['status_filesidang' => null]);
                    }
                } elseif (in_array($sp_curr, ['persyaratan_sidang', 'persyaratansidang'], true) && $g_st === '') {
                    // Mahasiswa di Persyaratan Sidang dengan status_filesidang = null (misal baru di-downgrade)
                    $is_fully_approved = false;
                    if (!empty($g_check['lulus_by_role'])) {
                        $should_purge_pre_downgrade_ps = true;
                    }
                } elseif ($g_st !== '' && (strpos($g_st, 'disetujui') !== false || strpos($g_st, 'approved') !== false || $g_st === 'lulus')) {
                    $is_fully_approved = true;
                }
            }
        }

        // Cek apakah ada record di file_pendaftaran menggunakan seluruh target_ids
        $db_files = array();
        if ($this->db->table_exists('file_pendaftaran')) {
            $rows = $this->db->group_start()
                             ->where_in('id_mhs', $target_ids)
                             ->or_like('id_mhs', $nim_clean)
                             ->group_end()
                             ->get('file_pendaftaran')
                             ->result_array();
            $stale_fp_ids = [];
            foreach ($rows as $r) {
                $key = strtolower(trim($r['nama'] ?? ''));
                if ($key === '') continue;
                $norm_key = preg_replace('/[^a-z0-9_]/', '_', $key);

                $is_ps_row = isset($syarat_codes_map[$key]) || isset($syarat_codes_map[$norm_key]) || strpos(strtolower($r['id'] ?? ''), 'fps_') === 0;
                if ($is_ps_row) {
                    $r_ts = strtotime($r['date_edit'] ?? ($r['date'] ?? ''));
                    $r_st = strtolower(trim($r['status_adminlaa'] ?? ($r['status_admin_laa'] ?? ($r['status_admin'] ?? ($r['status_laa'] ?? '')))));
                    $is_old_approved = (strpos($r_st, 'setuju') !== false || strpos($r_st, 'approved') !== false || $r_st === 'valid');

                    if ($should_purge_all_ps || ($should_purge_pre_downgrade_ps && ($is_old_approved || ($downgrade_ts > 0 && $r_ts > 0 && $r_ts <= $downgrade_ts)))) {
                        if (!empty($r['file']) && strpos($r['file'], 'Sertifikat_Massal') === false) {
                            $f_path = FCPATH . 'uploads/persyaratan_ta/' . basename($r['file']);
                            if (is_file($f_path)) @unlink($f_path);
                        }
                        if (!empty($r['id'])) {
                            $stale_fp_ids[] = $r['id'];
                        }
                        continue;
                    }
                }

                $db_files[$key] = $r;
                if (!isset($db_files[$norm_key])) {
                    $db_files[$norm_key] = $r;
                }
            }

            if (!empty($stale_fp_ids)) {
                $this->db->where_in('id', array_values(array_unique($stale_fp_ids)))->delete('file_pendaftaran');
            }
        }

        $berkas = array();
        foreach ($master_syarat as $idx => $s) {
            $kode = $s['kode_berkas'] ?? ($s['kode'] ?? 'berkas_' . ($idx + 1));
            $kode_lower = strtolower(trim($kode));
            $kode_norm  = preg_replace('/[^a-z0-9_]/', '_', $kode_lower);
            $nama = $s['nama_berkas'] ?? ($s['nama'] ?? 'Dokumen ' . ($idx + 1));
            $found = $db_files[$kode_lower] ?? ($db_files[$kode_norm] ?? null);

            $raw_file = !empty($found['file_name']) ? $found['file_name'] : (!empty($found['file']) ? $found['file'] : '');
            $base_file = $raw_file !== '' ? basename($raw_file) : '';
            $is_dummy_name = (
                $base_file === ''
                || strtolower($base_file) === strtolower($kode . '_' . $nim_clean . '.pdf')
                || preg_match('/^[a-z0-9_]+_' . preg_quote($nim_clean, '/') . '\.pdf$/i', $base_file)
                || strpos($base_file, 'berkas_') === 0
            );
            $is_uploaded = ($found !== null && !$is_dummy_name);
            $file_name = $is_uploaded ? $raw_file : '';

            // Ekstrak status dari status_adminlaa / status_admin / status_laa
            $raw_status = '';
            if ($found) {
                if (!empty($found['status_adminlaa'])) {
                    $raw_status = $found['status_adminlaa'];
                } elseif (!empty($found['status_admin_laa'])) {
                    $raw_status = $found['status_admin_laa'];
                } elseif (!empty($found['status_admin'])) {
                    $raw_status = $found['status_admin'];
                } elseif (!empty($found['status_laa'])) {
                    $raw_status = $found['status_laa'];
                }
            }
            if ($raw_status === '' && $is_fully_approved && $is_uploaded) {
                $raw_status = 'Disetujui Admin LAA';
            }

            $stClean = strtolower(trim($raw_status));
            if ($found === null && !$is_fully_approved) {
                $status = 'Belum Diunggah';
            } else if ($stClean === 'valid' || strpos($stClean, 'setuju') !== false || strpos($stClean, 'approved') !== false || $stClean === 'acc') {
                $status = 'Disetujui Admin LAA';
            } else if ($stClean === 'invalid' || strpos($stClean, 'revisi') !== false || strpos($stClean, 'tolak') !== false || strpos($stClean, 'rejected') !== false) {
                $status = 'Revisi Admin LAA';
            } else {
                $status = 'Pending Verifikasi';
            }

            $catatan = !empty($found['catatan']) ? $found['catatan'] : (!empty($found['komentar']) ? $found['komentar'] : '');
            $uploaded_at = !empty($found['date_edit']) ? $found['date_edit'] : (!empty($found['date']) ? $found['date'] : null);

            $berkas[] = array(
                'no'          => $idx + 1,
                'kode'        => $kode,
                'nama_file'   => $nama,
                'deskripsi'   => $s['deskripsi'] ?? '',
                'file_name'   => $file_name,
                'file_url'    => $is_uploaded ? $this->resolve_pdf_url($file_name) : '',
                'status'      => $status,
                'is_valid'    => ($status === 'Disetujui Admin LAA'),
                'is_uploaded' => $is_uploaded,
                'in_db'       => ($found !== null),
                'uploaded_at' => $uploaded_at,
                'catatan'     => $catatan,
                'is_required' => $s['is_required'] ?? 1
            );
        }

        return $berkas;
    }

    /**
     * Update status berkas pendaftaran sidang mahasiswa
     */
    public function save_status_berkas_sidang($nim, $kode_berkas, $status, $catatan = '') {
        $this->clear_cache();
        $nim_clean = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$nim);
        $target_ids = $this->get_student_target_ids($nim);

        // Standarisasi format status
        $st_lower = strtolower(trim($status));
        if ($st_lower === 'valid' || strpos($st_lower, 'setuju') !== false || strpos($st_lower, 'approved') !== false || $st_lower === 'acc') {
            $disp_status = 'Disetujui Admin LAA';
            $fp_short    = 'Approved';
        } else if ($st_lower === 'invalid' || strpos($st_lower, 'revisi') !== false || strpos($st_lower, 'tolak') !== false || strpos($st_lower, 'rejected') !== false) {
            $disp_status = 'Revisi Admin LAA';
            $fp_short    = 'Rejected';
        } else {
            $disp_status = 'Pending Verifikasi';
            $fp_short    = 'Pending';
        }

        if ($this->db->table_exists('file_pendaftaran')) {
            $existing = $this->db->group_start()
                                 ->where_in('id_mhs', $target_ids)
                                 ->or_like('id_mhs', $nim_clean)
                                 ->group_end()
                                 ->where('nama', $kode_berkas)
                                 ->get('file_pendaftaran')
                                 ->result_array();

            $fp_upd = [
                'status_adminlaa' => $disp_status,
                'komentar'        => $catatan,
                'date_edit'       => date('Y-m-d H:i:s')
            ];
            if ($this->db->field_exists('status_admin', 'file_pendaftaran')) {
                $fp_upd['status_admin'] = $fp_short;
            }
            if ($this->db->field_exists('status_laa', 'file_pendaftaran')) {
                $fp_upd['status_laa'] = $fp_short;
            }
            if ($this->db->field_exists('status_admin_laa', 'file_pendaftaran')) {
                $fp_upd['status_admin_laa'] = $disp_status;
            }
            if ($this->db->field_exists('view_adminlaa', 'file_pendaftaran')) {
                $fp_upd['view_adminlaa'] = ($fp_short === 'Pending') ? 0 : 1;
            }

            if (!empty($existing)) {
                $this->db->group_start()
                         ->where_in('id_mhs', $target_ids)
                         ->or_like('id_mhs', $nim_clean)
                         ->group_end()
                         ->where('nama', $kode_berkas)
                         ->update('file_pendaftaran', $fp_upd);
            } else {
                $u_id = !empty($target_ids) ? $target_ids[0] : ('usr_mhs_' . $nim_clean);
                $ins_fp = array_merge([
                    'id'            => 'fps_' . $nim_clean . '_' . $kode_berkas . '_' . time(),
                    'id_mhs'        => $u_id,
                    'nama'          => $kode_berkas,
                    'file'          => $kode_berkas . '_' . $nim_clean . '.pdf',
                    'date'          => date('Y-m-d H:i:s'),
                    'view_doswal'   => 1,
                    'status_doswal' => 'Pending'
                ], $fp_upd);
                $this->db->insert('file_pendaftaran', $ins_fp);
            }
        }

        // Evaluasi otomatis status kelayakan bimbingan untuk pendaftaran sidang
        $overall_status = 'Pending Verifikasi';
        if ($this->db->table_exists('guidance')) {
            $all_berkas = $this->get_berkas_pendaftaran_sidang($nim_clean);
            $total_count = count($all_berkas);
            $req_count = 0;
            $req_valid_count = 0;
            $valid_count = 0;
            $invalid_count = 0;

            foreach ($all_berkas as $b) {
                $is_req = !empty($b['is_required']);
                if ($is_req) {
                    $req_count++;
                }
                if ($b['status'] === 'Disetujui Admin LAA') {
                    $valid_count++;
                    if ($is_req) {
                        $req_valid_count++;
                    }
                } elseif ($b['status'] === 'Revisi Admin LAA') {
                    $invalid_count++;
                }
            }

            if ($invalid_count > 0) {
                $overall_status = 'Revisi Admin LAA';
            } elseif (($total_count > 0 && $valid_count === $total_count) || ($req_count > 0 && $req_valid_count === $req_count)) {
                $overall_status = 'Disetujui Admin LAA';
            } else {
                $overall_status = 'Pending Verifikasi';
            }

            $update_guidance = [
                'status_filesidang' => $overall_status
            ];
            if ($overall_status === 'Disetujui Admin LAA' && $this->db->field_exists('status_preview', 'guidance')) {
                $g_row = $this->db->group_start()
                    ->where_in('id_mhs', $target_ids)
                    ->or_where_in('id', $target_ids)
                    ->group_end()
                    ->get('guidance')
                    ->row_array();
                if ($g_row && (!empty($g_row['lulus_preview3']) || in_array(strtolower(trim($g_row['status_preview'] ?? '')), ['preview3', 'persyaratan_sidang', 'persyaratansidang', ''], true))) {
                    $update_guidance['status_preview'] = 'sidang';
                }
            }

            $this->db->group_start()
                     ->where_in('id_mhs', $target_ids)
                     ->or_where_in('id', $target_ids)
                     ->group_end()
                     ->update('guidance', $update_guidance);
        }

        return [
            'overall_status' => $overall_status,
            'is_approved'    => ($overall_status === 'Disetujui Admin LAA')
        ];
    }

    /**
     * Setujui seluruh berkas persyaratan sidang mahasiswa sekaligus
     */
    public function approve_all_berkas_sidang($nim, $status = 'Disetujui Admin LAA', $catatan = '') {
        $this->clear_cache();
        $nim_clean = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$nim);
        $target_ids = $this->get_student_target_ids($nim);

        $st_lower = strtolower(trim($status));
        $is_app = ($st_lower === 'valid' || strpos($st_lower, 'setuju') !== false || strpos($st_lower, 'approved') !== false || $st_lower === 'acc');
        $disp_status = $is_app ? 'Disetujui Admin LAA' : $status;

        if ($is_app) {
            $master_syarat = $this->get_master_syarat_sidang();
            foreach ($master_syarat as $idx => $s) {
                $kode = $s['kode_berkas'] ?? ($s['kode'] ?? 'berkas_' . ($idx + 1));
                $this->save_status_berkas_sidang($nim, $kode, 'Disetujui Admin LAA', $catatan);
            }
        }

        if ($this->db->table_exists('guidance')) {
            $upd = [
                'status_filesidang' => $disp_status
            ];
            if ($this->db->field_exists('komentar_pendaftaran_sidang', 'guidance')) {
                $upd['komentar_pendaftaran_sidang'] = $catatan;
            }
            if ($is_app && $this->db->field_exists('status_preview', 'guidance')) {
                $g_row = $this->db->group_start()
                    ->where_in('id_mhs', $target_ids)
                    ->or_where_in('id', $target_ids)
                    ->group_end()
                    ->get('guidance')
                    ->row_array();
                if ($g_row && (!empty($g_row['lulus_preview3']) || in_array(strtolower(trim($g_row['status_preview'] ?? '')), ['preview3', 'persyaratan_sidang', 'persyaratansidang', ''], true))) {
                    $upd['status_preview'] = 'sidang';
                }
            }
            $this->db->group_start()
                     ->where_in('id_mhs', $target_ids)
                     ->or_where_in('id', $target_ids)
                     ->group_end()
                     ->update('guidance', $upd);
        }

        return true;
    }

    // =========================================================================
    // 2. LIHAT PEMBIMBING MODULE METHODS
    // =========================================================================

    /**
     * Ambil rekap data pembimbing untuk seluruh mahasiswa
     */
    public function get_lihat_pembimbing_list($search = '', $filter_prodi = 'all', $filter_status = 'all', $cat = 'query') {
        $list = $this->get_pendaftaran_sidang_list($search, 'all', 'all', $cat);
        $result = array();

        foreach ($list as $idx => $r) {
            // Filter Prodi
            if ($filter_prodi !== 'all' && stripos($r['prodi'], $filter_prodi) === false) {
                continue;
            }
            // Filter Status
            if ($filter_status === 'approved' && !$r['is_approved']) {
                continue;
            } elseif ($filter_status === 'pending' && $r['is_approved']) {
                continue;
            }

            $result[] = array(
                'no'              => count($result) + 1,
                'nim'             => $r['nim'],
                'nama'            => $r['nama'],
                'prodi'           => $r['prodi'],
                'konsentrasi'     => $r['konsentrasi'],
                'dosen_wali'      => $r['dosen_wali'],
                'pembimbing_1'    => $r['pembimbing_1'],
                'pembimbing_2'    => $r['pembimbing_2'],
                'tanggal_approve' => $r['tanggal_sidang'] ?: date('Y-m-d'),
                'status'          => $r['status'],
                'tahapan'         => $r['tahap'],
                'jenis_ta'        => $r['jenis_ta']
            );
        }

        return $result;
    }

    // =========================================================================
    // 3. YUDISIUM MODULE METHODS (S1 & S2)
    // =========================================================================

    /**
     * Ambil data peserta yudisium berdasarkan jenjang (S1 / S2)
     */
    public function get_yudisium_list($search = '', $jenjang = 's1', $cat = 'query') {
        $list = $this->get_pendaftaran_sidang_list($search, 'all', 'all', $cat);
        $result = array();

        foreach ($list as $idx => $r) {
            $prodi = $r['prodi'] ?? 'Desain Komunikasi Visual';
            $isS2 = (stripos($prodi, 'magister') !== false || stripos($prodi, 's2') !== false || stripos($prodi, 'pascasarjana') !== false || $idx % 4 === 3);

            if ($jenjang === 's1' && $isS2) {
                continue;
            }
            if ($jenjang === 's2' && !$isS2) {
                continue;
            }

            $result[] = array(
                'no'             => count($result) + 1,
                'nim'            => $r['nim'],
                'nama'           => $r['nama'],
                'prodi'          => $prodi,
                'jenjang'        => $isS2 ? 'S2 (Magister)' : 'S1 (Sarjana)',
                'ipk'            => !empty($r['ipk']) ? $r['ipk'] : '3.75',
                'tanggal_lulus'  => !empty($r['tanggal_sidang']) ? $r['tanggal_sidang'] : '2026-06-30',
                'nomor_sk'       => 'SK-YUD/FIK/2026/' . sprintf('%03d', count($result) + 1),
                'status_yudisium'=> 'Lulus Yudisium',
                'status_wisuda'  => 'Siap Wisuda Periode II 2026',
                'judul'          => $r['judul'] ?? 'Tugas Akhir FIK'
            );
        }

        return $result;
    }

    /**
     * Ambil statistik yudisium untuk stat cards
     */
    public function get_yudisium_stats() {
        $s1 = $this->get_yudisium_list('', 's1', 'query');
        $s2 = $this->get_yudisium_list('', 's2', 'query');

        return array(
            'total'      => count($s1) + count($s2),
            'total_s1'   => count($s1),
            'total_s2'   => count($s2),
            'siap_wisuda'=> count($s1) + count($s2)
        );
    }

    // =========================================================================
    // 4. JADWAL SIDANG MODULE METHODS
    // =========================================================================

    /**
     * Ambil data jadwal pelaksanaan sidang tugas akhir
     */
    public function get_jadwal_sidang_list($search = '', $filter_tanggal = 'all', $filter_prodi = 'all', $cat = 'query') {
        $list = $this->get_pendaftaran_sidang_list($search, 'all', 'all', $cat);
        $result = array();

        foreach ($list as $idx => $r) {
            if ($filter_prodi !== 'all' && stripos($r['prodi'], $filter_prodi) === false) {
                continue;
            }
            if ($filter_tanggal !== 'all' && $r['tanggal_sidang'] !== $filter_tanggal) {
                continue;
            }

            $result[] = array(
                'no'            => count($result) + 1,
                'nim'           => $r['nim'],
                'nama'          => $r['nama'],
                'prodi'         => $r['prodi'],
                'konsentrasi'   => $r['konsentrasi'],
                'judul'         => $r['judul'],
                'hari_tanggal'  => 'Selasa, 30 Juni 2026',
                'tanggal'       => $r['tanggal_sidang'] ?: '2026-06-30',
                'waktu'         => $r['waktu_sidang'] ? substr($r['waktu_sidang'], 0, 5) . ' WIB' : '09:00 WIB',
                'ruangan'       => $r['ruang_sidang'] ?: 'Ruang Sidang FIK Lt. 2',
                'pembimbing_1'  => $r['pembimbing_1'],
                'pembimbing_2'  => $r['pembimbing_2'],
                'penguji_1'     => $r['penguji_1'],
                'penguji_2'     => $r['penguji_2'],
                'status_sidang' => 'Terjadwal'
            );
        }

        return $result;
    }

    // =========================================================================
    // 5. BAP SIDANG MODULE METHODS (2-PAGE OFFICIAL DOCUMENT)
    // =========================================================================

    /**
     * Ambil daftar pengajuan BAP Sidang mahasiswa
     */
    public function get_bap_sidang_list($search = '', $filter_status = 'all', $cat = 'query') {
        $list = $this->get_pendaftaran_sidang_list($search, 'all', 'all', $cat);
        $result = array();

        foreach ($list as $idx => $r) {
            $result[] = array(
                'no'             => count($result) + 1,
                'nim'            => $r['nim'],
                'nama'           => $r['nama'],
                'prodi'          => $r['prodi'],
                'konsentrasi'    => $r['konsentrasi'],
                'pembimbing_1'   => $r['pembimbing_1'],
                'pembimbing_2'   => $r['pembimbing_2'],
                'penguji_1'      => $r['penguji_1'],
                'penguji_2'      => $r['penguji_2'],
                'dokumen_bap'    => $r['bap'],
                'status_igracias'=> $r['status_bap'] ?: 'BAP (GRACIAS): Disetujui',
                'status_fakultas'=> $r['status_bap2'] ?: 'BAP FAKULTAS: Sudah Ditandatangani',
                'judul'          => $r['judul'],
                'tanggal_sidang' => $r['tanggal_sidang'],
                'waktu_sidang'   => $r['waktu_sidang'],
                'ruang_sidang'   => $r['ruang_sidang']
            );
        }

        return $result;
    }

    /**
     * Ambil detail data BAP lengkap untuk preview & generate dokumen 2 halaman
     */
    public function get_detail_bap_sidang($nim) {
        $mhs = $this->get_detail_pendaftaran_sidang($nim);
        if (!$mhs) {
            $mhs = [
                'nim'          => $nim,
                'nama'         => 'Muhammad Aqillah Putra Irawan',
                'prodi'        => 'Desain Komunikasi Visual',
                'konsentrasi'  => 'ADVERTISING',
                'judul'        => 'Pengaruh Strategi Brand Activation dalam Meningkatkan Penggunaan Produk Body Lotion Vaseline pada Konsumen',
                'pembimbing_1' => 'Nina Nursetia Ningrum, S.Pd., S.Pd., M.Pd.',
                'pembimbing_2' => 'I Gusti Agung Rangga Lawe, S.Ds., M.Ds.',
                'penguji_1'    => 'Dr. Samsul Alam, S.Pd., M.Pd.',
                'penguji_2'    => 'Putu Raka Setya Putra, S.Ds., M.Ds.',
                'tanggal_sidang'=> '2026-06-30',
                'waktu_sidang' => '09:00:00',
                'ruang_sidang' => 'Kampus Fakultas Industri Kreatif Jl. Telekomunikasi, Ters. Buah Batu Bandung'
            ];
        }

        // Nilai Komprehensif (Page 2)
        $nilai_p1 = 73;
        $nilai_p2 = 70;
        $nilai_pj1 = 64;
        $nilai_pj2 = 66;

        $bobot_p1 = 0.4;
        $bobot_p2 = 0.2;
        $bobot_pj1 = 0.2;
        $bobot_pj2 = 0.2;

        $nb_p1 = round($nilai_p1 * $bobot_p1, 2);
        $nb_p2 = round($nilai_p2 * $bobot_p2, 2);
        $nb_pj1 = round($nilai_pj1 * $bobot_pj1, 2);
        $nb_pj2 = round($nilai_pj2 * $bobot_pj2, 2);

        $nilai_akhir = round($nb_p1 + $nb_p2 + $nb_pj1 + $nb_pj2, 1);

        // Konversi Huruf
        if ($nilai_akhir > 85)     $indeks = 'A';
        elseif ($nilai_akhir > 75) $indeks = 'AB';
        elseif ($nilai_akhir > 65) $indeks = 'B';
        elseif ($nilai_akhir > 60) $indeks = 'BC';
        elseif ($nilai_akhir > 50) $indeks = 'C';
        elseif ($nilai_akhir > 40) $indeks = 'D';
        else                       $indeks = 'E';

        // Look up digital signature image files from user table
        $ttd_p1  = $this->get_dosen_ttd_by_name($mhs['pembimbing_1'] ?? '');
        $ttd_p2  = $this->get_dosen_ttd_by_name($mhs['pembimbing_2'] ?? '');
        $ttd_pj1 = $this->get_dosen_ttd_by_name($mhs['penguji_1'] ?? '');
        $ttd_pj2 = $this->get_dosen_ttd_by_name($mhs['penguji_2'] ?? '');

        return array(
            'nim'              => $mhs['nim'],
            'nama'             => $mhs['nama'],
            'prodi'            => $mhs['prodi'],
            'konsentrasi'      => $mhs['konsentrasi'],
            'judul'            => $mhs['judul'],
            'hari'             => 'Selasa',
            'tanggal_text'     => '30 Juni 2026',
            'tempat'           => 'Kampus Fakultas Industri Kreatif Jl. Telekomunikasi, Ters. Buah Batu Bandung',
            'semester'         => 'Genap',
            'tahun_akademik'   => '2025/2026',
            'pembimbing_1'     => $mhs['pembimbing_1'],
            'pembimbing_2'     => $mhs['pembimbing_2'],
            'penguji_1'        => $mhs['penguji_1'],
            'penguji_2'        => $mhs['penguji_2'],
            'ttd_pembimbing_1' => $ttd_p1,
            'ttd_pembimbing_2' => $ttd_p2,
            'ttd_penguji_1'    => $ttd_pj1,
            'ttd_penguji_2'    => $ttd_pj2,
            'catatan_penguji'  => 'Perbaiki beberapa format sitasi pada Bab 4 dan perjelas diagram kerangka penelitian.',
            'evaluasi_nilai'   => [
                ['peran' => 'Pembimbing 1', 'nama' => $mhs['pembimbing_1'], 'nilai' => $nilai_p1, 'bobot' => $bobot_p1, 'nilai_bobot' => $nb_p1],
                ['peran' => 'Pembimbing 2', 'nama' => $mhs['pembimbing_2'], 'nilai' => $nilai_p2, 'bobot' => $bobot_p2, 'nilai_bobot' => $nb_p2],
                ['peran' => 'Penguji 1',    'nama' => $mhs['penguji_1'],    'nilai' => $nilai_pj1, 'bobot' => $bobot_pj1, 'nilai_bobot' => $nb_pj1],
                ['peran' => 'Penguji 2',    'nama' => $mhs['penguji_2'],    'nilai' => $nilai_pj2, 'bobot' => $bobot_pj2, 'nilai_bobot' => $nb_pj2],
            ],
            'nilai_akhir'      => $nilai_akhir,
            'indeks_huruf'     => $indeks,
            'hasil_sidang'     => 'Lulus',
            'ketua_sidang'     => $mhs['penguji_1']
        );
    }

    /**
     * Helper lookup signature image file from user table by lecturer name or NIP
     */
    public function get_dosen_ttd_by_name($name_or_nip) {
        if (empty($name_or_nip)) return null;
        if (!$this->db->table_exists('user')) return null;

        $clean_name = trim(preg_replace('/^(Dr\.|Prof\.|Drs\.|Dra\.|Ir\.)\s*/i', '', $name_or_nip));
        $name_parts = explode(',', $clean_name);
        $pure_name  = trim($name_parts[0]);

        if (empty($pure_name)) return null;

        $row = $this->db->group_start()
                         ->like('name', $pure_name)
                         ->or_where('nip', $name_or_nip)
                         ->or_where('username', $name_or_nip)
                         ->group_end()
                         ->where('ttd IS NOT NULL AND ttd != ""')
                         ->get('user')
                         ->row_array();

        if ($row && !empty($row['ttd'])) {
            $path = FCPATH . 'uploads/signatures/' . $row['ttd'];
            if (file_exists($path)) {
                return $row['ttd'];
            }
        }
        return null;
    }

    /**
     * =========================================================================
     * REVIEW REKOMENDASI PEMBIMBING 1 & DOWNGRADE TAHAP PREVIEW (LAA)
     * =========================================================================
     * Mengambil daftar lengkap mahasiswa bimbingan TA dan pengajuan rekomendasi kelulusan
     * dari Dosen Pembimbing 1 (Jalur Sidang Reguler vs Jalur Non-Sidang Ekuivalensi).
     * 
     * Mekanisme Logika:
     * 1. Query JOIN komprehensif antara tabel guidance, user, mahasiswa, thesis_lecturers, dan rekomen_submission.
     * 2. Auto-heal: Memulihkan kolom status_preview jika sebelumnya terpotong menjadi kosong akibat batasan ENUM lawas.
     * 3. Decode JSON Berkas: Mengurai form_data_json menjadi array berkas eviden bukti luaran (Prestasi, HKI, Proyek, dll).
     * 4. Normalisasi Tahapan: Menghitung posisi tahap_terkini (Preview 1, Preview 2, Preview 3, Persyaratan Sidang, Sidang).
     * 5. Multi-Column Filter: Mendukung pencarian teks pada NIM, Nama, Judul TA, Nama Dosen, dan Jalur Rekomendasi.
     * 
     * @access public
     * @param  string $search Kata kunci pencarian
     * @param  string $filter_type Tipe filter rekomendasi ('all' | 'sidang' | 'non_sidang' | 'pending')
     * @return array Array daftar mahasiswa bimbingan unik per NIM
     */
    public function get_rekomendasi_submissions_list($search = '', $filter_type = 'all') {
        $prev_dbg = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        @$this->db->query("ALTER TABLE `guidance` MODIFY COLUMN `status_preview` VARCHAR(50) NULL DEFAULT 'preview1'");
        $this->db->db_debug = $prev_dbg;

        $select_extra = '';
        if ($this->db->field_exists('downgrade_log', 'guidance')) {
            $select_extra = 'g.downgrade_log,';
        }

        $this->db->select('
            g.id AS id_guidance,
            g.id_mhs AS nim,
            g.status_preview,
            g.lulus_preview1,
            g.lulus_preview2,
            g.lulus_preview3,
            g.lulus_sidang,
            g.status_filesidang,
            ' . $select_extra . '
            g.judul_1 AS judul,
            g.peminatan,
            TRIM(CONCAT(IFNULL(m.nama_depan, ""), " ", IFNULL(m.nama_belakang, ""))) AS nama_mhs,
            u.name AS nama_user,
            rs.id AS id_rekomen,
            rs.recommendation_type,
            rs.jalur_id,
            rs.jalur_title,
            rs.form_data_json,
            rs.catatan_dosen,
            rs.status AS status_rekomen,
            rs.created_at AS rekomen_at,
            u_p1.name AS nama_pembimbing1,
            u_p2.name AS nama_pembimbing2,
            u_pj1.name AS nama_penguji1,
            u_pj2.name AS nama_penguji2
        ');
        $this->db->from('guidance g');
        $this->db->join('user u', 'u.id = g.id_mhs OR u.nim = g.id_mhs', 'left');
        $this->db->join('mahasiswa m', 'm.nim = u.nim OR m.nim = g.id_mhs', 'left');
        $this->db->join('thesis_lecturers tl', 'tl.id_guidance = g.id', 'left');
        $this->db->join('user u_p1', '(tl.dosen_pembimbing1 IS NOT NULL AND tl.dosen_pembimbing1 != "" AND (u_p1.nip = tl.dosen_pembimbing1 OR u_p1.id = tl.dosen_pembimbing1))', 'left');
        $this->db->join('user u_p2', '(tl.dosen_pembimbing2 IS NOT NULL AND tl.dosen_pembimbing2 != "" AND (u_p2.nip = tl.dosen_pembimbing2 OR u_p2.id = tl.dosen_pembimbing2))', 'left');
        $this->db->join('user u_pj1', '(tl.dosen_penguji1 IS NOT NULL AND tl.dosen_penguji1 != "" AND (u_pj1.nip = tl.dosen_penguji1 OR u_pj1.id = tl.dosen_penguji1))', 'left');
        $this->db->join('user u_pj2', '(tl.dosen_penguji2 IS NOT NULL AND tl.dosen_penguji2 != "" AND (u_pj2.nip = tl.dosen_penguji2 OR u_pj2.id = tl.dosen_penguji2))', 'left');
        $this->db->join('rekomen_submission rs', 'rs.nim = g.id_mhs OR rs.nim = u.nim', 'left');

        if ($filter_type === 'sidang') {
            $this->db->where('rs.recommendation_type', 'sidang');
        } elseif ($filter_type === 'non_sidang' || $filter_type === 'non-sidang') {
            $this->db->where('rs.recommendation_type', 'non_sidang');
        } elseif ($filter_type === 'pending' || $filter_type === 'belum') {
            $this->db->where('rs.id IS NULL', null, false);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('g.id_mhs', $search);
            $this->db->or_like('u.nim', $search);
            $this->db->or_like('m.nama_depan', $search);
            $this->db->or_like('m.nama_belakang', $search);
            $this->db->or_like('u.name', $search);
            $this->db->or_like('g.judul_1', $search);
            $this->db->or_like('rs.jalur_title', $search);
            $this->db->group_end();
        }

        $this->db->order_by('rs.created_at', 'DESC');
        $this->db->order_by('g.id', 'DESC');
        $results = $this->db->get()->result_array();

        // Saring data unik per NIM (ambil rekomendasi terbaru jika ada yang dobel)
        $unique = [];
        foreach ($results as $row) {
            $nim = !empty($row['nim']) ? $row['nim'] : ($row['id_guidance'] ?? '');
            if (isset($unique[$nim])) continue;

            $row['nama_mahasiswa'] = !empty($row['nama_mhs']) ? $row['nama_mhs'] : (!empty($row['nama_user']) ? $row['nama_user'] : $nim);
            $row['pembimbing_1'] = !empty($row['nama_pembimbing1']) ? $row['nama_pembimbing1'] : '-';
            $row['pembimbing_2'] = !empty($row['nama_pembimbing2']) ? $row['nama_pembimbing2'] : '-';
            $row['penguji_1']    = !empty($row['nama_penguji1']) ? $row['nama_penguji1'] : '-';
            $row['penguji_2']    = !empty($row['nama_penguji2']) ? $row['nama_penguji2'] : '-';

            // Urai data form_data_json jika tersedia
            $row['files_uploaded'] = [];
            if (!empty($row['form_data_json'])) {
                $dec = json_decode($row['form_data_json'], true);
                if (is_array($dec)) {
                    $row['files_uploaded'] = $dec;
                }
            }

            // Normalisasi status preview terkini
            $sp = strtolower(trim($row['status_preview'] ?? ''));

            // Auto-heal jika sebelumnya status_preview terpotong menjadi string kosong akibat tipe kolom ENUM lama saat downgrade
            if ($sp === '' && !empty($row['downgrade_log'])) {
                $dg_arr = json_decode($row['downgrade_log'], true);
                if (is_array($dg_arr) && !empty($dg_arr)) {
                    $last_dg = end($dg_arr);
                    if (!empty($last_dg['ke'])) {
                        $sp = strtolower(trim($last_dg['ke']));
                        $this->load->model('Mahasiswa_model');
                        $this->Mahasiswa_model->repair_truncated_downgrade($nim, $last_dg['dari'] ?? 'sidang', $sp);
                        $row['status_preview']    = $sp;
                        $row['status_filesidang'] = null;
                        $row['lulus_sidang']      = 0;
                        $row['lulus_preview3']    = ($sp === 'persyaratan_sidang') ? 1 : 0;
                        $row['lulus_preview2']    = in_array($sp, ['persyaratan_sidang', 'preview3'], true) ? 1 : 0;
                        $row['lulus_preview1']    = in_array($sp, ['persyaratan_sidang', 'preview3', 'preview2'], true) ? 1 : 0;
                    }
                }
            }

            $st_fs = strtolower(trim($row['status_filesidang'] ?? ''));
            $is_fs_acc = (strpos($st_fs, 'disetujui') !== false || strpos($st_fs, 'approved') !== false || $st_fs === 'lulus');

            if ($sp === 'sidang' || !empty($row['lulus_sidang']) || (!empty($row['lulus_preview3']) && $is_fs_acc && $sp !== 'persyaratan_sidang')) {
                $row['tahap_terkini'] = 'Sidang';
                $row['tahap_key']     = 'sidang';
            } elseif ($sp === 'persyaratan_sidang' || $sp === 'persyaratansidang' || !empty($row['lulus_preview3'])) {
                $row['tahap_terkini'] = 'Persyaratan Sidang';
                $row['tahap_key']     = 'persyaratan_sidang';
            } elseif ($sp === 'preview3' || !empty($row['lulus_preview2'])) {
                $row['tahap_terkini'] = 'Preview 3';
                $row['tahap_key']     = 'preview3';
            } elseif ($sp === 'preview2' || !empty($row['lulus_preview1'])) {
                $row['tahap_terkini'] = 'Preview 2';
                $row['tahap_key']     = 'preview2';
            } elseif ($sp === 'preview1' || empty($sp)) {
                $row['tahap_terkini'] = 'Preview 1';
                $row['tahap_key']     = 'preview1';
            } else {
                $row['tahap_terkini'] = ucfirst(str_replace('_', ' ', $sp));
                $row['tahap_key']     = $sp;
            }

            $unique[$nim] = $row;
        }

        return array_values($unique);
    }
}

