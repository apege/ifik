<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Panel SDM dan Keuangan
 * Role ID: 15
 * Menus:
 * 1. Membuat Ticketing
 * 2. Respon Ticketing
 * 3. Riwayat
 */
class SdmKeuanganTicketing extends CI_Controller {

    private $roleIdRequired = 15;
    private $baseRoute      = 'sdmkeuangan';
    private $unitName       = 'SDM dan Keuangan';
    private $panelBadge     = 'SDM dan Keuangan';
    private $panelTitle     = 'Panel SDM dan Keuangan';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form', 'text']);
        $this->load->model('DosenTicketing_model');

        // Pengecekan Login
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu SDM dan Keuangan.');
            redirect('login');
            return;
        }

        // Pengecekan Otorisasi Role (Role 15 = SDM & Keuangan, Role 1 = Super Admin)
        $roleId = (int)$this->session->userdata('role_id');
        if (!in_array($roleId, [1, $this->roleIdRequired])) {
            $this->session->set_flashdata('error', 'Akses ditolak. Halaman ini khusus untuk unit SDM dan Keuangan.');
            redirect('dashboard');
            return;
        }
    }

    /**
     * 1. Inbox Respon Ticketing (Menu Utama)
     */
    public function index() {
        $filterStatus = $this->input->get('status', true) ?: 'all';
        $search       = trim($this->input->get('q', true) ?? '');

        // Query tiket masuk khusus unit SDM dan Keuangan
        $tickets = $this->DosenTicketing_model->get_respon_tickets($filterStatus, $search, $this->unitName);

        // Statistik tiket unit SDM dan Keuangan
        $stats = [
            'total'    => $this->DosenTicketing_model->count_respon_tickets('all', $this->unitName),
            'menunggu' => $this->DosenTicketing_model->count_respon_tickets('Menunggu', $this->unitName),
            'diproses' => $this->DosenTicketing_model->count_respon_tickets('Diproses', $this->unitName),
            'selesai'  => $this->DosenTicketing_model->count_respon_tickets('Selesai', $this->unitName),
            'ditutup'  => $this->DosenTicketing_model->count_respon_tickets('Ditutup', $this->unitName)
        ];

        $data = [
            'title'         => 'Inbox Respon Tiket — ' . $this->panelTitle,
            'panelTitle'    => 'Inbox Respon Tiket SDM & Keuangan',
            'panelBadge'    => $this->panelBadge,
            'panelSubtitle' => 'Tinjau dan tindak lanjuti laporan kendala terkait SDM, keuangan kuliah, gaji, dan anggaran.',
            'baseRoute'     => $this->baseRoute,
            'baseResponUrl' => $this->baseRoute . '/respon-ticketing',
            'baseInputUrl'  => $this->baseRoute . '/ticketing/input',
            'baseRiwayatUrl'=> $this->baseRoute . '/ticketing/riwayat',
            'tickets'       => $tickets,
            'stats'         => $stats,
            'filterStatus'  => $filterStatus,
            'search'        => $search
        ];

        $this->load->view('unit_ticketing/respon', $data);
    }

    /**
     * AJAX Endpoint: Detail Tiket untuk Modal Respon
     */
    public function detail($id_or_kode) {
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
                'unit_tujuan'     => $ticket->unit_tujuan,
                'kategori'        => $ticket->kategori,
                'prioritas'       => $ticket->prioritas,
                'subjek'          => $ticket->subjek,
                'deskripsi'       => $deskripsiFormatted,
                'lampiran'        => $ticket->lampiran,
                'lampiran_url'    => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                'status'          => $ticket->status,
                'tanggapan'       => $ticket->tanggapan,
                'created_at'      => date('d M Y H:i', strtotime($ticket->created_at))
            ]
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Simpan Tanggapan Tiket dari Inbox Respon
     */
    public function simpan_tanggapan() {
        $idTiket   = trim($this->input->post('id_tiket') ?: $this->input->post('ticket_id'));
        $status    = trim($this->input->post('status', true));
        $tanggapan = trim($this->input->post('tanggapan') ?? '');

        if (empty($idTiket)) {
            $this->session->set_flashdata('error', 'ID Tiket tidak valid.');
            redirect($this->baseRoute . '/respon-ticketing');
            return;
        }

        if (!in_array($status, ['Dikirim', 'Sedang Diproses', 'Closed', 'Menunggu', 'Diproses', 'Selesai', 'Ditutup'])) {
            $status = 'Diproses';
        }

        $updateSuccess = $this->DosenTicketing_model->update_respon($idTiket, $status, $tanggapan);

        if ($updateSuccess) {
            $this->session->set_flashdata('success', 'Tanggapan dan status tiket #' . htmlspecialchars($idTiket) . ' berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui tanggapan tiket.');
        }

        redirect($this->baseRoute . '/respon-ticketing');
    }

    /**
     * 2. Membuat Ticketing Baru
     */
    public function input() {
        $userId = $this->session->userdata('user_id');
        $nama   = $this->session->userdata('name') ?: 'Petugas SDM dan Keuangan';
        $email  = $this->session->userdata('email') ?: '-';

        // Load unit & kategori dinamis
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

        // Fallback jika database belum memiliki data ticketing_units
        if (empty($unit_kategori_map)) {
            $unit_kategori_map = [
                'Laboratorium Bengkel dan Studio' => [
                    'Fasilitas Ruangan / AC / Proyektor Lab',
                    'Perangkat Komputer / Hardware / Monitor',
                    'Software / Lisensi Aplikasi Praktikum & Studio',
                    'Jaringan & Koneksi Internet Lab',
                    'Lain-lain (Laboratorium Bengkel dan Studio)'
                ],
                'Layanan Akademik - LAA' => [
                    'Surat Keterangan Mahasiswa / Pengantar Kuliah',
                    'Administrasi Nilai, KSM & Transkrip Akademik',
                    'Pengajuan Cuti Akademik / Aktif Kembali',
                    'Lain-lain (Layanan Akademik - LAA)'
                ],
                'Kemahasiswaan' => [
                    'Beasiswa & Bantuan Finansial Mahasiswa',
                    'Kegiatan Ormawa, UKM & Himpunan Mahasiswa',
                    'Lomba, Kompetisi & Pencatatan Prestasi Mahasiswa',
                    'Lain-lain (Kemahasiswaan)'
                ],
                'Sekretariat' => [
                    'Penerbitan Surat Keputusan (SK) Dekanat',
                    'Pengesahan & Tanda Tangan Dokumen Pimpinan',
                    'Administrasi Persuratan Masuk & Keluar Fakultas',
                    'Lain-lain (Sekretariat)'
                ],
                'SDM dan Keuangan' => [
                    'Pembayaran BPP / Registrasi Keuangan Kuliah',
                    'Dispensasi & Penyesuaian Tagihan Biaya Kuliah',
                    'Administrasi Penggajian, Honor & Klaim (SDM)',
                    'Lain-lain (SDM dan Keuangan)'
                ],
                'Program Studi (DKV, DI, DP, KTF, SR, Film dan Animasi)' => [
                    'Kurikulum, Silabus & Mata Kuliah Program Studi',
                    'Bimbingan Akademik / Dosen Pembimbing Prodi',
                    'Pendaftaran, Seminar Proposal & Sidang TA / Skripsi',
                    'Lain-lain (Program Studi)'
                ]
            ];
        }

        $data = [
            'title'             => 'Buat Tiket Kendala — ' . $this->panelTitle,
            'panelTitle'        => 'Buat Tiket Kendala Baru',
            'panelBadge'        => $this->panelBadge,
            'baseRoute'         => $this->baseRoute,
            'baseResponUrl'     => $this->baseRoute . '/respon-ticketing',
            'baseInputUrl'      => $this->baseRoute . '/ticketing/input',
            'baseRiwayatUrl'    => $this->baseRoute . '/ticketing/riwayat',
            'unit_kategori_map' => $unit_kategori_map,
            'user'              => [
                'nama'  => $nama,
                'email' => $email
            ]
        ];

        $this->load->view('unit_ticketing/input', $data);
    }

    /**
     * Simpan Tiket Kendala Baru
     */
    public function simpan() {
        $isAjax = $this->input->is_ajax_request();
        $userId = $this->session->userdata('user_id');
        $nama   = $this->input->post('nama_lengkap', true) ?: $this->session->userdata('name');
        $email  = $this->session->userdata('email') ?: '-';

        $unitTujuan = $this->input->post('unit_tujuan', true);
        $subProdi   = $this->input->post('sub_prodi', true);
        if (!empty($subProdi) && (strpos(strtolower($unitTujuan), 'prodi') !== false || strpos(strtolower($unitTujuan), 'program studi') !== false)) {
            $unitTujuan = 'Program Studi - ' . $subProdi;
        }

        $kategori   = $this->input->post('kategori', true);
        if (preg_match('/lain/i', (string)$kategori)) {
            $ketLainnya = trim($this->input->post('kategori_lainnya', true));
            if (!empty($ketLainnya)) {
                $kategori = 'Lain-lain (' . $ketLainnya . ')';
            }
        }

        $prioritas = $this->input->post('prioritas', true) ?: 'Sedang';
        $subjek    = trim($this->input->post('subjek', true));
        $deskripsi = trim($this->input->post('deskripsi'));

        if (empty($unitTujuan) || empty($subjek) || empty($deskripsi)) {
            if ($isAjax) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Harap lengkapi semua field yang berbintang merah (*).'
                ]));
            }
            $this->session->set_flashdata('error', 'Harap lengkapi semua field wajib.');
            redirect($this->baseRoute . '/ticketing/input');
            return;
        }

        // Upload lampiran jika ada
        $lampiranFile = null;
        if (!empty($_FILES['lampiran']['name'])) {
            $config['upload_path']   = './uploads/ticketing/';
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|docx|xlsx|zip';
            $config['max_size']      = 5120; // 5MB
            $config['encrypt_name']  = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('lampiran')) {
                $uploadData   = $this->upload->data();
                $lampiranFile = $uploadData['file_name'];
            }
        }

        $ticketData = [
            'id_user'         => $userId,
            'nama_dosen'      => $nama,
            'nidn'            => $this->session->userdata('nidn_nim') ?: '-',
            'unit_tujuan'     => $unitTujuan,
            'kategori'        => $kategori,
            'prioritas'       => $prioritas,
            'subjek'          => $subjek,
            'deskripsi'       => $deskripsi,
            'lampiran'        => $lampiranFile,
            'status'          => 'Dikirim',
            'tujuan_penerima' => $unitTujuan,
            'unit_terkait'    => $unitTujuan
        ];

        $insertId = $this->DosenTicketing_model->insert($ticketData);

        if ($insertId) {
            if ($isAjax) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'       => 'success',
                    'kode_tiket'   => $insertId,
                    'redirect_url' => site_url($this->baseRoute . '/ticketing/riwayat')
                ]));
            }
            $this->session->set_flashdata('success', 'Tiket kendala berhasil diajukan dengan Kode: ' . $insertId);
            redirect($this->baseRoute . '/ticketing/riwayat');
        } else {
            if ($isAjax) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan sistem saat menyimpan tiket.'
                ]));
            }
            $this->session->set_flashdata('error', 'Gagal mengajukan tiket kendala.');
            redirect($this->baseRoute . '/ticketing/input');
        }
    }

    /**
     * 3. Riwayat Tiket Saya
     */
    public function riwayat() {
        $userId = $this->session->userdata('user_id');
        $nidn   = $this->session->userdata('nidn_nim') ?: null;

        $filterStatus = $this->input->get('status', true) ?: 'all';
        $search       = trim($this->input->get('q', true) ?? '');

        // Query tiket yang diajukan oleh user ini
        $allTickets = $this->DosenTicketing_model->get_tickets($userId, $nidn);

        // Filter status & search
        $filteredTickets = array_filter($allTickets, function($t) use ($filterStatus, $search) {
            if ($filterStatus !== 'all' && $t->status !== $filterStatus) {
                return false;
            }
            if (!empty($search)) {
                $needle = strtolower($search);
                $kode   = strtolower($t->kode_tiket ?: $t->id);
                $subjek = strtolower($t->subjek ?: '');
                $kat    = strtolower($t->kategori ?: '');
                if (strpos($kode, $needle) === false && strpos($subjek, $needle) === false && strpos($kat, $needle) === false) {
                    return false;
                }
            }
            return true;
        });

        // Hitung statistik
        $stats = [
            'total'    => count($allTickets),
            'menunggu' => count(array_filter($allTickets, function($t) { return $t->status === 'Menunggu'; })),
            'diproses' => count(array_filter($allTickets, function($t) { return $t->status === 'Diproses'; })),
            'selesai'  => count(array_filter($allTickets, function($t) { return $t->status === 'Selesai'; })),
            'ditutup'  => count(array_filter($allTickets, function($t) { return $t->status === 'Ditutup'; }))
        ];

        $data = [
            'title'         => 'Riwayat Tiket — ' . $this->panelTitle,
            'panelTitle'    => 'Riwayat Tiket Kendala Saya',
            'panelBadge'    => $this->panelBadge,
            'baseRoute'     => $this->baseRoute,
            'baseResponUrl' => $this->baseRoute . '/respon-ticketing',
            'baseInputUrl'  => $this->baseRoute . '/ticketing/input',
            'baseRiwayatUrl'=> $this->baseRoute . '/ticketing/riwayat',
            'tickets'       => $filteredTickets,
            'stats'         => $stats,
            'filterStatus'  => $filterStatus,
            'search'        => $search
        ];

        $this->load->view('unit_ticketing/riwayat', $data);
    }

    /**
     * AJAX Endpoint: Detail Tiket untuk Modal Riwayat
     */
    public function riwayat_detail($id_or_kode) {
        return $this->detail($id_or_kode);
    }
}
