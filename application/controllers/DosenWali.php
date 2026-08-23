<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('DosenWali_model');
        $this->load->helper(array('form', 'url'));
    }

    // Dashboard Dosen Wali: Daftar Mahasiswa Bimbingan Akademik
    public function index() {
        $nip_dosen = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19850101'; // Mock NIP Dosen Wali
        $data['title'] = 'Dashboard Dosen Wali';
        $data['dosen_info'] = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $data['list_mahasiswa'] = $this->DosenWali_model->get_mahasiswa_bimbingan($nip_dosen);

        $this->load->view('dosen_wali/dashboard', $data);
    }

    // Detail Mahasiswa Bimbingan & Approval
    public function detail_mahasiswa($nim) {
        $nip_dosen = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19850101';
        $data['title'] = 'Detail Mahasiswa & Approval Pendaftaran TA';
        $data['dosen_info'] = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $data['detail'] = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);

        if ($this->input->post('action')) {
            $status  = $this->input->post('status'); // 'Approved' atau 'Rejected'
            $catatan = trim($this->input->post('catatan_wali') ?? '');

            if ($status === 'Rejected' && empty($catatan)) {
                $this->session->set_flashdata('error', 'Alasan penolakan / catatan revisi wajib diisi jika memilih Reject!');
                redirect('dosenwali/detail_mahasiswa/' . $nim);
                return;
            }

            $this->DosenWali_model->update_approval_wali($nim, $status, $catatan);
            $this->session->set_flashdata('success', 'Status approval pendaftaran TA berhasil diperbarui!');
            redirect('dosenwali/detail_mahasiswa/' . $nim);
        }

        $this->load->view('dosen_wali/detail_mahasiswa', $data);
    }

    // AJAX Endpoint: Log ketika Dosen Wali membuka/melihat PDF berkas
    public function log_review_ajax() {
        $nim = $this->input->post('nim');
        $file_type = $this->input->post('file_type');

        if (!$nim || !$file_type) {
            echo json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.'));
            return;
        }

        $res = $this->DosenWali_model->log_file_review($nim, $file_type);
        echo json_encode(array(
            'success' => $res,
            'file_type' => $file_type,
            'message' => 'Berkas ' . strtoupper($file_type) . ' telah ditinjau/direview.'
        ));
    }

    // AJAX Endpoint: Update status per-file (Approved / Rejected)
    public function update_file_approval_ajax() {
        $nim = $this->input->post('nim');
        $file_type = $this->input->post('file_type');
        $status = $this->input->post('status');
        $comment = trim($this->input->post('comment') ?? '');

        if (!$nim || !$file_type || !$status) {
            echo json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.'));
            return;
        }

        $res = $this->DosenWali_model->update_file_approval($nim, $file_type, $status, $comment);
        echo json_encode(array(
            'success' => $res,
            'file_type' => $file_type,
            'status' => $status,
            'comment' => $comment,
            'message' => 'Status berkas ' . strtoupper($file_type) . ' berhasil diperbarui ke ' . $status . '.'
        ));
    }

    // AJAX Endpoint: Approve Semua / Tolak Semua Berkas
    public function approve_all_files_ajax() {
        $nim = $this->input->post('nim');
        $status = $this->input->post('status');

        if (!$nim || !$status) {
            echo json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.'));
            return;
        }

        $res = $this->DosenWali_model->update_all_files_approval($nim, $status);
        echo json_encode(array(
            'success' => $res,
            'status' => $status,
            'message' => 'Semua berkas berhasil diperbarui ke ' . $status . '.'
        ));
    }

    // AJAX Endpoint: Update keseluruhan Approval Dosen Wali
    public function ajax_approval() {
        $nim = $this->input->post('nim');
        $status = $this->input->post('status');
        $catatan = trim($this->input->post('catatan_wali') ?? '');

        if (!$nim || !$status) {
            echo json_encode(array('success' => false, 'message' => 'NIM dan Status wajib diisi!'));
            return;
        }

        if ($status === 'Rejected' && empty($catatan)) {
            echo json_encode(array('success' => false, 'message' => 'Catatan revisi wajib diisi jika memilih Reject!'));
            return;
        }

        $res = $this->DosenWali_model->update_approval_wali($nim, $status, $catatan);
        if ($res) {
            echo json_encode(array(
                'success' => true,
                'status' => $status,
                'catatan' => $catatan,
                'message' => 'Persetujuan pendaftaran TA berhasil disimpan!'
            ));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Gagal memperbarui status ke database.'));
        }
    }
}
