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
        $search        = trim($this->input->get('q') ?? '');

        // Paging Parameters
        $per_page = (int)($this->input->get('per_page') ?: 5);
        if ($per_page < 1) $per_page = 5;

        $page = (int)($this->input->get('page') ?: 1);
        if ($page < 1) $page = 1;

        $total_rows  = $this->AdminLayanan_model->get_count_pengajuan($filter_status, $search);
        $total_pages = max(1, ceil($total_rows / $per_page));

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $offset = ($page - 1) * $per_page;

        $data['title']          = 'Dashboard Admin Layanan (LAA)';
        $data['stats']          = $this->AdminLayanan_model->get_stats();
        $data['filter_status']  = $filter_status;
        $data['search']         = $search;

        // Paging Data
        $data['per_page']       = $per_page;
        $data['page']           = $page;
        $data['total_rows']     = $total_rows;
        $data['total_pages']    = $total_pages;
        $data['list_pengajuan'] = $this->AdminLayanan_model->get_all_pengajuan($filter_status, $search, $per_page, $offset);

        $this->load->view('admin_layanan/dashboard', $data);
    }

    /**
     * Endpoint Autocomplete Search JSON untuk Admin Layanan
     */
    public function autocomplete() {
        $term = trim($this->input->get('q') ?? '');
        $results = $this->AdminLayanan_model->autocomplete_search($term);

        $output = array();
        foreach ($results as $r) {
            $is_prereq_ok = ($r['status_approval_wali'] === 'Approved');
            $output[] = array(
                'nim' => $r['nim'],
                'nama' => htmlspecialchars(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? '')),
                'judul' => htmlspecialchars($r['judul_1'] ?? ''),
                'konsentrasi' => htmlspecialchars($r['konsentrasi_dkv'] ?? ''),
                'is_prereq_ok' => $is_prereq_ok
            );
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($output));
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

        // Cek Prasyarat Tahap 01 Dosen Wali
        if ($detail['status_approval_wali'] !== 'Approved') {
            $this->session->set_flashdata('error', 'Berkas mahasiswa ini belum dapat diverifikasi Admin LAA karena tahap Dosen Wali belum disetujui!');
            redirect('adminlayanan');
            return;
        }

        $data['title']  = 'Verifikasi Berkas Mahasiswa - ' . ($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? '');
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
        
        $semua_berkas = array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');

        // Fallback jika tombol action tidak terdeteksi (misal submit via tombol Enter)
        if (empty($action)) {
            if (!empty($berkas_kurang_arr) || !empty($catatan_admin)) {
                $action = 'reject';
            } else {
                $action = 'approve';
            }
        }

        if ($action === 'cancel_reject') {
            $this->AdminLayanan_model->reset_verifikasi_pending($nim);
            $this->session->set_flashdata('success', 'Status revisi berhasil dibatalkan. Status pengajuan mahasiswa kembali ke Pending (Dalam Verifikasi).');
        } else if ($action === 'reject') {
            if (!empty($berkas_kurang_arr)) {
                $berkas_kurang_final = array_values(array_intersect($semua_berkas, $berkas_kurang_arr));
            } else {
                $berkas_kurang_final = array_values(array_diff($semua_berkas, $berkas_valid_arr));
            }

            if (empty($catatan_admin) && empty($berkas_kurang_final)) {
                // Jika tidak ada berkas kurang dan catatan kosong, anggap membatalkan revisi yang kepencet
                $this->AdminLayanan_model->reset_verifikasi_pending($nim);
                $this->session->set_flashdata('success', 'Status revisi dibatalkan. Berkas mahasiswa dikembalikan ke status Pending (Dalam Verifikasi).');
                redirect('adminlayanan');
                return;
            }

            $berkas_kurang_json = json_encode($berkas_kurang_final);
            $this->AdminLayanan_model->update_verifikasi($nim, 'reject', $berkas_kurang_json, $catatan_admin, $berkas_valid_arr, $berkas_kurang_final);
            $this->session->set_flashdata('success', 'Status pengembalian/revisi berkas berhasil dikirimkan ke mahasiswa.');
        } else {
            $this->AdminLayanan_model->update_verifikasi($nim, 'approve', NULL, $catatan_admin);
            $this->session->set_flashdata('success', 'Verifikasi berkas mahasiswa berhasil disetujui! Pengajuan diteruskan ke Koordinator TA.');
        }

        redirect('adminlayanan');
    }

    /**
     * AJAX Endpoint: Ambil data detail beberapa mahasiswa sekaligus untuk Popup Batch
     */
    public function get_batch_details() {
        $nims = $this->input->post('nims') ?: array();
        if (empty($nims)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Tidak ada NIM terpilih.')));
            return;
        }

        $list = $this->AdminLayanan_model->get_batch_details_by_nims($nims);
        $data = array();

        $resolve_pdf_url = function($filename) {
            if (empty($filename)) {
                return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
            }
            if (strpos($filename, 'uploads/') === 0 && file_exists(FCPATH . $filename)) {
                return base_url($filename);
            }
            $sub_path = 'uploads/persyaratan_ta/' . $filename;
            if (file_exists(FCPATH . $sub_path)) {
                return base_url($sub_path);
            }
            return base_url('uploads/persyaratan_ta/Sertifikat_Massal_2026-07-07_(2).pdf');
        };

        foreach ($list as $r) {
            $full_name = trim(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? ''));
            if (empty($full_name)) $full_name = 'Mahasiswa ' . $r['nim'];

            // Parse berkas_kurang
            $raw_bk = $r['berkas_kurang'] ?? '';
            $berkas_kurang_arr = array();
            if (!empty($raw_bk)) {
                if (is_string($raw_bk) && (strpos(trim($raw_bk), '[') === 0 || strpos(trim($raw_bk), '{') === 0)) {
                    $decoded = json_decode($raw_bk, true);
                    if (is_array($decoded)) $berkas_kurang_arr = array_map('trim', $decoded);
                }
                if (empty($berkas_kurang_arr)) {
                    $parts = explode(',', $raw_bk);
                    foreach ($parts as $p) {
                        $p = trim($p, "[]\"' \t\n\r\0\x0B");
                        if ($p !== '') $berkas_kurang_arr[] = $p;
                    }
                }
            }

            $data[] = array(
                'nim'                  => $r['nim'],
                'nama'                 => htmlspecialchars($full_name),
                'prodi'                => htmlspecialchars($r['prodi'] ?? 'DKV'),
                'kode_kk'              => htmlspecialchars($r['kode_kk'] ?? 'KK-VCM'),
                'judul'                => htmlspecialchars($r['judul_1'] ?? ''),
                'status_approval_wali' => $r['status_approval_wali'] ?? 'Pending',
                'status_approval_admin'=> $r['status_approval_admin'] ?? 'Pending',
                'catatan_admin'        => htmlspecialchars($r['catatan_admin'] ?? ''),
                'berkas_kurang'        => $berkas_kurang_arr,
                'files' => array(
                    'ksm'        => array('name' => $r['file_ksm'] ?? 'ksm_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_ksm'] ?? ''), 'status' => $r['status_ksm'] ?? 'Pending'),
                    'transkrip'  => array('name' => $r['file_transkrip'] ?? 'transkrip_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_transkrip'] ?? ''), 'status' => $r['status_transkrip'] ?? 'Pending'),
                    'pernyataan' => array('name' => $r['file_pernyataan'] ?? 'pernyataan_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_pernyataan'] ?? ''), 'status' => $r['status_pernyataan'] ?? 'Pending'),
                    'bebas_lab'  => array('name' => $r['file_bebas_lab'] ?? 'bebas_lab_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_bebas_lab'] ?? ''), 'status' => $r['status_bebas_lab'] ?? 'Pending'),
                )
            );
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array('success' => true, 'data' => $data)));
    }

    /**
     * Submit Verifikasi Batch Massal untuk beberapa mahasiswa
     */
    public function submit_verifikasi_batch() {
        $action = $this->input->post('action'); // 'approve_all' atau 'batch_update'
        $nims   = $this->input->post('nims') ?: array();

        if (empty($nims)) {
            $this->session->set_flashdata('error', 'Tidak ada mahasiswa yang dipilih.');
            redirect('adminlayanan');
            return;
        }

        if ($action === 'approve_all') {
            $count = 0;
            foreach ($nims as $nim) {
                $detail = $this->AdminLayanan_model->get_detail_pengajuan($nim);
                if ($detail && ($detail['status_approval_wali'] ?? '') === 'Approved') {
                    $this->AdminLayanan_model->update_verifikasi($nim, 'approve', NULL, '');
                    $count++;
                }
            }
            $this->session->set_flashdata('success', "Berhasil menyetujui (Approve) $count berkas pendaftaran mahasiswa sekaligus!");
        } else if ($action === 'batch_update') {
            $verifications = json_decode($this->input->post('verifications_json') ?? '[]', true);
            $count_app = 0;
            $count_rej = 0;

            if (is_array($verifications)) {
                foreach ($verifications as $v) {
                    $nim = $v['nim'] ?? '';
                    if (empty($nim)) continue;
                    $act = $v['action'] ?? 'approve';
                    $catatan = trim($v['catatan_admin'] ?? '');
                    $berkas_valid = $v['berkas_valid'] ?? array();
                    $berkas_kurang = $v['berkas_kurang'] ?? array();

                    if ($act === 'approve') {
                        $this->AdminLayanan_model->update_verifikasi($nim, 'approve', NULL, $catatan);
                        $count_app++;
                    } else if ($act === 'reject') {
                        $bk_json = json_encode(array_values($berkas_kurang));
                        $this->AdminLayanan_model->update_verifikasi($nim, 'reject', $bk_json, $catatan, $berkas_valid, $berkas_kurang);
                        $count_rej++;
                    } else if ($act === 'cancel_reject') {
                        $this->AdminLayanan_model->reset_verifikasi_pending($nim);
                    }
                }
            }
            $this->session->set_flashdata('success', "Verifikasi massal selesai! $count_app Mahasiswa Disetujui, $count_rej Mahasiswa Dikembalikan untuk Revisi.");
        }

        redirect('adminlayanan');
    }
}
