<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Dashboard
 * 
 * Controller utama untuk mengelola halaman publik / landing page portal IFIK (Integrated Faculty Information & Facilities).
 * Menangani rendering halaman beranda yang mengintegrasikan bilah navigasi (Navbar Publik), hero slider,
 * informasi jadwal peminjaman ruangan laboratorium, serta layanan fakultas.
 * 
 * @package     IFIK Telkom University
 * @subpackage  Controllers
 * @category    Public Dashboard & Navigation
 * @author      Nazril Rizki
 */
class Dashboard extends CI_Controller {

	/**
	 * Halaman Utama (Landing Page / Beranda Publik)
	 * 
	 * Menyiapkan data header/slide banner, memuat jadwal peminjaman ruangan yang telah disetujui,
	 * data kategori lab, serta me-render view 'dashboard/index' yang secara modular mengikutsertakan
	 * partial navigasi utama: application/views/partials/navbar.php.
	 * 
	 * @access public
	 * @return void
	 */
	public function index()
	{
        // Cegah Chrome meng-cache halaman landing page saat status login berubah
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->output->set_header('Pragma: no-cache');

        // Load the URL helper if it's not loaded globally, since we need base_url()
        $this->load->helper('url');
        $this->load->model('Booking_model');
        $this->load->model('Header_model');
        
        $data['jadwal_peminjaman'] = $this->Booking_model->get_approved_bookings();
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        
        $data['ruangan'] = $this->Booking_model->get_all_ruangan();

        $data['header_settings'] = $this->Header_model->get_settings();
        $data['header_slides'] = $this->Header_model->get_slides();
        
        $this->load->view('dashboard/index', $data);
    }

    public function lab_detail($id = 'multimedia')
    {
        $this->load->helper('url');
        $this->load->model('Booking_model');
        $data['lab_key'] = strtolower($id);

        // Load all ruangan data via Booking_model to sync foto and model_3d details
        $data['all_ruangan'] = $this->Booking_model->get_all_ruangan();

        $this->load->view('dashboard/lab_detail', $data);
    }

    public function kalender()
    {
        $this->load->helper('url');
        $this->load->model('Booking_model');
        $data['jadwal_peminjaman'] = $this->Booking_model->get_approved_bookings();
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        $data['ruangan'] = $this->Booking_model->get_all_ruangan();
        $this->load->view('dashboard/kalender', $data);
    }

    public function ajukan()
    {
        $this->load->helper('url');
        // Pengecekan Login: Hanya pengguna yang sudah login yang bisa mengajukan peminjaman
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengajukan peminjaman ruangan.');
            redirect('login');
            return;
        }

        $this->load->model('Booking_model');
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        $this->db->select('ruangan.*, ruangan.ruangan AS nama_ruangan, ruangan.id AS kode_ruangan');
        $this->db->order_by('ruangan.date', 'ASC');
        $this->db->order_by('ruangan.id', 'ASC');
        $data['ruangan'] = $this->db->get('ruangan')->result();

