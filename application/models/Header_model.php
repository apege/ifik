<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Header_model
 * 
 * Model ini bertanggung jawab mengelola data konfigurasi dan konten header/navbar pada halaman utama (Dashboard / Landing Page).
 * Menyediakan fungsi untuk memastikan ketersediaan tabel skema database, memuat pengaturan teks dan aset hero slider,
 * serta menangani operasi CRUD untuk slide banner yang ditampilkan di bawah bilah navigasi (Navbar).
 * 
 * @package     IFIK Telkom University
 * @subpackage  Models
 * @category    Header & Navigation Settings
 * @author      Nazril Rizki
 */
class Header_model extends CI_Model {

    /**
     * Memastikan tabel `header_settings` tersedia di database dan menginjeksi rekam konfigurasi default jika kosong.
     * Fungsi ini dipanggil otomatis secara internal (lazy migration) sebelum menjalankan query pengaturan header.
     * 
     * @access private
     * @return void
     */
    private function _ensure_tables() {
        // 1. Periksa dan buat tabel header_settings jika belum ada
        if (!$this->db->table_exists('header_settings')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `header_settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) DEFAULT 'Fakultas Industri Kreatif',
                `description` TEXT NULL,
                `dekanat_image` VARCHAR(255) DEFAULT 'dekanat2.png',
                `slide_duration` INT DEFAULT 4,
                `slide1_image` VARCHAR(255) DEFAULT 'Fakultas.jpg',
                `slide2_video` VARCHAR(255) DEFAULT 'vidtelkom.mp4',
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        // 2. Injeksi record pengaturan default pada ID 1 jika data masih kosong
        if ($this->db->table_exists('header_settings')) {
            $existing = $this->db->get_where('header_settings', ['id' => 1])->row();
            if (!$existing) {
                $this->db->insert('header_settings', [
                    'id'             => 1,
                    'title'          => 'Fakultas Industri Kreatif',
                    'description'    => 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.',
                    'dekanat_image'  => 'dekanat2.png',
                    'slide_duration' => 4,
                    'slide1_image'   => 'Fakultas.jpg',
                    'slide2_video'   => 'vidtelkom.mp4',
                    'updated_at'     => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Menentukan nama tabel yang aktif untuk menyimpan item slide banner.
     * Mendukung kompatibilitas skema lama (`tb_panel`) maupun skema baru (`header_slides`).
     * 
     * @access private
     * @return string Nama tabel database yang tersedia
     */
    private function _get_panel_table() {
        return $this->db->table_exists('tb_panel') ? 'tb_panel' : 'header_slides';
    }

    /**
     * Mengambil rekam konfigurasi utama header landing page (judul, deskripsi, gambar dekanat, durasi slide).
     * Jika tabel belum memiliki data, fungsi ini mengembalikan objek default standar agar tampilan navbar & landing page tetap berfungsi normal.
     * 
     * @access public
     * @return object Objek berisi data pengaturan header
     */
    public function get_settings() {
        // Pastikan tabel dan baris default siap
        $this->_ensure_tables();
        $row = $this->db->get_where('header_settings', ['id' => 1])->row();
        
        // Fallback default objek jika query tidak menghasilkan baris
        if (!$row) {
            $default = new stdClass();
            $default->id             = 1;
            $default->title          = 'Fakultas Industri Kreatif';
            $default->description    = 'Seiring dengan berkembangnya kebutuhan pelayanan untuk mahasiswa, dosen dan pegawai FIK maka diperlukan peningkatan layanan yang mengusung efisiensi dan efektifitas. Ifik lahir dari keresahan dan kesulitan mahasiswa maupun dosen dalam beberapa layanan, antara lain pendaftaran TA, bimbingan online, dokumen online, peminjaman ruangan dan lain sebagainya. Sejak dibuat tahun 2021 oleh tim unit lab FIK, aplikasi berbasis web ini telah digunakan hingga saat ini untuk mempermudah layanan untuk kalangan internal FIK, baik untuk mahasiswa, dosen maupun pegawai FIK.';
            $default->dekanat_image  = 'dekanat2.png';
            $default->slide_duration = 4;
            $default->slide1_image   = 'Fakultas.jpg';
            $default->slide2_video   = 'vidtelkom.mp4';
            $default->updated_at     = null;
            return $default;
        }
        return $row;
    }

    /**
     * Memperbarui konfigurasi header pada database.
     * Melakukan operasi UPDATE jika record ID 1 sudah ada, atau INSERT jika belum tersedia.
     * 
     * @access public
     * @param  array $data Array asosiatif kolom konfigurasi yang akan disimpan
     * @return bool Status keberhasilan query update/insert
     */
    public function update_settings($data) {
        $this->_ensure_tables();
        $data['updated_at'] = date('Y-m-d H:i:s');
        $existing = $this->db->get_where('header_settings', ['id' => 1])->row();
        if ($existing) {
            $this->db->where('id', 1);
            return $this->db->update('header_settings', $data);
        } else {
            $data['id'] = 1;
            return $this->db->insert('header_settings', $data);
        }
    }

    /**
     * Mengambil seluruh daftar slide hero banner aktif yang diurutkan berdasarkan kolom order_num secara menaik.
     * 
     * @access public
     * @return array Array objek slide banner
     */
    public function get_slides() {
        $table = $this->_get_panel_table();
        if (!$this->db->table_exists($table)) {
            return [];
        }
        $this->db->order_by('order_num', 'ASC');
        return $this->db->get($table)->result();
    }

    /**
     * Mengambil satu baris data slide hero banner berdasarkan ID uniknya.
     * 
     * @access public
     * @param  int $id ID slide yang dicari
     * @return object|null Data slide berupa objek atau null jika tidak ditemukan
     */
    public function get_slide($id) {
        $table = $this->_get_panel_table();
        return $this->db->get_where($table, ['id' => $id])->row();
    }

    /**
     * Menambahkan slide banner baru ke dalam tabel slide.
     * 
     * @access public
     * @param  array $data Data slide baru (judul, gambar/video, urutan, dll)
     * @return bool Status keberhasilan insert data
     */
    public function add_slide($data) {
        $table = $this->_get_panel_table();
        return $this->db->insert($table, $data);
    }

    /**
     * Menghapus rekaman slide banner dari tabel berdasarkan ID.
     * 
     * @access public
     * @param  int $id ID slide yang akan dihapus
     * @return bool Status keberhasilan delete data
     */
    public function delete_slide($id) {
        $table = $this->_get_panel_table();
        $this->db->where('id', $id);
        return $this->db->delete($table);
    }

    /**
     * Memperbarui urutan tampil slide banner (order_num) untuk mendukung re-ordering drag-and-drop.
     * 
     * @access public
     * @param  int $id ID slide yang diubah urutannya
     * @param  int $order_num Nilai nomor urut baru
     * @return bool Status keberhasilan update urutan
     */
    public function update_slide_order($id, $order_num) {
        $table = $this->_get_panel_table();
        $this->db->where('id', $id);
        return $this->db->update($table, ['order_num' => $order_num]);
    }
}
