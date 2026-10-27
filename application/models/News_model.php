<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model Berita Fakultas
 * Mengelola tabel berita, query publik vs admin, dan status publikasi.
 */
class News_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->_check_table();
    }

    /** Auto create table 'berita' if it does not exist in DB */
    private function _check_table()
    {
        if (!$this->db->table_exists('berita')) {
            $sql = "CREATE TABLE IF NOT EXISTS `berita` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `judul` VARCHAR(255) NOT NULL,
                `kategori` VARCHAR(100) DEFAULT 'Berita Acara',
                `excerpt` TEXT NULL,
                `konten` LONGTEXT NULL,
                `tanggal` DATE NULL,
                `gambar` VARCHAR(255) NULL,
                `published` TINYINT(1) NOT NULL DEFAULT 1,
                `border_style` VARCHAR(50) DEFAULT 'none',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql);
        }
    }

    // ─── READ ─────────────────────────────────────────────────────────────────

    /** Semua berita (untuk admin) — urut terbaru */
    public function get_all()
    {
        if (!$this->db->table_exists('berita')) {
            return [];
        }
        return $this->db
            ->order_by('tanggal', 'DESC')
            ->order_by('id', 'DESC')
            ->get('berita')
            ->result();
    }

    /** Hanya yang published = 1 (untuk dashboard publik) */
    public function get_published()
    {
        if (!$this->db->table_exists('berita')) {
            return [];
        }
        return $this->db
            ->where('published', 1)
            ->order_by('tanggal', 'DESC')
            ->order_by('id', 'DESC')
            ->get('berita')
            ->result();
    }

    /** Satu berita berdasarkan ID */
    public function get_by_id($id)
    {
        if (!$this->db->table_exists('berita')) {
            return null;
        }
        return $this->db
            ->where('id', (int)$id)
            ->get('berita')
            ->row();
    }

    // ─── CREATE ───────────────────────────────────────────────────────────────

    public function insert_news($data)
    {
        return $this->db->insert('berita', $data);
    }

    // ─── UPDATE ───────────────────────────────────────────────────────────────

    public function update_news($id, $data)
    {
        $this->db->where('id', (int)$id);
        return $this->db->update('berita', $data);
    }

    /** Toggle status published/unpublished */
    public function toggle_publish($id)
    {
        $berita = $this->get_by_id($id);
        if (!$berita) return false;

        $new_status = $berita->published ? 0 : 1;
        $this->db->where('id', (int)$id);
        return $this->db->update('berita', ['published' => $new_status]);
    }

    // ─── DELETE ───────────────────────────────────────────────────────────────

    public function delete_news($id)
    {
        // Ambil data dulu untuk hapus file gambar jika ada
        $berita = $this->get_by_id($id);
        if ($berita && $berita->gambar) {
            $file_path = FCPATH . $berita->gambar;
            if (file_exists($file_path) && strpos($berita->gambar, 'uploads/news/') !== false) {
                @unlink($file_path);
            }
        }
        $this->db->where('id', (int)$id);
        return $this->db->delete('berita');
    }
}
