<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kp_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->init_tables();
    }

    /**
     * Pastikan 4 tabel KP tersedia di database sesuai struktur tanpa data awal (seed).
     */
    public function init_tables() {
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        try {
            // 1. Tabel Utama Pengajuan KP (tb_kp)
            if (!$this->db->table_exists('tb_kp')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `tb_kp` (
                    `id_kp` VARCHAR(100) NOT NULL,
                    `id_mhs` VARCHAR(100) NOT NULL,
                    `peminatan` VARCHAR(255) NOT NULL DEFAULT '',
                    `sks_lulus` INT NOT NULL DEFAULT 0,
                    `sks_berjalan` INT NOT NULL DEFAULT 0,
                    `nama_perusahaan` VARCHAR(255) NOT NULL DEFAULT '',
                    `alamat_perusahaan` TEXT NOT NULL,
                    `kota` VARCHAR(255) NOT NULL DEFAULT '',
                    `kode_pos` VARCHAR(100) NOT NULL DEFAULT '',
                    `nama_tujuan` VARCHAR(255) NOT NULL DEFAULT '',
                    `jabatan` VARCHAR(255) NOT NULL DEFAULT '',
                    `posisi_pekerjaan` VARCHAR(255) NOT NULL DEFAULT '',
                    `telp_fax` VARCHAR(100) NOT NULL DEFAULT '',
                    `email_perusahaan` VARCHAR(255) NOT NULL DEFAULT '',
                    `cp` VARCHAR(100) NOT NULL DEFAULT '',
                    `status_file` ENUM(
                        'Draft',
                        'Sudah Input Formulir',
                        'Sudah Upload Persyaratan',
                        'Disetujui LAA',
                        'Revisi Persyaratan',
                        'Sudah Plot Pembimbing',
                        'Sudah Upload Laporan',
                        'Revisi Laporan',
                        'Selesai'
                    ) NOT NULL DEFAULT 'Sudah Input Formulir',
                    `date` DATE NOT NULL,
                    `status_preview` VARCHAR(100) NOT NULL DEFAULT 'Pending',
                    `tahun` VARCHAR(100) NOT NULL DEFAULT '',
                    `dosen_pembimbing1` VARCHAR(255) NOT NULL DEFAULT '',
                    `jenis_kp` VARCHAR(100) NOT NULL DEFAULT '',
                    PRIMARY KEY (`id_kp`),
                    KEY `idx_tb_kp_mhs` (`id_mhs`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }

            // 2. Tabel File Persyaratan KP (file_pendaftaran_kp) - Tanpa Doswal
            if (!$this->db->table_exists('file_pendaftaran_kp')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `file_pendaftaran_kp` (
                    `id` VARCHAR(64) NOT NULL,
                    `id_mhs` VARCHAR(64) NOT NULL,
                    `id_kp` VARCHAR(100) NOT NULL,
                    `nama` VARCHAR(64) NOT NULL,
                    `file` VARCHAR(255) NOT NULL,
                    `poin` INT NOT NULL DEFAULT 0,
                    `view_adminlaa` VARCHAR(64) NOT NULL DEFAULT '0',
                    `status_adminlaa` VARCHAR(64) NOT NULL DEFAULT 'Pending',
                    `komentar` VARCHAR(255) NOT NULL DEFAULT '',
                    `date` VARCHAR(64) NOT NULL,
                    `date_edit` VARCHAR(64) NOT NULL DEFAULT '',
                    `jenis_pendaftaran` ENUM('', 'pendaftaran_kp', 'pendaftaran_sidang', 'pendaftaran_bimbingan') NOT NULL DEFAULT 'pendaftaran_kp',
                    PRIMARY KEY (`id`),
                    KEY `idx_fp_kp_mhs` (`id_mhs`),
                    KEY `idx_fp_kp_idkp` (`id_kp`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }

            // 3. Tabel File Laporan & Penilaian KP (file_laporan_kp) - Tanpa Doswal
            if (!$this->db->table_exists('file_laporan_kp')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `file_laporan_kp` (
                    `id` VARCHAR(64) NOT NULL,
                    `id_mhs` VARCHAR(64) NOT NULL,
                    `id_kp` VARCHAR(100) NOT NULL,
                    `nama` VARCHAR(64) NOT NULL,
                    `file` VARCHAR(255) NOT NULL,
                    `poin` INT NOT NULL DEFAULT 0,
                    `view_adminlaa` VARCHAR(64) NOT NULL DEFAULT '0',
                    `status_adminlaa` VARCHAR(64) NOT NULL DEFAULT 'Pending',
                    `komentar` VARCHAR(255) NOT NULL DEFAULT '',
                    `date` VARCHAR(64) NOT NULL,
                    `date_edit` VARCHAR(64) NOT NULL DEFAULT '',
                    `jenis_pendaftaran` ENUM('', 'laporan_kp', 'pendaftaran_sidang', 'pendaftaran_bimbingan') NOT NULL DEFAULT 'laporan_kp',
                    PRIMARY KEY (`id`),
                    KEY `idx_fl_kp_mhs` (`id_mhs`),
                    KEY `idx_fl_kp_idkp` (`id_kp`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }

            // 4. Tabel Master Persyaratan KP Dinamis (syarat_berkas_kp) - Murni Struktur Tanpa Seed
            if (!$this->db->table_exists('syarat_berkas_kp')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `syarat_berkas_kp` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `kode_berkas` VARCHAR(64) NOT NULL UNIQUE,
                    `nama_berkas` VARCHAR(150) NOT NULL,
                    `deskripsi` VARCHAR(255) NULL,
                    `file_template` VARCHAR(255) NULL,
                    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                    `urutan` INT NOT NULL DEFAULT 1,
                    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }
        } catch (Throwable $e) {
            // Silent fallback
        }

        $this->db->db_debug = $prev_debug;
    }

    // =========================================================================
    // HELPER IDENTITAS MAHASISWA
    // =========================================================================

    public function get_target_mhs_ids($nim, $user_id = null) {
        $ids = [];
        if (!empty($user_id)) $ids[] = (string)$user_id;
        if (!empty($nim)) {
            $ids[] = (string)$nim;
            $ids[] = 'usr_mhs_' . $nim;
            $ids[] = 'mhs_' . $nim;
        }

        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
            if ($user_tbl && !empty($nim)) {
                $this->db->group_start();
                if ($this->db->field_exists('nim', $user_tbl)) $this->db->or_where('nim', $nim);
                if ($this->db->field_exists('nidn_nim', $user_tbl)) $this->db->or_where('nidn_nim', $nim);
                if ($this->db->field_exists('username', $user_tbl)) $this->db->or_where('username', $nim);
                $this->db->group_end();
                $u = $this->db->select('id')->limit(1)->get($user_tbl)->row_array();
                if ($u && !empty($u['id'])) {
                    $ids[] = (string)$u['id'];
                }
            }
        } catch (Throwable $e) {}
        $this->db->db_debug = $prev_debug;

        return array_values(array_unique(array_filter($ids)));
    }

    /**
     * Ambil data mahasiswa langsung dari database (tabel user / mahasiswa) tanpa fallback statis.
     */
    public function get_mahasiswa_db($nim, $user_id = null) {
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $result = [
            'id'    => $user_id ?: '',
            'nim'   => $nim ?: '',
            'nama'  => '',
            'prodi' => '',
            'email' => '',
            'no_hp' => '',
        ];

        try {
            $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
            if ($user_tbl) {
                $u_fields = $this->db->list_fields($user_tbl);
                $this->db->group_start();
                if (!empty($user_id) && in_array('id', $u_fields)) {
                    $this->db->or_where('id', $user_id);
                }
                if (!empty($nim)) {
                    if (in_array('nim', $u_fields)) $this->db->or_where('nim', $nim);
                    if (in_array('nidn_nim', $u_fields)) $this->db->or_where('nidn_nim', $nim);
                    if (in_array('username', $u_fields)) $this->db->or_where('username', $nim);
                }
                $this->db->group_end();
                $u = $this->db->limit(1)->get($user_tbl)->row_array();
                if ($u) {
                    $result['id']    = $u['id'] ?? $result['id'];
                    $result['nim']   = !empty($u['nim']) ? $u['nim'] : (!empty($u['nidn_nim']) ? $u['nidn_nim'] : ($u['username'] ?? $result['nim']));
                    $result['nama']  = $u['name'] ?? ($u['nama'] ?? '');
                    $result['prodi'] = $u['prodi'] ?? '';
                    $result['email'] = $u['email'] ?? '';
                    $result['no_hp'] = $u['no_hp'] ?? ($u['telepon'] ?? '');
                }
            }

            if ($this->db->table_exists('mahasiswa') && !empty($result['nim'])) {
                $m = $this->db->get_where('mahasiswa', ['nim' => $result['nim']])->row_array();
                if ($m) {
                    $nama_mhs = trim(($m['nama_depan'] ?? '') . ' ' . ($m['nama_belakang'] ?? ''));
                    if ($nama_mhs !== '') $result['nama'] = $nama_mhs;
                    if (!empty($m['prodi'])) $result['prodi'] = $m['prodi'];
                    elseif (!empty($m['konsentrasi_dkv'])) $result['prodi'] = $m['konsentrasi_dkv'];
                }
            }
        } catch (Throwable $e) {}

        $this->db->db_debug = $prev_debug;
        return $result;
    }

    public function resolve_dosen_name($identifier) {
        $identifier = trim((string)$identifier);
        if ($identifier === '' || $identifier === '-' || $identifier === '0') {
            return '';
        }

        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $resolved = $identifier;

        try {
            if ($this->db->table_exists('dosen')) {
                $d_fields = $this->db->list_fields('dosen');
                $this->db->group_start();
                if (in_array('id', $d_fields)) $this->db->or_where('id', $identifier);
                if (in_array('nip', $d_fields)) $this->db->or_where('nip', $identifier);
                if (in_array('nidn', $d_fields)) $this->db->or_where('nidn', $identifier);
                if (in_array('kode_dosen', $d_fields)) $this->db->or_where('kode_dosen', $identifier);
                $this->db->group_end();
                $d = $this->db->limit(1)->get('dosen')->row_array();
                if ($d) {
                    if (!empty($d['nama_dosen'])) $resolved = $d['nama_dosen'];
                    elseif (!empty($d['nama'])) $resolved = $d['nama'];
                    elseif (!empty($d['name'])) $resolved = $d['name'];
                }
            }

            if ($resolved === $identifier) {
                $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
                if ($user_tbl) {
                    $u_fields = $this->db->list_fields($user_tbl);
                    $this->db->group_start();
                    if (in_array('id', $u_fields)) $this->db->or_where('id', $identifier);
                    if (in_array('nim', $u_fields)) $this->db->or_where('nim', $identifier);
                    if (in_array('nidn_nim', $u_fields)) $this->db->or_where('nidn_nim', $identifier);
                    if (in_array('username', $u_fields)) $this->db->or_where('username', $identifier);
                    $this->db->group_end();
                    $u = $this->db->limit(1)->get($user_tbl)->row_array();
                    if ($u && !empty($u['name'])) {
                        $resolved = $u['name'];
                    }
                }
            }
        } catch (Throwable $e) {}

        $this->db->db_debug = $prev_debug;
        return $resolved;
    }

    // =========================================================================
    // MASTER PERSYARATAN KP DINAMIS (syarat_berkas_kp) - KHUSUS SUPER ADMIN
    // =========================================================================

    /**
     * [SUPER ADMIN DINAMIS] Mengambil semua master berkas persyaratan KP untuk panel admin.
     *
     * @return array Daftar seluruh dokumen persyaratan terurut berdasarkan kolom urutan
     * @affected_table syarat_berkas_kp
     */
    public function get_all_syarat_kp() {
        if (!$this->db->table_exists('syarat_berkas_kp')) return [];
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $rows = $this->db->order_by('urutan', 'ASC')->order_by('id', 'ASC')->get('syarat_berkas_kp')->result_array();
            $this->db->db_debug = $prev_debug;
            return $rows ?: [];
        } catch (Throwable $e) {
            $this->db->db_debug = $prev_debug;
            return [];
        }
    }

    /**
     * [MAHASISWA FORM DINAMIS] Mengambil daftar berkas persyaratan KP yang berstatus aktif (is_active = 1).
     * Digunakan untuk me-render input file dinamis pada formulir pendaftaran mahasiswa.
     *
     * @return array Daftar persyaratan aktif
     * @affected_table syarat_berkas_kp
     */
    public function get_active_syarat_kp() {
        if (!$this->db->table_exists('syarat_berkas_kp')) return [];
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $rows = $this->db->where('is_active', 1)->order_by('urutan', 'ASC')->order_by('id', 'ASC')->get('syarat_berkas_kp')->result_array();
            $this->db->db_debug = $prev_debug;
            return $rows ?: [];
        } catch (Throwable $e) {
            $this->db->db_debug = $prev_debug;
            return [];
        }
    }

    /**
     * [SUPER ADMIN] Menambah atau memperbarui master berkas persyaratan KP.
     *
     * @param  int|null $id   ID persyaratan (null jika tambah baru)
     * @param  array    $data Payload (kode_berkas, nama_berkas, is_required, is_active, urutan)
     * @return int|false      ID baris tersimpan
     * @affected_table syarat_berkas_kp
     */
    public function save_syarat_kp($id, $data) {
        if (!$this->db->table_exists('syarat_berkas_kp')) return false;
        if (!empty($id)) {
            $this->db->where('id', (int)$id)->update('syarat_berkas_kp', $data);
            return (int)$id;
        } else {
            // Cek jika kode_berkas sudah ada
            $exist = $this->db->get_where('syarat_berkas_kp', ['kode_berkas' => $data['kode_berkas']])->row_array();
            if ($exist) {
                $this->db->where('id', (int)$exist['id'])->update('syarat_berkas_kp', $data);
                return (int)$exist['id'];
            }
            $this->db->insert('syarat_berkas_kp', $data);
            return $this->db->insert_id();
        }
    }

    /**
     * [SUPER ADMIN] Mengubah status keaktifan (aktif/nonaktif) persyaratan berkas KP secara instan.
     *
     * @param  int $id ID berkas persyaratan
     * @return bool    True jika status berhasil di-toggle
     * @affected_table syarat_berkas_kp
     */
    public function toggle_syarat_kp($id) {
        if (!$this->db->table_exists('syarat_berkas_kp')) return false;
        $row = $this->db->get_where('syarat_berkas_kp', ['id' => (int)$id])->row_array();
        if (!$row) return false;
        $new_status = ((int)$row['is_active'] === 1) ? 0 : 1;
        return $this->db->where('id', (int)$id)->update('syarat_berkas_kp', ['is_active' => $new_status]);
    }

    /**
     * [SUPER ADMIN] Menghapus master persyaratan berkas KP dari database.
     *
     * @param  int $id ID berkas persyaratan
     * @return bool
     * @affected_table syarat_berkas_kp
     */
    public function delete_syarat_kp($id) {
        if (!$this->db->table_exists('syarat_berkas_kp')) return false;
        return $this->db->where('id', (int)$id)->delete('syarat_berkas_kp');
    }

    // =========================================================================
    // DATA UTAMA PENGAJUAN KP (tb_kp)
    // =========================================================================

    /**
     * [READ] Mengambil record pendaftaran KP milik mahasiswa berdasarkan NIM atau user_id.
     *
     * @param  string      $nim     Nomor Induk Mahasiswa
     * @param  string|null $user_id ID Akun User (opsional)
     * @return array|null           Data baris pengajuan KP lengkap beserta nama dosen pembimbing
     * @affected_table tb_kp
     */
    public function get_kp_by_mhs($nim, $user_id = null) {
        if (!$this->db->table_exists('tb_kp')) return null;
        $target_ids = $this->get_target_mhs_ids($nim, $user_id);
        if (empty($target_ids)) return null;

        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $row = null;
        try {
            $row = $this->db->where_in('id_mhs', $target_ids)
                ->order_by('date', 'DESC')
                ->limit(1)
                ->get('tb_kp')
                ->row_array();

            if (!$row && !empty($nim)) {
                $row = $this->db->get_where('tb_kp', ['id_kp' => 'kp_' . $nim])->row_array();
            }
        } catch (Throwable $e) {
            $row = null;
        }
        $this->db->db_debug = $prev_debug;

        if ($row) {
            $row['dosen_pembimbing1_nama'] = $this->resolve_dosen_name($row['dosen_pembimbing1'] ?? '');
        }

        return $row;
    }

    /**
     * [CREATE / UPDATE] Menyimpan data formulir instansi KP mahasiswa (Auto-Save Draft & Submit).
     * Menerapkan mekanisme upsert: update jika sudah ada pengajuan, insert jika baru pertama kali.
     *
     * @param  string      $nim     NIM mahasiswa
     * @param  string|null $user_id ID akun pengguna
     * @param  array       $payload Data instansi, alamat, kota, dan status pengajuan
     * @return string|false         ID KP (id_kp) yang disimpan
     * @affected_table tb_kp
     */
    public function save_kp($nim, $user_id, $payload) {
        if (!$this->db->table_exists('tb_kp')) return false;
        $existing = $this->get_kp_by_mhs($nim, $user_id);
        $fields = $this->db->list_fields('tb_kp');

        $clean_data = [];
        foreach ($payload as $k => $v) {
            if (in_array($k, $fields)) {
                $clean_data[$k] = $v;
            }
        }

        if ($existing) {
            $id_kp = $existing['id_kp'];
            $this->db->where('id_kp', $id_kp)->update('tb_kp', $clean_data);
            return $id_kp;
        } else {
            $id_kp = !empty($clean_data['id_kp']) ? $clean_data['id_kp'] : ('kp_' . $nim);
            $clean_data['id_kp']  = $id_kp;
            $clean_data['id_mhs'] = !empty($user_id) ? (string)$user_id : (string)$nim;
            if (!isset($clean_data['date'])) {
                $clean_data['date'] = date('Y-m-d');
            }
            if (!isset($clean_data['tahun'])) {
                $clean_data['tahun'] = date('Y');
            }
            if (!isset($clean_data['status_preview'])) {
                $clean_data['status_preview'] = '';
            }
            if (!isset($clean_data['dosen_pembimbing1'])) {
                $clean_data['dosen_pembimbing1'] = '';
            }
            $this->db->insert('tb_kp', $clean_data);
            return $id_kp;
        }
    }

    // =========================================================================
    // FILE PERSYARATAN KP (file_pendaftaran_kp)
    // =========================================================================

    /**
     * [READ] Mengambil seluruh berkas persyaratan dokumen pendaftaran KP yang sudah diunggah mahasiswa.
     *
     * @param  string      $nim     NIM mahasiswa
     * @param  string|null $id_kp   ID pengajuan KP (opsional)
     * @param  string|null $user_id ID akun mahasiswa
     * @return array                Array map berkas dengan indeks kode_berkas
     * @affected_table file_pendaftaran_kp
     */
    public function get_files_pendaftaran_kp($nim, $id_kp = null, $user_id = null) {
        if (!$this->db->table_exists('file_pendaftaran_kp')) return [];
        $target_ids = $this->get_target_mhs_ids($nim, $user_id);

        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $rows = [];
        try {
            $this->db->group_start();
            if (!empty($target_ids)) {
                $this->db->where_in('id_mhs', $target_ids);
            }
            if (!empty($id_kp)) {
                $this->db->or_where('id_kp', $id_kp);
            }
            $this->db->group_end();
            $rows = $this->db->get('file_pendaftaran_kp')->result_array();
        } catch (Throwable $e) {
            $rows = [];
        }
        $this->db->db_debug = $prev_debug;

        $map = [];
        foreach ($rows as $r) {
            $kode = trim((string)($r['nama'] ?? ''));
            if ($kode !== '') {
                $r['file_basename'] = basename((string)($r['file'] ?? ''));
                $map[$kode] = $r;
            }
        }
        return $map;
    }

    /**
     * [CREATE / UPDATE] Menyimpan data berkas pendaftaran KP mahasiswa ke tabel file_pendaftaran_kp.
     *
     * @param  string      $nim         NIM mahasiswa
     * @param  string|null $user_id     ID user
     * @param  string      $id_kp       ID pengajuan KP
     * @param  string      $kode_berkas Kode dokumen persyaratan dinamis
     * @param  string      $filename    Nama file PDF yang tersimpan di disk
     * @param  string      $status_laa  Status awal verifikasi ('Draft' atau 'Pending')
     * @return bool                     True jika operasi insert/update berhasil
     * @affected_table file_pendaftaran_kp
     */
    public function save_file_pendaftaran_kp($nim, $user_id, $id_kp, $kode_berkas, $filename, $status_laa = 'Draft') {
        if (!$this->db->table_exists('file_pendaftaran_kp')) return false;
        $fields = $this->db->list_fields('file_pendaftaran_kp');
        $target_ids = $this->get_target_mhs_ids($nim, $user_id);
        $id_record = 'fpkp_' . $nim . '_' . $kode_berkas;
        $rel_path = 'uploads/persyaratan_kp/' . $filename;
        $now = date('Y-m-d H:i:s');

        $existing = $this->db->group_start()
            ->where('id', $id_record)
            ->or_group_start()
                ->where_in('id_mhs', !empty($target_ids) ? $target_ids : [$nim])
                ->where('nama', $kode_berkas)
            ->group_end()
            ->group_end()
            ->limit(1)
            ->get('file_pendaftaran_kp')
            ->row_array();

        $data = [];
        if (in_array('id_kp', $fields))             $data['id_kp'] = $id_kp ?: ('kp_' . $nim);
        if (in_array('nama', $fields))              $data['nama'] = $kode_berkas;
        if (in_array('file', $fields))              $data['file'] = $rel_path;
        if (in_array('view_adminlaa', $fields))     $data['view_adminlaa'] = '0';
        if (in_array('status_adminlaa', $fields))   $data['status_adminlaa'] = $status_laa;
        if (in_array('komentar', $fields))          $data['komentar'] = '';
        if (in_array('date_edit', $fields))         $data['date_edit'] = $now;
        if (in_array('jenis_pendaftaran', $fields)) $data['jenis_pendaftaran'] = 'pendaftaran_kp';

        if ($existing) {
            return $this->db->where('id', $existing['id'])->update('file_pendaftaran_kp', $data);
        } else {
            if (in_array('id', $fields))     $data['id'] = $id_record;
            if (in_array('id_mhs', $fields)) $data['id_mhs'] = !empty($user_id) ? (string)$user_id : (string)$nim;
            if (in_array('poin', $fields))   $data['poin'] = 0;
            if (in_array('date', $fields))   $data['date'] = $now;
            return $this->db->insert('file_pendaftaran_kp', $data);
        }
    }

    /**
     * [UPDATE] Mengubah status semua berkas persyaratan KP mahasiswa dari 'Draft' menjadi 'Pending' untuk verifikasi LAA.
     *
     * @param  string      $nim     NIM mahasiswa
     * @param  string|null $user_id ID user
     * @param  string|null $id_kp   ID pengajuan KP
     * @return bool
     * @affected_table file_pendaftaran_kp
     */
    public function submit_files_pendaftaran_kp($nim, $user_id, $id_kp = null) {
        if (!$this->db->table_exists('file_pendaftaran_kp')) return false;
        $target_ids = $this->get_target_mhs_ids($nim, $user_id);
        $now = date('Y-m-d H:i:s');

        $this->db->group_start();
        if (!empty($target_ids)) {
            $this->db->where_in('id_mhs', $target_ids);
        } else {
            $this->db->where('id_mhs', (string)$nim);
        }
        if (!empty($id_kp)) {
            $this->db->or_where('id_kp', $id_kp);
        }
        $this->db->group_end();
        $this->db->where_in('status_adminlaa', ['Draft', 'draft', '']);

        return $this->db->update('file_pendaftaran_kp', [
            'status_adminlaa' => 'Pending',
            'view_adminlaa'   => '0',
            'date_edit'       => $now,
        ]);
    }

    /**
     * [DELETE] Menghapus dokumen persyaratan KP tertentu baik file fisik di disk maupun baris database.
     *
     * @param  string      $nim         NIM mahasiswa
     * @param  string|null $user_id     ID user
     * @param  string      $kode_berkas Kode berkas persyaratan
     * @return bool
     * @affected_table file_pendaftaran_kp
     */
    public function delete_file_pendaftaran_kp($nim, $user_id, $kode_berkas) {
        if (!$this->db->table_exists('file_pendaftaran_kp')) return false;
        $target_ids = $this->get_target_mhs_ids($nim, $user_id);
        $id_record = 'fpkp_' . $nim . '_' . $kode_berkas;

        $row = $this->db->group_start()
            ->where('id', $id_record)
            ->or_group_start()
                ->where_in('id_mhs', !empty($target_ids) ? $target_ids : [$nim])
                ->where('nama', $kode_berkas)
            ->group_end()
            ->group_end()
            ->limit(1)
            ->get('file_pendaftaran_kp')
            ->row_array();

        if ($row) {
            if (!empty($row['file'])) {
                $full = FCPATH . ltrim($row['file'], '/\\');
                if (is_file($full)) @unlink($full);
            }
            return $this->db->where('id', $row['id'])->delete('file_pendaftaran_kp');
        }
        return false;
    }

    // =========================================================================
    // FILE LAPORAN & PENILAIAN KP (file_laporan_kp)
    // =========================================================================

    /**
     * [READ] Mengambil berkas laporan akhir KP dan bukti penilaian lapangan yang diunggah mahasiswa.
     *
     * @param  string      $nim     NIM mahasiswa
     * @param  string|null $id_kp   ID pengajuan KP
     * @param  string|null $user_id ID user
     * @return array
     * @affected_table file_laporan_kp
     */
    public function get_files_laporan_kp($nim, $id_kp = null, $user_id = null) {
        if (!$this->db->table_exists('file_laporan_kp')) return [];
        $target_ids = $this->get_target_mhs_ids($nim, $user_id);

        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $rows = [];
        try {
            $this->db->group_start();
            if (!empty($target_ids)) {
                $this->db->where_in('id_mhs', $target_ids);
            }
            if (!empty($id_kp)) {
                $this->db->or_where('id_kp', $id_kp);
            }
            $this->db->group_end();
            $rows = $this->db->get('file_laporan_kp')->result_array();
        } catch (Throwable $e) {
            $rows = [];
        }
        $this->db->db_debug = $prev_debug;

        $map = [];
        foreach ($rows as $r) {
            $kode = trim((string)($r['nama'] ?? ''));
            if ($kode !== '') {
                $r['file_basename'] = basename((string)($r['file'] ?? ''));
                $map[$kode] = $r;
            }
        }
        return $map;
    }

    /**
     * [CREATE / UPDATE] Menyimpan berkas laporan akhir KP dan nilai pembimbing lapangan ke tabel file_laporan_kp.
     *
     * @param  string      $nim       NIM mahasiswa
     * @param  string|null $user_id   ID user
     * @param  string      $id_kp     ID pengajuan KP
     * @param  string      $nama_item 'laporan_kp' atau 'nilai_lapangan'
     * @param  string      $filename  Nama file tersimpan
     * @param  int         $poin      Poin nilai jika ada
     * @return bool
     * @affected_table file_laporan_kp
     */
    public function save_file_laporan_kp($nim, $user_id, $id_kp, $nama_item, $filename, $poin = 0) {
        if (!$this->db->table_exists('file_laporan_kp')) return false;
        $fields = $this->db->list_fields('file_laporan_kp');
        $target_ids = $this->get_target_mhs_ids($nim, $user_id);
        $id_record = 'flkp_' . $nim . '_' . $nama_item;
        $rel_path = 'uploads/laporan_kp/' . $filename;
        $now = date('Y-m-d H:i:s');

        $existing = $this->db->group_start()
            ->where('id', $id_record)
            ->or_group_start()
                ->where_in('id_mhs', !empty($target_ids) ? $target_ids : [$nim])
                ->where('nama', $nama_item)
            ->group_end()
            ->group_end()
            ->limit(1)
            ->get('file_laporan_kp')
            ->row_array();

        $data = [];
        if (in_array('id_kp', $fields))             $data['id_kp'] = $id_kp ?: ('kp_' . $nim);
        if (in_array('nama', $fields))              $data['nama'] = $nama_item;
        if (in_array('file', $fields))              $data['file'] = $rel_path;
        if (in_array('poin', $fields))              $data['poin'] = (int)$poin;
        if (in_array('view_adminlaa', $fields))     $data['view_adminlaa'] = '0';
        if (in_array('status_adminlaa', $fields))   $data['status_adminlaa'] = 'Pending';
        if (in_array('komentar', $fields))          $data['komentar'] = '';
        if (in_array('date_edit', $fields))         $data['date_edit'] = $now;
        if (in_array('jenis_pendaftaran', $fields)) $data['jenis_pendaftaran'] = 'laporan_kp';

        if ($existing) {
            return $this->db->where('id', $existing['id'])->update('file_laporan_kp', $data);
        } else {
            if (in_array('id', $fields))     $data['id'] = $id_record;
            if (in_array('id_mhs', $fields)) $data['id_mhs'] = !empty($user_id) ? (string)$user_id : (string)$nim;
            if (in_array('date', $fields))   $data['date'] = $now;
            return $this->db->insert('file_laporan_kp', $data);
        }
    }

    // =========================================================================
    // KALKULASI STATUS WORKFLOW 7 TAHAP KP
    // =========================================================================

    public function is_status_approved($st) {
        $s = strtolower(trim((string)$st));
        return in_array($s, ['approved', 'valid', 'acc', 'disetujui', 'setuju', '1', 'lulus', 'selesai']);
    }

    public function is_status_rejected($st) {
        $s = strtolower(trim((string)$st));
        return in_array($s, ['rejected', 'invalid', 'tolak', 'ditolak', 'revisi', 'perbaikan']);
    }

    public function get_workflow_summary($nim, $user_id = null) {
        $kp = $this->get_kp_by_mhs($nim, $user_id);
        $id_kp = $kp['id_kp'] ?? null;
        $syarat_list = $this->get_active_syarat_kp();
        $files_syarat = $this->get_files_pendaftaran_kp($nim, $id_kp, $user_id);
        $files_laporan = $this->get_files_laporan_kp($nim, $id_kp, $user_id);

        // 1. Cek apakah form pengajuan sudah diisi
        $has_form = !empty($kp) && !empty($kp['nama_perusahaan']);

        // 2. Evaluasi berkas persyaratan KP
        $total_syarat_wajib = 0;
        $uploaded_wajib = 0;
        $approved_syarat_count = 0;
        $rejected_syarat_count = 0;
        $total_syarat_checked = count($syarat_list);

        if (!empty($syarat_list)) {
            foreach ($syarat_list as $sb) {
                $k = $sb['kode_berkas'];
                $is_req = ((int)($sb['is_required'] ?? 1) === 1);
                if ($is_req) $total_syarat_wajib++;

                $f = $files_syarat[$k] ?? null;
                if ($f && !empty($f['file'])) {
                    if ($is_req) $uploaded_wajib++;
                    if ($this->is_status_approved($f['status_adminlaa'] ?? '')) {
                        $approved_syarat_count++;
                    } elseif ($this->is_status_rejected($f['status_adminlaa'] ?? '')) {
                        $rejected_syarat_count++;
                    }
                }
            }
        }

        $status_file_kp = $kp['status_file'] ?? '';
        $has_uploaded_syarat = $has_form && !empty($syarat_list) && !empty($files_syarat) && (
            ($total_syarat_wajib > 0 && $uploaded_wajib >= $total_syarat_wajib) ||
            ($total_syarat_wajib === 0 && count($files_syarat) > 0)
        );

        // 3. Verifikasi persyaratan oleh LAA
        $syarat_has_revisi = ($rejected_syarat_count > 0 || $status_file_kp === 'Revisi Persyaratan');
        $syarat_all_approved = $has_form && !$syarat_has_revisi && (
            ($total_syarat_checked > 0 && $approved_syarat_count >= $total_syarat_checked && !empty($files_syarat)) ||
            in_array($status_file_kp, ['Disetujui LAA', 'Sudah Plot Pembimbing', 'Sudah Upload Laporan', 'Revisi Laporan', 'Selesai'])
        );

        // 4. Plotting Pembimbing 1 oleh Koordinator KP
        $pembimbing1_raw = trim((string)($kp['dosen_pembimbing1'] ?? ''));
        $has_pembimbing1 = ($pembimbing1_raw !== '' && $pembimbing1_raw !== '-' && $pembimbing1_raw !== '0');

        // 5. Menu Upload Laporan KP Aktif jika syarat LAA disetujui & sudah di-plot Pembimbing 1
        $can_upload_laporan = ($syarat_all_approved && $has_pembimbing1);

        // 6. Status Laporan KP & Verifikasi LAA
        $laporan_item = $files_laporan['laporan_kp'] ?? (!empty($files_laporan) ? reset($files_laporan) : null);
        $has_uploaded_laporan = !empty($laporan_item['file']);
        $laporan_status_raw = $laporan_item['status_adminlaa'] ?? '';
        $laporan_is_approved = ($has_uploaded_laporan && $this->is_status_approved($laporan_status_raw)) || ($has_uploaded_laporan && $status_file_kp === 'Selesai');
        $laporan_is_rejected = ($has_uploaded_laporan && $this->is_status_rejected($laporan_status_raw)) || ($has_uploaded_laporan && $status_file_kp === 'Revisi Laporan');

        // 7. Selesai
        $is_selesai = $laporan_is_approved;

        // Tentukan nomor step aktif saat ini (1..7)
        $current_step_num = 1;
        if ($is_selesai) {
            $current_step_num = 7;
        } elseif ($has_uploaded_laporan) {
            $current_step_num = 6;
        } elseif ($can_upload_laporan) {
            $current_step_num = 5;
        } elseif ($syarat_all_approved) {
            $current_step_num = 4;
        } elseif ($has_uploaded_syarat) {
            $current_step_num = 3;
        } elseif ($has_form) {
            $current_step_num = 2;
        }

        return [
            'kp'                   => $kp,
            'syarat_list'          => $syarat_list,
            'files_syarat'         => $files_syarat,
            'files_laporan'        => $files_laporan,
            'laporan_item'         => $laporan_item,
            'has_form'             => $has_form,
            'has_uploaded_syarat'  => $has_uploaded_syarat,
            'syarat_has_revisi'    => $syarat_has_revisi,
            'syarat_all_approved'  => $syarat_all_approved,
            'has_pembimbing1'      => $has_pembimbing1,
            'pembimbing1_nama'     => $kp['dosen_pembimbing1_nama'] ?? '',
            'can_upload_laporan'   => $can_upload_laporan,
            'has_uploaded_laporan' => $has_uploaded_laporan,
            'laporan_is_approved'  => $laporan_is_approved,
            'laporan_is_rejected'  => $laporan_is_rejected,
            'is_selesai'           => $is_selesai,
            'current_step_num'     => $current_step_num,
        ];
    }
}
