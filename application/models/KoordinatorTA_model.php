<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_ensure_columns_exist();
    }

    private function _ensure_columns_exist() {
        if (!$this->db->table_exists('pendaftaran_ta')) return;
        $fields = $this->db->list_fields('pendaftaran_ta');
        $new_cols = array(
            'pembimbing_1'            => "VARCHAR(50) DEFAULT NULL",
            'pembimbing_2'            => "VARCHAR(50) DEFAULT NULL",
            'penguji_1'               => "VARCHAR(50) DEFAULT NULL",
            'penguji_2'               => "VARCHAR(50) DEFAULT NULL",
            'tgl_sidang'              => "DATE DEFAULT NULL",
            'jam_mulai_sidang'        => "TIME DEFAULT NULL",
            'jam_selesai_sidang'      => "TIME DEFAULT NULL",
            'ruangan_sidang'          => "VARCHAR(100) DEFAULT NULL",
            'status_approval_koor'    => "VARCHAR(50) DEFAULT 'Pending'",
            'catatan_koor'            => "TEXT DEFAULT NULL",
            'current_stage'           => "VARCHAR(100) DEFAULT 'Koordinator TA'",
            'peminatan'               => "VARCHAR(100) DEFAULT NULL",
            'nilai_akhir_sidang'      => "DECIMAL(5,2) DEFAULT NULL",
            'grade_sidang'            => "VARCHAR(10) DEFAULT NULL",
            'status_kelulusan_sidang' => "VARCHAR(50) DEFAULT 'Belum Dinilai'",
            'detail_penilaian_sidang' => "LONGTEXT DEFAULT NULL",
            'tgl_penilaian_sidang'    => "DATETIME DEFAULT NULL",
            'status_publish_sidang'   => "VARCHAR(50) DEFAULT 'Draft'",
            'tgl_publish_sidang'      => "DATETIME DEFAULT NULL",
            'versi_penilaian'         => "INT(11) DEFAULT 1",
        );
        foreach ($new_cols as $col => $type) {
            if (!in_array($col, $fields)) {
                $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col}` {$type}");
            }
        }

        // Buat tabel history log penilaian sidang jika belum ada
        if (!$this->db->table_exists('history_penilaian_sidang')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `history_penilaian_sidang` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `nim` VARCHAR(50) NOT NULL,
                    `nama_mahasiswa` VARCHAR(150) NULL,
                    `tahun_akademik` VARCHAR(20) NULL,
                    `versi` INT(11) NOT NULL DEFAULT 1,
                    `prodi` VARCHAR(50) NULL,
                    `peminatan` VARCHAR(100) NULL,
                    `nilai_akhir` DECIMAL(5,2) NULL,
                    `grade` VARCHAR(10) NULL,
                    `status_kelulusan` VARCHAR(50) NULL,
                    `detail_penilaian` LONGTEXT NULL,
                    `status_publish` VARCHAR(50) NOT NULL DEFAULT 'Draft',
                    `tgl_publish` DATETIME NULL,
                    `catatan` TEXT NULL,
                    `aksi` VARCHAR(50) NOT NULL DEFAULT 'Input Penilaian',
                    `actor_nip` VARCHAR(50) NULL,
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_nim` (`nim`),
                    KEY `idx_tahun` (`tahun_akademik`),
                    KEY `idx_created` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // Buat tabel history log plotting penguji jika belum ada
        if (!$this->db->table_exists('history_plotting_penguji')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `history_plotting_penguji` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `nim` VARCHAR(50) NOT NULL,
                    `nama_mahasiswa` VARCHAR(150) NULL,
                    `penguji_1_lama` VARCHAR(50) NULL,
                    `penguji_2_lama` VARCHAR(50) NULL,
                    `nama_penguji_1_lama` VARCHAR(150) NULL,
                    `nama_penguji_2_lama` VARCHAR(150) NULL,
                    `penguji_1_baru` VARCHAR(50) NOT NULL,
                    `penguji_2_baru` VARCHAR(50) NOT NULL,
                    `nama_penguji_1_baru` VARCHAR(150) NULL,
                    `nama_penguji_2_baru` VARCHAR(150) NULL,
                    `aksi` VARCHAR(50) NOT NULL DEFAULT 'Penetapan Penguji',
                    `keterangan` TEXT NULL,
                    `actor_nip` VARCHAR(50) NULL,
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_nim` (`nim`),
                    KEY `idx_created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // Buat tabel history log plotting TA (Pembimbing & Penguji terpadu) jika belum ada
        if (!$this->db->table_exists('history_plotting_ta')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `history_plotting_ta` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `kategori` VARCHAR(50) NOT NULL DEFAULT 'Pembimbing',
                    `nim` VARCHAR(50) NOT NULL,
                    `nama_mahasiswa` VARCHAR(150) NULL,
                    `dosen_1_lama` VARCHAR(50) NULL,
                    `dosen_2_lama` VARCHAR(50) NULL,
                    `nama_dosen_1_lama` VARCHAR(150) NULL,
                    `nama_dosen_2_lama` VARCHAR(150) NULL,
                    `dosen_1_baru` VARCHAR(50) NULL,
                    `dosen_2_baru` VARCHAR(50) NULL,
                    `nama_dosen_1_baru` VARCHAR(150) NULL,
                    `nama_dosen_2_baru` VARCHAR(150) NULL,
                    `aksi` VARCHAR(50) NOT NULL DEFAULT 'Penetapan Dosen',
                    `keterangan` TEXT NULL,
                    `actor_nip` VARCHAR(50) NULL,
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_nim` (`nim`),
                    KEY `idx_kategori` (`kategori`),
                    KEY `idx_created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        } else {
            // Pastikan kolom kategori bertipe VARCHAR agar menampung 'Sidang TA'
            $this->db->query("ALTER TABLE `history_plotting_ta` MODIFY COLUMN `kategori` VARCHAR(50) NOT NULL DEFAULT 'Pembimbing'");
        }

        // Buat tabel master rubrik sidang dinamis jika belum ada
        if (!$this->db->table_exists('master_rubrik_sidang')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `master_rubrik_sidang` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `prodi` VARCHAR(50) NOT NULL,
                    `peminatan` VARCHAR(100) NOT NULL,
                    `judul_rubrik` VARCHAR(150) NOT NULL,
                    `kriteria_json` LONGTEXT NOT NULL,
                    `total_bobot` INT(11) NOT NULL DEFAULT 100,
                    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_prodi_peminatan` (`prodi`, `peminatan`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
            $this->_seed_default_master_rubrik();
        } else {
            $count = $this->db->count_all('master_rubrik_sidang');
            if ($count === 0) {
                $this->_seed_default_master_rubrik();
            }
        }
    }

    // Ambil list semua dosen pembimbing dari database (role: dosen)
    public function get_dosen_list() {
        $list = array();

        // 1. Ambil dari tabel users yang memiliki role 'dosen'
        if ($this->db->table_exists('users') && $this->db->table_exists('roles')) {
            $this->db->select('u.id, u.name as nama_dosen, u.nidn_nim as nip, u.email, u.no_hp');
            $this->db->from('users u');
            $this->db->join('roles r', 'r.id = u.role_id');
            $this->db->where('r.name', 'dosen');
            $this->db->where('u.status', 'active');
            $this->db->order_by('u.name', 'ASC');
            $usersDosen = $this->db->get()->result_array();
            
            foreach ($usersDosen as $ud) {
                if (!empty($ud['nip'])) {
                    $list[$ud['nip']] = array(
                        'nip' => (string)$ud['nip'],
                        'nama_dosen' => $ud['nama_dosen'],
                        'email' => $ud['email'] ?? '',
                        'prodi' => 'Informatika'
                    );
                }
            }
        }

        // 2. Lengkapi juga dari tabel dosen_wali
        if ($this->db->table_exists('dosen_wali')) {
            $dwList = $this->db->get('dosen_wali')->result_array();
            foreach ($dwList as $dw) {
                if (!empty($dw['nip']) && !isset($list[$dw['nip']])) {
                    $list[$dw['nip']] = array(
                        'nip' => (string)$dw['nip'],
                        'nama_dosen' => $dw['nama_dosen'],
                        'email' => $dw['email'] ?? '',
                        'prodi' => $dw['jurusan'] ?? 'Informatika'
                    );
                }
            }
        }

        return array_values($list);
    }

    // Get List All Mahasiswa Mendaftar TA untuk Koordinator TA (Real Database Records)
    public function get_all_mahasiswa_ta() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $this->db->select('m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.alamat, m.kota, m.provinsi, m.email, m.no_hp, p.*, COALESCE(dw.nama_dosen, dw_alt.nama_dosen, "Dosen Wali") as nama_dosen_wali, dw.nip as nip_dosen_wali, COALESCE(dw1.nama_dosen, u1.name, p.pembimbing_1) as nama_pembimbing_1, COALESCE(dw2.nama_dosen, u2.name, p.pembimbing_2) as nama_pembimbing_2');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('(SELECT nim, MIN(nama_depan) as nama_depan, MIN(nama_belakang) as nama_belakang, MIN(konsentrasi_dkv) as konsentrasi_dkv, MIN(alamat) as alamat, MIN(kota) as kota, MIN(provinsi) as provinsi, MIN(email) as email, MIN(no_hp) as no_hp, MIN(nip_dosen_wali) as nip_dosen_wali FROM mahasiswa GROUP BY nim) m', 'm.nim = p.nim', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw', 'dw.nip = m.nip_dosen_wali', 'left');
        $this->db->join('dosen_wali dw_alt', 'dw_alt.id = p.id_dosen_wali', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nidn_nim, MIN(name) as name FROM users WHERE nidn_nim IS NOT NULL AND nidn_nim != "" GROUP BY nidn_nim) u1', 'u1.nidn_nim = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('(SELECT nidn_nim, MIN(name) as name FROM users WHERE nidn_nim IS NOT NULL AND nidn_nim != "" GROUP BY nidn_nim) u2', 'u2.nidn_nim = p.pembimbing_2', 'left');
        $this->db->where('p.is_submitted', 1);
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query ? $query->result_array() : array();

        // Normalisasi data
        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['konsentrasi_dkv'])) {
                $row['konsentrasi_dkv'] = 'Informatika';
            }
            if (empty($row['nama_dosen_wali'])) {
                $row['nama_dosen_wali'] = 'Dosen Wali';
            }
            if (empty($row['email'])) {
                $fn = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($row['nama_depan']));
                $ln = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($row['nama_belakang']));
                $row['email'] = ($fn ? $fn : 'mhs') . ($ln ? '.' . $ln : '.' . $row['nim']) . '@student.telkomuniversity.ac.id';
            }
            if (empty($row['status_approval_koor'])) {
                $row['status_approval_koor'] = 'Pending';
            }
            if (empty($row['current_stage'])) {
                $row['current_stage'] = 'Koordinator TA';
            }
        }

        return $result;
    }

    // Get Detail Mahasiswa dan Pendaftaran TA untuk Koordinator TA
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $this->db->select('m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.alamat as mhs_alamat, m.kota, m.provinsi, m.email, m.no_hp, p.*, COALESCE(dw.nama_dosen, dw_alt.nama_dosen, "Dosen Wali") as nama_dosen_wali, dw.nip as nip_dosen_wali, COALESCE(dw1.nama_dosen, u1.name, p.pembimbing_1) as nama_pembimbing_1, COALESCE(dw2.nama_dosen, u2.name, p.pembimbing_2) as nama_pembimbing_2');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('(SELECT nim, MIN(nama_depan) as nama_depan, MIN(nama_belakang) as nama_belakang, MIN(konsentrasi_dkv) as konsentrasi_dkv, MIN(alamat) as alamat, MIN(kota) as kota, MIN(provinsi) as provinsi, MIN(email) as email, MIN(no_hp) as no_hp, MIN(nip_dosen_wali) as nip_dosen_wali FROM mahasiswa GROUP BY nim) m', 'm.nim = p.nim', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw', 'dw.nip = m.nip_dosen_wali', 'left');
        $this->db->join('dosen_wali dw_alt', 'dw_alt.id = p.id_dosen_wali', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nidn_nim, MIN(name) as name FROM users WHERE nidn_nim IS NOT NULL AND nidn_nim != "" GROUP BY nidn_nim) u1', 'u1.nidn_nim = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('(SELECT nidn_nim, MIN(name) as name FROM users WHERE nidn_nim IS NOT NULL AND nidn_nim != "" GROUP BY nidn_nim) u2', 'u2.nidn_nim = p.pembimbing_2', 'left');
        $this->db->where('p.nim', $nim);
        $query = $this->db->get();
        $row = $query ? $query->row_array() : null;

        if (!$row && $this->db->table_exists('mahasiswa')) {
            $this->db->where('nim', $nim);
            $row = $this->db->get('mahasiswa')->row_array();
        }

        if ($row) {
            if (empty($row['alamat']) && !empty($row['mhs_alamat'])) {
                $row['alamat'] = $row['mhs_alamat'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['nama_dosen_wali'])) {
                $row['nama_dosen_wali'] = 'Dosen Wali';
            }
            if (empty($row['email'])) {
                $fn = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($row['nama_depan'] ?? 'mhs'));
                $ln = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($row['nama_belakang'] ?? ''));
                $row['email'] = ($fn ? $fn : 'mhs') . ($ln ? '.' . $ln : '.' . ($row['nim'] ?? '1')) . '@student.telkomuniversity.ac.id';
            }
            if (empty($row['status_approval_koor'])) {
                $row['status_approval_koor'] = 'Pending';
            }
            if (empty($row['current_stage'])) {
                $row['current_stage'] = 'Koordinator TA';
            }
        }

        return $row;
    }

    // Approval / Reject Pendaftaran TA oleh Koordinator TA dengan AJAX & Validasi Pembimbing
    public function update_approval_koor_ajax($nim, $status, $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        // Cek data pendaftaran mahasiswa
        $this->db->where('nim', $nim);
        $curr = $this->db->get('pendaftaran_ta')->row_array();
        if (!$curr) {
            return array('status' => false, 'message' => 'Data pendaftaran mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
        }

        // Validasi: Harus sudah disetujui Dosen Wali & Admin LAA sebelum Koordinator TA bisa approve
        if ($status === 'Approved') {
            $statusWali = $curr['status_approval_wali'] ?? 'Pending';
            if ($statusWali !== 'Approved') {
                return array(
                    'status' => false, 
                    'message' => 'Persetujuan ditolak! Berkas mahasiswa harus terlebih dahulu disetujui oleh Dosen Wali.'
                );
            }

            $statusAdmin = $curr['status_approval_admin'] ?? 'Pending';
            if ($statusAdmin !== 'Approved') {
                return array(
                    'status' => false, 
                    'message' => 'Persetujuan ditolak! Berkas mahasiswa harus terlebih dahulu disetujui oleh Admin Layanan Akademik (LAA).'
                );
            }

            // Validasi Dosen Pembimbing 1 & 2 wajib dipilih
            if (empty($pembimbing_1) || empty($pembimbing_2)) {
                return array(
                    'status' => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 wajib dipilih sebelum menyetujui pendaftaran TA!'
                );
            }

            // Validasi: Pembimbing 1 dan Pembimbing 2 TIDAK BOLEH SAMA
            if ($pembimbing_1 === $pembimbing_2) {
                return array(
                    'status' => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 tidak boleh sama! Silakan pilih dua dosen yang berbeda.'
                );
            }
        }

        // Validasi Catatan saat Reject
        if ($status === 'Rejected' && empty(trim($catatan))) {
            return array(
                'status' => false, 
                'message' => 'Catatan revisi / alasan penolakan wajib diisi jika memilih status Reject!'
            );
        }

        $p1_lama = $curr['pembimbing_1'] ?? null;
        $p2_lama = $curr['pembimbing_2'] ?? null;
        $is_changed = (string)$p1_lama !== (string)$pembimbing_1 || (string)$p2_lama !== (string)$pembimbing_2;
        $aksi = (empty($p1_lama) && empty($p2_lama)) ? 'Penetapan Awal Pembimbing' : 'Perubahan Pembimbing';

        $data = array(
            'status_approval_koor' => $status, // 'Approved' / 'Rejected'
            'catatan_koor'         => trim($catatan),
            'updated_at'           => date('Y-m-d H:i:s')
        );

        if ($status === 'Approved') {
            $data['pembimbing_1']  = $pembimbing_1;
            $data['pembimbing_2']  = $pembimbing_2;
            $data['current_stage'] = 'Ketua KK';
        } else {
            $data['current_stage'] = 'Admin Layanan';
        }

        $this->db->where('nim', $nim);
        $update = $this->db->update('pendaftaran_ta', $data);

        if ($update) {
            // Rekam ke history log jika disetujui atau terjadi perubahan pembimbing
            if ($status === 'Approved' && $is_changed) {
                $this->record_history_ta('Pembimbing', $nim, $p1_lama, $p2_lama, $pembimbing_1, $pembimbing_2, $aksi, $catatan);
            }

            return array(
                'status'  => true, 
                'message' => ($status === 'Approved') ? 'Pendaftaran TA berhasil disetujui dan Dosen Pembimbing telah ditetapkan!' : 'Pendaftaran TA berhasil ditolak dengan catatan revisi.'
            );
        } else {
            return array('status' => false, 'message' => 'Gagal memperbarui database.');
        }
    }

    // Ambil detail lengkap sekumpulan mahasiswa terpilih (untuk Cek Dokumen Massal)
    public function get_batch_details_by_nims($nims) {
        if (empty($nims) || !is_array($nims) || !$this->db->table_exists('pendaftaran_ta')) return array();

        $this->db->select('
            p.*, 
            m.nama_depan, 
            m.nama_belakang, 
            m.prodi, 
            m.konsentrasi_dkv, 
            m.email, 
            m.no_hp,
            m.nip_dosen_wali,
            COALESCE(dw.nama_dosen, dw_alt.nama_dosen, "Dosen Wali") as nama_dosen_wali,
            COALESCE(dw1.nama_dosen, u1.name, p.pembimbing_1) as nama_pembimbing_1,
            COALESCE(dw2.nama_dosen, u2.name, p.pembimbing_2) as nama_pembimbing_2
        ');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('(SELECT nim, MIN(nama_depan) as nama_depan, MIN(nama_belakang) as nama_belakang, MIN(prodi) as prodi, MIN(konsentrasi_dkv) as konsentrasi_dkv, MIN(email) as email, MIN(no_hp) as no_hp, MIN(nip_dosen_wali) as nip_dosen_wali FROM mahasiswa GROUP BY nim) m', 'm.nim = p.nim', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw', 'dw.nip = m.nip_dosen_wali', 'left');
        $this->db->join('dosen_wali dw_alt', 'dw_alt.id = p.id_dosen_wali', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nidn_nim, MIN(name) as name FROM users WHERE nidn_nim IS NOT NULL AND nidn_nim != "" GROUP BY nidn_nim) u1', 'u1.nidn_nim = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('(SELECT nidn_nim, MIN(name) as name FROM users WHERE nidn_nim IS NOT NULL AND nidn_nim != "" GROUP BY nidn_nim) u2', 'u2.nidn_nim = p.pembimbing_2', 'left');
        $this->db->where_in('p.nim', $nims);
        $query = $this->db->get();
        $result = $query ? $query->result_array() : array();

        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            if (empty($row['nama_dosen_wali'])) {
                $row['nama_dosen_wali'] = 'Dosen Wali';
            }
            if (empty($row['email'])) {
                $fn = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($row['nama_depan']));
                $ln = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($row['nama_belakang']));
                $row['email'] = ($fn ? $fn : 'mhs') . ($ln ? '.' . $ln : '.' . $row['nim']) . '@student.telkomuniversity.ac.id';
            }
        }

        return $result;
    }

    // Approval Pendaftaran TA Massal (Batch / Multi-Select) oleh Koordinator TA dengan Opsi Per-Mahasiswa
    public function batch_approval_koor_ajax($nims, $status = 'Approved', $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null, $penguji_1 = null, $penguji_2 = null, $plottings = array()) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Tidak ada mahasiswa yang dipilih.');
        }

        // Format $plottings jika dikirim dalam bentuk array list [{nim, pemb1, pemb2}, ...]
        $plottingsMap = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $item) {
                if (is_array($item) && !empty($item['nim'])) {
                    $plottingsMap[(string)$item['nim']] = $item;
                }
            }
        }

        $successCount = 0;
        $failedList = array();

        foreach ($nims as $nim) {
            $nimStr = (string)$nim;
            $this->db->where('nim', $nimStr);
            $curr = $this->db->get('pendaftaran_ta')->row_array();
            if (!$curr) {
                $failedList[] = "NIM {$nimStr} (Data tidak ditemukan)";
                continue;
            }

            // Validasi per-mahasiswa saat Approve: harus disetujui Dosen Wali & Admin LAA
            if ($status === 'Approved') {
                $statusWali = $curr['status_approval_wali'] ?? 'Pending';
                $statusAdmin = $curr['status_approval_admin'] ?? 'Pending';

                if (strcasecmp($statusWali, 'Approved') !== 0) {
                    $failedList[] = "NIM {$nimStr} (Belum disetujui Dosen Wali)";
                    continue;
                }
                if (strcasecmp($statusAdmin, 'Approved') !== 0) {
                    $failedList[] = "NIM {$nimStr} (Belum diverifikasi Admin Layanan)";
                    continue;
                }
            }

            // Tentukan Dosen Pembimbing untuk mahasiswa ini (individual vs global)
            $p1 = $pembimbing_1;
            $p2 = $pembimbing_2;
            $cat = $catatan;

            if (isset($plottingsMap[$nimStr])) {
                $p1 = $plottingsMap[$nimStr]['pembimbing_1'] ?? $p1;
                $p2 = $plottingsMap[$nimStr]['pembimbing_2'] ?? $p2;
                if (isset($plottingsMap[$nimStr]['catatan_koor'])) {
                    $cat = $plottingsMap[$nimStr]['catatan_koor'];
                }
            }

            if ($status === 'Approved') {
                if (empty($p1) || empty($p2)) {
                    $failedList[] = "NIM {$nimStr} (Pembimbing 1 & 2 wajib dipilih)";
                    continue;
                }
                if ($p1 === $p2) {
                    $failedList[] = "NIM {$nimStr} (Pembimbing 1 dan 2 tidak boleh dosen yang sama)";
                    continue;
                }
            }

            $p1_lama = $curr['pembimbing_1'] ?? null;
            $p2_lama = $curr['pembimbing_2'] ?? null;
            $is_changed = (string)$p1_lama !== (string)$p1 || (string)$p2_lama !== (string)$p2;
            $aksi = (empty($p1_lama) && empty($p2_lama)) ? 'Penetapan Awal Pembimbing' : 'Perubahan Pembimbing';

            $data = array(
                'status_approval_koor' => $status,
                'catatan_koor'         => trim($cat),
                'updated_at'           => date('Y-m-d H:i:s')
            );

            if ($status === 'Approved') {
                $data['pembimbing_1']  = $p1;
                $data['pembimbing_2']  = $p2;
                if (!empty($penguji_1)) $data['penguji_1'] = $penguji_1;
                if (!empty($penguji_2)) $data['penguji_2'] = $penguji_2;
                $data['current_stage'] = 'Ketua KK';
            } else {
                $data['current_stage'] = 'Admin Layanan';
            }

            $this->db->where('nim', $nimStr);
            if ($this->db->update('pendaftaran_ta', $data)) {
                $successCount++;
                // Rekam ke history log jika ada penetapan / perubahan
                if ($status === 'Approved' && $is_changed) {
                    $this->record_history_ta('Pembimbing', $nimStr, $p1_lama, $p2_lama, $p1, $p2, $aksi, $cat);
                }
            } else {
                $failedList[] = "NIM {$nimStr} (Gagal update database)";
            }
        }

        if ($successCount === 0) {
            $msg = 'Gagal memproses approval massal.';
            if (!empty($failedList)) {
                $msg .= ' Alasan: ' . implode(', ', $failedList);
            }
            return array('status' => false, 'message' => $msg);
        }

        $resMsg = "Berhasil memproses {$successCount} mahasiswa sekaligus!";
        if (!empty($failedList)) {
            $resMsg .= " (" . count($failedList) . " dilewati: " . implode(', ', $failedList) . ")";
        }

        return array(
            'status' => true,
            'message' => $resMsg,
            'success_count' => $successCount,
            'failed_list' => $failedList
        );
    }

    // =========================================================
    // FITUR TAHAP PREVIEW 2 (PLOT DOSEN PENGUJI & JADWAL SIDANG)
    // =========================================================

    // Ambil daftar ruangan yang aktif / tersedia
    public function get_available_ruangan() {
        if (!$this->db->table_exists('ruangan')) {
            return array(
                array('id' => 1, 'kode_ruangan' => 'IK.01.05', 'nama_ruangan' => 'AULA Utama', 'lokasi' => 'Gedung Utama FIK Lantai 1', 'kapasitas' => 35),
                array('id' => 2, 'kode_ruangan' => 'IK.03.05', 'nama_ruangan' => 'Lab 3D printing Utama', 'lokasi' => 'Gedung FIK Lantai 3', 'kapasitas' => 35),
                array('id' => 3, 'kode_ruangan' => 'IK.01.09', 'nama_ruangan' => 'Lab Audio Utama', 'lokasi' => 'Gedung FIK Lantai 1', 'kapasitas' => 35),
                array('id' => 4, 'kode_ruangan' => 'IK.02.01', 'nama_ruangan' => 'Ruang Sidang 1 FIK', 'lokasi' => 'Gedung FIK Lantai 2', 'kapasitas' => 20),
                array('id' => 5, 'kode_ruangan' => 'IK.02.02', 'nama_ruangan' => 'Ruang Sidang 2 FIK', 'lokasi' => 'Gedung FIK Lantai 2', 'kapasitas' => 20)
            );
        }
        $query = $this->db->select('id, kode_ruangan, nama_ruangan, lokasi, kapasitas')
                          ->from('ruangan')
                          ->where('status', 'Tersedia')
                          ->order_by('nama_ruangan', 'ASC')
                          ->get();
        return $query->result_array();
    }

    // Ambil daftar mahasiswa yang berada di tahap Preview 2
    public function get_all_mahasiswa_preview2() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $this->db->select('
            m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.email, m.no_hp,
            p.id as id_pendaftaran, p.nim, p.judul_1, p.pembimbing_1, p.pembimbing_2,
            p.penguji_1, p.penguji_2, p.tgl_sidang, p.jam_mulai_sidang, p.jam_selesai_sidang, p.ruangan_sidang,
            p.status_approval_koor, p.status_approval_kk, p.current_stage, p.is_bimbingan_unlocked,
            dw1.nama_dosen as nama_pembimbing_1,
            dw2.nama_dosen as nama_pembimbing_2,
            dp1.nama_dosen as nama_penguji_1,
            dp2.nama_dosen as nama_penguji_2,
            r.nama_ruangan as detail_nama_ruangan
        ');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('(SELECT nim, MIN(nama_depan) as nama_depan, MIN(nama_belakang) as nama_belakang, MIN(konsentrasi_dkv) as konsentrasi_dkv, MIN(email) as email, MIN(no_hp) as no_hp FROM mahasiswa GROUP BY nim) m', 'm.nim = p.nim', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dp1', 'dp1.nip = p.penguji_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dp2', 'dp2.nip = p.penguji_2', 'left');
        $this->db->join('(SELECT nama_ruangan, MIN(kode_ruangan) as kode_ruangan FROM ruangan GROUP BY nama_ruangan) r', 'r.nama_ruangan = p.ruangan_sidang OR r.kode_ruangan = p.ruangan_sidang', 'left');
        
        // Hanya mahasiswa yang telah disetujui pendaftarannya oleh Koordinator TA & memiliki Dosen Pembimbing lengkap
        $this->db->where('p.status_approval_koor', 'Approved');
        $this->db->where('p.pembimbing_1 IS NOT NULL', null, false);
        $this->db->where("p.pembimbing_1 !=", "");
        $this->db->where('p.pembimbing_2 IS NOT NULL', null, false);
        $this->db->where("p.pembimbing_2 !=", "");
        
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();

        // Normalisasi data & tentukan status plotting preview 2
        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['konsentrasi_dkv'])) {
                $row['konsentrasi_dkv'] = 'Informatika';
            }

            // Cek status plotting penguji & jadwal
            $hasPenguji = (!empty($row['penguji_1']) && !empty($row['penguji_2']));
            $hasJadwal  = (!empty($row['tgl_sidang']) && !empty($row['ruangan_sidang']));

            if ($hasPenguji && $hasJadwal) {
                $row['status_preview2'] = 'Terjadwal'; // Sudah diplot Penguji & Ruangan
            } else if ($hasPenguji) {
                $row['status_preview2'] = 'Penguji Ditetapkan'; // Penguji sudah, jadwal belum
            } else {
                $row['status_preview2'] = 'Belum Diplot'; // Menunggu penetapan penguji
            }
        }

        return $result;
    }

    // Update Penguji & Jadwal Sidang Preview 2 untuk Mahasiswa Tunggal
    public function update_penguji_jadwal_preview2($nim, $penguji_1, $penguji_2, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $catatan = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nim) || empty($penguji_1) || empty($penguji_2)) {
            return array('status' => false, 'message' => 'NIM, Dosen Penguji 1, dan Dosen Penguji 2 wajib diisi!');
        }

        if ($penguji_1 === $penguji_2) {
            return array('status' => false, 'message' => 'Dosen Penguji 1 dan Dosen Penguji 2 tidak boleh orang yang sama!');
        }

        // Ambil data pendaftaran untuk cek Dosen Pembimbing
        $this->db->where('nim', $nim);
        $curr = $this->db->get('pendaftaran_ta')->row_array();
        if (!$curr) {
            return array('status' => false, 'message' => 'Data pendaftaran mahasiswa tidak ditemukan.');
        }

        // Validasi: Penguji tidak boleh sama dengan Pembimbing
        if ($penguji_1 === $curr['pembimbing_1'] || $penguji_1 === $curr['pembimbing_2'] || 
            $penguji_2 === $curr['pembimbing_1'] || $penguji_2 === $curr['pembimbing_2']) {
            return array('status' => false, 'message' => 'Dosen Penguji tidak boleh sama dengan Dosen Pembimbing mahasiswa!');
        }

        $p1_lama = $curr['penguji_1'] ?? null;
        $p2_lama = $curr['penguji_2'] ?? null;
        $is_changed = (string)$p1_lama !== (string)$penguji_1 || (string)$p2_lama !== (string)$penguji_2;
        $aksi = (empty($p1_lama) && empty($p2_lama)) ? 'Penetapan Awal' : 'Perubahan Penguji';

        $data = array(
            'penguji_1'          => $penguji_1,
            'penguji_2'          => $penguji_2,
            'tgl_sidang'         => !empty($tgl_sidang) ? $tgl_sidang : null,
            'jam_mulai_sidang'   => !empty($jam_mulai) ? $jam_mulai : null,
            'jam_selesai_sidang' => !empty($jam_selesai) ? $jam_selesai : null,
            'ruangan_sidang'     => !empty($ruangan) ? $ruangan : null,
            'updated_at'         => date('Y-m-d H:i:s')
        );

        $this->db->where('nim', $nim);
        $update = $this->db->update('pendaftaran_ta', $data);

        if ($update) {
            // Rekam ke tabel history_plotting_penguji jika terjadi penetapan/perubahan
            if ($is_changed) {
                $this->record_history_penguji($nim, $p1_lama, $p2_lama, $penguji_1, $penguji_2, $aksi, $catatan);
            }

            // Update / Insert ke ta_penguji jika tabel ada
            if ($this->db->table_exists('ta_penguji')) {
                $this->db->where('nim', $nim);
                $existPenguji = $this->db->get('ta_penguji')->row_array();
                if ($existPenguji) {
                    $this->db->where('nim', $nim)->update('ta_penguji', array(
                        'id_penguji_1' => $penguji_1,
                        'id_penguji_2' => $penguji_2
                    ));
                } else {
                    $this->db->insert('ta_penguji', array(
                        'nim'          => $nim,
                        'id_penguji_1' => $penguji_1,
                        'id_penguji_2' => $penguji_2,
                        'created_at'   => date('Y-m-d H:i:s')
                    ));
                }
            }

            // Update / Insert ke ta_jadwal_sidang jika tabel ada dan tanggal diisi
            if (!empty($tgl_sidang) && $this->db->table_exists('ta_jadwal_sidang')) {
                $this->db->where('nim', $nim);
                $existJadwal = $this->db->get('ta_jadwal_sidang')->row_array();
                if ($existJadwal) {
                    $this->db->where('nim', $nim)->update('ta_jadwal_sidang', array(
                        'tanggal' => $tgl_sidang,
                        'waktu'   => $jam_mulai,
                        'ruangan' => $ruangan
                    ));
                } else {
                    $this->db->insert('ta_jadwal_sidang', array(
                        'nim'        => $nim,
                        'tanggal'    => $tgl_sidang,
                        'waktu'      => $jam_mulai,
                        'ruangan'    => $ruangan,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                }
            }

            return array('status' => true, 'message' => 'Dosen Penguji & Jadwal Sidang Preview 2 berhasil disimpan!');
        } else {
            return array('status' => false, 'message' => 'Gagal memperbarui database.');
        }
    }

    // Rekam Log Histori Plotting Terpadu (Pembimbing / Penguji)
    public function record_history_ta($kategori, $nim, $d1_lama, $d2_lama, $d1_baru, $d2_baru, $aksi = 'Penetapan Dosen', $keterangan = '', $actor_nip = null) {
        if (!$this->db->table_exists('history_plotting_ta')) {
            $this->_ensure_columns_exist();
        }

        if (empty($actor_nip)) {
            $actor_nip = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19800202002';
        }

        // Ambil nama mahasiswa
        $nama_mhs = '';
        if ($this->db->table_exists('mahasiswa')) {
            $mhs = $this->db->where('nim', $nim)->get('mahasiswa')->row_array();
            if ($mhs) {
                $nama_mhs = trim(($mhs['nama_depan'] ?? '') . ' ' . ($mhs['nama_belakang'] ?? ''));
            }
        }

        // Helper nama dosen
        $dosen_list = $this->get_dosen_list();
        $get_dosen_name = function($nip) use ($dosen_list) {
            if (empty($nip)) return null;
            return $dosen_list[$nip]['nama_dosen'] ?? $nip;
        };

        $nama_d1_lama = $get_dosen_name($d1_lama);
        $nama_d2_lama = $get_dosen_name($d2_lama);
        $nama_d1_baru = $get_dosen_name($d1_baru);
        $nama_d2_baru = $get_dosen_name($d2_baru);

        $logData = array(
            'kategori'          => $kategori, // 'Pembimbing' / 'Penguji'
            'nim'               => $nim,
            'nama_mahasiswa'    => $nama_mhs,
            'dosen_1_lama'      => $d1_lama,
            'dosen_2_lama'      => $d2_lama,
            'nama_dosen_1_lama' => $nama_d1_lama,
            'nama_dosen_2_lama' => $nama_d2_lama,
            'dosen_1_baru'      => $d1_baru,
            'dosen_2_baru'      => $d2_baru,
            'nama_dosen_1_baru' => $nama_d1_baru,
            'nama_dosen_2_baru' => $nama_d2_baru,
            'aksi'              => $aksi,
            'keterangan'        => $keterangan,
            'actor_nip'         => $actor_nip,
            'created_at'        => date('Y-m-d H:i:s')
        );

        $this->db->insert('history_plotting_ta', $logData);

        // Kompatibilitas tabel history_plotting_penguji
        if ($kategori === 'Penguji' && $this->db->table_exists('history_plotting_penguji')) {
            $this->db->insert('history_plotting_penguji', array(
                'nim'                 => $nim,
                'nama_mahasiswa'      => $nama_mhs,
                'penguji_1_lama'      => $d1_lama,
                'penguji_2_lama'      => $d2_lama,
                'nama_penguji_1_lama' => $nama_d1_lama,
                'nama_penguji_2_lama' => $nama_d2_lama,
                'penguji_1_baru'      => $d1_baru,
                'penguji_2_baru'      => $d2_baru,
                'nama_penguji_1_baru' => $nama_d1_baru,
                'nama_penguji_2_baru' => $nama_d2_baru,
                'aksi'                => $aksi,
                'keterangan'          => $keterangan,
                'actor_nip'           => $actor_nip,
                'created_at'          => date('Y-m-d H:i:s')
            ));
        }

        return true;
    }

    // Ambil Data Histori Log Perubahan TA (Pembimbing, Penguji, & Sidang TA)
    public function get_history_ta($kategori = null, $nim = null, $limit = 100) {
        if (!$this->db->table_exists('history_plotting_ta')) {
            $this->_ensure_columns_exist();
        }

        $this->db->from('history_plotting_ta');
        if (!empty($kategori) && $kategori !== 'All') {
            $this->db->where('kategori', $kategori);
        }
        if (!empty($nim)) {
            $this->db->where('nim', $nim);
        }
        $this->db->order_by('created_at', 'DESC');
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result_array();
    }

    // Rekam Log Histori Plotting Penguji (Wrapper Backward Compatible)
    public function record_history_penguji($nim, $p1_lama, $p2_lama, $p1_baru, $p2_baru, $aksi = 'Penetapan Penguji', $keterangan = '', $actor_nip = null) {
        return $this->record_history_ta('Penguji', $nim, $p1_lama, $p2_lama, $p1_baru, $p2_baru, $aksi, $keterangan, $actor_nip);
    }

    // Ambil Data Histori Log Perubahan Penguji (Wrapper Backward Compatible)
    public function get_history_penguji($nim = null, $limit = 50) {
        return $this->get_history_ta('Penguji', $nim, $limit);
    }

    // Batch Plotting Penguji Preview 2 Massal (Mendukung Per-Mahasiswa Plotting)
    public function batch_penguji_preview2_ajax($nims, $penguji_1 = null, $penguji_2 = null, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $plottings = array()) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Pilih setidaknya satu mahasiswa.');
        }

        // Create a lookup from $plottings if provided
        $plot_map = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $p) {
                if (!empty($p['nim'])) {
                    $plot_map[$p['nim']] = $p;
                }
            }
        }

        $successCount = 0;
        $failedList = array();

        foreach ($nims as $nim) {
            $p1 = $plot_map[$nim]['penguji_1'] ?? $penguji_1;
            $p2 = $plot_map[$nim]['penguji_2'] ?? $penguji_2;

            if (empty($p1) || empty($p2)) {
                $failedList[] = "NIM {$nim} (Penguji belum lengkap)";
                continue;
            }

            if ($p1 === $p2) {
                $failedList[] = "NIM {$nim} (Penguji 1 & 2 sama)";
                continue;
            }

            $res = $this->update_penguji_jadwal_preview2($nim, $p1, $p2);
            if ($res['status']) {
                $successCount++;
            } else {
                $failedList[] = "NIM {$nim} ({$res['message']})";
            }
        }

        if ($successCount > 0) {
            $msg = "Berhasil menetapkan Dosen Penguji untuk {$successCount} mahasiswa.";
            if (!empty($failedList)) {
                $msg .= " Namun " . count($failedList) . " mahasiswa gagal: " . implode(', ', $failedList);
            }
            return array('status' => true, 'message' => $msg, 'success_count' => $successCount);
        } else {
            return array('status' => false, 'message' => 'Gagal menetapkan penguji: ' . implode(', ', $failedList));
        }
    }

    // =========================================================
    // TAHAP 3: PENJADWALAN SIDANG TA & MANAJEMEN RUANGAN DINAMIS
    // =========================================================

    // Ambil daftar mahasiswa yang mendaftar sidang / siap dijadwalkan sidang
    public function get_all_mahasiswa_sidang() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $this->db->select('
            m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.prodi as master_prodi, m.email, m.no_hp,
            p.id as id_pendaftaran, p.nim, p.judul_1, p.pembimbing_1, p.pembimbing_2,
            p.penguji_1, p.penguji_2, p.tgl_sidang, p.jam_mulai_sidang, p.jam_selesai_sidang, p.ruangan_sidang,
            p.status_approval_koor, p.status_approval_kk, p.current_stage,
            p.peminatan, p.nilai_akhir_sidang, p.grade_sidang, p.status_kelulusan_sidang,
            p.detail_penilaian_sidang, p.tgl_penilaian_sidang,
            COALESCE(p.status_publish_sidang, "Draft") as status_publish_sidang,
            p.tgl_publish_sidang,
            COALESCE(p.versi_penilaian, 1) as versi_penilaian,
            dw1.nama_dosen as nama_pembimbing_1,
            dw2.nama_dosen as nama_pembimbing_2,
            dp1.nama_dosen as nama_penguji_1,
            dp2.nama_dosen as nama_penguji_2,
            r.nama_ruangan as detail_nama_ruangan,
            r.kode_ruangan as detail_kode_ruangan,
            r.lokasi as detail_lokasi_ruangan,
            ps.id as id_pendaftaran_sidang,
            ps.status as status_verifikasi_sidang
        ');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('(SELECT nim, MIN(nama_depan) as nama_depan, MIN(nama_belakang) as nama_belakang, MIN(konsentrasi_dkv) as konsentrasi_dkv, MIN(prodi) as prodi, MIN(email) as email, MIN(no_hp) as no_hp FROM mahasiswa GROUP BY nim) m', 'm.nim = p.nim', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dp1', 'dp1.nip = p.penguji_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dp2', 'dp2.nip = p.penguji_2', 'left');
        $this->db->join('(SELECT nama_ruangan, MIN(kode_ruangan) as kode_ruangan, MIN(lokasi) as lokasi FROM ruangan GROUP BY nama_ruangan) r', 'r.nama_ruangan = p.ruangan_sidang OR r.kode_ruangan = p.ruangan_sidang', 'left');
        $this->db->join('(SELECT nim, MIN(id) as id, MIN(status) as status FROM ta_pendaftaran_sidang GROUP BY nim) ps', 'ps.nim = p.nim', 'left');
        
        // Mahasiswa yang telah disetujui Koordinator TA
        $this->db->where('p.status_approval_koor', 'Approved');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();

        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            $row['nama_lengkap'] = trim($row['nama_depan'] . ' ' . $row['nama_belakang']);
            $row['prodi'] = !empty($row['prodi_mhs']) ? $row['prodi_mhs'] : (!empty($row['master_prodi']) ? $row['master_prodi'] : 'Desain Komunikasi Visual');

            // Format / default peminatan jika belum di-set
            if (empty($row['peminatan'])) {
                if (stripos($row['prodi'], 'DKV') !== false || stripos($row['prodi'], 'Komunikasi') !== false) {
                    $row['peminatan'] = 'Multimedia';
                } elseif (stripos($row['prodi'], 'Interior Bisnis') !== false || stripos($row['prodi'], 'DIB') !== false) {
                    $row['peminatan'] = 'Spatial Branding';
                } elseif (stripos($row['prodi'], 'Interior') !== false || stripos($row['prodi'], 'DI') !== false) {
                    $row['peminatan'] = 'Komersial';
                } elseif (stripos($row['prodi'], 'Produk') !== false || stripos($row['prodi'], 'DP') !== false) {
                    $row['peminatan'] = 'Desain Industri';
                } else {
                    $row['peminatan'] = 'Multimedia';
                }
            }

            if (empty($row['status_kelulusan_sidang'])) {
                $row['status_kelulusan_sidang'] = 'Belum Dinilai';
            }

            $hasJadwal = (!empty($row['tgl_sidang']) && !empty($row['jam_mulai_sidang']) && !empty($row['ruangan_sidang']));
            if ($hasJadwal) {
                $row['status_sidang'] = 'Terjadwal';
            } else {
                $row['status_sidang'] = 'Belum Dijadwalkan';
            }
        }

        return $result;
    }

    // Tambah Ruangan Sidang Baru Dinamis
    public function tambah_ruangan_ajax($kode_ruangan, $nama_ruangan, $lokasi = '', $kapasitas = 30) {
        if (!$this->db->table_exists('ruangan')) {
            return array('status' => false, 'message' => 'Tabel ruangan tidak ditemukan.');
        }

        $kode_ruangan = strtoupper(trim($kode_ruangan));
        $nama_ruangan = trim($nama_ruangan);

        if (empty($kode_ruangan) || empty($nama_ruangan)) {
            return array('status' => false, 'message' => 'Kode Ruangan dan Nama Ruangan wajib diisi!');
        }

        // Cek duplikasi kode ruangan fisik (1 ruangan fisik = 1 fasilitas)
        $existing_ruangan = $this->db->select('id, nama_ruangan, kode_ruangan')->get('ruangan')->result();
        $canonicalize = function($str) {
            return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string)$str));
        };
        $target_canon = $canonicalize($kode_ruangan);
        foreach ($existing_ruangan as $row) {
            if (empty($row->kode_ruangan)) continue;
            $row_rooms = array_filter(array_map('trim', explode(',', $row->kode_ruangan)));
            foreach ($row_rooms as $r) {
                $r_clean = strtoupper(trim($r));
                $r_canon = $canonicalize($r);
                if ($kode_ruangan === $r_clean || (!empty($target_canon) && $target_canon === $r_canon)) {
                    return array('status' => false, 'message' => "Ruangan fisik '{$kode_ruangan}' sudah digunakan oleh fasilitas '{$row->nama_ruangan}'! Satu ruangan fisik hanya dapat digunakan oleh 1 fasilitas.");
                }
            }
        }

        $id_kategori = 1;
        if ($this->db->table_exists('kategori_ruangan')) {
            $kat = $this->db->get('kategori_ruangan')->row_array();
            if ($kat) $id_kategori = $kat['id'];
        }

        $data = array(
            'id_kategori'   => $id_kategori,
            'kode_ruangan'  => $kode_ruangan,
            'nama_ruangan'  => $nama_ruangan,
            'lokasi'        => !empty($lokasi) ? trim($lokasi) : 'Gedung Utama FIK',
            'kapasitas'     => intval($kapasitas) > 0 ? intval($kapasitas) : 30,
            'status'        => 'Tersedia',
            'created_at'    => date('Y-m-d H:i:s')
        );

        $inserted = $this->db->insert('ruangan', $data);
        if ($inserted) {
            $newId = $this->db->insert_id();
            return array(
                'status'  => true,
                'message' => "Ruangan '{$nama_ruangan}' ({$kode_ruangan}) berhasil ditambahkan!",
                'data'    => array_merge(['id' => $newId], $data)
            );
        } else {
            return array('status' => false, 'message' => 'Gagal menyimpan ruangan ke database.');
        }
    }

    // Hapus Ruangan Sidang Dinamis
    public function hapus_ruangan_ajax($id_ruangan) {
        if (!$this->db->table_exists('ruangan')) {
            return array('status' => false, 'message' => 'Tabel ruangan tidak ditemukan.');
        }

        $this->db->where('id', $id_ruangan);
        $ruang = $this->db->get('ruangan')->row_array();
        if (!$ruang) {
            return array('status' => false, 'message' => 'Ruangan tidak ditemukan.');
        }

        $this->db->where('id', $id_ruangan);
        $deleted = $this->db->delete('ruangan');

        if ($deleted) {
            return array('status' => true, 'message' => "Ruangan '{$ruang['nama_ruangan']}' berhasil dihapus.");
        } else {
            return array('status' => false, 'message' => 'Gagal menghapus ruangan.');
        }
    }

    // Helper Cek Bentrok Jadwal Sidang (Ruangan & Waktu)
    public function check_sidang_conflict($nim, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan) {
        if (!$this->db->table_exists('pendaftaran_ta') || empty($tgl_sidang) || empty($jam_mulai) || empty($ruangan)) {
            return null;
        }

        $jam_mulai_norm = substr(trim($jam_mulai), 0, 5);
        $jam_selesai_norm = !empty($jam_selesai) ? substr(trim($jam_selesai), 0, 5) : date('H:i', strtotime($jam_mulai_norm . ' + 90 minutes'));

        $startA = strtotime("2000-01-01 {$jam_mulai_norm}:00");
        $endA   = strtotime("2000-01-01 {$jam_selesai_norm}:00");

        // Cek apakah ada mahasiswa lain yang memakai ruangan yang sama di tanggal & jam bertabrakan
        $this->db->select('p.nim, m.nama_depan, m.nama_belakang, p.tgl_sidang, p.jam_mulai_sidang, p.jam_selesai_sidang, p.ruangan_sidang');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->where('p.nim !=', $nim);
        $this->db->where('p.tgl_sidang', $tgl_sidang);
        $this->db->where('p.ruangan_sidang', $ruangan);
        $this->db->where('p.jam_mulai_sidang IS NOT NULL', null, false);
        $this->db->where("p.jam_mulai_sidang !=", "");
        $query = $this->db->get();
        $conflicts = $query->result_array();

        foreach ($conflicts as $c) {
            $c_mulai = substr(trim($c['jam_mulai_sidang']), 0, 5);
            $c_selesai = !empty($c['jam_selesai_sidang']) ? substr(trim($c['jam_selesai_sidang']), 0, 5) : date('H:i', strtotime($c_mulai . ' + 90 minutes'));

            $startB = strtotime("2000-01-01 {$c_mulai}:00");
            $endB   = strtotime("2000-01-01 {$c_selesai}:00");

            // Check time overlap: (StartA < EndB) and (EndA > StartB)
            if (($startA < $endB) && ($endA > $startB)) {
                $mhsName = trim(($c['nama_depan'] ?? '') . ' ' . ($c['nama_belakang'] ?? ''));
                if (empty($mhsName)) $mhsName = 'Mahasiswa NIM ' . $c['nim'];
                return array(
                    'conflict' => true,
                    'type'     => 'ruangan',
                    'message'  => "Ruangan {$ruangan} sudah dijadwalkan untuk {$mhsName} ({$c['nim']}) pada tanggal {$tgl_sidang} pukul {$c_mulai} - {$c_selesai} WIB!"
                );
            }
        }
        return null;
    }

    // Update Jadwal Sidang Single Mahasiswa
    public function update_jadwal_sidang_ajax($nim, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan, $bypass_conflict = false) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nim) || empty($tgl_sidang) || empty($jam_mulai) || empty($ruangan)) {
            return array('status' => false, 'message' => 'NIM, Tanggal Sidang, Jam Mulai, dan Ruangan Sidang wajib diisi.');
        }

        // Cek bentrok jika tidak di-bypass
        if (!$bypass_conflict) {
            $conflict = $this->check_sidang_conflict($nim, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
            if ($conflict) {
                return array('status' => false, 'message' => $conflict['message'], 'is_conflict' => true);
            }
        }

        $data = array(
            'tgl_sidang'         => $tgl_sidang,
            'jam_mulai_sidang'   => $jam_mulai,
            'jam_selesai_sidang' => $jam_selesai,
            'ruangan_sidang'     => $ruangan
        );

        $this->db->where('nim', $nim);
        $update = $this->db->update('pendaftaran_ta', $data);

        if ($update) {
            // Update / Insert ke ta_jadwal_sidang jika tabel ada
            if ($this->db->table_exists('ta_jadwal_sidang')) {
                $this->db->where('nim', $nim);
                $existJadwal = $this->db->get('ta_jadwal_sidang')->row_array();
                if ($existJadwal) {
                    $this->db->where('nim', $nim)->update('ta_jadwal_sidang', array(
                        'tanggal' => $tgl_sidang,
                        'waktu'   => $jam_mulai,
                        'ruangan' => $ruangan
                    ));
                } else {
                    $this->db->insert('ta_jadwal_sidang', array(
                        'nim'        => $nim,
                        'tanggal'    => $tgl_sidang,
                        'waktu'      => $jam_mulai,
                        'ruangan'    => $ruangan,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                }
            }

            // Catat log histori penjadwalan sidang
            $jamStr = $jam_mulai . (!empty($jam_selesai) ? " - {$jam_selesai}" : "");
            $this->record_history_ta(
                'Sidang TA',
                $nim,
                '-',
                '-',
                "Tgl: {$tgl_sidang} ({$jamStr})",
                "Ruangan: {$ruangan}",
                'Penjadwalan Sidang',
                "Penetapan jadwal sidang TA: Tanggal {$tgl_sidang} pukul {$jamStr} di Ruangan {$ruangan}."
            );

            return array('status' => true, 'message' => 'Jadwal Sidang Tugas Akhir berhasil ditetapkan!');
        } else {
            return array('status' => false, 'message' => 'Gagal memperbarui jadwal sidang.');
        }
    }

    // Batch Update Jadwal Sidang Massal (Per-Mahasiswa Schedules)
    public function batch_jadwal_sidang_per_mhs_ajax($schedules) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($schedules) || !is_array($schedules)) {
            return array('status' => false, 'message' => 'Tidak ada data jadwal mahasiswa yang diproses.');
        }

        // 1. Cek bentrok internal di antara schedule yang dikirim bersamaan
        $seen = array();
        foreach ($schedules as $idx => $sch) {
            $nim = $sch['nim'] ?? '';
            $tgl = $sch['tgl_sidang'] ?? '';
            $mulai = substr(trim($sch['jam_mulai_sidang'] ?? ''), 0, 5);
            $selesai = !empty($sch['jam_selesai_sidang']) ? substr(trim($sch['jam_selesai_sidang']), 0, 5) : date('H:i', strtotime($mulai . ' + 90 minutes'));
            $ruang = $sch['ruangan_sidang'] ?? '';

            if (empty($nim) || empty($tgl) || empty($mulai) || empty($ruang)) continue;

            $startA = strtotime("2000-01-01 {$mulai}:00");
            $endA   = strtotime("2000-01-01 {$selesai}:00");

            foreach ($seen as $prev) {
                if ($prev['tgl'] === $tgl && $prev['ruang'] === $ruang) {
                    $startB = strtotime("2000-01-01 {$prev['mulai']}:00");
                    $endB   = strtotime("2000-01-01 {$prev['selesai']}:00");

                    if (($startA < $endB) && ($endA > $startB)) {
                        return array(
                            'status' => false,
                            'message' => "Jadwal bentrok antar mahasiswa terpilih! Mahasiswa NIM {$nim} dan NIM {$prev['nim']} dijadwalkan di ruangan {$ruang} pada jam yang bersamaan ({$mulai}-{$selesai} vs {$prev['mulai']}-{$prev['selesai']})."
                        );
                    }
                }
            }

            $seen[] = array('nim' => $nim, 'tgl' => $tgl, 'mulai' => $mulai, 'selesai' => $selesai, 'ruang' => $ruang);
        }

        // 2. Eksekusi update per-mahasiswa
        $successCount = 0;
        $failedList = array();

        foreach ($schedules as $sch) {
            $nim     = $sch['nim'] ?? '';
            $tgl     = $sch['tgl_sidang'] ?? '';
            $mulai   = $sch['jam_mulai_sidang'] ?? '';
            $selesai = $sch['jam_selesai_sidang'] ?? '';
            $ruang   = $sch['ruangan_sidang'] ?? '';

            if (empty($nim) || empty($tgl) || empty($mulai) || empty($ruang)) {
                $failedList[] = "NIM {$nim} (Data tanggal/waktu/ruangan belum lengkap)";
                continue;
            }

            $res = $this->update_jadwal_sidang_ajax($nim, $tgl, $mulai, $selesai, $ruang);
            if ($res['status']) {
                $successCount++;
            } else {
                $failedList[] = "NIM {$nim} ({$res['message']})";
            }
        }

        if ($successCount === 0) {
            $msg = 'Gagal menyimpan jadwal sidang mahasiswa.';
            if (!empty($failedList)) $msg .= ' ' . implode(', ', $failedList);
            return array('status' => false, 'message' => $msg);
        }

        $msg = "Berhasil menetapkan jadwal sidang individual untuk {$successCount} mahasiswa!";
        if (!empty($failedList)) {
            $msg .= " (" . count($failedList) . " gagal: " . implode(', ', $failedList) . ")";
        }

        return array(
            'status'        => true,
            'message'       => $msg,
            'success_count' => $successCount,
            'failed_list'   => $failedList
        );
    }

    // Legacy Batch Update Fallback (Multi-Select)
    public function batch_jadwal_sidang_ajax($nims, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan) {
        $schedules = array();
        if (is_array($nims)) {
            foreach ($nims as $n) {
                $schedules[] = array(
                    'nim'                => $n,
                    'tgl_sidang'         => $tgl_sidang,
                    'jam_mulai_sidang'   => $jam_mulai,
                    'jam_selesai_sidang' => $jam_selesai,
                    'ruangan_sidang'     => $ruangan
                );
            }
        }
        return $this->batch_jadwal_sidang_per_mhs_ajax($schedules);
    }

    // =========================================================
    // FITUR REVISI: PENILAIAN AKHIR SIDANG TA BERDASARKAN PRODI & PEMINATAN
    // =========================================================

    // Simpan Penilaian Akhir Sidang TA dengan Versi History & Snapshot Kriteria Mandiri
    public function simpan_penilaian_sidang_ajax($nim, $prodi, $peminatan, $nilai_akhir, $grade, $status_kelulusan, $detail_penilaian = array(), $catatan = '', $status_publish = 'Draft', $tgl_publish = null, $tahun_akademik = null) {
        try {
            $this->_ensure_columns_exist();

            if (!$this->db->table_exists('pendaftaran_ta')) {
                return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
            }

            if (empty($nim)) {
                return array('status' => false, 'message' => 'NIM mahasiswa wajib diisi.');
            }

            $exist = $this->db->where('nim', $nim)->get('pendaftaran_ta')->row_array();
            if (!$exist) {
                return array('status' => false, 'message' => 'Data pendaftaran tugas akhir mahasiswa tidak ditemukan.');
            }

            // Tentukan tahun akademik aktif jika kosong (contoh: 2026/2027)
            if (empty($tahun_akademik)) {
                $curYear = (int)date('Y');
                $curMonth = (int)date('n');
                $tahun_akademik = ($curMonth >= 8) ? "{$curYear}/" . ($curYear + 1) : ($curYear - 1) . "/{$curYear}";
            }

            // Hitung nomor versi penilaian berikutnya
            $currVer = 1;
            if ($this->db->table_exists('history_penilaian_sidang')) {
                $lastLog = $this->db->select_max('versi')
                                    ->where('nim', $nim)
                                    ->get('history_penilaian_sidang')
                                    ->row_array();
                if (!empty($lastLog['versi'])) {
                    $currVer = (int)$lastLog['versi'] + 1;
                }
            }

            // Format tanggal publish
            $nowStr = date('Y-m-d H:i:s');
            if ($status_publish === 'Published' && empty($tgl_publish)) {
                $tgl_publish = $nowStr;
            } elseif ($status_publish === 'Draft') {
                $tgl_publish = null;
            }

            // Normalisasi data detail kriteria menjadi snapshot mandiri
            $criteriaList = array();
            if (is_array($detail_penilaian)) {
                if (isset($detail_penilaian['criteria']) && is_array($detail_penilaian['criteria'])) {
                    $criteriaList = $detail_penilaian['criteria'];
                } elseif (isset($detail_penilaian[0]) && is_array($detail_penilaian[0])) {
                    $criteriaList = $detail_penilaian;
                }
            } elseif (is_string($detail_penilaian)) {
                $dec = json_decode($detail_penilaian, true);
                if (is_array($dec)) {
                    if (isset($dec['criteria']) && is_array($dec['criteria'])) {
                        $criteriaList = $dec['criteria'];
                    } else {
                        $criteriaList = $dec;
                    }
                }
            }

            // Snapshot struktur penilaian lengkap
            $snapshotData = array(
                'version'           => $currVer,
                'prodi'             => $prodi,
                'peminatan'         => $peminatan,
                'tahun_akademik'    => $tahun_akademik,
                'calculated_score'  => is_numeric($nilai_akhir) ? floatval($nilai_akhir) : 0,
                'grade'             => !empty($grade) ? $grade : '-',
                'status_kelulusan'  => !empty($status_kelulusan) ? $status_kelulusan : 'Lulus',
                'status_publish'    => $status_publish,
                'tgl_publish'       => $tgl_publish,
                'catatan'           => $catatan,
                'assessed_at'       => $nowStr,
                'criteria'          => $criteriaList
            );

            $detailJson = json_encode($snapshotData, JSON_UNESCAPED_UNICODE);

            $updateData = array(
                'peminatan'               => !empty($peminatan) ? $peminatan : ($exist['peminatan'] ?? 'Multimedia'),
                'nilai_akhir_sidang'      => is_numeric($nilai_akhir) ? floatval($nilai_akhir) : null,
                'grade_sidang'            => !empty($grade) ? $grade : null,
                'status_kelulusan_sidang' => !empty($status_kelulusan) ? $status_kelulusan : 'Lulus',
                'detail_penilaian_sidang' => $detailJson,
                'tgl_penilaian_sidang'    => $nowStr,
                'status_publish_sidang'   => $status_publish,
                'tgl_publish_sidang'      => $tgl_publish,
                'versi_penilaian'         => $currVer
            );

            if (!empty($catatan)) {
                $updateData['catatan_koor'] = $catatan;
            }

            $this->db->where('nim', $nim);
            $ok = $this->db->update('pendaftaran_ta', $updateData);

            if ($ok) {
                // Ambil data nama mahasiswa
                $mhs = $this->db->where('nim', $nim)->get('mahasiswa')->row_array();
                $namaMhs = $mhs ? trim(($mhs['nama_depan'] ?? '') . ' ' . ($mhs['nama_belakang'] ?? '')) : "Mahasiswa {$nim}";

                // Catat ke tabel riwayat khusus history_penilaian_sidang
                $actorNip = $this->session->userdata('nip') ?: '19800202002';
                $aksiLabel = ($currVer > 1) ? "Revisi Penilaian (Versi {$currVer})" : "Input Penilaian Awal";
                if ($status_publish === 'Republished') {
                    $aksiLabel = "Republish Penilaian (Versi {$currVer})";
                }

                if ($this->db->table_exists('history_penilaian_sidang')) {
                    $this->db->insert('history_penilaian_sidang', array(
                        'nim'              => $nim,
                        'nama_mahasiswa'   => $namaMhs,
                        'tahun_akademik'   => $tahun_akademik,
                        'versi'            => $currVer,
                        'prodi'            => $prodi,
                        'peminatan'        => $peminatan,
                        'nilai_akhir'      => is_numeric($nilai_akhir) ? floatval($nilai_akhir) : null,
                        'grade'            => $grade,
                        'status_kelulusan' => $status_kelulusan,
                        'detail_penilaian' => $detailJson,
                        'status_publish'   => $status_publish,
                        'tgl_publish'      => $tgl_publish,
                        'catatan'          => $catatan,
                        'aksi'             => $aksiLabel,
                        'actor_nip'        => $actorNip,
                        'created_at'       => $nowStr
                    ));
                }

                // Rekam ke tabel riwayat histori terpadu
                try {
                    $this->record_history_ta(
                        'Sidang TA',
                        $nim,
                        $exist['penguji_1'] ?? null,
                        $exist['penguji_2'] ?? null,
                        $exist['penguji_1'] ?? null,
                        $exist['penguji_2'] ?? null,
                        $aksiLabel,
                        "Nilai Akhir: {$nilai_akhir} (Grade: {$grade}) - Status: {$status_kelulusan} [Prodi: {$prodi}, Peminatan: {$peminatan}, Publikasi: {$status_publish}]" . (!empty($catatan) ? " | Catatan: {$catatan}" : "")
                    );
                } catch (\Throwable $thLog) {
                    log_message('error', 'Logging history error: ' . $thLog->getMessage());
                }

                return array(
                    'status'  => true,
                    'message' => "Penilaian Akhir Sidang untuk {$namaMhs} ({$nim}) berhasil disimpan (Versi {$currVer})!",
                    'data'    => array(
                        'nim'                   => $nim,
                        'versi'                 => $currVer,
                        'peminatan'             => $updateData['peminatan'],
                        'nilai_akhir'           => $updateData['nilai_akhir_sidang'],
                        'grade'                 => $updateData['grade_sidang'],
                        'status_kelulusan'      => $updateData['status_kelulusan_sidang'],
                        'status_publish'        => $status_publish,
                        'tgl_publish'           => $tgl_publish,
                        'tahun_akademik'        => $tahun_akademik
                    )
                );
            } else {
                $dbErr = $this->db->error();
                return array('status' => false, 'message' => 'Gagal menyimpan penilaian: ' . ($dbErr['message'] ?? 'Database error.'));
            }
        } catch (\Throwable $e) {
            log_message('error', 'simpan_penilaian_sidang_ajax error: ' . $e->getMessage());
            return array('status' => false, 'message' => 'Exception: ' . $e->getMessage());
        }
    }

    // Ambil Riwayat Versi Penilaian Mahasiswa
    public function get_history_penilaian_sidang($nim) {
        if (!$this->db->table_exists('history_penilaian_sidang')) {
            $this->_ensure_columns_exist();
            return array();
        }

        $rows = $this->db->where('nim', $nim)
                         ->order_by('versi', 'DESC')
                         ->order_by('created_at', 'DESC')
                         ->get('history_penilaian_sidang')
                         ->result_array();

        foreach ($rows as &$row) {
            if (!empty($row['detail_penilaian'])) {
                $row['detail_parsed'] = json_decode($row['detail_penilaian'], true);
            } else {
                $row['detail_parsed'] = null;
            }
        }

        return $rows;
    }

    // Publish / Republish / Set Jadwal Publikasi Nilai Sidang
    public function publish_penilaian_sidang_ajax($nim, $status_publish = 'Published', $tgl_publish = null, $catatan = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        $exist = $this->db->where('nim', $nim)->get('pendaftaran_ta')->row_array();
        if (!$exist) {
            return array('status' => false, 'message' => 'Data mahasiswa tidak ditemukan.');
        }

        if (empty($exist['nilai_akhir_sidang']) && $status_publish !== 'Draft') {
            return array('status' => false, 'message' => 'Mahasiswa belum memiliki nilai sidang untuk dipublikasikan.');
        }

        $nowStr = date('Y-m-d H:i:s');
        if ($status_publish === 'Published' && empty($tgl_publish)) {
            $tgl_publish = $nowStr;
        } elseif ($status_publish === 'Draft') {
            $tgl_publish = null;
        }

        $updateData = array(
            'status_publish_sidang' => $status_publish,
            'tgl_publish_sidang'    => $tgl_publish,
            'updated_at'            => $nowStr
        );

        $this->db->where('nim', $nim);
        $ok = $this->db->update('pendaftaran_ta', $updateData);

        if ($ok) {
            // Update juga snapshot di history jika ada
            if ($this->db->table_exists('history_penilaian_sidang')) {
                $actorNip = $this->session->userdata('nip') ?: '19800202002';
                $aksiStr = ($status_publish === 'Republished') ? 'Republish Nilai' : ($status_publish === 'Published' ? 'Publish Nilai' : 'Unpublish (Draft)');
                
                $this->record_history_ta(
                    'Sidang TA',
                    $nim,
                    $exist['penguji_1'] ?? null,
                    $exist['penguji_2'] ?? null,
                    $exist['penguji_1'] ?? null,
                    $exist['penguji_2'] ?? null,
                    $aksiStr,
                    "Status publikasi diubah menjadi {$status_publish} (Jadwal: " . ($tgl_publish ?: 'Draft') . ")" . (!empty($catatan) ? " | {$catatan}" : "")
                );
            }

            return array(
                'status'  => true,
                'message' => "Status publikasi nilai berhasil diperbarui menjadi {$status_publish}!",
                'data'    => array(
                    'nim'            => $nim,
                    'status_publish' => $status_publish,
                    'tgl_publish'    => $tgl_publish
                )
            );
        }

        return array('status' => false, 'message' => 'Gagal memperbarui status publikasi.');
    }

    // Ambil Detail Penilaian Sidang Mahasiswa Lengkap dengan Snapshot & Riwayat Versi
    public function get_detail_penilaian_sidang($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) return null;

        $this->db->select('
            p.nim, p.judul_1, p.pembimbing_1, p.pembimbing_2, p.penguji_1, p.penguji_2,
            p.tgl_sidang, p.jam_mulai_sidang, p.jam_selesai_sidang, p.ruangan_sidang,
            p.peminatan, p.nilai_akhir_sidang, p.grade_sidang, p.status_kelulusan_sidang,
            p.detail_penilaian_sidang, p.tgl_penilaian_sidang, p.catatan_koor,
            COALESCE(p.status_publish_sidang, "Draft") as status_publish_sidang,
            p.tgl_publish_sidang,
            COALESCE(p.versi_penilaian, 1) as versi_penilaian,
            m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.prodi as master_prodi,
            dw1.nama_dosen as nama_pembimbing_1, dw2.nama_dosen as nama_pembimbing_2,
            dp1.nama_dosen as nama_penguji_1, dp2.nama_dosen as nama_penguji_2
        ');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('(SELECT nim, MIN(nama_depan) as nama_depan, MIN(nama_belakang) as nama_belakang, MIN(konsentrasi_dkv) as konsentrasi_dkv, MIN(prodi) as prodi FROM mahasiswa GROUP BY nim) m', 'm.nim = p.nim', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dp1', 'dp1.nip = p.penguji_1', 'left');
        $this->db->join('(SELECT nip, MIN(nama_dosen) as nama_dosen FROM dosen_wali GROUP BY nip) dp2', 'dp2.nip = p.penguji_2', 'left');
        $this->db->where('p.nim', $nim);

        $row = $this->db->get()->row_array();
        if (!$row) return null;

        $row['nama_lengkap'] = trim(($row['nama_depan'] ?? '') . ' ' . ($row['nama_belakang'] ?? ''));
        $row['prodi'] = !empty($row['prodi_mhs']) ? $row['prodi_mhs'] : (!empty($row['master_prodi']) ? $row['master_prodi'] : 'Desain Komunikasi Visual');

        if (!empty($row['detail_penilaian_sidang']) && is_string($row['detail_penilaian_sidang'])) {
            $parsed = json_decode($row['detail_penilaian_sidang'], true);
            $row['detail_penilaian_parsed'] = $parsed;
        } else {
            $row['detail_penilaian_parsed'] = null;
        }

        // Ambil riwayat versi terdahulu
        $row['history_versions'] = $this->get_history_penilaian_sidang($nim);

        return $row;
    }

    // =========================================================
    // MASTER RUBRIK PENILAIAN DINAMIS & PENERAPAN MASSAL PER PRODI/PEMINATAN
    // =========================================================

    public function _seed_default_master_rubrik() {
        if (!$this->db->table_exists('master_rubrik_sidang')) return;

        $defaults = array(
            array(
                'prodi' => 'DKV',
                'peminatan' => 'Multimedia',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - Multimedia',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Konsep & Storyboard Multimedia', 'desc' => 'Kedalaman gagasan, orisinalitas ide, alur narasi, dan struktur storyboard visual.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Penguasaan Teknis Audio Visual & Animasi', 'desc' => 'Kualitas editing, rendering, compositing, motion graphic, dan sinkronisasi audio-visual.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Interaktivitas & User Experience (UI/UX)', 'desc' => 'Kemudahan interaksi antarmuka, responsivitas, dan fungsionalitas media multimedia terapan.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Komprehensi & Presentasi Sidang', 'desc' => 'Kelancaran penyampaian argumen karya, penguasaan materi, dan pertanggungjawaban desain.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DKV',
                'peminatan' => 'Game',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - Game',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Game Design Document (GDD) & Core Concept', 'desc' => 'Kelengkapan GDD, target audience, core loop, dan inovasi genre mekanika game.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Game Mechanics, Balancing & Asset Art 2D/3D', 'desc' => 'Kualitas asset grafis karakter/lingkungan, animasi, balancing kesulitan, dan level design.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Playability, Prototype Testing & Bug Handling', 'desc' => 'Kelancaran gameplay (playability), performa frame rate, dan hasil uji coba playtest pengguna.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Live Demo & Argumentasi Teknis Sidang', 'desc' => 'Penguasaan implementasi game engine, demonstrasi gameplay langsung, dan respon tanya jawab.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DKV',
                'peminatan' => 'Designpreneur',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - Designpreneur',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Riset Pasar & Business Model Canvas (BMC)', 'desc' => 'Validasi problem-solution fit, positioning pasar, analisis kompetitor, dan segmen konsumen.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Identitas Visual & Desain Produk/Kemasan Komersial', 'desc' => 'Kekuatan branding, packaging design, konsistensi collateral visual, dan daya tarik jual.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Strategi Pemasaran & Feasibility Finansial', 'desc' => 'Rencana go-to-market, cost of goods sold (COGS), proyeksi ROI, dan skalabilitas bisnis.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Pitching Produk & Pertanggungjawaban Bisnis', 'desc' => 'Kualitas deck presentasi, kemampuan pitching, dan kesiapan eksekusi komersial di pasar.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DKV',
                'peminatan' => 'VIID',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - VIID',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Riset Strategis & Brand Architecture VIID', 'desc' => 'Landasan riset identitas visual, brand DNA, brand archetype, dan positioning strategi.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Sistem Identitas Visual & Graphic Guidelines', 'desc' => 'Ketepatan tipografi, color palette, grid system, logo versatility, dan manual guideline lengkap.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Aplikasi Interaktif & Environmental Media Terapan', 'desc' => 'Penerapan identitas visual pada media digital/interaktif, signage, wayfinding, dan merchandise.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Presentasi Konsep & Ketajaman Analisis Visual', 'desc' => 'Artikulasi konsep visual, justifikasi semiotika desain, dan penguasaan respon akademik.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DI',
                'peminatan' => 'Komersial',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir Desain Interior - Komersial',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Konsep Ruang & Analisis Tapak Komersial', 'desc' => 'Kesesuaian tema desain dengan fungsi komersial, zoning, dan brand experience pengunjung.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Detail Konstruksi, Material & Furnitur Kustom', 'desc' => 'Spesifikasi material, keakuratan gambar kerja interior, dan inovasi furnitur kustom.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Ergonomi, Tata Cahaya (Lighting) & Akustik Ruang', 'desc' => 'Efisiensi sirkulasi, standar kenyamanan termal, pencahayaan buatan/alami, dan akustik.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Visualisasi 3D Rendering & Presentasi Sidang', 'desc' => 'Realisme 3D render, kelengkapan maket/board material, dan argumentasi pemilihan desain.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DI',
                'peminatan' => 'Residensial',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir Desain Interior - Residensial',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Analisis Kebutuhan Penghuni & Konsep Hunian', 'desc' => 'Pemahaman profil klien, efisiensi zonasi ruang privat-publik, dan atmosfer interior.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Pemilihan Material, Tekstur & Furnitur Hunian', 'desc' => 'Kualitas pemilihan finishing, keselarasan warna, dan ketahanan material interior.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Sirkulasi Ruang, Utilitas & Keberlanjutan (Eco-Design)', 'desc' => 'Penataan utilitas ME, sirkulasi udara alami, dan penggunaan material ramah lingkungan.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Gambar Kerja Teknis & Pertanggungjawaban Desain', 'desc' => 'Kelengkapan dokumen gambar arsitektural interior dan kelancaran presentasi sidang.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DIB',
                'peminatan' => 'Spatial Branding',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DIB - Spatial Branding',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Analisis Spatial Branding & Customer Journey', 'desc' => 'Integrasi brand identity ke dalam elemen spasial, touchpoints konsumen, dan visual merchandising.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Layout Efisiensi Ruang Retail & Sirkulasi', 'desc' => 'Optimalisasi sales floor, zoning display produk, dan kenyamanan sirkulasi flow pengunjung.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Analisis Kelayakan Finansial & Fit-out Costing', 'desc' => 'Estimasi RAB interior, pemilihan material tahan lama cost-effective, dan ROI ruang bisnis.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Pitching Konsep Bisnis Interior & Presentasi Teknis', 'desc' => 'Kemampuan menyampaikan value proposition ruang terhadap peningkatan performa bisnis.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DIB',
                'peminatan' => 'Hospitality',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DIB - Hospitality',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Konsep Hospitality & Standar Layanan Ruang', 'desc' => 'Karakter ambience penginapan/kafe/hotel, alur front-of-house dan back-of-house efisien.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Spesifikasi Material Heavy-Duty & Furnitur Kontrak', 'desc' => 'Ketahanan material standar komersial tinggi, kemudahan perawatan, dan estetika premium.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Standar Keamanan, Pencahayaan Mood & Akustik', 'desc' => 'Penerapan jalur evakuasi, pencahayaan dramatis, dan peredaman suara lingkungan hospitality.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Presentasi Komprehensif & Gambar Detail Interior', 'desc' => 'Kelengkapan gambar kerja dan kepiawaian dalam menjawab pertanyaan dewan penguji.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DP',
                'peminatan' => 'Desain Industri',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir Desain Produk - Desain Industri',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'User Research, Problem Framing & Inovasi Fungsi', 'desc' => 'Ketepatan identifikasi masalah pengguna, riset antropometri, dan kebaruan solusi fungsi produk.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Bentuk Estetika, Ergonomi & Styling Produk', 'desc' => 'Kematangan eksplorasi bentuk, proporsi, kenyamanan genggaman/penggunaan, dan CMF design.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Material, Manufakturabilitas & Prototyping Uji', 'desc' => 'Kesesuaian proses produksi massal, pemilihan polimer/logam, dan hasil uji prototype fisik.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Demonstrasi Produk Fisik & Argumentasi Sidang', 'desc' => 'Unjuk kerja prototype 1:1, detail exploded view 3D CAD, dan penguasaan materi sidang.', 'bobot' => 20)
                ))
            ),
            array(
                'prodi' => 'DP',
                'peminatan' => 'Furnitur',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir Desain Produk - Furnitur',
                'total_bobot' => 100,
                'kriteria_json' => json_encode(array(
                    array('id' => 'k1', 'title' => 'Riset Kebutuhan Furnitur & Analisis Ergonomi', 'desc' => 'Standar antropometri duduk/kerja, fungsi multi-purpose, dan efisiensi ruang pakai.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Konstruksi Sambungan, Kekuatan Struktur & Material', 'desc' => 'Inovasi joint system (knockdown/tenon), pemilihan kayu/metal, dan uji beban struktur.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Finishing, Kemudahan Perakitan & Kemasan Flat-pack', 'desc' => 'Kualitas finishing permukaan, efisiensi kemasan distribusi, dan instruksi perakitan.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Presentasi Prototype Skala 1:1 & Pertanggungjawaban', 'desc' => 'Kualitas mock-up fisik, keakuratan gambar kerja teknik, dan ketajaman jawaban ujian.', 'bobot' => 20)
                ))
            )
        );

        foreach ($defaults as $d) {
            $check = $this->db->where('prodi', $d['prodi'])->where('peminatan', $d['peminatan'])->get('master_rubrik_sidang')->row_array();
            if ($check) {
                $this->db->where('id', $check['id'])->update('master_rubrik_sidang', $d);
            } else {
                $this->db->insert('master_rubrik_sidang', $d);
            }
        }
    }

    // Ambil Semua Master Rubrik Sidang
    public function get_all_master_rubrik() {
        if (!$this->db->table_exists('master_rubrik_sidang')) {
            $this->_ensure_columns_exist();
        }

        $rows = $this->db->order_by('prodi', 'ASC')->order_by('peminatan', 'ASC')->get('master_rubrik_sidang')->result_array();
        foreach ($rows as &$r) {
            $r['kriteria'] = json_decode($r['kriteria_json'], true) ?: array();
        }
        return $rows;
    }

    public function _normalize_prodi($prodi) {
        $prodi = trim((string)$prodi);
        if (stripos($prodi, 'DIB') !== false) return 'DIB';
        if (stripos($prodi, 'DKV') !== false) return 'DKV';
        if (stripos($prodi, 'DI') !== false || stripos($prodi, 'Interior') !== false) return 'DI';
        if (stripos($prodi, 'DP') !== false || stripos($prodi, 'Produk') !== false) return 'DP';
        return $prodi;
    }

    // Ambil Master Rubrik Berdasarkan Prodi dan Peminatan
    public function get_master_rubrik_by_prodi_peminatan($prodi, $peminatan) {
        if (!$this->db->table_exists('master_rubrik_sidang')) {
            $this->_ensure_columns_exist();
        }

        $cleanProdi = $this->_normalize_prodi($prodi);
        $cleanPem   = trim((string)$peminatan);

        $row = $this->db->where('prodi', $cleanProdi)->where('peminatan', $cleanPem)->get('master_rubrik_sidang')->row_array();
        
        // If not found, re-seed defaults and try again
        if (!$row) {
            $this->_seed_default_master_rubrik();
            $row = $this->db->where('prodi', $cleanProdi)->where('peminatan', $cleanPem)->get('master_rubrik_sidang')->row_array();
        }

        // Fallback to any rubric in same prodi if specific peminatan isn't matched
        if (!$row) {
            $row = $this->db->where('prodi', $cleanProdi)->order_by('id', 'ASC')->get('master_rubrik_sidang')->row_array();
        }

        if ($row) {
            $row['kriteria'] = json_decode($row['kriteria_json'], true) ?: array();
        }
        return $row;
    }

    // Simpan / Update Master Rubrik Dinamis
    public function simpan_master_rubrik($prodi, $peminatan, $judul_rubrik, $kriteria = array(), $total_bobot = 100) {
        $this->_ensure_columns_exist();

        $cleanProdi = $this->_normalize_prodi($prodi);
        $cleanPem   = trim((string)$peminatan);

        if (empty($cleanProdi) || empty($cleanPem)) {
            return array('status' => false, 'message' => 'Prodi dan Peminatan wajib dipilih.');
        }

        if (empty($kriteria) || !is_array($kriteria)) {
            return array('status' => false, 'message' => 'Kriteria rubrik penilaian tidak boleh kosong.');
        }

        // Hitung total bobot
        $sumBobot = 0;
        foreach ($kriteria as &$k) {
            $k['bobot'] = floatval($k['bobot'] ?? 0);
            $sumBobot += $k['bobot'];
            if (empty($k['id'])) {
                $k['id'] = 'k_' . substr(md5(uniqid()), 0, 6);
            }
        }

        if (abs($sumBobot - 100) > 0.01) {
            return array('status' => false, 'message' => "Total persentase bobot harus pas 100% (saat ini: {$sumBobot}%).");
        }

        $kriteriaJson = json_encode($kriteria);
        $data = array(
            'prodi'        => $cleanProdi,
            'peminatan'    => $cleanPem,
            'judul_rubrik' => $judul_rubrik ?: "Rubrik Sidang TA {$cleanProdi} - {$cleanPem}",
            'kriteria_json'=> $kriteriaJson,
            'total_bobot'  => $sumBobot,
            'is_active'    => 1,
            'updated_at'   => date('Y-m-d H:i:s')
        );

        $exist = $this->db->where('prodi', $cleanProdi)->where('peminatan', $cleanPem)->get('master_rubrik_sidang')->row_array();
        if ($exist) {
            $this->db->where('id', $exist['id'])->update('master_rubrik_sidang', $data);
            $id = $exist['id'];
        } else {
            $this->db->insert('master_rubrik_sidang', $data);
            $id = $this->db->insert_id();
        }

        return array(
            'status'  => true,
            'message' => "Master Rubrik Penilaian untuk {$cleanProdi} - {$cleanPem} berhasil disimpan!",
            'data'    => array_merge($data, array('id' => $id, 'kriteria' => $kriteria))
        );
    }

    // Terapkan Rubrik Secara Massal ke Seluruh / Sebagian Mahasiswa di Prodi & Peminatan
    public function terapkan_rubrik_massal($prodi, $peminatan, $nim_list = array()) {
        $this->_ensure_columns_exist();

        $cleanProdi = $this->_normalize_prodi($prodi);
        $cleanPem   = trim((string)$peminatan);

        $rubrik = $this->get_master_rubrik_by_prodi_peminatan($cleanProdi, $cleanPem);
        if (!$rubrik) {
            return array('status' => false, 'message' => "Master rubrik untuk {$prodi} - {$peminatan} belum disetting.");
        }

        $kriteria = isset($rubrik['kriteria']) && is_array($rubrik['kriteria']) ? $rubrik['kriteria'] : array();
        $detailTemplate = array();
        foreach ($kriteria as $k) {
            $detailTemplate[] = array(
                'id'       => $k['id'] ?? ('k_' . substr(md5(uniqid()), 0, 6)),
                'kriteria' => $k['title'] ?? ($k['kriteria'] ?? 'Kriteria Penilaian'),
                'bobot'    => floatval($k['bobot'] ?? 25),
                'nilai'    => 0
            );
        }

        $detailJson = json_encode($detailTemplate);

        if (!empty($nim_list) && is_array($nim_list)) {
            $this->db->where_in('nim', $nim_list);
        }

        $updateData = array(
            'peminatan' => $cleanPem
        );

        $this->db->update('pendaftaran_ta', $updateData);
        $affected = $this->db->affected_rows();

        return array(
            'status'  => true,
            'message' => "Rubrik {$cleanProdi} - {$cleanPem} berhasil diterapkan secara massal ke {$affected} mahasiswa!",
            'affected'=> $affected,
            'rubrik'  => $rubrik
        );
    }
}

