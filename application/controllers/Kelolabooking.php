<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelolabooking extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->helper('url');
        // If there's an auth check, it should be here, e.g., if (!$this->session->userdata('is_admin')) redirect('login');
    }

    public function index()
    {
        $data['title'] = 'Dashboard Peminjaman';
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        $data['peminjaman'] = $this->Booking_model->get_all_peminjaman();
        
        $this->load->view('admin/booking/index', $data);
    }

    // AJAX endpoint for dependent dropdown
    public function get_ruangan()
    {
        $id_kategori = $this->input->post('id_kategori');
        $ruangan = $this->Booking_model->get_ruangan_by_kategori($id_kategori);
        
        header('Content-Type: application/json');
        echo json_encode($ruangan);
    }

    // AJAX endpoint for form submission
    public function submit_booking()
    {
        header('Content-Type: application/json');

        // Simple validation
        $nama_lengkap = $this->input->post('nama_lengkap', true);
        $id_ruangan = $this->input->post('id_ruangan', true);
        $keterangan = $this->input->post('keterangan', true);
        
        $tanggal_range = $this->input->post('tanggal_peminjaman', true); // Expected "YYYY-MM-DD to YYYY-MM-DD" from Flatpickr
        $jam_mulai = $this->input->post('jam_mulai', true);
        $jam_selesai = $this->input->post('jam_selesai', true);

        if(empty($nama_lengkap) || empty($id_ruangan) || empty($tanggal_range) || empty($jam_mulai) || empty($jam_selesai)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap lengkapi semua field wajib!']);
            return;
        }

        // Parse date range
        $tgl_arr = explode(' to ', $tanggal_range);
        $tanggal_mulai = $tgl_arr[0];
        $tanggal_selesai = isset($tgl_arr[1]) ? $tgl_arr[1] : $tgl_arr[0];

        // Validasi Role: Jika bukan Admin (role_id = 1), batas peminjaman hanya 1 hari.
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
            'nama_lengkap' => $nama_lengkap,
            'id_ruangan' => $id_ruangan,
            'keterangan' => $keterangan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'status' => 'Pending', // default status
            'created_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->Booking_model->insert_booking($data_peminjaman);

        if($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan peminjaman']);
        }
    }

    public function approve($id)
    {
        $role_id = $this->session->userdata('role_id');

        // Status disesuaikan per role, sesuai ENUM di DB
        if ($role_id == 3) {
            $status = 'Disetujui Ka. Ur';
        } elseif ($role_id == 2) {
            $status = 'Disetujui Laboran';
        } else {
            $status = 'Disetujui Admin';
        }

        $update = $this->Booking_model->update_status($id, $status);
        if($update) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Peminjaman berhasil ' . $status . '! Surat resmi ber-QR Code siap dicetak.',
                'surat_url' => site_url('kaur/surat/' . $id)
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyetujui peminjaman']);
        }
    }

    public function surat($id)
    {
        // Get booking detail with room and category info
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan, ruangan.lokasi, ruangan.kapasitas, kategori_ruangan.nama_kategori');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->join('kategori_ruangan', 'kategori_ruangan.id = ruangan.id_kategori', 'left');
        $this->db->where('peminjaman.id', $id);
        $data['booking'] = $this->db->get()->row();

        if (!$data['booking']) {
            show_404();
            return;
        }

        $data['title'] = 'Surat Resmi Peminjaman Ruangan - ' . ($data['booking']->kode_ruangan ?? 'IFIK');
        $data['nomor_surat'] = 'SURAT/LAB-IFIK/' . date('Y', strtotime($data['booking']->created_at)) . '/' . sprintf('%04d', $data['booking']->id);
        $data['qr_data'] = site_url('kaur/surat/' . $id);

        $this->load->view('kaur/surat_resmi', $data);
    }

    public function reject($id)
    {
        $alasan = $this->input->post('alasan_penolakan', true);
        if (empty(trim($alasan))) {
            echo json_encode(['status' => 'error', 'message' => 'Catatan alasan penolakan wajib diisi!']);
            return;
        }
        $update = $this->Booking_model->update_status($id, 'Ditolak', $alasan);
        if($update) {
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman ditolak!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menolak peminjaman']);
        }
    }

    public function batch_approve()
    {
        header('Content-Type: application/json');
        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih setidaknya satu data peminjaman!']);
            return;
        }

        $role_id = $this->session->userdata('role_id');
        if ($role_id == 3) {
            $status = 'Disetujui Ka. Ur';
        } elseif ($role_id == 2) {
            $status = 'Disetujui Laboran';
        } else {
            $status = 'Disetujui Laboran';
        }

        $update = $this->Booking_model->batch_update_status($ids, $status);
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' data peminjaman berhasil ' . $status . '!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyetujui data terpilih']);
        }
    }

    public function batch_reject()
    {
        header('Content-Type: application/json');
        $ids = $this->input->post('ids');
        $alasan = $this->input->post('alasan_penolakan', true);

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih setidaknya satu data peminjaman!']);
            return;
        }

        if (empty(trim($alasan))) {
            echo json_encode(['status' => 'error', 'message' => 'Catatan alasan penolakan wajib diisi!']);
            return;
        }

        $update = $this->Booking_model->batch_update_status($ids, 'Ditolak', $alasan);
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' data peminjaman berhasil ditolak!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menolak data terpilih']);
        }
    }

    public function delete($id)
    {
        $delete = $this->Booking_model->delete_booking($id);
        if($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Data peminjaman berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }
}
