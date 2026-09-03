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
                `file_name` VARCHAR(255) NOT NULL,
                `status_verifikasi` ENUM('Pending','Valid','Invalid') DEFAULT 'Pending',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `nim_kode` (`nim`, `kode_berkas`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
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

    public function save_student_berkas($nim, $kode_berkas, $file_name, $status = 'Pending') {
        $this->_ensure_tables();
        $existing = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim, 'kode_berkas' => $kode_berkas])->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id']);
            return $this->db->update('pendaftaran_berkas', [
                'file_name' => $file_name,
                'status_verifikasi' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->db->insert('pendaftaran_berkas', [
                'nim' => $nim,
                'kode_berkas' => $kode_berkas,
                'file_name' => $file_name,
                'status_verifikasi' => $status,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function update_verifikasi($nim, $status_input, $catatan = '', $extra_catatan = null, $berkas_valid = array(), $berkas_kurang = array()) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return false;
        }

        $active_syarat = $this->get_active_syarat_berkas();
        $student_berkas = $this->get_student_berkas_map($nim);

        // Check if explicit action string was passed (e.g. 'approve', 'Approved', 'reject', 'Rejected')
        $is_explicit_approve = (is_string($status_input) && (strtolower($status_input) === 'approve' || strtolower($status_input) === 'approved'));
        $is_explicit_reject  = (is_string($status_input) && (strtolower($status_input) === 'reject' || strtolower($status_input) === 'rejected'));

        if ($is_explicit_approve) {
            // Mark all active berkas for this student as Valid
            foreach ($active_syarat as $sb) {
                $kode = $sb['kode_berkas'];
                $file_name = $student_berkas[$kode]['file_name'] ?? '';
                if (empty($file_name) && isset($this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array()['file_' . $kode])) {
                    $file_name = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array()['file_' . $kode];
                }
                if (empty($file_name)) {
                    $file_name = 'berkas_' . $kode . '_' . $nim . '.pdf';
                }
                $this->save_student_berkas($nim, $kode, $file_name, 'Valid');
            }

            $data = array(
                'status_approval_admin' => 'Approved',
                'catatan_admin'         => $catatan ?: (is_string($extra_catatan) ? $extra_catatan : NULL),
                'berkas_kurang'         => NULL,
                'current_stage'         => 'Koordinator TA',
                'status_ksm'            => 'Valid',
                'status_transkrip'      => 'Valid',
                'status_pernyataan'     => 'Valid',
                'status_bebas_lab'      => 'Valid'
            );

            $this->db->where('nim', $nim);
            return $this->db->update('pendaftaran_ta', $data);
        }

        // If array of status or berkas_valid / berkas_kurang passed
        $invalid_items = array();
        $all_valid = true;

        if ($is_explicit_reject) {
            $all_valid = false;
        }

        foreach ($active_syarat as $sb) {
            $kode = $sb['kode_berkas'];
            
            // Determine status for this berkas
            $st = 'Valid';
            if (is_array($status_input) && isset($status_input[$kode])) {
                $st = $status_input[$kode];
            } elseif (in_array($kode, (array)$berkas_kurang)) {
                $st = 'Invalid';
            } elseif (in_array($kode, (array)$berkas_valid)) {
                $st = 'Valid';
            }

            $file_name = $student_berkas[$kode]['file_name'] ?? '';
            if (empty($file_name) && isset($this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array()['file_' . $kode])) {
                $file_name = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array()['file_' . $kode];
            }

            if (!empty($file_name)) {
                $this->save_student_berkas($nim, $kode, $file_name, $st);
            }

            if ($st === 'Invalid') {
                $all_valid = false;
                $invalid_items[] = $sb['nama_berkas'] . ' (Tidak Sesuai / Invalid)';
            }
        }

        if ($all_valid && !$is_explicit_reject) {
            $status_approval = 'Approved';
            $berkas_kurang_str = NULL;
            $current_stage = 'Koordinator TA';
        } else {
            $status_approval = 'Rejected';
            $berkas_kurang_str = !empty($invalid_items) ? implode(', ', $invalid_items) : ($extra_catatan ?: 'Dokumen Persyaratan Perlu Revisi');
            $current_stage = 'Admin Layanan';
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
        if ($has_mhs) $select .= ', m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp';
        if ($has_kk)  $select .= ', kk.nama_kk, kk.kode_kk';

        $this->db->select($select);
        $this->db->from('pendaftaran_ta p');
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        if ($has_kk)  $this->db->join('kelompok_keahlian kk', 'kk.id = p.id_kk', 'left');

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
        if ($has_mhs) $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');

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
        if ($has_mhs) $select .= ', m.nama_depan, m.nama_belakang, m.prodi, m.konsentrasi_dkv, m.email, m.no_hp, m.alamat';
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

        foreach ($active_syarat as $sb) {
            $kode = $sb['kode_berkas'];
            $st = 'Pending';

            if (isset($map[$kode]['status_verifikasi']) && $map[$kode]['status_verifikasi'] !== 'Pending') {
                $st = $map[$kode]['status_verifikasi'];
            } elseif (isset($row['status_' . $kode]) && !empty($row['status_' . $kode])) {
                $st = $row['status_' . $kode];
            } elseif (isset($row['status_approval_admin']) && $row['status_approval_admin'] === 'Approved') {
                $st = 'Valid';
            } elseif (isset($map[$kode]['status_verifikasi'])) {
                $st = $map[$kode]['status_verifikasi'];
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

            $file_name = $map[$kode]['file_name'] ?? ($row['file_' . $kode] ?? '');
            $file_url  = $this->resolve_pdf_url($file_name);

            $items[] = array(
                'kode'      => $kode,
                'nama'      => $sb['nama_berkas'],
                'short'     => strtoupper(substr($sb['nama_berkas'], 0, 3)),
                'status'    => $st,
                'file_name' => $file_name,
                'file_url'  => $file_url
            );
        }

        return array(
            'valid_count'   => $valid_count,
            'invalid_count' => $invalid_count,
            'pending_count' => $pending_count,
            'total_count'   => count($active_syarat),
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

            foreach ($active_syarat as $sb) {
                $kode = $sb['kode_berkas'];
                $st = 'Pending';

                if (isset($map[$kode]['status_verifikasi']) && $map[$kode]['status_verifikasi'] !== 'Pending') {
                    $st = $map[$kode]['status_verifikasi'];
                } elseif (isset($s_row['status_' . $kode]) && !empty($s_row['status_' . $kode])) {
                    $st = $s_row['status_' . $kode];
                } elseif (isset($s_row['status_approval_admin']) && $s_row['status_approval_admin'] === 'Approved') {
                    $st = 'Valid';
                } elseif (isset($map[$kode]['status_verifikasi'])) {
                    $st = $map[$kode]['status_verifikasi'];
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

                $file_name = $map[$kode]['file_name'] ?? ($s_row['file_' . $kode] ?? '');
                $file_url  = $this->resolve_pdf_url($file_name);

                $items[] = array(
                    'kode'      => $kode,
                    'nama'      => $sb['nama_berkas'],
                    'short'     => strtoupper(substr($sb['nama_berkas'], 0, 3)),
                    'status'    => $st,
                    'file_name' => $file_name,
                    'file_url'  => $file_url
                );
            }

            $summaries[$nim] = array(
                'valid_count'   => $valid_count,
                'invalid_count' => $invalid_count,
                'pending_count' => $pending_count,
                'total_count'   => count($active_syarat),
                'items'         => $items
            );
        }

        return $summaries;
    }
}