        $this->load->view('dashboard/ajukan_booking', $data);
    }


    public function ajukan_booking()
    {
        header('Content-Type: application/json');
        $this->load->model('Booking_model');
        
        $nama_lengkap = $this->input->post('nama_lengkap', true);
        $id_ruangan = $this->input->post('id_ruangan', true);
        $keterangan = $this->input->post('keterangan', true);
        $tanggal_range = $this->input->post('tanggal_peminjaman', true);
        $jam_mulai = $this->input->post('jam_mulai', true);
        $jam_selesai = $this->input->post('jam_selesai', true);

        if(empty($nama_lengkap) || empty($id_ruangan) || empty($tanggal_range) || empty($jam_mulai) || empty($jam_selesai)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap lengkapi semua field wajib!']);
            return;
        }

        // Parse date range (format: "YYYY-MM-DD" atau "YYYY-MM-DD to YYYY-MM-DD")
        $tgl_arr = explode(' to ', $tanggal_range);
        $tanggal_mulai = $tgl_arr[0];
        $tanggal_selesai = isset($tgl_arr[1]) ? $tgl_arr[1] : $tgl_arr[0];

        // Validasi Role: non-admin hanya bisa 1 hari
        $role_id = $this->session->userdata('role_id');
        if ($role_id != 1) {
            $tanggal_selesai = $tanggal_mulai;
        }

        // Check bentrok/konflik peminjaman untuk ruangan yang sama
        $conflicts = $this->Booking_model->check_conflict($id_ruangan, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai);
        if (!empty($conflicts)) {
            $c = $conflicts[0];
            $jMulai = substr($c->jam_mulai, 0, 5);
            $jSelesai = substr($c->jam_selesai, 0, 5);
            $roomName = $c->kode_ruangan ? "{$c->nama_ruangan} (Ruang: {$c->kode_ruangan})" : ($c->nama_ruangan ? $c->nama_ruangan : "Ruangan ini");
            echo json_encode([
                'status' => 'error',
                'message' => "Bentrok! {$roomName} sudah diajukan/dipinjam pada jam {$jMulai} - {$jSelesai} oleh {$c->nama_lengkap}. Silakan pilih jam atau ruangan lain."
            ]);
            return;
        }


        $data_peminjaman = array(
            'id_user' => $this->session->userdata('user_id'),
            'nama_lengkap' => $nama_lengkap,
            'id_ruangan' => $id_ruangan,
            'keterangan' => $keterangan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->Booking_model->insert_booking($data_peminjaman);

        if($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Booking berhasil diajukan dan menunggu persetujuan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengajukan booking']);
        }
    }

    public function approve_booking($id)
    {
        header('Content-Type: application/json');
        $this->load->model('Booking_model');
        $role_id = (int)$this->session->userdata('role_id');
        $jabatan = strtolower((string)($this->session->userdata('jabatan') ?? ''));
        $is_kaur_user = ($role_id == 2) || ($role_id == 3 && $jabatan === 'kaur');

        if (!in_array($role_id, [1, 2, 21, 22]) && !$is_kaur_user) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menyetujui peminjaman ini.']);
            return;
        }

        // Validasi: Cek tanda tangan jika Laboran atau Ka. Ur
        if ($is_kaur_user || $role_id == 21) {
            if (!$this->Booking_model->has_signature($this->session->userdata('user_id'), $role_id)) {
                $targetUrl = $is_kaur_user ? site_url('kaur/tanda-tangan') : site_url('laboran/tanda-tangan');
                echo json_encode([
                    'status'          => 'error',
                    'needs_signature' => true,
                    'signature_url'   => $targetUrl,
                    'message'         => 'Anda belum memiliki Tanda Tangan Digital! Silakan buat atau unggah tanda tangan terlebih dahulu sebelum menyetujui peminjaman.'
                ]);
                return;
            }
        }

        if ($is_kaur_user) {
            $status = 'Disetujui Ka. Ur';
        } elseif ($role_id == 21) {
            $status = 'Disetujui Laboran';
        } else {
            $status = 'Disetujui Admin';
        }

        $update = $this->Booking_model->update_status($id, $status);
        if($update) {
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman ' . $status . '!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyetujui peminjaman']);
        }
    }

    public function reject_booking($id)
    {
        header('Content-Type: application/json');
        $this->load->model('Booking_model');
        $role_id = (int)$this->session->userdata('role_id');

        if (!in_array($role_id, [1, 2, 21, 22])) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menolak peminjaman ini.']);
            return;
        }

        $alasan = $this->input->post('alasan_penolakan', true);
        $update = $this->Booking_model->update_status($id, 'Ditolak', $alasan);
        if($update) {
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman ditolak!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menolak peminjaman']);
        }
    }

    public function delete_booking($id)
    {
        header('Content-Type: application/json');
        $this->load->model('Booking_model');
        $role_id = (int)$this->session->userdata('role_id');

        if (!in_array($role_id, [1, 2, 21, 22])) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menghapus jadwal ini.']);
            return;
        }

        $delete = $this->Booking_model->delete_booking($id);
        if($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Jadwal peminjaman berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus jadwal peminjaman']);
        }
    }

    public function get_updated_bookings()
    {
        header('Content-Type: application/json');
        $this->load->model('Booking_model');
        $data = $this->Booking_model->get_approved_bookings();
        echo json_encode($data ? $data : []);
    }

    public function tambah_ruangan()
    {
        header('Content-Type: application/json');
        $role_id = $this->session->userdata('role_id');

        if ($role_id != 1 && $role_id != 22) {
            echo json_encode(['status' => 'error', 'message' => 'Hanya Admin System yang dapat menambahkan ruangan baru!']);
            return;
        }

        $nama_ruangan = $this->input->post('nama_ruangan', true);
        $kode_ruangan = $this->input->post('kode_ruangan', true);
        $id_kategori  = $this->input->post('id_kategori', true);
        $kapasitas    = $this->input->post('kapasitas', true);
        $lokasi       = $this->input->post('lokasi', true);
        $status       = $this->input->post('status', true);

        $rooms = array_values(array_filter(array_map('trim', explode(',', (string)$kode_ruangan))));
        if (empty($rooms) && !empty($kode_ruangan)) {
            $rooms = [strtoupper(trim((string)$kode_ruangan))];
        }

        if (empty($nama_ruangan) || empty($rooms) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan/Lab, Ruangan Fisik (Nomor LK), dan Kategori!']);
            return;
        }

        // Cek duplikasi ruangan fisik (1 ruangan fisik = 1 baris)
        $this->db->select('id, ruangan, ruangan AS nama_ruangan, id AS kode_ruangan');
        $existing_ruangan = $this->db->get('ruangan')->result();
        $canonicalize = function($str) {
            return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string)$str));
        };
        foreach ($rooms as $target) {
            $target_clean = strtoupper(trim($target));
            $target_canon = $canonicalize($target);
            if (empty($target_clean)) continue;

            foreach ($existing_ruangan as $row) {
                $rowCode = $row->kode_ruangan ?: $row->id;
                if (empty($rowCode)) continue;
                $row_rooms = array_filter(array_map('trim', explode(',', (string)$rowCode)));
                foreach ($row_rooms as $r) {
                    $r_clean = strtoupper(trim($r));
                    $r_canon = $canonicalize($r);
                    if ($target_clean === $r_clean || (!empty($target_canon) && $target_canon === $r_canon)) {
                        echo json_encode([
                            'status'  => 'error',
                            'message' => "Ruangan fisik '{$target_clean}' sudah digunakan oleh fasilitas '{$row->nama_ruangan}'! Satu nomor ruangan fisik hanya dapat digunakan oleh 1 fasilitas."
                        ]);
                        return;
                    }
                }
            }
        }

        $fields = $this->db->list_fields('ruangan');
        $base_data = array();
        if (in_array('ruangan', $fields)) $base_data['ruangan'] = $nama_ruangan;
        if (in_array('nama_ruangan', $fields)) $base_data['nama_ruangan'] = $nama_ruangan;
        if (in_array('id_kategori', $fields)) $base_data['id_kategori'] = $id_kategori;
        if (in_array('kapasitas', $fields)) $base_data['kapasitas'] = $kapasitas ? $kapasitas : 30;
        if (in_array('lokasi', $fields)) $base_data['lokasi'] = $lokasi ? $lokasi : 'Gedung Sebatik (FIK)';
        if (in_array('status', $fields)) $base_data['status'] = $status ? $status : 'Tersedia';
        if (in_array('date', $fields)) $base_data['date'] = date('Y-m-d H:i:s');

        $inserted = 0;
        foreach ($rooms as $single_code) {
            $clean_code = strtoupper(trim($single_code));
            if (empty($clean_code)) continue;

            $row_data = $base_data;
            if (in_array('id', $fields)) $row_data['id'] = $clean_code;
            if (in_array('kode_ruangan', $fields)) $row_data['kode_ruangan'] = $clean_code;

            if ($this->db->insert('ruangan', $row_data)) {
                $inserted++;
            }
        }

        if ($inserted > 0) {
            $msg = ($inserted > 1) 
                ? "{$inserted} ruangan fisik baru berhasil ditambahkan!" 
                : "Ruangan baru berhasil ditambahkan!";
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan ruangan baru.']);
        }
    }

    public function about()
    {
        $this->load->helper('url');
        $this->load->model('Header_model');
        $data['header_settings'] = $this->Header_model->get_settings();
        $data['title'] = 'Tentang Fakultas Industri Kreatif';
        $this->load->view('dashboard/about', $data);
    }

    public function riwayat()
    {
        $this->load->helper('url');
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk melihat riwayat peminjaman.');
            redirect('login');
            return;
        }

        $this->load->model('Booking_model');
        $user_id = $this->session->userdata('user_id');
        $nama_lengkap = $this->session->userdata('name');

        $raw_riwayat = $this->Booking_model->get_peminjaman_by_user($user_id, $nama_lengkap);

        $totalCount = count($raw_riwayat);
        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        $peminjaman = array();
        foreach ($raw_riwayat as $p) {
            $s = $p->status;
            $statusCategory = 'menunggu';
            if ($s === 'Pending') {
                $pendingCount++;
                $statusCategory = 'menunggu';
            } elseif (stripos($s, 'Disetujui') !== false || stripos($s, 'Approved') !== false || stripos($s, 'Ka. Ur') !== false || stripos($s, 'Laboran') !== false || stripos($s, 'Admin') !== false) {
                $approvedCount++;
                $statusCategory = 'disetujui';
            } elseif ($s === 'Ditolak' || $s === 'Dibatalkan') {
                $rejectedCount++;
                $statusCategory = 'ditolak';
            }

            $indoMonths = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $dateFormatted = '';
            if (!empty($p->tanggal_mulai)) {
                $tglM = $p->tanggal_mulai;
                $tglS = $p->tanggal_selesai;
                $dM = (int)date('j', strtotime($tglM));
                $mM = $indoMonths[(int)date('n', strtotime($tglM))];
                $yM = date('Y', strtotime($tglM));
                $dateFormatted = "{$dM} {$mM} {$yM}";

                if (!empty($tglS) && $tglM !== $tglS) {
                    $dS = (int)date('j', strtotime($tglS));
                    $mS = $indoMonths[(int)date('n', strtotime($tglS))];
                    $yS = date('Y', strtotime($tglS));
                    if ($yM === $yS && $mM === $mS) {
                        $dateFormatted = "{$dM} - {$dS} {$mM} {$yM}";
                    } else {
                        $dateFormatted = "{$dM} {$mM} {$yM} s/d {$dS} {$mS} {$yS}";
                    }
                }
            }

            $timeFormatted = '';
            if (!empty($p->jam_mulai) && !empty($p->jam_selesai)) {
                $timeFormatted = substr($p->jam_mulai, 0, 5) . ' - ' . substr($p->jam_selesai, 0, 5) . ' WIB';
            }

            $roomName = $p->nama_ruangan ?: ($p->kode_ruangan ?: 'Ruangan Lab');

            $peminjaman[] = array(
                'id' => (string)$p->id,
                'id_ruangan' => $p->id_ruangan,
                'nama_lengkap' => $p->nama_lengkap,
                'ruangan' => $roomName,
                'nama_ruangan' => $p->nama_ruangan,
                'kode_ruangan' => $p->kode_ruangan,
                'kategori' => $p->nama_kategori ?? 'Ruangan',
                'nama_kategori' => $p->nama_kategori ?? 'Ruangan',
                'lokasi' => $p->lokasi ?? '',
                'kapasitas' => $p->kapasitas ?? '',
                'keterangan' => $p->keterangan,
                'agenda' => $p->keterangan,
                'status' => $p->status,
                'status_category' => $statusCategory,
                'alasan_penolakan' => $p->alasan_penolakan ?? '',
                'tanggal' => $p->tanggal_mulai,
                'tanggal_booking' => $p->tanggal_mulai,
                'tanggal_mulai' => $p->tanggal_mulai,
                'tanggal_selesai' => $p->tanggal_selesai,
                'tanggal_raw' => $p->tanggal_mulai,
                'tanggal_formatted' => $dateFormatted,
                'waktu_mulai' => $p->jam_mulai,
                'waktu_selesai' => $p->jam_selesai,
                'time_formatted' => $timeFormatted,
                'dokumen_pendukung' => $p->dokumen_pendukung ?? '',
                'created_at' => $p->created_at
            );
        }

        $data['title'] = 'Riwayat Peminjaman Ruangan & Lab - IFIK';
        $data['peminjaman'] = $peminjaman;
        $data['riwayat'] = $peminjaman;
        $data['total_pengajuan'] = $totalCount;
        $data['totalCount'] = $totalCount;
        $data['total_menunggu'] = $pendingCount;
        $data['pendingCount'] = $pendingCount;
        $data['total_disetujui'] = $approvedCount;
        $data['approvedCount'] = $approvedCount;
        $data['total_ditolak'] = $rejectedCount;
        $data['rejectedCount'] = $rejectedCount;

        $this->load->view('dashboard/riwayat_booking', $data);
    }

    public function cancel_booking($id)
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
            return;
        }

        $this->load->model('Booking_model');
        $user_id = $this->session->userdata('user_id');
        $nama_lengkap = $this->session->userdata('name');

        $cancel = $this->Booking_model->cancel_booking((int)$id, $user_id, $nama_lengkap);

        if ($cancel) {
            echo json_encode(['status' => 'success', 'success' => true, 'message' => 'Pengajuan peminjaman berhasil dibatalkan.']);
        } else {
            echo json_encode(['status' => 'error', 'success' => false, 'message' => 'Gagal membatalkan pengajuan. Hanya pengajuan berstatus "Pending" yang dapat dibatalkan.']);
        }
    }

    public function bulk_cancel_booking()
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'success' => false, 'message' => 'Silakan login terlebih dahulu.']);
            return;
        }

        $this->load->model('Booking_model');
        $user_id = $this->session->userdata('user_id');
        $nama_lengkap = $this->session->userdata('name');

        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'success' => false, 'message' => 'Tidak ada pengajuan yang dipilih.']);
            return;
        }

        $successCount = 0;
        $failedCount = 0;
        foreach ($ids as $id) {
            $cancel = $this->Booking_model->cancel_booking((int)$id, $user_id, $nama_lengkap);
            if ($cancel) {
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        if ($successCount > 0) {
            $msg = $successCount . ' pengajuan peminjaman berhasil dibatalkan.';
            if ($failedCount > 0) {
                $msg .= " ($failedCount pengajuan dilewati karena sudah tidak berstatus Pending).";
            }
            echo json_encode([
                'status' => 'success',
                'success' => true,
                'message' => $msg,
                'cancelled_count' => $successCount
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => 'Tidak ada pengajuan yang dapat dibatalkan (hanya pengajuan berstatus "Pending" yang bisa dibatalkan).'
            ]);
        }
    }

    public function get_my_bookings_json()
    {
        header('Content-Type: application/json');
        if (!$this->session->userdata('logged_in')) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'data' => []]);
            return;
        }

        $this->load->model('Booking_model');
        $user_id = $this->session->userdata('user_id');
        $nama_lengkap = $this->session->userdata('name');

        $riwayat = $this->Booking_model->get_peminjaman_by_user($user_id, $nama_lengkap);

        $totalCount = count($riwayat);
        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        $formatted = array();
        foreach ($riwayat as $p) {
            $s = $p->status;
            $statusCategory = 'menunggu';
            if ($s === 'Pending') {
                $pendingCount++;
                $statusCategory = 'menunggu';
            } elseif (stripos($s, 'Disetujui') !== false || stripos($s, 'Approved') !== false || stripos($s, 'Ka. Ur') !== false || stripos($s, 'Laboran') !== false || stripos($s, 'Admin') !== false) {
                $approvedCount++;
                $statusCategory = 'disetujui';
            } elseif ($s === 'Ditolak' || $s === 'Dibatalkan') {
                $rejectedCount++;
                $statusCategory = 'ditolak';
            }

            $indoMonths = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $dateFormatted = '';
            if (!empty($p->tanggal_mulai)) {
                $tglM = $p->tanggal_mulai;
                $tglS = $p->tanggal_selesai;
                $dM = (int)date('j', strtotime($tglM));
                $mM = $indoMonths[(int)date('n', strtotime($tglM))];
                $yM = date('Y', strtotime($tglM));
                $dateFormatted = "{$dM} {$mM} {$yM}";

                if (!empty($tglS) && $tglM !== $tglS) {
                    $dS = (int)date('j', strtotime($tglS));
                    $mS = $indoMonths[(int)date('n', strtotime($tglS))];
                    $yS = date('Y', strtotime($tglS));
                    if ($yM === $yS && $mM === $mS) {
                        $dateFormatted = "{$dM} - {$dS} {$mM} {$yM}";
                    } else {
                        $dateFormatted = "{$dM} {$mM} {$yM} s/d {$dS} {$mS} {$yS}";
                    }
                }
            }

            $timeFormatted = '';
            if (!empty($p->jam_mulai) && !empty($p->jam_selesai)) {
                $timeFormatted = substr($p->jam_mulai, 0, 5) . ' - ' . substr($p->jam_selesai, 0, 5) . ' WIB';
            }

            $formatted[] = array(
                'id' => (string)$p->id,
                'id_ruangan' => $p->id_ruangan,
                'nama_lengkap' => $p->nama_lengkap,
                'nama_ruangan' => $p->nama_ruangan,
                'kode_ruangan' => $p->kode_ruangan,
                'nama_kategori' => $p->nama_kategori ?? 'Ruangan',
                'lokasi' => $p->lokasi ?? '',
                'kapasitas' => $p->kapasitas ?? '',
                'keterangan' => $p->keterangan,
                'agenda' => $p->keterangan,
                'status' => $p->status,
                'status_category' => $statusCategory,
                'alasan_penolakan' => $p->alasan_penolakan ?? '',
                'tanggal_mulai' => $p->tanggal_mulai,
                'tanggal_selesai' => $p->tanggal_selesai,
                'tanggal_raw' => $p->tanggal_mulai,
                'tanggal_formatted' => $dateFormatted,
                'waktu_mulai' => $p->jam_mulai,
                'waktu_selesai' => $p->jam_selesai,
                'time_formatted' => $timeFormatted,
                'dokumen_pendukung' => $p->dokumen_pendukung ?? '',
                'created_at' => $p->created_at
            );
        }

        echo json_encode(array(
            'status' => 'success',
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'data' => $formatted
        ));
    }

    public function sidebar_demo()
    {
        $this->load->helper('url');
        $this->load->view('demo/sidebar');
    }
}


