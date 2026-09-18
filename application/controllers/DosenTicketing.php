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

        // Ambil data Unit dan Kategori dinamis dari database (dengan fallback aman)
        $unit_kategori_map = [];
        if ($this->db->table_exists('ticketing_units')) {
            $units = $this->db
                ->where('is_active', 1)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('ticketing_units')
                ->result_array();

            foreach ($units as $u) {
                $categories = [];
                if ($this->db->table_exists('ticketing_kategori')) {
                    $categories = $this->db
                        ->where('unit_id', $u['id'])
                        ->where('is_active', 1)
                        ->order_by("(CASE WHEN nama_kategori LIKE 'Lain-lain%' OR nama_kategori LIKE 'Lainnya%' THEN 1 ELSE 0 END)", 'ASC', FALSE)
                        ->order_by('sort_order', 'ASC')
                        ->order_by('id', 'ASC')
                        ->get('ticketing_kategori')
                        ->result_array();
                }

                $catList = array_column($categories, 'nama_kategori');
                if (empty($catList)) {
                    $catList = ['Lain-lain (' . $u['nama_unit'] . ')'];
                }
                $unit_kategori_map[$u['nama_unit']] = $catList;
            }
        }

        // Fallback default unit & kategori jika tabel konfigurasi tidak ada
        if (empty($unit_kategori_map)) {
            $unit_kategori_map = [
                'Laboran (Fasilitas & Lab)' => [
                    'Fasilitas Ruangan / AC / Proyektor',
                    'Perangkat Komputer / Hardware',
                    'Koneksi Jaringan / Internet Lab',
                    'Software / Lisensi Praktikum',
                    'Lain-lain (Laboran)'
                ],
                'Layanan Akademik (LAA)' => [
                    'Surat Keterangan / Pengantar',
                    'Administrasi Nilai & Transkrip',
                    'Jadwal Kuliah / Ujian',
                    'Lain-lain (LAA)'
                ],
                'Koordinator TA' => [
                    'Bimbingan & Penguji Tugas Akhir',
                    'Jadwal Preview / Sidang TA',
                    'Rubrik Penilaian TA',
                    'Lain-lain (Koordinator TA)'
                ],
                'Dosen Wali' => [
                    'Konsultasi Akademik / Perwalian',
                    'Persetujuan / Tanda Tangan Dokumen',
                    'Kendala Perkuliahan & Nilai',
                    'Bimbingan Akademik',
                    'Lain-lain (Dosen Wali)'
                ]
            ];
        }

        // Load active dynamic custom fields for ticketing (jika tabel ada)
        $custom_fields = [];
        if ($this->db->table_exists('laboran_ticketing_fields')) {
            $custom_fields = $this->db
                ->where('is_active', 1)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('laboran_ticketing_fields')
                ->result_array();
        }

        $data = [
            'title'             => 'Input Tiket Kendala Dosen',
            'active_menu'       => 'ticketing_input',
            'user'              => [
                'id'    => $userId,
                'nidn'  => $nidn,
                'nama'  => $nama,
                'email' => $email
            ],
            'custom_fields'     => $custom_fields,
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
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Harap lengkapi semua kolom wajib (Nama Lengkap, Unit yang Dituju, Kategori, Subjek, dan Deskripsi).']);
                return;
            }
            $this->session->set_flashdata('error', 'Harap lengkapi semua kolom wajib (Nama Lengkap, Unit yang Dituju, Kategori, Subjek, dan Deskripsi).');
            redirect('dosen/ticketing/input');
            return;
        }

        // Cek jika kategori berupa 'Lainnya' / 'Lain-lain'
        if (preg_match('/lain/i', $kategori)) {
            if (empty($kategoriLainnya)) {
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'Harap sebutkan rincian kendala pada kolom Kategori Lainnya.']);
                    return;
                }
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
        $recentTickets = $this->DosenTicketing_model->get_tickets($userId, $nidn);
        $recentTicket = null;
        if (!empty($recentTickets)) {
            $latest = $recentTickets[0];
            if ($latest->subjek === $subjek && (time() - strtotime($latest->created_at)) <= 5) {
                $recentTicket = $latest;
            }
        }

        if ($recentTicket) {
            $msg = "Tiket kendala berhasil dikirim dengan kode: <strong>{$recentTicket->kode_tiket}</strong> ke unit <strong>" . htmlspecialchars($unitTujuan) . "</strong>.";
            $this->session->set_flashdata('success', $msg);
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status'       => 'success',
                    'kode_tiket'   => $recentTicket->kode_tiket,
                    'message'      => $msg,
                    'redirect_url' => site_url('dosen/ticketing/riwayat')
                ]);
                return;
            }
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

        // Process Dynamic Custom Fields if any extra fields exist
        $coreFieldNames = ['nama_lengkap', 'unit_tujuan', 'kategori', 'prioritas', 'subjek', 'deskripsi', 'lampiran'];
        $activeCustomFields = [];
        if ($this->db->table_exists('laboran_ticketing_fields')) {
            $activeCustomFields = $this->db
                ->where('is_active', 1)
                ->where_not_in('field_name', $coreFieldNames)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('laboran_ticketing_fields')
                ->result_array();
        }

        $rawCustomFieldsPost = $this->input->post('custom_fields') ?: [];
        $submittedCustomData = [];

        foreach ($activeCustomFields as $f) {
            $fName = $f['field_name'];
            $fVal = isset($rawCustomFieldsPost[$fName]) ? trim($rawCustomFieldsPost[$fName]) : '';
            if ($f['is_required'] && empty($fVal)) {
                $this->session->set_flashdata('error', 'Field "' . htmlspecialchars($f['field_label']) . '" wajib diisi.');
                redirect('dosen/ticketing/input');
                return;
            }
            if ($fVal !== '') {
                $submittedCustomData[] = [
                    'label' => $f['field_label'],
                    'name'  => $f['field_name'],
                    'type'  => $f['field_type'],
                    'value' => $fVal
                ];
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
            'custom_fields_data' => !empty($submittedCustomData) ? json_encode($submittedCustomData, JSON_UNESCAPED_UNICODE) : null,
            'lampiran'    => $lampiranFile,
            'status'      => 'Menunggu'
        ];

        $insertId = $this->DosenTicketing_model->insert($ticketData);

        if ($insertId) {
            $msg = "Tiket kendala berhasil dikirim dengan kode: <strong>{$kodeTiket}</strong> ke unit <strong>" . htmlspecialchars($unitTujuan) . "</strong>. Tim terkait akan segera meninjau laporan Anda.";
            $this->session->set_flashdata('success', $msg);
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status'       => 'success',
                    'kode_tiket'   => $kodeTiket,
                    'message'      => $msg,
                    'redirect_url' => site_url('dosen/ticketing/riwayat')
                ]);
                return;
            }
            redirect('dosen/ticketing/riwayat');
        } else {
            $err = 'Terjadi kesalahan sistem saat menyimpan tiket. Silakan coba beberapa saat lagi.';
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $err]);
                return;
            }
            $this->session->set_flashdata('error', $err);
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
     * Halaman Inbox Respon Ticketing khusus Role Dosen
     */
    public function respon_index() {
        $filterStatus = $this->input->get('status', true) ?: 'all';
        $search = trim($this->input->get('q', true) ?? '');

        // 1. Query Tiket Masuk Khusus Dosen dari Model
        $tickets = $this->DosenTicketing_model->get_respon_tickets($filterStatus, $search);

        // 2. Hitung Statistik Khusus Unit Dosen
        $stats = [
            'total'    => $this->DosenTicketing_model->count_respon_tickets('all'),
            'menunggu' => $this->DosenTicketing_model->count_respon_tickets('Menunggu'),
            'diproses' => $this->DosenTicketing_model->count_respon_tickets('Diproses'),
            'selesai'  => $this->DosenTicketing_model->count_respon_tickets('Selesai'),
            'ditutup'  => $this->DosenTicketing_model->count_respon_tickets('Ditutup')
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
     * AJAX Endpoint: Detail Tiket untuk Modal Respon Dosen
     */
    public function respon_detail($id_or_kode) {
        $ticket = $this->DosenTicketing_model->get_by_id($id_or_kode);

        if (!$ticket) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Tiket kendala tidak ditemukan.'
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
        $idTiket   = trim($this->input->post('id_tiket') ?: $this->input->post('ticket_id'));
        $status    = trim($this->input->post('status', true));
        $tanggapan = trim($this->input->post('tanggapan') ?? '');

        if (empty($idTiket)) {
            $this->session->set_flashdata('error', 'ID Tiket tidak valid.');
            redirect('dosen/respon-ticketing');
            return;
        }

        if (!in_array($status, ['Menunggu', 'Diproses', 'Selesai', 'Ditutup'])) {
            $status = 'Diproses';
        }

        // Backend Guard: Status Stepper Satu Arah (Non-reversible)
        $statusWeight = [
            'Menunggu' => 1,
            'Diproses' => 2,
            'Selesai'  => 3,
            'Ditutup'  => 4
        ];

        $currentTicket = $this->DosenTicketing_model->get_by_id($idTiket);
        if ($currentTicket) {
            $currentStatus = $currentTicket->status ?? 'Menunggu';
            $curW = $statusWeight[$currentStatus] ?? 1;
            $newW = $statusWeight[$status] ?? 1;

            if ($newW < $curW) {
                $this->session->set_flashdata('error', "Status tiket tidak dapat dimundurkan kembali dari {$currentStatus} ke {$status}.");
                redirect('dosen/respon-ticketing');
                return;
            }
        }

        $this->DosenTicketing_model->update_respon($idTiket, $status, $tanggapan);

        $msg = ($tanggapan !== '')
            ? "Tanggapan berhasil disimpan! Status tiket kini diperbarui menjadi <strong>{$status}</strong>."
            : "Status tiket berhasil diperbarui menjadi <strong>{$status}</strong>.";

        $this->session->set_flashdata('success', $msg);
        redirect('dosen/respon-ticketing');
    }
}

