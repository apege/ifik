<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekomendasi_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_init_tables();
    }

    private function _init_tables() {
        // 1. Table options
        if (!$this->db->table_exists('rekomen_jalur_options')) {
            $this->db->query("CREATE TABLE `rekomen_jalur_options` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `category` VARCHAR(50) DEFAULT 'non_sidang',
                `code` VARCHAR(100) NOT NULL UNIQUE,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT NULL,
                `icon_type` VARCHAR(50) DEFAULT 'bi',
                `icon_class` VARCHAR(255) DEFAULT 'bi-award-fill',
                `is_active` TINYINT(1) DEFAULT 1,
                `sort_order` INT DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Seed default options
            $default_options = [
                [
                    'category' => 'main',
                    'code' => 'main_sidang',
                    'title' => 'SIDANG',
                    'description' => 'Lanjut ke Majelis Ujian Sidang Akhir TA',
                    'icon_type' => 'bi',
                    'icon_class' => 'bi-mortarboard-fill',
                    'is_active' => 1,
                    'sort_order' => 1
                ],
                [
                    'category' => 'main',
                    'code' => 'main_non_sidang',
                    'title' => 'NON SIDANG',
                    'description' => 'Rekognisi Prestasi / HKI / Kebijakan / MBKM',
                    'icon_type' => 'bi',
                    'icon_class' => 'bi-award-fill',
                    'is_active' => 1,
                    'sort_order' => 2
                ],
                [
                    'category' => 'non_sidang',
                    'code' => 'prestasi',
                    'title' => 'Prestasi',
                    'description' => 'Tugas Akhir jalur Prestasi / Kejuaraan Lomba',
                    'icon_type' => 'bi',
                    'icon_class' => 'bi-award-fill',
                    'is_active' => 1,
                    'sort_order' => 1
                ],
                [
                    'category' => 'non_sidang',
                    'code' => 'kebijakan',
                    'title' => 'Implementasi kebijakan',
                    'description' => 'Tugas Akhir jalur Implementasi Kebijakan',
                    'icon_type' => 'bi',
                    'icon_class' => 'bi-gear-wide-connected',
                    'is_active' => 1,
                    'sort_order' => 2
                ],
                [
                    'category' => 'non_sidang',
                    'code' => 'hki',
                    'title' => 'HAK KEKAYAAN INTELEKTUAL',
                    'description' => 'Tugas Akhir jalur Hak Kekayaan Intelektual / Paten',
                    'icon_type' => 'bi',
                    'icon_class' => 'bi-file-earmark-check-fill',
                    'is_active' => 1,
                    'sort_order' => 3
                ],
                [
                    'category' => 'non_sidang',
                    'code' => 'pameran',
                    'title' => 'Pameran',
                    'description' => 'Tugas Akhir jalur Pameran Karya',
                    'icon_type' => 'bi',
                    'icon_class' => 'bi-easel-fill',
                    'is_active' => 1,
                    'sort_order' => 4
                ],
                [
                    'category' => 'non_sidang',
                    'code' => 'project_industri',
                    'title' => 'Project Pada Industri',
                    'description' => 'Tugas Akhir jalur Proyek Industri / Magang MBKM',
                    'icon_type' => 'bi',
                    'icon_class' => 'bi-building-gear',
                    'is_active' => 1,
                    'sort_order' => 5
                ]
            ];
            $this->db->insert_batch('rekomen_jalur_options', $default_options);
        }

        // 2. Table form fields per jalur
        if (!$this->db->table_exists('rekomen_jalur_fields')) {
            $this->db->query("CREATE TABLE `rekomen_jalur_fields` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `jalur_id` INT NOT NULL,
                `field_key` VARCHAR(100) NOT NULL,
                `field_label` VARCHAR(255) NOT NULL,
                `field_type` VARCHAR(50) DEFAULT 'file',
                `allowed_ext` VARCHAR(255) DEFAULT 'pdf,docx,doc',
                `is_required` TINYINT(1) DEFAULT 1,
                `help_text` VARCHAR(255) NULL,
                `sort_order` INT DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Seed default fields for all non_sidang options
            $non_sidang_options = $this->db->get_where('rekomen_jalur_options', ['category' => 'non_sidang'])->result_array();
            $fields_batch = [];
            foreach ($non_sidang_options as $opt) {
                $fields_batch[] = [
                    'jalur_id' => $opt['id'],
                    'field_key' => 'eviden',
                    'field_label' => 'Eviden',
                    'field_type' => 'file',
                    'allowed_ext' => 'pdf,docx,doc',
                    'is_required' => 1,
                    'help_text' => 'Format file diharuskan pdf/docx',
                    'sort_order' => 1
                ];
                $fields_batch[] = [
                    'jalur_id' => $opt['id'],
                    'field_key' => 'persetujuan_pembimbing',
                    'field_label' => 'Persetujuan Pembimbing',
                    'field_type' => 'file',
                    'allowed_ext' => 'pdf,docx,doc',
                    'is_required' => 1,
                    'help_text' => 'Format file diharuskan pdf/docx',
                    'sort_order' => 2
                ];
                $fields_batch[] = [
                    'jalur_id' => $opt['id'],
                    'field_key' => 'catatan_alasan',
                    'field_label' => 'Tanggapan untuk rekomendasi non sidang jalur ' . strtolower($opt['title']),
                    'field_type' => 'textarea',
                    'allowed_ext' => NULL,
                    'is_required' => 0,
                    'help_text' => 'Masukan alasan direkomendasikan ke jalur TA',
                    'sort_order' => 3
                ];
            }
            if (!empty($fields_batch)) {
                $this->db->insert_batch('rekomen_jalur_fields', $fields_batch);
            }
        }

        // 3. Table submissions
        if (!$this->db->table_exists('rekomen_submission')) {
            $this->db->query("CREATE TABLE `rekomen_submission` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nim` VARCHAR(50) NOT NULL,
                `id_preview` INT NULL,
                `recommendation_type` VARCHAR(50) NOT NULL,
                `jalur_id` INT NULL,
                `jalur_title` VARCHAR(255) NULL,
                `form_data_json` LONGTEXT NULL,
                `catatan_dosen` TEXT NULL,
                `status` VARCHAR(50) DEFAULT 'Submitted',
                `created_by` VARCHAR(100) NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
    }

    // Fetch all active main (Pop-up 1) options
    public function get_active_main_options() {
        $this->db->where_in('category', ['main', 'sidang']);
        $this->db->where('is_active', 1);
        $this->db->order_by('sort_order', 'ASC');
        $options = $this->db->get('rekomen_jalur_options')->result_array();
        if (empty($options)) {
            // Fallback default
            return [
                [
                    'id' => 1,
                    'category' => 'main',
                    'code' => 'main_sidang',
                    'title' => 'SIDANG',
                    'description' => 'Lanjut ke Majelis Ujian Sidang Akhir TA',
                    'icon_class' => 'bi-mortarboard-fill'
                ],
                [
                    'id' => 2,
                    'category' => 'main',
                    'code' => 'main_non_sidang',
                    'title' => 'NON SIDANG',
                    'description' => 'Rekognisi Prestasi / HKI / Kebijakan / MBKM',
                    'icon_class' => 'bi-award-fill'
                ]
            ];
        }
        return $options;
    }


    // Fetch all active non_sidang options with their dynamic fields
    public function get_active_non_sidang_options() {
        $this->db->where('category', 'non_sidang');
        $this->db->where('is_active', 1);
        $this->db->order_by('sort_order', 'ASC');
        $options = $this->db->get('rekomen_jalur_options')->result_array();

        foreach ($options as &$opt) {
            $this->db->where('jalur_id', $opt['id']);
            $this->db->order_by('sort_order', 'ASC');
            $opt['fields'] = $this->db->get('rekomen_jalur_fields')->result_array();
        }
        return $options;
    }

    // Get single option by ID or Code
    public function get_option($id_or_code) {
        if (is_numeric($id_or_code)) {
            $this->db->where('id', $id_or_code);
        } else {
            $this->db->where('code', $id_or_code);
        }
        $opt = $this->db->get('rekomen_jalur_options')->row_array();
        if ($opt) {
            $this->db->where('jalur_id', $opt['id']);
            $this->db->order_by('sort_order', 'ASC');
            $opt['fields'] = $this->db->get('rekomen_jalur_fields')->result_array();
        }
        return $opt;
    }

    // Get latest submission for student
    public function get_latest_submission($nim) {
        $this->db->where('nim', $nim);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('rekomen_submission')->row_array();
    }

    // Save submission
    public function save_submission($data) {
        // Check if there is existing submission
        $existing = $this->get_latest_submission($data['nim']);
        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('rekomen_submission', $data);
            return $existing['id'];
        } else {
            $this->db->insert('rekomen_submission', $data);
            return $this->db->insert_id();
        }
    }

    // CRUD Methods for Admin / Dosen / Koordinator TA to manage options & fields
    public function get_all_options() {
        $this->db->order_by('category', 'ASC');
        $this->db->order_by('sort_order', 'ASC');
        $options = $this->db->get('rekomen_jalur_options')->result_array();
        foreach ($options as &$opt) {
            $this->db->where('jalur_id', $opt['id']);
            $this->db->order_by('sort_order', 'ASC');
            $opt['fields'] = $this->db->get('rekomen_jalur_fields')->result_array();
        }
        return $options;
    }

    public function get_all_options_grouped() {
        $options = $this->get_all_options();
        $grouped = [];
        foreach ($options as $opt) {
            $cat = !empty($opt['category']) ? $opt['category'] : 'main';
            $grouped[$cat][] = $opt;
        }
        return $grouped;
    }


    public function save_option($data, $id = null) {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->update('rekomen_jalur_options', $data);
        } else {
            $this->db->insert('rekomen_jalur_options', $data);
            return $this->db->insert_id();
        }
    }

    public function delete_option($id) {
        $this->db->where('jalur_id', $id)->delete('rekomen_jalur_fields');
        return $this->db->where('id', $id)->delete('rekomen_jalur_options');
    }

    public function save_field($data, $id = null) {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->update('rekomen_jalur_fields', $data);
        } else {
            $this->db->insert('rekomen_jalur_fields', $data);
            return $this->db->insert_id();
        }
    }

    // Auto Seed Dummy Bimbingan Data so User can directly access Preview 3 & Rekomendasi
    public function seed_dummy_bimbingan_data($nim = '1301210001') {
        if (empty($nim)) $nim = '1301210001';

        // 1. Ensure pendaftaran_ta exists and is fully approved & unlocked
        if ($this->db->table_exists('pendaftaran_ta')) {
            $existing = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
            $ta_data = [
                'nim' => $nim,
                'judul_1' => 'Perancangan Visual Interaktif & Aplikasi Sistem Rekomendasi Tugas Akhir IFIK',
                'status_approval_wali' => 'Approved',
                'status_approval_admin' => 'Approved',
                'status_approval_koor' => 'Approved',
                'status_approval_kk' => 'Approved',
                'current_stage' => 'Pra-Sidang (Preview 3)',
                'is_bimbingan_unlocked' => 1,
                'pembimbing_1' => '19850101', // Teguh Akbar
                'pembimbing_2' => '19900101', // Abdulloh Umar
                'penguji_1' => '19820301',
                'penguji_2' => '19880401'
            ];

            if ($existing) {
                $this->db->where('nim', $nim)->update('pendaftaran_ta', $ta_data);
            } else {
                $this->db->insert('pendaftaran_ta', $ta_data);
            }
        }

        // 2. Ensure bimbingan_preview has dummy uploads for Preview 1 (Approved), Preview 2 (Approved), and Preview 3 (Ready for Review/Rekomendasi)
        if ($this->db->table_exists('bimbingan_preview')) {
            // Update all existing Preview 1 rows to Approved
            $this->db->where('nim', $nim)->where('tahap_preview', 'Preview 1')->update('bimbingan_preview', ['status_pembimbing' => 'Approved']);
            $p1 = $this->db->get_where('bimbingan_preview', ['nim' => $nim, 'tahap_preview' => 'Preview 1'])->row_array();
            if (!$p1) {
                $this->db->insert('bimbingan_preview', [
                    'nim' => $nim,
                    'tahap_preview' => 'Preview 1',
                    'file_draft' => 'Draft_Proposal_Bab1_3_Alif.pdf',
                    'catatan_mahasiswa' => 'Pengajuan draft proposal Bab 1-3 lengkap dengan kerangka pemikiran',
                    'status_pembimbing' => 'Approved',
                    'catatan_pembimbing' => 'ACC Bab 1-3, silakan tingkatkan prototype di Preview 2.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
                ]);
            }

            // Update all existing Preview 2 rows to Approved
            $this->db->where('nim', $nim)->where('tahap_preview', 'Preview 2')->update('bimbingan_preview', ['status_pembimbing' => 'Approved']);
            $p2 = $this->db->get_where('bimbingan_preview', ['nim' => $nim, 'tahap_preview' => 'Preview 2'])->row_array();
            if (!$p2) {
                $this->db->insert('bimbingan_preview', [
                    'nim' => $nim,
                    'tahap_preview' => 'Preview 2',
                    'file_draft' => 'Progress_50_Prototype_Alif.pdf',
                    'catatan_mahasiswa' => 'Progress Bab 4 & link prototype desain sistem',
                    'status_pembimbing' => 'Approved',
                    'catatan_pembimbing' => 'Penguji menyetujui progress 50%. Lanjutkan naskah pra-sidang.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
                ]);
            }

            $p3 = $this->db->get_where('bimbingan_preview', ['nim' => $nim, 'tahap_preview' => 'Preview 3'])->row_array();
            if (!$p3) {
                $this->db->insert('bimbingan_preview', [
                    'nim' => $nim,
                    'tahap_preview' => 'Preview 3',
                    'file_draft' => 'Naskah_Lengkap_Bab1_5_Alif.pdf',
                    'catatan_mahasiswa' => 'Naskah lengkap 100% dan lampiran karya siap untuk pra-sidang/rekomendasi',
                    'status_pembimbing' => 'Pending',
                    'catatan_pembimbing' => '',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}


