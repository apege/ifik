<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perwalian_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->ensure_schema();
    }

    /**
     * Pastikan tabel-tabel perwalian tersedia di database (auto-migration)
     */
    public function ensure_schema() {
        $this->load->dbforge();

        // 1. master_mata_kuliah
        if (!$this->db->table_exists('master_mata_kuliah')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `master_mata_kuliah` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `kode_mk` VARCHAR(30) NOT NULL UNIQUE,
                  `nama_mk` VARCHAR(255) NOT NULL,
                  `sks` INT NOT NULL DEFAULT 3,
                  `semester` INT NOT NULL DEFAULT 1,
                  `prodi` VARCHAR(100) NULL,
                  `jenis` VARCHAR(30) DEFAULT 'Wajib',
                  `kurikulum` VARCHAR(20) DEFAULT '2024',
                  `is_active` TINYINT(1) DEFAULT 1,
                  `created_at` DATETIME NULL,
                  `updated_at` DATETIME NULL,
                  INDEX `idx_mk_prodi` (`prodi`),
                  INDEX `idx_mk_semester` (`semester`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 2. perwalian
        if (!$this->db->table_exists('perwalian')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `perwalian` (
                  `id_perwalian` INT AUTO_INCREMENT PRIMARY KEY,
                  `nim` VARCHAR(30) NOT NULL,
                  `nama_mahasiswa` VARCHAR(150) NULL,
                  `prodi` VARCHAR(100) NULL,
                  `nip_dosen_wali` VARCHAR(50) NULL,
                  `nama_dosen_wali` VARCHAR(150) NULL,
                  `tahun_ajaran` VARCHAR(20) NOT NULL DEFAULT '2024/2025',
                  `semester` VARCHAR(20) NOT NULL DEFAULT 'Ganjil',
                  `status_tahap1` ENUM('Draft', 'Diajukan', 'Disetujui', 'Revisi') DEFAULT 'Draft',
                  `status_tahap2` ENUM('Belum Dinilai', 'Proses Penilaian', 'Selesai') DEFAULT 'Belum Dinilai',
                  `file_krs` VARCHAR(255) NULL,
                  `catatan_mahasiswa` TEXT NULL,
                  `catatan_wali` TEXT NULL,
                  `tgl_pengajuan` DATETIME NULL,
                  `tgl_approval_tahap1` DATETIME NULL,
                  `created_at` DATETIME NULL,
                  `updated_at` DATETIME NULL,
                  INDEX `idx_perwalian_nim` (`nim`),
                  INDEX `idx_perwalian_wali` (`nip_dosen_wali`),
                  INDEX `idx_perwalian_periode` (`tahun_ajaran`, `semester`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 3. perwalian_detail
        if (!$this->db->table_exists('perwalian_detail')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `perwalian_detail` (
                  `id` INT AUTO_INCREMENT PRIMARY KEY,
                  `id_perwalian` INT NOT NULL,
                  `kode_mk` VARCHAR(30) NOT NULL,
                  `nama_mk` VARCHAR(255) NOT NULL,
                  `sks` INT NOT NULL DEFAULT 3,
                  `status_tahap` ENUM('akan_diambil', 'sudah_diambil') NOT NULL DEFAULT 'akan_diambil',
                  `nilai_huruf` VARCHAR(5) NULL,
                  `status_kelulusan` ENUM('Belum Ditentukan', 'Lulus', 'Tidak Lulus') NOT NULL DEFAULT 'Belum Ditentukan',
                  `is_approved_wali` TINYINT(1) NOT NULL DEFAULT 0,
                  `tgl_approved_wali` DATETIME NULL,
                  `is_imported` TINYINT(1) DEFAULT 0,
                  `created_at` DATETIME NULL,
                  `updated_at` DATETIME NULL,
                  INDEX `idx_detail_perwalian` (`id_perwalian`),
                  INDEX `idx_detail_mk` (`kode_mk`),
                  INDEX `idx_detail_status_tahap` (`status_tahap`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        } else {
            try {
                if (!$this->db->field_exists('is_approved_wali', 'perwalian_detail')) {
                    $this->db->query("ALTER TABLE `perwalian_detail` ADD COLUMN `is_approved_wali` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status_kelulusan`, ADD COLUMN `tgl_approved_wali` DATETIME NULL AFTER `is_approved_wali`;");
                }
            } catch (\Throwable $e) {
                // Abaikan jika kolom sudah ada
            }
        }

        // 4. Pastikan collation tabel-tabel perwalian selaras dengan tabel user/mahasiswa (utf8mb4_general_ci)
        try {
            if ($this->db->table_exists('perwalian')) {
                $check = $this->db->query("SELECT TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perwalian'")->row_array();
                if (!empty($check['TABLE_COLLATION']) && $check['TABLE_COLLATION'] !== 'utf8mb4_general_ci') {
                    $this->db->query("ALTER TABLE `master_mata_kuliah` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
                    $this->db->query("ALTER TABLE `perwalian` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
                    $this->db->query("ALTER TABLE `perwalian_detail` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika user DB tidak memiliki privilege ALTER
        }
    }

    // =========================================================================
    // MASTER MATA KULIAH (ADMIN PRODI)
    // =========================================================================

    public function get_mata_kuliah($filters = [], $limit = 500, $offset = 0) {
        $this->db->from('master_mata_kuliah');

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('kode_mk', $s)
                ->or_like('nama_mk', $s)
                ->or_like('prodi', $s)
            ->group_end();
        }

        if (!empty($filters['prodi']) && $filters['prodi'] !== 'all') {
            $this->db->group_start()
                ->where('prodi', $filters['prodi'])
                ->or_where('prodi', '')
                ->or_where('prodi IS NULL', null, false)
            ->group_end();
        }

        if (!empty($filters['semester']) && $filters['semester'] !== 'all') {
            $this->db->where('semester', (int)$filters['semester']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', (int)$filters['is_active']);
        }

        $this->db->order_by('semester', 'ASC');
        $this->db->order_by('kode_mk', 'ASC');

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result_array();
    }

    public function get_mata_kuliah_by_id($id) {
        return $this->db->get_where('master_mata_kuliah', ['id' => $id])->row_array();
    }

    public function get_mata_kuliah_by_kode($kode_mk) {
        return $this->db->get_where('master_mata_kuliah', ['kode_mk' => trim($kode_mk)])->row_array();
    }

    public function save_mata_kuliah($data) {
        $now = date('Y-m-d H:i:s');
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        $payload = [
            'kode_mk'    => strtoupper(trim($data['kode_mk'] ?? '')),
            'nama_mk'    => trim($data['nama_mk'] ?? ''),
            'sks'        => max(1, (int)($data['sks'] ?? 3)),
            'semester'   => max(1, (int)($data['semester'] ?? 1)),
            'prodi'      => trim($data['prodi'] ?? ''),
            'jenis'      => !empty($data['jenis']) ? trim($data['jenis']) : 'Wajib',
            'kurikulum'  => !empty($data['kurikulum']) ? trim($data['kurikulum']) : '2024',
            'is_active'  => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'updated_at' => $now,
        ];

        if ($id > 0) {
            $this->db->where('id', $id)->update('master_mata_kuliah', $payload);
            return $id;
        } else {
            $payload['created_at'] = $now;
            $this->db->insert('master_mata_kuliah', $payload);
            return $this->db->insert_id();
        }
    }

    public function delete_mata_kuliah($id) {
        return $this->db->where('id', (int)$id)->delete('master_mata_kuliah');
    }

    public function delete_mata_kuliah_multiple($ids) {
        if (empty($ids) || !is_array($ids)) return false;
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, function($v) { return $v > 0; });
        if (empty($ids)) return false;
        $this->db->where_in('id', $ids)->delete('master_mata_kuliah');
        return $this->db->affected_rows();
    }

    /**
     * Validasi baris mata kuliah sebelum import
     * Memeriksa kesalahan mengisi field, swap kolom, dan format data.
     */
    public function validate_course_row($r) {
        $errors = [];

        // 1. Ambil data mentah
        $rawKode = trim((string)($r['kode_mk'] ?? ($r['kode'] ?? ($r['Kode MK'] ?? ''))));
        $rawNama = trim((string)($r['nama_mk'] ?? ($r['nama'] ?? ($r['Nama Mata Kuliah'] ?? ''))));
        $rawSks  = $r['sks'] ?? ($r['SKS'] ?? null);
        $rawSmt  = $r['semester'] ?? ($r['Semester'] ?? null);
        $rawJenis= trim((string)($r['jenis'] ?? ($r['Jenis'] ?? ($r['Sifat'] ?? 'Wajib'))));
        $rawProdi= trim((string)($r['prodi'] ?? ($r['Prodi'] ?? ($r['Program Studi'] ?? ''))));
        $rawKur  = trim((string)($r['kurikulum'] ?? ($r['Kurikulum'] ?? '2024')));

        // Validasi Kode MK
        if ($rawKode === '') {
            $errors[] = 'Kode MK tidak boleh kosong';
        } else {
            // Deteksi jika tertukar dengan Nama MK (panjang > 25 atau banyak spasi / kalimat)
            if (strlen($rawKode) > 25 || substr_count($rawKode, ' ') >= 3) {
                $errors[] = 'Kolom Kode MK terindikasi tertukar dengan Nama MK (format kode harus singkat alfanumerik, misal CS101)';
            } elseif (strlen($rawKode) < 2) {
                $errors[] = 'Kode MK terlalu pendek (minimal 2 karakter)';
            }
        }

        // Validasi Nama MK
        if ($rawNama === '') {
            $errors[] = 'Nama Mata Kuliah tidak boleh kosong';
        } else {
            // Deteksi jika tertukar dengan Kode MK (terlalu pendek tanpa spasi dan berupa kode)
            if (strlen($rawNama) <= 5 && !strpos($rawNama, ' ') && preg_match('/^[A-Za-z0-9_-]+$/', $rawNama)) {
                $errors[] = 'Kolom Nama MK terindikasi tertukar dengan Kode MK (terlalu pendek untuk nama mata kuliah)';
            } elseif (strlen($rawNama) < 3) {
                $errors[] = 'Nama Mata Kuliah terlalu pendek (minimal 3 karakter)';
            }
        }

        // Validasi SKS
        if ($rawSks === null || $rawSks === '') {
            $errors[] = 'Kolom SKS wajib diisi angka';
        } elseif (!is_numeric($rawSks)) {
            $errors[] = "Kolom SKS harus berupa angka (1-10), bukan teks '{$rawSks}' (kemungkinan kolom tertukar)";
        } else {
            $intSks = (int)$rawSks;
            if ($intSks <= 0 || $intSks > 10) {
                $errors[] = "Jumlah SKS tidak wajar ({$intSks} SKS). Harus antara 1-10 SKS";
            }
        }

        // Validasi Semester
        if ($rawSmt !== null && $rawSmt !== '') {
            if (!is_numeric($rawSmt)) {
                $errors[] = "Kolom Semester harus berupa angka (1-8), bukan teks '{$rawSmt}'";
            } else {
                $intSmt = (int)$rawSmt;
                if ($intSmt <= 0 || $intSmt > 14) {
                    $errors[] = "Semester tidak wajar ({$intSmt}). Harus antara 1-8";
                }
            }
        }

        // Validasi Jenis
        $normJenis = ucfirst(strtolower($rawJenis));
        if (!empty($rawJenis) && !in_array($normJenis, ['Wajib', 'Pilihan'])) {
            $errors[] = "Jenis mata kuliah harus 'Wajib' atau 'Pilihan', bukan '{$rawJenis}'";
        }

        return [
            'is_valid' => empty($errors),
            'errors'   => $errors,
            'clean'    => [
                'kode_mk'   => strtoupper($rawKode),
                'nama_mk'   => $rawNama,
                'sks'       => max(1, min(10, (int)$rawSks)),
                'semester'  => max(1, min(14, (int)($rawSmt ?: 1))),
                'prodi'     => $rawProdi,
                'jenis'     => in_array($normJenis, ['Wajib', 'Pilihan']) ? $normJenis : 'Wajib',
                'kurikulum' => !empty($rawKur) ? $rawKur : '2024',
            ]
        ];
    }

    /**
     * Bulk Upsert Mata Kuliah dari Excel / JSON dengan validasi duplicate & error checking
     */
    public function upsert_mata_kuliah_bulk($rows, $options = []) {
        if (empty($rows)) return [
            'inserted'          => 0,
            'updated'           => 0,
            'skipped_duplicate' => 0,
            'skipped_invalid'   => 0,
            'errors'            => [],
        ];

        $now = date('Y-m-d H:i:s');
        $inserted = 0;
        $updated = 0;
        $skipped_dup = 0;
        $skipped_inv = 0;
        $errorLogs = [];

        $allowUpdate = !empty($options['allow_update']);
        $seenKodeInFile = [];
        $seenNamaInFile = [];

        foreach ($rows as $index => $r) {
            $rowNum = $index + 1;
            $val = $this->validate_course_row($r);

            if (!$val['is_valid']) {
                $skipped_inv++;
                $errorLogs[] = [
                    'row'     => $rowNum,
                    'kode_mk' => $r['kode_mk'] ?? ($r['Kode MK'] ?? '-'),
                    'reason'  => implode('; ', $val['errors']),
                ];
                continue;
            }

            $c = $val['clean'];
            $kode = $c['kode_mk'];
            $namaLower = strtolower($c['nama_mk']);

            // 1. Cek duplikasi di dalam file yang sama
            if (isset($seenKodeInFile[$kode])) {
                $skipped_dup++;
                $errorLogs[] = [
                    'row'     => $rowNum,
                    'kode_mk' => $kode,
                    'reason'  => "Duplikat di dalam file: Kode MK {$kode} sudah muncul di baris {$seenKodeInFile[$kode]}",
                ];
                continue;
            }
            $seenKodeInFile[$kode] = $rowNum;

            if (isset($seenNamaInFile[$namaLower]) && $seenNamaInFile[$namaLower]['prodi'] === $c['prodi']) {
                $prevRow = $seenNamaInFile[$namaLower]['row'];
                $skipped_dup++;
                $errorLogs[] = [
                    'row'     => $rowNum,
                    'kode_mk' => $kode,
                    'reason'  => "Duplikat di dalam file: Nama MK '{$c['nama_mk']}' sudah ada di baris {$prevRow}",
                ];
                continue;
            }
            $seenNamaInFile[$namaLower] = ['row' => $rowNum, 'prodi' => $c['prodi']];

            // 2. Cek duplikasi di Database
            $existing = $this->get_mata_kuliah_by_kode($kode);
            if ($existing) {
                if ($allowUpdate) {
                    $this->db->where('id', $existing['id'])->update('master_mata_kuliah', [
                        'nama_mk'    => $c['nama_mk'],
                        'sks'        => $c['sks'],
                        'semester'   => $c['semester'],
                        'prodi'      => !empty($c['prodi']) ? $c['prodi'] : $existing['prodi'],
                        'jenis'      => !empty($c['jenis']) ? $c['jenis'] : $existing['jenis'],
                        'kurikulum'  => !empty($c['kurikulum']) ? $c['kurikulum'] : $existing['kurikulum'],
                        'updated_at' => $now,
                    ]);
                    $updated++;
                } else {
                    $skipped_dup++;
                    $errorLogs[] = [
                        'row'     => $rowNum,
                        'kode_mk' => $kode,
                        'reason'  => "Duplikat Database: Kode MK {$kode} ({$existing['nama_mk']}) sudah terdaftar di sistem",
                    ];
                }
            } else {
                $this->db->insert('master_mata_kuliah', [
                    'kode_mk'    => $kode,
                    'nama_mk'    => $c['nama_mk'],
                    'sks'        => $c['sks'],
                    'semester'   => $c['semester'],
                    'prodi'      => $c['prodi'],
                    'jenis'      => $c['jenis'],
                    'kurikulum'  => $c['kurikulum'],
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $inserted++;
            }
        }

        return [
            'inserted'          => $inserted,
            'updated'           => $updated,
            'skipped_duplicate' => $skipped_dup,
            'skipped_invalid'   => $skipped_inv,
            'errors'            => $errorLogs,
        ];
    }

    // =========================================================================
    // MASTER MAHASISWA & DOSEN WALI (ADMIN PRODI)
    // =========================================================================

    public function get_dosen_wali_options($prodi = null) {
        $this->db->select('id, name, nip, username, email, prodi')
                 ->from('user')
                 ->where('role_id', 3); // Dosen

        if (!empty($prodi) && $prodi !== 'all') {
            $this->db->group_start()
                ->where('prodi', $prodi)
                ->or_where('prodi', '')
                ->or_where('prodi IS NULL', null, false)
            ->group_end();
        }

        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function assign_dosen_wali($nim, $nip_dosen) {
        $dosen = $this->db->get_where('user', ['nip' => $nip_dosen])->row_array();
        if (!$dosen) {
            $dosen = $this->db->get_where('user', ['username' => $nip_dosen])->row_array();
        }
        $nama_dosen = $dosen ? $dosen['name'] : '';

        // Update di tabel user/mahasiswa
        if ($this->db->table_exists('user')) {
            $this->db->where('nim', $nim)->or_where('username', $nim);
            $this->db->update('user', ['dosen_wali' => $nip_dosen]);
        }
        if ($this->db->table_exists('mahasiswa')) {
            $this->db->where('nim', $nim)->update('mahasiswa', ['dosen_wali' => $nip_dosen]);
        }

        // Update juga pada perwalian aktif
        $this->db->where('nim', $nim)->update('perwalian', [
            'nip_dosen_wali'  => $nip_dosen,
            'nama_dosen_wali' => $nama_dosen,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    // =========================================================================
    // PERWALIAN & KRS KOLEKTIF (IMPORT ADMIN PRODI)
    // =========================================================================

    /**
     * Validasi baris KRS sebelum import
     */
    public function validate_krs_row($r) {
        $errors = [];
        $rawNim   = trim((string)($r['nim'] ?? ($r['NIM'] ?? ($r['nim_mahasiswa'] ?? ''))));
        $rawKode  = strtoupper(trim((string)($r['kode_mk'] ?? ($r['kode'] ?? ($r['Kode MK'] ?? '')))));
        $rawTahun = trim((string)($r['tahun_ajaran'] ?? ($r['tahun'] ?? '')));
        $rawSmt   = trim((string)($r['semester'] ?? ''));

        // Validasi NIM
        if ($rawNim === '') {
            $errors[] = 'NIM tidak boleh kosong';
        } else {
            if (!preg_match('/^[0-9A-Za-z]{6,25}$/', $rawNim) || strpos($rawNim, ' ') !== false) {
                $errors[] = "Format NIM tidak valid ('{$rawNim}'). Kemungkinan tertukar dengan Nama mahasiswa";
            }
        }

        // Validasi Kode MK
        if ($rawKode === '') {
            $errors[] = 'Kode MK tidak boleh kosong';
        } else {
            if (strlen($rawKode) > 25 || substr_count($rawKode, ' ') >= 2) {
                $errors[] = "Format Kode MK tidak valid ('{$rawKode}'). Kemungkinan tertukar dengan Nama MK";
            }
        }

        // Validasi Tahun Ajaran (jika ada)
        if (!empty($rawTahun) && !preg_match('/^\d{4}\/\d{4}$/', $rawTahun)) {
            $errors[] = "Format Tahun Ajaran harus YYYY/YYYY (misal 2024/2025), bukan '{$rawTahun}'";
        }

        // Validasi Semester (jika ada)
        $normSmt = ucfirst(strtolower($rawSmt));
        if (!empty($rawSmt) && !in_array($normSmt, ['Ganjil', 'Genap'])) {
            $errors[] = "Semester harus 'Ganjil' atau 'Genap', bukan '{$rawSmt}'";
        }

        return [
            'is_valid' => empty($errors),
            'errors'   => $errors,
            'clean'    => [
                'nim'          => $rawNim,
                'kode_mk'      => $rawKode,
                'tahun_ajaran' => $rawTahun,
                'semester'     => in_array($normSmt, ['Ganjil', 'Genap']) ? $normSmt : '',
            ]
        ];
    }

    /**
     * Bulk Import KRS dari SIAKAD / i-Gracias oleh Admin Prodi
     * Setiap baris: [nim, kode_mk, tahun_ajaran, semester]
     */
    public function upsert_krs_bulk($rows, $defaultTahun = '2024/2025', $defaultSemester = 'Ganjil', $options = []) {
        if (empty($rows)) return [
            'processed'          => 0,
            'new_perwalian'      => 0,
            'courses_added'      => 0,
            'skipped_duplicate'  => 0,
            'skipped_invalid'    => 0,
            'errors'             => [],
        ];

        $now = date('Y-m-d H:i:s');
        $processed = 0;
        $new_perwalian = 0;
        $courses_added = 0;
        $skipped_dup = 0;
        $skipped_inv = 0;
        $errorLogs = [];

        $seenInFile = [];

        foreach ($rows as $index => $r) {
            $rowNum = $index + 1;
            $val = $this->validate_krs_row($r);

            if (!$val['is_valid']) {
                $skipped_inv++;
                $errorLogs[] = [
                    'row'     => $rowNum,
                    'nim'     => $r['nim'] ?? ($r['NIM'] ?? '-'),
                    'kode_mk' => $r['kode_mk'] ?? ($r['Kode MK'] ?? '-'),
                    'reason'  => implode('; ', $val['errors']),
                ];
                continue;
            }

            $c = $val['clean'];
            $nim = $c['nim'];
            $kode_mk = $c['kode_mk'];
            $tahun = !empty($c['tahun_ajaran']) ? $c['tahun_ajaran'] : $defaultTahun;
            $smt = !empty($c['semester']) ? $c['semester'] : $defaultSemester;

            $fileKey = "{$nim}_{$kode_mk}_{$tahun}_{$smt}";
            if (isset($seenInFile[$fileKey])) {
                $prevRow = $seenInFile[$fileKey];
                $skipped_dup++;
                $errorLogs[] = [
                    'row'     => $rowNum,
                    'nim'     => $nim,
                    'kode_mk' => $kode_mk,
                    'reason'  => "Duplikat di dalam file: NIM {$nim} dengan Kode MK {$kode_mk} sudah ada di baris {$prevRow}",
                ];
                continue;
            }
            $seenInFile[$fileKey] = $rowNum;

            // Ambil / buat perwalian mahasiswa
            $perwalian = $this->get_active_perwalian_by_nim($nim, $tahun, $smt);
            if (!empty($perwalian['is_new'])) {
                $new_perwalian++;
            }
            $id_perwalian = $perwalian['id_perwalian'];

            // Dapatkan detail master MK
            $mk = $this->get_mata_kuliah_by_kode($kode_mk);
            $nama_mk = $mk ? $mk['nama_mk'] : ($r['nama_mk'] ?? $kode_mk);
            $sks = $mk ? (int)$mk['sks'] : (int)($r['sks'] ?? 3);

            // Cek apakah MK sudah ada di perwalian_detail
            $existDetail = $this->db->get_where('perwalian_detail', [
                'id_perwalian' => $id_perwalian,
                'kode_mk'      => $kode_mk,
                'status_tahap' => 'akan_diambil',
            ])->row_array();

            if ($existDetail) {
                $skipped_dup++;
                $errorLogs[] = [
                    'row'     => $rowNum,
                    'nim'     => $nim,
                    'kode_mk' => $kode_mk,
                    'reason'  => "Duplikat KRS: Mahasiswa {$nim} sudah mengambil MK {$kode_mk} ({$nama_mk})",
                ];
            } else {
                $this->db->insert('perwalian_detail', [
                    'id_perwalian'     => $id_perwalian,
                    'kode_mk'          => $kode_mk,
                    'nama_mk'          => $nama_mk,
                    'sks'              => $sks,
                    'status_tahap'     => 'akan_diambil',
                    'nilai_huruf'      => null,
                    'status_kelulusan' => 'Belum Ditentukan',
                    'is_imported'      => 1,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
                $courses_added++;
            }
            $processed++;
        }

        return [
            'processed'          => $processed,
            'new_perwalian'      => $new_perwalian,
            'courses_added'      => $courses_added,
            'skipped_duplicate'  => $skipped_dup,
            'skipped_invalid'    => $skipped_inv,
            'errors'             => $errorLogs,
        ];
    }

    // =========================================================================
    // MODUL PERWALIAN MAHASISWA
    // =========================================================================

    /**
     * Ambil atau inisialisasi perwalian aktif mahasiswa untuk semester berjalan
     */
    public function get_active_perwalian_by_nim($nim, $tahun_ajaran = '2024/2025', $semester = 'Ganjil') {
        $nim = trim((string)$nim);
        $perwalian = $this->db->get_where('perwalian', [
            'nim'          => $nim,
            'tahun_ajaran' => $tahun_ajaran,
            'semester'     => $semester,
        ])->row_array();

        if ($perwalian) {
            $perwalian['is_new'] = false;
            return $perwalian;
        }

        // Cari data mahasiswa & dosen wali dari tabel user
        $u = $this->db->get_where('user', ['nim' => $nim])->row_array();
        if (!$u) {
            $u = $this->db->get_where('user', ['username' => $nim])->row_array();
        }

        $nama_mhs = $u ? $u['name'] : 'Mahasiswa ' . $nim;
        $prodi = $u ? ($u['prodi'] ?? '') : '';
        $nip_wali = $u ? ($u['dosen_wali'] ?? '') : '';
        $nama_wali = '';

        if (!empty($nip_wali)) {
            $d = $this->db->group_start()
                ->where('id', $nip_wali)
                ->or_where('nip', $nip_wali)
                ->or_where('username', $nip_wali)
                ->group_end()
                ->get('user')->row_array();
            if ($d) $nama_wali = $d['name'];
        }

        $now = date('Y-m-d H:i:s');
        $payload = [
            'nim'             => $nim,
            'nama_mahasiswa'  => $nama_mhs,
            'prodi'           => $prodi,
            'nip_dosen_wali'  => $nip_wali,
            'nama_dosen_wali' => $nama_wali,
            'tahun_ajaran'    => $tahun_ajaran,
            'semester'        => $semester,
            'status_tahap1'   => 'Draft',
            'status_tahap2'   => 'Belum Dinilai',
            'file_krs'        => null,
            'catatan_mahasiswa' => null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ];

        $this->db->insert('perwalian', $payload);
        $payload['id_perwalian'] = $this->db->insert_id();
        $payload['is_new'] = true;

        return $payload;
    }

    public function get_perwalian_by_id($id_perwalian) {
        return $this->db->get_where('perwalian', ['id_perwalian' => (int)$id_perwalian])->row_array();
    }

    /**
     * Ambil mata kuliah dalam perwalian tertentu
     * @param int $id_perwalian
     * @param string|null $status_tahap 'akan_diambil' | 'sudah_diambil' | null (all)
     */
    public function get_perwalian_detail($id_perwalian, $status_tahap = null) {
        $this->db->select('pd.*, COALESCE(m.semester, 1) as semester_mk, m.jenis as jenis_mk')
                 ->from('perwalian_detail pd')
                 ->join('master_mata_kuliah m', 'pd.kode_mk = m.kode_mk', 'left')
                 ->where('pd.id_perwalian', (int)$id_perwalian);

        if (!empty($status_tahap)) {
            $this->db->where('pd.status_tahap', $status_tahap);
        }

        $this->db->order_by('semester_mk', 'ASC');
        $this->db->order_by('pd.id', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Ambil riwayat seluruh MK yang SUDAH diambil oleh mahasiswa (dari semester-semester sebelumnya)
     */
    public function get_riwayat_mk_sudah_diambil($nim) {
        $this->db->select('pd.*, COALESCE(m.semester, 1) as semester_mk, p.tahun_ajaran, p.semester as periode_semester')
                 ->from('perwalian_detail pd')
                 ->join('perwalian p', 'pd.id_perwalian = p.id_perwalian', 'inner')
                 ->join('master_mata_kuliah m', 'pd.kode_mk = m.kode_mk', 'left')
                 ->where('p.nim', trim($nim))
                 ->where('pd.status_tahap', 'sudah_diambil')
                 ->order_by('semester_mk', 'ASC')
                 ->order_by('pd.id', 'ASC');

        return $this->db->get()->result_array();
    }

    /**
     * Simpan / Perbarui Mata Kuliah Kontrak Mandiri oleh Mahasiswa (Tahap 1)
     */
    public function sync_kontrak_mahasiswa($id_perwalian, $selected_mk_codes = []) {
        $now = date('Y-m-d H:i:s');
        $id_perwalian = (int)$id_perwalian;

        // Ambil daftar saat ini yang berstatus 'akan_diambil'
        $current = $this->db->get_where('perwalian_detail', [
            'id_perwalian' => $id_perwalian,
            'status_tahap' => 'akan_diambil',
        ])->result_array();

        $currentMap = [];
        foreach ($current as $c) {
            $currentMap[$c['kode_mk']] = $c;
        }

        // Hapus MK yang tidak ada lagi di selected_mk_codes
        foreach ($current as $c) {
            if (!in_array($c['kode_mk'], $selected_mk_codes, true)) {
                $this->db->where('id', $c['id'])->delete('perwalian_detail');
            }
        }

        // Tambahkan MK baru yang belum ada
        foreach ($selected_mk_codes as $kode) {
            $kode = strtoupper(trim($kode));
            if (empty($kode)) continue;
            if (!isset($currentMap[$kode])) {
                $mk = $this->get_mata_kuliah_by_kode($kode);
                $nama_mk = $mk ? $mk['nama_mk'] : $kode;
                $sks = $mk ? (int)$mk['sks'] : 3;

                $this->db->insert('perwalian_detail', [
                    'id_perwalian'     => $id_perwalian,
                    'kode_mk'          => $kode,
                    'nama_mk'          => $nama_mk,
                    'sks'              => $sks,
                    'status_tahap'     => 'akan_diambil',
                    'nilai_huruf'      => null,
                    'status_kelulusan' => 'Belum Ditentukan',
                    'is_imported'      => 0,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }
        }

        $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
            'updated_at' => $now,
        ]);

        return true;
    }

    /**
     * Tambah satu MK ke kontrak mahasiswa
     */
    public function add_mk_to_kontrak($id_perwalian, $kode_mk) {
        $kode_mk = strtoupper(trim($kode_mk));
        $id_perwalian = (int)$id_perwalian;

        $exists = $this->db->get_where('perwalian_detail', [
            'id_perwalian' => $id_perwalian,
            'kode_mk'      => $kode_mk,
            'status_tahap' => 'akan_diambil',
        ])->row_array();

        if ($exists) return true;

        $mk = $this->get_mata_kuliah_by_kode($kode_mk);
        $nama_mk = $mk ? $mk['nama_mk'] : $kode_mk;
        $sks = $mk ? (int)$mk['sks'] : 3;
        $now = date('Y-m-d H:i:s');

        return $this->db->insert('perwalian_detail', [
            'id_perwalian'     => $id_perwalian,
            'kode_mk'          => $kode_mk,
            'nama_mk'          => $nama_mk,
            'sks'              => $sks,
            'status_tahap'     => 'akan_diambil',
            'nilai_huruf'      => null,
            'status_kelulusan' => 'Belum Ditentukan',
            'is_imported'      => 0,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
    }

    /**
     * Hapus satu MK dari kontrak
     */
    public function remove_mk_from_kontrak($id_perwalian, $kode_mk) {
        return $this->db->where([
            'id_perwalian' => (int)$id_perwalian,
            'kode_mk'      => trim($kode_mk),
            'status_tahap' => 'akan_diambil',
        ])->delete('perwalian_detail');
    }

    /**
     * Upload Bukti KRS Asli dari Kampus
     */
    public function update_bukti_krs($id_perwalian, $filename) {
        return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', [
            'file_krs'   => $filename,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Submit Pengajuan KRS Tahap 1 ke Dosen Wali
     */
    public function submit_perwalian_tahap1($id_perwalian, $catatan_mhs = '') {
        $now = date('Y-m-d H:i:s');
        return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', [
            'status_tahap1'     => 'Diajukan',
            'catatan_mahasiswa' => trim($catatan_mhs),
            'tgl_pengajuan'     => $now,
            'updated_at'        => $now,
        ]);
    }

    // =========================================================================
    // MONITORING PERWALIAN (ADMIN PRODI & STATISTIK)
    // =========================================================================

    /**
     * Pemantauan kemajuan perwalian mahasiswa tingkat Program Studi
     */
    public function get_monitoring_list($filters = []) {
        $this->db->select('p.*, MAX(u.name) as nama_lengkap, MAX(u.email) as email, MAX(u.prodi) as user_prodi, COUNT(pd.id) as total_mk, COALESCE(SUM(pd.sks), 0) as total_sks')
                 ->from('perwalian p')
                 ->join('user u', 'p.nim = u.nim OR p.nim = u.username', 'left')
                 ->join('perwalian_detail pd', 'p.id_perwalian = pd.id_perwalian AND pd.status_tahap = "akan_diambil"', 'left')
                 ->group_by('p.id_perwalian');

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('p.nim', $s)
                ->or_like('p.nama_mahasiswa', $s)
                ->or_like('p.nama_dosen_wali', $s)
            ->group_end();
        }

        if (!empty($filters['status_tahap1']) && $filters['status_tahap1'] !== 'all') {
            $this->db->where('p.status_tahap1', $filters['status_tahap1']);
        }

        if (!empty($filters['tahun_ajaran']) && $filters['tahun_ajaran'] !== 'all') {
            $this->db->where('p.tahun_ajaran', $filters['tahun_ajaran']);
        }

        if (!empty($filters['semester']) && $filters['semester'] !== 'all') {
            $this->db->where('p.semester', $filters['semester']);
        }

        if (!empty($filters['prodi']) && $filters['prodi'] !== 'all') {
            $this->db->group_start()
                ->where('p.prodi', $filters['prodi'])
                ->or_where('p.prodi', '')
                ->or_where('p.prodi IS NULL', null, false)
                ->or_like('p.prodi', $filters['prodi'])
            ->group_end();
        }

        $this->db->order_by('p.updated_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_monitoring_stats($tahun = '2024/2025', $semester = 'Ganjil') {
        $this->db->where('tahun_ajaran', $tahun)->where('semester', $semester);
        $all = $this->db->get('perwalian')->result_array();

        $stats = [
            'total'     => count($all),
            'draft'     => 0,
            'diajukan'  => 0,
            'disetujui' => 0,
            'revisi'    => 0,
        ];

        foreach ($all as $item) {
            $st = strtolower($item['status_tahap1'] ?? 'draft');
            if ($st === 'diajukan') $stats['diajukan']++;
            elseif ($st === 'disetujui') $stats['disetujui']++;
            elseif ($st === 'revisi') $stats['revisi']++;
            else $stats['draft']++;
        }

        return $stats;
    }

    // =========================================================================
    // MODUL DOSEN WALI (APPROVAL TAHAP 1, GENERATE MK, PENILAIAN TAHAP 2)
    // Dibuat khusus tanpa hardcoding, mendukung multi-identifier & auto-grading
    // =========================================================================

    /**
     * Dapatkan daftar perwalian mahasiswa bimbingan dari dosen tertentu.
     * Mendukung multi-identifier (NIP, Kode Dosen, Username, ID) dan filter dinamis.
     */
    public function get_perwalian_by_dosen($dosen_identifiers, $filters = []) {
        $ids = is_array($dosen_identifiers) ? array_filter(array_unique($dosen_identifiers)) : [trim((string)$dosen_identifiers)];
        $ids = array_values(array_filter($ids, fn($v) => !empty($v)));

        $this->db->select('
            p.*, 
            MAX(u.name) as nama_lengkap_user, 
            MAX(u.email) as email_mahasiswa, 
            MAX(u.no_telp) as no_telp_mahasiswa,
            MAX(u.images) as foto_mahasiswa,
            COUNT(pd.id) as total_mk,
            COALESCE(SUM(pd.sks), 0) as total_sks,
            COALESCE(SUM(CASE WHEN pd.status_kelulusan = "Lulus" THEN 1 ELSE 0 END), 0) as total_lulus,
            COALESCE(SUM(CASE WHEN pd.status_kelulusan = "Tidak Lulus" THEN 1 ELSE 0 END), 0) as total_tidak_lulus,
            COALESCE(SUM(CASE WHEN pd.nilai_huruf IS NOT NULL AND pd.nilai_huruf != "" THEN 1 ELSE 0 END), 0) as total_dinilai,
            COALESCE(SUM(CASE WHEN pd.is_approved_wali = 1 THEN 1 ELSE 0 END), 0) as total_approved_mk
        ')
        ->from('perwalian p')
        ->join('user u', 'p.nim = u.nim OR p.nim = u.username', 'left', false)
        ->join('perwalian_detail pd', 'p.id_perwalian = pd.id_perwalian AND pd.status_tahap = "akan_diambil"', 'left');

        // Filter Dosen Wali (Multi-identifier atau 'all' untuk SuperAdmin)
        if (!in_array('all', $ids, true) && !empty($ids)) {
            $this->db->group_start();
            $this->db->where_in('p.nip_dosen_wali', $ids);
            $this->db->or_where_in('u.dosen_wali', $ids);
            $this->db->group_end();
        }

        // Filter Pencarian Dinamis (NIM / Nama Mahasiswa)
        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $this->db->group_start()
                ->like('p.nim', $s)
                ->or_like('p.nama_mahasiswa', $s)
                ->or_like('u.name', $s)
                ->or_like('p.prodi', $s)
            ->group_end();
        }

        // Filter Status Tahap 1
        if (!empty($filters['status_tahap1']) && $filters['status_tahap1'] !== 'all') {
            $this->db->where('p.status_tahap1', $filters['status_tahap1']);
        }

        // Filter Status Tahap 2
        if (!empty($filters['status_tahap2']) && $filters['status_tahap2'] !== 'all') {
            $this->db->where('p.status_tahap2', $filters['status_tahap2']);
        }

        // Filter Tahun Ajaran & Semester Dinamis
        if (!empty($filters['tahun_ajaran']) && $filters['tahun_ajaran'] !== 'all') {
            $this->db->where('p.tahun_ajaran', $filters['tahun_ajaran']);
        }
        if (!empty($filters['semester']) && $filters['semester'] !== 'all') {
            $this->db->where('p.semester', $filters['semester']);
        }

        // Filter Program Studi Dinamis
        if (!empty($filters['prodi']) && $filters['prodi'] !== 'all') {
            $this->db->group_start()
                ->where('p.prodi', $filters['prodi'])
                ->or_where('u.prodi', $filters['prodi'])
            ->group_end();
        }

        $this->db->group_by('p.id_perwalian');
        $this->db->order_by('p.updated_at', 'DESC');
        $rows = $this->db->get()->result_array();

        // Self-healing: pastikan status_tahap2 selalu sinkron dengan jumlah MK yang sudah di-approve Dosen Wali
        $filteredRows = [];
        foreach ($rows as &$r) {
            $totMk = (int)($r['total_mk'] ?? 0);
            $totDinilai = (int)($r['total_dinilai'] ?? 0);
            $totApproved = (int)($r['total_approved_mk'] ?? 0);
            $curSt2 = $r['status_tahap2'] ?? 'Belum Dinilai';

            if ($totMk === 0 || $totApproved === 0) {
                $fixedSt2 = 'Belum Dinilai';
            } elseif ($totApproved < $totMk) {
                $fixedSt2 = 'Proses Penilaian';
            } else {
                $fixedSt2 = 'Selesai';
            }

            if ($fixedSt2 !== $curSt2) {
                $r['status_tahap2'] = $fixedSt2;
                $this->db->where('id_perwalian', (int)$r['id_perwalian'])->update('perwalian', [
                    'status_tahap2' => $fixedSt2,
                ]);
            }

            // Jika filter only_has_nilai aktif (Tahap 2), hanya tampilkan mahasiswa yang sudah menginput nilai
            if (!empty($filters['only_has_nilai']) && $totDinilai === 0) {
                continue;
            }
            $filteredRows[] = $r;
        }
        unset($r);

        return $filteredRows;
    }

    /**
     * Dapatkan ringkasan statistik perwalian untuk dosen wali
     */
    public function get_dosen_perwalian_stats($dosen_identifiers, $tahun = null, $semester = null) {
        $ids = is_array($dosen_identifiers) ? array_filter(array_unique($dosen_identifiers)) : [trim((string)$dosen_identifiers)];
        $ids = array_values(array_filter($ids, fn($v) => !empty($v)));

        $this->db->select('
            p.*,
            COUNT(pd.id) as total_mk,
            COALESCE(SUM(CASE WHEN pd.nilai_huruf IS NOT NULL AND pd.nilai_huruf != "" THEN 1 ELSE 0 END), 0) as total_dinilai,
            COALESCE(SUM(CASE WHEN pd.is_approved_wali = 1 THEN 1 ELSE 0 END), 0) as total_approved_mk
        ')
        ->from('perwalian p')
        ->join('user u', 'p.nim = u.nim OR p.nim = u.username', 'left', false)
        ->join('perwalian_detail pd', 'p.id_perwalian = pd.id_perwalian AND pd.status_tahap = "akan_diambil"', 'left');

        if (!in_array('all', $ids, true) && !empty($ids)) {
            $this->db->group_start();
            $this->db->where_in('p.nip_dosen_wali', $ids);
            $this->db->or_where_in('u.dosen_wali', $ids);
            $this->db->group_end();
        }

        if (!empty($tahun) && $tahun !== 'all') {
            $this->db->where('p.tahun_ajaran', $tahun);
        }
        if (!empty($semester) && $semester !== 'all') {
            $this->db->where('p.semester', $semester);
        }

        $this->db->group_by('p.id_perwalian');
        $records = $this->db->get()->result_array();

        $stats = [
            'total_bimbingan'     => count($records),
            'tahap1_draft'        => 0,
            'tahap1_diajukan'     => 0,
            'tahap1_disetujui'    => 0,
            'tahap1_revisi'       => 0,
            'tahap2_total'        => 0,
            'tahap2_belum_dinilai'=> 0,
            'tahap2_proses'       => 0,
            'tahap2_selesai'      => 0,
        ];

        foreach ($records as $r) {
            $st1 = strtolower(trim($r['status_tahap1'] ?? 'draft'));
            if ($st1 === 'diajukan') $stats['tahap1_diajukan']++;
            elseif ($st1 === 'disetujui') $stats['tahap1_disetujui']++;
            elseif ($st1 === 'revisi') $stats['tahap1_revisi']++;
            else $stats['tahap1_draft']++;

            // Selalu tampilkan & hitung mahasiswa yang Tahap 1-nya sudah 'Disetujui' pada Tahap 2
            if ($st1 === 'disetujui') {
                $stats['tahap2_total']++;
                $totMk = (int)($r['total_mk'] ?? 0);
                $totApproved = (int)($r['total_approved_mk'] ?? 0);

                if ($totMk === 0 || $totApproved === 0) {
                    $curSt2 = 'Belum Dinilai';
                } elseif ($totApproved < $totMk) {
                    $curSt2 = 'Proses Penilaian';
                } else {
                    $curSt2 = 'Selesai';
                }

                $st2 = strtolower(trim($curSt2));
                if ($st2 === 'proses penilaian') $stats['tahap2_proses']++;
                elseif ($st2 === 'selesai') $stats['tahap2_selesai']++;
                else $stats['tahap2_belum_dinilai']++;
            }
        }

        return $stats;
    }

    /**
     * Dapatkan detail lengkap perwalian beserta mata kuliahnya
     */
    public function get_perwalian_detail_full($id_perwalian) {
        $id_perwalian = (int)$id_perwalian;
        $perwalian = $this->db->select('p.*, u.name as nama_lengkap, u.email as user_email, u.no_telp as user_phone, u.images as user_photo, u.prodi as user_prodi')
            ->from('perwalian p')
            ->join('user u', 'p.nim = u.nim OR p.nim = u.username', 'left', false)
            ->where('p.id_perwalian', $id_perwalian)
            ->get()
            ->row_array();

        if (!$perwalian) return null;

        // Ambil daftar mata kuliah yang diajukan dipisahkan / diurutkan per semester
        $mk_list = $this->db->select('pd.*, COALESCE(m.semester, 1) as semester_mk, m.jenis as jenis_mk')
            ->from('perwalian_detail pd')
            ->join('master_mata_kuliah m', 'pd.kode_mk = m.kode_mk', 'left')
            ->where('pd.id_perwalian', $id_perwalian)
            ->where('pd.status_tahap', 'akan_diambil')
            ->order_by('semester_mk', 'ASC')
            ->order_by('pd.id', 'ASC')
            ->get()
            ->result_array();

        // Hitung total SKS & statistik nilai / approval
        $totalSks = 0;
        $sksLulus = 0;
        $sksTidakLulus = 0;
        $totalDinilai = 0;
        $totalApproved = 0;

        foreach ($mk_list as &$mk) {
            $totalSks += (int)$mk['sks'];
            $nilai = strtoupper(trim((string)($mk['nilai_huruf'] ?? '')));
            $st = trim($mk['status_kelulusan'] ?? '');

            // Auto-sync status_kelulusan jika nilai_huruf sudah diisi mahasiswa
            if (!empty($nilai)) {
                if (in_array($nilai, ['A', 'AB', 'B', 'BC', 'C'], true) && $st !== 'Lulus') {
                    $st = 'Lulus';
                    $mk['status_kelulusan'] = 'Lulus';
                    $this->db->where('id', (int)$mk['id'])->update('perwalian_detail', ['status_kelulusan' => 'Lulus']);
                } elseif (in_array($nilai, ['D', 'E'], true) && $st !== 'Tidak Lulus') {
                    $st = 'Tidak Lulus';
                    $mk['status_kelulusan'] = 'Tidak Lulus';
                    $this->db->where('id', (int)$mk['id'])->update('perwalian_detail', ['status_kelulusan' => 'Tidak Lulus']);
                }
                $totalDinilai++;
            }

            if ($st === 'Lulus') $sksLulus += (int)$mk['sks'];
            elseif ($st === 'Tidak Lulus') $sksTidakLulus += (int)$mk['sks'];

            $mk['is_approved_wali'] = (int)($mk['is_approved_wali'] ?? 0);
            if ($mk['is_approved_wali'] === 1) {
                $totalApproved++;
            }
        }
        unset($mk);

        // Ambil riwayat MK yang SUDAH diambil di semester sebelumnya
        $mk_riwayat = $this->get_riwayat_mk_sudah_diambil($perwalian['nim']);

        return [
            'perwalian'         => $perwalian,
            'mata_kuliah'       => $mk_list,
            'riwayat_mk'        => $mk_riwayat,
            'total_sks'         => $totalSks,
            'sks_lulus'         => $sksLulus,
            'sks_tidak_lulus'   => $sksTidakLulus,
            'total_dinilai'     => $totalDinilai,
            'total_approved_mk' => $totalApproved,
            'total_mk'          => count($mk_list),
            'is_all_graded'     => count($mk_list) > 0 && ($totalDinilai === count($mk_list)),
            'is_all_approved'   => count($mk_list) > 0 && ($totalApproved === count($mk_list)),
        ];
    }

    /**
     * Approve / Unapprove Mata Kuliah pada Tahap 2 oleh Dosen Wali (Satu-satu per MK atau Multiple sekaligus)
     */
    public function approve_mk_tahap2($id_perwalian, $detail_ids = [], $approve = 1) {
        $id_perwalian = (int)$id_perwalian;
        $approve = ((int)$approve === 1) ? 1 : 0;
        $now = date('Y-m-d H:i:s');

        if ($id_perwalian <= 0) {
            return ['status' => false, 'message' => 'ID Perwalian tidak valid.'];
        }

        if ($detail_ids === 'all') {
            $this->db->where([
                'id_perwalian' => $id_perwalian,
                'status_tahap' => 'akan_diambil',
            ]);
            if ($approve === 1) {
                $this->db->where('nilai_huruf IS NOT NULL', null, false);
                $this->db->where('nilai_huruf !=', '');
            }
            $this->db->update('perwalian_detail', [
                'is_approved_wali'  => $approve,
                'tgl_approved_wali' => $approve ? $now : null,
                'updated_at'        => $now,
            ]);
        } else {
            if (!is_array($detail_ids)) {
                $detail_ids = [$detail_ids];
            }
            $cleanIds = array_values(array_filter(array_map('intval', $detail_ids), fn($v) => $v > 0));
            if (empty($cleanIds)) {
                return ['status' => false, 'message' => 'Pilih minimal satu mata kuliah untuk diproses.'];
            }

            $this->db->where('id_perwalian', $id_perwalian)
                     ->where_in('id', $cleanIds);
            if ($approve === 1) {
                $this->db->where('nilai_huruf IS NOT NULL', null, false);
                $this->db->where('nilai_huruf !=', '');
            }
            $this->db->update('perwalian_detail', [
                'is_approved_wali'  => $approve,
                'tgl_approved_wali' => $approve ? $now : null,
                'updated_at'        => $now,
            ]);
        }

        // Hitung ulang progress approval untuk menentukan status_tahap2 secara otomatis
        $allDetails = $this->db->get_where('perwalian_detail', [
            'id_perwalian' => $id_perwalian,
            'status_tahap' => 'akan_diambil',
        ])->result_array();

        $totMk = count($allDetails);
        $totApproved = 0;
        $totLulus = 0;
        $totTidakLulus = 0;
        $totDinilai = 0;
        $approvedIds = [];

        foreach ($allDetails as $d) {
            if ((int)($d['is_approved_wali'] ?? 0) === 1) {
                $totApproved++;
                $approvedIds[] = (int)$d['id'];
            }
            if ($d['status_kelulusan'] === 'Lulus') $totLulus++;
            elseif ($d['status_kelulusan'] === 'Tidak Lulus') $totTidakLulus++;
            if (!empty($d['nilai_huruf'])) $totDinilai++;
        }

        if ($totMk === 0 || $totApproved === 0) {
            $newSt2 = 'Belum Dinilai';
        } elseif ($totApproved < $totMk) {
            $newSt2 = 'Proses Penilaian';
        } else {
            $newSt2 = 'Selesai';
        }

        $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
            'status_tahap2' => $newSt2,
            'updated_at'    => $now,
        ]);

        return [
            'status'            => true,
            'id_perwalian'      => $id_perwalian,
            'total_mk'          => $totMk,
            'total_approved_mk' => $totApproved,
            'total_lulus'       => $totLulus,
            'total_tidak_lulus' => $totTidakLulus,
            'total_dinilai'     => $totDinilai,
            'status_tahap2'     => $newSt2,
            'approved_ids'      => $approvedIds,
        ];
    }

    /**
     * Fitur GENERATE MK:
     * Jika mahasiswa belum punya daftar MK di perwalian_detail,
     * salin otomatis daftar mata kuliah kurikulum dari master_mata_kuliah.
     */
    public function generate_mk_for_perwalian($id_perwalian) {
        $id_perwalian = (int)$id_perwalian;
        $perwalian = $this->get_perwalian_by_id($id_perwalian);
        if (!$perwalian) {
            return ['status' => false, 'message' => 'Data perwalian tidak ditemukan.'];
        }

        // Cek apakah sudah ada MK
        $existing = $this->db->get_where('perwalian_detail', [
            'id_perwalian' => $id_perwalian,
            'status_tahap' => 'akan_diambil',
        ])->result_array();

        if (!empty($existing)) {
            // MK sudah ada, pastikan status_kelulusan default siap dinilai
            $now = date('Y-m-d H:i:s');
            $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
                'updated_at' => $now,
            ]);
            return [
                'status'  => true,
                'count'   => count($existing),
                'message' => 'Mata kuliah mahasiswa sudah aktif dan siap untuk dinilai (' . count($existing) . ' MK).'
            ];
        }

        // Jika belum ada MK sama sekali, ambil kurikulum standar dari master_mata_kuliah
        $prodi = trim($perwalian['prodi'] ?? '');
        $this->db->from('master_mata_kuliah')->where('is_active', 1);

        if (!empty($prodi)) {
            $this->db->group_start()
                ->where('prodi', $prodi)
                ->or_where('prodi', '')
                ->or_where('prodi IS NULL', null, false)
            ->group_end();
        }

        // Batasi 6-8 MK default (sekitar 20 SKS)
        $masterList = $this->db->order_by('semester', 'ASC')->order_by('kode_mk', 'ASC')->limit(8)->get()->result_array();

        if (empty($masterList)) {
            return [
                'status'  => false,
                'message' => 'Master mata kuliah untuk program studi ini masih kosong. Silakan koordinasi dengan Admin Prodi.'
            ];
        }

        $now = date('Y-m-d H:i:s');
        $inserted = 0;
        foreach ($masterList as $m) {
            $this->db->insert('perwalian_detail', [
                'id_perwalian'     => $id_perwalian,
                'kode_mk'          => strtoupper(trim($m['kode_mk'])),
                'nama_mk'          => trim($m['nama_mk']),
                'sks'              => max(1, (int)$m['sks']),
                'status_tahap'     => 'akan_diambil',
                'nilai_huruf'      => null,
                'status_kelulusan' => 'Belum Ditentukan',
                'is_imported'      => 0,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $inserted++;
        }

        $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
            'updated_at' => $now,
        ]);

        return [
            'status'  => true,
            'count'   => $inserted,
            'message' => "Berhasil men-generate {$inserted} mata kuliah ke dalam rencana studi mahasiswa."
        ];
    }

    /**
     * Approval / Penolakan KRS Tahap 1 oleh Dosen Wali
     */
    public function approve_tahap1($id_perwalian, $status = 'Disetujui', $catatan = '') {
        $now = date('Y-m-d H:i:s');
        $status = in_array($status, ['Disetujui', 'Revisi', 'Diajukan', 'Draft'], true) ? $status : 'Disetujui';

        $updateData = [
            'status_tahap1'       => $status,
            'catatan_wali'        => trim($catatan),
            'tgl_approval_tahap1' => ($status === 'Disetujui') ? $now : null,
            'updated_at'          => $now,
        ];

        // Jika disetujui, periksa apakah sudah ada nilai riil pada mata kuliah yang dikontrak
        if ($status === 'Disetujui') {
            $mkList = $this->db->get_where('perwalian_detail', [
                'id_perwalian' => (int)$id_perwalian,
                'status_tahap' => 'akan_diambil'
            ])->result_array();

            $totalMK = count($mkList);
            $graded = 0;
            foreach ($mkList as $m) {
                if (!empty(trim((string)($m['nilai_huruf'] ?? '')))) {
                    $graded++;
                }
            }

            if ($graded === 0) {
                $updateData['status_tahap2'] = 'Belum Dinilai';
            } elseif ($graded < $totalMK) {
                $updateData['status_tahap2'] = 'Dinilai';
            } else {
                $updateData['status_tahap2'] = 'Selesai';
            }
        } else {
            // Jika dikembalikan ke Revisi/Draft, reset status Tahap 2
            $updateData['status_tahap2'] = 'Belum Dinilai';
        }

        return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', $updateData);
    }

    /**
     * Reset Status / Berkas Perwalian Mahasiswa (Buka Kunci atau Reset Total)
     * @param int $id_perwalian
     * @param string $action 'unlock' (buka kunci ke Draft/Revisi) | 'total' (kosongkan MK & file)
     * @param string $catatan
     */
    public function reset_perwalian($id_perwalian, $action = 'unlock', $catatan = '') {
        $now = date('Y-m-d H:i:s');
        $perwalian = $this->db->get_where('perwalian', ['id_perwalian' => (int)$id_perwalian])->row_array();
        if (!$perwalian) return false;

        if ($action === 'total') {
            // Hapus file KRS fisik jika ada
            if (!empty($perwalian['file_krs'])) {
                $filePath = FCPATH . 'uploads/krs/' . $perwalian['file_krs'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Hapus detail mata kuliah yang 'akan_diambil'
            $this->db->where('id_perwalian', (int)$id_perwalian)
                     ->where('status_tahap', 'akan_diambil')
                     ->delete('perwalian_detail');

            return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', [
                'status_tahap1'       => 'Draft',
                'status_tahap2'       => 'Belum Dinilai',
                'file_krs'            => null,
                'catatan_wali'        => !empty($catatan) ? trim($catatan) : 'Berkas KRS telah direset total oleh Admin Prodi.',
                'tgl_pengajuan'       => null,
                'tgl_approval_tahap1' => null,
                'updated_at'          => $now,
            ]);
        } else {
            // Mode unlock: kembalikan ke Draft atau Revisi agar form mahasiswa terbuka kembali
            $newStatus = !empty($catatan) ? 'Revisi' : 'Draft';

            // Bersihkan juga nilai yang menggantung di perwalian_detail
            $this->db->where('id_perwalian', (int)$id_perwalian)
                     ->where('status_tahap', 'akan_diambil')
                     ->update('perwalian_detail', [
                         'nilai_huruf'      => null,
                         'status_kelulusan' => null
                     ]);

            return $this->db->where('id_perwalian', (int)$id_perwalian)->update('perwalian', [
                'status_tahap1'       => $newStatus,
                'status_tahap2'       => 'Belum Dinilai',
                'catatan_wali'        => !empty($catatan) ? trim($catatan) : $perwalian['catatan_wali'],
                'tgl_approval_tahap1' => null,
                'updated_at'          => $now,
            ]);
        }
    }


    /**
     * Update nilai per mata kuliah (Tahap 2):
     * A, AB, B, BC, C -> Otomatis Lulus (Hijau)
     * D, E -> Otomatis Tidak Lulus (Merah)
     * Juga memperbarui status_tahap2 parent menjadi 'Proses Penilaian' jika belum dinilai.
     */
    public function update_nilai_detail($id_detail, $nilai_huruf, $status_kelulusan = null) {
        $id_detail = (int)$id_detail;
        $nilai_huruf = strtoupper(trim((string)$nilai_huruf));

        // Status kelulusan otomatis ditentukan 100% dari nilai huruf:
        // A, AB, B, BC, C = Lulus | D, E = Tidak Lulus | Kosong = Belum Ditentukan
        if (in_array($nilai_huruf, ['A', 'AB', 'B', 'BC', 'C'], true)) {
            $status_kelulusan = 'Lulus';
        } elseif (in_array($nilai_huruf, ['D', 'E'], true)) {
            $status_kelulusan = 'Tidak Lulus';
        } else {
            $status_kelulusan = 'Belum Ditentukan';
        }

        $now = date('Y-m-d H:i:s');
        $this->db->where('id', $id_detail)->update('perwalian_detail', [
            'nilai_huruf'      => !empty($nilai_huruf) ? $nilai_huruf : null,
            'status_kelulusan' => $status_kelulusan,
            'updated_at'       => $now,
        ]);

        $detail = $this->db->get_where('perwalian_detail', ['id' => $id_detail])->row_array();
        $counts = [
            'id_perwalian'      => 0,
            'total_lulus'       => 0,
            'total_tidak_lulus' => 0,
            'status_tahap2'     => 'Belum Dinilai',
        ];

        if ($detail) {
            $id_perwalian = (int)$detail['id_perwalian'];
            $counts['id_perwalian'] = $id_perwalian;
            $allDetails = $this->db->get_where('perwalian_detail', [
                'id_perwalian' => $id_perwalian,
                'status_tahap' => 'akan_diambil',
            ])->result_array();

            $totMk = count($allDetails);
            $totDinilai = 0;
            foreach ($allDetails as $d) {
                if ($d['status_kelulusan'] === 'Lulus') $counts['total_lulus']++;
                elseif ($d['status_kelulusan'] === 'Tidak Lulus') $counts['total_tidak_lulus']++;
                if (!empty($d['nilai_huruf'])) $totDinilai++;
            }

            $p = $this->get_perwalian_by_id($id_perwalian);
            $curSt2 = $p['status_tahap2'] ?? 'Belum Dinilai';

            if ($totDinilai === 0) {
                $newSt2 = 'Belum Dinilai';
            } elseif ($totDinilai < $totMk) {
                $newSt2 = 'Proses Penilaian';
            } else {
                // Semua MK sudah ada nilai: jika sebelumnya sudah Selesai tetap Selesai, jika belum jadi Proses Penilaian
                $newSt2 = ($curSt2 === 'Selesai') ? 'Selesai' : 'Proses Penilaian';
            }

            if ($newSt2 !== $curSt2) {
                $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
                    'status_tahap2' => $newSt2,
                    'updated_at'    => $now,
                ]);
            }
            $counts['status_tahap2'] = $newSt2;
        }

        return array_merge([
            'status'           => true,
            'nilai_huruf'      => $nilai_huruf,
            'status_kelulusan' => $status_kelulusan,
        ], $counts);
    }

    /**
     * Submit Tahap 2: Selesaikan & Kunci Evaluasi Nilai Perwalian
     * Menyalin status_tahap = 'sudah_diambil' untuk MK yang telah dievaluasi
     */
    public function submit_perwalian_tahap2($id_perwalian) {
        $id_perwalian = (int)$id_perwalian;
        $now = date('Y-m-d H:i:s');

        // Pastikan ada MK yang dinilai dan seluruhnya sudah memiliki nilai_huruf
        $details = $this->db->get_where('perwalian_detail', [
            'id_perwalian' => $id_perwalian,
            'status_tahap' => 'akan_diambil',
        ])->result_array();

        if (empty($details)) {
            return ['status' => false, 'message' => 'Tidak ada mata kuliah untuk diselesaikan.'];
        }

        foreach ($details as $d) {
            if (empty(trim((string)($d['nilai_huruf'] ?? '')))) {
                return ['status' => false, 'message' => 'Seluruh mata kuliah wajib diberi Nilai Huruf sebelum perwalian dikunci.'];
            }
        }

        // Tandai status perwalian selesai
        $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
            'status_tahap2' => 'Selesai',
            'updated_at'    => $now,
        ]);

        return ['status' => true, 'message' => 'Evaluasi perwalian Tahap 2 berhasil diselesaikan dan dikunci.'];
    }

    /**
     * Import Nilai Bulk dari Excel oleh Dosen Wali
     * Baris bisa berupa: [nim, kode_mk, nilai_huruf] atau [kode_mk, nilai_huruf] jika id_perwalian sudah ditentukan
     */
    public function import_nilai_bulk_dosen($dosen_identifiers, $rows, $specific_id_perwalian = null) {
        if (empty($rows)) {
            return ['status' => false, 'message' => 'Data baris nilai kosong.'];
        }

        $now = date('Y-m-d H:i:s');
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $r) {
            $kode_mk = strtoupper(trim((string)($r['kode_mk'] ?? ($r['kode'] ?? ($r['Kode MK'] ?? '')))));
            $nilai   = strtoupper(trim((string)($r['nilai_huruf'] ?? ($r['nilai'] ?? ($r['Nilai'] ?? ($r['Nilai Huruf'] ?? ''))))));
            $nim     = trim((string)($r['nim'] ?? ($r['NIM'] ?? '')));

            if (empty($kode_mk) || empty($nilai)) {
                $skipped++;
                continue;
            }

            // Validasi nilai huruf wajib standar
            if (!in_array($nilai, ['A', 'AB', 'B', 'BC', 'C', 'D', 'E'], true)) {
                $skipped++;
                continue;
            }

            // Hitung status kelulusan otomatis
            $status_lulus = in_array($nilai, ['A', 'AB', 'B', 'BC', 'C'], true) ? 'Lulus' : 'Tidak Lulus';

            if (!empty($specific_id_perwalian)) {
                // Mode single mahasiswa: langsung update detail
                $det = $this->db->get_where('perwalian_detail', [
                    'id_perwalian' => (int)$specific_id_perwalian,
                    'kode_mk'      => $kode_mk,
                    'status_tahap' => 'akan_diambil',
                ])->row_array();

                if ($det) {
                    $this->db->where('id', $det['id'])->update('perwalian_detail', [
                        'nilai_huruf'      => $nilai,
                        'status_kelulusan' => $status_lulus,
                        'updated_at'       => $now,
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
            } elseif (!empty($nim)) {
                // Mode kolektif bimbingan: cari perwalian aktif mahasiswa yang Tahap 1-nya sudah Disetujui
                $p = $this->db->where(['nim' => $nim, 'status_tahap1' => 'Disetujui'])->order_by('id_perwalian', 'DESC')->get('perwalian')->row_array();
                if ($p) {
                    $det = $this->db->get_where('perwalian_detail', [
                        'id_perwalian' => $p['id_perwalian'],
                        'kode_mk'      => $kode_mk,
                        'status_tahap' => 'akan_diambil',
                    ])->row_array();

                    if ($det) {
                        $this->db->where('id', $det['id'])->update('perwalian_detail', [
                            'nilai_huruf'      => $nilai,
                            'status_kelulusan' => $status_lulus,
                            'updated_at'       => $now,
                        ]);

                        if ($p['status_tahap2'] === 'Belum Dinilai') {
                            $this->db->where('id_perwalian', $p['id_perwalian'])->update('perwalian', [
                                'status_tahap2' => 'Proses Penilaian',
                                'updated_at'    => $now,
                            ]);
                        }
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }

        if ($updated === 0) {
            return [
                'status'  => false,
                'updated' => 0,
                'skipped' => $skipped,
                'message' => 'Tidak ada mata kuliah yang cocok untuk diimpor. Pastikan Kode MK sesuai dengan rencana studi mahasiswa.'
            ];
        }

        $msg = "Berhasil memperbarui {$updated} nilai mata kuliah.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} baris dilewati karena Kode MK/NIM tidak sesuai).";
        }

        return [
            'status'  => true,
            'updated' => $updated,
            'skipped' => $skipped,
            'message' => $msg
        ];
    }

    /**
     * Dapatkan opsi filter dinamis dari database (Tahun Ajaran, Semester, Prodi)
     * Tidak ada yang di-hardcode!
     */
    /**
     * Impor nilai mahasiswa (Tahap 2) dengan proteksi anti-duplikasi: murni operasi UPDATE berbasis (id_perwalian + kode_mk)
     */
    public function import_nilai_single($id_perwalian, $rows) {
        $id_perwalian = (int)$id_perwalian;
        if (empty($rows)) {
            return ['status' => false, 'message' => 'Data baris nilai kosong.'];
        }

        $now = date('Y-m-d H:i:s');
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $r) {
            $kode_mk = strtoupper(trim((string)($r['kode_mk'] ?? ($r['kode'] ?? ($r['Kode MK'] ?? '')))));
            $nilai   = strtoupper(trim((string)($r['nilai_huruf'] ?? ($r['nilai'] ?? ($r['Nilai'] ?? ($r['Nilai Huruf'] ?? ''))))));

            if (empty($kode_mk) || empty($nilai)) {
                $skipped++;
                continue;
            }

            if (!in_array($nilai, ['A', 'AB', 'B', 'BC', 'C', 'D', 'E'], true)) {
                $skipped++;
                continue;
            }

            $status_lulus = in_array($nilai, ['A', 'AB', 'B', 'BC', 'C'], true) ? 'Lulus' : 'Tidak Lulus';

            $det = $this->db->get_where('perwalian_detail', [
                'id_perwalian' => $id_perwalian,
                'kode_mk'      => $kode_mk,
                'status_tahap' => 'akan_diambil',
            ])->row_array();

            if ($det) {
                $this->db->where('id', $det['id'])->update('perwalian_detail', [
                    'nilai_huruf'      => $nilai,
                    'status_kelulusan' => $status_lulus,
                    'updated_at'       => $now,
                ]);
                $updated++;
            } else {
                $skipped++;
            }
        }

        if ($updated > 0) {
            $p = $this->db->get_where('perwalian', ['id_perwalian' => $id_perwalian])->row_array();
            if ($p && $p['status_tahap2'] === 'Belum Dinilai') {
                $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', [
                    'status_tahap2' => 'Proses Penilaian',
                    'updated_at'    => $now,
                ]);
            }
        }

        if ($updated === 0) {
            return [
                'status'  => false,
                'updated' => 0,
                'skipped' => $skipped,
                'message' => 'Tidak ada mata kuliah yang cocok untuk diimpor. Pastikan Kode MK sesuai dengan rencana studi mahasiswa.'
            ];
        }

        $msg = "Berhasil memperbarui {$updated} nilai mata kuliah.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} baris dilewati karena Kode MK tidak sesuai).";
        }

        return [
            'status'  => true,
            'updated' => $updated,
            'skipped' => $skipped,
            'message' => $msg
        ];
    }

        public function get_filter_options() {
        // 1. Tahun Ajaran
        $thnList = $this->db->distinct()->select('tahun_ajaran')->from('perwalian')->order_by('tahun_ajaran', 'DESC')->get()->result_array();
        $tahun_ajaran = array_filter(array_column($thnList, 'tahun_ajaran'));
        if (empty($tahun_ajaran)) {
            $tahun_ajaran = ['2024/2025', '2023/2024'];
        }

        // 2. Semester
        $smtList = $this->db->distinct()->select('semester')->from('perwalian')->order_by('semester', 'ASC')->get()->result_array();
        $semester = array_filter(array_column($smtList, 'semester'));
        if (empty($semester)) {
            $semester = ['Ganjil', 'Genap'];
        }

        // 3. Program Studi (dari master_prodi)
        $prodi = [];
        if ($this->db->table_exists('master_prodi')) {
            $pList = $this->db->select('kode_prodi, nama_prodi')->from('master_prodi')->where('is_active', 1)->order_by('urutan', 'ASC')->get()->result_array();
            foreach ($pList as $pl) {
                $prodi[] = $pl['nama_prodi'];
            }
        }
        if (empty($prodi)) {
            $pDist = $this->db->distinct()->select('prodi')->from('user')->where('prodi IS NOT NULL', null, false)->where('prodi !=', '')->get()->result_array();
            $prodi = array_column($pDist, 'prodi');
        }

        return [
            'tahun_ajaran' => array_values(array_unique($tahun_ajaran)),
            'semester'     => array_values(array_unique($semester)),
            'prodi'        => array_values(array_unique($prodi)),
        ];
    }
}
