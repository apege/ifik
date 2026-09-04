<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('KoordinatorTA_model');
        $this->load->helper(array('form', 'url', 'text'));
    }

    // Dashboard Koordinator TA: Daftar Mahasiswa Mendaftar Tugas Akhir, Plotting Preview 2, & Penjadwalan Sidang
    public function index() {
        $nip_koor = $this->session->userdata('nip') ? $this->session->userdata('nip') : '19800202002'; // Mock NIP Koordinator TA
        $data['title'] = 'Dashboard Koordinator TA';
        $data['nip_koor'] = $nip_koor;
        $data['list_mahasiswa'] = $this->KoordinatorTA_model->get_all_mahasiswa_ta();
        $data['list_preview2'] = $this->KoordinatorTA_model->get_all_mahasiswa_preview2();
        $data['list_sidang'] = $this->KoordinatorTA_model->get_all_mahasiswa_sidang();
        $data['dosen_list'] = $this->KoordinatorTA_model->get_dosen_list();
        $data['ruangan_list'] = $this->KoordinatorTA_model->get_available_ruangan();

        $this->load->view('koordinator_ta/dashboard', $data);
    }

    public function pengaturan_jalur() {
        $data['title'] = 'Pengaturan Jalur Sidang & Non-Sidang (Dinamis)';
        $this->load->model('Rekomendasi_model');
        $data['options'] = $this->Rekomendasi_model->get_all_options();
        $data['options_grouped'] = $this->Rekomendasi_model->get_all_options_grouped();
        $this->load->view('admin_layanan/pengaturan_jalur', $data);
    }


    // Detail Mahasiswa & Approval Koordinator TA

    public function detail_mahasiswa($nim) {
        $data['title'] = 'Detail & Approval Koordinator TA';
        $data['detail'] = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($nim);
        $data['dosen_list'] = $this->KoordinatorTA_model->get_dosen_list();
        $this->load->model('Rekomendasi_model');
        $data['latest_rekomendasi'] = $this->Rekomendasi_model->get_latest_submission($nim);



        // Fallback POST standard
        if ($this->input->post('action')) {
            $status       = $this->input->post('status'); // 'Approved' atau 'Rejected'
            $catatan      = trim($this->input->post('catatan_koor') ?? '');
            $pembimbing_1 = $this->input->post('pembimbing_1');
            $pembimbing_2 = $this->input->post('pembimbing_2');

            $res = $this->KoordinatorTA_model->update_approval_koor_ajax($nim, $status, $catatan, $pembimbing_1, $pembimbing_2);
            if ($res['status']) {
                $this->load->model('Approval_log_model');
                $mhs_name = trim(($data['detail']['nama_depan'] ?? '') . ' ' . ($data['detail']['nama_belakang'] ?? ''));
                $this->Approval_log_model->log(array(
                    'modul'       => 'Koordinator TA',
                    'ref_id'      => $nim,
                    'target_name' => $mhs_name,
                    'action'      => ($status === 'Approved') ? 'Approved' : 'Rejected',
                    'catatan'     => $catatan
                ));
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
        if ($result['status']) {
            $this->load->model('Approval_log_model');
            $detail = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($nim);
            $mhs_name = trim(($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? ''));
            $this->Approval_log_model->log(array(
                'modul'       => 'Koordinator TA',
                'ref_id'      => $nim,
                'target_name' => $mhs_name,
                'action'      => ($status === 'Approved') ? 'Approved' : 'Rejected',
                'catatan'     => $catatan
            ));
        }
        echo json_encode($result);
    }

    // AJAX Endpoint: Ambil Detail Berkas & Profil Lengkap untuk Cek Dokumen Massal
    public function ajax_get_batch_details() {
        header('Content-Type: application/json');

        $nims_raw = $this->input->post('nims');
        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);

        if (empty($nims) || !is_array($nims)) {
            echo json_encode(array('status' => false, 'message' => 'Tidak ada NIM terpilih.'));
            return;
        }

        $list = $this->KoordinatorTA_model->get_batch_details_by_nims($nims);
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

            $data[] = array(
                'nim'                   => $r['nim'],
                'nama'                  => htmlspecialchars($full_name),
                'prodi'                 => htmlspecialchars($r['prodi'] ?? 'DKV'),
                'kode_kk'               => htmlspecialchars($r['kode_kk'] ?? 'KK-VCM'),
                'judul'                 => htmlspecialchars($r['judul_1'] ?? ''),
                'status_approval_wali'  => $r['status_approval_wali'] ?? 'Pending',
                'status_approval_admin' => $r['status_approval_admin'] ?? 'Pending',
                'status_approval_koor'  => $r['status_approval_koor'] ?? 'Pending',
                'catatan_wali'          => htmlspecialchars($r['catatan_wali'] ?? ''),
                'catatan_admin'         => htmlspecialchars($r['catatan_admin'] ?? ''),
                'catatan_koor'          => htmlspecialchars($r['catatan_koor'] ?? ''),
                'pembimbing_1'          => $r['pembimbing_1'] ?? '',
                'pembimbing_2'          => $r['pembimbing_2'] ?? '',
                'current_stage'         => $r['current_stage'] ?? 'Koordinator TA',
                'files' => array(
                    'ksm'        => array('title' => 'KSM (Kartu Studi Mahasiswa)', 'name' => $r['file_ksm'] ?? 'ksm_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_ksm'] ?? ''), 'status' => $r['status_ksm'] ?? 'Valid'),
                    'transkrip'  => array('title' => 'Transkrip Nilai Akademik', 'name' => $r['file_transkrip'] ?? 'transkrip_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_transkrip'] ?? ''), 'status' => $r['status_transkrip'] ?? 'Valid'),
                    'pernyataan' => array('title' => 'Surat Pernyataan Mahasiswa', 'name' => $r['file_pernyataan'] ?? 'pernyataan_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_pernyataan'] ?? ''), 'status' => $r['status_pernyataan'] ?? 'Valid'),
                    'bebas_lab'  => array('title' => 'Surat Bebas Laboratorium & Perpus', 'name' => $r['file_bebas_lab'] ?? 'bebas_lab_' . $r['nim'] . '.pdf', 'url' => $resolve_pdf_url($r['file_bebas_lab'] ?? ''), 'status' => $r['status_bebas_lab'] ?? 'Valid')
                )
            );
        }

        echo json_encode(array('status' => true, 'data' => $data));
    }

    // AJAX Endpoint: Batch Approval Koordinator TA (Multi-Select & Per-Mahasiswa Plotting)
    public function ajax_batch_approval() {
        header('Content-Type: application/json');

        $nims_raw      = $this->input->post('nims'); // JSON string atau array
        $status        = $this->input->post('status') ?: 'Approved'; // Default: Approved
        $catatan       = trim($this->input->post('catatan_koor') ?? '');
        $pembimbing_1  = $this->input->post('pembimbing_1');
        $pembimbing_2  = $this->input->post('pembimbing_2');
        $penguji_1     = $this->input->post('penguji_1');
        $penguji_2     = $this->input->post('penguji_2');
        $plottings_raw = $this->input->post('plottings');

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);
        $plottings = is_array($plottings_raw) ? $plottings_raw : json_decode($plottings_raw ?? '[]', true);

        if (empty($nims) || !is_array($nims)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih setidaknya satu mahasiswa untuk diproses.'
            ));
            return;
        }

        $result = $this->KoordinatorTA_model->batch_approval_koor_ajax($nims, $status, $catatan, $pembimbing_1, $pembimbing_2, $penguji_1, $penguji_2, $plottings);
        if ($result['status']) {
            $this->load->model('Approval_log_model');
            foreach ($nims as $n) {
                $detail = $this->KoordinatorTA_model->get_detail_pendaftaran_mahasiswa($n);
                $mhs_name = trim(($detail['nama_depan'] ?? '') . ' ' . ($detail['nama_belakang'] ?? ''));
                $this->Approval_log_model->log(array(
                    'modul'       => 'Koordinator TA',
                    'ref_id'      => $n,
                    'target_name' => $mhs_name,
                    'action'      => ($status === 'Approved') ? 'Approved' : 'Rejected',
                    'catatan'     => $catatan
                ));
            }
        }
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
        $siapDiplotCount = 0;
        $approvedCount = 0;
        $rejectedCount = 0;
        $kkApprovedCount = 0;

        foreach ($list as $row) {
            $stKoor = $row['status_approval_koor'] ?? 'Pending';
            $stWali = $row['status_approval_wali'] ?? 'Pending';
            $stAdmin = $row['status_approval_admin'] ?? 'Pending';
            $stKk = $row['status_approval_kk'] ?? 'Pending';

            $isWaliApproved = (strcasecmp($stWali, 'Approved') === 0);
            $isAdminApproved = (strcasecmp($stAdmin, 'Approved') === 0);

            if (strcasecmp($stKoor, 'Approved') === 0) {
                $approvedCount++;
            } else if (strcasecmp($stKoor, 'Rejected') === 0) {
                $rejectedCount++;
            } else {
                if ($isWaliApproved && $isAdminApproved) {
                    $siapDiplotCount++;
                }
            }

            if (strcasecmp($stKk, 'Approved') === 0) {
                $kkApprovedCount++;
            }
        }

        echo json_encode(array(
            'status' => true,
            'data'   => $list,
            'stats'  => array(
                'total'       => $totalMhs,
                'siap_diplot' => $siapDiplotCount,
                'pending'     => $siapDiplotCount,
                'approved'    => $approvedCount,
                'rejected'    => $rejectedCount,
                'kk_approved' => $kkApprovedCount
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

    // AJAX Endpoint: Batch Update Dosen Penguji Preview 2 (Multi-Select & Per-Mahasiswa Plotting)
    public function ajax_batch_preview2_penguji() {
        header('Content-Type: application/json');

        $nims_raw      = $this->input->post('nims');
        $penguji_1     = $this->input->post('penguji_1');
        $penguji_2     = $this->input->post('penguji_2');
        $plottings_raw = $this->input->post('plottings');

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw ?? '[]', true);
        $plottings = is_array($plottings_raw) ? $plottings_raw : json_decode($plottings_raw ?? '[]', true);

        if (empty($nims) || !is_array($nims)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih mahasiswa serta tentukan Dosen Penguji 1 & 2.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->batch_penguji_preview2_ajax($nims, $penguji_1, $penguji_2, null, null, null, null, $plottings);
        echo json_encode($res);
    }

    // AJAX Endpoint: Realtime Sync Data Preview 2
    public function ajax_realtime_preview2() {
        header('Content-Type: application/json');

        $list = $this->KoordinatorTA_model->get_all_mahasiswa_preview2();

        $totalP2        = count($list);
        $pengujiLengkap = 0;
        $belumPenguji   = 0;

        foreach ($list as $row) {
            $hasP1 = !empty($row['penguji_1']);
            $hasP2 = !empty($row['penguji_2']);
            if ($hasP1 && $hasP2) {
                $pengujiLengkap++;
            } else {
                $belumPenguji++;
            }
        }

        $pctLengkap = $totalP2 > 0 ? round(($pengujiLengkap / $totalP2) * 100) : 0;
        $pctBelum   = $totalP2 > 0 ? round(($belumPenguji   / $totalP2) * 100) : 0;

        echo json_encode(array(
            'status' => true,
            'data'   => $list,
            'stats'  => array(
                'total'       => $totalP2,
                'terjadwal'   => $pengujiLengkap,
                'belum_set'   => $belumPenguji,
                'pct_lengkap' => $pctLengkap,
                'pct_belum'   => $pctBelum
            )
        ));
    }

    // AJAX Endpoint: Ambil Histori Log Perubahan TA (Pembimbing & Penguji)
    public function ajax_get_history_ta() {
        header('Content-Type: application/json');

        $kategori = $this->input->get('kategori') ?: $this->input->post('kategori');
        $nim      = $this->input->get('nim') ?: $this->input->post('nim');
        $limit    = (int)($this->input->get('limit') ?: $this->input->post('limit') ?: 100);

        $logs = $this->KoordinatorTA_model->get_history_ta($kategori, $nim, $limit);

        echo json_encode(array(
            'status'   => true,
            'kategori' => $kategori,
            'data'     => $logs
        ));
    }

    // AJAX Endpoint: Ambil Histori Log Perubahan Penugasan Dosen Penguji (Wrapper)
    public function ajax_get_history_penguji() {
        header('Content-Type: application/json');

        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        $logs = $this->KoordinatorTA_model->get_history_penguji($nim);

        echo json_encode(array(
            'status' => true,
            'data'   => $logs
        ));
    }

    // =========================================================
    // TAHAP 3: AJAX PENJADWALAN SIDANG TA & MANAJEMEN RUANGAN
    // =========================================================

    // AJAX Endpoint: Update Jadwal Sidang TA Single Mahasiswa
    public function ajax_update_jadwal_sidang() {
        header('Content-Type: application/json');

        $nim          = $this->input->post('nim');
        $tgl_sidang   = $this->input->post('tgl_sidang');
        $jam_mulai    = $this->input->post('jam_mulai_sidang');
        $jam_selesai  = $this->input->post('jam_selesai_sidang');
        $ruangan      = $this->input->post('ruangan_sidang');

        if (empty($nim) || empty($tgl_sidang) || empty($jam_mulai) || empty($ruangan)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'NIM, Tanggal Sidang, Jam Mulai, dan Ruangan Sidang wajib diisi.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->update_jadwal_sidang_ajax($nim, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Batch Update Jadwal Sidang TA (Multi-Select)
    public function ajax_batch_jadwal_sidang() {
        header('Content-Type: application/json');

        $nims_raw     = $this->input->post('nims');
        $tgl_sidang   = $this->input->post('tgl_sidang');
        $jam_mulai    = $this->input->post('jam_mulai_sidang');
        $jam_selesai  = $this->input->post('jam_selesai_sidang');
        $ruangan      = $this->input->post('ruangan_sidang');

        $nims = is_array($nims_raw) ? $nims_raw : json_decode($nims_raw, true);

        if (empty($nims) || !is_array($nims) || empty($tgl_sidang) || empty($jam_mulai) || empty($ruangan)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Pilih mahasiswa serta tentukan Tanggal, Jam, dan Ruangan Sidang.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->batch_jadwal_sidang_ajax($nims, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Realtime Sync Data Sidang TA
    public function ajax_realtime_sidang() {
        header('Content-Type: application/json');

        $list = $this->KoordinatorTA_model->get_all_mahasiswa_sidang();
        $ruangan = $this->KoordinatorTA_model->get_available_ruangan();

        $totalSidang = count($list);
        $terjadwalCount = 0;
        $belumSetCount = 0;
        $sudahDinilaiCount = 0;

        foreach ($list as $row) {
            $st = $row['status_sidang'] ?? 'Belum Dijadwalkan';
            if ($st === 'Terjadwal') $terjadwalCount++;
            else $belumSetCount++;

            $nilai = $row['nilai_akhir_sidang'] ?? null;
            if (!empty($nilai) || !empty($row['grade_sidang']) || ($row['status_kelulusan_sidang'] ?? '') === 'Lulus') {
                $sudahDinilaiCount++;
            }
        }

        echo json_encode(array(
            'status'  => true,
            'data'    => $list,
            'ruangan' => $ruangan,
            'stats'   => array(
                'total'         => $totalSidang,
                'terjadwal'     => $terjadwalCount,
                'belum_set'     => $belumSetCount,
                'sudah_dinilai' => $sudahDinilaiCount,
                'ruangan_cnt'   => count($ruangan)
            )
        ));
    }

    // AJAX Endpoint: Tambah Ruangan Sidang Baru Dinamis
    public function ajax_tambah_ruangan() {
        header('Content-Type: application/json');

        $kode_ruangan = $this->input->post('kode_ruangan');
        $nama_ruangan = $this->input->post('nama_ruangan');
        $lokasi       = $this->input->post('lokasi');
        $kapasitas    = $this->input->post('kapasitas');

        if (empty($kode_ruangan) || empty($nama_ruangan)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'Kode Ruangan dan Nama Ruangan wajib diisi.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->tambah_ruangan_ajax($kode_ruangan, $nama_ruangan, $lokasi, $kapasitas);
        echo json_encode($res);
    }

    // AJAX Endpoint: Hapus Ruangan Sidang Dinamis
    public function ajax_hapus_ruangan() {
        header('Content-Type: application/json');

        $id_ruangan = $this->input->post('id_ruangan');
        if (empty($id_ruangan)) {
            echo json_encode(array(
                'status' => false,
                'message' => 'ID Ruangan tidak ditemukan.'
            ));
            return;
        }

        $res = $this->KoordinatorTA_model->hapus_ruangan_ajax($id_ruangan);
        echo json_encode($res);
    }

    // AJAX Endpoint: Ambil Daftar Ruangan Aktif untuk Auto-Refresh Dropdown
    public function ajax_get_ruangan_list() {
        header('Content-Type: application/json');

        $ruangan = $this->KoordinatorTA_model->get_available_ruangan();
        echo json_encode(array(
            'status' => true,
            'data'   => $ruangan
        ));
    }

    // AJAX Endpoint: Simpan Penilaian Akhir Sidang TA
    public function ajax_simpan_penilaian_sidang() {
        header('Content-Type: application/json');

        $nim              = $this->input->post('nim');
        $prodi            = $this->input->post('prodi');
        $peminatan        = $this->input->post('peminatan');
        $nilai_akhir      = $this->input->post('nilai_akhir');
        $grade            = $this->input->post('grade');
        $status_kelulusan = $this->input->post('status_kelulusan');
        $detail_penilaian = $this->input->post('detail_penilaian');
        $catatan          = $this->input->post('catatan');

        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM mahasiswa wajib disertakan.'));
            return;
        }

        if (is_string($detail_penilaian)) {
            $decoded = json_decode($detail_penilaian, true);
            if (is_array($decoded)) {
                $detail_penilaian = $decoded;
            }
        }

        $res = $this->KoordinatorTA_model->simpan_penilaian_sidang_ajax(
            $nim,
            $prodi,
            $peminatan,
            $nilai_akhir,
            $grade,
            $status_kelulusan,
            $detail_penilaian,
            $catatan
        );

        echo json_encode($res);
    }

    // AJAX Endpoint: Ambil Detail Penilaian Sidang Mahasiswa
    public function ajax_get_detail_penilaian_sidang() {
        header('Content-Type: application/json');

        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        if (empty($nim)) {
            echo json_encode(array('status' => false, 'message' => 'NIM mahasiswa wajib diisi.'));
            return;
        }

        $detail = $this->KoordinatorTA_model->get_detail_penilaian_sidang($nim);
        if (!$detail) {
            echo json_encode(array('status' => false, 'message' => 'Data mahasiswa tidak ditemukan.'));
            return;
        }

        echo json_encode(array(
            'status' => true,
            'data'   => $detail
        ));
    }

    // AJAX Endpoint: Ambil Semua Master Rubrik Dinamis
    public function ajax_get_all_master_rubrik() {
        header('Content-Type: application/json');
        $rubriks = $this->KoordinatorTA_model->get_all_master_rubrik();
        echo json_encode(array(
            'status' => true,
            'data'   => $rubriks
        ));
    }

    // AJAX Endpoint: Simpan / Update Master Rubrik Dinamis
    public function ajax_simpan_master_rubrik() {
        header('Content-Type: application/json');

        $prodi        = $this->input->post('prodi');
        $peminatan    = $this->input->post('peminatan');
        $judul_rubrik = $this->input->post('judul_rubrik');
        $kriteria_raw = $this->input->post('kriteria');

        $kriteria = is_string($kriteria_raw) ? json_decode($kriteria_raw, true) : $kriteria_raw;

        $res = $this->KoordinatorTA_model->simpan_master_rubrik($prodi, $peminatan, $judul_rubrik, $kriteria);
        echo json_encode($res);
    }

    // AJAX Endpoint: Terapkan Rubrik Secara Massal
    public function ajax_terapkan_rubrik_massal() {
        header('Content-Type: application/json');

        $prodi     = $this->input->post('prodi');
        $peminatan = $this->input->post('peminatan');
        $nim_list  = $this->input->post('nim_list');

        if (is_string($nim_list)) {
            $decoded = json_decode($nim_list, true);
            if (is_array($decoded)) {
                $nim_list = $decoded;
            } else if (!empty($nim_list)) {
                $nim_list = explode(',', $nim_list);
            }
        }

        $res = $this->KoordinatorTA_model->terapkan_rubrik_massal($prodi, $peminatan, $nim_list);
        echo json_encode($res);
    }

    // AJAX Endpoint: Reset ke Master Rubrik Default
    public function ajax_reset_default_rubrik() {
        header('Content-Type: application/json');
        $this->KoordinatorTA_model->_seed_default_master_rubrik();
        $rubriks = $this->KoordinatorTA_model->get_all_master_rubrik();
        echo json_encode(array(
            'status'  => true,
            'message' => 'Seluruh master rubrik penilaian prodi & peminatan berhasil direset ke standar kurikulum default!',
            'data'    => $rubriks
        ));
    }
}

