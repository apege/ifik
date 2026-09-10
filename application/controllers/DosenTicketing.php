<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenTicketing extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Pengecekan sesi login
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu Ticketing.');
            redirect('login');
            return;
        }

        $this->load->model('DosenTicketing_model');
        $this->load->helper(['url', 'form']);
    }

    /**
     * Default index: Redirect to input
     */
    public function index() {
        redirect('dosen/ticketing/input');
    }

    /**
     * Halaman Input Tiket Baru
     */
    public function input() {
        $userId = $this->session->userdata('user_id');
        $nidn = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');
        $nama = $this->session->userdata('name');
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
            'title'             => 'Input Tiket Kendala Dosen',
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
            ]
        ];

        $this->load->view('dosen/ticketing_input', $data);
    }

    /**
     * Proses Simpan Tiket Baru
     */
    public function simpan() {
        $userId      = $this->session->userdata('user_id');
        $nidn        = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');
        $namaLengkap = trim($this->input->post('nama_lengkap', true)) ?: ($this->session->userdata('name') ?: 'Dosen');
        $unitTujuan      = trim($this->input->post('unit_tujuan', true));
        $kategori        = trim($this->input->post('kategori', true));
        $kategoriLainnya = trim($this->input->post('kategori_lainnya', true));
        $prioritas       = trim($this->input->post('prioritas', true));
        $subjek          = trim($this->input->post('subjek', true));
        $deskripsi       = $this->input->post('deskripsi'); // Rich text from TinyMCE

        // Validasi input
        $textOnly = trim(strip_tags($deskripsi));
        if (empty($namaLengkap) || empty($unitTujuan) || empty($kategori) || empty($subjek) || empty($textOnly)) {
            $this->session->set_flashdata('error', 'Harap lengkapi semua kolom wajib (Nama Lengkap, Unit yang Dituju, Kategori, Subjek, dan Deskripsi).');
            redirect('dosen/ticketing/input');
            return;
        }

        // Cek jika kategori berupa 'Lainnya' / 'Lain-lain'
        if (preg_match('/lain/i', $kategori)) {
            if (empty($kategoriLainnya)) {
                $this->session->set_flashdata('error', 'Harap sebutkan rincian kendala pada kolom Kategori Lainnya.');
                redirect('dosen/ticketing/input');
                return;
            }
            $kategori = $kategori . ': ' . $kategoriLainnya;
        }

        if (!in_array($prioritas, ['Rendah', 'Sedang', 'Tinggi', 'Darurat'])) {
            $prioritas = 'Sedang';
        }

        // Anti-Duplicate / Debounce Check (Mencegah submit ganda akibat double-click)
        $this->db->where('id_user', $userId);
        $this->db->where('subjek', $subjek);
        $this->db->where('unit_tujuan', $unitTujuan);
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-5 seconds')));
        $recentTicket = $this->db->get('dosen_ticketing')->row();

        if ($recentTicket) {
            $this->session->set_flashdata('success', "Tiket kendala berhasil dikirim dengan kode: <strong>{$recentTicket->kode_tiket}</strong> ke unit <strong>" . htmlspecialchars($unitTujuan) . "</strong>.");
            redirect('dosen/ticketing/riwayat');
            return;
        }

        $lampiranFile = null;

        // Upload lampiran jika ada
        if (!empty($_FILES['lampiran']['name'])) {
            $uploadPath = FCPATH . 'uploads/ticketing/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $config = [
                'upload_path'   => $uploadPath,
                'allowed_types' => 'jpg|jpeg|png|pdf|doc|docx|zip|rar',
                'max_size'      => 10240, // 10MB
                'encrypt_name'  => TRUE
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('lampiran')) {
                $uploadData = $this->upload->data();
                $lampiranFile = $uploadData['file_name'];
            } else {
                $uploadError = $this->upload->display_errors('', '');
                $this->session->set_flashdata('error', 'Gagal mengunggah lampiran: ' . $uploadError);
                redirect('dosen/ticketing/input');
                return;
            }
        }

        // Generate Kode Tiket
        $kodeTiket = $this->DosenTicketing_model->generate_kode();

        $ticketData = [
            'kode_tiket'  => $kodeTiket,
            'id_user'     => $userId,
            'nama_dosen'  => $namaLengkap,
            'nidn'        => $nidn,
            'unit_tujuan' => $unitTujuan,
            'kategori'    => $kategori,
            'prioritas'   => $prioritas,
            'subjek'      => $subjek,
            'deskripsi'   => $deskripsi,
            'lampiran'    => $lampiranFile,
            'status'      => 'Menunggu'
        ];

        $insertId = $this->DosenTicketing_model->insert($ticketData);

        if ($insertId) {
            $this->session->set_flashdata('success', "Tiket kendala berhasil dikirim dengan kode: <strong>{$kodeTiket}</strong> ke unit <strong>" . htmlspecialchars($unitTujuan) . "</strong>. Tim terkait akan segera meninjau laporan Anda.");
            redirect('dosen/ticketing/riwayat');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem saat menyimpan tiket. Silakan coba beberapa saat lagi.');
            redirect('dosen/ticketing/input');
        }
    }

    /**
     * Halaman Riwayat Tiket
     */
    public function riwayat() {
        $userId = $this->session->userdata('user_id');
        $nidn = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');
        $roleId = (int)$this->session->userdata('role_id');

        // Superadmin (role 1) can view all tickets, others view their own
        $filterUser = ($roleId === 1) ? null : $userId;
        $filterNidn = ($roleId === 1) ? null : $nidn;

        $tickets = $this->DosenTicketing_model->get_tickets($filterUser, $filterNidn);
        $stats = $this->DosenTicketing_model->get_stats($filterUser, $filterNidn);

        $data = [
            'title'       => 'Riwayat Ticketing Dosen',
            'active_menu' => 'ticketing_riwayat',
            'tickets'     => $tickets,
            'stats'       => $stats,
            'is_admin'    => ($roleId === 1)
        ];

        $this->load->view('dosen/ticketing_riwayat', $data);
    }

    /**
     * AJAX endpoint: Detail Tiket
     */
    public function detail($id_or_kode) {
        $ticket = $this->DosenTicketing_model->get_by_id($id_or_kode);

        if (!$ticket) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Tiket tidak ditemukan.'
                ]));
            return;
        }

        $userId = $this->session->userdata('user_id');
        $nidn = $this->session->userdata('nidn_nim') ?: $this->session->userdata('nim');
        $roleId = (int)$this->session->userdata('role_id');

        // Access check: Only owner or superadmin
        if ($roleId !== 1 && $ticket->id_user != $userId && $ticket->nidn != $nidn) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk melihat tiket ini.'
                ]));
            return;
        }

        $isHtml = (strpos($ticket->deskripsi, '<') !== false && strpos($ticket->deskripsi, '>') !== false);
        $deskripsiFormatted = $isHtml ? $ticket->deskripsi : nl2br(htmlspecialchars($ticket->deskripsi));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'data'   => [
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

    /**
     * Filter query khusus untuk tiket yang ditujukan ke Dosen / Dosen Wali / Koordinator TA / Ketua KK
     */
    private function _apply_dosen_unit_filter() {
        $this->db->group_start();
        $this->db->like('unit_tujuan', 'Dosen');
        $this->db->or_like('unit_tujuan', 'Wali');
        $this->db->or_like('unit_tujuan', 'Koordinator');
        $this->db->or_like('unit_tujuan', 'Ketua KK');
        $this->db->group_end();
    }

    /**
     * Halaman Inbox Respon Ticketing khusus Role Dosen
     */
    public function respon_index() {
        $filterStatus = $this->input->get('status', true) ?: 'all';
        $search = trim($this->input->get('q', true) ?? '');

        // 1. Query Tiket Masuk Khusus Dosen
        $this->db->from('dosen_ticketing');
        $this->_apply_dosen_unit_filter();

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

        // 2. Hitung Statistik Khusus Unit Dosen
        $stats = [
            'total'    => $this->count_dosen_tickets_by_status('all'),
            'menunggu' => $this->count_dosen_tickets_by_status('Menunggu'),
            'diproses' => $this->count_dosen_tickets_by_status('Diproses'),
            'selesai'  => $this->count_dosen_tickets_by_status('Selesai'),
            'ditutup'  => $this->count_dosen_tickets_by_status('Ditutup')
        ];

        $data = [
            'title'        => 'Inbox Respon Tiket Dosen — IFIK Portal',
            'tickets'      => $tickets,
            'stats'        => $stats,
            'filterStatus' => $filterStatus,
            'search'       => $search
        ];

        $this->load->view('dosen/ticketing_respon', $data);
    }

    /**
     * Hitung total tiket per status khusus Dosen
     */
    private function count_dosen_tickets_by_status($status = 'all') {
        $this->db->from('dosen_ticketing');
        $this->_apply_dosen_unit_filter();
        if ($status !== 'all') {
            $this->db->where('status', $status);
        }
        return (int)$this->db->count_all_results();
    }

    /**
     * AJAX Endpoint: Detail Tiket untuk Modal Respon Dosen
     */
    public function respon_detail($id_or_kode) {
        $this->db->from('dosen_ticketing');
        $this->_apply_dosen_unit_filter();
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
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Tiket kendala tidak ditemukan atau bukan ditujukan untuk unit Dosen.'
                ]));
        }

        $isHtml = (strpos($ticket->deskripsi, '<') !== false && strpos($ticket->deskripsi, '>') !== false);
        $deskripsiFormatted = $isHtml ? $ticket->deskripsi : nl2br(htmlspecialchars($ticket->deskripsi));

        $response = [
            'status' => true,
            'data'   => [
                'id'            => $ticket->id,
                'kode_tiket'    => $ticket->kode_tiket,
                'nama_dosen'    => $ticket->nama_dosen,
                'nidn'          => $ticket->nidn ?: '-',
                'unit_tujuan'   => $ticket->unit_tujuan,
                'kategori'      => $ticket->kategori,
                'prioritas'     => $ticket->prioritas,
                'subjek'        => $ticket->subjek,
                'deskripsi'     => $deskripsiFormatted,
                'lampiran'      => $ticket->lampiran,
                'lampiran_url'  => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                'status'        => $ticket->status,
                'tanggapan'     => $ticket->tanggapan,
                'tgl_tanggapan' => $ticket->tgl_tanggapan ? date('d M Y H:i', strtotime($ticket->tgl_tanggapan)) : null,
                'created_at'    => date('d M Y H:i', strtotime($ticket->created_at))
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Simpan Tanggapan & Perubahan Status oleh Dosen
     */
    public function respon_simpan_tanggapan() {
        $idTiket   = (int)($this->input->post('id_tiket') ?: $this->input->post('ticket_id'));
        $status    = trim($this->input->post('status', true));
        $tanggapan = trim($this->input->post('tanggapan') ?? '');

        if (!$idTiket) {
            $this->session->set_flashdata('error', 'ID Tiket tidak valid.');
            redirect('dosen/respon-ticketing');
            return;
        }

        if (!in_array($status, ['Menunggu', 'Diproses', 'Selesai', 'Ditutup'])) {
            $status = 'Diproses';
        }

        $updateData = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Jika dosen memberikan respon teks, simpan tanggapan dan waktu respon
        if ($tanggapan !== '') {
            $updateData['tanggapan'] = $tanggapan;
            $updateData['tgl_tanggapan'] = date('Y-m-d H:i:s');
        } elseif ($status === 'Menunggu') {
            // Jika dikembalikan ke status Menunggu dan teks tanggapan kosong
            $updateData['tanggapan'] = null;
            $updateData['tgl_tanggapan'] = null;
        }

        $this->db->where('id', $idTiket);
        $this->db->update('dosen_ticketing', $updateData);

        $msg = ($tanggapan !== '')
            ? "Tanggapan berhasil disimpan! Status tiket kini diperbarui menjadi <strong>{$status}</strong>."
            : "Status tiket berhasil diperbarui menjadi <strong>{$status}</strong>.";

        $this->session->set_flashdata('success', $msg);
        redirect('dosen/respon-ticketing');
    }
}

