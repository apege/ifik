<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->_ensure_tables();
    }

    private function _ensure_tables()
    {
        if (!$this->db->table_exists('kategori_ruangan')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `kategori_ruangan` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nama_kategori` VARCHAR(100) NOT NULL,
                `keterangan` VARCHAR(255) NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->db->query("INSERT IGNORE INTO `kategori_ruangan` (`id`, `nama_kategori`, `keterangan`) VALUES
                (1, 'Laboratorium Komputer', 'Lab dengan fasilitas PC high-end'),
                (2, 'Laboratorium Desain', 'Lab untuk karya fisik'),
                (3, 'Ruang Rapat & Seminar', 'Ruangan presentasi')");
        }

        if (!$this->db->table_exists('ruangan')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `ruangan` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `id_kategori` INT NOT NULL,
                `kode_ruangan` VARCHAR(50) NOT NULL,
                `nama_ruangan` VARCHAR(150) NOT NULL,
                `kapasitas` INT DEFAULT 30,
                `lokasi` VARCHAR(150) DEFAULT 'Gedung Sebatik (FIK)',
                `status` ENUM('Tersedia', 'Tidak Tersedia', 'Perbaikan') DEFAULT 'Tersedia',
                `foto` VARCHAR(255) NULL,
                `model_3d` VARCHAR(255) NULL,
                `tagline` VARCHAR(255) NULL,
                `jumlah_unit` VARCHAR(100) NULL,
                `jam_operasional` VARCHAR(100) NULL,
                `deskripsi` TEXT NULL,
                `spesifikasi_fasilitas` TEXT NULL,
                `tata_tertib` TEXT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->db->query("INSERT IGNORE INTO `ruangan` (`id`, `id_kategori`, `kode_ruangan`, `nama_ruangan`, `kapasitas`, `lokasi`, `status`) VALUES
                (1, 1, 'LAB-MM', 'Lab Multimedia & 3D Design', 40, 'Gedung Sebatik Lt. 2', 'Tersedia'),
                (2, 1, 'LAB-UIUX', 'Lab UI/UX & Web Development', 35, 'Gedung Sebatik Lt. 2', 'Tersedia'),
                (3, 2, 'LAB-GRAFIS', 'Studio Desain Grafis & Seni', 30, 'Gedung Sebatik Lt. 1', 'Tersedia'),
                (4, 3, 'AUD-FIK', 'Auditorium FIK', 150, 'Gedung Sebatik Lt. 3', 'Tersedia')");
        } else {
            // Auto-migrasi: pastikan seluruh kolom lengkap jika tabel dibuat dengan skema lama
            $fields = $this->db->list_fields('ruangan');
            $new_cols = array(
                'foto'                  => "VARCHAR(255) NULL",
                'model_3d'              => "VARCHAR(255) NULL",
                'tagline'               => "VARCHAR(255) NULL",
                'jumlah_unit'           => "VARCHAR(100) NULL",
                'jam_operasional'       => "VARCHAR(100) NULL",
                'deskripsi'             => "TEXT NULL",
                'spesifikasi_fasilitas' => "TEXT NULL",
                'tata_tertib'           => "TEXT NULL"
            );
            foreach ($new_cols as $col => $type) {
                if (!in_array($col, $fields)) {
                    $this->db->query("ALTER TABLE `ruangan` ADD COLUMN `{$col}` {$type}");
                }
            }
        }

        if (!$this->db->table_exists('peminjaman')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `peminjaman` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `id_ruangan` INT NOT NULL,
                `nama_lengkap` VARCHAR(150) NOT NULL,
                `keterangan` TEXT NULL,
                `tanggal_mulai` DATE NOT NULL,
                `tanggal_selesai` DATE NOT NULL,
                `jam_mulai` TIME NOT NULL,
                `jam_selesai` TIME NOT NULL,
                `status` VARCHAR(50) DEFAULT 'Pending',
                `alasan_penolakan` TEXT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->db->query("INSERT IGNORE INTO `peminjaman` (`id_ruangan`, `nama_lengkap`, `keterangan`, `tanggal_mulai`, `tanggal_selesai`, `jam_mulai`, `jam_selesai`, `status`) VALUES
                (1, 'Alif Mahasiswa', 'Kegiatan Pameran Interaktif 3D', CURDATE(), CURDATE(), '08:00:00', '12:00:00', 'Disetujui Admin')");
        }

        if (!$this->db->table_exists('slot_waktu')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `slot_waktu` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nama_slot` VARCHAR(50) NOT NULL,
                `jam_mulai` TIME NOT NULL,
                `jam_selesai` TIME NOT NULL,
                `urutan` INT DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->db->query("INSERT IGNORE INTO `slot_waktu` (`id`, `nama_slot`, `jam_mulai`, `jam_selesai`, `urutan`) VALUES
                (1, 'Sesi Pagi 1', '08:00:00', '10:00:00', 1),
                (2, 'Sesi Pagi 2', '10:00:00', '12:00:00', 2),
                (3, 'Sesi Siang 1', '13:00:00', '15:00:00', 3),
                (4, 'Sesi Siang 2', '15:00:00', '17:00:00', 4)");
        }
    }

    public function get_all_kategori()
    {
        return $this->db->get('kategori_ruangan')->result();
    }

    public function get_ruangan_by_kategori($id_kategori)
    {
        $this->db->where('id_kategori', $id_kategori);
        $this->db->where('status', 'Tersedia');
        return $this->db->get('ruangan')->result();
    }

    public function get_all_slot_waktu()
    {
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get('slot_waktu')->result();
    }

    public function get_all_peminjaman()
    {
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->order_by('peminjaman.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function insert_booking($data)
    {
        $this->db->trans_start();

        // Insert into peminjaman
        $this->db->insert('peminjaman', $data);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function update_status($id, $status, $alasan = null)
    {
        $data = array('status' => $status);
        if ($alasan !== null) {
            $data['alasan_penolakan'] = $alasan;
        }

        $this->db->where('id', $id);
        return $this->db->update('peminjaman', $data);
    }

    public function delete_booking($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('peminjaman');
    }

    public function get_approved_bookings()
    {
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan, ruangan.id_kategori, kategori_ruangan.nama_kategori');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->join('kategori_ruangan', 'kategori_ruangan.id = ruangan.id_kategori', 'left');
        // Tampilkan semua kecuali yang Ditolak dan Dibatalkan
        $this->db->where_not_in('peminjaman.status', ['Ditolak', 'Dibatalkan']);
        $this->db->order_by('peminjaman.tanggal_mulai', 'ASC');
        $this->db->order_by('peminjaman.jam_mulai', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Check if a room booking conflicts with existing non-rejected/non-cancelled bookings
     */
    public function check_conflict($id_ruangan, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai, $ignore_id = null)
    {
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->where('peminjaman.id_ruangan', $id_ruangan);
        $this->db->where_not_in('peminjaman.status', ['Ditolak', 'Dibatalkan']);

        // Check date range overlap
        $this->db->where('peminjaman.tanggal_mulai <=', $tanggal_selesai);
        $this->db->where('peminjaman.tanggal_selesai >=', $tanggal_mulai);

        // Check time range overlap (strict overlap: start < existing_end AND end > existing_start)
        $this->db->where('peminjaman.jam_mulai <', $jam_selesai);
        $this->db->where('peminjaman.jam_selesai >', $jam_mulai);

        if ($ignore_id !== null) {
            $this->db->where('peminjaman.id !=', $ignore_id);
        }

        return $this->db->get()->result();
    }
}

