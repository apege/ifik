<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help_chat_model extends CI_Model {

    private $table_conversations = 'help_conversations';
    private $table_messages = 'help_messages';

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->_ensure_tables();
    }

    /**
     * Pastikan tabel-tabel bantuan chat tersedia di database
     */
    private function _ensure_tables() {
        if (!$this->db->table_exists($this->table_conversations)) {
            $sql1 = "CREATE TABLE IF NOT EXISTS `{$this->table_conversations}` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT NULL,
                `user_nama` VARCHAR(150) NOT NULL,
                `user_email` VARCHAR(150) NULL,
                `user_role` VARCHAR(50) DEFAULT 'Mahasiswa',
                `user_nim_nip` VARCHAR(50) NULL,
                `topik` VARCHAR(255) NOT NULL,
                `status` ENUM('open', 'resolved') DEFAULT 'open',
                `laboran_id` INT NULL,
                `laboran_nama` VARCHAR(150) NULL,
                `last_message` TEXT NULL,
                `last_message_time` DATETIME NULL,
                `unread_laboran` INT DEFAULT 0,
                `unread_user` INT DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (`status`),
                INDEX (`user_id`),
                INDEX (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql1);
        }

        if (!$this->db->table_exists($this->table_messages)) {
            $sql2 = "CREATE TABLE IF NOT EXISTS `{$this->table_messages}` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `conversation_id` INT NOT NULL,
                `sender_id` INT NULL,
                `sender_name` VARCHAR(150) NOT NULL,
                `sender_role` ENUM('laboran', 'user') NOT NULL,
                `message` TEXT NOT NULL,
                `attachment` VARCHAR(255) NULL,
                `is_read` TINYINT(1) DEFAULT 0,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`conversation_id`),
                INDEX (`sender_role`),
                INDEX (`is_read`),
                INDEX (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->db->query($sql2);

            // Buat sample percakapan awal jika baru dibuat agar laboran bisa langsung melihat demo chat
            $this->_seed_initial_sample_data();
        }

        // Bersihkan karakter ???? pada pesan yang sudah ada
        $this->db->query("UPDATE `{$this->table_messages}` SET `message` = REPLACE(`message`, '????', '[Sistem]') WHERE `message` LIKE '%????%';");
        $this->db->query("UPDATE `{$this->table_conversations}` SET `last_message` = REPLACE(`last_message`, '????', '[Sistem]') WHERE `last_message` LIKE '%????%';");
    }

    /**
     * Sample initial conversations and messages for demo / initial view
     */
    private function _seed_initial_sample_data() {
        $count = $this->db->count_all($this->table_conversations);
        if ($count > 0) {
            return;
        }

        // Sample 1: Open chat dari Mahasiswa
        $this->db->insert($this->table_conversations, [
            'user_id'           => 991,
            'user_nama'         => 'Rian Firmansyah',
            'user_email'        => 'rian.firmansyah@student.telkomuniversity.ac.id',
            'user_role'         => 'Mahasiswa',
            'user_nim_nip'      => '1301213001',
            'topik'             => 'Peminjaman Ruangan Lab IoT untuk TA',
            'status'            => 'open',
            'last_message'      => 'Selamat pagi mas Laboran, apakah Lab IoT bisa digunakan untuk pengujian sensor besok sore?',
            'last_message_time' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
            'unread_laboran'    => 1,
            'unread_user'       => 0,
            'created_at'        => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'updated_at'        => date('Y-m-d H:i:s', strtotime('-15 minutes'))
        ]);
        $c1_id = $this->db->insert_id();

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c1_id,
            'sender_id'       => 991,
            'sender_name'     => 'Rian Firmansyah',
            'sender_role'     => 'user',
            'message'         => 'Halo, permisi mau tanya terkait izin penggunaan Lab IoT.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 hour'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c1_id,
            'sender_id'       => 991,
            'sender_name'     => 'Rian Firmansyah',
            'sender_role'     => 'user',
            'message'         => 'Selamat pagi mas Laboran, apakah Lab IoT bisa digunakan untuk pengujian sensor besok sore?',
            'is_read'         => 0,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-15 minutes'))
        ]);

        // Sample 2: Open chat dari Dosen
        $this->db->insert($this->table_conversations, [
            'user_id'           => 992,
            'user_nama'         => 'Dr. Hendra Gunawan, S.T., M.T.',
            'user_email'        => 'hendragunawan@telkomuniversity.ac.id',
            'user_role'         => 'Dosen',
            'user_nim_nip'      => '198503152010121001',
            'topik'             => 'Kabel Proyektor Lab Multimedia Bermasalah',
            'status'            => 'open',
            'last_message'      => 'Siap terima kasih, mohon dibantu dicek ya mas sebelum jam praktikum pukul 13.00.',
            'last_message_time' => date('Y-m-d H:i:s', strtotime('-45 minutes')),
            'unread_laboran'    => 1,
            'unread_user'       => 0,
            'created_at'        => date('Y-m-d H:i:s', strtotime('-2 hours')),
            'updated_at'        => date('Y-m-d H:i:s', strtotime('-45 minutes'))
        ]);
        $c2_id = $this->db->insert_id();

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c2_id,
            'sender_id'       => null,
            'sender_name'     => 'Petugas Laboran',
            'sender_role'     => 'laboran',
            'message'         => 'Selamat pagi pak Hendra, kami siapkan kabel pengganti dan langsung kami cek ke lokasi.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 hour 15 minutes'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c2_id,
            'sender_id'       => 992,
            'sender_name'     => 'Dr. Hendra Gunawan, S.T., M.T.',
            'sender_role'     => 'user',
            'message'         => 'Siap terima kasih, mohon dibantu dicek ya mas sebelum jam praktikum pukul 13.00.',
            'is_read'         => 0,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-45 minutes'))
        ]);

        // Sample 3: Resolved chat
        $this->db->insert($this->table_conversations, [
            'user_id'           => 993,
            'user_nama'         => 'Anisa Putri Maharani',
            'user_email'        => 'anisa.pm@student.telkomuniversity.ac.id',
            'user_role'         => 'Mahasiswa',
            'user_nim_nip'      => '1301210452',
            'topik'             => 'Formulir Peminjaman Osiloskop',
            'status'            => 'resolved',
            'laboran_nama'      => 'Laboran IFIK',
            'last_message'      => 'Sama-sama kak Anisa. Tiket bantuan telah diselesaikan. Jika ada pertanyaan lain silakan hubungi kami kembali.',
            'last_message_time' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'unread_laboran'    => 0,
            'unread_user'       => 0,
            'created_at'        => date('Y-m-d H:i:s', strtotime('-1 day -2 hours')),
            'updated_at'        => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]);
        $c3_id = $this->db->insert_id();

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c3_id,
            'sender_id'       => 993,
            'sender_name'     => 'Anisa Putri Maharani',
            'sender_role'     => 'user',
            'message'         => 'Permisi kak, untuk form peminjaman osiloskop digital diunduh di mana ya?',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 day -2 hours'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c3_id,
            'sender_id'       => null,
            'sender_name'     => 'Petugas Laboran',
            'sender_role'     => 'laboran',
            'message'         => 'Halo Anisa, formulir dapat diakses langsung pada menu Ajukan Booking atau unduh SOP di portal IFIK.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 day -1 hour'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c3_id,
            'sender_id'       => 993,
            'sender_name'     => 'Anisa Putri Maharani',
            'sender_role'     => 'user',
            'message'         => 'Baik kak sudah ketemu, terima kasih banyak ya!',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 day -30 minutes'))
        ]);

        $this->db->insert($this->table_messages, [
            'conversation_id' => $c3_id,
            'sender_id'       => null,
            'sender_name'     => 'Petugas Laboran',
            'sender_role'     => 'laboran',
            'message'         => 'Sama-sama kak Anisa. Tiket bantuan telah diselesaikan. Jika ada pertanyaan lain silakan hubungi kami kembali.',
            'is_read'         => 1,
            'created_at'      => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]);
    }

    /**
     * Ambil statistik percakapan help desk
     */
    public function get_stats() {
        $total = $this->db->count_all($this->table_conversations);

        $this->db->where('status', 'open');
        $open = $this->db->count_all_results($this->table_conversations);

        $this->db->where('status', 'resolved');
        $resolved = $this->db->count_all_results($this->table_conversations);

        $this->db->select_sum('unread_laboran');
        $unreadQuery = $this->db->get($this->table_conversations)->row();
        $unread = (int)($unreadQuery->unread_laboran ?? 0);

        return [
            'total'    => (int)$total,
            'open'     => (int)$open,
            'resolved' => (int)$resolved,
            'unread'   => (int)$unread
        ];
    }

    /**
     * Ambil list percakapan dengan filter status dan pencarian
     */
    public function get_conversations($status = 'all', $search = '') {
        $this->db->from($this->table_conversations);

        if (!empty($status) && $status !== 'all') {
            $this->db->where('status', $status);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('user_nama', $search);
            $this->db->or_like('user_nim_nip', $search);
            $this->db->or_like('topik', $search);
            $this->db->or_like('last_message', $search);
            $this->db->group_end();
        }

        $this->db->order_by('last_message_time', 'DESC');
        $this->db->order_by('created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Ambil single percakapan berdasarkan ID
     */
    public function get_conversation_by_id($id) {
        $this->db->from($this->table_conversations);
        $this->db->where('id', (int)$id);
        return $this->db->get()->row();
    }

    /**
     * Ambil daftar pesan dalam sebuah percakapan
     */
    public function get_messages($conversation_id) {
        $this->db->from($this->table_messages);
        $this->db->where('conversation_id', (int)$conversation_id);
        $this->db->order_by('created_at', 'ASC');
        $this->db->order_by('id', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Tandai semua pesan dari user sebagai telah dibaca oleh laboran
     */
    public function mark_as_read_by_laboran($conversation_id) {
        $conversation_id = (int)$conversation_id;

        // Update status is_read di tabel help_messages
        $this->db->where('conversation_id', $conversation_id);
        $this->db->where('sender_role', 'user');
        $this->db->where('is_read', 0);
        $this->db->update($this->table_messages, ['is_read' => 1]);

        // Reset unread_laboran pada help_conversations
        $this->db->where('id', $conversation_id);
        $this->db->update($this->table_conversations, ['unread_laboran' => 0]);

        return true;
    }

    /**
     * Kirim pesan baru dalam percakapan
     */
    public function send_message($conversation_id, $sender_id, $sender_name, $sender_role, $message, $attachment = null) {
        $now = date('Y-m-d H:i:s');
        $conversation_id = (int)$conversation_id;

        $msgData = [
            'conversation_id' => $conversation_id,
            'sender_id'       => $sender_id,
            'sender_name'     => $sender_name,
            'sender_role'     => $sender_role,
            'message'         => trim($message),
            'attachment'      => $attachment,
            'is_read'         => ($sender_role === 'laboran') ? 1 : 0,
            'created_at'      => $now
        ];

        $this->db->insert($this->table_messages, $msgData);
        $message_id = $this->db->insert_id();

        // Update info percakapan utama
        $convUpdate = [
            'last_message'      => trim($message),
            'last_message_time' => $now,
            'updated_at'        => $now
        ];

        if ($sender_role === 'laboran') {
            $convUpdate['laboran_id'] = $sender_id;
            $convUpdate['laboran_nama'] = $sender_name;
            // Tambah counter unread untuk user
            $this->db->set('unread_user', 'unread_user + 1', FALSE);
        } else {
            // Tambah counter unread untuk laboran
            $this->db->set('unread_laboran', 'unread_laboran + 1', FALSE);
        }

        $this->db->where('id', $conversation_id);
        $this->db->update($this->table_conversations, $convUpdate);

        return $message_id;
    }

    /**
     * Update status percakapan (open / resolved)
     */
    public function update_status($conversation_id, $status, $laboran_id = null, $laboran_nama = null) {
        $conversation_id = (int)$conversation_id;
        $status = ($status === 'resolved') ? 'resolved' : 'open';

        $data = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($laboran_id !== null) {
            $data['laboran_id'] = $laboran_id;
        }
        if ($laboran_nama !== null) {
            $data['laboran_nama'] = $laboran_nama;
        }

        $this->db->where('id', $conversation_id);
        return $this->db->update($this->table_conversations, $data);
    }

    /**
     * Membuat percakapan bantuan baru (Bisa dipanggil oleh modul Mahasiswa/Dosen nanti)
     */
    public function create_conversation($data) {
        $now = date('Y-m-d H:i:s');
        $convData = [
            'user_id'           => $data['user_id'] ?? null,
            'user_nama'         => $data['user_nama'] ?? 'Pengguna',
            'user_email'        => $data['user_email'] ?? null,
            'user_role'         => $data['user_role'] ?? 'Mahasiswa',
            'user_nim_nip'      => $data['user_nim_nip'] ?? null,
            'topik'             => $data['topik'] ?? 'Bantuan Umum',
            'status'            => 'open',
            'last_message'      => $data['message'] ?? '',
            'last_message_time' => $now,
            'unread_laboran'    => 1,
            'unread_user'       => 0,
            'created_at'        => $now,
            'updated_at'        => $now
        ];

        $this->db->insert($this->table_conversations, $convData);
        $conv_id = $this->db->insert_id();

        if (!empty($data['message'])) {
            $this->db->insert($this->table_messages, [
                'conversation_id' => $conv_id,
                'sender_id'       => $data['user_id'] ?? null,
                'sender_name'     => $data['user_nama'] ?? 'Pengguna',
                'sender_role'     => 'user',
                'message'         => trim($data['message']),
                'attachment'      => $data['attachment'] ?? null,
                'is_read'         => 0,
                'created_at'      => $now
            ]);
        }

        return $conv_id;
    }
}
