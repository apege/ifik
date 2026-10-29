<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * =========================================================================================
 * CONTROLLER: DOSEN BIMBINGAN & EVALUASI TUGAS AKHIR (PREVIEW 1, 2, 3 & SIDANG)
 * Path: application/controllers/Dosen_bimbingan.php
 * Pengembang: Nazril Rizki
 * =========================================================================================
 * 
 * Deskripsi:
 * Controller sentral untuk seluruh proses bimbingan dan evaluasi akademik Tugas Akhir (TA).
 * Menghubungkan interaksi antara Mahasiswa bimbingan dengan 4 Tim Dosen (Pembimbing 1, Pembimbing 2,
 * Penguji 1, dan Penguji 2) sepanjang tahapan Preview 1, Preview 2 (Desk Evaluation), Preview 3,
 * Persyaratan Sidang, dan Majelis Sidang Akhir.
 * 
 * Fitur & Tanggung Jawab Utama:
 * 1. Portal Bimbingan Mahasiswa: Upload naskah bab, catatan bimbingan logbook, dan pengajuan revisi.
 * 2. Portal Evaluasi Dosen: Review berkas, persetujuan bab (ACC), pemberian catatan revisi, dan form rekomendasi.
 * 3. Evaluasi Tahap Preview 2 (Desk Evaluation / DE): Plotting jadwal presentasi dan penilaian kelayakan 4 dosen.
 * 4. Penilaian Sidang Akhir: Pengisian rubrik nilai sidang oleh 4 dosen, perhitungan skor akhir, dan status kelulusan.
 * 5. Realtime Sync (SSE): Server-Sent Events untuk pembaruan logbook bimbingan secara instan tanpa reload.
 */
class Dosen_bimbingan extends CI_Controller {

    /** Peta role UI => posisi model (1=P1, 2=P2, 3=U1, 4=U2) */
    private $role_map = ['p1' => 1, 'p2' => 2, 'u1' => 3, 'u2' => 4];

    /** Status review yang boleh disimpan oleh P1 */
    private $allowed_status = ['Approved', 'Revision', 'Pending'];

    /** Tahap yang boleh di-query dari dashboard dosen */
    private $allowed_tahap = ['Preview 1', 'Preview 2', 'Preview 3', 'Persyaratan Sidang', 'Sidang', 'Lulus'];

