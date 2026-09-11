<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenWali extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('DosenWali_model');
        $this->load->helper(array('form', 'url'));

        // Normalisasi URL: Jika diakses lewat /dosenwali via browser (bukan AJAX), arahkan ke /dosen/wali
        if ($this->uri->segment(1) === 'dosenwali' && !$this->input->is_ajax_request()) {
            $segments = $this->uri->segment_array();
            array_shift($segments);
            $subPath = !empty($segments) ? '/' . implode('/', $segments) : '';
            redirect('dosen/wali' . $subPath, 'location', 301);
            return;
        }
    }

    private function _get_current_nip() {
        return $this->session->userdata('nidn_nim') ?: ($this->session->userdata('nip') ?: ($this->session->userdata('nim') ?: '19850101'));
    }

    // Dashboard Dosen Wali: Daftar Mahasiswa Bimbingan Akademik
    public function index() {
        $nip_dosen = $this->_get_current_nip();
        $this->load->model('AdminLayanan_model');
        $data['title'] = 'Dashboard Dosen Wali';
        $data['dosen_info'] = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $data['list_mahasiswa'] = $this->DosenWali_model->get_mahasiswa_bimbingan($nip_dosen);
        $data['syarat_berkas'] = $this->AdminLayanan_model->get_active_syarat_berkas();

        $this->load->view('dosen_wali/dashboard', $data);
    }

    private function _is_stage_locked($nim) {
        $detail = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);
        if (!$detail) return false;
        $current_stage = $detail['current_stage'] ?? 'Dosen Wali';
        $status_wali = $detail['status_approval_wali'] ?? 'Pending';
        return ($status_wali === 'Approved' || in_array($current_stage, ['Admin Layanan', 'Koordinator TA', 'Ketua KK', 'Selesai Approval']));
    }

    // Detail Mahasiswa Bimbingan & Approval
    public function detail_mahasiswa($nim) {
        $nip_dosen = $this->_get_current_nip();
        $this->load->model('AdminLayanan_model');

        $data['title']          = 'Detail Mahasiswa & Approval Pendaftaran TA';
        $data['dosen_info']     = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $data['detail']         = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['student_berkas'] = $this->AdminLayanan_model->get_student_berkas_map($nim);

        if ($this->input->post('action')) {
            if ($this->_is_stage_locked($nim)) {
                $this->session->set_flashdata('error', 'Pendaftaran mahasiswa ini telah disetujui dan saat ini berada di tahap selanjutnya, sehingga tidak dapat diubah lagi.');
                redirect('dosen/wali/detail_mahasiswa/' . $nim);
                return;
            }

            $action  = strtolower($this->input->post('action'));
            $status  = $this->input->post('status'); // 'Approved' atau 'Rejected'
            if (empty($status)) {
                $status = ($action === 'approve') ? 'Approved' : (($action === 'reject') ? 'Rejected' : 'Pending');
            }
            $catatan = trim($this->input->post('catatan_wali') ?? '');

            if ($status === 'Rejected') {
                if (empty($catatan) && empty($this->input->post('berkas_kurang')) && ($this->input->post('status_judul_jenis') !== 'Rejected')) {
                    $this->session->set_flashdata('error', 'Alasan penolakan / catatan revisi wajib diisi jika memilih Reject!');
                    redirect('dosen/wali/detail_mahasiswa/' . $nim);
                    return;
                }
                $submitted_kurang = $this->input->post('berkas_kurang') ?: array();
                $submitted_notes  = $this->input->post('catatan_berkas') ?: array();
                foreach ($submitted_kurang as $bk) {
                    if (empty(trim($submitted_notes[$bk] ?? ''))) {
                        $this->session->set_flashdata('error', 'Catatan revisi untuk setiap berkas yang ditandai Kurang/Revisi wajib diisi!');
                        redirect('dosen/wali/detail_mahasiswa/' . $nim);
                        return;
                    }
                }
                if ($this->input->post('status_judul_jenis') === 'Rejected') {
                    $note_jj = trim($this->input->post('catatan_judul_jenis') ?? '');
                    if (empty($note_jj)) {
                        $this->session->set_flashdata('error', 'Catatan revisi untuk Usulan Judul & Skema TA wajib diisi jika ditandai Kurang/Revisi!');
                        redirect('dosen/wali/detail_mahasiswa/' . $nim);
                        return;
                    }
                }
            }

            // Simpan status per berkas jika dikirim melalui form
            $berkas_valid_arr  = $this->input->post('berkas_valid') ?: array();
            $berkas_kurang_arr = $this->input->post('berkas_kurang') ?: array();
            $catatan_berkas    = $this->input->post('catatan_berkas') ?: array();
            $semua_berkas      = !empty($data['syarat_berkas']) ? array_column($data['syarat_berkas'], 'kode_berkas') : array('ksm', 'transkrip', 'pernyataan', 'bebas_lab');

            foreach ($semua_berkas as $bk) {
                if (in_array($bk, $berkas_kurang_arr)) {
                    $note = trim($catatan_berkas[$bk] ?? '');
                    $this->DosenWali_model->update_file_approval($nim, $bk, 'Rejected', $note);
                } else if (in_array($bk, $berkas_valid_arr) || $status === 'Approved') {
                    $this->DosenWali_model->update_file_approval($nim, $bk, 'Approved', '');
                }
            }

            // Simpan status usulan Judul & Skema TA (Disatukan)
            $status_judul_jenis  = $this->input->post('status_judul_jenis');
            $catatan_judul_jenis = trim($this->input->post('catatan_judul_jenis') ?? '');

            if ($status_judul_jenis === 'Approved' || $status === 'Approved') {
                $this->DosenWali_model->approve_jenis_ta($nim, 'Approved', '');
                $this->DosenWali_model->update_judul_approval($nim, 'Approved', '');
            } else if ($status_judul_jenis === 'Rejected') {
                $this->DosenWali_model->approve_jenis_ta($nim, 'Rejected', $catatan_judul_jenis);
                $this->DosenWali_model->update_judul_approval($nim, 'Rejected', $catatan_judul_jenis);
            }

            $this->DosenWali_model->update_approval_wali($nim, $status, $catatan);

            // Record Approval History Log
            $this->load->model('Approval_log_model');
            $mhs_name = trim(($data['detail']['nama_depan'] ?? '') . ' ' . ($data['detail']['nama_belakang'] ?? ''));
            $this->Approval_log_model->log(array(
                'modul'       => 'Dosen Wali',
                'ref_id'      => $nim,
                'target_name' => $mhs_name,
                'action'      => ($status === 'Approved') ? 'Approved' : 'Rejected',
                'catatan'     => $catatan
            ));

            $this->session->set_flashdata('success', 'Status approval pendaftaran TA berhasil diperbarui!');
            redirect('dosen/wali/detail_mahasiswa/' . $nim);
            return;
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

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
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

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
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

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
            return;
        }

        if ($status === 'Rejected' && empty($catatan)) {
            echo json_encode(array('success' => false, 'message' => 'Catatan revisi wajib diisi jika memilih Reject!'));
            return;
        }

        $res = $this->DosenWali_model->update_approval_wali($nim, $status, $catatan);
        if ($res) {
            $this->load->model('Approval_log_model');
            $mhs = $this->DosenWali_model->get_detail_pendaftaran_mahasiswa($nim);
            $mhs_name = trim(($mhs['nama_depan'] ?? '') . ' ' . ($mhs['nama_belakang'] ?? ''));
            $this->Approval_log_model->log(array(
                'modul'       => 'Dosen Wali',
                'ref_id'      => $nim,
                'target_name' => $mhs_name,
                'action'      => ($status === 'Approved') ? 'Approved' : 'Rejected',
                'catatan'     => $catatan
            ));

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

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
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

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
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

    // AJAX Endpoint: Update Keputusan Usulan Judul & Skema TA Sekaligus (Disatukan)
    public function update_judul_jenis_ajax() {
        $nim = $this->input->post('nim');
        $status = $this->input->post('status') ?? 'Pending';
        $catatan = trim($this->input->post('catatan') ?? '');

        if (!$nim || !$status) {
            echo json_encode(array('success' => false, 'message' => 'Parameter tidak lengkap.'));
            return;
        }

        if ($this->_is_stage_locked($nim)) {
            echo json_encode(array('success' => false, 'message' => 'Pendaftaran telah disetujui dan berada di tahap berikutnya. Perubahan tidak diizinkan.'));
            return;
        }

        $note = ($status === 'Approved') ? '' : $catatan;
        $res1 = $this->DosenWali_model->approve_jenis_ta($nim, $status, $note);
        $res2 = $this->DosenWali_model->update_judul_approval($nim, $status, $note);

        echo json_encode(array(
            'success' => ($res1 && $res2),
            'status' => $status,
            'catatan' => $note,
            'message' => 'Status Usulan Judul & Skema TA berhasil diperbarui ke ' . $status . '.'
        ));
    }

    // AJAX Endpoint: Realtime fetch daftar mahasiswa bimbingan & statistik status
    public function get_mahasiswa_ajax() {
        $nip_dosen = $this->_get_current_nip();
        $this->load->model('AdminLayanan_model');
        $active_syarat = $this->AdminLayanan_model->get_active_syarat_berkas();
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
            $item = [
                'nim'                    => $m['nim'],
                'nama'                   => $nama,
                'konsentrasi'            => $m['mhs_konsentrasi'] ?? '',
                'judul'                  => $m['judul_1'] ?? '',
                'status_approval_wali'   => $st,
                'current_stage'          => $m['current_stage'] ?? 'Dosen Wali',
                'detail_url'             => site_url('dosen/wali/detail_mahasiswa/' . $m['nim']),
                'berkas_map'             => $m['berkas_map'] ?? []
            ];

            // Pasang status & file untuk setiap berkas dinamis
            foreach ($m as $k => $v) {
                if (strpos($k, 'file_') === 0 || strpos($k, 'status_file_') === 0 || strpos($k, 'catatan_file_') === 0) {
                    $item[$k] = $v;
                }
            }

            $formattedList[] = $item;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'       => true,
                'syarat_berkas' => $active_syarat,
                'stats'         => [
                    'total'        => $totalMhs,
                    'pending'      => $pendingCount,
                    'approved'     => $approvedCount,
                    'rejected'     => $rejectedCount,
                    'approved_pct' => $totalMhs > 0 ? round(($approvedCount / $totalMhs) * 100) : 0
                ],
                'data'          => $formattedList
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
                'current_stage'        => $r['current_stage'] ?? 'Dosen Wali',
                'file_ksm'             => $r['file_ksm'] ?? '',
                'status_file_ksm'      => $r['status_file_ksm'] ?? 'Pending',
                'file_transkrip'       => $r['file_transkrip'] ?? '',
                'status_file_transkrip'=> $r['status_file_transkrip'] ?? 'Pending',
                'file_pernyataan'      => $r['file_pernyataan'] ?? '',
                'status_file_pernyataan'=> $r['status_file_pernyataan'] ?? 'Pending',
                'file_bebas_lab'       => $r['file_bebas_lab'] ?? '',
                'status_file_bebas_lab'=> $r['status_file_bebas_lab'] ?? 'Pending',
                'files' => (function() use ($r, $resolve_pdf_url) {
                    $ci =& get_instance();
                    $ci->load->model('AdminLayanan_model');
                    $active_syarat = $ci->AdminLayanan_model->get_active_syarat_berkas();
                    if (empty($active_syarat)) {
                        $active_syarat = [
                            ['kode_berkas' => 'ksm'],
                            ['kode_berkas' => 'transkrip'],
                            ['kode_berkas' => 'pernyataan'],
                            ['kode_berkas' => 'bebas_lab']
                        ];
                    }
                    $fMap = array();
                    foreach ($active_syarat as $asb) {
                        $k = $asb['kode_berkas'];
                        $fMap[$k] = array(
                            'name'   => $r['file_' . $k] ?? ($k . '_' . $r['nim'] . '.pdf'),
                            'url'    => $resolve_pdf_url($r['file_' . $k] ?? ''),
                            'status' => $r['status_file_' . $k] ?? 'Pending',
                            'note'   => (($r['status_file_' . $k] ?? '') === 'Rejected') ? htmlspecialchars($r['catatan_file_' . $k] ?? '') : ''
                        );
                    }
                    return $fMap;
                })()
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
            redirect('dosen/wali');
            return;
        }

        // Filter: hanya proses NIM yang belum locked (belum di Admin LAA ke atas)
        $valid_nims  = array();
        $locked_nims = array();
        foreach ($nims as $nim) {
            if ($this->_is_stage_locked($nim)) {
                $locked_nims[] = $nim;
            } else {
                $valid_nims[] = $nim;
            }
        }

        $msg = "Persetujuan massal berhasil diselesaikan!";

        if ($action === 'approve_all') {
            if (empty($valid_nims)) {
                $msg = "Semua mahasiswa yang dipilih sudah berada di tahap Admin LAA atau lebih lanjut, tidak ada yang diproses.";
            } else {
                $count = $this->DosenWali_model->batch_approve_wali($valid_nims);
                $msg = "Berhasil menyetujui (Approve) $count berkas pendaftaran mahasiswa sekaligus! Pengajuan otomatis diteruskan ke Admin LAA.";
                if (!empty($locked_nims)) {
                    $msg .= " (" . count($locked_nims) . " mahasiswa dilewati karena sudah di tahap selanjutnya.)";
                }
            }
            $this->session->set_flashdata('success', $msg);
        } else if ($action === 'batch_update') {
            $decisions = json_decode($this->input->post('decisions_json') ?? '[]', true);
            // Filter decisions hanya untuk valid_nims
            if (!empty($valid_nims)) {
                $decisions = array_filter($decisions, function($d) use ($valid_nims) {
                    return in_array($d['nim'] ?? '', $valid_nims);
                });
                $decisions = array_values($decisions);
            } else {
                $decisions = array();
            }

            if (empty($decisions)) {
                $msg = "Semua mahasiswa yang dipilih sudah berada di tahap Admin LAA atau lebih lanjut, tidak ada yang diproses.";
            } else {
                $result = $this->DosenWali_model->update_batch_decisions($decisions);
                $count_app = $result['approved'] ?? 0;
                $count_rej = $result['rejected'] ?? 0;
                $msg = "Persetujuan massal selesai! $count_app Mahasiswa Disetujui, $count_rej Mahasiswa Ditolak / Diberi Catatan Revisi.";
                if (!empty($locked_nims)) {
                    $msg .= " (" . count($locked_nims) . " mahasiswa dilewati karena sudah di tahap selanjutnya.)";
                }
            }
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

        redirect('dosen/wali');
    }

    /**
     * Halaman Pengaturan Tanda Tangan Digital Dosen
     */
    public function tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $dosen_info = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $tanda_tangan = $this->DosenWali_model->get_tanda_tangan($nip_dosen);

        $data = [
            'title'        => 'Pengaturan Tanda Tangan Digital Dosen',
            'dosen_info'   => $dosen_info,
            'nip'          => $nip_dosen,
            'tanda_tangan' => $tanda_tangan
        ];

        $this->load->view('dosen_wali/tanda_tangan', $data);
    }

    /**
     * Simpan Tanda Tangan Digital (via Canvas Base64 atau Upload File)
     */
    public function simpan_tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $tipe = $this->input->post('tipe'); // 'canvas' atau 'upload'

        $uploadDir = FCPATH . 'uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = '';

        if ($tipe === 'canvas') {
            $signatureData = $this->input->post('signature_data');
            if (empty($signatureData) || strpos($signatureData, 'data:image/png;base64,') !== 0) {
                $this->session->set_flashdata('error', 'Silakan goreskan tanda tangan pada canvas terlebih dahulu.');
                redirect('dosen/tanda-tangan');
                return;
            }

            // Decode base64 image
            $imageData = str_replace('data:image/png;base64,', '', $signatureData);
            $imageData = str_replace(' ', '+', $imageData);
            $decoded = base64_decode($imageData);

            if (!$decoded) {
                $this->session->set_flashdata('error', 'Format gambar tanda tangan tidak valid.');
                redirect('dosen/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_dosen);
            $filename = 'ttd_' . $cleanNip . '_' . time() . '.png';
            file_put_contents($uploadDir . $filename, $decoded);

        } else {
            // Upload file
            if (empty($_FILES['file_ttd']['name'])) {
                $this->session->set_flashdata('error', 'Pilih file gambar tanda tangan terlebih dahulu.');
                redirect('dosen/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_dosen);
            $config = [
                'upload_path'   => $uploadDir,
                'allowed_types' => 'png|jpg|jpeg',
                'max_size'      => 3072, // 3MB
                'file_name'     => 'ttd_' . $cleanNip . '_' . time()
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file_ttd')) {
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];
            } else {
                $err = $this->upload->display_errors('', '');
                $this->session->set_flashdata('error', 'Gagal mengunggah file tanda tangan: ' . $err);
                redirect('dosen/tanda-tangan');
                return;
            }
        }

        if (!empty($filename)) {
            // Hapus file tanda tangan lama jika ada
            $oldTtd = $this->DosenWali_model->get_tanda_tangan($nip_dosen);
            if (!empty($oldTtd) && file_exists($uploadDir . $oldTtd)) {
                @unlink($uploadDir . $oldTtd);
            }

            $this->DosenWali_model->save_tanda_tangan($nip_dosen, $filename);
            $this->session->set_flashdata('success', 'Tanda tangan digital Anda berhasil disimpan dan siap digunakan!');
        }

        redirect('dosen/tanda-tangan');
    }

    /**
     * Hapus Tanda Tangan Digital
     */
    public function hapus_tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $oldTtd = $this->DosenWali_model->get_tanda_tangan($nip_dosen);

        if (!empty($oldTtd)) {
            $filePath = FCPATH . 'uploads/signatures/' . $oldTtd;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $this->DosenWali_model->delete_tanda_tangan($nip_dosen);
            $this->session->set_flashdata('success', 'Tanda tangan digital berhasil dihapus.');
        }

        redirect('dosen/tanda-tangan');
    }

    /**
     * Unduh File Tanda Tangan Digital (Format PNG Transparan)
     */
    public function download_tanda_tangan() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $nip_dosen = $this->_get_current_nip();
        $dosen_info = $this->DosenWali_model->get_dosen_wali_info($nip_dosen);
        $tanda_tangan = $this->DosenWali_model->get_tanda_tangan($nip_dosen);

        if (empty($tanda_tangan)) {
            $this->session->set_flashdata('error', 'Belum ada tanda tangan yang tersimpan untuk diunduh.');
            redirect('dosen/tanda-tangan');
            return;
        }

        $filePath = FCPATH . 'uploads/signatures/' . $tanda_tangan;
        if (!file_exists($filePath)) {
            $this->session->set_flashdata('error', 'File tanda tangan fisik tidak ditemukan di server.');
            redirect('dosen/tanda-tangan');
            return;
        }

        $this->load->helper('download');
        $namaBersih = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dosen_info['nama_dosen'] ?? 'dosen');
        $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_dosen);
        $downloadName = 'TTD_' . $namaBersih . '_' . $cleanNip . '.png';

        force_download($downloadName, file_get_contents($filePath));
    }
}

