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
        $cat           = trim($this->input->get('cat') ?? 'query');

        // Paging Parameters
        $per_page = (int)($this->input->get('per_page') ?: 5);
        if ($per_page < 1) $per_page = 5;

        $page = (int)($this->input->get('page') ?: 1);
        if ($page < 1) $page = 1;

        $total_rows  = $this->AdminLayanan_model->get_count_pengajuan($filter_status, $search, $cat);
        $total_pages = max(1, ceil($total_rows / $per_page));

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $offset = ($page - 1) * $per_page;

        $data['title']          = 'Dashboard Admin Layanan (LAA)';
        $data['stats']          = $this->AdminLayanan_model->get_stats();
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['filter_status']  = $filter_status;
        $data['search']         = $search;
        $data['cat']            = $cat;

        // Paging Data
        $data['per_page']       = $per_page;
        $data['page']           = $page;
        $data['total_rows']     = $total_rows;
        $data['total_pages']    = $total_pages;
        $data['list_pengajuan'] = $this->AdminLayanan_model->get_all_pengajuan($filter_status, $search, $per_page, $offset, $cat);

        $data['berkas_summaries'] = $this->AdminLayanan_model->get_batch_student_berkas_summaries($data['list_pengajuan'], $data['syarat_berkas']);

        $this->load->view('admin_layanan/dashboard', $data);
    }

    /**
     * AJAX Endpoint: Get realtime table data & stats for Admin Layanan
     */
    public function ajax_get_table() {
        $filter_status = $this->input->get('status') ?: 'all';
        $search        = trim($this->input->get('q') ?? '');
        $cat           = trim($this->input->get('cat') ?? 'query');
        $per_page      = (int)($this->input->get('per_page') ?: 5);
        if ($per_page < 1) $per_page = 5;

        $page = (int)($this->input->get('page') ?: 1);
        if ($page < 1) $page = 1;

        $total_rows  = $this->AdminLayanan_model->get_count_pengajuan($filter_status, $search, $cat);
        $total_pages = max(1, ceil($total_rows / $per_page));

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $offset = ($page - 1) * $per_page;

        $stats         = $this->AdminLayanan_model->get_stats();
        $syarat_berkas = $this->AdminLayanan_model->get_active_syarat_berkas();
        $list          = $this->AdminLayanan_model->get_all_pengajuan($filter_status, $search, $per_page, $offset, $cat);

        $summaries     = $this->AdminLayanan_model->get_batch_student_berkas_summaries($list, $syarat_berkas);

        // Sanitize & format output for JSON
        $formatted_list = array();
        foreach ($list as $r) {
            $nim = $r['nim'] ?? '';
            $full_name = trim(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? ''));
            if (empty($full_name)) $full_name = 'Mahasiswa ' . $nim;

            $summary = $summaries[$nim] ?? array(
                'valid_count'   => 0,
                'invalid_count' => 0,
                'pending_count' => count($syarat_berkas),
                'total_count'   => count($syarat_berkas),
                'items'         => array()
            );

            $formatted_list[] = array(
                'nim'                  => $nim,
                'full_name'            => htmlspecialchars($full_name),
                'first_char'           => strtoupper(substr($r['nama_depan'] ?? 'M', 0, 1)),
                'prodi'                => htmlspecialchars($r['prodi'] ?? 'DKV'),
                'kode_kk'              => htmlspecialchars($r['kode_kk'] ?? 'KK-VCM'),
                'judul_1'              => htmlspecialchars($r['judul_1'] ?? ''),
                'status_ksm'           => $r['status_ksm'] ?? 'Pending',
                'status_transkrip'     => $r['status_transkrip'] ?? 'Pending',
                'status_pernyataan'    => $r['status_pernyataan'] ?? 'Pending',
                'status_bebas_lab'     => $r['status_bebas_lab'] ?? 'Pending',
                'status_approval_wali' => $r['status_approval_wali'] ?? 'Pending',
                'status_approval_admin'=> $r['status_approval_admin'] ?? 'Pending',
                'is_wali_app'          => (($r['status_approval_wali'] ?? '') === 'Approved'),
                'detail_url'           => site_url('adminlayanan/detail_berkas/' . $nim),
                'berkas_summary'       => $summary
            );
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array(
                 'success'       => true,
                 'stats'         => $stats,
                 'page'          => $page,
                 'per_page'      => $per_page,
                 'total_rows'    => $total_rows,
                 'total_pages'   => $total_pages,
                 'active_syarat' => $syarat_berkas,
                 'list'          => $formatted_list
             )));
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
     * AJAX Endpoint: Update status verifikasi 1 berkas mahasiswa langsung dari Popup Preview
     */
    public function ajax_update_single_berkas() {
        $nim         = trim($this->input->post('nim') ?? '');
        $kode_berkas = trim($this->input->post('kode_berkas') ?? '');
        $status      = trim($this->input->post('status') ?? ''); // 'Valid' atau 'Invalid'
        $catatan     = trim($this->input->post('catatan') ?? '');

        if (empty($nim) || empty($kode_berkas) || empty($status)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Parameter NIM, kode berkas, atau status tidak valid.')));
            return;
        }

        $detail = $this->AdminLayanan_model->get_detail_pengajuan($nim);
        if (!$detail) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Data mahasiswa tidak ditemukan.')));
            return;
        }

        if (($detail['status_approval_wali'] ?? '') !== 'Approved') {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Mahasiswa ini belum disetujui oleh Dosen Wali.')));
            return;
        }

        // Standardize status
        if ($status === 'Approved') $status = 'Valid';
        if ($status === 'Rejected') $status = 'Invalid';

        // Get file name
        $student_berkas_map = $this->AdminLayanan_model->get_student_berkas_map($nim);
        $file_name = $student_berkas_map[$kode_berkas]['file_name'] ?? ($detail['file_' . $kode_berkas] ?? ($kode_berkas . '_' . $nim . '.pdf'));

        // Save status in pendaftaran_berkas table
        $this->AdminLayanan_model->save_student_berkas($nim, $kode_berkas, $file_name, $status);

        // Update legacy column if exists (status_ksm, status_transkrip, etc)
        if (in_array($kode_berkas, array('ksm', 'transkrip', 'pernyataan', 'bebas_lab'))) {
            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', array('status_' . $kode_berkas => $status));
        }

        // Recompute all berkas summary for this student
        $active_syarat = $this->AdminLayanan_model->get_active_syarat_berkas();
        $summary = $this->AdminLayanan_model->get_student_berkas_summary($nim, $active_syarat);

        // If any required berkas is Invalid, update berkas_kurang or status_approval_admin
        $invalid_kodes = array();
        foreach ($summary['items'] as $it) {
            if ($it['status'] === 'Invalid') {
                $invalid_kodes[] = $it['kode'];
            }
        }

        if (!empty($invalid_kodes)) {
            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', array(
                'status_approval_admin' => 'Rejected',
                'berkas_kurang'         => json_encode($invalid_kodes),
                'catatan_admin'         => !empty($catatan) ? $catatan : ($detail['catatan_admin'] ?? 'Beberapa berkas perlu direvisi')
            ));
        } elseif ($summary['valid_count'] === $summary['total_count']) {
            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', array(
                'status_approval_admin' => 'Approved',
                'catatan_admin'         => !empty($catatan) ? $catatan : 'Seluruh berkas persyaratan telah lengkap & valid.',
                'berkas_kurang'         => NULL,
                'current_stage'         => 'Koordinator TA'
            ));
        } else {
            $this->db->where('nim', $nim);
            $this->db->update('pendaftaran_ta', array(
                'status_approval_admin' => 'Pending',
                'berkas_kurang'         => NULL,
                'current_stage'         => 'Admin Layanan'
            ));
        }

        // Log action
        $this->load->model('Approval_log_model');
        $mhs_name = trim(($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? ''));
        $this->Approval_log_model->log(array(
            'modul'       => 'Admin Layanan',
            'ref_id'      => $nim,
            'target_name' => $mhs_name,
            'action'      => 'Verifikasi Berkas (' . strtoupper($kode_berkas) . ': ' . $status . ')',
            'catatan'     => $catatan ?: 'Verifikasi langsung dari Pop-up Preview'
        ));

        // Get updated stats and berkas summary
        $stats = $this->AdminLayanan_model->get_stats();

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array(
                 'success'    => true,
                 'message'    => 'Status berkas ' . strtoupper($kode_berkas) . ' berhasil diubah menjadi ' . $status . '!',
                 'status'     => $status,
                 'summary'    => $summary,
                 'stats'      => $stats
             )));
    }

    /**
     * Halaman Pengaturan Master Syarat Berkas TA oleh LAA
     */
    public function pengaturan_berkas() {
        $data['title'] = 'Pengaturan Syarat Berkas TA - Admin Layanan';
        $data['syarat_berkas'] = $this->AdminLayanan_model->get_all_syarat_berkas();
        $this->load->view('admin_layanan/pengaturan_berkas', $data);
    }

    /**
     * Halaman Pengaturan Jalur Sidang & Non-Sidang (Dinamis)
     */
    public function pengaturan_jalur() {
        $data['title'] = 'Pengaturan Jalur Sidang & Non-Sidang (Dinamis)';
        $this->load->model('Rekomendasi_model');
        $data['options'] = $this->Rekomendasi_model->get_all_options();
        $data['options_grouped'] = $this->Rekomendasi_model->get_all_options_grouped();
        $this->load->view('admin_layanan/pengaturan_jalur', $data);
    }



    /**
     * Simpan / Tambah / Edit Syarat Berkas TA
     */
    public function simpan_syarat_berkas() {
        $id = $this->input->post('id');
        $nama_berkas = trim($this->input->post('nama_berkas') ?? '');
        $deskripsi = trim($this->input->post('deskripsi') ?? '');
        $is_required = $this->input->post('is_required') ? 1 : 0;
        $is_active = $this->input->post('is_active') ? 1 : 0;
        $urutan = (int)($this->input->post('urutan') ?: 1);

        $existing_items = $this->AdminLayanan_model->get_all_syarat_berkas();
        $total_items = count($existing_items);

        if (!empty($id)) {
            $max_allowed = max(1, $total_items);
        } else {
            $max_allowed = $total_items + 1;
        }

        if ($urutan > $max_allowed) {
            $urutan = $max_allowed;
        }
        if ($urutan < 1) {
            $urutan = 1;
        }

        if (empty($nama_berkas)) {
            $this->session->set_flashdata('error', 'Nama berkas wajib diisi!');
            redirect('adminlayanan/pengaturan_berkas');
            return;
        }

        if (!empty($id)) {
            $data = [
                'nama_berkas' => $nama_berkas,
                'deskripsi' => $deskripsi,
                'is_required' => $is_required,
                'is_active' => $is_active,
                'urutan' => $urutan
            ];
            $this->AdminLayanan_model->update_syarat_berkas($id, $data);
            $this->session->set_flashdata('success', 'Persyaratan berkas berhasil diperbarui!');
        } else {
            $kode_berkas = 'berkas_' . time();
            $data = [
                'kode_berkas' => $kode_berkas,
                'nama_berkas' => $nama_berkas,
                'deskripsi' => $deskripsi,
                'is_required' => $is_required,
                'is_active' => $is_active,
                'urutan' => $urutan
            ];
            $this->AdminLayanan_model->save_syarat_berkas($data);
            $this->session->set_flashdata('success', 'Persyaratan berkas baru berhasil ditambahkan!');
        }

        redirect('adminlayanan/pengaturan_berkas');
    }

    /**
     * Toggle Aktif / Non-Aktif Syarat Berkas
     */
    public function toggle_syarat_berkas($id) {
        $this->AdminLayanan_model->toggle_syarat_berkas($id);
        $this->session->set_flashdata('success', 'Status keaktifan berkas berhasil diubah!');
        redirect('adminlayanan/pengaturan_berkas');
    }

    /**
     * Hapus Syarat Berkas
     */
    public function hapus_syarat_berkas($id) {
        $this->AdminLayanan_model->delete_syarat_berkas($id);
        $this->session->set_flashdata('success', 'Syarat berkas berhasil dihapus!');
        redirect('adminlayanan/pengaturan_berkas');
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

        $data['title']          = 'Verifikasi Berkas Mahasiswa - ' . ($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? '');
        $data['detail']         = $detail;
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['student_berkas'] = $this->AdminLayanan_model->get_student_berkas_map($nim);

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

        $catatan_admin = trim($this->input->post('catatan_admin') ?? '');
        $action_submit = strtolower(trim($this->input->post('action_submit') ?: ($this->input->post('action') ?: '')));
        $berkas_valid  = $this->input->post('berkas_valid') ?: array();
        $berkas_kurang = $this->input->post('berkas_kurang') ?: array();

        if ($action_submit === 'approve' || $action_submit === 'approved' || (empty($berkas_kurang) && !empty($action_submit))) {
            $this->AdminLayanan_model->update_verifikasi($nim, 'Approved', $catatan_admin);
        } elseif (empty($berkas_kurang) && empty($action_submit)) {
            // Default to Approved if no berkas marked invalid/kurang
            $this->AdminLayanan_model->update_verifikasi($nim, 'Approved', $catatan_admin);
        } else {
            $this->AdminLayanan_model->update_verifikasi($nim, 'Rejected', $catatan_admin, null, $berkas_valid, $berkas_kurang);
        }

        $res = $this->AdminLayanan_model->get_detail_pengajuan($nim);

        // Record Approval History Log
        $this->load->model('Approval_log_model');
        $mhs_name = trim(($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? ''));
        $this->Approval_log_model->log(array(
            'modul'       => 'Admin Layanan',
            'ref_id'      => $nim,
            'target_name' => $mhs_name,
            'action'      => ($res['status_approval_admin'] === 'Approved') ? 'Approved' : 'Rejected',
            'catatan'     => $catatan_admin
        ));

        if ($res['status_approval_admin'] === 'Approved') {
            $this->session->set_flashdata('success', 'Pengajuan berkas mahasiswa NIM ' . $nim . ' berhasil DISETUJUI! Berkas diteruskan ke Koordinator TA.');
        } else {
            $this->session->set_flashdata('error', 'Pengajuan berkas mahasiswa NIM ' . $nim . ' DIKEMBALIKAN UNTUK REVISI. Catatan & rincian berkas kurang telah dikirim.');
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
        $active_syarat = $this->AdminLayanan_model->get_active_syarat_berkas();
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
            $nim = $r['nim'];
            $full_name = trim(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? ''));
            if (empty($full_name)) $full_name = 'Mahasiswa ' . $nim;

            $student_berkas_map = $this->AdminLayanan_model->get_student_berkas_map($nim);
            $files = array();

            foreach ($active_syarat as $idx => $sb) {
                $kode = $sb['kode_berkas'];
                $file_name = $student_berkas_map[$kode]['file_name'] ?? ($r['file_' . $kode] ?? '');
                if (empty($file_name)) {
                    $file_name = $kode . '_' . $nim . '.pdf';
                }
                $st = $student_berkas_map[$kode]['status_verifikasi'] ?? ($r['status_' . $kode] ?? 'Pending');

                $files[$kode] = array(
                    'title'       => ($idx + 1) . '. ' . $sb['nama_berkas'],
                    'name'        => $file_name,
                    'url'         => $resolve_pdf_url($file_name),
                    'status'      => $st,
                    'is_required' => $sb['is_required']
                );
            }

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
                'nim'                  => $nim,
                'nama'                 => htmlspecialchars($full_name),
                'prodi'                => htmlspecialchars($r['prodi'] ?? 'DKV'),
                'kode_kk'              => htmlspecialchars($r['kode_kk'] ?? 'KK-VCM'),
                'judul'                => htmlspecialchars($r['judul_1'] ?? ''),
                'status_approval_wali' => $r['status_approval_wali'] ?? 'Pending',
                'status_approval_admin'=> $r['status_approval_admin'] ?? 'Pending',
                'catatan_admin'        => htmlspecialchars($r['catatan_admin'] ?? ''),
                'berkas_kurang'        => $berkas_kurang_arr,
                'files'                => $files
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

        $msg = "Verifikasi massal berhasil diselesaikan!";
        if ($action === 'approve_all') {
            $count = 0;
            foreach ($nims as $nim) {
                $detail = $this->AdminLayanan_model->get_detail_pengajuan($nim);
                if ($detail && ($detail['status_approval_wali'] ?? '') === 'Approved') {
                    $this->AdminLayanan_model->update_verifikasi($nim, 'approve', NULL, '');
                    $count++;
                }
            }
            $msg = "Berhasil menyetujui (Approve) $count berkas pendaftaran mahasiswa sekaligus!";
            $this->session->set_flashdata('success', $msg);
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
            $msg = "Verifikasi massal selesai! $count_app Mahasiswa Disetujui, $count_rej Mahasiswa Dikembalikan untuk Revisi.";
            $this->session->set_flashdata('success', $msg);
        }

        if ($this->input->is_ajax_request()) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array(
                     'success' => true,
                     'message' => $msg
                 )));
            return;
        }

        redirect('adminlayanan');
    }
}
