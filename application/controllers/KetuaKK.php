<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KetuaKK extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KetuaKK_model');
        $this->load->helper(array('form', 'url', 'text'));
        $this->load->library('session');
    }

    /**
     * Dashboard Ketua Kelompok Keahlian (KK)
     */
    public function index() {
        $id_kk         = $this->input->get('kk') ?: 'all';
        $filter_status = $this->input->get('status') ?: 'all';
        $search        = $this->input->get('q') ?: null;

        $data['title']          = 'Dashboard Ketua Kelompok Keahlian (KK)';
        $data['all_kk']         = $this->KetuaKK_model->get_all_kk();
        $data['selected_kk']    = $id_kk;
        $data['filter_status']  = $filter_status;
        $data['search']         = $search;
        $data['stats']          = $this->KetuaKK_model->get_stats($id_kk);
        $data['list_mahasiswa'] = $this->KetuaKK_model->get_mahasiswa_by_kk($id_kk, $filter_status, $search);

        $this->load->view('ketua_kk/dashboard', $data);
    }

    /**
     * Detail Mahasiswa & Approval Ketua KK
     */
    public function detail($nim) {
        $detail = $this->KetuaKK_model->get_detail_mahasiswa($nim);
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data mahasiswa tidak ditemukan!');
            redirect('ketuakk');
            return;
        }

        // Cek Prasyarat: Dosen Wali, Admin Layanan, dan Koordinator TA harus Approved
        $is_wali_app  = ($detail['status_approval_wali'] === 'Approved');
        $is_admin_app = ($detail['status_approval_admin'] === 'Approved');
        $is_koor_app  = ($detail['status_approval_koor'] === 'Approved');
        $is_prerequisite_met = ($is_wali_app && $is_admin_app && $is_koor_app);

        $data['title']               = 'Approval Kelompok Keahlian - ' . $detail['nama_depan'] . ' ' . $detail['nama_belakang'];
        $data['detail']              = $detail;
        $data['is_prerequisite_met'] = $is_prerequisite_met;

        $this->load->view('ketua_kk/detail_mahasiswa', $data);
    }

    /**
     * Proses Simpan Approval Ketua KK & Unlock Tahap Bimbingan
     */
    public function submit_approval($nim) {
        $detail = $this->KetuaKK_model->get_detail_mahasiswa($nim);
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data mahasiswa tidak ditemukan!');
            redirect('ketuakk');
            return;
        }

        $status  = $this->input->post('status'); // 'Approved' atau 'Rejected'
        $catatan = trim($this->input->post('catatan_kk') ?? '');

        // Validasi prasyarat rantai approval
        $is_wali_app  = ($detail['status_approval_wali'] === 'Approved');
        $is_admin_app = ($detail['status_approval_admin'] === 'Approved');
        $is_koor_app  = ($detail['status_approval_koor'] === 'Approved');

        if (!($is_wali_app && $is_admin_app && $is_koor_app)) {
            $this->session->set_flashdata('error', 'Gagal Approval! Tahap sebelumnya (Dosen Wali, Admin Layanan, Koordinator TA) harus berstatus Disetujui terlebih dahulu.');
            redirect('ketuakk/detail/' . $nim);
            return;
        }

        if ($status === 'Rejected' && empty($catatan)) {
            $this->session->set_flashdata('error', 'Catatan / alasan penolakan wajib diisi jika memilih Reject!');
            redirect('ketuakk/detail/' . $nim);
            return;
        }

        $this->KetuaKK_model->update_approval_kk($nim, $status, $catatan);

        if ($status === 'Approved') {
            $this->session->set_flashdata('success', 'Persetujuan Ketua KK berhasil disimpan! Akses modul Bimbingan Tugas Akhir mahasiswa resmi DIBUKA (Unlocked).');
        } else {
            $this->session->set_flashdata('success', 'Status penolakan/revisi Ketua KK berhasil diperbarui.');
        }

        redirect('ketuakk');
    }
}
