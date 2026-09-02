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

    // Submit perbaikan berkas revisi langsung dari modal mahasiswa
    public function upload_revisi_berkas() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        if (!$pendaftaran) {
            redirect('mahasiswa');
            return;
        }

        $config['upload_path']   = './uploads/persyaratan_ta/';
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 5120; // 5MB

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        $fields = ['file_ksm', 'file_transkrip', 'file_pernyataan', 'file_bebas_lab'];
        $status_keys = [
            'file_ksm'        => 'status_file_ksm',
            'file_transkrip'  => 'status_file_transkrip',
            'file_pernyataan' => 'status_file_pernyataan',
            'file_bebas_lab'  => 'status_file_bebas_lab'
        ];
        $review_keys = [
            'file_ksm'        => 'review_file_ksm',
            'file_transkrip'  => 'review_file_transkrip',
            'file_pernyataan' => 'review_file_pernyataan',
            'file_bebas_lab'  => 'review_file_bebas_lab'
        ];

        $updated_data = [];
        $uploaded_count = 0;

        foreach ($fields as $f) {
            if (!empty($_FILES[$f]['name'])) {
                $new_file = $this->_do_upload($f, $config);
                if ($new_file) {
                    $updated_data[$f] = $new_file;
                    $updated_data[$status_keys[$f]] = 'Pending';
                    $updated_data[$review_keys[$f]] = 0;
                    $short_key = str_replace('file_', '', $f);
                    $updated_data['catatan_file_' . $short_key] = '';
                    $uploaded_count++;
                }
            }
        }

        if ($this->input->post('judul_1')) {
            $updated_data['judul_1'] = $this->input->post('judul_1');
            $updated_data['status_judul'] = 'Pending';
            $updated_data['catatan_judul'] = '';
        }

        if (!empty($updated_data)) {
            $updated_data['status_approval_wali'] = 'Pending';
            $updated_data['status_approval_admin'] = 'Pending';
            $updated_data['current_stage'] = 'Dosen Wali';
            $updated_data['updated_at'] = date('Y-m-d H:i:s');

            // Reset catatan_wali jika semua item revisi sudah dikirimkan / diperbaiki
            $curr_ksm = isset($updated_data['status_file_ksm']) ? $updated_data['status_file_ksm'] : ($pendaftaran['status_file_ksm'] ?? 'Pending');
            $curr_trn = isset($updated_data['status_file_transkrip']) ? $updated_data['status_file_transkrip'] : ($pendaftaran['status_file_transkrip'] ?? 'Pending');
            $curr_prn = isset($updated_data['status_file_pernyataan']) ? $updated_data['status_file_pernyataan'] : ($pendaftaran['status_file_pernyataan'] ?? 'Pending');
            $curr_lab = isset($updated_data['status_file_bebas_lab']) ? $updated_data['status_file_bebas_lab'] : ($pendaftaran['status_file_bebas_lab'] ?? 'Pending');
            $curr_jud = isset($updated_data['status_judul']) ? $updated_data['status_judul'] : ($pendaftaran['status_judul'] ?? 'Pending');

            $has_any_rejected_left = ($curr_ksm === 'Rejected' || $curr_trn === 'Rejected' || $curr_prn === 'Rejected' || $curr_lab === 'Rejected' || $curr_jud === 'Rejected');

            if (!$has_any_rejected_left) {
                $updated_data['catatan_wali'] = '';
            }

            $this->db->where('nim', $nim)->update('pendaftaran_ta', $updated_data);
            $this->session->set_flashdata('success', 'Berhasil mengunggah ' . ($uploaded_count ? $uploaded_count . ' berkas perbaikan' : 'perubahan usulan') . ' untuk diverifikasi kembali!');
        } else {
            $this->session->set_flashdata('error', 'Tidak ada file baru yang diunggah. Silakan pilih file PDF yang valid.');
        }

        redirect('mahasiswa');
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
        // Form hanya terkunci jika sudah resmi dikirim (is_submitted = 1)
        $has_completed_submission = !empty($pendaftaran['is_submitted']);

        $has_revisi = false;
        $is_locked = false;

        if ($has_completed_submission) {
            $w_status  = $pendaftaran['status_approval_wali'] ?? 'Pending';
            $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
            $k_status  = $pendaftaran['status_approval_koor'] ?? 'Pending';
            $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
            $st_judul  = $pendaftaran['status_judul'] ?? 'Pending';
            $has_revisi = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected' || !empty($pendaftaran['berkas_kurang']) || $st_judul === 'Rejected');
            $is_locked = !$has_revisi;
        }

        $has_ta = !empty($pendaftaran['judul_1']);
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

            // Jika status Dosen Wali tadinya Rejected atau Draft, reset Dosen Wali ke Pending saat dikirim resmi
            if ($w_status === 'Rejected' || $w_status === 'Draft' || empty($w_status)) {
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
                'is_submitted'         => 1,
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

        // Ambil Data Pembimbing & Penguji Asli dari Database
        $data['pembimbing_1'] = !empty($pembimbing_penguji['pembimbing_1']) ? $pembimbing_penguji['pembimbing_1'] : '';
        $data['pembimbing_2'] = !empty($pembimbing_penguji['pembimbing_2']) ? $pembimbing_penguji['pembimbing_2'] : '';
        $data['penguji_ta'] = !empty($pembimbing_penguji['penguji_1']) ? $pembimbing_penguji['penguji_1'] : '';

        // Cek apakah minimal pembimbing 1 dan 2 sudah di-assign
        $data['is_pembimbing_assigned'] = (!empty($data['pembimbing_1']) && !empty($data['pembimbing_2']));

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

    // AJAX Endpoint: Realtime fetch status approval 4-tahap pendaftaran TA mahasiswa
    public function get_status_pendaftaran_ajax() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        if (!$pendaftaran || empty($pendaftaran['judul_1'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'has_ta'  => false,
                    'message' => 'Belum ada pengajuan Tugas Akhir.'
                ]));
            return;
        }

        $w_status  = $pendaftaran['status_approval_wali']  ?? 'Pending';
        $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
        $k_status  = $pendaftaran['status_approval_koor']  ?? 'Pending';
        $kk_status = $pendaftaran['status_approval_kk']    ?? 'Pending';

        $approved_count = 0;
        if ($w_status === 'Approved') $approved_count++;
        if ($a_status === 'Approved') $approved_count++;
        if ($k_status === 'Approved') $approved_count++;
        if ($kk_status === 'Approved') $approved_count++;

        $progress_pct = round(($approved_count / 4) * 100);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'        => true,
                'has_ta'         => true,
                'nim'            => $nim,
                'current_stage'  => $pendaftaran['current_stage'] ?? 'Dosen Wali',
                'status_wali'    => $w_status,
                'status_admin'   => $a_status,
                'status_koor'    => $k_status,
                'status_kk'      => $kk_status,
                'approved_count' => $approved_count,
                'progress_pct'   => $progress_pct,
                'judul_1'        => $pendaftaran['judul_1'] ?? ''
            ]));
    }

    // AJAX Endpoint: Instant Background Auto-Upload berkas persyaratan TA (Step 3-6)
    public function ajax_upload_file_ta() {
        $nim = $this->_get_current_nim();
        $field_name = $this->input->post('field_name'); // 'file_ksm', 'file_transkrip', 'file_pernyataan', 'file_bebas_lab'

        $allowed_fields = ['file_ksm', 'file_transkrip', 'file_pernyataan', 'file_bebas_lab'];
        if (!in_array($field_name, $allowed_fields)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Field tidak valid.']));
            return;
        }

        $upload_dir = './uploads/persyaratan_ta/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 5120; // 5MB
        $config['file_name']     = $field_name . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];

            $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);
            $mhs_konsentrasi = !empty($mhs['konsentrasi_dkv']) ? $mhs['konsentrasi_dkv'] : 'Desain Komunikasi Visual';
            $mhs_id_kk = !empty($mhs['id_kk']) ? $mhs['id_kk'] : 1;

            // Simpan / update ke database sebagai draft
            $existing_ta = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
            if ($existing_ta) {
                $upData = [$field_name => $file_name];
                if (empty($existing_ta['konsentrasi_dkv'])) $upData['konsentrasi_dkv'] = $mhs_konsentrasi;
                if (empty($existing_ta['id_kk'])) $upData['id_kk'] = $mhs_id_kk;
                $this->db->where('nim', $nim)->update('pendaftaran_ta', $upData);
            } else {
                $this->db->insert('pendaftaran_ta', [
                    'nim'                  => $nim,
                    'konsentrasi_dkv'      => $mhs_konsentrasi,
                    'id_kk'                => $mhs_id_kk,
                    'is_submitted'         => 0,
                    'status_approval_wali' => 'Draft',
                    'current_stage'        => 'Draft',
                    $field_name            => $file_name,
                    'created_at'           => date('Y-m-d H:i:s')
                ]);
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success'    => true,
                    'field_name' => $field_name,
                    'file_name'  => $file_name,
                    'file_size'  => number_format($upload_data['file_size'] / 1024, 2) . ' MB',
                    'file_url'   => base_url('uploads/persyaratan_ta/' . $file_name),
                    'message'    => 'Berkas berhasil diunggah dan tersimpan di server.'
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => strip_tags($this->upload->display_errors())
                ]));
        }
    }

    // AJAX Endpoint: Auto-Save Draft Teks (Jenis TA & Judul) ke Database Server
    public function ajax_save_draft_ta() {
        $nim = $this->_get_current_nim();
        $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);
        $mhs_konsentrasi = !empty($mhs['konsentrasi_dkv']) ? $mhs['konsentrasi_dkv'] : 'Desain Komunikasi Visual';
        $mhs_id_kk = !empty($mhs['id_kk']) ? $mhs['id_kk'] : 1;

        $jenis_ta = $this->input->post('jenis_ta', true);
        $judul_1  = $this->input->post('judul_1', true);
        $judul_2  = $this->input->post('judul_2', true);
        $judul_3  = $this->input->post('judul_3', true);
        $judul_en = $this->input->post('judul_en', true);
        $konsentrasi_dkv = $this->input->post('konsentrasi_dkv', true) ?: $mhs_konsentrasi;

        $draft_step = (int)$this->input->post('draft_step', true);

        $data_update = array();
        if ($jenis_ta !== null && $jenis_ta !== '') $data_update['jenis_ta'] = $jenis_ta;
        if ($judul_1 !== null)  $data_update['judul_1'] = $judul_1;
        if ($judul_2 !== null)  $data_update['judul_2'] = $judul_2;
        if ($judul_3 !== null)  $data_update['judul_3'] = $judul_3;
        if ($judul_en !== null) $data_update['judul_en'] = $judul_en;
        if ($draft_step >= 1 && $draft_step <= 6) $data_update['draft_step'] = $draft_step;
        $data_update['konsentrasi_dkv'] = $konsentrasi_dkv;
        $data_update['id_kk'] = $mhs_id_kk;

        $data_update['updated_at'] = date('Y-m-d H:i:s');

        $existing = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
        if ($existing) {
            $this->db->where('nim', $nim)->update('pendaftaran_ta', $data_update);
        } else {
            $data_update['nim'] = $nim;
            $data_update['created_at'] = date('Y-m-d H:i:s');
            $data_update['is_submitted'] = 0;
            $data_update['status_approval_wali'] = 'Draft';
            $data_update['status_approval_admin'] = 'Pending';
            $data_update['status_approval_koor'] = 'Pending';
            $data_update['status_approval_kk'] = 'Pending';
            $data_update['current_stage'] = 'Draft';
            $this->db->insert('pendaftaran_ta', $data_update);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true, 'message' => 'Draft berhasil tersimpan di server.']));
    }

    // AJAX Endpoint: Get list of students and their previews for Dosen Bimbingan
    public function ajax_get_dosen_bimbingan() {
        header('Content-Type: application/json');
        
        $role_id = $this->session->userdata('role_id');
        if ($role_id != 4) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            $dosen_id = $this->session->userdata('user_id');
            $posisi = $this->input->get('posisi') ?: 1;
            $tahap = $this->input->get('tahap') ?: 'Preview 1';
            
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
            
            $data = [];
            $total = count($students);
            
            foreach ($students as $student) {
                $previews = $this->Mahasiswa_model->get_riwayat_preview($student['nim'], $tahap);
                $latest = !empty($previews) ? $previews[0] : null;
                
                $data[] = [
                    'nim' => $student['nim'],
                    'nama_mahasiswa' => $student['nama_mahasiswa'] ?? $student['nim'],
                    'judul' => $student['judul'] ?? '-',
                    'konsentrasi_dkv' => $student['konsentrasi_dkv'] ?? '',
                    'latest_preview' => $latest
                ];
            }

            echo json_encode([
                'status' => true,
                'data' => $data,
                'stats' => [
                    'total' => $total
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // AJAX Endpoint: Get preview log for logged in Mahasiswa
    public function ajax_get_preview_log() {
        header('Content-Type: application/json');
        
        $nim = $this->_get_current_nim();
        $tahap = $this->input->get('tahap');
        
        if (empty($tahap)) {
            echo json_encode(['status' => false, 'message' => 'Tahap tidak valid']);
            return;
        }
        
        $riwayat = $this->Mahasiswa_model->get_riwayat_preview($nim, $tahap);
        
        echo json_encode([
            'status' => true,
            'data' => $riwayat
        ]);
    }
    // AJAX Endpoint: Upload draft preview mahasiswa tanpa reload
    public function upload_preview_ajax() {
        header('Content-Type: application/json');
        $nim = $this->_get_current_nim();
        $tahap = $this->input->post('tahap_preview') ?: 'Preview 1';

        $upload_dir = './uploads/preview_ta/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|docx|zip';
        $config['max_size']      = 10240;
        $clean_tahap = str_replace(' ', '', strtoupper($tahap));
        $config['file_name']     = $clean_tahap . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file_draft')) {
            echo json_encode([
                'status' => false,
                'message' => $this->upload->display_errors('', '')
            ]);
            return;
        }

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
        
        $riwayat = $this->Mahasiswa_model->get_riwayat_preview($nim, $tahap);
        $latest = $riwayat[0] ?? null;

        echo json_encode([
            'status' => true,
            'message' => "Draft Berkas {$tahap} berhasil diunggah!",
            'tahap' => $tahap,
            'upload_count' => count($riwayat),
            'latest_preview' => $latest,
            'riwayat' => $riwayat
        ]);
    }

    // AJAX Endpoint: Review dosen tanpa reload
    public function review_preview_ajax() {
        header('Content-Type: application/json');
        $role_id = $this->session->userdata('role_id');
        if ($role_id != 4) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id = $this->input->post('id_preview');
        $posisi = $this->input->post('posisi');
        $catatan = $this->input->post('catatan_pembimbing');
        $status = $this->input->post('status_pembimbing');

        if (empty($id)) {
            echo json_encode(['status' => false, 'message' => 'ID preview tidak valid']);
            return;
        }

        if ($posisi == 1) {
            $data = [
                'status_pembimbing' => $status,
                'catatan_pembimbing' => $catatan
            ];
            $this->Mahasiswa_model->update_review_preview($id, $data);
            $message = 'Review Pembimbing 1 berhasil disimpan.';
        } else if ($posisi == 2) {
            $data = [
                'catatan_pembimbing_2' => $catatan
            ];
            $this->Mahasiswa_model->update_review_preview($id, $data);
            $message = 'Komentar Pembimbing 2 berhasil disimpan.';
        } else {
            echo json_encode(['status' => false, 'message' => 'Posisi tidak valid']);
            return;
        }

        echo json_encode([
            'status' => true,
            'message' => $message
        ]);
    }

    public function sse_dosen_bimbingan() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); 
        session_write_close(); // FIX FOR MAX EXECUTION TIME

        $role_id = $this->session->userdata('role_id');
        if ($role_id != 4) {
            echo "data: " . json_encode(['status' => false, 'message' => 'Unauthorized']) . "\n\n";
            ob_flush(); flush(); exit;
        }

        $dosen_id = $this->session->userdata('user_id');
        $posisi = $this->input->get('posisi') ?: 1;
        $lastData = null;

        while (true) {
            $students = $this->Mahasiswa_model->get_students_by_dosen($dosen_id, $posisi);
            $data = [];
            foreach ($students as $student) {
                $p1 = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 1');
                $p2 = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 2');
                $p3 = $this->Mahasiswa_model->get_latest_preview_status($student['nim'], 'Preview 3');
                $data[] = [
                    'nim' => $student['nim'],
                    'nama_mahasiswa' => $student['nama_mahasiswa'] ?? $student['nim'],
                    'judul' => $student['judul'] ?? '-',
                    'preview1' => $p1,
                    'preview2' => $p2,
                    'preview3' => $p3
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

    public function sse_mahasiswa_bimbingan() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        session_write_close(); // FIX FOR MAX EXECUTION TIME

        $nim = $this->_get_current_nim();
        $lastData = null;

        while (true) {
            $riwayat_p1 = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 1');
            $riwayat_p2 = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 2');
            $riwayat_p3 = $this->Mahasiswa_model->get_riwayat_preview($nim, 'Preview 3');

            $data = [
                'riwayat_p1' => $riwayat_p1,
                'riwayat_p2' => $riwayat_p2,
                'riwayat_p3' => $riwayat_p3,
                'latest_p1' => $riwayat_p1[0] ?? null,
                'latest_p2' => $riwayat_p2[0] ?? null,
                'latest_p3' => $riwayat_p3[0] ?? null,
                'upload_count_p1' => count($riwayat_p1),
                'upload_count_p2' => count($riwayat_p2),
                'upload_count_p3' => count($riwayat_p3),
                'is_p1_app' => (bool)(($riwayat_p1[0]['status_pembimbing'] ?? null) == 'Approved'),
                'is_p2_app' => (bool)(($riwayat_p2[0]['status_pembimbing'] ?? null) == 'Approved'),
                'is_p3_app' => (bool)(($riwayat_p3[0]['status_pembimbing'] ?? null) == 'Approved'),
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
}
