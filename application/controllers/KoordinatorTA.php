<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KoordinatorTA_model');
        $this->load->helper(array('form', 'url', 'text'));
    }

    // Dashboard Koordinator TA: Daftar Mahasiswa Mendaftar Tugas Akhir & Plotting Preview 2
    public function index() {
        $nip_koor = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19800202002'; // Mock NIP Koordinator TA
        $data['title'] = 'Dashboard Koordinator TA';
        $data['nip_koor'] = $nip_koor;
        $data['list_mahasiswa'] = $this->KoordinatorTA_model->get_all_mahasiswa_ta();
        $data['list_preview2'] = $this->KoordinatorTA_model->get_all_mahasiswa_preview2();
        $data['dosen_list'] = $this->KoordinatorTA_model->get_dosen_list();
        $data['ruangan_list'] = $this->KoordinatorTA_model->get_available_ruangan();

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

    // AJAX Endpoint: Batch Approval / Reject Koordinator TA (Multi-Select)
    public function ajax_batch_approval() {
        header('Content-Type: application/json');

        $nims_raw     = $this->input->post('nims'); // JSON string atau array
        $status       = $this->input->post('status'); // 'Approved' / 'Rejected'
        $catatan      = trim($this->input->post('catatan_koor') ?? '');
        $pembimbing_1 = $this->input->post('pembimbing_1');
        $pembimbing_2 = $this->input->post('pembimbing_2');
        $penguji_1    = $this->input->post('penguji_1');
        $penguji_2    = $this->input->post('penguji_2');

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);

        if (empty($nims) || !is_array($nims) || empty($status)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih setidaknya satu mahasiswa untuk diproses.'
            ));
            return;
        }

        $result = $this->KoordinatorTA_model->batch_approval_koor_ajax($nims, $status, $catatan, $pembimbing_1, $pembimbing_2, $penguji_1, $penguji_2);
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

    // AJAX Endpoint: Realtime Dashboard Sync (Background Polling for Table & Stat Cards)
    public function ajax_realtime_dashboard() {
        header('Content-Type: application/json');

        $list = $this->KoordinatorTA_model->get_all_mahasiswa_ta();

        $totalMhs = count($list);
        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($list as $row) {
            $st = $row['status_approval_koor'] ?? 'Pending';
            if ($st === 'Approved') $approvedCount++;
            else if ($st === 'Rejected') $rejectedCount++;
            else $pendingCount++;
        }

        echo json_encode(array(
            'status' => true,
            'data'   => $list,
            'stats'  => array(
                'total'    => $totalMhs,
                'pending'  => $pendingCount,
                'approved' => $approvedCount,
                'rejected' => $rejectedCount
            )
        ));
    }

    // AJAX Endpoint: Update Dosen Penguji & Jadwal Sidang Preview 2 (Single Mahasiswa)
    public function ajax_update_preview2_penguji() {
        header('Content-Type: application/json');

        $nim          = $this->input->post('nim');
        $penguji_1    = $this->input->post('penguji_1');
        $penguji_2    = $this->input->post('penguji_2');
        $tgl_sidang   = $this->input->post('tgl_sidang');
        $jam_mulai    = $this->input->post('jam_mulai_sidang');
        $jam_selesai  = $this->input->post('jam_selesai_sidang');
        $ruangan      = $this->input->post('ruangan_sidang');

        if (empty($nim) || empty($penguji_1) || empty($penguji_2)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'NIM, Dosen Penguji 1, dan Dosen Penguji 2 wajib dipilih.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->update_penguji_jadwal_preview2($nim, $penguji_1, $penguji_2, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Batch Update Dosen Penguji & Jadwal Preview 2 (Multi-Select)
    public function ajax_batch_preview2_penguji() {
        header('Content-Type: application/json');

        $nims_raw     = $this->input->post('nims');
        $penguji_1    = $this->input->post('penguji_1');
        $penguji_2    = $this->input->post('penguji_2');
        $tgl_sidang   = $this->input->post('tgl_sidang');
        $jam_mulai    = $this->input->post('jam_mulai_sidang');
        $jam_selesai  = $this->input->post('jam_selesai_sidang');
        $ruangan      = $this->input->post('ruangan_sidang');

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);

        if (empty($nims) || !is_array($nims) || empty($penguji_1) || empty($penguji_2)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih mahasiswa serta tentukan Dosen Penguji 1 & 2.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->batch_penguji_preview2_ajax($nims, $penguji_1, $penguji_2, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Realtime Sync Data Preview 2
    public function ajax_realtime_preview2() {
        header('Content-Type: application/json');

        $list = $this->KoordinatorTA_model->get_all_mahasiswa_preview2();

        $totalP2 = count($list);
        $terjadwalCount = 0;
        $pengujiSetCount = 0;
        $belumSetCount = 0;

        foreach ($list as $row) {
            $st = $row['status_preview2'] ?? 'Belum Diplot';
            if ($st === 'Terjadwal') $terjadwalCount++;
            else if ($st === 'Penguji Ditetapkan') $pengujiSetCount++;
            else $belumSetCount++;
        }

        echo json_encode(array(
            'status' => true,
            'data'   => $list,
            'stats'  => array(
                'total'       => $totalP2,
                'terjadwal'   => $terjadwalCount,
                'penguji_set' => $pengujiSetCount,
                'belum_set'   => $belumSetCount
            )
        ));
    }
}
