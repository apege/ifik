-- =========================================================
-- SQL MIGRATION FILE FOR RECOMMENDATION SYSTEM (TAILSCALE DB)
-- Database: db_ifik
-- =========================================================

-- 1. Table for Recommendation Pathway Options (Main Categories & Sub-Pathways)
CREATE TABLE IF NOT EXISTS `rekomen_jalur_options` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50) DEFAULT 'non_sidang',
  `code` VARCHAR(100) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `icon_type` VARCHAR(50) DEFAULT 'bi',
  `icon_class` VARCHAR(100) DEFAULT 'bi-award-fill',
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Default Main Categories & Sub-Pathways
INSERT IGNORE INTO `rekomen_jalur_options` (`id`, `category`, `code`, `title`, `description`, `icon_type`, `icon_class`, `is_active`, `sort_order`) VALUES
(1, 'main', 'main_sidang', 'SIDANG', 'Lanjut ke Majelis Ujian Sidang Akhir TA', 'bi', 'bi-mortarboard-fill', 1, 1),
(2, 'main', 'main_non_sidang', 'NON SIDANG', 'Rekognisi Prestasi / HKI / Kebijakan / MBKM', 'bi', 'bi-award-fill', 1, 2),
(3, 'non_sidang', 'prestasi', 'Prestasi', 'Tugas Akhir jalur Prestasi / Kejuaraan Lomba', 'bi', 'bi-award-fill', 1, 1),
(4, 'non_sidang', 'kebijakan', 'Implementasi kebijakan', 'Tugas Akhir jalur Implementasi Kebijakan', 'bi', 'bi-gear-wide-connected', 1, 2),
(5, 'non_sidang', 'hki', 'HAK KEKAYAAN INTELEKTUAL', 'Tugas Akhir jalur Hak Kekayaan Intelektual / Paten', 'bi', 'bi-file-earmark-check-fill', 1, 3),
(6, 'non_sidang', 'pameran', 'Pameran', 'Tugas Akhir jalur Pameran Karya', 'bi', 'bi-easel-fill', 1, 4),
(7, 'non_sidang', 'project_industri', 'Project Pada Industri', 'Tugas Akhir jalur Proyek Industri / Magang MBKM', 'bi', 'bi-building-gear', 1, 5);


-- 2. Table for Dynamic Form Requirement Fields
CREATE TABLE IF NOT EXISTS `rekomen_jalur_fields` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `jalur_id` INT NOT NULL,
  `field_key` VARCHAR(100) NOT NULL,
  `field_label` VARCHAR(255) NOT NULL,
  `field_type` VARCHAR(50) DEFAULT 'file',
  `allowed_ext` VARCHAR(255) DEFAULT 'pdf,docx,doc',
  `is_required` TINYINT(1) DEFAULT 1,
  `help_text` VARCHAR(255) NULL,
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Default Form Requirements for Non-Sidang Options
INSERT IGNORE INTO `rekomen_jalur_fields` (`id`, `jalur_id`, `field_key`, `field_label`, `field_type`, `allowed_ext`, `is_required`, `help_text`, `sort_order`) VALUES
(1, 3, 'eviden', 'Eviden', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 1),
(2, 3, 'persetujuan_pembimbing', 'Persetujuan Pembimbing', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 2),
(3, 3, 'catatan_alasan', 'Tanggapan untuk rekomendasi non sidang jalur prestasi', 'textarea', NULL, 0, 'Masukan alasan direkomendasikan ke jalur TA', 3),
(4, 4, 'eviden', 'Eviden', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 1),
(5, 4, 'persetujuan_pembimbing', 'Persetujuan Pembimbing', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 2),
(6, 4, 'catatan_alasan', 'Tanggapan untuk rekomendasi non sidang jalur implementasi kebijakan', 'textarea', NULL, 0, 'Masukan alasan direkomendasikan ke jalur TA', 3),
(7, 5, 'eviden', 'Eviden', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 1),
(8, 5, 'persetujuan_pembimbing', 'Persetujuan Pembimbing', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 2),
(9, 5, 'catatan_alasan', 'Tanggapan untuk rekomendasi non sidang jalur hak kekayaan intelektual', 'textarea', NULL, 0, 'Masukan alasan direkomendasikan ke jalur TA', 3),
(10, 6, 'eviden', 'Eviden', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 1),
(11, 6, 'persetujuan_pembimbing', 'Persetujuan Pembimbing', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 2),
(12, 6, 'catatan_alasan', 'Tanggapan untuk rekomendasi non sidang jalur pameran', 'textarea', NULL, 0, 'Masukan alasan direkomendasikan ke jalur TA', 3),
(13, 7, 'eviden', 'Eviden', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 1),
(14, 7, 'persetujuan_pembimbing', 'Persetujuan Pembimbing', 'file', 'pdf,docx,doc', 1, 'Format file diharuskan pdf/docx', 2),
(15, 7, 'catatan_alasan', 'Tanggapan untuk rekomendasi non sidang jalur project pada industri', 'textarea', NULL, 0, 'Masukan alasan direkomendasikan ke jalur TA', 3);


-- 3. Table for Student Submissions
CREATE TABLE IF NOT EXISTS `rekomen_submission` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
