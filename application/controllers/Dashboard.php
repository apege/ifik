<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
        // Load the URL helper if it's not loaded globally, since we need base_url()
        $this->load->helper('url');
        $this->load->model('Booking_model');
        $this->load->model('Header_model');
        
        $data['jadwal_peminjaman'] = $this->Booking_model->get_approved_bookings();
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        
        $this->db->where('status', 'Tersedia');
        $data['ruangan'] = $this->db->get('ruangan')->result();

        $data['header_settings'] = $this->Header_model->get_settings();
        $data['header_slides'] = $this->Header_model->get_slides();
        
        $this->load->view('dashboard/index', $data);
    }

    public function lab_detail($id = 'multimedia')
    {
        $this->load->helper('url');
        $data['lab_key'] = strtolower($id);

        // Load ruangan data from DB for dynamic rooms (non-hardcoded keys)
        $this->db->where('status', 'Tersedia');
        $data['all_ruangan'] = $this->db->get('ruangan')->result();

        $this->load->view('dashboard/lab_detail', $data);
    }

    public function kalender()
    {
        $this->load->helper('url');
        $this->load->model('Booking_model');
        $data['jadwal_peminjaman'] = $this->Booking_model->get_approved_bookings();
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        $this->db->where('status', 'Tersedia');
        $data['ruangan'] = $this->db->get('ruangan')->result();
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
        $this->db->where('status', 'Tersedia');
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
            $roomName = $c->kode_ruangan ? "{$c->kode_ruangan} - {$c->nama_ruangan}" : "Ruangan ini";
            echo json_encode([
                'status' => 'error',
                'message' => "Bentrok! {$roomName} sudah diajukan/dipinjam pada jam {$jMulai} - {$jSelesai} oleh {$c->nama_lengkap}. Silakan pilih jam atau ruangan lain."
            ]);
            return;
        }


        $data_peminjaman = array(
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
        $role_id = $this->session->userdata('role_id');

        if (!in_array($role_id, [1, 2, 3])) {
            echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk menyetujui peminjaman ini.']);
            return;
        }

        if ($role_id == 3) {
            $status = 'Disetujui Ka. Ur';
        } elseif ($role_id == 2) {
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
        $role_id = $this->session->userdata('role_id');

        if (!in_array($role_id, [1, 2, 3])) {
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
        $role_id = $this->session->userdata('role_id');

        if (!in_array($role_id, [1, 2, 3])) {
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

        if ($role_id != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Hanya Admin System yang dapat menambahkan ruangan baru!']);
            return;
        }

        $nama_ruangan = $this->input->post('nama_ruangan', true);
        $kode_ruangan = $this->input->post('kode_ruangan', true);
        $id_kategori  = $this->input->post('id_kategori', true);
        $kapasitas    = $this->input->post('kapasitas', true);
        $lokasi       = $this->input->post('lokasi', true);
        $status       = $this->input->post('status', true);

        if (empty($nama_ruangan) || empty($kode_ruangan) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan, Kode Ruangan, dan Kategori!']);
            return;
        }

        $data_ruangan = array(
            'nama_ruangan' => $nama_ruangan,
            'kode_ruangan' => strtoupper($kode_ruangan),
            'id_kategori'  => $id_kategori,
            'kapasitas'    => $kapasitas ? $kapasitas : 30,
            'lokasi'       => $lokasi ? $lokasi : 'Gedung Sebatik (FIK)',
            'status'       => $status ? $status : 'Tersedia'
        );

        $insert = $this->db->insert('ruangan', $data_ruangan);

        if ($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Ruangan baru berhasil ditambahkan!']);
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
}


