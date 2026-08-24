<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminLayanan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AdminLayanan_model');
        $this->load->helper(array('form', 'url', 'text'));
        $this->load->library('session');
    }

    /**
     * Dashboard Admin Layanan: Daftar pengajuan berkas pendaftaran TA mahasiswa
     */
    public function index() {
        $filter_status = $this->input->get('status') ?: 'all';
        $search        = $this->input->get('q') ?: null;

        $data['title']         = 'Dashboard Admin Layanan (LAA)';
        $data['stats']         = $this->AdminLayanan_model->get_stats();
        $data['filter_status'] = $filter_status;
        $data['search']        = $search;
        $data['list_pengajuan']= $this->AdminLayanan_model->get_all_pengajuan($filter_status, $search);

        $this->load->view('admin_layanan/dashboard', $data);
    }

    /**
     * Halaman Detail & Verifikasi Setiap Berkas Mahasiswa
     */
    public function detail_berkas($nim) {
        $detail = $this->AdminLayanan_model->get_detail_pengajuan($nim);
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data mahasiswa tidak ditemukan!');
            redirect('adminlayanan');
            return;
        }

        $data['title']  = 'Verifikasi Berkas Mahasiswa - ' . $detail['nama_depan'] . ' ' . $detail['nama_belakang'];
        $data['detail'] = $detail;

        $this->load->view('admin_layanan/detail_berkas', $data);
    }

    /**
     * Proses Validasi & Approval / Pengembalian Berkas ke Mahasiswa
     */
    public function submit_verifikasi($nim) {
        $detail = $this->AdminLayanan_model->get_detail_pengajuan($nim);
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data mahasiswa tidak ditemukan!');
            redirect('adminlayanan');
            return;
        }

        $action            = $this->input->post('action'); // 'approve' atau 'reject'
        $catatan_admin     = trim($this->input->post('catatan_admin') ?? '');
        
        $berkas_valid_arr  = $this->input->post('berkas_valid') ?: array();
        $berkas_kurang_arr = $this->input->post('berkas_kurang') ?: array();
        $berkas_kurang_str = !empty($berkas_kurang_arr) ? implode(',', $berkas_kurang_arr) : null;

        $get_status = function($key) use ($berkas_valid_arr, $berkas_kurang_arr) {
            if (in_array($key, $berkas_kurang_arr)) return 'Invalid';
            if (in_array($key, $berkas_valid_arr))  return 'Valid';
            return 'Pending';
        };

        if ($action === 'reject') {
            if (empty($catatan_admin)) {
                $this->session->set_flashdata('error', 'Wajib menuliskan catatan revisi/alasan pengembalian berkas!');
                redirect('adminlayanan/detail_berkas/' . $nim);
                return;
            }

            if (empty($berkas_kurang_arr)) {
                $this->session->set_flashdata('error', 'Wajib mencentang minimal 1 berkas yang kurang/perlu diperbaiki!');
                redirect('adminlayanan/detail_berkas/' . $nim);
                return;
            }

            $update_data = array(
                'status_ksm'           => $get_status('ksm'),
                'status_transkrip'     => $get_status('transkrip'),
                'status_pernyataan'    => $get_status('pernyataan'),
                'status_bebas_lab'     => $get_status('bebas_lab'),
                'berkas_kurang'        => $berkas_kurang_str,
                'status_approval_admin'=> 'Rejected',
                'catatan_admin'        => $catatan_admin,
                'current_stage'        => 'Admin Layanan'
            );

            $this->AdminLayanan_model->update_verifikasi_berkas($nim, $update_data);
            $this->session->set_flashdata('success', 'Pengajuan berhasil dikembalikan ke mahasiswa untuk perbaikan berkas.');
            redirect('adminlayanan');
            return;
        }

        if ($action === 'approve') {
            $update_data = array(
                'status_ksm'           => 'Valid',
                'status_transkrip'     => 'Valid',
                'status_pernyataan'    => 'Valid',
                'status_bebas_lab'     => 'Valid',
                'berkas_kurang'        => null,
                'status_approval_admin'=> 'Approved',
                'catatan_admin'        => $catatan_admin ?: 'Seluruh berkas persyaratan telah lengkap dan tervalidasi.',
                'current_stage'        => 'Koordinator TA'
            );

            $this->AdminLayanan_model->update_verifikasi_berkas($nim, $update_data);
            $this->session->set_flashdata('success', 'Seluruh berkas persyaratan berhasil divalidasi dan disetujui! Pengajuan diteruskan ke tahap Koordinator TA.');
            redirect('adminlayanan');
            return;
        }

        redirect('adminlayanan');
    }
}
