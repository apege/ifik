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
        $search        = trim($this->input->get('q') ?? '');

        // Paging Parameters
        $per_page = (int)($this->input->get('per_page') ?: 5);
        if ($per_page < 1) $per_page = 5;

        $page = (int)($this->input->get('page') ?: 1);
        if ($page < 1) $page = 1;

        $total_rows  = $this->KetuaKK_model->get_count_mahasiswa_by_kk($id_kk, $filter_status, $search);
        $total_pages = max(1, ceil($total_rows / $per_page));

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $offset = ($page - 1) * $per_page;

        $data['title']          = 'Dashboard Ketua Kelompok Keahlian (KK)';
        $data['all_kk']         = $this->KetuaKK_model->get_all_kk();
        $data['selected_kk']    = $id_kk;
        $data['filter_status']  = $filter_status;
        $data['search']         = $search;
        $data['stats']          = $this->KetuaKK_model->get_stats($id_kk);
        
        // Paging Data
        $data['per_page']       = $per_page;
        $data['page']           = $page;
        $data['total_rows']     = $total_rows;
        $data['total_pages']    = $total_pages;
        $data['list_mahasiswa'] = $this->KetuaKK_model->get_mahasiswa_by_kk($id_kk, $filter_status, $search, $per_page, $offset);

        $this->load->view('ketua_kk/dashboard', $data);
    }

    /**
     * Endpoint Autocomplete Search JSON
     */
    public function autocomplete() {
        $term  = trim($this->input->get('q') ?? '');
        $id_kk = $this->input->get('kk') ?: 'all';

        $results = $this->KetuaKK_model->autocomplete_search($term, $id_kk);

        $output = array();
        foreach ($results as $r) {
            $is_prereq_ok = ($r['status_approval_wali'] === 'Approved' && $r['status_approval_admin'] === 'Approved' && $r['status_approval_koor'] === 'Approved');
            $output[] = array(
                'nim' => $r['nim'],
                'nama' => htmlspecialchars($r['nama_depan'] . ' ' . $r['nama_belakang']),
                'judul' => htmlspecialchars($r['judul_1']),
                'konsentrasi' => htmlspecialchars($r['konsentrasi_dkv'] ?? ''),
                'is_prereq_ok' => $is_prereq_ok
            );
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($output));
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

        if (!$is_prerequisite_met) {
            $this->session->set_flashdata('error', 'Pengajuan mahasiswa ini belum dapat dibuka/direview Ketua KK karena tahap Dosen Wali, Admin Layanan LAA, atau Koordinator TA belum disetujui!');
            redirect('ketuakk');
            return;
        }

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

        $status  = $this->input->post('status') ?: $this->input->post('action_status'); // 'Approved' atau 'Rejected'
        if (empty($status)) {
            $status = 'Approved';
        }
        
        $catatan = trim($this->input->post('catatan_kk') ?? '');

        if (!in_array($status, array('Approved', 'Rejected'))) {
            $this->session->set_flashdata('error', 'Pilih tindakan persetujuan (Setujui atau Tolak) dengan benar!');
            redirect('ketuakk/detail/' . $nim);
            return;
        }

        if ($status === 'Rejected' && empty($catatan)) {
            $this->session->set_flashdata('error', 'Catatan / alasan penolakan wajib diisi jika memilih Tolak / Minta Perubahan!');
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

    /**
     * Bulk / Batch Approval sekaligus beberapa mahasiswa oleh Ketua KK
     */
    public function submit_bulk_approval() {
        $nim_list = $this->input->post('nim_list');
        $catatan  = trim($this->input->post('catatan_kk_bulk') ?? '');

        if (empty($nim_list) || !is_array($nim_list)) {
            $this->session->set_flashdata('error', 'Pilih minimal satu mahasiswa untuk disetujui sekaligus!');
            redirect('ketuakk');
            return;
        }

        $approved_count = 0;
        foreach ($nim_list as $nim) {
            $detail = $this->KetuaKK_model->get_detail_mahasiswa($nim);
            if ($detail) {
                $is_wali_app  = ($detail['status_approval_wali'] === 'Approved');
                $is_admin_app = ($detail['status_approval_admin'] === 'Approved');
                $is_koor_app  = ($detail['status_approval_koor'] === 'Approved');

                // Hanya approve yang sudah memenuhi prasyarat
                if ($is_wali_app && $is_admin_app && $is_koor_app) {
                    $note = !empty($catatan) ? $catatan : 'Disetujui secara masif oleh Ketua KK.';
                    $this->KetuaKK_model->update_approval_kk($nim, 'Approved', $note);
                    $approved_count++;
                }
            }
        }

        if ($approved_count > 0) {
            $this->session->set_flashdata('success', "Berhasil menyetujui $approved_count mahasiswa sekaligus! Akses modul Bimbingan Tugas Akhir mereka resmi DIBUKA (Unlocked).");
        } else {
            $this->session->set_flashdata('error', 'Tidak ada mahasiswa terpilih yang memenuhi prasyarat persetujuan.');
        }

        redirect('ketuakk');
    }
}
