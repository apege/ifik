<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mahasiswa_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
    }

    private function _get_current_nim() {
        return $this->session->userdata('nim') ?: ($this->session->userdata('nidn_nim') ?: '1301210001');
    }

    // Dashboard Mahasiswa & Overview Status Approval Chain
    public function index() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Dashboard Mahasiswa';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $this->load->view('mahasiswa/dashboard', $data);
    }

    // Detail Lengkap Pendaftaran Tugas Akhir (Single Page View)
    public function detail_pendaftaran() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Detail Pengajuan Tugas Akhir';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $this->load->view('mahasiswa/detail_pendaftaran', $data);
    }

    // Formulir Edit Pendaftaran TA (Single Page Non-Wizard Form)
    public function edit_pendaftaran() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        $has_ta = !empty($pendaftaran['judul_1']);

        if (!$has_ta) {
            redirect('mahasiswa/pendaftaran_ta');
            return;
        }

        $w_status  = $pendaftaran['status_approval_wali'] ?? 'Pending';
        $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
        $k_status  = $pendaftaran['status_approval_koor'] ?? 'Pending';
        $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
        $st_judul  = $pendaftaran['status_judul'] ?? 'Pending';
        $has_revisi = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected' || !empty($pendaftaran['berkas_kurang']) || $st_judul === 'Rejected');
        $is_locked = !$has_revisi;

        $data['title'] = $is_locked ? 'Detail / Ringkasan Formulir Tugas Akhir' : 'Edit Formulir Tugas Akhir (Revisi)';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $pendaftaran;
        $data['is_locked'] = $is_locked;
        $data['has_revisi'] = $has_revisi;

        if ($this->input->post()) {
            if ($is_locked) {
                $this->session->set_flashdata('error', 'Formulir saat ini terkunci (hanya lihat) karena pengajuan sedang dalam proses peninjauan.');
                redirect('mahasiswa/edit_pendaftaran');
                return;
            }

            $config['upload_path']   = './uploads/persyaratan_ta/';
            $config['allowed_types'] = 'pdf';
            $config['max_size']      = 5120; // 5MB

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            $file_step3 = $this->_do_upload('file_ksm', $config);
            $file_step4 = $this->_do_upload('file_transkrip', $config);
            $file_step5 = $this->_do_upload('file_pernyataan', $config);
            $file_step6 = $this->_do_upload('file_bebas_lab', $config);

            // Reset status approval yang sebelumnya ditolak kembali ke Pending untuk re-review
            $new_w_status  = ($w_status === 'Rejected') ? 'Pending' : $w_status;
            $new_a_status  = ($a_status === 'Rejected') ? 'Pending' : $a_status;
            $new_k_status  = ($k_status === 'Rejected') ? 'Pending' : $k_status;
            $new_kk_status = ($kk_status === 'Rejected') ? 'Pending' : $kk_status;

            $data_ta = array(
                'nim'                  => $nim,
                'jenis_ta'             => $this->input->post('jenis_ta'),
                'judul_1'              => $this->input->post('judul_1'),
                'judul_2'              => $this->input->post('judul_2'),
                'judul_3'              => $this->input->post('judul_3'),
                'judul_en'             => $this->input->post('judul_en'),
                'konsentrasi_dkv'      => $this->input->post('konsentrasi_dkv'),
                'file_ksm'             => $file_step3 ? $file_step3 : $this->input->post('file_ksm_old'),
                'file_transkrip'       => $file_step4 ? $file_step4 : $this->input->post('file_transkrip_old'),
                'file_pernyataan'      => $file_step5 ? $file_step5 : $this->input->post('file_pernyataan_old'),
                'file_bebas_lab'       => $file_step6 ? $file_step6 : $this->input->post('file_bebas_lab_old'),
                'status_approval_wali' => $new_w_status,
                'status_approval_admin'=> $new_a_status,
                'status_approval_koor' => $new_k_status,
                'status_approval_kk'   => $new_kk_status,
                'status_judul'         => ($st_judul === 'Rejected') ? 'Pending' : $st_judul,
                'berkas_kurang'        => NULL,
                'created_at'           => $pendaftaran['created_at'] ?? date('Y-m-d H:i:s')
            );

            $this->Mahasiswa_model->save_pendaftaran_ta($data_ta);
            $this->session->set_flashdata('success', 'Perbaikan berkas & formulir Tugas Akhir berhasil dikirim untuk ditinjau ulang!');
            redirect('mahasiswa');
            return;
        }

        $this->load->view('mahasiswa/edit_pendaftaran', $data);
    }

    // Fitur Geodata Mahasiswa
    public function geodata() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Geodata Mahasiswa';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);

        if ($this->input->post()) {
            $update_data = array(
                'alamat'    => $this->input->post('alamat'),
                'latitude'  => $this->input->post('latitude'),
                'longitude' => $this->input->post('longitude'),
                'kota'      => $this->input->post('kota'),
                'provinsi'  => $this->input->post('provinsi')
            );
            $this->Mahasiswa_model->update_geodata($nim, $update_data);
            $this->session->set_flashdata('success', 'Geodata berhasil diperbarui!');
            redirect('mahasiswa/geodata');
        }

        $this->load->view('mahasiswa/geodata', $data);
    }

    // Fitur Pendaftaran TA 6-Step Wizard
    public function pendaftaran_ta() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        $has_ta = !empty($pendaftaran['judul_1']);

        $has_revisi = false;
        $is_locked = false;

        if ($has_ta) {
            $w_status  = $pendaftaran['status_approval_wali'] ?? 'Pending';
            $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
            $k_status  = $pendaftaran['status_approval_koor'] ?? 'Pending';
            $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
            $st_judul  = $pendaftaran['status_judul'] ?? 'Pending';
            $has_revisi = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected' || !empty($pendaftaran['berkas_kurang']) || $st_judul === 'Rejected');
            $is_locked = !$has_revisi;
        }

        $data['title'] = $is_locked ? 'Pendaftaran Tugas Akhir (Sedang Ditinjau)' : 'Pendaftaran Tugas Akhir (6 Step)';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $pendaftaran;
        $data['is_locked'] = $is_locked;
        $data['has_revisi'] = $has_revisi;
        $data['has_ta'] = $has_ta;

        if ($this->input->post()) {
            // Konfigurasi Upload File PDF
            $config['upload_path']   = './uploads/persyaratan_ta/';
            $config['allowed_types'] = 'pdf';
            $config['max_size']      = 5120; // 5MB

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            $file_step3 = $this->_do_upload('file_ksm', $config);
            $file_step4 = $this->_do_upload('file_transkrip', $config);
            $file_step5 = $this->_do_upload('file_pernyataan', $config);
            $file_step6 = $this->_do_upload('file_bebas_lab', $config);

            // Ambil data pendaftaran_ta yang sudah ada untuk menjaga status yang sudah di-Approve
            $existing_ta = $this->db->get_where('pendaftaran_ta', array('nim' => $nim))->row_array();

            $w_status  = isset($existing_ta['status_approval_wali']) ? $existing_ta['status_approval_wali'] : 'Pending';
            $a_status  = isset($existing_ta['status_approval_admin']) ? $existing_ta['status_approval_admin'] : 'Pending';
            $k_status  = isset($existing_ta['status_approval_koor']) ? $existing_ta['status_approval_koor'] : 'Pending';
            $kk_status = isset($existing_ta['status_approval_kk']) ? $existing_ta['status_approval_kk'] : 'Pending';

            // Jika status Dosen Wali tadinya Rejected, reset Dosen Wali ke Pending untuk re-review
            if ($w_status === 'Rejected') {
                $w_status = 'Pending';
            }

            // Jika status Admin LAA tadinya Rejected dan siswa mengedit/re-upload, reset LAA ke Pending untuk re-verifikasi
            if ($a_status === 'Rejected') {
                $a_status = 'Pending';
            }

            // Jika status Koor TA / Ketua KK Rejected, reset stage terkait ke Pending saat diedit
            if ($k_status === 'Rejected') $k_status = 'Pending';
            if ($kk_status === 'Rejected') $kk_status = 'Pending';

            // Tentukan current_stage secara presisi berdasarkan rantai prasyarat
            if ($w_status !== 'Approved') {
                $current_stage = 'Dosen Wali';
            } else if ($a_status !== 'Approved') {
                $current_stage = 'Admin Layanan';
            } else if ($k_status !== 'Approved') {
                $current_stage = 'Koordinator TA';
            } else if ($kk_status !== 'Approved') {
                $current_stage = 'Ketua KK';
            } else {
                $current_stage = 'Selesai Approval';
            }

            // Lookup id_dosen_wali dari tabel dosen_wali berdasarkan nip_dosen_wali mahasiswa
            $mhs_data      = $this->db->get_where('mahasiswa', ['nim' => $nim])->row_array();
            $nip_dw        = !empty($mhs_data['nip_dosen_wali']) ? $mhs_data['nip_dosen_wali'] : null;
            $dw_row        = $nip_dw ? $this->db->get_where('dosen_wali', ['nip' => $nip_dw])->row_array() : null;
            $id_dosen_wali = $dw_row ? $dw_row['id'] : null;

            $data_ta = array(

                'nim'                  => $nim,
                'id_dosen_wali'        => $id_dosen_wali,
                'jenis_ta'             => $this->input->post('jenis_ta'),
                'judul_1'              => $this->input->post('judul_1'),
                'judul_2'              => $this->input->post('judul_2'),
                'judul_3'              => $this->input->post('judul_3'),
                'judul_en'             => $this->input->post('judul_en'),
                'konsentrasi_dkv'      => $this->input->post('konsentrasi_dkv'),
                'file_ksm'             => $file_step3 ? $file_step3 : $this->input->post('file_ksm_old'),
                'file_transkrip'       => $file_step4 ? $file_step4 : $this->input->post('file_transkrip_old'),
                'file_pernyataan'      => $file_step5 ? $file_step5 : $this->input->post('file_pernyataan_old'),
                'file_bebas_lab'       => $file_step6 ? $file_step6 : $this->input->post('file_bebas_lab_old'),
                'status_approval_wali' => $w_status,
                'status_approval_admin'=> $a_status,
                'status_approval_koor' => $k_status,
                'status_approval_kk'   => $kk_status,
                'current_stage'        => $current_stage,
                'created_at'           => isset($existing_ta['created_at']) ? $existing_ta['created_at'] : date('Y-m-d H:i:s')
            );

            // Update individual document status columns if new files were uploaded
            if ($file_step3) $data_ta['status_ksm'] = 'Pending';
            if ($file_step4) $data_ta['status_transkrip'] = 'Pending';
            if ($file_step5) $data_ta['status_pernyataan'] = 'Pending';
            if ($file_step6) $data_ta['status_bebas_lab'] = 'Pending';

            // Jika siswa mengunggah file baru saat LAA revisi, reset berkas_kurang setelah perbaikan
            if ($a_status === 'Pending' && !empty($existing_ta['berkas_kurang'])) {
                $data_ta['berkas_kurang'] = NULL;
            }

            $this->Mahasiswa_model->save_pendaftaran_ta($data_ta);
            $this->session->set_flashdata('success', 'Pendaftaran / Revisi Tugas Akhir berhasil disimpan!');
            redirect('mahasiswa');
        }

        $this->load->view('mahasiswa/pendaftaran_ta', $data);
    }

    // Helper Upload File PDF
    private function _do_upload($field_name, $config) {
        $this->upload->initialize($config);
        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
        return null;
    }

    // Fitur Ganti Password
    public function ganti_password() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Ganti Password';

        if ($this->input->post()) {
            $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
            $this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required|matches[password_baru]');

            if ($this->form_validation->run() === TRUE) {
                $password_hashed = password_hash($this->input->post('password_baru'), PASSWORD_BCRYPT);
                $this->Mahasiswa_model->update_password($nim, $password_hashed);
                $this->session->set_flashdata('success', 'Password berhasil diubah!');
                redirect('mahasiswa');
            }
        }

        $this->load->view('mahasiswa/ganti_password', $data);
    }

    // Endpoint Auto-Translate Judul ID -> EN
    public function translate_judul() {
        header('Content-Type: application/json');
        $text = trim($this->input->post('text') ?? '');

        if (empty($text)) {
            echo json_encode(['status' => 'error', 'message' => 'Teks judul tidak boleh kosong!']);
            return;
        }

        // 1. Coba Google Translate API
        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=id&tl=en&dt=t&q=" . urlencode($text);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if (!$err && !empty($response)) {
            $data = json_decode($response, true);
            $translated_text = '';
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data[0] as $segment) {
                    $translated_text .= $segment[0] ?? '';
                }
            }
            if (!empty($translated_text)) {
                echo json_encode(['status' => 'success', 'translated' => trim($translated_text)]);
                return;
            }
        }

        // 2. Fallback: MyMemory Translation API
        $url_fallback = "https://api.mymemory.translated.net/get?q=" . urlencode($text) . "&langpair=id|en";
        $fallback_res = @file_get_contents($url_fallback);
        if ($fallback_res) {
            $json = json_decode($fallback_res, true);
            $translated = $json['responseData']['translatedText'] ?? '';
            if ($translated) {
                echo json_encode(['status' => 'success', 'translated' => trim($translated)]);
                return;
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'Gagal menerjemahkan secara online, silakan ketik judul Inggris secara manual.']);
    }

    // Fitur Reset Pengajuan TA
    public function reset_pendaftaran() {
        $nim = $this->_get_current_nim();
        $this->Mahasiswa_model->reset_pendaftaran_ta($nim);
        $this->session->set_flashdata('success', 'Pengajuan Tugas Akhir berhasil di-reset!');
        redirect('mahasiswa');
    }

    // Modul Bimbingan TA & Upload Berkas Preview (Multi-Stage Hub)
    public function bimbingan() {
        $role_id = $this->session->userdata('role_id');

        if ($role_id == 4) {
            $dosen_id = $this->session->userdata('user_id');
            $posisi = $this->input->get('posisi') ?: 1;
            
            $data['title'] = 'Dashboard Bimbingan Dosen';
            $data['posisi'] = $posisi;
            $data['students'] = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
            
            foreach ($data['students'] as &$student) {
                $student['preview1'] = $this->Mahasiswa_model->get_riwayat_preview($student['nim'], 'Preview 1');
                $student['preview2'] = $this->Mahasiswa_model->get_riwayat_preview($student['nim'], 'Preview 2');
                $student['preview3'] = $this->Mahasiswa_model->get_riwayat_preview($student['nim'], 'Preview 3');
            }
            
            $this->load->view('mahasiswa/dosen_bimbingan', $data);
            return;
        }

        $nim = $this->_get_current_nim();
        $data['title'] = 'Bimbingan & Evaluasi Preview TA';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        
        $pembimbing_penguji = $this->Mahasiswa_model->get_pembimbing_penguji($nim);
        
        // Riwayat tiap tahapan preview
        $data['riwayat_preview1'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 1');
        $data['riwayat_preview2'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 2');
        $data['riwayat_preview3'] = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 3');

        // Total upload count
        $data['upload_count_p1'] = count($data['riwayat_preview1']);
        $data['upload_count_p2'] = count($data['riwayat_preview2']);
        $data['upload_count_p3'] = count($data['riwayat_preview3']);

        // Status terbaru
        $data['latest_p1'] = $data['riwayat_preview1'][0] ?? null;
        $data['latest_p2'] = $data['riwayat_preview2'][0] ?? null;
        $data['latest_p3'] = $data['riwayat_preview3'][0] ?? null;

        // Default Mock Pembimbing & Penguji jika belum di-set di database
        $data['pembimbing_1'] = !empty($pembimbing_penguji['pembimbing_1']) 
            ? $pembimbing_penguji['pembimbing_1'] 
            : (!empty($data['pendaftaran']['nama_pembimbing_1']) ? $data['pendaftaran']['nama_pembimbing_1'] : 'Dr. Rina Fitriana, S.Ds., M.Ds.');
        $data['pembimbing_2'] = !empty($pembimbing_penguji['pembimbing_2']) 
            ? $pembimbing_penguji['pembimbing_2'] 
            : (!empty($data['pendaftaran']['nama_pembimbing_2']) ? $data['pendaftaran']['nama_pembimbing_2'] : 'Agung Pratama, S.T., M.Kom.');
        $data['penguji_ta'] = !empty($pembimbing_penguji['penguji_1']) 
            ? $pembimbing_penguji['penguji_1'] 
            : 'Bambang Sudarsono, S.T., M.T.';

        $this->load->view('mahasiswa/bimbingan_preview1', $data);
    }

    // Alias route preview1
    public function preview1() {
        $this->bimbingan();
    }

    // Endpoint Upload Draft Berkas Preview (Preview 1 / 2 / 3)
    public function upload_preview() {
        $nim = $this->_get_current_nim();
        $tahap = $this->input->post('tahap_preview') ?: 'Preview 1';

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|docx|zip';
        $config['max_size']      = 10240; // 10MB
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

            $data_insert = array(
                'nim'                => $nim,
                'tahap_preview'      => $tahap,
                'file_draft'         => $file_name,
                'catatan_mahasiswa'  => $catatan,
                'status_pembimbing'  => 'Pending',
                'created_at'         => date('Y-m-d H:i:s')
            );

            $this->Mahasiswa_model->save_upload_preview($data_insert);
            $this->session->set_flashdata('success', "Draft Berkas {$tahap} berhasil diunggah! Menunggu peninjauan dari Dosen Penilai.");
        }

        redirect('mahasiswa/bimbingan');
    }

    // Fallback handler upload_preview1
    public function upload_preview1() {
        $this->upload_preview();
    }

    // Endpoint for Dosen to submit review
    public function review_preview() {
        $role_id = $this->session->userdata('role_id');
        if ($role_id != 4) {
            redirect('mahasiswa/bimbingan');
            return;
        }

        $id = $this->input->post('id_preview');
        $posisi = $this->input->post('posisi');
        $catatan = $this->input->post('catatan_pembimbing');
        $status = $this->input->post('status_pembimbing');

        if ($posisi == 1) {
            $data = array(
                'status_pembimbing' => $status,
                'catatan_pembimbing' => $catatan
            );
            $this->Mahasiswa_model->update_review_preview($id, $data);
            $this->session->set_flashdata('success', 'Review Pembimbing 1 berhasil disimpan.');
        } else if ($posisi == 2) {
            $data = array(
                'catatan_pembimbing_2' => $catatan
            );
            $this->Mahasiswa_model->update_review_preview($id, $data);
            $this->session->set_flashdata('success', 'Komentar Pembimbing 2 berhasil disimpan.');
        }

        redirect('mahasiswa/bimbingan?posisi=' . $posisi);
    }
}
