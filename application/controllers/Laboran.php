<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laboran extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->helper('url');
    }

    public function index()
    {
        redirect('laboran/booking');
    }

    public function booking()
    {
        $data['title'] = 'Approval & Kelola Peminjaman - Panel Laboran';
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        $data['peminjaman'] = $this->Booking_model->get_all_peminjaman();
        
        $this->load->view('laboran/booking/index', $data);
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

        $tgl_arr = explode(' to ', $tanggal_range);
        $tanggal_mulai = $tgl_arr[0];
        $tanggal_selesai = isset($tgl_arr[1]) ? $tgl_arr[1] : $tgl_arr[0];

        $role_id = $this->session->userdata('role_id');
        if ($role_id != 1 && $role_id != 2) {
            $tanggal_selesai = $tanggal_mulai;
        }

        $conflicts = $this->Booking_model->check_conflict($id_ruangan, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai);
        if (!empty($conflicts)) {
            $c = $conflicts[0];
            $jMulai = substr($c->jam_mulai, 0, 5);
            $jSelesai = substr($c->jam_selesai, 0, 5);
            $roomName = $c->kode_ruangan ? "{$c->kode_ruangan} - {$c->nama_ruangan}" : "Ruangan ini";
            echo json_encode([
                'status' => 'error',
                'message' => "Bentrok! {$roomName} sudah diajukan/dipinjam pada jam {$jMulai} - {$jSelesai} oleh {$c->nama_lengkap}."
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
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan peminjaman']);
        }
    }

    public function approve($id)
    {
        header('Content-Type: application/json');
        $status = 'Disetujui Laboran';

        $update = $this->Booking_model->update_status($id, $status);
        if($update) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Peminjaman berhasil Disetujui Laboran! Surat ber-QR Code siap diterbitkan.',
                'surat_url' => site_url('laboran/surat/' . $id)
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
        $data['qr_data'] = site_url('verifikasi/surat/' . $id);
        $data['penandatangan'] = $this->Booking_model->get_penandatangan($data['booking']->status);

        $this->load->view('kaur/surat_resmi', $data);
    }

    public function reject($id)
    {
        header('Content-Type: application/json');
        $alasan = $this->input->post('alasan_penolakan', true);

        if (empty(trim($alasan))) {
            echo json_encode(['status' => 'error', 'message' => 'Catatan alasan penolakan wajib diisi!']);
            return;
        }

        $update = $this->Booking_model->update_status($id, 'Ditolak', $alasan);
        if($update) {
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman berhasil ditolak!']);
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

        $status = 'Disetujui Laboran';
        $update = $this->Booking_model->batch_update_status($ids, $status);
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' data peminjaman berhasil Disetujui Laboran!']);
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
        header('Content-Type: application/json');
        $delete = $this->Booking_model->delete_booking($id);
        if($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Data peminjaman berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    public function batch_delete()
    {
        header('Content-Type: application/json');
        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih setidaknya satu data peminjaman!']);
            return;
        }

        $delete = $this->Booking_model->batch_delete_booking($ids);
        if ($delete) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' data peminjaman berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data terpilih']);
        }
    }

    /**
     * Halaman Pengaturan Tanda Tangan Digital Laboran
     */
    public function tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('users', ['id' => $user_id])->row();

        $nip_laboran = $user ? ($user->nidn_nim ?: $this->session->userdata('nidn_nim')) : $this->session->userdata('nidn_nim');
        $nama_laboran = $user ? $user->name : $this->session->userdata('name');
        $tanda_tangan = $user ? $user->tanda_tangan : null;

        $data = [
            'title'        => 'Pengaturan Tanda Tangan Digital - Panel Laboran',
            'user'         => $user,
            'nama'         => $nama_laboran,
            'nip'          => $nip_laboran,
            'tanda_tangan' => $tanda_tangan
        ];

        $this->load->view('laboran/tanda_tangan', $data);
    }

    /**
     * Simpan Tanda Tangan Digital Laboran (Canvas Base64 atau Upload File)
     */
    public function simpan_tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('users', ['id' => $user_id])->row();
        $nip_laboran = $user ? ($user->nidn_nim ?: $this->session->userdata('nidn_nim')) : $this->session->userdata('nidn_nim');
        $tipe = $this->input->post('tipe'); // 'canvas' atau 'upload'

        $uploadDir = FCPATH . 'uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = '';

        if ($tipe === 'canvas') {
            $signatureData = $this->input->post('signature_data');
            if (empty($signatureData) || strpos($signatureData, 'data:image/png;base64,') !== 0) {
                $this->session->set_flashdata('error', 'Silakan goreskan tanda tangan pada canvas terlebih dahulu.');
                redirect('laboran/tanda-tangan');
                return;
            }

            // Decode base64 image
            $imageData = str_replace('data:image/png;base64,', '', $signatureData);
            $imageData = str_replace(' ', '+', $imageData);
            $decoded = base64_decode($imageData);

            if (!$decoded) {
                $this->session->set_flashdata('error', 'Format gambar tanda tangan tidak valid.');
                redirect('laboran/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_laboran ?: 'laboran');
            $filename = 'ttd_laboran_' . $cleanNip . '_' . time() . '.png';
            file_put_contents($uploadDir . $filename, $decoded);

        } else {
            // Upload file
            if (empty($_FILES['file_ttd']['name'])) {
                $this->session->set_flashdata('error', 'Pilih file gambar tanda tangan terlebih dahulu.');
                redirect('laboran/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_laboran ?: 'laboran');
            $config = [
                'upload_path'   => $uploadDir,
                'allowed_types' => 'png|jpg|jpeg',
                'max_size'      => 3072, // 3MB
                'file_name'     => 'ttd_laboran_' . $cleanNip . '_' . time()
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file_ttd')) {
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];
            } else {
                $err = $this->upload->display_errors('', '');
                $this->session->set_flashdata('error', 'Gagal mengunggah file tanda tangan: ' . $err);
                redirect('laboran/tanda-tangan');
                return;
            }
        }

        if (!empty($filename)) {
            // Hapus file tanda tangan lama jika ada
            $oldTtd = $user ? $user->tanda_tangan : null;
            if (!empty($oldTtd) && file_exists($uploadDir . $oldTtd)) {
                @unlink($uploadDir . $oldTtd);
            }

            $this->db->where('id', $user_id)->update('users', ['tanda_tangan' => $filename]);
            $this->session->set_flashdata('success', 'Tanda tangan digital Laboran berhasil disimpan dan siap digunakan pada surat resmi!');
        }

        redirect('laboran/tanda-tangan');
    }

    /**
     * Hapus Tanda Tangan Digital Laboran
     */
    public function hapus_tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('users', ['id' => $user_id])->row();
        $oldTtd = $user ? $user->tanda_tangan : null;

        if (!empty($oldTtd)) {
            $filePath = FCPATH . 'uploads/signatures/' . $oldTtd;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $this->db->where('id', $user_id)->update('users', ['tanda_tangan' => null]);
            $this->session->set_flashdata('success', 'Tanda tangan digital berhasil dihapus.');
        }

        redirect('laboran/tanda-tangan');
    }

    /**
     * Unduh File Tanda Tangan Digital Laboran
     */
    public function download_tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('users', ['id' => $user_id])->row();
        $tanda_tangan = $user ? $user->tanda_tangan : null;

        if (empty($tanda_tangan)) {
            $this->session->set_flashdata('error', 'Belum ada tanda tangan yang tersimpan untuk diunduh.');
            redirect('laboran/tanda-tangan');
            return;
        }

        $filePath = FCPATH . 'uploads/signatures/' . $tanda_tangan;
        if (!file_exists($filePath)) {
            $this->session->set_flashdata('error', 'File tanda tangan fisik tidak ditemukan di server.');
            redirect('laboran/tanda-tangan');
            return;
        }

        $this->load->helper('download');
        $namaBersih = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name ?? 'laboran');
        $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $user->nidn_nim ?? 'nip');
        $downloadName = 'TTD_Laboran_' . $namaBersih . '_' . $cleanNip . '.png';

        force_download($downloadName, file_get_contents($filePath));
    }
}