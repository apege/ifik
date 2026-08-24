<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KoordinatorTA_model');
        $this->load->helper(array('form', 'url', 'text'));
    }

    // Dashboard Koordinator TA: Daftar Mahasiswa Mendaftar Tugas Akhir
    public function index() {
        $nip_koor = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19800202002'; // Mock NIP Koordinator TA
        $data['title'] = 'Dashboard Koordinator TA';
        $data['nip_koor'] = $nip_koor;
        $data['list_mahasiswa'] = $this->KoordinatorTA_model->get_all_mahasiswa_ta();

        $this->load->view('koordinator_ta/dashboard', $data);
    }

    // Detail Mahasiswa & Approval Koordinator TA
    public function detail_mahasiswa($nim) {
        $data['title'] = 'Detail & Approval Koordinator TA';
        $data['detail'] = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($nim);
        $data['dosen_list'] = $this->KoordinatorTA_model->get_dosen_list();

        // Fallback POST standard
        if ($this->input->post('action')) {
            $status       = $this->input->post('status'); // 'Approved' atau 'Rejected'
            $catatan      = trim($this->input->post('catatan_koor') ?? '');
            $pembimbing_1 = $this->input->post('pembimbing_1');
            $pembimbing_2 = $this->input->post('pembimbing_2');

            $res = $this->KoordinatorTA_model->update_approval_koor_ajax($nim, $status, $catatan, $pembimbing_1, $pembimbing_2);
            if ($res['status']) {
                $this->session->set_flashdata('success', $res['message']);
            } else {
                $this->session->set_flashdata('error', $res['message']);
            }
            redirect('koordinatorta/detail_mahasiswa/' . $nim);
            return;
        }

        $this->load->view('koordinator_ta/detail_mahasiswa', $data);
    }

    // AJAX Endpoint: Approval Koordinator TA dengan Validasi LAA & Pembimbing 1 & 2
    public function ajax_approval() {
        header('Content-Type: application/json');

        $nim          = $this->input->post('nim');
        $status       = $this->input->post('status'); // 'Approved' / 'Rejected'
        $catatan      = trim($this->input->post('catatan_koor') ?? '');
        $pembimbing_1 = $this->input->post('pembimbing_1');
        $pembimbing_2 = $this->input->post('pembimbing_2');

        if (empty($nim) || empty($status)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Parameter tidak lengkap.'
            ));
            return;
        }

        $result = $this->KoordinatorTA_model->update_approval_koor_ajax($nim, $status, $catatan, $pembimbing_1, $pembimbing_2);
        echo json_encode($result);
    }

    // AJAX Endpoint: Realtime Live Status Polling
    public function ajax_realtime_status($nim) {
        header('Content-Type: application/json');

        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM tidak ditemukan'));
            return;
        }

        $detail = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($nim);
        if (!$detail) {
            echo json_encode(array('status' => false, 'message' => 'Data tidak ditemukan'));
            return;
        }

        $stWali  = $detail['status_approval_wali'] ?? 'Pending';
        $stAdmin = $detail['status_approval_admin'] ?? 'Pending';
        $stKoor  = $detail['status_approval_koor'] ?? 'Pending';
        $stKk    = $detail['status_approval_kk'] ?? 'Pending';

        // Hitung stage aktif secara akurat
        $activeStageNum = 1;
        $tahapTerakhir = 'Dosen Wali';

        if ($stWali === 'Approved') {
            $activeStageNum = 2;
            $tahapTerakhir = 'Admin Layanan';
            if ($stAdmin === 'Approved') {
                $activeStageNum = 3;
                $tahapTerakhir = 'Koordinator TA';
                if ($stKoor === 'Approved') {
                    $activeStageNum = 4;
                    $tahapTerakhir = 'Ketua KK';
                    if ($stKk === 'Approved') {
                        $activeStageNum = 5;
                        $tahapTerakhir = 'Selesai (Disetujui Semua)';
                    }
                }
            }
        }

        echo json_encode(array(
            'status'                => true,
            'nim'                   => $nim,
            'status_approval_wali'  => $stWali,
            'status_approval_admin' => $stAdmin,
            'status_approval_koor'  => $stKoor,
            'status_approval_kk'    => $stKk,
            'catatan_wali'          => $detail['catatan_wali'] ?? '',
            'catatan_admin'         => $detail['catatan_admin'] ?? '',
            'catatan_koor'          => $detail['catatan_koor'] ?? '',
            'pembimbing_1'          => $detail['pembimbing_1'] ?? '',
            'pembimbing_2'          => $detail['pembimbing_2'] ?? '',
            'activeStageNum'        => $activeStageNum,
            'tahapTerakhir'         => $tahapTerakhir,
            'isWaliApproved'        => ($stWali === 'Approved'),
            'isLAAApproved'         => ($stAdmin === 'Approved'),
            'isKoorApproved'        => ($stKoor === 'Approved'),
            'isKkApproved'          => ($stKk === 'Approved')
        ));
    }
}
