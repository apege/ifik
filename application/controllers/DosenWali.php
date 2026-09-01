<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('DosenWali_model');
        $this->load->helper(array('form', 'url'));
    }

    private function _get_current_nip() {
        return $this->session->userdata('nidn_nim') ?: ($this->session->userdata('nip') ?: ($this->session->userdata('nim') ?: '19850101'));
    }

    // Dashboard Dosen Wali: Daftar Mahasiswa Bimbingan Akademik
    public function index() {
        $nip_dosen = $this->_get_current_nip();
        $data['title'] = 'Dashboard Dosen Wali';
        $data['dosen_info'] = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $data['list_mahasiswa'] = $this->DosenWali_model->get_mahasiswa_bimbingan($nip_dosen);

        $this->load->view('dosen_wali/dashboard', $data);
    }

    // Detail Mahasiswa Bimbingan & Approval
    public function detail_mahasiswa($nim) {
        $nip_dosen = $this->_get_current_nip();
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

    // AJAX Endpoint: Update Keputusan Jenis TA (Status Jenis TA & Catatan/Saran)
    public function update_jenis_approval_ajax() {
        $nim = $this->input->post('nim');
        $status_jenis = $this->input->post('status_jenis_ta') ?? 'Pending';
        $catatan_jenis = trim($this->input->post('catatan_jenis_ta') ?? '');

        if (!$nim) {
            echo json_encode(array('success' => false, 'message' => 'NIM wajib diisi.'));
            return;
        }

        $res = $this->DosenWali_model->approve_jenis_ta($nim, $status_jenis, $catatan_jenis);
        echo json_encode(array(
            'success' => $res,
            'status_jenis_ta' => $status_jenis,
            'catatan_jenis_ta' => $catatan_jenis,
            'message' => 'Status Jenis TA berhasil diperbarui ke ' . $status_jenis . '.'
        ));
    }

    // AJAX Endpoint: Update Keputusan Judul TA (Status Judul & Saran/Catatan Revisi)
    public function update_judul_approval_ajax() {
        $nim = $this->input->post('nim');
        $status_judul = $this->input->post('status_judul') ?? 'Pending';
        $catatan_judul = trim($this->input->post('catatan_judul') ?? '');

        if (!$nim) {
            echo json_encode(array('success' => false, 'message' => 'NIM wajib diisi.'));
            return;
        }

        $res = $this->DosenWali_model->update_judul_approval($nim, $status_judul, $catatan_judul);
        echo json_encode(array(
            'success' => $res,
            'status_judul' => $status_judul,
            'catatan_judul' => $catatan_judul,
            'message' => 'Status usulan judul Tugas Akhir berhasil diperbarui ke ' . $status_judul . '.'
        ));
    }

    // AJAX Endpoint: Realtime fetch daftar mahasiswa bimbingan & statistik status
    public function get_mahasiswa_ajax() {
        $nip_dosen = $this->_get_current_nip();
        $list = $this->DosenWali_model->get_mahasiswa_bimbingan($nip_dosen);

        $totalMhs = count($list);
        $pendingCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;

        $formattedList = [];
        foreach ($list as $m) {
            $st = $m['status_approval_wali'] ?? 'Pending';
            if ($st === 'Approved') {
                $approvedCount++;
            } elseif ($st === 'Rejected') {
                $rejectedCount++;
            } else {
                $pendingCount++;
            }

            $nama = trim(($m['nama_depan'] ?? '') . ' ' . ($m['nama_belakang'] ?? ''));
            $formattedList[] = [
                'nim'                  => $m['nim'],
                'nama'                 => $nama,
                'judul'                => $m['judul_1'] ?? '',
                'status_approval_wali' => $st,
                'current_stage'        => $m['current_stage'] ?? 'Dosen Wali',
                'detail_url'           => site_url('dosenwali/detail_mahasiswa/' . $m['nim'])
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'stats'   => [
                    'total'    => $totalMhs,
                    'pending'  => $pendingCount,
                    'approved' => $approvedCount,
                    'rejected' => $rejectedCount,
                    'approved_pct' => $totalMhs > 0 ? round(($approvedCount / $totalMhs) * 100) : 0
                ],
                'data'    => $formattedList
            ]));
    }

    // AJAX Endpoint: Ambil data detail beberapa mahasiswa sekaligus untuk Popup Cek Masal Dosen Wali
    public function get_batch_details() {
        $nims = $this->input->post('nims') ?: array();
        if (empty($nims)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('success' => false, 'message' => 'Tidak ada NIM terpilih.')));
            return;
        }

        $list = $this->DosenWali_model->get_batch_details_by_nims($nims);
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

            $st_wali = $r['status_approval_wali'] ?? 'Pending';
            $st_jenis = $r['status_jenis_ta'] ?? 'Pending';
            $st_judul = $r['status_judul'] ?? 'Pending';

            $data[] = array(
                'nim'                  => $r['nim'],
                'nama'                 => htmlspecialchars($full_name),
                'prodi'                => htmlspecialchars($r['prodi'] ?? 'Desain Komunikasi Visual'),
                'kode_kk'              => htmlspecialchars($r['kode_kk'] ?? 'KK-VCM'),
                'nama_kk'              => htmlspecialchars($r['nama_kk'] ?? 'Visual & Communication Media'),
                'jenis_ta'             => htmlspecialchars($r['jenis_ta'] ?? 'Reguler'),
                'status_jenis_ta'      => $st_jenis,
                'catatan_jenis_ta'     => ($st_jenis === 'Rejected') ? htmlspecialchars($r['catatan_jenis_ta'] ?? '') : '',
                'judul_1'              => htmlspecialchars($r['judul_1'] ?? ''),
                'judul_2'              => htmlspecialchars($r['judul_2'] ?? ''),
                'judul_3'              => htmlspecialchars($r['judul_3'] ?? ''),
                'judul_en'             => htmlspecialchars($r['judul_en'] ?? ''),
                'status_judul'         => $st_judul,
                'catatan_judul'        => ($st_judul === 'Rejected') ? htmlspecialchars($r['catatan_judul'] ?? '') : '',
                'status_approval_wali' => $st_wali,
                'catatan_wali'         => ($st_wali === 'Rejected') ? htmlspecialchars($r['catatan_wali'] ?? '') : '',
                'files' => array(
                    'ksm'        => array(
                        'name'   => $r['file_ksm'] ?? 'ksm_' . $r['nim'] . '.pdf', 
                        'url'    => $resolve_pdf_url($r['file_ksm'] ?? ''), 
                        'status' => $r['status_file_ksm'] ?? 'Pending',
                        'note'   => (($r['status_file_ksm'] ?? '') === 'Rejected') ? htmlspecialchars($r['catatan_file_ksm'] ?? '') : ''
                    ),
                    'transkrip'  => array(
                        'name'   => $r['file_transkrip'] ?? 'transkrip_' . $r['nim'] . '.pdf', 
                        'url'    => $resolve_pdf_url($r['file_transkrip'] ?? ''), 
                        'status' => $r['status_file_transkrip'] ?? 'Pending',
                        'note'   => (($r['status_file_transkrip'] ?? '') === 'Rejected') ? htmlspecialchars($r['catatan_file_transkrip'] ?? '') : ''
                    ),
                    'pernyataan' => array(
                        'name'   => $r['file_pernyataan'] ?? 'pernyataan_' . $r['nim'] . '.pdf', 
                        'url'    => $resolve_pdf_url($r['file_pernyataan'] ?? ''), 
                        'status' => $r['status_file_pernyataan'] ?? 'Pending',
                        'note'   => (($r['status_file_pernyataan'] ?? '') === 'Rejected') ? htmlspecialchars($r['catatan_file_pernyataan'] ?? '') : ''
                    ),
                    'bebas_lab'  => array(
                        'name'   => $r['file_bebas_lab'] ?? 'bebas_lab_' . $r['nim'] . '.pdf', 
                        'url'    => $resolve_pdf_url($r['file_bebas_lab'] ?? ''), 
                        'status' => $r['status_file_bebas_lab'] ?? 'Pending',
                        'note'   => (($r['status_file_bebas_lab'] ?? '') === 'Rejected') ? htmlspecialchars($r['catatan_file_bebas_lab'] ?? '') : ''
                    ),
                )
            );
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(array('success' => true, 'data' => $data)));
    }

    // Submit Approval Batch Massal Dosen Wali
    public function submit_batch_approval() {
        $action = $this->input->post('action'); // 'approve_all' atau 'batch_update'
        $nims   = $this->input->post('nims') ?: array();

        if (empty($nims)) {
            $this->session->set_flashdata('error', 'Tidak ada mahasiswa yang dipilih.');
            redirect('dosenwali');
            return;
        }

        $msg = "Persetujuan massal berhasil diselesaikan!";

        if ($action === 'approve_all') {
            $count = $this->DosenWali_model->batch_approve_wali($nims);
            $msg = "Berhasil menyetujui (Approve) $count berkas pendaftaran mahasiswa sekaligus! Pengajuan otomatis diteruskan ke Admin LAA.";
            $this->session->set_flashdata('success', $msg);
        } else if ($action === 'batch_update') {
            $decisions = json_decode($this->input->post('decisions_json') ?? '[]', true);
            $result = $this->DosenWali_model->update_batch_decisions($decisions);
            $count_app = $result['approved'] ?? 0;
            $count_rej = $result['rejected'] ?? 0;
            $msg = "Persetujuan massal selesai! $count_app Mahasiswa Disetujui, $count_rej Mahasiswa Ditolak / Diberi Catatan Revisi.";
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

        redirect('dosenwali');
    }
}

