<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LaboranTicketing extends CI_Controller {

    private $table = 'dosen_ticketing';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'text']);
        $this->load->model('DosenTicketing_model');
    }

    /**
     * Filter query khusus untuk unit Laboratorium & Sarpras
     */
    private function _apply_lab_unit_filter() {
        $this->db->group_start();
        $this->db->like('unit_tujuan', 'Laboratorium');
        $this->db->or_like('unit_tujuan', 'Lab');
        $this->db->or_like('unit_tujuan', 'Sarpras');
        $this->db->group_end();
    }

    /**
     * Halaman Utama Inbox Respon Tiket Masuk Khusus Laboran
     */
    public function index() {
        $filterStatus = $this->input->get('status', true) ?: 'all';
        $search = trim($this->input->get('q', true) ?? '');

        // 1. Query Tiket Masuk Khusus Unit Laboratorium
        $this->db->from($this->table);
        $this->_apply_lab_unit_filter();

        if (!empty($filterStatus) && $filterStatus !== 'all') {
            $this->db->where('status', $filterStatus);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode_tiket', $search);
            $this->db->or_like('nama_dosen', $search);
            $this->db->or_like('subjek', $search);
            $this->db->or_like('kategori', $search);
            $this->db->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        $tickets = $this->db->get()->result();

        // 2. Hitung Statistik Khusus Unit Laboratorium
        $stats = [
            'total'    => $this->count_lab_tickets_by_status('all'),
            'menunggu' => $this->count_lab_tickets_by_status('Menunggu'),
            'diproses' => $this->count_lab_tickets_by_status('Diproses'),
            'selesai'  => $this->count_lab_tickets_by_status('Selesai'),
            'ditutup'  => $this->count_lab_tickets_by_status('Ditutup')
        ];

        $data = [
            'title'        => 'Inbox Respon Tiket Masuk — Panel Laboran',
            'tickets'      => $tickets,
            'stats'        => $stats,
            'filterStatus' => $filterStatus,
            'search'       => $search
        ];

        $this->load->view('laboran/ticketing_respon', $data);
    }

    /**
     * Hitung total tiket per status khusus Laboratorium
     */
    private function count_lab_tickets_by_status($status = 'all') {
        $this->db->from($this->table);
        $this->_apply_lab_unit_filter();
        if ($status !== 'all') {
            $this->db->where('status', $status);
        }
        return (int)$this->db->count_all_results();
    }

    /**
     * AJAX Endpoint: Detail Tiket untuk Modal Respon
     */
    public function detail($id_or_kode) {
        $this->db->from($this->table);
        $this->_apply_lab_unit_filter();
        if (is_numeric($id_or_kode)) {
            $this->db->where('id', (int)$id_or_kode);
        } else {
            $this->db->where('kode_tiket', $id_or_kode);
        }
        $ticket = $this->db->get()->row();

        if (!$ticket) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['status' => false, 'message' => 'Tiket tidak ditemukan atau bukan ditujukan ke Laboratorium.']));
        }

        $response = [
            'status' => true,
            'data'   => [
                'id'             => $ticket->id,
                'kode_tiket'     => $ticket->kode_tiket,
                'nama_dosen'     => $ticket->nama_dosen,
                'nidn'           => $ticket->nidn ?: '-',
                'unit_tujuan'    => $ticket->unit_tujuan,
                'kategori'       => $ticket->kategori,
                'prioritas'      => $ticket->prioritas,
                'subjek'         => $ticket->subjek,
                'deskripsi'      => $ticket->deskripsi,
                'lampiran'       => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                'lampiran_name'  => $ticket->lampiran,
                'status'         => $ticket->status,
                'tanggapan'      => $ticket->tanggapan,
                'tgl_tanggapan'  => $ticket->tgl_tanggapan ? date('d M Y - H:i', strtotime($ticket->tgl_tanggapan)) : null,
                'created_at_fmt' => date('d M Y - H:i', strtotime($ticket->created_at))
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Simpan Tanggapan & Perubahan Status oleh Laboran
     */
    public function simpan_tanggapan() {
        $id = $this->input->post('ticket_id', true);
        $status = $this->input->post('status', true);
        $tanggapan = trim($this->input->post('tanggapan', true));

        // Validasi input
        if (empty($id) || empty($status) || empty($tanggapan)) {
            $this->session->set_flashdata('error', 'Harap isi status tiket dan tanggapan dengan lengkap.');
            redirect('laboran/respon-ticketing');
            return;
        }

        // Cek tiket valid dan ditujukan ke lab
        $this->db->from($this->table);
        $this->db->where('id', (int)$id);
        $this->_apply_lab_unit_filter();
        $ticket = $this->db->get()->row();

        if (!$ticket) {
            $this->session->set_flashdata('error', 'Tiket tidak ditemukan atau Anda tidak memiliki akses.');
            redirect('laboran/respon-ticketing');
            return;
        }

        $allowedStatus = ['Menunggu', 'Diproses', 'Selesai', 'Ditutup'];
        if (!in_array($status, $allowedStatus)) {
            $status = 'Diproses';
        }

        $updateData = [
            'status'        => $status,
            'tanggapan'     => $tanggapan,
            'tgl_tanggapan' => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', (int)$id);
        $this->db->update($this->table, $updateData);

        $this->session->set_flashdata('success', "Tiket {$ticket->kode_tiket} berhasil direspon dengan status '{$status}'.");
        redirect('laboran/respon-ticketing');
    }

    /**
     * Halaman Buat Tiket Kendala Baru oleh Laboran
     */
    public function input() {
        $userId = $this->session->userdata('user_id');
        $nidn = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');
        $nama = $this->session->userdata('name') ?: 'Laboran';
        $email = $this->session->userdata('email');

        $unit_kategori_map = [
            'LAA (Layanan Akademik & Administrasi)' => [
                'Validasi Berkas Pendaftaran TA',
                'Transkrip Nilai & KSM',
                'Surat Keterangan / Pengantar Akademik',
                'Administrasi Kelulusan & Wisuda',
                'Lain-lain (LAA)'
            ],
            'Koordinator Tugas Akhir (Koor TA)' => [
                'Pengajuan / Perubahan Topik TA',
                'Penjadwalan Seminar & Sidang TA',
                'Alokasi Pembimbing & Penguji',
                'Nilai Akhir & Berita Acara Sidang',
                'Kuota & Batas Waktu Bimbingan TA',
                'Lain-lain (Koordinator TA)'
            ],
            'Ketua Kelompok Keahlian (Ketua KK)' => [
                'Kesesuaian Topik TA dengan KK',
                'Rekomendasi Usulan TA Mahasiswa',
                'Kuota Bimbingan Dosen Kelompok Keahlian',
                'Lain-lain (Ketua KK)'
            ],
            'Laboratorium & Sarana Prasarana (Lab/Sarpras)' => [
                'Peminjaman Ruangan Lab / Studio',
                'Pengurusan Surat Bebas Laboratorium',
                'Kendala Perangkat Hardware & PC Lab',
                'Lisensi Software & Aplikasi Komputer Lab',
                'Lain-lain (Lab & Sarpras)'
            ],
            'IT Support & Pusat Sistem Informasi' => [
                'Akun, Password & Hak Akses Portal',
                'Bug / Error Teknis Sistem Web IFIK',
                'Sinkronisasi Data & Riwayat Log Approval',
                'Usulan Fitur Baru / Perbaikan Fitur',
                'Lain-lain (IT Support)'
            ],
            'Dosen Wali & Akademik' => [
                'Bimbingan Akademik Mahasiswa Wali',
                'Dispensasi & Masalah Studi Mahasiswa',
                'Perwalian & Rencana Studi Semester',
                'Lain-lain (Dosen Wali)'
            ]
        ];

        $data = [
            'title'             => 'Buat Tiket Kendala Baru — Panel Laboran',
            'active_menu'       => 'ticketing_input',
            'user'              => [
                'id'    => $userId,
                'nidn'  => $nidn,
                'nama'  => $nama,
                'email' => $email
            ],
            'unit_kategori_map' => $unit_kategori_map,
            'prioritas_list'    => [
                'Rendah'  => ['label' => 'Rendah', 'color' => 'slate', 'desc' => 'Pertanyaan umum / kendala minor'],
                'Sedang'  => ['label' => 'Sedang', 'color' => 'blue', 'desc' => 'Kendala kerja rutin tanpa hambatan fatal'],
                'Tinggi'  => ['label' => 'Tinggi', 'color' => 'amber', 'desc' => 'Proses tertunda, butuh respon cepat'],
                'Darurat' => ['label' => 'Darurat', 'color' => 'rose', 'desc' => 'Sistem kritis / jadwal mendesak hari ini']
            ],
            'form_action'       => site_url('laboran/ticketing/simpan')
        ];

        $this->load->view('laboran/ticketing_input', $data);
    }

    /**
     * Simpan Tiket Baru yang diajukan oleh Laboran
     */
    public function simpan() {
        $userId      = $this->session->userdata('user_id');
        $nidn        = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');
        $namaLengkap = trim($this->input->post('nama_lengkap', true)) ?: ($this->session->userdata('name') ?: 'Laboran');

        $unit_tujuan      = trim($this->input->post('unit_tujuan', true));
        $kategori         = trim($this->input->post('kategori', true));
        $kategori_lainnya = trim($this->input->post('kategori_lainnya', true));
        $prioritas        = trim($this->input->post('prioritas', true));
        $subjek           = trim($this->input->post('subjek', true));
        $deskripsi        = $this->input->post('deskripsi'); // Rich text from TinyMCE

        $textOnly = trim(strip_tags($deskripsi));
        if (empty($namaLengkap) || empty($unit_tujuan) || empty($kategori) || empty($subjek) || empty($textOnly)) {
            $this->session->set_flashdata('error', 'Semua field bertanda bintang wajib diisi.');
            redirect('laboran/ticketing/input');
            return;
        }

        // Cek jika kategori berupa 'Lainnya' / 'Lain-lain'
        if (preg_match('/lain/i', $kategori)) {
            if (!empty($kategori_lainnya)) {
                $kategori = $kategori . ': ' . $kategori_lainnya;
            }
        }

        // Anti-Duplicate check (5 detik)
        if ($userId) {
            $this->db->where('id_user', $userId);
            $this->db->where('subjek', $subjek);
            $this->db->where('unit_tujuan', $unit_tujuan);
            $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-5 seconds')));
            $recentTicket = $this->db->get($this->table)->row();

            if ($recentTicket) {
                $this->session->set_flashdata('success', "Tiket kendala berhasil diajukan dengan Kode: <b>{$recentTicket->kode_tiket}</b> ke unit <b>" . htmlspecialchars($unit_tujuan) . "</b>.");
                redirect('laboran/ticketing/riwayat');
                return;
            }
        }

        // Upload lampiran
        $lampiran_name = null;
        if (!empty($_FILES['lampiran']['name'])) {
            $uploadPath = FCPATH . 'uploads/ticketing/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $config['upload_path']   = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx';
            $config['max_size']      = 5120; // 5MB
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('lampiran')) {
                $uploadData = $this->upload->data();
                $lampiran_name = $uploadData['file_name'];
            }
        }

        $ticketData = [
            'id_user'     => $userId,
            'nama_dosen'  => $namaLengkap,
            'nidn'        => $nidn,
            'unit_tujuan' => $unit_tujuan,
            'kategori'    => $kategori,
            'prioritas'   => in_array($prioritas, ['Rendah', 'Sedang', 'Tinggi', 'Darurat']) ? $prioritas : 'Sedang',
            'subjek'      => $subjek,
            'deskripsi'   => $deskripsi,
            'lampiran'    => $lampiran_name,
            'status'      => 'Menunggu'
        ];

        $insertedId = $this->DosenTicketing_model->insert($ticketData);
        $createdTicket = $this->DosenTicketing_model->get_by_id($insertedId);

        $this->session->set_flashdata('success', "Tiket Anda berhasil diajukan dengan Kode: <b>{$createdTicket->kode_tiket}</b> ke unit <b>" . htmlspecialchars($unit_tujuan) . "</b>.");
        redirect('laboran/ticketing/riwayat');
    }

    /**
     * Riwayat Tiket yang pernah diajukan oleh Laboran
     */
    public function riwayat() {
        $userId = $this->session->userdata('user_id');
        $nidn   = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');

        $tickets = $this->DosenTicketing_model->get_tickets($userId, $nidn);
        $stats   = $this->DosenTicketing_model->get_stats($userId, $nidn);

        $data = [
            'title'       => 'Riwayat Tiket Kendala Saya — Panel Laboran',
            'active_menu' => 'ticketing_riwayat',
            'tickets'     => $tickets,
            'stats'       => $stats
        ];

        $this->load->view('laboran/ticketing_riwayat', $data);
    }

    /**
     * AJAX Endpoint: Detail Tiket untuk Riwayat Laboran
     */
    public function riwayat_detail($id_or_kode) {
        $ticket = $this->DosenTicketing_model->get_by_id($id_or_kode);

        if (!$ticket) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Tiket tidak ditemukan.'
                ]));
        }

        $userId = $this->session->userdata('user_id');
        $nidn   = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');
        $roleId = (int)$this->session->userdata('role_id');

        // Access check: Superadmin (1), Laboran (2), or Owner
        if ($roleId !== 1 && $roleId !== 2 && $ticket->id_user != $userId && $ticket->nidn != $nidn) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk melihat tiket ini.'
                ]));
        }

        $isHtml = (strpos($ticket->deskripsi, '<') !== false && strpos($ticket->deskripsi, '>') !== false);
        $deskripsiFormatted = $isHtml ? $ticket->deskripsi : nl2br(htmlspecialchars($ticket->deskripsi));

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'data'    => [
                    'id'            => $ticket->id,
                    'kode_tiket'    => $ticket->kode_tiket,
                    'nama_dosen'    => $ticket->nama_dosen,
                    'nidn'          => $ticket->nidn,
                    'unit_tujuan'   => $ticket->unit_tujuan ?: 'Layanan IFIK',
                    'kategori'      => $ticket->kategori,
                    'prioritas'     => $ticket->prioritas,
                    'subjek'        => $ticket->subjek,
                    'deskripsi'     => $deskripsiFormatted,
                    'lampiran'      => $ticket->lampiran,
                    'lampiran_url'  => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                    'status'        => $ticket->status,
                    'tanggapan'     => $ticket->tanggapan ? nl2br(htmlspecialchars($ticket->tanggapan)) : null,
                    'tgl_tanggapan' => $ticket->tgl_tanggapan ? date('d M Y H:i', strtotime($ticket->tgl_tanggapan)) : null,
                    'created_at'    => date('d M Y H:i', strtotime($ticket->created_at)),
                    'updated_at'    => date('d M Y H:i', strtotime($ticket->updated_at))
                ]
            ]));
    }
}
