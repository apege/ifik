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
        $this->load->helper(['url', 'form', 'fik_prodi']);
    }

    /**
     * Default index: Redirect to riwayat
     */
    public function index() {
        redirect('dosen/ticketing/riwayat');
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
                'Laboratorium Bengkel dan Studio' => [
                    'Fasilitas Ruangan / AC / Proyektor Lab',
                    'Perangkat Komputer / Hardware / Monitor',
                    'Drawing Tablet / Display Cintiq / Alat Digital',
                    'Software / Lisensi Aplikasi Praktikum & Studio',
                    'Jaringan & Koneksi Internet Lab',
                    'Peralatan & Bahan Praktikum Bengkel / Studio',
                    'Peminjaman & Pemeliharaan Alat Lab',
                    'Lain-lain (Laboratorium Bengkel dan Studio)'
                ],
                'Layanan Akademik - LAA' => [
                    'Surat Keterangan Mahasiswa / Pengantar Kuliah',
                    'Administrasi Nilai, KSM & Transkrip Akademik',
                    'Registrasi Mata Kuliah / Perubahan KRS',
                    'Pengajuan Cuti Akademik / Aktif Kembali',
                    'Jadwal Kuliah & Ruang Ujian (UTS/UAS)',
                    'Legalisir Ijazah, Transkrip & KTM',
                    'Lain-lain (Layanan Akademik - LAA)'
                ],
                'Kemahasiswaan' => [
                    'Beasiswa & Bantuan Finansial Mahasiswa',
                    'Kegiatan Ormawa, UKM & Himpunan Mahasiswa',
                    'Lomba, Kompetisi & Pencatatan Prestasi Mahasiswa',
                    'Surat Rekomendasi & Izin Kegiatan Mahasiswa',
                    'Pembinaan Karakter, Konseling & Tata Tertib',
                    'Asuransi & Kesejahteraan Mahasiswa',
                    'Lain-lain (Kemahasiswaan)'
                ],
                'Sekretariat' => [
                    'Penerbitan Surat Keputusan (SK) Dekanat',
                    'Pengesahan & Tanda Tangan Dokumen Pimpinan',
                    'Administrasi Persuratan Masuk & Keluar Fakultas',
                    'Disposisi & Permohonan Informasi Resmi',
                    'Peminjaman Fasilitas Rapat / Ruang Dekanat',
                    'Lain-lain (Sekretariat)'
                ],
                'SDM dan Keuangan' => [
                    'Pembayaran BPP / Registrasi Keuangan Kuliah',
                    'Dispensasi & Penyesuaian Tagihan Biaya Kuliah',
                    'Konfirmasi & Verifikasi Bukti Pembayaran',
                    'Administrasi Penggajian, Honor & Klaim (SDM)',
                    'Berkas Kepegawaian & Verifikasi Dosen / Tendik',
                    'Anggaran Operasional & Pengadaan Sarana Unit',
                    'Lain-lain (SDM dan Keuangan)'
                ],
                'Program Studi (DKV, DI, DP, KTF, SR, Film dan Animasi)' => [
                    'Kurikulum, Silabus & Mata Kuliah Program Studi',
                    'Bimbingan Akademik / Dosen Pembimbing Prodi',
                    'Pendaftaran, Seminar Proposal & Sidang TA / Skripsi',
                    'Pengajuan / Perubahan Judul & Pembimbing TA',
                    'Konversi SKS Magang / MBKM / Kerja Praktik (KP)',
                    'Surat Rekomendasi / Keterangan Program Studi',
                    'Lain-lain (Program Studi)'
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
            'penerima_list'     => [
                'Laboran'    => [
                    'id'    => 'Laboran',
                    'title' => 'Laboran',
                    'desc'  => 'Fasilitas Lab, Hardware, Software, Jaringan & Sarpras',
                    'icon'  => 'bi-pc-display-horizontal'
                ],
                'Kaur' => [
                    'id'    => 'Kaur',
                    'title' => 'Kaur (Kepala Urusan)',
                    'desc'  => 'Kepala Urusan, Fasilitas Akademik, Perkuliahan & Pengesahan',
                    'icon'  => 'bi-person-badge'
                ],
                'Admin LAA'  => [
                    'id'    => 'Admin LAA',
                    'title' => 'Admin LAA',
                    'desc'  => 'Layanan Akademik, Surat Pengantar, Ijazah & KTM',
                    'icon'  => 'bi-building-check'
                ]
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
        $unitTujuan  = trim($this->input->post('unit_tujuan', true)) ?: (trim($this->input->post('tujuan_penerima', true)) ?: trim($this->input->post('unit_terkait', true)));
        if (empty($unitTujuan)) {
            $unitTujuan = 'Laboratorium Bengkel dan Studio';
        }
        $subProdi = trim($this->input->post('sub_prodi', true) ?? '');
        if (!empty($subProdi) && (strpos($unitTujuan, 'Program Studi') !== false || strpos($unitTujuan, 'Prodi') !== false)) {
            $unitTujuan = "Program Studi - {$subProdi}";
        }
        $tujuanPenerima  = $unitTujuan;
        $unitTerkait     = $unitTujuan;
        $kategori        = trim($this->input->post('kategori', true));
        $kategoriLainnya = trim($this->input->post('kategori_lainnya', true));
        $prioritas       = trim($this->input->post('prioritas', true));
        $subjek          = trim($this->input->post('subjek', true));
        $deskripsi       = $this->input->post('deskripsi'); // Rich text from TinyMCE

        // Validasi input
        $textOnly = trim(strip_tags($deskripsi));
        if (empty($namaLengkap) || empty($unitTujuan) || empty($kategori) || empty($subjek) || empty($textOnly)) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Harap lengkapi semua kolom wajib (Nama Lengkap, Unit yang Dituju, Kategori Kendala, Subjek, dan Deskripsi).']);
                return;
            }
            $this->session->set_flashdata('error', 'Harap lengkapi semua kolom wajib (Nama Lengkap, Unit yang Dituju, Kategori Kendala, Subjek, dan Deskripsi).');
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
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah lampiran: ' . $uploadError]);
                    return;
                }
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
            'kode_tiket'         => $kodeTiket,
            'id_user'            => $userId,
            'nama_dosen'         => $namaLengkap,
            'nama'               => $namaLengkap,
            'nidn'               => $nidn,
            'tujuan_penerima'    => $tujuanPenerima,
            'unit_terkait'       => $unitTerkait,
            'unit_tujuan'        => $unitTerkait,
            'kategori'           => $kategori,
            'prioritas'          => $prioritas,
            'subjek'             => $subjek,
            'deskripsi'          => $deskripsi,
            'custom_fields_data' => !empty($submittedCustomData) ? json_encode($submittedCustomData, JSON_UNESCAPED_UNICODE) : null,
            'lampiran'           => $lampiranFile,
            'status'             => 'Dikirim'
        ];

        $insertId = $this->DosenTicketing_model->insert($ticketData);

        if ($insertId) {
            $msg = "Tiket kendala berhasil dikirim dengan kode: <strong>{$kodeTiket}</strong> ditujukan kepada <strong>" . htmlspecialchars($unitTujuan) . "</strong>. Tim terkait akan segera meninjau laporan Anda.";
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
        if ($roleId !== 1 && $ticket->id_user != $userId && ($ticket->nidn ?? '') != $nidn) {
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
                    'id'              => $ticket->id,
                    'kode_tiket'      => $ticket->kode_tiket,
                    'nama_dosen'      => $ticket->nama_dosen,
                    'nidn'            => $ticket->nidn ?? '-',
                    'label_identitas' => $ticket->label_identitas ?? 'NIDN/ID',
                    'tujuan_penerima' => ($ticket->tujuan_penerima === 'Dosen Kaur') ? 'Kaur' : ($ticket->tujuan_penerima ?? 'Kaur'),
                    'unit_terkait'    => $ticket->unit_terkait ?? ($ticket->unit_tujuan ?: 'Layanan IFIK'),
                    'unit_tujuan'     => $ticket->unit_terkait ?? ($ticket->unit_tujuan ?: 'Layanan IFIK'),
                    'kategori'        => $ticket->kategori,
                    'prioritas'       => $ticket->prioritas,
                    'subjek'          => $ticket->subjek,
                    'deskripsi'       => $deskripsiFormatted,
                    'lampiran'        => $ticket->lampiran,
                    'lampiran_url'    => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                    'status'          => $ticket->status,
                    'tanggapan'       => $ticket->tanggapan ? nl2br(htmlspecialchars($ticket->tanggapan)) : null,
                    'catatan_proses'  => $ticket->catatan_proses ?? null,
                    'catatan_selesai' => $ticket->catatan_selesai ?? null,
                    'catatan_tutup'   => $ticket->catatan_tutup ?? null,
                    'tgl_diproses'    => !empty($ticket->tgl_diproses) ? date('d M Y H:i', strtotime($ticket->tgl_diproses)) : null,
                    'tgl_closed'      => !empty($ticket->tgl_closed) ? date('d M Y H:i', strtotime($ticket->tgl_closed)) : null,
                    'tgl_tanggapan'   => $ticket->tgl_tanggapan ? date('d M Y H:i', strtotime($ticket->tgl_tanggapan)) : null,
                    'created_at'      => date('d M Y H:i', strtotime($ticket->created_at)),
                    'updated_at'      => date('d M Y H:i', strtotime($ticket->updated_at))
                ]
            ]));
    }

    /**
     * Halaman Inbox Respon Ticketing khusus Role Kaur (Kepala Urusan)
     */
    public function respon_index() {
        $roleId = (int)$this->session->userdata('role_id');
        $jabatan = strtolower((string)($this->session->userdata('jabatan') ?? ''));
        $is_kaur = in_array($roleId, [1, 2, 22]) || ($roleId === 3 && $jabatan === 'kaur');

        // Hanya role Kaur (2), Dosen Kaur, atau Admin yang dapat merespon tiket Kaur
        if (!$is_kaur) {
            $this->session->set_flashdata('error', 'Akses ditolak. Halaman Respon Tiket khusus untuk Kepala Urusan (Kaur).');
            redirect('dosen/ticketing/riwayat');
            return;
        }

        $filterStatus = $this->input->get('status', true) ?: 'all';
        $search = trim($this->input->get('q', true) ?? '');

        // 1. Query Tiket Masuk Khusus Kaur dari Model
        $tickets = $this->DosenTicketing_model->get_respon_tickets($filterStatus, $search, 'Kaur');

        // 2. Hitung Statistik Khusus Unit Kaur
        $stats = [
            'total'    => $this->DosenTicketing_model->count_respon_tickets('all', 'Kaur'),
            'menunggu' => $this->DosenTicketing_model->count_respon_tickets('Menunggu', 'Kaur'),
            'diproses' => $this->DosenTicketing_model->count_respon_tickets('Diproses', 'Kaur'),
            'selesai'  => $this->DosenTicketing_model->count_respon_tickets('Selesai', 'Kaur'),
            'ditutup'  => $this->DosenTicketing_model->count_respon_tickets('Ditutup', 'Kaur')
        ];

        $baseResponUrl = ($roleId === 2) ? 'kaur/respon-ticketing' : 'dosen/respon-ticketing';

        $data = [
            'title'         => 'Inbox Respon Tiket Kepala Urusan (Kaur) — IFIK Portal',
            'tickets'       => $tickets,
            'stats'         => $stats,
            'filterStatus'  => $filterStatus,
            'search'        => $search,
            'baseResponUrl' => $baseResponUrl
        ];

        $this->load->view('dosen/ticketing_respon', $data);
    }

    /**
     * AJAX Endpoint: Detail Tiket untuk Modal Respon Kaur
     */
    public function respon_detail($id_or_kode) {
        $roleId = (int)$this->session->userdata('role_id');
        $jabatan = strtolower((string)($this->session->userdata('jabatan') ?? ''));
        $is_kaur = in_array($roleId, [1, 2, 22]) || ($roleId === 3 && $jabatan === 'kaur');
        if (!$is_kaur) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Akses ditolak. Khusus untuk Kepala Urusan (Kaur).'
                ]));
        }

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
                'id'              => $ticket->id,
                'kode_tiket'      => $ticket->kode_tiket,
                'nama_dosen'      => $ticket->nama_dosen,
                'nidn'            => $ticket->nidn ?? '-',
                'label_identitas' => $ticket->label_identitas ?? 'NIDN/ID',
                'unit_tujuan'     => $ticket->unit_tujuan,
                'kategori'        => $ticket->kategori,
                'prioritas'       => $ticket->prioritas,
                'subjek'          => $ticket->subjek,
                'deskripsi'       => $deskripsiFormatted,
                'lampiran'        => $ticket->lampiran,
                'lampiran_url'    => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                'status'          => $ticket->status,
                'tanggapan'       => $ticket->tanggapan,
                'catatan_proses'  => $ticket->catatan_proses ?? null,
                'catatan_selesai' => $ticket->catatan_selesai ?? null,
                'catatan_tutup'   => $ticket->catatan_tutup ?? null,
                'tgl_diproses'    => !empty($ticket->tgl_diproses) ? date('d M Y H:i', strtotime($ticket->tgl_diproses)) : null,
                'tgl_closed'      => !empty($ticket->tgl_closed) ? date('d M Y H:i', strtotime($ticket->tgl_closed)) : null,
                'tgl_tanggapan'   => $ticket->tgl_tanggapan ? date('d M Y H:i', strtotime($ticket->tgl_tanggapan)) : null,
                'created_at'      => date('d M Y H:i', strtotime($ticket->created_at))
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Simpan Tanggapan & Perubahan Status oleh Kaur
     */
    public function respon_simpan_tanggapan() {
        $roleId = (int)$this->session->userdata('role_id');
        $jabatan = strtolower((string)($this->session->userdata('jabatan') ?? ''));
        $is_kaur = in_array($roleId, [1, 2, 22]) || ($roleId === 3 && $jabatan === 'kaur');
        if (!$is_kaur) {
            $this->session->set_flashdata('error', 'Akses ditolak. Khusus untuk Kepala Urusan (Kaur).');
            redirect('dosen/ticketing/riwayat');
            return;
        }

        $idTiket   = trim($this->input->post('id_tiket') ?: $this->input->post('ticket_id'));
        $status    = trim($this->input->post('status', true));
        $tanggapan = trim($this->input->post('tanggapan') ?? '');

        $redirectUrl = ($roleId === 2) ? 'kaur/respon-ticketing' : 'dosen/respon-ticketing';

        if (empty($idTiket)) {
            $this->session->set_flashdata('error', 'ID Tiket tidak valid.');
            redirect($redirectUrl);
            return;
        }

        if (!in_array($status, ['Dikirim', 'Sedang Diproses', 'Closed', 'Menunggu', 'Diproses', 'Selesai', 'Ditutup'])) {
            $status = 'Diproses';
        }

        // Backend Guard: Status Stepper Satu Arah (Non-reversible)
        $statusWeight = [
            'Dikirim'        => 1,
            'Menunggu'       => 1,
            'Sedang Diproses'=> 2,
            'Diproses'       => 2,
            'Closed'         => 3,
            'Selesai'        => 3,
            'Ditutup'        => 3
        ];

        $currentTicket = $this->DosenTicketing_model->get_by_id($idTiket);
        if ($currentTicket) {
            $currentStatus = $currentTicket->status ?? 'Menunggu';
            $curW = $statusWeight[$currentStatus] ?? 1;
            $newW = $statusWeight[$status] ?? 1;

            if ($newW < $curW) {
                $this->session->set_flashdata('error', "Status tiket tidak dapat dimundurkan kembali dari {$currentStatus} ke {$status}.");
                redirect($redirectUrl);
                return;
            }
        }

        $this->DosenTicketing_model->update_respon($idTiket, $status, $tanggapan);

        $msg = ($tanggapan !== '')
            ? "Tanggapan berhasil disimpan! Status tiket kini diperbarui menjadi <strong>{$status}</strong>."
            : "Status tiket berhasil diperbarui menjadi <strong>{$status}</strong>.";

        $this->session->set_flashdata('success', $msg);
        redirect($redirectUrl);
    }
}