    public function __construct() {
        parent::__construct();
        $this->load->model('Mahasiswa_model');
        $this->load->model('Rekomendasi_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));

        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status'  => false,
                        'success' => false,
                        'message' => 'Sesi berakhir, silakan login kembali.'
                    ]));
                exit;
            }
            redirect('login');
            return;
        }
    }

    // =================================================================
    // HELPERS
    // =================================================================

    private function _get_current_nim() {
        return $this->session->userdata('nim')
            ?: ($this->session->userdata('nidn_nim')
            ?: ($this->session->userdata('username') ?: ''));
    }

    private function _is_authorized_reviewer() {
        $role_id = (int) $this->session->userdata('role_id');
        return in_array($role_id, [1, 2, 3, 5, 6, 9], true);
    }

    private function _is_admin() {
        return (int) $this->session->userdata('role_id') === 1;
    }

    private function _do_upload($field_name, $config) {
        $this->upload->initialize($config);
        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
        return null;
    }

    private function _clean_posisi($value, $default = 1) {
        $p = (int) $value;
        return ($p >= 1 && $p <= 4) ? $p : $default;
    }

    private function _posisi_from_get() {
        $raw = $this->input->get('model_posisi') ?: $this->input->get('posisi');
        return $this->_clean_posisi($raw, 1);
    }

    private function _clean_comment($html) {
        $html = (string) $html;
        return $this->security->xss_clean($html);
    }

    private function _get_reviewable_preview($id_preview, $posisi) {
        if (!$this->_is_authorized_reviewer()) return false;

        $posisi = (int) $posisi;
        if ($posisi < 1 || $posisi > 4 || empty($id_preview) || strpos((string)$id_preview, 'ps_') === 0) return false;

        $preview = $this->Mahasiswa_model->get_preview_by_id($id_preview);
        if (empty($preview)) return false;

        if ($this->_is_admin()) return $preview;

        $dosen_id = $this->session->userdata('user_id');
        $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
        $nims     = array_column($students, 'nim');

        return in_array($preview['nim'], $nims, true) ? $preview : false;
    }

    // =========================================================
    // DOMAIN 1: BIMBINGAN & PREVIEW TA (MAHASISWA)
    // =========================================================

    public function index() {
        $this->bimbingan();
    }

    public function bimbingan() {
        $nim = $this->_get_current_nim();
        $role_id = (int) $this->session->userdata('role_id');

        // Jika mahasiswa, pastikan seluruh 4 tahap approval pendaftaran TA sudah disetujui (Approved)
        if ($role_id === 4) {
            $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
            $w_app  = ($pendaftaran['status_approval_wali']  ?? '') === 'Approved';
            $a_app  = ($pendaftaran['status_approval_admin'] ?? '') === 'Approved';
            $k_app  = ($pendaftaran['status_approval_koor']  ?? '') === 'Approved';
            $kk_app = ($pendaftaran['status_approval_kk']    ?? '') === 'Approved';
            $is_full_approved = ($w_app && $a_app && $k_app && $kk_app);

            if (!$is_full_approved) {
                $this->session->set_flashdata('warning', 'Akses modul Bimbingan TA masih terkunci. Pengajuan Tugas Akhir Anda harus disetujui lengkap oleh 4 tahap (Dosen Wali, Admin Layanan, Koordinator TA, dan Ketua KK).');
                redirect('mahasiswa');
                return;
            }
        }

        $data['title']       = 'Bimbingan & Evaluasi Preview TA';
        $data['mahasiswa']   = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = !empty($pendaftaran) ? $pendaftaran : $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $pembimbing_penguji = $this->Mahasiswa_model->get_pembimbing_penguji($nim);

        $data['riwayat_preview1'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 1');
        $data['riwayat_preview2'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 2');
        $data['riwayat_preview3'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 3');
        $data['riwayat_sidang']   = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Sidang');

        $data['upload_count_p1']     = count($data['riwayat_preview1']);
        $data['upload_count_p2']     = count($data['riwayat_preview2']);
        $data['upload_count_p3']     = count($data['riwayat_preview3']);
        $data['upload_count_sidang'] = count($data['riwayat_sidang']);

        $data['latest_p1']     = $data['riwayat_preview1'][0] ?? null;
        $data['latest_p2']     = $data['riwayat_preview2'][0] ?? null;
        $data['latest_p3']     = $data['riwayat_preview3'][0] ?? null;
        $data['latest_sidang'] = $data['riwayat_sidang'][0]   ?? null;

        $data['pembimbing_1'] = !empty($pembimbing_penguji['pembimbing_1']) ? $pembimbing_penguji['pembimbing_1'] : '';
        $data['pembimbing_2'] = !empty($pembimbing_penguji['pembimbing_2']) ? $pembimbing_penguji['pembimbing_2'] : '';
        $data['penguji_ta']   = !empty($pembimbing_penguji['penguji_1'])    ? $pembimbing_penguji['penguji_1']    : '';
        $data['penguji_1']    = !empty($pembimbing_penguji['penguji_1'])    ? $pembimbing_penguji['penguji_1']    : '';
        $data['penguji_2']    = !empty($pembimbing_penguji['penguji_2'])    ? $pembimbing_penguji['penguji_2']    : '';

        $detail_penilaian = null;
        if (!empty($data['pendaftaran']['detail_penilaian_sidang'])) {
            $raw_detail = $data['pendaftaran']['detail_penilaian_sidang'];
            if (is_array($raw_detail)) {
                $detail_penilaian = $raw_detail;
            } elseif (is_string($raw_detail)) {
                $dec = json_decode($raw_detail, true);
                if (is_array($dec)) $detail_penilaian = $dec;
            }
        }
        $data['detail_penilaian']       = $detail_penilaian;
        $data['is_pembimbing_assigned'] = (!empty($data['pembimbing_1']) && !empty($data['pembimbing_2']));

        $rekap = $this->Mahasiswa_model->get_rekap_nilai_sidang($nim);
        $data['rekap_nilai_sidang'] = $rekap;

        // Data Jadwal & Lokasi Sidang Aktual (Sinkron dengan Koordinator TA)
        $jadwal_sidang = $this->Mahasiswa_model->get_jadwal_sidang($nim);
        $data['tgl_sidang']          = $jadwal_sidang['tgl_sidang'];
        $data['jam_mulai_sidang']    = $jadwal_sidang['jam_mulai_sidang'];
        $data['jam_selesai_sidang']  = $jadwal_sidang['jam_selesai_sidang'];
        $data['ruangan_sidang']      = $jadwal_sidang['ruangan_sidang'];
        $data['link_sidang']         = $jadwal_sidang['link_sidang'];
        $data['tgl_publish_sidang']  = $jadwal_sidang['tgl_publish_sidang'];
        $data['catatan_sidang']      = !empty($jadwal_sidang['catatan_sidang']) ? $jadwal_sidang['catatan_sidang'] : ($data['pendaftaran']['catatan_koor'] ?? '');
        $data['is_sidang_scheduled'] = $jadwal_sidang['is_sidang_scheduled'];

        $sp_mhs   = strtolower(trim($data['pendaftaran']['status_preview'] ?? ''));
        $kel_info = $this->Mahasiswa_model->get_kelulusan_sidang_info($nim);

        $is_published = ($sp_mhs === 'lulus' || !empty($kel_info['is_published']));
        $data['is_nilai_published'] = $is_published && !empty($rekap['semua_terisi']);
        $data['is_nilai_draft']     = !$data['is_nilai_published'] && !empty($jadwal_sidang['is_sidang_scheduled']) && (!empty($rekap['has_data']) || !empty($rekap['semua_terisi']));

        $data['nilai_akhir'] = !empty($rekap['nilai_akhir'])
            ? number_format((float)$rekap['nilai_akhir'], 2, '.', '')
            : '-';
        $data['grade_sidang']     = $rekap['grade']            ?? '-';
        $data['status_kelulusan'] = $rekap['status_kelulusan'] ?? 'Belum Dinilai';

        $data['criteria_items'] = $rekap['kriteria_referensi'] ?? [];

        $data['de_detail']           = $this->Mahasiswa_model->get_de_detail($nim);
        $data['is_de']               = !empty($data['de_detail']['is_de']);
        $data['rekomendasi']         = $this->Rekomendasi_model->get_latest_submission($nim);

        $this->load->model('AdminLayanan_model');
        $berkas_ps = $this->AdminLayanan_model->get_berkas_pendaftaran_sidang($nim);
        $ps_uploaded = 0;
        $ps_approved = 0;
        $ps_req_total = 0;
        $ps_req_approved = 0;
        $ps_revision = 0;
        foreach ($berkas_ps as $bps) {
            $is_req = !empty($bps['is_required']);
            if ($is_req) $ps_req_total++;
            if (!empty($bps['is_uploaded'])) {
                $ps_uploaded++;
            }
            if (($bps['status'] ?? '') === 'Disetujui Admin LAA') {
                $ps_approved++;
                if ($is_req) $ps_req_approved++;
            } elseif (($bps['status'] ?? '') === 'Revisi Admin LAA') {
                $ps_revision++;
            }
        }
        $fs_status = strtolower(trim($data['pendaftaran']['status_filesidang'] ?? ''));
        $sp_status = strtolower(trim($data['pendaftaran']['status_preview'] ?? ''));
        $lulus_p3_flag = (!empty($data['latest_p3']['lulus_preview3']) && (string)$data['latest_p3']['lulus_preview3'] === '1')
            || (!empty($data['pendaftaran']['lulus_preview3']) && (string)$data['pendaftaran']['lulus_preview3'] === '1');
        $is_ps_app = $lulus_p3_flag
            && !in_array($sp_status, ['preview1', 'preview2', 'preview3'], true)
            && (
                in_array($fs_status, ['disetujui admin laa', 'approved', 'disetujui', 'lulus'], true)
                || strpos($fs_status, 'disetujui') !== false
                || ($ps_uploaded > 0 && !empty($berkas_ps) && $ps_approved === count($berkas_ps))
                || ($ps_uploaded > 0 && $ps_req_total > 0 && $ps_req_approved === $ps_req_total && $ps_revision === 0)
            );
        $data['berkas_syarat_sidang'] = $berkas_ps;
        $data['upload_count_ps']      = $ps_uploaded;
        $data['is_ps_app']            = $is_ps_app;
        $data['is_ps_revision']       = ($lulus_p3_flag && !in_array($sp_status, ['preview1', 'preview2', 'preview3'], true)) && ($ps_revision > 0 || strpos($fs_status, 'revisi') !== false);

        $this->load->view('mahasiswa/bimbingan_preview1', $data);
    }

    /**
     * Halaman Utama Bimbingan Preview 1 Mahasiswa
     * 
     * Menampilkan riwayat logbook bimbingan Bab 1-3, status persetujuan Dosen Pembimbing 1 & 2,
     * form unggah naskah naskah preview 1, serta kartu konsultasi bimbingan.
     * 
     * @access public
     * @return void
     */
    public function preview1() {
        $this->bimbingan();
    }

    public function upload_preview() {
        $nim = $this->_get_current_nim();
        $tahap = $this->input->post('tahap_preview') ?: 'Preview 1';

        // Pengecekan blokir: jika sudah upload dan status masih Pending (belum ACC/Revisi), blokir upload ulang
        $latest = $this->Mahasiswa_model->get_latest_preview_status($nim, $tahap);
        if ($latest && strtolower($latest['status_pembimbing'] ?? '') === 'pending') {
            $this->session->set_flashdata('error', "Unggah berkas diblokir: Anda masih memiliki berkas {$tahap} yang sedang dalam proses peninjauan (Pending). Anda baru dapat mengunggah kembali setelah berkas disetujui (ACC) atau memerlukan revisi.");
            redirect('mahasiswa/bimbingan');
            return;
        }

        if ($tahap === 'Preview 1' && !empty($latest['lulus_preview1']) && (string)$latest['lulus_preview1'] === '1') {
            $this->session->set_flashdata('error', "Tahap Preview 1 telah selesai dan disetujui (ACC). Anda tidak dapat mengunggah ulang berkas.");
            redirect('mahasiswa/bimbingan');
            return;
        }
        if ($tahap === 'Preview 2' && !empty($latest['lulus_preview2']) && (string)$latest['lulus_preview2'] === '1') {
            $this->session->set_flashdata('error', "Tahap Preview 2 telah selesai dan disetujui (ACC). Anda tidak dapat mengunggah ulang berkas.");
            redirect('mahasiswa/bimbingan');
            return;
        }

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|docx|zip';
        $config['max_size']      = 10240;
        $clean_tahap = str_replace(' ', '', strtoupper($tahap));
        $config['file_name']     = $clean_tahap . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_draft')) {
            $error_msg = $this->upload->display_errors('', '');
            $this->session->set_flashdata('error', 'Gagal mengunggah berkas: ' . $error_msg);
        } else {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];
            $catatan = trim($this->input->post('catatan_mahasiswa') ?? '');

            $data_insert = [
                'nim'               => $nim,
                'tahap_preview'     => $tahap,
                'file_draft'        => $file_name,
                'catatan_mahasiswa' => $catatan,
                'status_pembimbing' => 'Pending',
                'created_at'        => date('Y-m-d H:i:s')
            ];

            $this->Mahasiswa_model->save_upload_preview($data_insert);

            if (!empty($_FILES['rekomen_eviden']['name'])) {
                $ev_config['upload_path']   = './uploads/persyaratan_ta/';
                $ev_config['allowed_types'] = 'pdf|docx|doc';
                $ev_config['max_size']      = 10240;
                $ev_config['file_name']     = 'EVIDEN_' . $nim . '_' . time();

                $this->upload->initialize($ev_config);
                if ($this->upload->do_upload('rekomen_eviden')) {
                    $ev_data = $this->upload->data();
                    $ev_file = $ev_data['file_name'];
                    $rek_title = trim($this->input->post('rekomen_title') ?? 'Jalur Ekuivalensi Prestasi');

                    $this->Rekomendasi_model->save_submission([
                        'nim'                 => $nim,
                        'recommendation_type' => 'non_sidang',
                        'jalur_title'         => $rek_title,
                        'form_data_json'      => json_encode(['eviden' => $ev_file, 'uploaded_by' => 'mahasiswa']),
                        'catatan_dosen'       => 'Mahasiswa mengajukan kelulusan jalur Non-Sidang dengan dokumen bukti pendukung.',
                        'status'              => 'pending'
                    ]);
                }
            }

            $this->session->set_flashdata('success', "Draft Berkas {$tahap} berhasil diunggah! Menunggu peninjauan dari Dosen Penilai.");
        }

        redirect('mahasiswa/bimbingan');
    }

    public function upload_preview1() {
        $this->upload_preview();
    }

    public function upload_preview3() {
        $this->upload_preview3_ajax();
    }

    public function upload_preview3_ajax() {
        header('Content-Type: application/json');

        $nim = $this->_get_current_nim();
        $tahap = 'Preview 3';

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $jenis = $this->input->post('jenis_file');
        if (!in_array($jenis, ['bimbingan', 'sitasi', 'persyaratan'])) {
            echo json_encode(['status' => false, 'message' => 'Jenis file tidak valid.']);
            return;
        }

        // Pengecekan blokir: jika berkas jenis ini sudah pernah diunggah dan masih Pending, blokir upload ulang
        $riwayat_p3 = $this->Mahasiswa_model->get_riwayat_preview($nim, $tahap);
        foreach ($riwayat_p3 as $r) {
            if (!empty($r['file_' . $jenis])) {
                $st = strtolower($r['status_pembimbing'] ?? '');
                if ($st === 'pending') {
                    echo json_encode([
                        'status'  => false,
                        'message' => "Unggah berkas diblokir: Berkas " . ucfirst($jenis) . " Anda sebelumnya masih dalam proses peninjauan (Pending). Harap menunggu keputusan ACC atau catatan revisi dari dosen sebelum mengunggah kembali."
                    ]);
                    return;
                }
                if ($st === 'approved') {
                    echo json_encode([
                        'status'  => false,
                        'message' => "Berkas " . ucfirst($jenis) . " telah disetujui (ACC). Anda tidak perlu mengunggah ulang berkas ini."
                    ]);
                    return;
                }
                break;
            }
        }

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf|doc|docx',
            'max_size'      => 10240,
        ];

        $this->load->library('upload');

        $config['file_name'] = 'PREVIEW3_' . strtoupper($jenis) . '_' . $nim . '_' . time();
        $file_name = $this->_do_upload('file_upload', $config);

        if (!$file_name) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal upload file ' . $jenis . '. Pastikan format PDF/DOC/DOCX maksimal 10MB. ' . $this->upload->display_errors('', '')
            ]);
            return;
        }

        $catatan = trim($this->input->post('catatan_mahasiswa') ?? '');

        $data_insert = [
            'nim'                => $nim,
            'tahap_preview'      => $tahap,
            'file_draft'         => $file_name,
            'file_sitasi'        => ($jenis === 'sitasi') ? $file_name : null,
            'file_bimbingan'     => ($jenis === 'bimbingan') ? $file_name : null,
            'file_persyaratan'   => ($jenis === 'persyaratan') ? $file_name : null,
            'catatan_mahasiswa'  => $catatan,
            'status_pembimbing'  => 'Pending',
            'created_at'         => date('Y-m-d H:i:s')
        ];

        $this->Mahasiswa_model->save_upload_preview($data_insert);

        echo json_encode([
            'status'  => true,
            'message' => 'Berkas ' . ucfirst($jenis) . ' Preview 3 berhasil diunggah. Menunggu review dosen.'
        ]);
    }

    public function upload_sidang() {
        $this->upload_sidang_ajax();
    }

    public function upload_sidang_ajax() {
        header('Content-Type: application/json');

        $nim = $this->_get_current_nim();

        // Pengecekan rekomendasi Sidang vs Non-Sidang untuk validasi plotting jadwal & ruangan
        $rekomen = $this->Rekomendasi_model->get_latest_submission($nim);
        $rekomen_type = strtolower(trim($rekomen['recommendation_type'] ?? 'sidang'));
        $is_non_sidang = ($rekomen_type === 'non_sidang');

        if (!$is_non_sidang) {
            $jadwal = $this->Mahasiswa_model->get_jadwal_sidang($nim);
            if (empty($jadwal['is_sidang_scheduled'])) {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Unggah dokumen diblokir: Tanggal dan ruangan sidang Anda belum di-plotting oleh Koordinator TA. Harap menunggu hingga jadwal & ruangan sidang ditetapkan.'
                ]);
                return;
            }
        }

        // Pengecekan blokir: jika berkas sidang sebelumnya masih Pending, blokir upload ulang
        $riwayat_sidang = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Sidang');
        if (!empty($riwayat_sidang)) {
            $st_sidang = strtolower($riwayat_sidang[0]['status_pembimbing'] ?? '');
            if ($st_sidang === 'pending') {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Unggah dokumen diblokir: Berkas pendaftaran sidang Anda sedang dalam proses peninjauan/verifikasi (Pending). Harap menunggu keputusan verifikasi atau arahan revisi sebelum mengunggah kembali.'
                ]);
                return;
            }
            if ($st_sidang === 'approved') {
                echo json_encode([
                    'status'  => false,
                    'message' => 'Dokumen sidang Anda telah disetujui (ACC). Pengunggahan ulang tidak diperlukan.'
                ]);
                return;
            }
        }

        $upload_dir = './uploads/sidang/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf|doc|docx',
            'max_size'      => 10240,
            'file_name'     => 'SIDANG_' . $nim . '_' . time(),
        ];

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_sidang')) {
            echo json_encode([
                'status'  => false,
                'message' => 'Gagal upload berkas sidang: ' . $this->upload->display_errors('', '')
            ]);
            return;
        }

        $upload_data = $this->upload->data();
        $file_sidang = $upload_data['file_name'];
        $catatan = trim($this->input->post('catatan_sidang') ?? '');

        $data_insert = [
            'nim'               => $nim,
            'tahap_preview'     => 'Sidang',
            'file_draft'        => $file_sidang,
            'file_sidang'       => $file_sidang,
            'catatan_mahasiswa' => $catatan,
            'status_pembimbing' => 'Pending',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->Mahasiswa_model->save_upload_preview($data_insert);

        echo json_encode([
            'status'  => true,
            'message' => 'Berkas Sidang Akhir berhasil diunggah.'
        ]);
    }

    public function upload_bap_ajax() {
        $this->output->set_content_type('application/json');

        if (!$this->_is_authorized_reviewer()) {
            $this->output
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Unauthorized — Anda tidak memiliki hak akses.'
                ]));
            return;
        }

        $role_id = (int) $this->session->userdata('role_id');
        $is_admin_or_koor = in_array($role_id, [1, 6, 9], true);

        $nim = $this->input->post('nim');
        if (empty($nim)) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'NIM tidak valid.'
                ]));
            return;
        }

        if (!$is_admin_or_koor) {
            $dosen_id = $this->session->userdata('user_id');
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, 3);
            $allowed  = false;
            foreach ($students as $s) {
                if ((string) $s['nim'] === (string) $nim) { $allowed = true; break; }
            }
            if (!$allowed) {
                $this->output
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status'  => false,
                        'message' => 'Anda bukan Penguji 1 mahasiswa ini.'
                    ]));
                return;
            }
        }

        $marker = 'bap_published_' . date('YmdHis');

        $saved = $this->Mahasiswa_model->save_pendaftaran_ta([
            'nim'      => $nim,
            'file_bap' => $marker,
        ]);

        if ($saved) {
            $this->output
                ->set_status_header(200)
                ->set_output(json_encode([
                    'status'  => true,
                    'message' => 'BAP berhasil dipublikasikan ke halaman mahasiswa.',
                    'marker'  => $marker,
                ]));
        } else {
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Gagal mempublikasikan BAP. Silakan coba lagi.'
                ]));
        }
    }

    // =========================================================
    // REVIEW PREVIEW (DOSEN)
    // =========================================================

    private function _build_review_update($posisi, $catatan, $status) {
        $catatan = $this->_clean_comment($catatan);

        switch ((int) $posisi) {
            case 1:
                $data = ['catatan_pembimbing' => $catatan];
                if ($status !== null && $status !== '') {
                    if (!in_array($status, $this->allowed_status, true)) return null;
                    $data['status_pembimbing'] = $status;
                }
                return [$data, 'Review Pembimbing 1 berhasil disimpan.'];
            case 2:
                return [['catatan_pembimbing_2' => $catatan], 'Catatan Pembimbing 2 berhasil disimpan.'];
            case 3:
                return [['catatan_penguji_1' => $catatan], 'Catatan Penguji 1 berhasil disimpan.'];
            case 4:
                return [['catatan_penguji_2' => $catatan], 'Catatan Penguji 2 berhasil disimpan.'];
        }
        return null;
    }

    public function review_preview() {
        if (!$this->_is_authorized_reviewer()) {
            redirect('mahasiswa/bimbingan');
            return;
        }

        $id      = $this->input->post('id_preview');
        $posisi  = $this->_clean_posisi($this->input->post('posisi'), 0);
        $catatan = $this->input->post('catatan_pembimbing');
        $status  = $this->input->post('status_pembimbing');

        $role_back = array_search($posisi, $this->role_map, true) ?: 'p1';

        if (!$this->_get_reviewable_preview($id, $posisi)) {
            $this->session->set_flashdata('error', 'Anda tidak berhak me-review berkas ini.');
            redirect('mahasiswa/dosen_bimbingan?role=' . $role_back);
            return;
        }

        $built = $this->_build_review_update($posisi, $catatan, $status);
        if ($built === null) {
            $this->session->set_flashdata('error', 'Data review tidak valid.');
            redirect('mahasiswa/dosen_bimbingan?role=' . $role_back);
            return;
        }

        list($data, $message) = $built;
        $this->Mahasiswa_model->update_review_preview($id, $data);
        $this->session->set_flashdata('success', $message);

        redirect('mahasiswa/dosen_bimbingan?role=' . $role_back);
    }

    public function review_preview_ajax() {
        header('Content-Type: application/json');
        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id      = $this->input->post('id_preview');
        $posisi  = $this->_clean_posisi($this->input->post('posisi'), 0);
        $catatan = $this->input->post('catatan_pembimbing');
        $status  = $this->input->post('status_pembimbing');

        if (empty($id)) {
            echo json_encode(['status' => false, 'message' => 'ID preview tidak valid']);
            return;
        }
        if ($posisi === 0) {
            echo json_encode(['status' => false, 'message' => 'Posisi tidak valid']);
            return;
        }

        if (!$this->_get_reviewable_preview($id, $posisi)) {
            echo json_encode(['status' => false, 'message' => 'Anda tidak berhak me-review berkas ini.']);
            return;
        }

        $built = $this->_build_review_update($posisi, $catatan, $status);
        if ($built === null) {
            echo json_encode(['status' => false, 'message' => 'Status atau posisi tidak valid']);
            return;
        }

        list($data, $message) = $built;
        $this->Mahasiswa_model->update_review_preview($id, $data);

        echo json_encode(['status' => true, 'message' => $message]);
    }

    public function review_preview_batch_ajax() {
        header('Content-Type: application/json');
        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $ids    = $this->input->post('ids');
        $posisi = $this->_clean_posisi($this->input->post('posisi'), 0);

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => false, 'message' => 'Tidak ada data yang dipilih']);
            return;
        }
        if ($posisi === 0) {
            echo json_encode(['status' => false, 'message' => 'Posisi tidak valid']);
            return;
        }

        $comment_col = [
            2 => 'catatan_pembimbing_2',
            3 => 'catatan_penguji_1',
            4 => 'catatan_penguji_2',
        ];
        $label = [1 => 'P1', 2 => 'P2', 3 => 'Penguji 1', 4 => 'Penguji 2'];

        $processed = 0;
        $skipped   = 0;

        foreach ($ids as $id) {
            $preview = $this->_get_reviewable_preview($id, $posisi);
            if (!$preview) { $skipped++; continue; }

            if ($posisi === 1) {
                $this->Mahasiswa_model->update_review_preview($id, ['status_pembimbing' => 'Approved']);
                $processed++;
            } else {
                $col = $comment_col[$posisi];
                if (!empty(trim(strip_tags((string) ($preview[$col] ?? ''))))) {
                    $skipped++;
                    continue;
                }
                $this->Mahasiswa_model->update_review_preview($id, [$col => 'Telah ditinjau (massal)']);
                $processed++;
            }
        }

        if ($processed === 0) {
            echo json_encode(['status' => false, 'message' => 'Tidak ada berkas yang diproses (' . $skipped . ' dilewati).']);
            return;
        }

        $message = $processed . ' berkas berhasil diproses (' . $label[$posisi] . ').';
        if ($skipped > 0) $message .= ' ' . $skipped . ' dilewati (tidak berhak / sudah ada komentar).';

        echo json_encode(['status' => true, 'message' => $message]);
    }

    public function ajax_get_preview_log() {
        header('Content-Type: application/json');
        $nim   = $this->_get_current_nim();
        $tahap = $this->input->get('tahap');

        if (empty($tahap)) {
            echo json_encode(['status' => false, 'message' => 'Tahap tidak valid']);
            return;
        }

        $riwayat = $this->Mahasiswa_model->get_riwayat_preview($nim, $tahap);
        echo json_encode(['status' => true, 'data' => $riwayat]);
    }

    public function upload_preview_ajax() {
        header('Content-Type: application/json');
        $nim   = $this->_get_current_nim();
        $tahap = $this->input->post('tahap_preview') ?: 'Preview 1';

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|docx|zip';
        $config['max_size']      = 10240;
        $clean_tahap = str_replace(' ', '', strtoupper($tahap));
        $config['file_name']     = $clean_tahap . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_draft')) {
            echo json_encode([
                'status'  => false,
                'message' => $this->upload->display_errors('', '')
            ]);
            return;
        }

        $upload_data = $this->upload->data();
        $file_name = $upload_data['file_name'];
        $catatan = trim($this->input->post('catatan_mahasiswa') ?? '');

        $data_insert = [
            'nim'               => $nim,
            'tahap_preview'     => $tahap,
            'file_draft'        => $file_name,
            'catatan_mahasiswa' => $catatan,
            'status_pembimbing' => 'Pending',
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $this->Mahasiswa_model->save_upload_preview($data_insert);

        $riwayat = $this->Mahasiswa_model->get_riwayat_preview($nim, $tahap);
        $latest  = $riwayat[0] ?? null;

        echo json_encode([
            'status'         => true,
            'message'        => "Draft Berkas {$tahap} berhasil diunggah!",
            'tahap'          => $tahap,
            'upload_count'   => count($riwayat),
            'latest_preview' => $latest,
            'riwayat'        => $riwayat
        ]);
    }

    // =========================================================
    // SSE MAHASISWA
    // =========================================================

    public function sse_mahasiswa_bimbingan() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        session_write_close();

        $nim = $this->_get_current_nim();
        $lastData = null;

        while (true) {
            $riwayat_p1     = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 1');
            $riwayat_p2     = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 2');
            $riwayat_p3     = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 3');
            $riwayat_sidang = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Sidang');

            $has_app_bimbingan   = false;
            $has_app_sitasi      = false;
            $has_app_persyaratan = false;
            $seen_bimbingan      = false;
            $seen_sitasi         = false;
            $seen_persyaratan    = false;
            foreach ($riwayat_p3 as $r) {
                if (!$seen_bimbingan && !empty($r['file_bimbingan'])) {
                    $seen_bimbingan = true;
                    $st_b = (!empty($r['status_file_bimbingan']) && $r['status_file_bimbingan'] !== 'Pending')
                        ? $r['status_file_bimbingan']
                        : ($r['status_pembimbing'] ?? 'Pending');
                    if ($st_b === 'Approved') $has_app_bimbingan = true;
                }
                if (!$seen_sitasi && !empty($r['file_sitasi'])) {
                    $seen_sitasi = true;
                    $st_s = (!empty($r['status_file_sitasi']) && $r['status_file_sitasi'] !== 'Pending')
                        ? $r['status_file_sitasi']
                        : ($r['status_pembimbing'] ?? 'Pending');
                    if ($st_s === 'Approved') $has_app_sitasi = true;
                }
                if (!$seen_persyaratan && !empty($r['file_persyaratan'])) {
                    $seen_persyaratan = true;
                    $st_p = (!empty($r['status_file_persyaratan']) && $r['status_file_persyaratan'] !== 'Pending')
                        ? $r['status_file_persyaratan']
                        : ($r['status_pembimbing'] ?? 'Pending');
                    if ($st_p === 'Approved') $has_app_persyaratan = true;
                }
            }

            $jadwal_sidang = $this->Mahasiswa_model->get_jadwal_sidang($nim);
            $this->load->model('AdminLayanan_model');
            $berkas_ps = $this->AdminLayanan_model->get_berkas_pendaftaran_sidang($nim);
            $ps_uploaded = 0;
            $ps_approved = 0;
            $ps_req_total = 0;
            $ps_req_approved = 0;
            $ps_revision = 0;
            foreach ($berkas_ps as $bps) {
                $is_req = !empty($bps['is_required']);
                if ($is_req) $ps_req_total++;
                if (!empty($bps['is_uploaded'])) $ps_uploaded++;
                if (($bps['status'] ?? '') === 'Disetujui Admin LAA') {
                    $ps_approved++;
                    if ($is_req) $ps_req_approved++;
                } elseif (($bps['status'] ?? '') === 'Revisi Admin LAA') {
                    $ps_revision++;
                }
            }
            $g_curr = $this->Mahasiswa_model->get_guidance_by_nim($nim);
            $fs_status = strtolower(trim($g_curr['status_filesidang'] ?? ''));
            $sp_status = strtolower(trim($g_curr['status_preview'] ?? ''));
            $is_p1_app_sse = (!empty($riwayat_p1[0]['lulus_preview1']) && (string)$riwayat_p1[0]['lulus_preview1'] === '1')
                || (!empty($g_curr['lulus_preview1']) && (string)$g_curr['lulus_preview1'] === '1');
            $is_p2_app_sse = (!empty($riwayat_p2[0]['lulus_preview2']) && (string)$riwayat_p2[0]['lulus_preview2'] === '1')
                || (!empty($g_curr['lulus_preview2']) && (string)$g_curr['lulus_preview2'] === '1');
            $is_p3_app_sse = (!empty($riwayat_p3[0]['lulus_preview3']) && (string)$riwayat_p3[0]['lulus_preview3'] === '1')
                || (!empty($g_curr['lulus_preview3']) && (string)$g_curr['lulus_preview3'] === '1');

            $is_ps_app = $is_p3_app_sse
                && !in_array($sp_status, ['preview1', 'preview2', 'preview3'], true)
                && (
                    in_array($fs_status, ['disetujui admin laa', 'approved', 'disetujui', 'lulus'], true)
                    || strpos($fs_status, 'disetujui') !== false
                    || ($ps_uploaded > 0 && !empty($berkas_ps) && $ps_approved === count($berkas_ps))
                    || ($ps_uploaded > 0 && $ps_req_total > 0 && $ps_req_approved === $ps_req_total && $ps_revision === 0)
                );

            $data = [
                'riwayat_p1'          => $riwayat_p1,
                'riwayat_p2'          => $riwayat_p2,
                'riwayat_p3'          => $riwayat_p3,
                'riwayat_sidang'      => $riwayat_sidang,
                'latest_p1'           => $riwayat_p1[0] ?? null,
                'latest_p2'           => $riwayat_p2[0] ?? null,
                'latest_p3'           => $riwayat_p3[0] ?? null,
                'latest_sidang'       => $riwayat_sidang[0] ?? null,
                'upload_count_p1'     => count($riwayat_p1),
                'upload_count_p2'     => count($riwayat_p2),
                'upload_count_p3'     => count($riwayat_p3),
                'upload_count_ps'     => $ps_uploaded,
                'upload_count_sidang' => count($riwayat_sidang),
                'is_p1_app'           => (bool)$is_p1_app_sse,
                'is_p2_app'           => (bool)$is_p2_app_sse,
                'is_p3_app'           => (bool)$is_p3_app_sse,
                'is_ps_app'           => $is_ps_app,
                'is_ps_revision'      => ($is_p3_app_sse && !in_array($sp_status, ['preview1', 'preview2', 'preview3'], true)) && ($ps_revision > 0 || strpos($fs_status, 'revisi') !== false),
                'berkas_syarat_sidang'=> $berkas_ps,
                'has_app_bimbingan'   => $has_app_bimbingan,
                'has_app_sitasi'      => $has_app_sitasi,
                'has_app_persyaratan' => $has_app_persyaratan,
                'tgl_sidang'          => $jadwal_sidang['tgl_sidang'],
                'jam_mulai_sidang'    => $jadwal_sidang['jam_mulai_sidang'],
                'jam_selesai_sidang'  => $jadwal_sidang['jam_selesai_sidang'],
                'ruangan_sidang'      => $jadwal_sidang['ruangan_sidang'],
                'link_sidang'         => $jadwal_sidang['link_sidang'],
                'is_sidang_scheduled' => $jadwal_sidang['is_sidang_scheduled'],
                'rekomendasi'         => $this->Rekomendasi_model->get_latest_submission($nim),
                'de_detail'           => $this->Mahasiswa_model->get_de_detail($nim),
            ];

            $json = json_encode($data);
            if ($json !== $lastData) {
                echo "data: " . $json . "\n\n";
                ob_flush(); flush();
                $lastData = $json;
            }

            if (connection_aborted()) break;
            sleep(3);
        }
        exit;
    }

    // =========================================================
    // DOMAIN 2: DOSEN (BIMBINGAN & PENGUJI) — UNIFIED 4 ROLE
    // =========================================================

    private function _render_dosen_dashboard($default_role) {
        if (!$this->_is_authorized_reviewer()) {
            redirect('login');
            return;
        }

        $role = $this->input->get('role', TRUE);

        if (!$role && $this->input->get('posisi')) {
            $is_p2_like = ((int) $this->input->get('posisi') === 2);
            $is_penguji = in_array($default_role, ['u1', 'u2'], true);
            $role = $is_penguji ? ($is_p2_like ? 'u2' : 'u1') : ($is_p2_like ? 'p2' : 'p1');
        }

        if (!isset($this->role_map[$role])) $role = $default_role;

        $model_posisi   = $this->role_map[$role];
        $display_posisi = in_array($role, ['p1', 'u1'], true) ? 1 : 2;
        $is_pembimbing  = in_array($role, ['p1', 'p2'], true);

        $dosen_id = $this->session->userdata('user_id');
        $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $model_posisi);
        $tab_counts = [
            'Preview 1'          => 0,
            'Preview 2'          => 0,
            'Preview 3'          => 0,
            'Persyaratan Sidang' => 0,
            'Sidang'             => 0,
            'Lulus'              => 0,
        ];
        foreach ($students as $st) {
            $thp = $this->Mahasiswa_model->get_student_current_tahap($st);
            if (isset($tab_counts[$thp])) {
                $tab_counts[$thp]++;
            }
        }

        $tahap_input = $this->input->get('tahap', TRUE);
        $tahap_map = [
            'preview1'           => 'Preview 1',
            'preview2'           => 'Preview 2',
            'preview3'           => 'Preview 3',
            'persyaratan_sidang' => 'Persyaratan Sidang',
            'persyaratansidang'  => 'Persyaratan Sidang',
            'sidang'             => 'Sidang',
            'lulus'              => 'Lulus',
            'Preview 1'          => 'Preview 1',
            'Preview 2'          => 'Preview 2',
            'Preview 3'          => 'Preview 3',
            'Persyaratan Sidang' => 'Persyaratan Sidang',
            'Sidang'             => 'Sidang',
            'Lulus'              => 'Lulus',
        ];
        $is_penguji = in_array($role, ['u1', 'u2'], true);
        if ($is_penguji) {
            $default_tahap = (isset($tahap_map[$tahap_input]) && $tahap_map[$tahap_input] !== 'Preview 1')
                ? $tahap_map[$tahap_input]
                : 'Preview 2';
        } else {
            $default_tahap = isset($tahap_map[$tahap_input])
                ? $tahap_map[$tahap_input]
                : 'Preview 1';
        }

        $data = [
            'title'         => $is_pembimbing ? 'Dashboard Bimbingan Dosen' : 'Dashboard Dosen Penguji',
            'role'          => $role,
            'posisi'        => $display_posisi,
            'model_posisi'  => $model_posisi,
            'tab_counts'    => $tab_counts,
            'default_tahap' => $default_tahap,
        ];

        $this->load->view('mahasiswa/dosen_bimbingan', $data);
    }

    /**
     * Halaman Dashboard Bimbingan Dosen (Sebagai Pembimbing)
     * 
     * Menampilkan daftar seluruh mahasiswa bimbingan yang dibimbing oleh dosen yang sedang login
     * (baik sebagai Pembimbing 1 maupun Pembimbing 2) lengkap dengan status tahapan dan berkas naskah.
     * 
     * @access public
     * @return void
     */
    public function dosen_bimbingan() {
        $this->_render_dosen_dashboard('p1');
    }

    /**
     * Halaman Dashboard Pengujian Dosen (Sebagai Penguji)
     * 
     * Menampilkan daftar mahasiswa yang diuji oleh dosen yang sedang login (sebagai Penguji 1 atau Penguji 2)
     * untuk keperluan penilaian Desk Evaluation Preview 2 maupun Ujian Sidang Akhir.
     * 
     * @access public
     * @return void
     */
    public function dosen_penguji() {
        $this->_render_dosen_dashboard('u1');
    }

    /**
     * AJAX Endpoint: Ambil Data Mahasiswa Bimbingan & Pengujian Dosen
     * 
     * Mengambil daftar mahasiswa bimbingan/pengujian secara asinkron lengkap dengan normalisasi status tahapan,
     * status kelulusan bab, berkas naskah terbaru, nilai DE/Sidang, serta rekomendasi jalur kelulusan P1.
     * 
     * @access public
     * @return void Output JSON {status, data, stats, total}
     */
    public function ajax_get_dosen_bimbingan() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $dosen_id = $this->session->userdata('user_id');
        $posisi   = $this->_posisi_from_get();

        $tahap = $this->input->get('tahap') ?: 'Preview 1';
        if (!in_array($tahap, $this->allowed_tahap, true)) $tahap = 'Preview 1';

        $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);

        $counts_by_tahap = [
            'Preview 1'          => 0,
            'Preview 2'          => 0,
            'Preview 3'          => 0,
            'Persyaratan Sidang' => 0,
            'Sidang'             => 0,
            'Lulus'              => 0,
        ];

        foreach ($students as $student) {
            $st_tahap = $this->Mahasiswa_model->get_student_current_tahap($student);
            if (isset($counts_by_tahap[$st_tahap])) {
                $counts_by_tahap[$st_tahap]++;
            }
        }

        $data = [];

        foreach ($students as $student) {
            $student_tahap = $this->Mahasiswa_model->get_student_current_tahap($student);
            // Mahasiswa hanya ditampilkan di tabel yang sesuai dengan preview/tahap aktif mereka
            if ($student_tahap !== $tahap) {
                continue;
            }

            if ($tahap === 'Persyaratan Sidang') {
                $this->load->model('AdminLayanan_model');
                $bs = $this->AdminLayanan_model->get_berkas_pendaftaran_sidang($student['nim']);
                $files_ps = [];
                $uploaded_count = 0;
                $approved_count = 0;
                $revision_count = 0;
                $pending_count  = 0;
                $latest_date    = null;

                foreach ($bs as $b) {
                    $st_b = $b['status'] ?? 'Pending Verifikasi';
                    $norm_st = ($st_b === 'Disetujui Admin LAA') ? 'Approved' : (($st_b === 'Revisi Admin LAA') ? 'Revision' : 'Pending');
                    if (!empty($b['is_uploaded'])) {
                        $uploaded_count++;
                        if ($norm_st === 'Approved') $approved_count++;
                        elseif ($norm_st === 'Revision') $revision_count++;
                        else $pending_count++;

                        if (!empty($b['uploaded_at']) && (!$latest_date || strtotime($b['uploaded_at']) > strtotime($latest_date))) {
                            $latest_date = $b['uploaded_at'];
                        }

                        $files_ps[] = [
                            'type'     => $b['kode'],
                            'label'    => $b['nama_file'],
                            'file'     => $b['file_name'],
                            'file_url' => $b['file_url'],
                            'status'   => $norm_st,
                            'raw_status' => $st_b,
                            'catatan'  => $b['catatan'] ?? '',
                        ];
                    }
                }

                $fs_status = strtolower(trim($student['status_filesidang'] ?? ''));
                $overall_ps = 'Pending';
                if (in_array($fs_status, ['disetujui admin laa', 'approved', 'disetujui', 'lulus'], true) || (count($bs) > 0 && $approved_count === count($bs))) {
                    $overall_ps = 'Approved';
                } elseif ($revision_count > 0 || strpos($fs_status, 'revisi') !== false) {
                    $overall_ps = 'Revision';
                }

                if ($uploaded_count > 0) {
                    $latest = [
                        'id'                       => 'ps_' . $student['nim'],
                        'nim'                      => $student['nim'],
                        'tahapan_preview'          => 'persyaratan_sidang',
                        'created_at'               => $latest_date ?: date('Y-m-d H:i:s'),
                        'status_pembimbing'        => $overall_ps,
                        'status_penguji'           => $overall_ps,
                        'files_persyaratan_sidang' => $files_ps,
                        'total_required'           => count($bs),
                        'uploaded_count'           => $uploaded_count,
                        'approved_count'           => $approved_count,
                        'revision_count'           => $revision_count,
                        'pending_count'            => $pending_count,
                    ];
                    $previews = [$latest];
                } else {
                    $latest   = null;
                    $previews = [];
                }
            } else {
                $query_tahap = ($tahap === 'Lulus') ? 'Sidang' : $tahap;
                $previews = $this->Mahasiswa_model->get_riwayat_preview($student['nim'], $query_tahap);
                if ($tahap === 'Lulus' && empty($previews)) {
                    $previews = $this->Mahasiswa_model->get_riwayat_preview($student['nim'], 'Preview 3');
                }
                $latest = !empty($previews) ? $previews[0] : null;

                // Untuk Preview 3: jika berkas Bimbingan, Sitasi, dan Persyaratan Jalur TA diunggah di row terpisah,
                // gabungkan berkas & status terbaru dari ke-3 kategori ke dalam $latest agar status ACC per-kategori akurat
                if ($tahap === 'Preview 3' && !empty($previews) && $latest) {
                    foreach (['bimbingan', 'sitasi', 'persyaratan'] as $cat_k) {
                        $f_col  = 'file_' . $cat_k;
                        $st_col = 'status_file_' . $cat_k;
                        $ct_col = 'catatan_file_' . $cat_k;
                        foreach ($previews as $pr_row) {
                            if (!empty($pr_row[$f_col])) {
                                $latest[$f_col] = $pr_row[$f_col];
                                $eff_st = (!empty($pr_row[$st_col]) && $pr_row[$st_col] !== 'Pending')
                                    ? $pr_row[$st_col]
                                    : ($pr_row['status_pembimbing'] ?? 'Pending');
                                $latest[$st_col] = $eff_st;
                                if (!empty($pr_row[$ct_col])) {
                                    $latest[$ct_col] = $pr_row[$ct_col];
                                }
                                break;
                            }
                        }
                    }
                }
            }

            $rekomen = $this->Rekomendasi_model->get_latest_submission($student['nim']);
            $jadwal_sidang = ($tahap === 'Sidang' || $tahap === 'Lulus')
                ? $this->Mahasiswa_model->get_jadwal_sidang($student['nim'])
                : null;
            $kelulusan_info = ($tahap === 'Sidang' || $tahap === 'Lulus')
                ? $this->Mahasiswa_model->get_kelulusan_sidang_info($student)
                : null;

            if ($latest && !empty($latest['file_draft']) && $tahap !== 'Persyaratan Sidang') {
                if ($tahap === 'Sidang' || $tahap === 'Lulus') {
                    $sidangFile = $latest['file_sidang'] ?? $latest['file_draft'];
                    $filePath = FCPATH . 'uploads/sidang/' . $sidangFile;
                } else {
                    $filePath = FCPATH . 'uploads/preview_ta/' . $latest['file_draft'];
                }
                if (!file_exists($filePath)) {
                    $latest['file_missing'] = true;
                }
            }

            $has_syarat_sidang = false;
            if ($tahap === 'Preview 3' || $tahap === 'Persyaratan Sidang') {
                if (!empty($latest['file_persyaratan'])) {
                    $has_syarat_sidang = true;
                } else {
                    $this->load->model('AdminLayanan_model');
                    $bs_check = $this->AdminLayanan_model->get_berkas_pendaftaran_sidang($student['nim']);
                    foreach ($bs_check as $b) {
                        if (!empty($b['is_uploaded'])) {
                            $has_syarat_sidang = true;
                            break;
                        }
                    }
                }
            }

            $data[] = [
                'nim'                    => $student['nim'],
                'nama_mahasiswa'         => $student['nama_mahasiswa'] ?? $student['nim'],
                'judul'                  => $student['judul'] ?? '-',
                'konsentrasi_dkv'        => $student['konsentrasi_dkv'] ?? '',
                'current_tahap'          => $student_tahap,
                'latest_preview'         => $latest,
                'riwayat_previews'       => $previews,
                'rekomendasi'            => $rekomen,
                'jadwal_sidang'          => $jadwal_sidang,
                'kelulusan_info'         => $kelulusan_info,
                'has_persyaratan_sidang' => $has_syarat_sidang,
                'status_de'              => $student['status_de'] ?? ($latest['kelayakan2'] ?? null),
                'nilai_pembimbing1'      => $student['nilai_pembimbing1'] ?? ($latest['nilai_pembimbing1'] ?? null),
                'nilai_pembimbing2'      => $student['nilai_pembimbing2'] ?? ($latest['nilai_pembimbing2'] ?? null),
                'nilai_penguji1'         => $student['nilai_penguji1'] ?? ($latest['nilai_penguji1'] ?? null),
                'nilai_penguji2'         => $student['nilai_penguji2'] ?? ($latest['nilai_penguji2'] ?? null),
                'catatan_p1'             => $student['catatan_p1'] ?? ($latest['penilaian_pembimbing1'] ?? ''),
                'catatan_p2'             => $student['catatan_p2'] ?? ($latest['penilaian_pembimbing2'] ?? ''),
                'catatan_u1'             => $student['catatan_u1'] ?? ($latest['penilaian_penguji1'] ?? ''),
                'catatan_u2'             => $student['catatan_u2'] ?? ($latest['penilaian_penguji2'] ?? ''),
                'de_tanggal'             => $student['de_tanggal'] ?? ($latest['tanggal_presentasi'] ?? null),
                'de_waktu'               => $student['de_waktu'] ?? ($latest['waktu_presentasi'] ?? null),
                'de_ruangan'             => $student['de_ruangan'] ?? ($latest['ruang_sidang'] ?? null),
                'de_link'                => $student['de_link'] ?? ($latest['link_presentasi'] ?? null),
                'de_catatan'             => $student['de_catatan'] ?? ($latest['komentar_kelayakan2'] ?? null),
                'lulus_preview2'         => $student['lulus_preview2'] ?? ($latest['lulus_preview2'] ?? 0),
            ];
        }

        echo json_encode([
            'status'     => true,
            'data'       => $data,
            'stats'      => ['total' => count($data)],
            'tab_counts' => $counts_by_tahap
        ]);
    }

    public function sse_dosen_bimbingan() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $authorized = $this->_is_authorized_reviewer();
        $dosen_id   = $this->session->userdata('user_id');
        $posisi     = $this->_posisi_from_get();
        session_write_close();

        if (!$authorized) {
            echo "data: " . json_encode(['status' => false, 'message' => 'Unauthorized']) . "\n\n";
            ob_flush(); flush(); exit;
        }

        set_time_limit(0);
        $lastData = null;

        while (true) {
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
            $data = [];
            foreach ($students as $student) {
                $p1  = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 1');
                $p2  = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 2');
                $p3  = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 3');
                $cur = $this->Mahasiswa_model->get_student_current_tahap($student);
                $data[] = [
                    'nim'            => $student['nim'],
                    'nama_mahasiswa' => $student['nama_mahasiswa'] ?? $student['nim'],
                    'judul'          => $student['judul'] ?? '-',
                    'current_tahap'  => $cur,
                    'preview1'       => $p1,
                    'preview2'       => $p2,
                    'preview3'       => $p3
                ];
            }

            $json = json_encode($data);
            if ($json !== $lastData) {
                echo "data: " . $json . "\n\n";
                ob_flush(); flush();
                $lastData = $json;
            }

            if (connection_aborted()) break;
            sleep(3);
        }
        exit;
    }

    // =================================================================
    // PER-FILE ACC/REVISI/RESET/KOMENTAR, LULUS TAHAP, DOWNGRADE
    // =================================================================

    /** Helper: role PIC untuk suatu tahap. */
    private function _get_responsible_role($tahap) {
        $t = strtolower(trim($tahap));
        if ($t === 'persyaratan sidang' || $t === 'persyaratan_sidang' || $t === 'persyaratansidang') return null;
        if ($t === 'preview1' || $t === 'preview 1' || $t === 'preview3' || $t === 'preview 3') return 'p1';
        return 'u1';
    }

    /** Helper: cek apakah user yang login = PIC untuk mahasiswa ini di tahap tsb. */
    private function _is_pic_for_student($nim, $pic_role) {
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id === 1) return true;

        $dosen_id = $this->session->userdata('user_id');
        $posisi_map = ['p1' => 1, 'p2' => 2, 'u1' => 3, 'u2' => 4];
        $pic_posisi = $posisi_map[$pic_role] ?? 1;

        $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $pic_posisi);
        foreach ($students as $s) {
            if ((string)$s['nim'] === (string)$nim) return true;
        }
        return false;
    }

    /**
     * POST — ACC / Revisi / Reset / Komentar Berkas PER FILE.
     * Body: id_preview, file_type, action (Approved|Revision|Reset|Comment), catatan
     *
     * ─── CHANGE 2026-10-09 ───
     *  - Tambah dukungan action='Reset' → reset status_file_* ke 'Pending'.
     *  - Tambah dukungan action='Comment' → update catatan_file_* tanpa ubah status.
     */
    /**
     * AJAX Endpoint: Review & Validasi Berkas Naskah oleh Dosen
     * 
     * Menyimpan persetujuan (Approved) atau penolakan/revisi (Rejected) pada berkas naskah bab mahasiswa
     * beserta catatan evaluasi dari dosen pembimbing yang bersangkutan.
     * 
     * @access public
     * @return void Output JSON {status, message}
     */
    public function review_file_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id_preview = $this->input->post('id_preview');
        $file_type  = $this->input->post('file_type');
        $action     = $this->input->post('action');
        $catatan    = $this->input->post('catatan') ?? '';

        if (empty($id_preview) || empty($file_type) || empty($action)) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        // Validasi action
        $valid_actions = ['Approved', 'Revision', 'Reset', 'Comment'];
        if (!in_array($action, $valid_actions, true)) {
            echo json_encode(['status' => false, 'message' => 'Action tidak valid: ' . $action]);
            return;
        }

        if (strpos((string)$id_preview, 'ps_') === 0) {
            echo json_encode([
                'status'  => false,
                'message' => 'Berkas Persyaratan Sidang diverifikasi oleh Admin Layanan (LAA), bukan oleh Dosen.',
            ]);
            return;
        }

        $preview = $this->Mahasiswa_model->get_preview_by_id($id_preview);
        if (!$preview) {
            echo json_encode(['status' => false, 'message' => 'Preview tidak ditemukan.']);
            return;
        }

        $tahap_key  = strtolower($preview['tahapan_preview'] ?? 'preview1');
        $tahap_name = [
            'preview1' => 'Preview 1', 'preview2' => 'Preview 2',
            'preview3' => 'Preview 3', 'sidang'   => 'Sidang',
        ][$tahap_key] ?? 'Preview 1';

        $pic_role = $this->_get_responsible_role($tahap_name);

        // ─── Aksi Comment: tidak mengubah status, boleh siapa saja yang authorized ───
        if ($action === 'Comment') {
            $res = $this->Mahasiswa_model->update_file_comment($id_preview, $file_type, $catatan, $pic_role);
            echo json_encode($res);
            return;
        }

        // ─── Aksi ubah status (Approved/Revision/Reset): wajib PIC ───
        if (!$this->_is_pic_for_student($preview['nim'], $pic_role)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Hanya ' . strtoupper($pic_role) . ' (PIC tahap ' . $tahap_name . ') yang dapat ACC/Revisi berkas ini.',
            ]);
            return;
        }

        // Normalisasi: Reset → Pending
        $normalized_status = ($action === 'Reset') ? 'Pending' : $action;

        $res = $this->Mahasiswa_model->update_file_status(
            $id_preview, $file_type, $normalized_status, $catatan, $pic_role
        );

        echo json_encode($res);
    }

    /**
     * AJAX Endpoint: Penetapan Kelulusan Tahap Preview (Lulus Preview 1 / 2 / 3)
     * 
     * Menetapkan status lulus pada tahap preview mahasiswa di tabel guidance, mengaktifkan akses tahap berikutnya,
     * serta mencatat riwayat kelulusan lengkap dengan identitas dosen dan peran PIC.
     * 
     * @access public
     * @return void Output JSON {status, message}
     */
    public function lulus_stage_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim        = $this->input->post('nim');
        $tahap      = $this->input->post('tahap');
        $id_preview = $this->input->post('id_preview') ?: null;

        if (empty($nim) || empty($tahap)) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        $pic_role = $this->_get_responsible_role($tahap);
        if ($pic_role === null) {
            echo json_encode([
                'status'  => false,
                'message' => 'Tahap Persyaratan Sidang diverifikasi secara terpusat oleh Admin Layanan (LAA).',
            ]);
            return;
        }

        if (!$this->_is_pic_for_student($nim, $pic_role)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Hanya ' . strtoupper($pic_role) . ' yang dapat meluluskan tahap ' . $tahap . '.',
            ]);
            return;
        }

        $res = $this->Mahasiswa_model->set_stage_lulus($nim, $tahap, $pic_role, $id_preview);
        echo json_encode($res);
    }

    public function downgrade_stage_ajax() {
        header('Content-Type: application/json');

        // Wewenang downgrade / reset tahap dialihkan sepenuhnya secara eksklusif ke Koordinator TA
        echo json_encode([
            'status'  => false,
            'message' => 'Akses ditolak: Wewenang penurunan atau reset tahapan bimbingan telah dialihkan secara terpusat ke akun Koordinator TA.'
        ]);
        return;
    }

    // =================================================================
    // DESK EVALUATION (DE) PREVIEW 2
    // =================================================================

    public function set_keputusan_preview2_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim       = $this->input->post('nim');
        $keputusan = $this->input->post('keputusan');

        if (empty($nim) || empty($keputusan)) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        $pic_role = 'u1';
        if (!$this->_is_pic_for_student($nim, $pic_role)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Hanya Penguji 1 (U1) yang berhak menentukan kelulusan Preview 2 (DE / Non-DE).'
            ]);
            return;
        }

        $res = $this->Mahasiswa_model->set_keputusan_preview2($nim, $keputusan, $pic_role);
        echo json_encode($res);
    }

    public function simpan_jadwal_de_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim     = $this->input->post('nim');
        $tanggal = $this->input->post('tanggal');
        $waktu   = $this->input->post('waktu');
        $ruangan = $this->input->post('ruangan');
        $link    = $this->input->post('link') ?? '';
        $catatan = $this->input->post('catatan') ?? '';

        if (empty($nim) || empty($tanggal) || empty($waktu)) {
            echo json_encode(['status' => false, 'message' => 'NIM, Tanggal, dan Waktu DE wajib diisi.']);
            return;
        }

        $pic_role = 'u1';
        if (!$this->_is_pic_for_student($nim, $pic_role)) {
            echo json_encode([
                'status'  => false,
                'message' => 'Hanya Penguji 1 (U1) yang dapat menjadwalkan Desk Evaluation (DE).'
            ]);
            return;
        }

        $res = $this->Mahasiswa_model->simpan_jadwal_de($nim, $tanggal, $waktu, $ruangan, $link, $catatan);
        echo json_encode($res);
    }

    /**
     * AJAX Endpoint: Simpan Nilai Desk Evaluation (DE) Preview 2
     * 
     * Menyimpan nilai evaluasi kelayakan dan catatan penilaian naskah Preview 2 dari salah satu dosen
     * (Pembimbing 1, Pembimbing 2, Penguji 1, Penguji 2) ke tabel guidance.
     * 
     * @access public
     * @return void Output JSON {status, message}
     */
    public function simpan_nilai_de_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim     = $this->input->post('nim');
        $posisi  = (int)$this->input->post('posisi');
        $nilai   = $this->input->post('nilai');
        $catatan = $this->input->post('catatan') ?? '';

        if (empty($nim) || !in_array($posisi, [1, 2, 3, 4], true)) {
            echo json_encode(['status' => false, 'message' => 'Parameter posisi atau NIM tidak valid.']);
            return;
        }

        if ($nilai === null || $nilai === '' || !is_numeric($nilai) || (float)$nilai < 0 || (float)$nilai > 100) {
            echo json_encode(['status' => false, 'message' => 'Nilai wajib diisi angka antara 0 sampai 100.']);
            return;
        }

        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id !== 1) {
            $dosen_id = $this->session->userdata('user_id');
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
            $nims = array_column($students, 'nim');
            if (!in_array($nim, $nims, true)) {
                echo json_encode(['status' => false, 'message' => 'Anda tidak memiliki hak akses memberi nilai untuk mahasiswa ini pada posisi ini.']);
                return;
            }
        }

        $res = $this->Mahasiswa_model->simpan_nilai_de($nim, $posisi, (float)$nilai, $catatan);
        echo json_encode($res);
    }

    public function get_de_detail_ajax() {
        header('Content-Type: application/json');
        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        if (empty($nim)) {
            echo json_encode(['status' => false, 'message' => 'NIM wajib diisi.']);
            return;
        }
        $detail = $this->Mahasiswa_model->get_de_detail($nim);
        if (!$detail) {
            echo json_encode(['status' => false, 'message' => 'Data bimbingan tidak ditemukan.']);
            return;
        }
        echo json_encode(['status' => true, 'data' => $detail]);
    }

    /**
     * AJAX Endpoint: Detail Nilai Sidang Mahasiswa + Dynamic Rubrik Koordinator TA
     */
    public function get_detail_nilai_sidang_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim = $this->input->get('nim') ?: $this->input->post('nim');
        if (empty($nim)) {
            echo json_encode(['status' => false, 'message' => 'NIM wajib diisi.']);
            return;
        }

        $detail = $this->Mahasiswa_model->get_detail_nilai_sidang($nim);
        if (!$detail) {
            echo json_encode(['status' => false, 'message' => 'Data mahasiswa tidak ditemukan.']);
            return;
        }

        echo json_encode(['status' => true, 'data' => $detail]);
    }

    /**
     * AJAX Endpoint: Simpan / Edit Penilaian Sidang Mahasiswa
     */
    /**
     * AJAX Endpoint: Simpan Penilaian Majelis Ujian Sidang Akhir TA
     * 
     * Menyimpan skor evaluasi sidang dari dosen pembimbing/penguji, menghitung rata-rata skor akhir sidang,
     * menentukan status kelulusan sidang ('Lulus' / 'Tidak Lulus' / 'Lulus Bersyarat'), dan menyimpan ke database.
     * 
     * @access public
     * @return void Output JSON {status, message, nilai_akhir, kelulusan}
     */
    public function simpan_nilai_sidang_ajax() {
        header('Content-Type: application/json');

        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $nim         = $this->input->post('nim');
        $posisi      = (int)$this->input->post('posisi');
        $nilai       = $this->input->post('nilai_akhir');
        $catatan     = $this->input->post('catatan');
        $detail_raw  = $this->input->post('detail_penilaian');

        if (empty($nim) || !in_array($posisi, [1, 2, 3, 4], true)) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap atau posisi tidak valid.']);
            return;
        }

        if ($nilai === null || $nilai === '' || !is_numeric($nilai) || (float)$nilai < 0 || (float)$nilai > 100) {
            echo json_encode(['status' => false, 'message' => 'Nilai akhir wajib diisi angka antara 0 sampai 100.']);
            return;
        }

        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id !== 1) {
            $dosen_id = $this->session->userdata('user_id');
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
            $nims = array_column($students, 'nim');
            if (!in_array($nim, $nims, true)) {
                echo json_encode(['status' => false, 'message' => 'Anda tidak memiliki hak akses memberi nilai untuk mahasiswa ini pada posisi ini.']);
                return;
            }
        }

        $detail = null;
        if (!empty($detail_raw)) {
            $detail = is_string($detail_raw) ? json_decode($detail_raw, true) : $detail_raw;
        }

        $res = $this->Mahasiswa_model->save_nilai_sidang_by_posisi(
            $nim,
            $posisi,
            (float)$nilai,
            (string)$catatan,
            $detail
        );

        echo json_encode($res);
    }

    /**
     * AJAX Endpoint: Ambil Seluruh Master Rubrik Penilaian Dinamis
     */
    public function ajax_get_all_master_rubrik() {
        header('Content-Type: application/json');
        if (!$this->_is_authorized_reviewer()) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }
        $this->load->model('KoordinatorTA_model');
        $rubriks = $this->KoordinatorTA_model->get_all_master_rubrik();
        echo json_encode(['status' => true, 'data' => $rubriks]);
    }
}