<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kaur extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->helper('url');
    }

    public function index()
    {
        redirect('kaur/approval');
    }

    public function approval()
    {
        $data['title'] = 'Persetujuan Resmi & Surat QR - Ka. Ur / Kepala Lab';
        $data['peminjaman'] = $this->Booking_model->get_all_peminjaman();
        
        $this->load->view('kaur/approval/index', $data);
    }

    public function live_data()
    {
        header('Content-Type: application/json');
        $data = $this->Booking_model->get_all_peminjaman();
        
        $totalCount = count($data);
        $pendingCount = 0;
        $laboranCount = 0;
        $kaurCount = 0;
        $adminCount = 0;
        $rejectedCount = 0;

        $indoMonths = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $formatted = [];

        foreach ($data as $row) {
            $st = $row->status;
            $statusCategory = 'pending';
            if (strpos($st, 'Laboran') !== false) {
                $statusCategory = 'laboran';
                $laboranCount++;
            } elseif (strpos($st, 'Ka. Ur') !== false || strpos($st, 'Kaur') !== false) {
                $statusCategory = 'kaur';
                $kaurCount++;
            } elseif (strpos($st, 'Admin') !== false) {
                $statusCategory = 'admin';
                $adminCount++;
            } elseif ($st === 'Ditolak') {
                $statusCategory = 'rejected';
                $rejectedCount++;
            } else {
                $pendingCount++;
            }

            // Format tanggal
            $tglM = $row->tanggal_mulai;
            $tglS = $row->tanggal_selesai;
            $dM = (int)date('j', strtotime($tglM));
            $mM = $indoMonths[(int)date('n', strtotime($tglM))];
            $yM = date('Y', strtotime($tglM));
            $tglFormatted = "{$dM} {$mM} {$yM}";

            if ($tglM !== $tglS && !empty($tglS)) {
                $dS = (int)date('j', strtotime($tglS));
                $mS = $indoMonths[(int)date('n', strtotime($tglS))];
                $yS = date('Y', strtotime($tglS));
                if ($yM === $yS && $mM === $mS) {
                    $tglFormatted = "{$dM} - {$dS} {$mM} {$yM}";
                } else {
                    $tglFormatted = "{$dM} {$mM} {$yM} s/d {$dS} {$mS} {$yS}";
                }
            }

            $waktuFormatted = substr($row->jam_mulai, 0, 5) . ' - ' . substr($row->jam_selesai, 0, 5);

            $formatted[] = [
                'id' => (int)$row->id,
                'id_user' => $row->id_user ? (int)$row->id_user : null,
                'id_ruangan' => (int)$row->id_ruangan,
                'nama_lengkap' => $row->nama_lengkap,
                'nama_ruangan' => $row->nama_ruangan,
                'kode_ruangan' => $row->kode_ruangan,
                'lokasi' => $row->lokasi,
                'kapasitas' => $row->kapasitas,
                'nama_kategori' => $row->nama_kategori,
                'keterangan' => $row->keterangan,
                'tanggal_mulai' => $row->tanggal_mulai,
                'tanggal_selesai' => $row->tanggal_selesai,
                'tanggal_formatted' => $tglFormatted,
                'jam_mulai' => $row->jam_mulai,
                'jam_selesai' => $row->jam_selesai,
                'waktu_formatted' => $waktuFormatted,
                'status' => $row->status,
                'status_category' => $statusCategory,
                'alasan_penolakan' => $row->alasan_penolakan,
                'created_at' => $row->created_at,
                'created_at_formatted' => date('d/m/Y H:i', strtotime($row->created_at))
            ];
        }

        echo json_encode([
            'status' => 'success',
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'laboranCount' => $laboranCount,
            'kaurCount' => $kaurCount,
            'adminCount' => $adminCount,
            'rejectedCount' => $rejectedCount,
            'data' => $formatted
        ]);
    }

    public function approve($id)
    {
        header('Content-Type: application/json');
        $status = 'Disetujui Ka. Ur';

        $update = $this->Booking_model->update_status($id, $status);
        if($update) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Peminjaman berhasil Disetujui Resmi oleh Ka. Ur / Kepala Lab! Surat ber-QR Code siap diterbitkan.',
                'surat_url' => site_url('kaur/surat/' . $id)
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyetujui peminjaman']);
        }
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
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman resmi ditolak!']);
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

        $status = 'Disetujui Ka. Ur';
        $update = $this->Booking_model->batch_update_status($ids, $status);
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' permohonan berhasil Disetujui Resmi oleh Ka. Ur!']);
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
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' permohonan berhasil ditolak!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menolak data terpilih']);
        }
    }

    public function delete($id)
    {
        header('Content-Type: application/json');
        $delete = $this->Booking_model->delete_booking($id);
        if($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Data permohonan berhasil dihapus!']);
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
}