<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminLayanan_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_ensure_tables();
    }

    /**
     * Memastikan tabel syarat_berkas_ta dan pendaftaran_berkas tersedia
     */
    private function _ensure_tables() {
        if (!$this->db->table_exists('syarat_berkas_ta')) {
            $this->db->query("CREATE TABLE `syarat_berkas_ta` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `kode_berkas` VARCHAR(50) NOT NULL UNIQUE,
                `nama_berkas` VARCHAR(150) NOT NULL,
                `deskripsi` TEXT NULL,
                `is_required` TINYINT(1) DEFAULT 1,
                `is_active` TINYINT(1) DEFAULT 1,
                `urutan` INT DEFAULT 1,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Seed default requirement items
            $default_items = [
                ['kode_berkas' => 'ksm', 'nama_berkas' => 'KSM (Kartu Studi Mahasiswa)', 'deskripsi' => 'Kartu Studi Mahasiswa semester aktif (PDF)', 'is_required' => 1, 'is_active' => 1, 'urutan' => 1],
                ['kode_berkas' => 'transkrip', 'nama_berkas' => 'Transkrip Nilai', 'deskripsi' => 'Transkrip nilai Kumulatif sampai semester terakhir (PDF)', 'is_required' => 1, 'is_active' => 1, 'urutan' => 2],
                ['kode_berkas' => 'pernyataan', 'nama_berkas' => 'Surat Pernyataan', 'deskripsi' => 'Surat Pernyataan Keaslian & Orisinalitas (PDF)', 'is_required' => 1, 'is_active' => 1, 'urutan' => 3],
                ['kode_berkas' => 'bebas_lab', 'nama_berkas' => 'Surat Bebas Lab', 'deskripsi' => 'Surat Bebas Tanggungan Laboratorium (PDF)', 'is_required' => 1, 'is_active' => 1, 'urutan' => 4]
            ];
            $this->db->insert_batch('syarat_berkas_ta', $default_items);
        }

        if (!$this->db->table_exists('pendaftaran_berkas')) {
            $this->db->query("CREATE TABLE `pendaftaran_berkas` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nim` VARCHAR(30) NOT NULL,
                `kode_berkas` VARCHAR(50) NOT NULL,
                `nama_berkas` VARCHAR(150) NULL,
                `file_name` VARCHAR(255) NOT NULL,
                `status_verifikasi` ENUM('Pending','Valid','Invalid') DEFAULT 'Pending',
                `catatan` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `nim_kode` (`nim`, `kode_berkas`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } else {
            if (!$this->db->field_exists('nama_berkas', 'pendaftaran_berkas')) {
                $this->db->query("ALTER TABLE `pendaftaran_berkas` ADD COLUMN `nama_berkas` VARCHAR(150) NULL AFTER `kode_berkas`;");
            }
            if (!$this->db->field_exists('catatan', 'pendaftaran_berkas')) {
                $this->db->query("ALTER TABLE `pendaftaran_berkas` ADD COLUMN `catatan` TEXT NULL AFTER `status_verifikasi`;");
            }
            if ($this->db->table_exists('syarat_berkas_ta')) {
                $this->db->query("UPDATE `pendaftaran_berkas` pb 
                                  JOIN `syarat_berkas_ta` sb ON pb.kode_berkas = sb.kode_berkas 
                                  SET pb.nama_berkas = sb.nama_berkas 
                                  WHERE pb.nama_berkas IS NULL OR pb.nama_berkas = ''");
            }
        }

        if ($this->db->table_exists('pendaftaran_berkas') && $this->db->table_exists('pendaftaran_ta')) {
            $this->db->query("UPDATE `pendaftaran_berkas` pb 
                              JOIN `pendaftaran_ta` pt ON pt.nim = pb.nim 
                              SET pb.status_verifikasi = 'Pending' 
                              WHERE pt.status_approval_admin = 'Pending'");
        }

        if ($this->db->table_exists('pendaftaran_ta')) {
            if (!$this->db->field_exists('is_submitted', 'pendaftaran_ta')) {
                $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `is_submitted` TINYINT(1) NOT NULL DEFAULT 1;");
            }
        }
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
        $this->_ensure_tables();
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('syarat_berkas_ta')->result_array();
    }

    public function get_active_syarat_berkas() {
        $this->_ensure_tables();
        $this->db->where('is_active', 1);
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('syarat_berkas_ta')->result_array();
    }

    public function save_syarat_berkas($data) {
        $this->_ensure_tables();
        return $this->db->insert('syarat_berkas_ta', $data);
    }

    public function update_syarat_berkas($id, $data) {
        $this->_ensure_tables();
        $this->db->where('id', $id);
        return $this->db->update('syarat_berkas_ta', $data);
    }

    public function toggle_syarat_berkas($id) {
        $this->_ensure_tables();
        $row = $this->db->get_where('syarat_berkas_ta', ['id' => $id])->row_array();
        if (!$row) return false;

        $new_status = $row['is_active'] == 1 ? 0 : 1;
        $this->db->where('id', $id);
        return $this->db->update('syarat_berkas_ta', ['is_active' => $new_status]);
    }

    public function delete_syarat_berkas($id) {
        $this->_ensure_tables();
        $this->db->where('id', $id);
        return $this->db->delete('syarat_berkas_ta');
    }

    public function get_student_berkas_map($nim) {
        $this->_ensure_tables();
        $rows = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim])->result_array();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['kode_berkas']] = $r;
        }
        return $map;
    }

    public function save_student_berkas($nim, $kode_berkas, $file_name, $status = 'Pending', $arg5 = null, $arg6 = null) {
        $this->_ensure_tables();

        $nama_berkas = null;
        $catatan     = null;

        if ($arg6 !== null) {
            $nama_berkas = $arg5;
            $catatan     = $arg6;
        } else if ($arg5 !== null) {
            if ($status === 'Pending' || ($this->db->table_exists('syarat_berkas_ta') && $this->db->get_where('syarat_berkas_ta', ['nama_berkas' => $arg5])->num_rows() > 0)) {
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

        $existing = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim, 'kode_berkas' => $kode_berkas])->row_array();

        $data = [
            'file_name'         => $file_name,
            'status_verifikasi' => $status,
            'updated_at'        => date('Y-m-d H:i:s')
        ];

        if (!empty($nama_berkas) && $this->db->field_exists('nama_berkas', 'pendaftaran_berkas')) {
            $data['nama_berkas'] = $nama_berkas;
        }

        if ($catatan !== null && $this->db->field_exists('catatan', 'pendaftaran_berkas')) {
            $data['catatan'] = $catatan;
        }

        if ($existing) {
            $this->db->where('id', $existing['id']);
            $res = $this->db->update('pendaftaran_berkas', $data);
        } else {
            $data['nim']         = $nim;
            $data['kode_berkas'] = $kode_berkas;
            $data['created_at']  = date('Y-m-d H:i:s');
            $res = $this->db->insert('pendaftaran_berkas', $data);
        }

        // Fail-safe sync: If a berkas is uploaded/set to Pending, ensure pendaftaran_ta's status_approval_admin resets to Pending
        if ($status === 'Pending' && $this->db->table_exists('pendaftaran_ta')) {
            $p_row = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
            if ($p_row && $p_row['status_approval_admin'] === 'Approved') {
                $up_ta = ['status_approval_admin' => 'Pending'];
                if (($p_row['status_approval_wali'] ?? '') === 'Approved') {
                    $up_ta['current_stage'] = 'Admin Layanan';
                }
                $this->db->where('nim', $nim)->update('pendaftaran_ta', $up_ta);
            }
        }

        return $res;
    }

    public function update_verifikasi($nim, $status_input, $catatan = '', $extra_catatan = null, $berkas_valid = array(), $berkas_kurang = array()) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return false;
        }

        $active_syarat  = $this->get_active_syarat_berkas();
        $student_berkas = $this->get_student_berkas_map($nim);

        $invalid_items = array();
        $has_invalid   = false;
        $has_pending   = false;

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

            $file_name = $student_berkas[$kode]['file_name'] ?? '';
            if (empty($file_name)) {
                $p_row = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
                $file_name = $p_row['file_' . $kode] ?? ('berkas_' . $kode . '_' . $nim . '.pdf');
            }

            $this->save_student_berkas($nim, $kode, $file_name, $st);

            // Update legacy column if exists
            if (in_array($kode, array('ksm', 'transkrip', 'pernyataan', 'bebas_lab'))) {
                $this->db->where('nim', $nim)->update('pendaftaran_ta', array('status_' . $kode => $st));
            }

            if ($st === 'Invalid') {
                $has_invalid = true;
                $invalid_items[] = $sb['nama_berkas'] . ' (Tidak Sesuai / Invalid)';
            } elseif ($st === 'Pending') {
                $has_pending = true;
            }
        }

        if ($has_invalid || (is_string($status_input) && in_array(strtolower($status_input), array('reject', 'rejected')))) {
            $status_approval = 'Rejected';
            $berkas_kurang_str = !empty($invalid_items) ? implode(', ', $invalid_items) : ($extra_catatan ?: 'Dokumen Persyaratan Perlu Revisi');
            $current_stage = 'Admin Layanan';
        } elseif ($has_pending) {
            $status_approval = 'Pending';
            $berkas_kurang_str = NULL;
            $current_stage = 'Admin Layanan';
        } else {
            $status_approval = 'Approved';
            $berkas_kurang_str = NULL;
            $current_stage = 'Koordinator TA';
        }

        $data = array(
            'status_approval_admin' => $status_approval,
            'catatan_admin'         => $catatan,
            'berkas_kurang'         => $berkas_kurang_str,
            'current_stage'         => $current_stage
        );

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_ta', $data);
    }


    public function reset_verifikasi_pending($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return false;
        }

        $data = array(
            'status_approval_admin' => 'Pending',
            'catatan_admin'         => NULL,
            'berkas_kurang'         => NULL,
            'current_stage'         => 'Admin Layanan',
            'status_ksm'            => 'Pending',
            'status_transkrip'      => 'Pending',
            'status_pernyataan'     => 'Pending',
            'status_bebas_lab'      => 'Pending'
        );

        $this->db->where('nim', $nim);
        $this->db->update('pendaftaran_ta', $data);

        $this->db->where('nim', $nim);
        return $this->db->update('pendaftaran_berkas', ['status_verifikasi' => 'Pending']);
    }

    public function get_batch_details_by_nims($nims) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($nims)) {
            return array();
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $has_kk  = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) $select .= ', m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, m.alamat';
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        $this->db->where_in('p.nim', $nims);
        
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    /**
     * Hitung total pengajuan untuk Paging
     */
    public function get_count_pengajuan($filter_status = null, $search = null, $cat = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return 0;
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($this->db->field_exists('is_submitted', 'pendaftaran_ta')) {
            $this->db->where('p.is_submitted', 1);
        }



        if ($filter_status && $filter_status !== 'all') {
            $this->db->where('p.status_approval_admin', $filter_status);
        }

        if ($search) {
            $this->db->group_start();
            if ($cat === 'nama' && $has_mhs) {
                $this->db->like('m.nama_depan', $search);
                $this->db->or_like('m.nama_belakang', $search);
            } elseif ($cat === 'nim') {
                $this->db->like('p.nim', $search);
            } elseif ($cat === 'judul') {
                $this->db->like('p.judul_1', $search);
            } elseif ($cat === 'prodi' && $has_mhs) {
                $this->db->like('m.prodi', $search);
                $this->db->or_like('m.konsentrasi_dkv', $search);
            } else {
                if ($has_mhs) {
                    $this->db->like('m.nama_depan', $search);
                    $this->db->or_like('m.nama_belakang', $search);
                    $this->db->or_like('m.prodi', $search);
                }
                $this->db->or_like('p.nim', $search);
                $this->db->or_like('p.judul_1', $search);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * Ambil daftar pengajuan berkas mahasiswa untuk Admin Layanan dengan Paging (Limit & Offset)
     */
    public function get_all_pengajuan($filter_status = null, $search = null, $limit = 5, $offset = 0, $cat = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $has_mhs   = $this->db->table_exists('mahasiswa');
        $has_kk    = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) {
            $select .= ', COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.konsentrasi_dkv, m.alamat';
            if ($this->db->field_exists('prodi', 'mahasiswa')) $select .= ', m.prodi';
            if ($this->db->field_exists('email', 'mahasiswa')) $select .= ', m.email';
            if ($this->db->field_exists('no_hp', 'mahasiswa')) $select .= ', m.no_hp';
        }
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        if ($this->db->field_exists('is_submitted', 'pendaftaran_ta')) {
            $this->db->where('p.is_submitted', 1);
        }



        if ($filter_status && $filter_status !== 'all') {
            $this->db->where('p.status_approval_admin', $filter_status);
        }

        if ($search) {
            $this->db->group_start();
            if ($cat === 'nama' && $has_mhs) {
                $this->db->like('m.nama_depan', $search);
                $this->db->or_like('m.nama_belakang', $search);
            } elseif ($cat === 'nim') {
                $this->db->like('p.nim', $search);
            } elseif ($cat === 'judul') {
                $this->db->like('p.judul_1', $search);
            } elseif ($cat === 'prodi' && $has_mhs) {
                $this->db->like('m.prodi', $search);
                $this->db->or_like('m.konsentrasi_dkv', $search);
            } else {
                if ($has_mhs) {
                    $this->db->like('m.nama_depan', $search);
                    $this->db->or_like('m.nama_belakang', $search);
                    $this->db->or_like('m.prodi', $search);
                }
                $this->db->or_like('p.nim', $search);
                $this->db->or_like('p.judul_1', $search);
            }
            $this->db->group_end();
        }

        $this->db->order_by("CASE 
            WHEN p.status_approval_admin = 'Pending' AND p.status_approval_wali = 'Approved' THEN 1 
            WHEN p.status_approval_admin = 'Rejected' THEN 2 
            WHEN p.status_approval_admin = 'Approved' THEN 3 
            ELSE 4 END", "ASC", false);
        $this->db->order_by('p.created_at', 'DESC');

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    /**
     * Autocomplete search untuk Admin Layanan LAA
     */
    public function autocomplete_search($term) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($term)) {
            return array();
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $this->db->select('p.nim, p.judul_1, p.status_approval_wali, p.status_approval_admin, m.nama_depan, m.nama_belakang, m.konsentrasi_dkv');
        $this->db->from('pendaftaran_ta p');
        if ($this->db->field_exists('is_submitted', 'pendaftaran_ta')) {
            $this->db->where('p.is_submitted', 1);
        }


        $this->db->group_start();
        if ($has_mhs) {
            $this->db->like('m.nama_depan', $term);
            $this->db->or_like('m.nama_belakang', $term);
        }
        $this->db->or_like('p.nim', $term);
        $this->db->or_like('p.judul_1', $term);
        $this->db->group_end();

        $this->db->limit(8);
        $query = $this->db->get();
        return $query ? $query->result_array() : array();
    }

    /**
     * Hitung statistik berkas untuk kartu metrik Admin Layanan
     */
    public function get_stats() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0);
        }

        $total = $this->db->count_all_results('pendaftaran_ta');
        
        $pending = $this->db->where('status_approval_admin', 'Pending')
                            ->where('status_approval_wali', 'Approved')
                            ->count_all_results('pendaftaran_ta');
                            
        $approved = $this->db->where('status_approval_admin', 'Approved')
                             ->count_all_results('pendaftaran_ta');
                             
        $rejected = $this->db->where('status_approval_admin', 'Rejected')
                             ->count_all_results('pendaftaran_ta');

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
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $has_mhs = $this->db->table_exists('mahasiswa');
        $has_kk  = $this->db->table_exists('kelompok_keahlian') && $this->db->field_exists('id_kk', 'pendaftaran_ta');

        $select = 'p.*';
        if ($has_mhs) {
            $select .= ', COALESCE(m.nama_depan, "Mahasiswa") as nama_depan, COALESCE(m.nama_belakang, "") as nama_belakang, m.konsentrasi_dkv, m.alamat';
            if ($this->db->field_exists('prodi', 'mahasiswa')) $select .= ', m.prodi';
            if ($this->db->field_exists('email', 'mahasiswa')) $select .= ', m.email';
            if ($this->db->field_exists('no_hp', 'mahasiswa')) $select .= ', m.no_hp';
        }
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');
        $this->db->where('p.nim', $nim);
        
        $query = $this->db->get();
        return $query ? $query->row_array() : null;
    }

    /**
     * Resolve URL file PDF untuk preview modal
     */
    public function resolve_pdf_url($filename) {
        if (empty($filename)) {
            return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
        }
        if (strpos($filename, 'uploads/') === 0 && file_exists(FCPATH . $filename)) {
            return base_url($filename);
        }
        $sub_path = 'uploads/persyaratan_ta/' . $filename;
        if (file_exists(FCPATH . $sub_path)) {
            return base_url($sub_path);
        }
        return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
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

        // 1. Process student's existing recorded files in pendaftaran_berkas
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

            $items[] = array(
                'kode'      => $kode,
                'nama'      => $nama_berkas,
                'short'     => $this->get_short_berkas_label($nama_berkas, $kode),
                'status'    => $st,
                'file_name' => $file_name,
                'file_url'  => $file_url
            );
        }

        // 2. Append active requirements if student hasn't uploaded them yet
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

            $items[] = array(
                'kode'      => $kode,
                'nama'      => $sb['nama_berkas'],
                'short'     => $this->get_short_berkas_label($sb['nama_berkas'], $kode),
                'status'    => $st,
                'file_name' => $file_name,
                'file_url'  => $file_url
            );
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

        $this->_ensure_tables();
        $this->db->where_in('nim', $nims);
        $rows = $this->db->get('pendaftaran_berkas')->result_array();

        $student_maps = array();
        foreach ($rows as $r) {
            $student_maps[$r['nim']][$r['kode_berkas']] = $r;
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

            // 1. Process student's existing recorded files in pendaftaran_berkas
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

                $items[] = array(
                    'kode'      => $kode,
                    'nama'      => $nama_berkas,
                    'short'     => $this->get_short_berkas_label($nama_berkas, $kode),
                    'status'    => $st,
                    'file_name' => $file_name,
                    'file_url'  => $file_url
                );
            }

            // 2. Append active requirements if student hasn't uploaded them yet
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

                $items[] = array(
                    'kode'      => $kode,
                    'nama'      => $sb['nama_berkas'],
                    'short'     => $this->get_short_berkas_label($sb['nama_berkas'], $kode),
                    'status'    => $st,
                    'file_name' => $file_name,
                    'file_url'  => $file_url
                );
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
}
