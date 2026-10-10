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

        foreach ($data['list_pengajuan'] as &$r) {
            $nim = $r['nim'] ?? '';
            $r['berkas_summary'] = $data['berkas_summaries'][$nim] ?? null;
        }
        unset($r);

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

        if (empty($nim) || empty($kode_berkas)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Parameter NIM atau kode berkas tidak valid.')));
            return;
        }

        $detail = $this->AdminLayanan_model->get_detail_pengajuan($nim);
        if (!$detail) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Data mahasiswa tidak ditemukan.')));
            return;
        }

        if (strcasecmp($detail['status_approval_wali'] ?? '', 'Approved') !== 0) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Mahasiswa ini belum disetujui oleh Dosen Wali.')));
            return;
        }

        // Standardize status ('Valid', 'Invalid', or 'Pending')
        $sLower = strtolower($status);
        if ($status === 'Valid' || $status === 'Approved' || $sLower === 'setujui' || $sLower === 'acc') {
            $status = 'Valid';
        } else if ($status === 'Invalid' || $status === 'Rejected' || $sLower === 'revisi' || $sLower === 'kurang') {
            $status = 'Invalid';
        } else {
            $status = 'Pending';
        }


        // Get file name
        $student_berkas_map = $this->AdminLayanan_model->get_student_berkas_map($nim);
        $file_name = $student_berkas_map[$kode_berkas]['file_name'] ?? ($detail['file_' . $kode_berkas] ?? ($kode_berkas . '_' . $nim . '.pdf'));

        // Save status directly to file_pendaftaran
        $this->AdminLayanan_model->save_student_berkas($nim, $kode_berkas, $file_name, $status, $catatan);

        // Recompute all berkas summary and sync overall status
        $sync_res = $this->AdminLayanan_model->sync_overall_status($nim, $catatan);
        $summary = $sync_res['summary'];

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

        // Handle Upload File Template jika ada
        $uploaded_template = null;
        if (!empty($_FILES['file_template']['name'])) {
            $upload_dir = FCPATH . 'uploads/templates/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $orig_ext = strtolower(pathinfo($_FILES['file_template']['name'], PATHINFO_EXTENSION));
            $allowed_exts = array('pdf', 'doc', 'docx', 'xlsx', 'xls', 'zip');

            if (!in_array($orig_ext, $allowed_exts)) {
                $this->session->set_flashdata('error', 'Format file template tidak didukung! Gunakan format .pdf, .docx, .doc, .xlsx, atau .zip.');
                redirect('adminlayanan/pengaturan_berkas');
                return;
            }

            $clean_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['file_template']['name'], PATHINFO_FILENAME));
            $safe_filename = 'template_' . $clean_name . '_' . time() . '.' . $orig_ext;
            $target_file = $upload_dir . $safe_filename;

            if (move_uploaded_file($_FILES['file_template']['tmp_name'], $target_file)) {
                $uploaded_template = $safe_filename;
            } else {
                $this->session->set_flashdata('error', 'Gagal mengunggah file template.');
                redirect('adminlayanan/pengaturan_berkas');
                return;
            }
        }

        $hapus_template = (int)($this->input->post('hapus_template') ?: 0);

        if (!empty($id)) {
            $data = [
                'nama_berkas' => $nama_berkas,
                'deskripsi' => $deskripsi,
                'is_required' => $is_required,
                'is_active' => $is_active,
                'urutan' => $urutan
            ];
            if ($uploaded_template !== null) {
                $data['file_template'] = $uploaded_template;
            } elseif ($hapus_template === 1) {
                $data['file_template'] = NULL;
            }
            $this->AdminLayanan_model->update_syarat_berkas($id, $data);
            $this->session->set_flashdata('success', 'Persyaratan berkas berhasil diperbarui!');
        } else {
            $kode_berkas = 'berkas_' . time();
            $data = [
                'kode_berkas' => $kode_berkas,
                'nama_berkas' => $nama_berkas,
                'deskripsi' => $deskripsi,
                'file_template' => $uploaded_template,
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
     * Hapus File Template dari Syarat Berkas
     */
    public function hapus_template_berkas($id) {
        $this->AdminLayanan_model->update_syarat_berkas($id, ['file_template' => NULL]);
        $this->session->set_flashdata('success', 'File template berhasil dihapus dari syarat berkas!');
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

        // Mark view_adminlaa = 1 in file_pendaftaran when Admin LAA views student's berkas
        if ($this->db->table_exists('file_pendaftaran')) {
            $target_ids = $this->AdminLayanan_model->get_student_target_ids($nim);
            if ($this->db->field_exists('view_adminlaa', 'file_pendaftaran') && !empty($target_ids)) {
                $this->db->where_in('id_mhs', $target_ids)
                         ->update('file_pendaftaran', ['view_adminlaa' => 1]);
            }
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

        if ($action_submit === 'reject') {
            $this->AdminLayanan_model->update_verifikasi($nim, 'reject', $catatan_admin, null, $berkas_valid, $berkas_kurang);
        } else {
            $this->AdminLayanan_model->update_verifikasi($nim, $action_submit ?: 'approve', $catatan_admin, null, $berkas_valid, $berkas_kurang);
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
            return $this->AdminLayanan_model->resolve_pdf_url($filename);
        };

        foreach ($list as $r) {
            $nim = $r['nim'];
            $full_name = trim(($r['nama_depan'] ?? '') . ' ' . ($r['nama_belakang'] ?? ''));
            if (empty($full_name)) $full_name = 'Mahasiswa ' . $nim;

            $student_summary = $this->AdminLayanan_model->get_student_berkas_summary($nim, $active_syarat, $r);
            $files = array();

            foreach ($student_summary['items'] as $idx => $it) {
                $kode = $it['kode'];
                $file_name = !empty($it['file_name']) ? $it['file_name'] : ($kode . '_' . $nim . '.pdf');
                $files[$kode] = array(
                    'title'       => ($idx + 1) . '. ' . $it['nama'],
                    'name'        => $file_name,
                    'url'         => $it['file_url'],
                    'status'      => $it['status'],
                    'is_required' => 1
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
                if ($detail && strcasecmp($detail['status_approval_wali'] ?? '', 'Approved') === 0) {
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

    // ==========================================
    // TICKETING MODULE ENDPOINTS
    // ==========================================

    public function ticketing() {
        $tab = $this->input->get('tab') ?: 'input';
        $data['title']      = 'Manajemen Ticketing LAA';
        $data['active_tab'] = $tab;
        $data['search']     = trim($this->input->get('q') ?? '');
        $data['tickets']    = $this->AdminLayanan_model->get_tickets($tab, $data['search']);
        $this->load->view('admin_layanan/ticketing', $data);
    }

    public function ticketing_input() {
        $data['title']      = 'Input Ticketing LAA';
        $data['active_tab'] = 'input';
        $data['search']     = '';
        $data['tickets']    = [];
        $this->load->view('admin_layanan/ticketing', $data);
    }

    public function ticketing_approval() {
        $data['title']      = 'Approval Ticketing LAA';
        $data['active_tab'] = 'approval';
        $data['search']     = trim($this->input->get('q') ?? '');
        $data['tickets']    = $this->AdminLayanan_model->get_tickets('approval', $data['search']);
        $this->load->view('admin_layanan/ticketing', $data);
    }

    public function ticketing_riwayat() {
        $data['title']      = 'Riwayat Ticketing LAA';
        $data['active_tab'] = 'riwayat';
        $data['search']     = trim($this->input->get('q') ?? '');
        $data['tickets']    = $this->AdminLayanan_model->get_tickets('riwayat', $data['search']);
        $this->load->view('admin_layanan/ticketing', $data);
    }

    public function simpan_ticket() {
        $nim_nip   = trim($this->input->post('nim_nip') ?? '');
        $nama      = trim($this->input->post('nama') ?? '');
        $email     = trim($this->input->post('email') ?? '');
        $kategori  = trim($this->input->post('kategori') ?? 'Layanan Umum');
        $perihal   = trim($this->input->post('perihal') ?? '');
        $deskripsi = trim($this->input->post('deskripsi') ?? '');
        $prioritas = trim($this->input->post('prioritas') ?? 'Normal');

        if (empty($nim_nip) || empty($nama) || empty($perihal)) {
            $this->session->set_flashdata('error', 'NIM/NIP, Nama, dan Perihal tiket wajib diisi!');
            redirect('adminlayanan/ticketing_input');
            return;
        }

        $ticket_data = [
            'ticket_number' => 'TICK-' . date('Ymd') . '-' . rand(1000, 9999),
            'nim_nip'       => $nim_nip,
            'nama'          => $nama,
            'email'         => $email,
            'kategori'      => $kategori,
            'perihal'       => $perihal,
            'deskripsi'     => $deskripsi,
            'prioritas'     => $prioritas,
            'status'        => 'Inputted'
        ];

        $this->AdminLayanan_model->save_ticket($ticket_data);
        $this->session->set_flashdata('success', 'Tiket baru ' . $ticket_data['ticket_number'] . ' berhasil dibuat!');
        redirect('adminlayanan/ticketing_riwayat');
    }

    public function update_ticket_status() {
        $id      = (int)$this->input->post('id');
        $status  = trim($this->input->post('status') ?? 'Approved');
        $catatan = trim($this->input->post('catatan') ?? '');

        if ($id <= 0) {
            $this->session->set_flashdata('error', 'ID Tiket tidak valid!');
            redirect('adminlayanan/ticketing_approval');
            return;
        }

        $this->AdminLayanan_model->update_ticket_status($id, $status, $catatan);
        $this->session->set_flashdata('success', 'Status tiket berhasil diperbarui menjadi ' . $status . '!');
        redirect('adminlayanan/ticketing_approval');
    }

    // ==========================================
    // TA MANAGEMENT ENDPOINTS
    // ==========================================

    public function lulus_sidang() {
        $data['title']   = 'Mahasiswa Sudah Lulus Sidang - Admin Layanan';
        $data['search']  = trim($this->input->get('q') ?? '');
        $data['cat']     = trim($this->input->get('cat') ?? 'query');
        $data['list']    = $this->AdminLayanan_model->get_students_lulus_sidang($data['search'], $data['cat']);
        $this->load->view('admin_layanan/lulus_sidang', $data);
    }

    public function status_peserta_ta() {
        $data['title']        = 'Status Peserta TA - Admin Layanan';
        $data['search']       = trim($this->input->get('q') ?? '');
        $data['cat']          = trim($this->input->get('cat') ?? 'query');
        $data['filter_stage'] = trim($this->input->get('stage') ?? 'all');
        $data['active_tab']   = trim($this->input->get('tab') ?? 'bimbingan'); // 'bimbingan' or 'file_ta'

        $data['syarat_berkas'] = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['list_peserta']  = $this->AdminLayanan_model->get_status_peserta_ta($data['search'], $data['filter_stage'], $data['cat']);

        $this->load->view('admin_layanan/status_peserta_ta', $data);
    }

    public function kembalikan_ke_preview3($nim) {
        if (empty($nim)) {
            $this->session->set_flashdata('error', 'NIM Mahasiswa tidak valid!');
            redirect('adminlayanan/status_peserta_ta');
            return;
        }

        $res = $this->AdminLayanan_model->revert_to_preview3($nim);
        if ($res) {
            $this->session->set_flashdata('success', 'Tahapan pendaftaran mahasiswa NIM ' . $nim . ' berhasil dikembalikan ke Preview 3!');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengembalikan tahapan mahasiswa NIM ' . $nim . '.');
        }

        redirect('adminlayanan/status_peserta_ta');
    }

    // ==========================================
    // RESET FILE TA
    // ==========================================

    /**
     * Halaman Reset File TA - reset file yang sudah diupload mahasiswa
     */
    public function reset_file_ta() {
        $search = trim($this->input->get('q') ?? '');
        $cat    = trim($this->input->get('cat') ?? 'query');
        $detail = null;
        $berkas = [];

        if (!empty($search)) {
            // Try exact NIM first
            $detail = $this->AdminLayanan_model->get_detail_pengajuan($search);
            // If not found by NIM, search by name/query
            if (!$detail) {
                $matches = $this->AdminLayanan_model->get_all_pengajuan('all', $search, 1, 0, $cat);
                if (!empty($matches)) {
                    $detail = $this->AdminLayanan_model->get_detail_pengajuan($matches[0]['nim']);
                }
            }
            if ($detail) {
                $berkas = $this->AdminLayanan_model->get_student_berkas_map($detail['nim']);
            }
        }

        $data['title']        = 'Reset File TA - Admin Layanan';
        $data['search']       = $search;
        $data['cat']          = $cat;
        $data['detail']       = $detail;
        $data['berkas']       = $berkas;
        $data['allPengajuan'] = $this->AdminLayanan_model->get_all_pengajuan('all', '', 9999, 0, 'query');

        $this->load->view('admin_layanan/reset_file_ta', $data);
    }

    /**
     * AJAX: Reset semua atau satu file berkas TA mahasiswa
     * POST: nim, kode_berkas (optional — kosong = reset semua)
     */
    public function ajax_reset_file_ta() {
        $nims_input  = $this->input->post('nims');
        $nim_single  = trim($this->input->post('nim') ?? '');
        $kode_berkas = trim($this->input->post('kode_berkas') ?? '');

        $nims = [];
        if (is_array($nims_input)) {
            $nims = array_filter(array_map('trim', $nims_input));
        } elseif (!empty($nims_input)) {
            $decoded = json_decode($nims_input, true);
            if (is_array($decoded)) {
                $nims = array_filter(array_map('trim', $decoded));
            } else {
                $nims = array_filter(array_map('trim', explode(',', $nims_input)));
            }
        } elseif (!empty($nim_single)) {
            $nims = [$nim_single];
        }

        if (empty($nims)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'NIM tidak boleh kosong.']));
            return;
        }

        $unlink_file = function($raw_name) {
            if (empty($raw_name)) return;
            $raw_name = trim($raw_name);
            $decoded = rawurldecode($raw_name);
            $base1 = basename($raw_name);
            $base2 = basename($decoded);
            $paths = [
                FCPATH . $raw_name,
                FCPATH . $decoded,
                FCPATH . 'uploads/' . $raw_name,
                FCPATH . 'uploads/' . $decoded,
                FCPATH . 'uploads/persyaratan_ta/' . $raw_name,
                FCPATH . 'uploads/persyaratan_ta/' . $decoded,
                FCPATH . 'uploads/persyaratan_ta/' . $base1,
                FCPATH . 'uploads/persyaratan_ta/' . $base2,
                FCPATH . 'uploads/preview_ta/' . $base1,
                FCPATH . 'uploads/preview_ta/' . $base2,
                FCPATH . 'uploads/sidang/' . $base1,
                FCPATH . 'uploads/sidang/' . $base2,
            ];
            foreach (array_unique($paths) as $p) {
                if (!empty($p) && file_exists($p) && is_file($p)) {
                    @unlink($p);
                }
            }
        };

        $reset_count = 0;
        foreach ($nims as $nim) {
            $clean_nim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$nim);
            $detail = $this->AdminLayanan_model->get_detail_pengajuan($clean_nim);
            $target_ids = $this->AdminLayanan_model->get_student_target_ids($clean_nim);

            $mhs_name = '';
            if ($detail) {
                $mhs_name = trim(($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? ''));
            }
            if (empty($mhs_name)) {
                $u_row = $this->db->get_where('user', ['nim' => $clean_nim])->row_array();
                if ($u_row) {
                    $mhs_name = $u_row['name'] ?? ($u_row['nama'] ?? '');
                }
            }
            if (empty($mhs_name)) {
                $m_row = $this->db->get_where('mahasiswa', ['nim' => $clean_nim])->row_array();
                if ($m_row) {
                    $mhs_name = trim(($m_row['nama_depan'] ?? '') . ' ' . ($m_row['nama_belakang'] ?? ''));
                }
            }
            if (empty($mhs_name)) {
                $mhs_name = 'Mahasiswa (' . $clean_nim . ')';
            }

            if (!empty($kode_berkas) && count($nims) === 1) {
                // -------------------------------------------------------------
                // 1. RESET 1 BERKAS SPESIFIK
                // -------------------------------------------------------------
                // Hapus berkas fisik & baris di pendaftaran_berkas
                if ($this->db->table_exists('pendaftaran_berkas')) {
                    $pb_q = $this->db->group_start()
                                         ->where('nim', $clean_nim)
                                         ->or_where_in('nim', $target_ids)
                                     ->group_end()
                                     ->group_start()
                                         ->where('kode_berkas', $kode_berkas)
                                         ->or_like('kode_berkas', $kode_berkas)
                                     ->group_end()
                                     ->get('pendaftaran_berkas');
                    if ($pb_q) {
                        foreach ($pb_q->result_array() as $r) {
                            $unlink_file($r['file_name'] ?? '');
                        }
                    }
                    $this->db->group_start()
                                 ->where('nim', $clean_nim)
                                 ->or_where_in('nim', $target_ids)
                             ->group_end()
                             ->group_start()
                                 ->where('kode_berkas', $kode_berkas)
                                 ->or_like('kode_berkas', $kode_berkas)
                             ->group_end()
                             ->delete('pendaftaran_berkas');
                }

                // Hapus berkas fisik & baris di file_pendaftaran
                if ($this->db->table_exists('file_pendaftaran')) {
                    $this->db->group_start()
                             ->where_in('id_mhs', $target_ids)
                             ->or_like('id_mhs', $clean_nim)
                             ->group_end()
                             ->group_start()
                             ->like('nama', $kode_berkas)
                             ->or_like('file', $kode_berkas)
                             ->or_like('id', $kode_berkas);
                    if ($kode_berkas === 'bebas_lab') {
                        $this->db->or_like('nama', 'bebas')
                                 ->or_like('nama', 'lab')
                                 ->or_like('file', 'bebas')
                                 ->or_like('file', 'lab');
                    } elseif ($kode_berkas === 'pernyataan') {
                        $this->db->or_like('nama', 'pernyataan')
                                 ->or_like('file', 'pernyataan');
                    } elseif ($kode_berkas === 'transkrip') {
                        $this->db->or_like('nama', 'transkrip')
                                 ->or_like('file', 'transkrip');
                    } elseif ($kode_berkas === 'ksm') {
                        $this->db->or_like('nama', 'ksm')
                                 ->or_like('file', 'ksm');
                    }
                    $this->db->group_end();
                    $fp_q = $this->db->get('file_pendaftaran');
                    if ($fp_q) {
                        foreach ($fp_q->result_array() as $r) {
                            $unlink_file($r['file'] ?? '');
                        }
                    }

                    $this->db->group_start()
                             ->where_in('id_mhs', $target_ids)
                             ->or_like('id_mhs', $clean_nim)
                             ->group_end()
                             ->group_start()
                             ->like('nama', $kode_berkas)
                             ->or_like('file', $kode_berkas)
                             ->or_like('id', $kode_berkas);
                    if ($kode_berkas === 'bebas_lab') {
                        $this->db->or_like('nama', 'bebas')
                                 ->or_like('nama', 'lab')
                                 ->or_like('file', 'bebas')
                                 ->or_like('file', 'lab');
                    } elseif ($kode_berkas === 'pernyataan') {
                        $this->db->or_like('nama', 'pernyataan')
                                 ->or_like('file', 'pernyataan');
                    } elseif ($kode_berkas === 'transkrip') {
                        $this->db->or_like('nama', 'transkrip')
                                 ->or_like('file', 'transkrip');
                    } elseif ($kode_berkas === 'ksm') {
                        $this->db->or_like('nama', 'ksm')
                                 ->or_like('file', 'ksm');
                    }
                    $this->db->group_end()
                             ->delete('file_pendaftaran');
                }

                // Kembalikan status pendaftaran ke Draft agar mahasiswa bisa mengunggah ulang file yang direset
                if ($this->db->table_exists('guidance')) {
                    $g_up = [];
                    if ($this->db->field_exists('keterangan', 'guidance')) $g_up['keterangan'] = 'Draft';
                    if (!empty($g_up)) {
                        $this->db->group_start()
                                 ->where_in('id_mhs', $target_ids)
                                 ->or_where('id', 'gdn_' . $clean_nim)
                                 ->or_like('id_mhs', $clean_nim)
                                 ->group_end()
                                 ->update('guidance', $g_up);
                    }
                }
                if ($this->db->table_exists('pendaftaran_ta')) {
                    $pt_up = [
                        'is_submitted'          => 0,
                        'status_approval_wali'  => 'Draft',
                        'current_stage'         => 'Draft',
                    ];
                    if ($this->db->field_exists('file_' . $kode_berkas, 'pendaftaran_ta')) {
                        $pt_up['file_' . $kode_berkas] = NULL;
                    }
                    if ($this->db->field_exists('status_' . $kode_berkas, 'pendaftaran_ta')) {
                        $pt_up['status_' . $kode_berkas] = 'Pending';
                    }
                    $this->db->where('nim', $clean_nim)->update('pendaftaran_ta', $pt_up);
                }

                // Sync status keseluruhan
                $this->AdminLayanan_model->sync_overall_status($clean_nim);
            } else {
                // -------------------------------------------------------------
                // 2. RESET SEMUA FILE & PENGAJUAN TA (HAPUS BERKAS, JENIS, & JUDUL)
                // -------------------------------------------------------------
                // A. Hapus SEMUA berkas dari pendaftaran_berkas (termasuk unlink file fisik)
                if ($this->db->table_exists('pendaftaran_berkas')) {
                    $pb_q = $this->db->group_start()
                                         ->where('nim', $clean_nim)
                                         ->or_where_in('nim', $target_ids)
                                     ->group_end()
                                     ->get('pendaftaran_berkas');
                    if ($pb_q) {
                        foreach ($pb_q->result_array() as $r) {
                            $unlink_file($r['file_name'] ?? '');
                        }
                    }
                    $this->db->group_start()
                                 ->where('nim', $clean_nim)
                                 ->or_where_in('nim', $target_ids)
                             ->group_end()
                             ->delete('pendaftaran_berkas');
                }

                // B. Hapus SEMUA berkas dari file_pendaftaran (termasuk unlink file fisik)
                if ($this->db->table_exists('file_pendaftaran')) {
                    $fp_q = $this->db->group_start()
                                         ->where_in('id_mhs', $target_ids)
                                         ->or_like('id_mhs', $clean_nim)
                                     ->group_end()
                                     ->get('file_pendaftaran');
                    if ($fp_q) {
                        foreach ($fp_q->result_array() as $r) {
                            $unlink_file($r['file'] ?? '');
                        }
                    }
                    $this->db->group_start()
                             ->where_in('id_mhs', $target_ids)
                             ->or_like('id_mhs', $clean_nim)
                             ->group_end()
                             ->delete('file_pendaftaran');
                }

                // C. Bersihkan tabel thesis & thesis_lecturers jika ada relasi bimbingan / preview
                $g_row = $this->db->group_start()
                                  ->where_in('id_mhs', $target_ids)
                                  ->or_where('id', 'gdn_' . $clean_nim)
                                  ->or_like('id_mhs', $clean_nim)
                                  ->group_end()
                                  ->get('guidance')->row_array();
                $gid = $g_row['id'] ?? ('gdn_' . $clean_nim);
                if ($gid && $this->db->table_exists('thesis')) {
                    $th_q = $this->db->get_where('thesis', ['id_guidance' => $gid]);
                    if ($th_q) {
                        foreach ($th_q->result_array() as $r) {
                            foreach (['pdf_file', 'file_sitasi', 'file_bimbingan', 'file_persyaratan', 'file_sidang'] as $col) {
                                if (!empty($r[$col])) $unlink_file($r[$col]);
                            }
                        }
                    }
                    $this->db->where('id_guidance', $gid)->delete('thesis');
                }
                if ($gid && $this->db->table_exists('thesis_lecturers')) {
                    $this->db->where('id_guidance', $gid)->delete('thesis_lecturers');
                }

                // D. Reset tabel guidance: Kosongkan Jenis TA, Judul 1, 2, 3, Judul EN, dan kembalikan ke Draft
                if ($this->db->table_exists('guidance')) {
                    $g_up = [];
                    if ($this->db->field_exists('keterangan', 'guidance'))          $g_up['keterangan'] = 'Draft';
                    if ($this->db->field_exists('status_file', 'guidance'))         $g_up['status_file'] = NULL;
                    if ($this->db->field_exists('status_preview', 'guidance'))      $g_up['status_preview'] = 'pending';
                    if ($this->db->field_exists('komentar', 'guidance'))            $g_up['komentar'] = NULL;
                    if ($this->db->field_exists('judul_1', 'guidance'))             $g_up['judul_1'] = NULL;
                    if ($this->db->field_exists('judul_2', 'guidance'))             $g_up['judul_2'] = NULL;
                    if ($this->db->field_exists('judul_3', 'guidance'))             $g_up['judul_3'] = NULL;
                    if ($this->db->field_exists('judul_en', 'guidance'))            $g_up['judul_en'] = NULL;
                    if ($this->db->field_exists('jenis_TA', 'guidance'))            $g_up['jenis_TA'] = NULL;
                    if ($this->db->field_exists('peminatan', 'guidance'))           $g_up['peminatan'] = NULL;
                    if ($this->db->field_exists('kelayakan', 'guidance'))           $g_up['kelayakan'] = NULL;
                    if ($this->db->field_exists('komentar_kelayakan', 'guidance'))  $g_up['komentar_kelayakan'] = NULL;
                    if ($this->db->field_exists('lulus_preview1', 'guidance'))      $g_up['lulus_preview1'] = 0;
                    if ($this->db->field_exists('lulus_preview2', 'guidance'))      $g_up['lulus_preview2'] = 0;
                    if ($this->db->field_exists('lulus_preview3', 'guidance'))      $g_up['lulus_preview3'] = 0;
                    if ($this->db->field_exists('lulus_sidang', 'guidance'))        $g_up['lulus_sidang'] = 0;
                    if ($this->db->field_exists('date', 'guidance'))                $g_up['date'] = date('Y-m-d H:i:s');
                    if ($this->db->field_exists('date_edit', 'guidance'))           $g_up['date_edit'] = date('Y-m-d H:i:s');
                    if ($this->db->field_exists('bap', 'guidance'))                 $g_up['bap'] = NULL;
                    if ($this->db->field_exists('bap2', 'guidance'))                $g_up['bap2'] = NULL;
                    if ($this->db->field_exists('status_bap', 'guidance'))          $g_up['status_bap'] = NULL;
                    if ($this->db->field_exists('status_bap2', 'guidance'))         $g_up['status_bap2'] = NULL;
                    if ($this->db->field_exists('status_filesidang', 'guidance'))   $g_up['status_filesidang'] = NULL;

                    if (!empty($g_up)) {
                        $this->db->group_start()
                                 ->where_in('id_mhs', $target_ids)
                                 ->or_where('id', 'gdn_' . $clean_nim)
                                 ->or_like('id_mhs', $clean_nim)
                                 ->group_end()
                                 ->update('guidance', $g_up);
                    }
                }

                // E. Hapus / Reset tabel pendaftaran_ta jika ada
                if ($this->db->table_exists('pendaftaran_ta')) {
                    $this->db->where('nim', $clean_nim)->or_where_in('nim', $target_ids)->delete('pendaftaran_ta');
                }

                // Sync status keseluruhan
                $this->AdminLayanan_model->sync_overall_status($clean_nim);
            }

            // Log aksi
            $this->load->model('Approval_log_model');
            $this->Approval_log_model->log([
                'modul'       => 'Admin Layanan',
                'ref_id'      => $clean_nim,
                'target_name' => $mhs_name,
                'action'      => !empty($kode_berkas) ? 'Reset File (' . strtoupper($kode_berkas) . ')' : 'Reset Pengajuan TA (Semua Berkas, Judul, & Jenis TA)',
                'catatan'     => 'Reset dilakukan oleh Admin LAA (Berkas fisik, judul, & jenis TA dibersihkan)'
            ]);

            $reset_count++;
        }

        $msg = (count($nims) > 1)
            ? 'Berhasil mereset file dan pengajuan TA dari ' . $reset_count . ' mahasiswa sekaligus!'
            : (!empty($kode_berkas) ? 'File ' . strtoupper($kode_berkas) . ' mahasiswa NIM ' . $nims[0] . ' berhasil direset.' : 'Semua file berkas, judul, dan jenis TA mahasiswa NIM ' . $nims[0] . ' berhasil direset total ke draft.');

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['success' => true, 'count' => $reset_count, 'message' => $msg]));
    }

    // =========================================================================
    // 1. PENDAFTARAN SIDANG CONTROLLER ACTIONS
    // =========================================================================

    /**
     * Halaman List Pendaftar Sidang
     */
    public function pendaftaran_sidang() {
        $search        = trim($this->input->get('q') ?? '');
        $cat           = trim($this->input->get('cat') ?? 'query');
        $filter_jenis  = trim($this->input->get('jenis') ?? 'all');   // 'all', 'sidang', 'non-sidang'
        $filter_status = trim($this->input->get('status') ?? 'all');  // 'all', 'disetujui', 'pending'

        $data['title']          = 'Pendaftaran Sidang - Admin Layanan';
        $data['search']         = $search;
        $data['cat']            = $cat;
        $data['filter_jenis']   = $filter_jenis;
        $data['filter_status']  = $filter_status;
        $data['stats']          = $this->AdminLayanan_model->get_pendaftaran_sidang_stats();
        $data['list']           = $this->AdminLayanan_model->get_pendaftaran_sidang_list($search, $filter_jenis, $filter_status, $cat);

        $this->load->view('admin_layanan/pendaftaran_sidang', $data);
    }

    /**
     * Halaman Detail & Verifikasi 7 Berkas Pendaftaran Sidang Mahasiswa
     */
    public function detail_pendaftaran_sidang($nim = '') {
        if (empty($nim)) {
            $this->session->set_flashdata('error', 'NIM mahasiswa tidak boleh kosong.');
            redirect('adminlayanan/pendaftaran_sidang');
            return;
        }

        $detail = $this->AdminLayanan_model->get_detail_pendaftaran_sidang($nim);
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data pendaftaran sidang untuk NIM ' . $nim . ' tidak ditemukan.');
            redirect('adminlayanan/pendaftaran_sidang');
            return;
        }

        $data['title']  = 'Detail Pendaftaran Sidang - ' . $detail['nama'];
        $data['detail'] = $detail;
        $data['berkas'] = $this->AdminLayanan_model->get_berkas_pendaftaran_sidang($nim);

        $this->load->view('admin_layanan/detail_pendaftaran_sidang', $data);
    }

    /**
     * AJAX: Update status verifikasi 1 berkas pendaftaran sidang
     */
    public function ajax_update_berkas_sidang() {
        $nim         = trim($this->input->post('nim') ?? '');
        $kode_berkas = trim($this->input->post('kode_berkas') ?? '');
        $status      = trim($this->input->post('status') ?? 'Disetujui Admin LAA');
        $catatan     = trim($this->input->post('catatan') ?? '');

        if (empty($nim) || empty($kode_berkas)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Parameter tidak valid.']));
            return;
        }

        $this->AdminLayanan_model->save_status_berkas_sidang($nim, $kode_berkas, $status, $catatan);

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode([
                         'success' => true,
                         'message' => 'Status berkas berhasil diupdate menjadi: ' . $status
                     ]));
    }

    /**
     * AJAX: Get all master requirements for defense registration
     */
    public function ajax_get_master_syarat_sidang() {
        $syarat = $this->AdminLayanan_model->get_all_master_syarat_sidang();
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['success' => true, 'data' => $syarat]));
    }

    /**
     * AJAX: Save or Update 1 master requirement
     */
    public function ajax_save_master_syarat_sidang() {
        $id          = $this->input->post('id');
        $nama_berkas = trim($this->input->post('nama_berkas') ?? '');
        $kode_berkas = trim($this->input->post('kode_berkas') ?? '');
        $deskripsi   = trim($this->input->post('deskripsi') ?? '');
        $is_required = (int)($this->input->post('is_required') ?? 1);
        $is_active   = (int)($this->input->post('is_active') ?? 1);
        $urutan      = (int)($this->input->post('urutan') ?? 0);

        if (empty($nama_berkas)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Nama berkas tidak boleh kosong.']));
            return;
        }

        if (empty($kode_berkas)) {
            $kode_berkas = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', trim($nama_berkas)));
        }

        $data = [
            'kode_berkas' => $kode_berkas,
            'nama_berkas' => $nama_berkas,
            'deskripsi'   => $deskripsi,
            'is_required' => $is_required,
            'is_active'   => $is_active,
            'urutan'      => $urutan
        ];

        $res = $this->AdminLayanan_model->save_master_syarat_item($id, $data);

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode([
                         'success' => true,
                         'message' => !empty($id) ? 'Master berkas berhasil diperbarui!' : 'Master berkas baru berhasil ditambahkan!',
                         'id'      => $res
                     ]));
    }

    /**
     * AJAX: Delete or toggle master requirement
     */
    public function ajax_delete_master_syarat_sidang() {
        $id = (int)$this->input->post('id');
        if (empty($id)) {
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'ID tidak valid.']));
            return;
        }

        $this->AdminLayanan_model->delete_master_syarat_item($id);
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['success' => true, 'message' => 'Master berkas berhasil dihapus.']));
    }

    /**
     * Submit Verifikasi Sidang Akhir
     */
    public function submit_approval_sidang($nim = '') {
        $status  = trim($this->input->post('status') ?? 'Disetujui Admin LAA');
        $catatan = trim($this->input->post('catatan') ?? '');

        if (!empty($nim) && $this->db->table_exists('guidance')) {
            $this->db->where('id_mhs', $nim)->or_where('id', $nim)->update('guidance', [
                'status_filesidang'           => $status,
                'komentar_pendaftaran_sidang' => $catatan
            ]);
            $this->session->set_flashdata('success', 'Status pendaftaran sidang mahasiswa NIM ' . $nim . ' berhasil diupdate!');
        }

        redirect('adminlayanan/detail_pendaftaran_sidang/' . $nim);
    }

    // =========================================================================
    // 2. LIHAT PEMBIMBING CONTROLLER ACTIONS
    // =========================================================================

    /**
     * Halaman Tabel Rekapitulasi Pembimbing
     */
    public function lihat_pembimbing() {
        $search        = trim($this->input->get('q') ?? '');
        $cat           = trim($this->input->get('cat') ?? 'query');
        $filter_prodi  = trim($this->input->get('prodi') ?? 'all');
        $filter_status = trim($this->input->get('status') ?? 'all');

        $data['title']         = 'Lihat Pembimbing Tugas Akhir - Admin Layanan';
        $data['search']        = $search;
        $data['cat']           = $cat;
        $data['filter_prodi']  = $filter_prodi;
        $data['filter_status'] = $filter_status;
        $data['list']          = $this->AdminLayanan_model->get_lihat_pembimbing_list($search, $filter_prodi, $filter_status, $cat);

        $this->load->view('admin_layanan/lihat_pembimbing', $data);
    }

    /**
     * Export Data Pembimbing ke Spreadsheet CSV (Excel Friendly)
     */
    public function export_pembimbing() {
        $list = $this->AdminLayanan_model->get_lihat_pembimbing_list('', 'all', 'all', 'query');
        
        $filename = "Rekap_Pembimbing_TA_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        // Add UTF-8 BOM for Excel compatibility
        fputs($output, "\xEF\xBB\xBF");

        // Header
        fputcsv($output, ['No', 'Nama Mahasiswa', 'NIM', 'Program Studi', 'Konsentrasi', 'Dosen Wali', 'Pembimbing 1', 'Pembimbing 2', 'Tanggal Approve', 'Status', 'Tahapan', 'Jenis TA']);

        // Data Rows
        foreach ($list as $r) {
            fputcsv($output, [
                $r['no'],
                $r['nama'],
                "'" . $r['nim'], // single quote to prevent scientific notation in Excel
                $r['prodi'],
                $r['konsentrasi'],
                $r['dosen_wali'],
                $r['pembimbing_1'],
                $r['pembimbing_2'],
                $r['tanggal_approve'],
                $r['status'],
                $r['tahapan'],
                $r['jenis_ta']
            ]);
        }

        fclose($output);
        exit();
    }

    // =========================================================================
    // 3. YUDISIUM CONTROLLER ACTIONS (S1 & S2)
    // =========================================================================

    /**
     * Halaman Rekapitulasi Yudisium
     */
    public function yudisium() {
        $search  = trim($this->input->get('q') ?? '');
        $cat     = trim($this->input->get('cat') ?? 'query');
        $jenjang = trim($this->input->get('jenjang') ?? 's1'); // 's1' or 's2'

        $data['title']       = 'Rekapitulasi Yudisium S1 & S2 - Admin Layanan';
        $data['search']      = $search;
        $data['cat']         = $cat;
        $data['jenjang']     = $jenjang;
        $data['stats']       = $this->AdminLayanan_model->get_yudisium_stats();
        $data['list']        = $this->AdminLayanan_model->get_yudisium_list($search, $jenjang, $cat);

        $this->load->view('admin_layanan/yudisium', $data);
    }

    /**
     * Export Data Yudisium ke CSV / Excel
     */
    public function export_yudisium() {
        $jenjang = trim($this->input->get('jenjang') ?? 's1');
        $list = $this->AdminLayanan_model->get_yudisium_list('', $jenjang, 'query');

        $filename = "Rekap_Yudisium_" . strtoupper($jenjang) . "_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['No', 'NIM', 'Nama Mahasiswa', 'Program Studi', 'Jenjang', 'IPK', 'Tanggal Lulus Sidang', 'Nomor SK Yudisium', 'Status Yudisium', 'Status Wisuda']);

        foreach ($list as $r) {
            fputcsv($output, [
                $r['no'],
                "'" . $r['nim'],
                $r['nama'],
                $r['prodi'],
                $r['jenjang'],
                $r['ipk'],
                $r['tanggal_lulus'],
                $r['nomor_sk'],
                $r['status_yudisium'],
                $r['status_wisuda']
            ]);
        }

        fclose($output);
        exit();
    }

    // =========================================================================
    // 4. JADWAL SIDANG CONTROLLER ACTIONS
    // =========================================================================

    /**
     * Halaman Jadwal Sidang Tugas Akhir
     */
    public function jadwal_sidang() {
        $search         = trim($this->input->get('q') ?? '');
        $cat            = trim($this->input->get('cat') ?? 'query');
        $filter_tanggal = trim($this->input->get('tanggal') ?? 'all');
        $filter_prodi   = trim($this->input->get('prodi') ?? 'all');

        $data['title']          = 'Jadwal Sidang Tugas Akhir - Admin Layanan';
        $data['search']         = $search;
        $data['cat']            = $cat;
        $data['filter_tanggal'] = $filter_tanggal;
        $data['filter_prodi']   = $filter_prodi;
        $data['list']           = $this->AdminLayanan_model->get_jadwal_sidang_list($search, $filter_tanggal, $filter_prodi, $cat);

        $this->load->view('admin_layanan/jadwal_sidang', $data);
    }

    /**
     * Export Jadwal Sidang ke Spreadsheet CSV
     */
    public function export_jadwal_sidang() {
        $list = $this->AdminLayanan_model->get_jadwal_sidang_list('', 'all', 'all', 'query');

        $filename = "Jadwal_Sidang_TA_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, ['No', 'Hari & Tanggal', 'Waktu', 'Ruangan', 'NIM', 'Nama Mahasiswa', 'Program Studi', 'Judul Tugas Akhir', 'Pembimbing 1', 'Pembimbing 2', 'Penguji 1', 'Penguji 2', 'Status Sidang']);

        foreach ($list as $r) {
            fputcsv($output, [
                $r['no'],
                $r['hari_tanggal'],
                $r['waktu'],
                $r['ruangan'],
                "'" . $r['nim'],
                $r['nama'],
                $r['prodi'],
                $r['judul'],
                $r['pembimbing_1'],
                $r['pembimbing_2'],
                $r['penguji_1'],
                $r['penguji_2'],
                $r['status_sidang']
            ]);
        }

        fclose($output);
        exit();
    }

    // =========================================================================
    // 5. BAP SIDANG CONTROLLER ACTIONS
    // =========================================================================

    /**
     * Halaman Tabel Pengajuan BAP Sidang
     */
    public function bap_sidang() {
        $search        = trim($this->input->get('q') ?? '');
        $cat           = trim($this->input->get('cat') ?? 'query');
        $filter_status = trim($this->input->get('status') ?? 'all');

        $data['title']         = 'Berita Acara Sidang (BAP) - Admin Layanan';
        $data['search']        = $search;
        $data['cat']           = $cat;
        $data['filter_status'] = $filter_status;
        $data['list']          = $this->AdminLayanan_model->get_bap_sidang_list($search, $filter_status, $cat);

        $this->load->view('admin_layanan/bap_sidang', $data);
    }

    /**
     * Preview Dokumen 1: BAP IGrACIAS (Foto 4)
     */
    public function preview_bap_igracias($nim = '') {
        if (empty($nim)) $nim = '1601200295';
        $bap = $this->AdminLayanan_model->get_detail_bap_sidang($nim);
        $data['title'] = 'BAP IGrACIAS — ' . $bap['nama'];
        $data['bap']   = $bap;
        $this->load->view('admin_layanan/template_bap_igracias', $data);
    }

    /**
     * Cetak Dokumen 1: BAP IGrACIAS (Foto 4)
     */
    public function cetak_bap_igracias($nim = '') {
        if (empty($nim)) $nim = '1601200295';
        $data['bap'] = $this->AdminLayanan_model->get_detail_bap_sidang($nim);
        $data['title'] = 'Cetak BAP IGrACIAS — ' . $data['bap']['nama'];
        $data['auto_print'] = true;
        $this->load->view('admin_layanan/template_bap_igracias', $data);
    }

    /**
     * Preview Dokumen 2: BAP Fakultas 2 Halaman (Foto 5 & 6)
     */
    public function preview_bap_fakultas($nim = '') {
        if (empty($nim)) $nim = '1601200295';
        $bap = $this->AdminLayanan_model->get_detail_bap_sidang($nim);
        $data['title'] = 'BAP Fakultas — ' . $bap['nama'];
        $data['bap']   = $bap;
        $this->load->view('admin_layanan/template_bap_fakultas', $data);
    }

    /**
     * Cetak Dokumen 2: BAP Fakultas 2 Halaman (Foto 5 & 6)
     */
    public function cetak_bap_fakultas($nim = '') {
        if (empty($nim)) $nim = '1601200295';
        $data['bap'] = $this->AdminLayanan_model->get_detail_bap_sidang($nim);
        $data['title'] = 'Cetak BAP Fakultas — ' . $data['bap']['nama'];
        $data['auto_print'] = true;
        $this->load->view('admin_layanan/template_bap_fakultas', $data);
    }

    /**
     * Alias default
     */
    public function preview_bap($nim = '') {
        $this->preview_bap_fakultas($nim);
    }

    public function cetak_bap($nim = '') {
        $this->cetak_bap_fakultas($nim);
    }

    /**
     * Routing Kelola Berita LAA -> News/newsroom
     */
    public function berita() {
        redirect('news/newsroom');
    }

    public function newsroom() {
        redirect('news/newsroom');
    }
}

