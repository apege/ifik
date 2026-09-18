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
                'custom_fields'  => !empty($ticket->custom_fields_data) ? json_decode($ticket->custom_fields_data, true) : [],
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
        $tanggapan = trim($this->input->post('tanggapan', true) ?? '');

        // Validasi input
        if (empty($id) || empty($status)) {
            $this->session->set_flashdata('error', 'Harap tentukan status tiket.');
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

        // Backend Guard: Status Stepper Satu Arah (Non-reversible)
        $statusWeight = [
            'Menunggu' => 1,
            'Diproses' => 2,
            'Selesai'  => 3,
            'Ditutup'  => 4
        ];
        $currentStatus = $ticket->status ?? 'Menunggu';
        $curW = $statusWeight[$currentStatus] ?? 1;
        $newW = $statusWeight[$status] ?? 1;

        if ($newW < $curW) {
            $this->session->set_flashdata('error', "Status tiket tidak dapat dimundurkan kembali dari {$currentStatus} ke {$status}.");
            redirect('laboran/respon-ticketing');
            return;
        }

        $updateData = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($tanggapan !== '') {
            $updateData['tanggapan'] = $tanggapan;
            $updateData['tgl_tanggapan'] = date('Y-m-d H:i:s');
        } elseif ($status === 'Menunggu') {
            $updateData['tanggapan'] = null;
            $updateData['tgl_tanggapan'] = null;
        }

        $this->db->where('id', (int)$id);
        $this->db->update($this->table, $updateData);

        $msg = ($tanggapan !== '')
            ? "Tiket {$ticket->kode_tiket} berhasil direspon dengan status '{$status}'."
            : "Status tiket {$ticket->kode_tiket} berhasil diperbarui menjadi '{$status}'.";

        $this->session->set_flashdata('success', $msg);
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

        $unit_kategori_map = $this->get_dynamic_unit_kategori_map();

        // Load active dynamic custom fields for ticketing
        $custom_fields = $this->db
            ->where('is_active', 1)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get('laboran_ticketing_fields')
            ->result_array();

        $data = [
            'title'             => 'Buat Tiket Kendala Baru — Panel Laboran',
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
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Semua field bertanda bintang wajib diisi.']);
                return;
            }
            $this->session->set_flashdata('error', 'Semua field bertanda bintang wajib diisi.');
            redirect('laboran/ticketing/input');
            return;
        }

        // Process Dynamic Custom Fields
        $activeCustomFields = $this->db
            ->where('is_active', 1)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get('laboran_ticketing_fields')
            ->result_array();

        $rawCustomFieldsPost = $this->input->post('custom_fields') ?: [];
        $submittedCustomData = [];

        foreach ($activeCustomFields as $f) {
            $fName = $f['field_name'];
            $fVal = isset($rawCustomFieldsPost[$fName]) ? trim($rawCustomFieldsPost[$fName]) : '';
            if ($f['is_required'] && empty($fVal)) {
                $err = 'Field "' . htmlspecialchars($f['field_label']) . '" wajib diisi.';
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => $err]);
                    return;
                }
                $this->session->set_flashdata('error', $err);
                redirect('laboran/ticketing/input');
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
                $msg = "Tiket kendala berhasil diajukan dengan Kode: <b>{$recentTicket->kode_tiket}</b> ke unit <b>" . htmlspecialchars($unit_tujuan) . "</b>.";
                $this->session->set_flashdata('success', $msg);
                if ($this->input->is_ajax_request()) {
                    echo json_encode([
                        'status'       => 'success',
                        'kode_tiket'   => $recentTicket->kode_tiket,
                        'message'      => $msg,
                        'redirect_url' => site_url('laboran/ticketing/riwayat')
                    ]);
                    return;
                }
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
            'id_user'            => $userId,
            'nama_dosen'         => $namaLengkap,
            'nidn'               => $nidn,
            'unit_tujuan'        => $unit_tujuan,
            'kategori'           => $kategori,
            'prioritas'          => in_array($prioritas, ['Rendah', 'Sedang', 'Tinggi', 'Darurat']) ? $prioritas : 'Sedang',
            'subjek'             => $subjek,
            'deskripsi'          => $deskripsi,
            'custom_fields_data' => !empty($submittedCustomData) ? json_encode($submittedCustomData, JSON_UNESCAPED_UNICODE) : null,
            'lampiran'           => $lampiran_name,
            'status'             => 'Menunggu'
        ];

        $insertedId = $this->DosenTicketing_model->insert($ticketData);
        $createdTicket = $this->DosenTicketing_model->get_by_id($insertedId);
        $kodeTiket = $createdTicket ? $createdTicket->kode_tiket : '';

        $msg = "Tiket Anda berhasil diajukan dengan Kode: <b>{$kodeTiket}</b> ke unit <b>" . htmlspecialchars($unit_tujuan) . "</b>.";
        $this->session->set_flashdata('success', $msg);
        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'status'       => 'success',
                'kode_tiket'   => $kodeTiket,
                'message'      => $msg,
                'redirect_url' => site_url('laboran/ticketing/riwayat')
            ]);
            return;
        }
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

        // Access check: Superadmin (1), Kaur (2), Laboran (21), or Owner
        if ($roleId !== 1 && $roleId !== 2 && $roleId !== 21 && $ticket->id_user != $userId && $ticket->nidn != $nidn) {
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
                    'custom_fields' => !empty($ticket->custom_fields_data) ? json_decode($ticket->custom_fields_data, true) : [],
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
     * Helper: Validasi hak akses khusus Laboran
     */
    private function _ensure_laboran() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            exit;
        }

        $roleId = (int)$this->session->userdata('role_id');
        // Hanya Admin (1), Kaur (2), atau Laboran (21) yang diizinkan
        if ($roleId !== 1 && $roleId !== 2 && $roleId !== 21) {
            $this->session->set_flashdata('error', 'Akses ditolak: Menu ini hanya untuk staf Laboran.');
            redirect('dashboard');
            exit;
        }
    }

    /**
     * Fallback konfigurasi standar sistem Unit & Kategori Kendala
     */
    private function _get_fallback_unit_kategori_map() {
        return [
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

    /**
     * Pastikan setiap unit tujuan selalu memiliki kategori 'Lain-lain' (Wajib Ada)
     */
    private function _ensure_default_lain_lain_categories() {
        if (!$this->db->table_exists('ticketing_units') || !$this->db->table_exists('ticketing_kategori')) {
            return;
        }
        $units = $this->db->get('ticketing_units')->result_array();
        $now = date('Y-m-d H:i:s');
        foreach ($units as $u) {
            $hasLain = $this->db
                ->where('unit_id', $u['id'])
                ->group_start()
                    ->like('nama_kategori', 'Lain-lain', 'after')
                    ->or_like('nama_kategori', 'Lainnya', 'after')
                ->group_end()
                ->count_all_results('ticketing_kategori');

            if ($hasLain == 0) {
                $this->db->insert('ticketing_kategori', [
                    'unit_id'       => $u['id'],
                    'nama_kategori' => 'Lain-lain (' . $u['nama_unit'] . ')',
                    'deskripsi'     => 'Kategori bawaan untuk kendala di luar daftar spesifik',
                    'sort_order'    => 999,
                    'is_active'     => 1,
                    'created_at'    => $now,
                    'updated_at'    => $now
                ]);
            }
        }
    }

    /**
     * Mengambil peta relasi Unit Tujuan dan Kategori Kendala secara dinamis dari database
     */
    public function get_dynamic_unit_kategori_map() {
        if (!$this->db->table_exists('ticketing_units')) {
            return $this->_get_fallback_unit_kategori_map();
        }

        $this->_ensure_default_lain_lain_categories();

        $units = $this->db
            ->where('is_active', 1)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get('ticketing_units')
            ->result_array();

        $map = [];
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
            $map[$u['nama_unit']] = $catList;
        }
        return !empty($map) ? $map : $this->_get_fallback_unit_kategori_map();
    }

    /**
     * Halaman Pengaturan Dropdown & Input Tiket Dinamis (Khusus Role Laboran)
     */
    public function custom_fields() {
        $this->_ensure_laboran();

        if (!$this->db->table_exists('ticketing_units')) {
            $defaultMap = $this->_get_fallback_unit_kategori_map();
            $units = [];
            $kategori = [];
            $uId = 1;
            $kId = 1;
            foreach ($defaultMap as $uName => $kList) {
                $unitKat = [];
                foreach ($kList as $kName) {
                    $kItem = [
                        'id'            => $kId++,
                        'unit_id'       => $uId,
                        'nama_unit'     => $uName,
                        'nama_kategori' => $kName,
                        'deskripsi'     => 'Kategori standar sistem',
                        'sort_order'    => $kId,
                        'is_active'     => 1
                    ];
                    $unitKat[] = $kItem;
                    $kategori[] = $kItem;
                }
                $units[] = [
                    'id'             => $uId,
                    'nama_unit'      => $uName,
                    'deskripsi'      => 'Unit layanan standar sistem',
                    'sort_order'     => $uId,
                    'is_active'      => 1,
                    'total_kategori' => count($kList),
                    'kategori'       => $unitKat
                ];
                $uId++;
            }

            $stats = [
                'total_units'     => count($units),
                'active_units'    => count($units),
                'total_kategori'  => count($kategori),
                'active_kategori' => count($kategori)
            ];

            $data = [
                'title'            => 'Pengaturan Dropdown & Input Tiket Dinamis — Panel Laboran',
                'active_menu'      => 'ticketing_fields',
                'units'            => $units,
                'kategori'         => $kategori,
                'selected_unit_id' => 0,
                'stats'            => $stats
            ];

            $this->load->view('laboran/ticketing_fields', $data);
            return;
        }

        $this->_ensure_default_lain_lain_categories();

        // 1. Data Unit Tujuan beserta jumlah kategori di dalamnya
        $units = $this->db
            ->select('u.*, COUNT(k.id) as total_kategori')
            ->from('ticketing_units u')
            ->join('ticketing_kategori k', 'u.id = k.unit_id', 'left')
            ->group_by('u.id')
            ->order_by('u.sort_order', 'ASC')
            ->order_by('u.id', 'ASC')
            ->get()
            ->result_array();

        foreach ($units as &$u) {
            $u['kategori'] = $this->db
                ->where('unit_id', $u['id'])
                ->order_by("(CASE WHEN nama_kategori LIKE 'Lain-lain%' OR nama_kategori LIKE 'Lainnya%' THEN 1 ELSE 0 END)", 'ASC', FALSE)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('ticketing_kategori')
                ->result_array();
        }
        unset($u);

        // 2. Data Kategori Kendala beserta nama unit induknya
        $selectedUnitId = (int)$this->input->get('unit_id');
        $kategoriQuery = $this->db
            ->select('k.*, u.nama_unit')
            ->from('ticketing_kategori k')
            ->join('ticketing_units u', 'u.id = k.unit_id', 'inner');

        if ($selectedUnitId > 0) {
            $kategoriQuery->where('k.unit_id', $selectedUnitId);
        }

        $kategori = $kategoriQuery
            ->order_by('u.sort_order', 'ASC')
            ->order_by("(CASE WHEN k.nama_kategori LIKE 'Lain-lain%' OR k.nama_kategori LIKE 'Lainnya%' THEN 1 ELSE 0 END)", 'ASC', FALSE)
            ->order_by('k.sort_order', 'ASC')
            ->order_by('k.id', 'ASC')
            ->get()
            ->result_array();

        // 3. Stats Ringkasan
        $stats = [
            'total_units'     => count($units),
            'active_units'    => count(array_filter($units, function($u) { return $u['is_active'] == 1; })),
            'total_kategori'  => $this->db->count_all('ticketing_kategori'),
            'active_kategori' => $this->db->where('is_active', 1)->count_all_results('ticketing_kategori')
        ];

        $data = [
            'title'            => 'Pengaturan Dropdown & Input Tiket Dinamis — Panel Laboran',
            'active_menu'      => 'ticketing_fields',
            'units'            => $units,
            'kategori'         => $kategori,
            'selected_unit_id' => $selectedUnitId,
            'stats'            => $stats
        ];

        $this->load->view('laboran/ticketing_fields', $data);
    }

    /**
     * Simpan / Perbarui Unit Tujuan (Dropdown No. 2)
     */
    public function unit_save() {
        $this->_ensure_laboran();

        if (!$this->db->table_exists('ticketing_units')) {
            $this->session->set_flashdata('error', 'Konfigurasi unit saat ini menggunakan standar sistem bawaan kode. Tabel database ticketing_units dinonaktifkan.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $id         = (int)$this->input->post('id');
        $nama_unit  = trim($this->input->post('nama_unit', true));
        $deskripsi  = trim($this->input->post('deskripsi', true));
        $sort_order = (int)$this->input->post('sort_order');
        $is_active  = $this->input->post('is_active') ? 1 : 0;

        if (empty($nama_unit)) {
            $this->session->set_flashdata('error', 'Nama Unit Tujuan wajib diisi.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            $this->db->where('id', $id)->update('ticketing_units', [
                'nama_unit'  => $nama_unit,
                'deskripsi'  => $deskripsi,
                'sort_order' => $sort_order,
                'is_active'  => $is_active,
                'updated_at' => $now
            ]);
            $this->session->set_flashdata('success', "Unit Tujuan <b>\"{$nama_unit}\"</b> berhasil diperbarui!");
        } else {
            // Cek duplikasi nama
            $exists = $this->db->get_where('ticketing_units', ['nama_unit' => $nama_unit])->row();
            if ($exists) {
                $this->session->set_flashdata('error', "Unit Tujuan \"{$nama_unit}\" sudah ada.");
                redirect('laboran/ticketing/fields');
                return;
            }

            $order = ($sort_order > 0) ? $sort_order : ($this->db->count_all('ticketing_units') + 1);
            $this->db->insert('ticketing_units', [
                'nama_unit'  => $nama_unit,
                'deskripsi'  => $deskripsi,
                'sort_order' => $order,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]);
            $unit_id = $this->db->insert_id();

            // Selalu tambahkan kategori wajib 'Lain-lain' untuk unit baru ini
            $this->db->insert('ticketing_kategori', [
                'unit_id'       => $unit_id,
                'nama_kategori' => 'Lain-lain (' . $nama_unit . ')',
                'deskripsi'     => 'Kategori bawaan wajib untuk kendala umum / di luar opsi spesifik',
                'sort_order'    => 999,
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now
            ]);

            // Simpan opsi kategori tambahan jika diisi
            $kategori_items = $this->input->post('kategori_items');
            $countKat = 0;
            if (!empty($kategori_items) && is_array($kategori_items)) {
                $katOrder = 1;
                foreach ($kategori_items as $item) {
                    $itemClean = trim($item);
                    if (!empty($itemClean)) {
                        $this->db->insert('ticketing_kategori', [
                            'unit_id'       => $unit_id,
                            'nama_kategori' => $itemClean,
                            'deskripsi'     => '',
                            'sort_order'    => $katOrder++,
                            'is_active'     => 1,
                            'created_at'    => $now,
                            'updated_at'    => $now
                        ]);
                        $countKat++;
                    }
                }
            }

            if ($countKat > 0) {
                $this->session->set_flashdata('success', "Unit Tujuan <b>\"{$nama_unit}\"</b> dan <b>{$countKat} pilihan kategori</b> berhasil ditambahkan ke dropdown!");
            } else {
                $this->session->set_flashdata('success', "Unit Tujuan baru <b>\"{$nama_unit}\"</b> (termasuk kategori bawaan) berhasil ditambahkan ke pilihan dropdown!");
            }
        }

        redirect('laboran/ticketing/fields');
    }

    /**
     * Toggle Aktif / Nonaktif Unit Tujuan
     */
    public function unit_toggle($id) {
        $this->_ensure_laboran();

        if (!$this->db->table_exists('ticketing_units')) {
            $this->session->set_flashdata('error', 'Konfigurasi unit saat ini menggunakan standar sistem.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $unit = $this->db->get_where('ticketing_units', ['id' => (int)$id])->row();
        if (!$unit) {
            $this->session->set_flashdata('error', 'Unit tidak ditemukan.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $newStatus = ($unit->is_active == 1) ? 0 : 1;
        $this->db->where('id', $unit->id)->update('ticketing_units', [
            'is_active'  => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $stText = ($newStatus == 1) ? 'diaktifkan' : 'dinonaktifkan';
        $this->session->set_flashdata('success', "Unit <b>\"{$unit->nama_unit}\"</b> berhasil {$stText}!");
        redirect('laboran/ticketing/fields');
    }

    /**
     * Hapus Unit Tujuan
     */
    public function unit_delete($id) {
        $this->_ensure_laboran();

        if (!$this->db->table_exists('ticketing_units')) {
            $this->session->set_flashdata('error', 'Konfigurasi unit saat ini menggunakan standar sistem.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $unit = $this->db->get_where('ticketing_units', ['id' => (int)$id])->row();
        if ($unit) {
            $this->db->where('id', $unit->id)->delete('ticketing_units');
            $this->session->set_flashdata('success', "Unit <b>\"{$unit->nama_unit}\"</b> dan seluruh kategori di dalamnya berhasil dihapus!");
        } else {
            $this->session->set_flashdata('error', 'Unit tidak ditemukan.');
        }

        redirect('laboran/ticketing/fields');
    }

    /**
     * Simpan / Perbarui Kategori Kendala (Dropdown No. 3)
     * Mendukung penambahan dinamis (bisa single edit atau multi-row input tanpa koma)
     */
    public function kategori_save() {
        $this->_ensure_laboran();

        if (!$this->db->table_exists('ticketing_kategori')) {
            $this->session->set_flashdata('error', 'Konfigurasi kategori saat ini menggunakan standar sistem bawaan kode.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $id             = (int)$this->input->post('id');
        $unit_id        = (int)$this->input->post('unit_id');
        $nama_kategori  = trim($this->input->post('nama_kategori', true));
        $deskripsi      = trim($this->input->post('deskripsi', true));
        $sort_order     = (int)$this->input->post('sort_order');
        $is_active      = $this->input->post('is_active') ? 1 : 0;
        $kategori_items = $this->input->post('kategori_items');

        if ($unit_id <= 0) {
            $this->session->set_flashdata('error', 'Pilih Unit Induk Tujuan.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            // Edit satu kategori
            if (empty($nama_kategori)) {
                $this->session->set_flashdata('error', 'Nama Kategori Kendala wajib diisi.');
                redirect('laboran/ticketing/fields');
                return;
            }

            $this->db->where('id', $id)->update('ticketing_kategori', [
                'unit_id'       => $unit_id,
                'nama_kategori' => $nama_kategori,
                'deskripsi'     => $deskripsi,
                'sort_order'    => $sort_order,
                'is_active'     => $is_active,
                'updated_at'    => $now
            ]);
            $this->session->set_flashdata('success', "Kategori Kendala <b>\"{$nama_kategori}\"</b> berhasil diperbarui!");
        } else {
            // Mode Tambah: Kumpulkan semua item kategori dari baris dinamis (tanpa perlu pakai koma)
            $itemsToAdd = [];
            if (!empty($kategori_items) && is_array($kategori_items)) {
                foreach ($kategori_items as $item) {
                    $c = trim($item);
                    if (!empty($c) && !in_array($c, $itemsToAdd)) {
                        $itemsToAdd[] = $c;
                    }
                }
            }
            if (!empty($nama_kategori) && !in_array($nama_kategori, $itemsToAdd)) {
                $itemsToAdd[] = $nama_kategori;
            }

            if (empty($itemsToAdd)) {
                $this->session->set_flashdata('error', 'Masukkan setidaknya satu nama Kategori Kendala.');
                redirect('laboran/ticketing/fields');
                return;
            }

            $currentMax = $this->db->where('unit_id', $unit_id)->count_all_results('ticketing_kategori');
            $inserted = 0;
            foreach ($itemsToAdd as $idx => $itemText) {
                $order = ($sort_order > 0 && count($itemsToAdd) === 1) ? $sort_order : ($currentMax + $idx + 1);
                $this->db->insert('ticketing_kategori', [
                    'unit_id'       => $unit_id,
                    'nama_kategori' => $itemText,
                    'deskripsi'     => $deskripsi,
                    'sort_order'    => $order,
                    'is_active'     => 1,
                    'created_at'    => $now,
                    'updated_at'    => $now
                ]);
                $inserted++;
            }

            if ($inserted > 1) {
                $this->session->set_flashdata('success', "Berhasil menambahkan <b>{$inserted} pilihan kategori baru</b> ke Unit terkait!");
            } else {
                $this->session->set_flashdata('success', "Pilihan kategori <b>\"{$itemsToAdd[0]}\"</b> berhasil ditambahkan!");
            }
        }

        redirect('laboran/ticketing/fields');
    }

    /**
     * Toggle Aktif / Nonaktif Kategori Kendala
     */
    public function kategori_toggle($id) {
        $this->_ensure_laboran();

        if (!$this->db->table_exists('ticketing_kategori')) {
            $this->session->set_flashdata('error', 'Konfigurasi kategori saat ini menggunakan standar sistem bawaan kode.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $kat = $this->db->get_where('ticketing_kategori', ['id' => (int)$id])->row();
        if (!$kat) {
            $this->session->set_flashdata('error', 'Kategori tidak ditemukan.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $newStatus = ($kat->is_active == 1) ? 0 : 1;
        $this->db->where('id', $kat->id)->update('ticketing_kategori', [
            'is_active'  => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $stText = ($newStatus == 1) ? 'diaktifkan' : 'dinonaktifkan';
        $this->session->set_flashdata('success', "Kategori <b>\"{$kat->nama_kategori}\"</b> berhasil {$stText}!");
        redirect('laboran/ticketing/fields');
    }

    /**
     * Hapus Kategori Kendala
     */
    public function kategori_delete($id) {
        $this->_ensure_laboran();

        if (!$this->db->table_exists('ticketing_kategori')) {
            $this->session->set_flashdata('error', 'Konfigurasi kategori saat ini menggunakan standar sistem bawaan kode.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $kat = $this->db->get_where('ticketing_kategori', ['id' => (int)$id])->row();
        if ($kat) {
            $unitId = $kat->unit_id;

            // Proteksi: Kategori 'Lain-lain' wajib ada dan tidak boleh dihapus
            if (stripos($kat->nama_kategori, 'Lain-lain') === 0 || stripos($kat->nama_kategori, 'Lainnya') === 0) {
                $this->session->set_flashdata('error', "Kategori <b>\"{$kat->nama_kategori}\"</b> adalah kategori wajib sistem dan tidak dapat dihapus! Anda dapat menggunakan fitur <b>Nonaktif</b> jika tidak ingin menampilkannya di formulir pelapor.");
                redirect('laboran/ticketing/fields');
                return;
            }

            $this->db->where('id', $kat->id)->delete('ticketing_kategori');
            $this->session->set_flashdata('success', "Kategori <b>\"{$kat->nama_kategori}\"</b> berhasil dihapus dari daftar pilihan!");
            redirect('laboran/ticketing/fields');
        } else {
            $this->session->set_flashdata('error', 'Kategori tidak ditemukan.');
            redirect('laboran/ticketing/fields');
        }
    }

    /**
     * Simpan / Perbarui Konfigurasi Input Dinamis Tiket
     */
    public function custom_fields_save() {
        $this->_ensure_laboran();

        $id           = (int)$this->input->post('id');
        $field_label  = trim($this->input->post('field_label', true));
        $field_type   = trim($this->input->post('field_type', true)) ?: 'text';
        $field_options= trim($this->input->post('field_options', true));
        $placeholder  = trim($this->input->post('placeholder', true));
        $is_required  = $this->input->post('is_required') ? 1 : 0;
        $is_active    = $this->input->post('is_active') ? 1 : 0;
        $sort_order   = (int)$this->input->post('sort_order');

        if (empty($field_label)) {
            $this->session->set_flashdata('error', 'Nama Label Field wajib diisi.');
            redirect('laboran/ticketing/fields');
            return;
        }

        // Generate clean field name
        $this->load->helper('url');
        $cleanName = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower(url_title($field_label, '_')));
        $cleanName = trim($cleanName, '_');
        if (empty($cleanName)) {
            $cleanName = 'field_' . time();
        }

        $allowedTypes = ['text', 'number', 'select', 'textarea', 'date'];
        if (!in_array($field_type, $allowedTypes)) {
            $field_type = 'text';
        }

        $now = date('Y-m-d H:i:s');

        if ($id > 0) {
            // Update existing field
            $updateData = [
                'field_label'   => $field_label,
                'field_type'    => $field_type,
                'field_options' => ($field_type === 'select') ? $field_options : null,
                'placeholder'   => $placeholder,
                'is_required'   => $is_required,
                'is_active'     => $is_active,
                'sort_order'    => $sort_order,
                'updated_at'    => $now
            ];
            $this->db->where('id', $id)->update('laboran_ticketing_fields', $updateData);
            $this->session->set_flashdata('success', "Konfigurasi field <b>\"{$field_label}\"</b> berhasil diperbarui!");
        } else {
            // Check unique field name
            $existing = $this->db->get_where('laboran_ticketing_fields', ['field_name' => $cleanName])->row();
            if ($existing) {
                $cleanName .= '_' . rand(10, 99);
            }

            $insertData = [
                'field_name'    => $cleanName,
                'field_label'   => $field_label,
                'field_type'    => $field_type,
                'field_options' => ($field_type === 'select') ? $field_options : null,
                'placeholder'   => $placeholder,
                'is_required'   => $is_required,
                'is_active'     => 1,
                'sort_order'    => $sort_order > 0 ? $sort_order : ($this->db->count_all('laboran_ticketing_fields') + 1),
                'created_at'    => $now,
                'updated_at'    => $now
            ];
            $this->db->insert('laboran_ticketing_fields', $insertData);
            $this->session->set_flashdata('success', "Field inputan baru <b>\"{$field_label}\"</b> berhasil ditambahkan ke formulir tiket!");
        }

        redirect('laboran/ticketing/fields');
    }

    /**
     * Toggle Aktif / Nonaktif Field Dinamis
     */
    public function custom_fields_toggle($id) {
        $this->_ensure_laboran();

        $field = $this->db->get_where('laboran_ticketing_fields', ['id' => (int)$id])->row();
        if (!$field) {
            $this->session->set_flashdata('error', 'Field tidak ditemukan.');
            redirect('laboran/ticketing/fields');
            return;
        }

        $newStatus = ($field->is_active == 1) ? 0 : 1;
        $this->db->where('id', $field->id)->update('laboran_ticketing_fields', [
            'is_active'  => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $statusText = ($newStatus == 1) ? 'diaktifkan' : 'dinonaktifkan';
        $this->session->set_flashdata('success', "Field <b>\"{$field->field_label}\"</b> berhasil {$statusText}!");
        redirect('laboran/ticketing/fields');
    }

    /**
     * Hapus Field Dinamis
     */
    public function custom_fields_delete($id) {
        $this->_ensure_laboran();

        $field = $this->db->get_where('laboran_ticketing_fields', ['id' => (int)$id])->row();
        if ($field) {
            $this->db->where('id', $field->id)->delete('laboran_ticketing_fields');
            $this->session->set_flashdata('success', "Field <b>\"{$field->field_label}\"</b> berhasil dihapus dari formulir!");
        } else {
            $this->session->set_flashdata('error', 'Field tidak ditemukan.');
        }

        redirect('laboran/ticketing/fields');
    }
}
