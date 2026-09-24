<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mahasiswa_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));

        if (!$this->session->userdata('logged_in')) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Sesi berakhir, silakan login kembali.']));
                exit;
            }
            redirect('login');
            return;
        }
    }

    private function _get_current_nim() {
        return $this->session->userdata('nim') ?: ($this->session->userdata('nidn_nim') ?: '1301210001');
    }

    private function _do_upload($field_name, $config) {
        $this->upload->initialize($config);
        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
        return null;
    }

    // =========================================================
    // DASHBOARD & PROFIL
    // =========================================================

    public function index() {
        $nim = $this->_get_current_nim();
        $this->load->model('AdminLayanan_model');
        $data['title']          = 'Dashboard Mahasiswa';
        $data['mahasiswa']      = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran']    = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['student_berkas'] = $this->AdminLayanan_model->get_student_berkas_map($nim);

        $this->load->view('mahasiswa/dashboard', $data);
    }

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

    public function translate_judul() {
        header('Content-Type: application/json');
        $text = trim($this->input->post('text') ?? '');

        if (empty($text)) {
            echo json_encode(['status' => 'error', 'message' => 'Teks judul tidak boleh kosong!']);
            return;
        }

        $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=id&tl=en&dt=t&q=" . urlencode($text);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if (!$err && $http_code === 200 && !empty($response)) {
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

        $url_fallback = "https://api.mymemory.translated.net/get?q=" . urlencode($text) . "&langpair=id|en";
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $url_fallback);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 3);
        $fallback_res = curl_exec($ch2);
        curl_close($ch2);

        if (!empty($fallback_res)) {
            $json = json_decode($fallback_res, true);
            $translated = $json['responseData']['translatedText'] ?? '';
            if ($translated) {
                echo json_encode(['status' => 'success', 'translated' => trim($translated)]);
                return;
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'Gagal menerjemahkan secara online, silakan ketik judul Inggris secara manual.']);
    }

    // =========================================================
    // PENDAFTARAN TA
    // =========================================================

    public function detail_pendaftaran() {
        $nim = $this->_get_current_nim();
        $data['title'] = 'Detail Pengajuan Tugas Akhir';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $this->load->view('mahasiswa/detail_pendaftaran', $data);
    }

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
            $config['max_size']      = 5120;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            $file_step3 = $this->_do_upload('file_ksm', $config);
            $file_step4 = $this->_do_upload('file_transkrip', $config);
            $file_step5 = $this->_do_upload('file_pernyataan', $config);
            $file_step6 = $this->_do_upload('file_bebas_lab', $config);

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

    public function upload_revisi_berkas() {
        $nim = $this->_get_current_nim();
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
        if (!$pendaftaran) {
            redirect('mahasiswa');
            return;
        }

        $this->load->model('AdminLayanan_model');
        $active_syarat = $this->AdminLayanan_model->get_active_syarat_berkas();
        if (empty($active_syarat)) {
            $active_syarat = array(
                array('kode_berkas' => 'ksm'),
                array('kode_berkas' => 'transkrip'),
                array('kode_berkas' => 'pernyataan'),
                array('kode_berkas' => 'bebas_lab')
            );
        }

        $config['upload_path']   = './uploads/persyaratan_ta/';
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 5120;

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, true);
        }

        $this->load->library('upload', $config);

        $updated_data = [];
        $uploaded_count = 0;

        foreach ($active_syarat as $sb) {
            $k = $sb['kode_berkas'];
            $f = 'file_' . $k;
            if (!empty($_FILES[$f]['name'])) {
                $new_file = $this->_do_upload($f, $config);
                if ($new_file) {
                    $uploaded_count++;
                    $this->AdminLayanan_model->save_student_berkas($nim, $k, $new_file, 'Pending');

                    // 2. Simpan ke file_pendaftaran jika ada
                    if ($this->db->table_exists('file_pendaftaran')) {
                        $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
                        $this->db->group_start()
                            ->where_in('id_mhs', $target_ids)
                            ->or_like('id_mhs', $nim)
                            ->group_end()
                            ->like('nama', $k)
                            ->update('file_pendaftaran', [
                                'file'            => 'uploads/persyaratan_ta/' . $new_file,
                                'status_doswal'   => 'Pending',
                                'status_adminlaa' => 'Pending',
                                'komentar'        => '',
                                'date_edit'       => date('Y-m-d H:i:s')
                            ]);
                    }

                    // 3. Simpan ke pendaftaran_ta jika tabel dan kolomnya ada
                    if ($this->db->table_exists('pendaftaran_ta')) {
                        if ($this->db->field_exists($f, 'pendaftaran_ta')) {
                            $updated_data[$f] = $new_file;
                        }
                        $col_status  = 'status_file_' . $k;
                        $col_review  = 'review_file_' . $k;
                        $col_catatan = 'catatan_file_' . $k;
                        if ($this->db->field_exists($col_status, 'pendaftaran_ta')) {
                            $updated_data[$col_status] = 'Pending';
                        }
                        $col_legacy_status = 'status_' . $k;
                        if ($this->db->field_exists($col_legacy_status, 'pendaftaran_ta')) {
                            $updated_data[$col_legacy_status] = 'Pending';
                        }
                        if ($this->db->field_exists($col_review, 'pendaftaran_ta')) {
                            $updated_data[$col_review] = 0;
                        }
                        if ($this->db->field_exists($col_catatan, 'pendaftaran_ta')) {
                            $updated_data[$col_catatan] = '';
                        }
                    }
                }
            }
        }

        if ($this->input->post('judul_1')) {
            $new_j = trim($this->input->post('judul_1'));
            if (!empty($new_j)) {
                $updated_data['judul_1'] = $new_j;
                $updated_data['status_judul'] = 'Pending';
                $updated_data['catatan_judul'] = '';
                if ($this->db->table_exists('guidance')) {
                    $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
                    $this->db->where_in('id_mhs', $target_ids)->update('guidance', [
                        'judul_1'    => $new_j,
                        'keterangan' => 'Pending',
                        'komentar'   => ''
                    ]);
                }
            }
        }

        if ($this->input->post('jenis_ta')) {
            $new_jen = trim($this->input->post('jenis_ta'));
            $updated_data['jenis_ta'] = $new_jen;
            $updated_data['status_jenis_ta'] = 'Pending';
            $updated_data['catatan_jenis_ta'] = '';
            if ($this->db->table_exists('guidance')) {
                $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
                $this->db->where_in('id_mhs', $target_ids)->update('guidance', [
                    'jenis_TA' => $new_jen
                ]);
            }
        }

        if (!empty($updated_data) || $uploaded_count > 0) {
            $prev_w_st  = $pendaftaran['status_approval_wali'] ?? 'Pending';
            $prev_a_st  = $pendaftaran['status_approval_admin'] ?? 'Pending';
            $prev_k_st  = $pendaftaran['status_approval_koor'] ?? 'Pending';
            $prev_kk_st = $pendaftaran['status_approval_kk'] ?? 'Pending';

            if ($prev_w_st === 'Rejected') $updated_data['status_approval_wali'] = 'Pending';
            if ($prev_a_st === 'Rejected' || !empty($pendaftaran['berkas_kurang'])) {
                $updated_data['status_approval_admin'] = 'Pending';
                $updated_data['berkas_kurang'] = NULL;
                $updated_data['catatan_admin'] = '';
            }
            if ($prev_kk_st === 'Rejected') {
                $updated_data['status_approval_kk'] = 'Pending';
                $updated_data['catatan_kk'] = '';
            }

            $new_w_st  = $updated_data['status_approval_wali'] ?? $prev_w_st;
            $new_a_st  = $updated_data['status_approval_admin'] ?? $prev_a_st;
            $new_k_st  = $updated_data['status_approval_koor'] ?? $prev_k_st;
            $new_kk_st = $updated_data['status_approval_kk'] ?? $prev_kk_st;

            if ($new_w_st !== 'Approved') {
                $updated_data['current_stage'] = 'Dosen Wali';
            } else if ($new_a_st !== 'Approved') {
                $updated_data['current_stage'] = 'Admin Layanan';
            } else if ($new_k_st !== 'Approved') {
                $updated_data['current_stage'] = 'Koordinator TA';
            } else if ($new_kk_st !== 'Approved') {
                $updated_data['current_stage'] = 'Ketua KK';
            } else {
                $updated_data['current_stage'] = 'Selesai Approval';
            }

            $updated_data['updated_at'] = date('Y-m-d H:i:s');

            $latest_berkas_map = $this->AdminLayanan_model->get_student_berkas_map($nim);
            $has_any_rejected_left = false;
            foreach ($active_syarat as $sb) {
                $k = $sb['kode_berkas'];
                $col_status = 'status_file_' . $k;
                if (isset($updated_data[$col_status])) {
                    $st = $updated_data[$col_status];
                } else {
                    $ver_laa = $latest_berkas_map[$k]['status_verifikasi'] ?? ($pendaftaran['status_' . $k] ?? null);
                    if ($ver_laa === 'Invalid') {
                        $st = 'Rejected';
                    } else {
                        $st = $pendaftaran[$col_status] ?? 'Pending';
                    }
                }
                if ($st === 'Rejected') { $has_any_rejected_left = true; break; }
            }
            if (!$has_any_rejected_left && ($updated_data['status_judul'] ?? $pendaftaran['status_judul'] ?? 'Pending') === 'Rejected') {
                $has_any_rejected_left = true;
            }
            if (!$has_any_rejected_left && ($updated_data['status_jenis_ta'] ?? $pendaftaran['status_jenis_ta'] ?? 'Pending') === 'Rejected') {
                $has_any_rejected_left = true;
            }

            if (!$has_any_rejected_left) {
                $updated_data['catatan_wali'] = '';
                if ($this->db->table_exists('guidance')) {
                    $target_ids = ['usr_mhs_' . $nim, 'mhs_' . $nim, $nim];
                    $this->db->where_in('id_mhs', $target_ids)->update('guidance', [
                        'komentar' => ''
                    ]);
                }
            }

            if ($this->db->table_exists('pendaftaran_ta')) {
                $this->db->where('nim', $nim)->update('pendaftaran_ta', $updated_data);
            }
            $this->session->set_flashdata('success', 'Berhasil mengunggah ' . ($uploaded_count ? $uploaded_count . ' berkas perbaikan' : 'perubahan usulan') . ' untuk diverifikasi kembali!');
        } else {
            $this->session->set_flashdata('error', 'Tidak ada file baru yang diunggah. Silakan pilih file PDF yang valid.');
        }

        redirect('mahasiswa');
    }

    public function pendaftaran_ta() {
        $nim = $this->_get_current_nim();

        $this->load->model('AdminLayanan_model');
        $student_berkas = $this->AdminLayanan_model->get_student_berkas_map($nim);
        $pendaftaran = $this->Mahasiswa_model->get_status_pendaftaran($nim);
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

        $has_ta = !empty($pendaftaran['jenis_ta']) || !empty($pendaftaran['judul_1']) || !empty($pendaftaran['file_ksm']) || !empty($student_berkas);

        $server_draft_step = 1;
        if (!empty($pendaftaran) || !empty($student_berkas)) {
            $saved_step = !empty($pendaftaran['draft_step']) ? (int)$pendaftaran['draft_step'] : 0;
            if ($saved_step >= 4) $saved_step = 2;

            if ($saved_step >= 1 && $saved_step <= 3) {
                $server_draft_step = $saved_step;
            } else {
                $has_any_file = !empty($pendaftaran['file_ksm']) || !empty($pendaftaran['file_transkrip']) || !empty($pendaftaran['file_pernyataan']) || !empty($pendaftaran['file_bebas_lab']) || !empty($student_berkas);
                $has_step1 = !empty($pendaftaran['jenis_ta']) && !empty($pendaftaran['judul_1']);
                if ($has_any_file) {
                    $server_draft_step = 2;
                } elseif ($has_step1) {
                    $server_draft_step = 2;
                } else {
                    $server_draft_step = 1;
                }
            }

            if ($server_draft_step > 3) $server_draft_step = 3;
            if ($server_draft_step < 1) $server_draft_step = 1;
        }

        $data['title']          = $is_locked ? 'Pendaftaran Tugas Akhir (Sedang Ditinjau)' : 'Pendaftaran Tugas Akhir (3 Step)';
        $data['mahasiswa']      = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran']    = $pendaftaran;
        $data['is_locked']      = $is_locked;
        $data['has_revisi']     = $has_revisi;
        $data['has_ta']         = $has_ta;
        $data['server_draft_step'] = $server_draft_step;
        $data['syarat_berkas']  = $this->AdminLayanan_model->get_active_syarat_berkas();
        $data['student_berkas'] = $student_berkas;

        if ($this->input->post()) {
            $config['upload_path']   = './uploads/persyaratan_ta/';
            $config['allowed_types'] = 'pdf';
            $config['max_size']      = 5120;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);

            $active_syarat = $data['syarat_berkas'];
            $file_uploads = [];

            foreach ($active_syarat as $sb) {
                $kode = $sb['kode_berkas'];
                $field_name = 'file_' . $kode;
                $uploaded = $this->_do_upload($field_name, $config);

                if ($uploaded) {
                    $file_uploads[$kode] = $uploaded;
                    $this->AdminLayanan_model->save_student_berkas($nim, $kode, $uploaded, 'Pending', $sb['nama_berkas'] ?? null);
                } else {
                    $old = $this->input->post($field_name . '_old');
                    if ($old) {
                        $file_uploads[$kode] = $old;
                    }
                }
            }

            $file_step3 = $file_uploads['ksm'] ?? $this->_do_upload('file_ksm', $config);
            $file_step4 = $file_uploads['transkrip'] ?? $this->_do_upload('file_transkrip', $config);
            $file_step5 = $file_uploads['pernyataan'] ?? $this->_do_upload('file_pernyataan', $config);
            $file_step6 = $file_uploads['bebas_lab'] ?? $this->_do_upload('file_bebas_lab', $config);

            $existing_ta = $this->db->table_exists('pendaftaran_ta')
                ? $this->db->get_where('pendaftaran_ta', array('nim' => $nim))->row_array()
                : $this->Mahasiswa_model->get_status_pendaftaran($nim);

            $w_status  = isset($existing_ta['status_approval_wali']) ? $existing_ta['status_approval_wali'] : 'Pending';
            $a_status  = isset($existing_ta['status_approval_admin']) ? $existing_ta['status_approval_admin'] : 'Pending';
            $k_status  = isset($existing_ta['status_approval_koor']) ? $existing_ta['status_approval_koor'] : 'Pending';
            $kk_status = isset($existing_ta['status_approval_kk']) ? $existing_ta['status_approval_kk'] : 'Pending';

            if ($w_status === 'Rejected' || $w_status === 'Draft' || empty($w_status)) {
                $w_status = 'Pending';
            }

            $has_new_uploads = !empty($file_step3) || !empty($file_step4) || !empty($file_step5) || !empty($file_step6);
            if ($a_status === 'Rejected' || ($a_status === 'Approved' && $has_new_uploads) || $has_new_uploads) {
                $a_status = 'Pending';
            }

            if ($k_status === 'Rejected') $k_status = 'Pending';
            if ($kk_status === 'Rejected') $kk_status = 'Pending';

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

            if ($file_step3) $data_ta['status_ksm'] = 'Pending';
            if ($file_step4) $data_ta['status_transkrip'] = 'Pending';
            if ($file_step5) $data_ta['status_pernyataan'] = 'Pending';
            if ($file_step6) $data_ta['status_bebas_lab'] = 'Pending';

            $file_updates = array(
                'ksm'        => $file_step3,
                'transkrip'  => $file_step4,
                'pernyataan' => $file_step5,
                'bebas_lab'  => $file_step6
            );
            if ($this->db->table_exists('pendaftaran_berkas')) {
                foreach ($file_updates as $f_code => $f_name) {
                    if (!empty($f_name)) {
                        $ex_b = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim, 'kode_berkas' => $f_code])->row_array();
                        if ($ex_b) {
                            $this->db->where('id', $ex_b['id'])->update('pendaftaran_berkas', [
                                'file_name'         => $f_name,
                                'status_verifikasi' => 'Pending',
                                'updated_at'        => date('Y-m-d H:i:s')
                            ]);
                        } else {
                            $this->db->insert('pendaftaran_berkas', [
                                'nim'               => $nim,
                                'kode_berkas'       => $f_code,
                                'file_name'         => $f_name,
                                'status_verifikasi' => 'Pending',
                                'created_at'        => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

            if ($a_status === 'Pending' && !empty($existing_ta['berkas_kurang'])) {
                $data_ta['berkas_kurang'] = NULL;
            }

            $this->Mahasiswa_model->save_pendaftaran_ta($data_ta);
            $this->session->set_flashdata('success', 'Pendaftaran / Revisi Tugas Akhir berhasil disimpan!');
            redirect('mahasiswa');
        }

        $this->load->view('mahasiswa/pendaftaran_ta', $data);
    }

    public function reset_pendaftaran() {
        $nim = $this->_get_current_nim();
        $this->Mahasiswa_model->reset_pendaftaran_ta($nim);
        $this->session->set_flashdata('success', 'Pengajuan Tugas Akhir berhasil di-reset!');
        redirect('mahasiswa');
    }

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
                'status_jenis_ta' => $pendaftaran['status_jenis_ta'] ?? 'Pending',
                'status_judul'    => $pendaftaran['status_judul'] ?? 'Pending',
                'status_file_ksm' => $pendaftaran['status_file_ksm'] ?? 'Pending',
                'status_file_transkrip' => $pendaftaran['status_file_transkrip'] ?? 'Pending',
                'status_file_pernyataan' => $pendaftaran['status_file_pernyataan'] ?? 'Pending',
                'status_file_bebas_lab' => $pendaftaran['status_file_bebas_lab'] ?? 'Pending',
                'updated_at'     => $pendaftaran['updated_at'] ?? '',
                'approved_count' => $approved_count,
                'progress_pct'   => $progress_pct,
                'judul_1'        => $pendaftaran['judul_1'] ?? ''
            ]));
    }

    public function ajax_upload_file_ta() {
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $field_name = $this->input->post('field_name');

        if (empty($field_name) || !preg_match('/^file_[a-z0-9_]+$/i', $field_name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Field berkas tidak valid.']));
            return;
        }

        $upload_dir = './uploads/persyaratan_ta/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 5120;
        $config['file_name']     = $field_name . '_' . $nim . '_' . time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];

            $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);
            $mhs_konsentrasi = !empty($mhs['konsentrasi_dkv']) ? $mhs['konsentrasi_dkv'] : 'Desain Komunikasi Visual';
            $mhs_id_kk = !empty($mhs['id_kk']) ? $mhs['id_kk'] : 1;

            $kode_berkas = str_replace('file_', '', $field_name);

            if ($this->db->table_exists('pendaftaran_ta')) {
                $existing_ta = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
                $upData = [];
                if ($this->db->field_exists($field_name, 'pendaftaran_ta')) {
                    $upData[$field_name] = $file_name;
                }
                $upData['draft_step'] = 2;
                $upData['updated_at'] = date('Y-m-d H:i:s');

                if ($existing_ta) {
                    if (empty($existing_ta['konsentrasi_dkv'])) $upData['konsentrasi_dkv'] = $mhs_konsentrasi;
                    if (empty($existing_ta['id_kk'])) $upData['id_kk'] = $mhs_id_kk;
                    if (isset($existing_ta['status_approval_admin']) && in_array($existing_ta['status_approval_admin'], ['Approved', 'Rejected'])) {
                        $upData['status_approval_admin'] = 'Pending';
                        if (($existing_ta['status_approval_wali'] ?? '') === 'Approved') {
                            $upData['current_stage'] = 'Admin Layanan';
                        }
                    }
                    $this->db->where('nim', $nim)->update('pendaftaran_ta', $upData);
                } else {
                    $upData['nim']                  = $nim;
                    $upData['konsentrasi_dkv']      = $mhs_konsentrasi;
                    $upData['id_kk']                = $mhs_id_kk;
                    $upData['is_submitted']         = 0;
                    $upData['status_approval_wali'] = 'Draft';
                    $upData['status_approval_admin'] = 'Pending';
                    $upData['status_approval_koor'] = 'Pending';
                    $upData['status_approval_kk']   = 'Pending';
                    $upData['current_stage']        = 'Draft';
                    $upData['created_at']           = date('Y-m-d H:i:s');
                    $this->db->insert('pendaftaran_ta', $upData);
                }
            }

            $this->load->model('AdminLayanan_model');
            $req_nama_berkas = $this->input->post('nama_berkas');
            if (empty($req_nama_berkas)) {
                $sb_row = $this->db->get_where('syarat_berkas_ta', ['kode_berkas' => $kode_berkas])->row_array();
                if ($sb_row) $req_nama_berkas = $sb_row['nama_berkas'];
            }

            if (method_exists($this->AdminLayanan_model, 'save_student_berkas')) {
                $this->AdminLayanan_model->save_student_berkas($nim, $kode_berkas, $file_name, 'Pending', $req_nama_berkas);
            }

            $kode_berkas = str_replace('file_', '', $field_name);
            if ($this->db->table_exists('pendaftaran_berkas')) {
                $existing_berkas = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim, 'kode_berkas' => $kode_berkas])->row_array();
                if ($existing_berkas) {
                    $up_data = [
                        'file_name'         => $file_name,
                        'status_verifikasi' => 'Pending',
                        'updated_at'        => date('Y-m-d H:i:s')
                    ];
                    if (!empty($req_nama_berkas)) $up_data['nama_berkas'] = $req_nama_berkas;
                    $this->db->where('id', $existing_berkas['id'])->update('pendaftaran_berkas', $up_data);
                } else {
                    $this->db->insert('pendaftaran_berkas', [
                        'nim'               => $nim,
                        'kode_berkas'       => $kode_berkas,
                        'nama_berkas'       => $req_nama_berkas,
                        'file_name'         => $file_name,
                        'status_verifikasi' => 'Pending',
                        'created_at'        => date('Y-m-d H:i:s')
                    ]);
                }
            }

            if ($this->db->table_exists('file_pendaftaran')) {
                try {
                    $fp_fields = $this->db->list_fields('file_pendaftaran');
                    $id_fp = 'fp_' . $nim . '_' . $kode_berkas;
                    $id_mhs_usr = 'usr_mhs_' . $nim;
                    $ex_fp = $this->db->group_start()
                        ->where('id', $id_fp)
                        ->or_group_start()
                            ->where('id_mhs', $id_mhs_usr)
                            ->where('nama', $kode_berkas)
                        ->group_end()
                    ->group_end()
                    ->get('file_pendaftaran')->row_array();
                    $rel_file_path = 'uploads/persyaratan_ta/' . $file_name;

                    $fp_payload = array();
                    if (in_array('file', $fp_fields)) $fp_payload['file'] = $rel_file_path;
                    if (in_array('status_doswal', $fp_fields)) $fp_payload['status_doswal'] = 'Pending';
                    if (in_array('status_adminlaa', $fp_fields)) $fp_payload['status_adminlaa'] = 'Pending';
                    if (in_array('date_edit', $fp_fields)) $fp_payload['date_edit'] = date('Y-m-d H:i:s');

                    if ($ex_fp) {
                        if (!empty($fp_payload)) {
                            $this->db->where('id', $ex_fp['id'])->update('file_pendaftaran', $fp_payload);
                        }
                    } else {
                        if (in_array('id', $fp_fields)) $fp_payload['id'] = $id_fp;
                        if (in_array('id_mhs', $fp_fields)) $fp_payload['id_mhs'] = $id_mhs_usr;
                        if (in_array('nama', $fp_fields)) $fp_payload['nama'] = $kode_berkas;
                        if (in_array('view_adminlaa', $fp_fields)) $fp_payload['view_adminlaa'] = 0;
                        if (in_array('view_doswal', $fp_fields)) $fp_payload['view_doswal'] = 0;
                        if (in_array('komentar', $fp_fields)) $fp_payload['komentar'] = '';
                        if (in_array('date', $fp_fields)) $fp_payload['date'] = date('Y-m-d H:i:s');
                        $this->db->insert('file_pendaftaran', $fp_payload);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error syncing file_pendaftaran in ajax_upload: ' . $e->getMessage());
                }
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success'    => true,
                    'field_name' => $field_name,
                    'kode_berkas'=> $kode_berkas,
                    'file_name'  => $file_name,
                    'file_size'  => number_format($upload_data['file_size'] / 1024, 2) . ' MB',
                    'file_url'   => base_url('uploads/persyaratan_ta/' . $file_name),
                    'message'    => 'Berkas berhasil diunggah dan tersimpan di database.'
                ]));
        } else {
            $error_msg = $this->upload->display_errors('', '');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $error_msg ?: 'Gagal mengunggah berkas.'
                ]));
        }
    }

    public function ajax_delete_file_ta() {
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $field_name = $this->input->post('field_name');

        if (empty($field_name) || !preg_match('/^file_[a-z0-9_]+$/i', $field_name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Field berkas tidak valid.']));
            return;
        }

        $kode_berkas = str_replace('file_', '', $field_name);

        $pendaftaran = $this->db->table_exists('pendaftaran_ta')
            ? $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array()
            : $this->Mahasiswa_model->get_status_pendaftaran($nim);
        if (!empty($pendaftaran['is_submitted'])) {
            $w_status  = $pendaftaran['status_approval_wali'] ?? 'Pending';
            $a_status  = $pendaftaran['status_approval_admin'] ?? 'Pending';
            $k_status  = $pendaftaran['status_approval_koor'] ?? 'Pending';
            $kk_status = $pendaftaran['status_approval_kk'] ?? 'Pending';
            $st_judul  = $pendaftaran['status_judul'] ?? 'Pending';
            $has_revisi = ($w_status === 'Rejected' || $a_status === 'Rejected' || $k_status === 'Rejected' || $kk_status === 'Rejected' || !empty($pendaftaran['berkas_kurang']) || $st_judul === 'Rejected');
            if (!$has_revisi) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Pendaftaran telah dikunci dan tidak dapat diubah.']));
                return;
            }
        }

        if ($this->db->table_exists('pendaftaran_berkas')) {
            $this->db->where('nim', $nim)->where('kode_berkas', $kode_berkas)->delete('pendaftaran_berkas');
        }

        if ($this->db->table_exists('pendaftaran_ta') && $this->db->field_exists($field_name, 'pendaftaran_ta')) {
            $this->db->where('nim', $nim)->update('pendaftaran_ta', [
                $field_name => '',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'     => true,
                'field_name'  => $field_name,
                'kode_berkas' => $kode_berkas,
                'message'     => 'Berkas berhasil dihapus dari database.'
            ]));
    }

    public function ajax_save_draft_ta() {
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
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
        if ($draft_step >= 1 && $draft_step <= 3) $data_update['draft_step'] = $draft_step;
        $data_update['konsentrasi_dkv'] = $konsentrasi_dkv;
        $data_update['id_kk'] = $mhs_id_kk;

        $this->load->model('AdminLayanan_model');
        $post_data = $this->input->post(null, true);
        $has_any_file = false;

        foreach ($post_data as $key => $val) {
            if (empty($val) || !is_string($val)) continue;
            if (preg_match('/^file_([a-z0-9_]+)$/i', $key)) {
                $clean_key = preg_replace('/_old$/i', '', $key);
                $raw_kode  = str_replace('file_', '', $clean_key);
                $col_name  = 'file_' . $raw_kode;
                $val = trim($val);

                if (!empty($val) && strtolower(substr($val, -4)) === '.pdf') {
                    $has_any_file = true;
                    if ($this->db->table_exists('pendaftaran_ta') && $this->db->field_exists($col_name, 'pendaftaran_ta')) {
                        $data_update[$col_name] = $val;
                    }
                    if ($this->db->table_exists('pendaftaran_berkas') && method_exists($this->AdminLayanan_model, 'save_student_berkas')) {
                        $this->AdminLayanan_model->save_student_berkas($nim, $raw_kode, $val, 'Pending');
                    }
                }
            }
        }

        $data_update['updated_at'] = date('Y-m-d H:i:s');
        $db_saved = false;

        if ($this->db->table_exists('pendaftaran_ta')) {
            $p_fields = $this->db->list_fields('pendaftaran_ta');
            foreach ($data_update as $fk => $fv) {
                if (!in_array($fk, $p_fields)) {
                    unset($data_update[$fk]);
                }
            }

            $existing = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
            if ($existing) {
                $this->db->where('nim', $nim)->update('pendaftaran_ta', $data_update);
                $db_saved = true;
            } else {
                if (!empty($jenis_ta) || !empty($judul_1) || $has_any_file) {
                    if (in_array('nim', $p_fields)) $data_update['nim'] = $nim;
                    if (in_array('created_at', $p_fields)) $data_update['created_at'] = date('Y-m-d H:i:s');
                    if (in_array('is_submitted', $p_fields)) $data_update['is_submitted'] = 0;
                    if (in_array('status_approval_wali', $p_fields)) $data_update['status_approval_wali'] = 'Draft';
                    if (in_array('status_approval_admin', $p_fields)) $data_update['status_approval_admin'] = 'Pending';
                    if (in_array('status_approval_koor', $p_fields)) $data_update['status_approval_koor'] = 'Pending';
                    if (in_array('status_approval_kk', $p_fields)) $data_update['status_approval_kk'] = 'Pending';
                    if (in_array('current_stage', $p_fields)) $data_update['current_stage'] = 'Draft';
                    $this->db->insert('pendaftaran_ta', $data_update);
                    $db_saved = true;
                }
            }
        }

        // Sinkronkan juga ke tabel guidance (legacy / active db)
        if ($this->db->table_exists('guidance')) {
            $existing_g = $this->db->group_start()
                ->where('id_mhs', 'usr_mhs_' . $nim)
                ->or_where('id_mhs', $nim)
            ->group_end()->get('guidance')->row_array();

            $g_fields = $this->db->list_fields('guidance');
            $g_data = array();

            if (!empty($judul_1) && in_array('judul_1', $g_fields)) $g_data['judul_1'] = $judul_1;
            if (!empty($judul_2) && in_array('judul_2', $g_fields)) $g_data['judul_2'] = $judul_2;
            if (!empty($judul_3) && in_array('judul_3', $g_fields)) $g_data['judul_3'] = $judul_3;
            if (!empty($judul_en) && in_array('judul_en', $g_fields)) $g_data['judul_en'] = $judul_en;
            if (!empty($jenis_ta) && in_array('jenis_TA', $g_fields)) $g_data['jenis_TA'] = $jenis_ta;
            if (!empty($konsentrasi_dkv) && in_array('peminatan', $g_fields)) $g_data['peminatan'] = $konsentrasi_dkv;
            if (in_array('date_edit', $g_fields)) $g_data['date_edit'] = date('Y-m-d H:i:s');

            if (!empty($g_data)) {
                if ($existing_g) {
                    $this->db->where('id', $existing_g['id'])->update('guidance', $g_data);
                    $db_saved = true;
                } else if (!empty($jenis_ta) || !empty($judul_1)) {
                    if (in_array('id', $g_fields)) $g_data['id'] = 'gdn_' . $nim;
                    if (in_array('id_mhs', $g_fields)) $g_data['id_mhs'] = 'usr_mhs_' . $nim;
                    if (in_array('date', $g_fields)) $g_data['date'] = date('Y-m-d H:i:s');
                    if (in_array('tahun', $g_fields)) $g_data['tahun'] = date('Y');
                    if (in_array('keterangan', $g_fields)) $g_data['keterangan'] = 'Pending';
                    $this->db->insert('guidance', $g_data);
                    $db_saved = true;
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'db_saved' => $db_saved,
                'message' => 'Draft formulir & berkas berhasil tersimpan di database server.'
            ]));
    }

    // =========================================================
    // REKOMENDASI SIDANG / NON-SIDANG
    // =========================================================

    public function ajax_get_rekomen_options() {
        $this->load->model('Rekomendasi_model');
        $main = $this->Rekomendasi_model->get_active_main_options();
        $non_sidang = $this->Rekomendasi_model->get_active_non_sidang_options();
        echo json_encode([
            'status' => 'success',
            'data' => $non_sidang,
            'main_options' => $main
        ]);
        exit;
    }

    public function submit_rekomendasi_sidang() {
        $this->load->model('Rekomendasi_model');
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $id_preview = $this->input->post('id_preview');
        $user_id = $this->session->userdata('user_id');

        $data_sub = [
            'nim' => $nim,
            'id_preview' => $id_preview,
            'recommendation_type' => 'sidang',
            'jalur_id' => NULL,
            'jalur_title' => 'SIDANG',
            'form_data_json' => json_encode(['catatan' => 'Direkomendasikan Sidang Akhir Reguler']),
            'catatan_dosen' => 'Lanjut ke Tahapan Pendaftaran Sidang Akhir',
            'status' => 'Submitted',
            'created_by' => $user_id
        ];

        $sub_id = $this->Rekomendasi_model->save_submission($data_sub);

        if ($this->db->table_exists('pendaftaran_ta')) {
            $update_ta = ['current_stage' => 'Pendaftaran Sidang'];
            if ($this->db->field_exists('status_sidang', 'pendaftaran_ta')) {
                $update_ta['status_sidang'] = 'Belum Dijadwalkan';
            }
            $this->db->where('nim', $nim)->update('pendaftaran_ta', $update_ta);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Rekomendasi Sidang berhasil disimpan! Status mahasiswa kini berlanjut ke Pendaftaran Sidang.']);
            exit;
        }

        $this->session->set_flashdata('success', 'Rekomendasi Sidang berhasil disimpan!');
        redirect('mahasiswa/bimbingan');
    }

    public function submit_rekomendasi_nonsidang() {
        $this->load->model('Rekomendasi_model');
        $nim = $this->input->post('nim') ?: $this->_get_current_nim();
        $id_preview = $this->input->post('id_preview');
        $jalur_id = $this->input->post('jalur_id');
        $user_id = $this->session->userdata('user_id');

        $option = $this->Rekomendasi_model->get_option($jalur_id);
        if (!$option) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Jalur Non-Sidang tidak ditemukan.']);
                exit;
            }
            $this->session->set_flashdata('error', 'Jalur Non-Sidang tidak valid.');
            redirect('mahasiswa/bimbingan');
        }

        $upload_dir = './uploads/rekomendasi_ta/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $form_data = [];
        $allowed_ext_array = ['pdf', 'docx', 'doc'];

        if (!empty($option['fields'])) {
            foreach ($option['fields'] as $f) {
                $fkey = $f['field_key'];
                if ($f['field_type'] == 'file') {
                    if (isset($_FILES[$fkey]) && $_FILES[$fkey]['error'] == UPLOAD_ERR_OK) {
                        $file_ext = strtolower(pathinfo($_FILES[$fkey]['name'], PATHINFO_EXTENSION));
                        if (!in_array($file_ext, $allowed_ext_array)) {
                            $res = ['status' => 'error', 'message' => 'Format file "' . $f['field_label'] . '" harus PDF atau DOCX.'];
                            if ($this->input->is_ajax_request()) { echo json_encode($res); exit; }
                            $this->session->set_flashdata('error', $res['message']);
                            redirect('mahasiswa/bimbingan');
                        }

                        $new_filename = 'rekomen_' . $fkey . '_' . $nim . '_' . time() . '.' . $file_ext;
                        $target_path = $upload_dir . $new_filename;
                        if (move_uploaded_file($_FILES[$fkey]['tmp_name'], $target_path)) {
                            $form_data[$fkey] = [
                                'label' => $f['field_label'],
                                'file' => base_url('uploads/rekomendasi_ta/' . $new_filename),
                                'original_name' => $_FILES[$fkey]['name']
                            ];
                        }
                    } else if ($f['is_required']) {
                        $res = ['status' => 'error', 'message' => 'Berkas "' . $f['field_label'] . '" wajib diunggah.'];
                        if ($this->input->is_ajax_request()) { echo json_encode($res); exit; }
                        $this->session->set_flashdata('error', $res['message']);
                        redirect('mahasiswa/bimbingan');
                    }
                } else {
                    $form_data[$fkey] = [
                        'label' => $f['field_label'],
                        'val' => $this->input->post($fkey)
                    ];
                }
            }
        }

        $catatan_alasan = $this->input->post('catatan_alasan') ?: ($this->input->post('tanggapan') ?: '');

        $data_sub = [
            'nim' => $nim,
            'id_preview' => $id_preview,
            'recommendation_type' => 'non_sidang',
            'jalur_id' => $option['id'],
            'jalur_title' => $option['title'],
            'form_data_json' => json_encode($form_data),
            'catatan_dosen' => $catatan_alasan,
            'status' => 'Submitted',
            'created_by' => $user_id
        ];

        $sub_id = $this->Rekomendasi_model->save_submission($data_sub);

        if ($this->db->table_exists('pendaftaran_ta')) {
            $update_ta = ['current_stage' => 'Rekomendasi Non-Sidang (' . $option['title'] . ')'];
            if ($this->db->field_exists('status_sidang', 'pendaftaran_ta')) {
                $update_ta['status_sidang'] = 'Non-Sidang (' . $option['title'] . ')';
            }
            $this->db->where('nim', $nim)->update('pendaftaran_ta', $update_ta);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'message' => 'Rekomendasi Non-Sidang (' . $option['title'] . ') dan berkas persyaratan berhasil disimpan!']);
            exit;
        }

        $this->session->set_flashdata('success', 'Rekomendasi Non-Sidang (' . $option['title'] . ') berhasil disimpan!');
        redirect('mahasiswa/bimbingan');
    }

    public function ajax_get_all_rekomen_crud() {
        $this->load->model('Rekomendasi_model');
        $options = $this->Rekomendasi_model->get_all_options();
        echo json_encode(['status' => 'success', 'data' => $options]);
        exit;
    }

    public function ajax_save_rekomen_option() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        $data = [
            'category' => $this->input->post('category') ?: 'non_sidang',
            'code' => strtolower(url_title($this->input->post('title'), '_', true)) . '_' . time(),
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'icon_type' => 'bi',
            'icon_class' => $this->input->post('icon_class') ?: 'bi-award-fill',
            'is_active' => $this->input->post('is_active') !== null ? (int)$this->input->post('is_active') : 1,
            'sort_order' => (int)($this->input->post('sort_order') ?: 0)
        ];

        if ($id) {
            unset($data['code']);
        }

        $res_id = $this->Rekomendasi_model->save_option($data, $id);
        echo json_encode(['status' => 'success', 'message' => 'Jalur berhasil disimpan!', 'id' => $res_id]);
        exit;
    }

    public function ajax_delete_rekomen_option() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        if ($id) {
            $this->Rekomendasi_model->delete_option($id);
            echo json_encode(['status' => 'success', 'message' => 'Jalur berhasil dihapus!']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan']);
        exit;
    }

    public function ajax_delete_rekomen_category() {
        $this->load->model('Rekomendasi_model');
        $category = $this->input->post('category');
        if ($category) {
            $this->Rekomendasi_model->delete_category($category);
            echo json_encode(['status' => 'success', 'message' => 'Tab Kategori beserta seluruh isinya berhasil dihapus!']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'Kategori tidak ditemukan']);
        exit;
    }

    public function ajax_save_rekomen_field() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        $data = [
            'jalur_id' => $this->input->post('jalur_id'),
            'field_key' => $this->input->post('field_key') ?: strtolower(url_title($this->input->post('field_label'), '_', true)),
            'field_label' => $this->input->post('field_label'),
            'field_type' => $this->input->post('field_type') ?: 'file',
            'allowed_ext' => $this->input->post('allowed_ext') ?: 'pdf,docx,doc',
            'is_required' => (int)($this->input->post('is_required') ?: 0),
            'help_text' => $this->input->post('help_text') ?: 'Format file diharuskan pdf/docx',
            'sort_order' => (int)($this->input->post('sort_order') ?: 0)
        ];

        $res_id = $this->Rekomendasi_model->save_field($data, $id);
        echo json_encode(['status' => 'success', 'message' => 'Field persyaratan berhasil disimpan!', 'id' => $res_id]);
        exit;
    }

    public function ajax_delete_rekomen_field() {
        $this->load->model('Rekomendasi_model');
        $id = $this->input->post('id');
        if ($id) {
            $this->Rekomendasi_model->delete_field($id);
            echo json_encode(['status' => 'success', 'message' => 'Field berhasil dihapus!']);
            exit;
        }
        echo json_encode(['status' => 'error', 'message' => 'ID field tidak ditemukan']);
        exit;
    }

    // =========================================================
    // TICKETING
    // =========================================================

    private function _get_dynamic_unit_kategori_map() {
        $map = [];
        if ($this->db->table_exists('ticketing_units')) {
            $units = $this->db
                ->where('is_active', 1)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('ticketing_units')
                ->result_array();

            foreach ($units as $u) {
                $categories = [];
                if ($this->db->table_exists('ticketing_kategori')) {
                    $categories = $this->db
                        ->where('unit_id', $u['id'])
                        ->where('is_active', 1)
                        ->order_by("(CASE WHEN nama_kategori LIKE 'Lain-lain%' OR nama_kategori LIKE 'Lainnya%' THEN 1 ELSE 0 END)", 'ASC', FALSE)
                        ->order_by('sort_order', 'ASC')
                        ->order_by('id', 'ASC')
                        ->get('ticketing_kategori')
                        ->result_array();
                }

                $catList = array_column($categories, 'nama_kategori');
                if (empty($catList)) {
                    $catList = ['Lain-lain (' . $u['nama_unit'] . ')'];
                }
                $map[$u['nama_unit']] = $catList;
            }
        }

        if (empty($map)) {
            $map = [
                'Laboran (Fasilitas & Lab)' => [
                    'Fasilitas Ruangan / AC / Proyektor',
                    'Perangkat Komputer / Hardware',
                    'Koneksi Jaringan / Internet Lab',
                    'Software / Lisensi Praktikum',
                    'Lain-lain (Laboran)'
                ],
                'Layanan Akademik (LAA)' => [
                    'Surat Keterangan / Pengantar',
                    'Administrasi Nilai & Transkrip',
                    'Jadwal Kuliah / Ujian',
                    'Lain-lain (LAA)'
                ],
                'Koordinator TA' => [
                    'Bimbingan & Penguji Tugas Akhir',
                    'Jadwal Preview / Sidang TA',
                    'Rubrik Penilaian TA',
                    'Lain-lain (Koordinator TA)'
                ],
                'Dosen Wali' => [
                    'Konsultasi Akademik / Perwalian',
                    'Persetujuan / Tanda Tangan Dokumen',
                    'Kendala Perkuliahan & Nilai',
                    'Bimbingan Akademik',
                    'Lain-lain (Dosen Wali)'
                ]
            ];
        }

        return $map;
    }

    public function ticketing_input() {
        $nim = $this->_get_current_nim();
        $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);

        $userId = $this->session->userdata('user_id');
        $nama = !empty($mhs['nama_depan']) ? trim($mhs['nama_depan'] . ' ' . ($mhs['nama_belakang'] ?? '')) : ($this->session->userdata('name') ?: 'Mahasiswa');
        $email = $this->session->userdata('email');

        $unit_kategori_map = $this->_get_dynamic_unit_kategori_map();

        $custom_fields = [];
        if ($this->db->table_exists('laboran_ticketing_fields')) {
            $custom_fields = $this->db
                ->where('is_active', 1)
                ->order_by('sort_order', 'ASC')
                ->order_by('id', 'ASC')
                ->get('laboran_ticketing_fields')
                ->result_array();
        }

        $data = [
            'title'             => 'Buat Tiket Kendala Mahasiswa — IFIK Portal',
            'active_menu'       => 'ticketing_input',
            'user'              => [
                'id'    => $userId,
                'nim'   => $nim,
                'nama'  => $nama,
                'email' => $email
            ],
            'mahasiswa'         => $mhs,
            'custom_fields'     => $custom_fields,
            'penerima_list'     => [
                'Laboran'    => [
                    'id'    => 'Laboran',
                    'title' => 'Laboran',
                    'desc'  => 'Fasilitas Lab, Hardware, Software, Jaringan & Sarpras',
                    'icon'  => 'bi-pc-display-horizontal'
                ],
                'Kaur'       => [
                    'id'    => 'Kaur',
                    'title' => 'Kaur (Kepala Urusan)',
                    'desc'  => 'Kepala Urusan, Fasilitas Akademik, Perkuliahan & Pengesahan',
                    'icon'  => 'bi-person-badge'
                ],
                'Admin LAA'  => [
                    'id'    => 'Admin LAA',
                    'title' => 'Admin LAA',
                    'desc'  => 'Layanan Akademik, Surat Pengantar, Ijazah & KTM',
                    'icon'  => 'bi-building-check'
                ]
            ],
            'unit_kategori_map' => $unit_kategori_map,
            'prioritas_list'    => [
                'Rendah'  => ['label' => 'Rendah', 'color' => 'slate', 'desc' => 'Pertanyaan umum / kendala minor'],
                'Sedang'  => ['label' => 'Sedang', 'color' => 'blue', 'desc' => 'Kendala kerja rutin tanpa hambatan fatal'],
                'Tinggi'  => ['label' => 'Tinggi', 'color' => 'amber', 'desc' => 'Proses tertunda, butuh respon cepat'],
                'Darurat' => ['label' => 'Darurat', 'color' => 'rose', 'desc' => 'Sistem kritis / jadwal mendesak hari ini']
            ],
            'form_action'       => site_url('mahasiswa/ticketing/simpan')
        ];

        $this->load->view('mahasiswa/ticketing_input', $data);
    }

    public function ticketing_simpan() {
        $nim = $this->_get_current_nim();
        $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);

        $userId      = $this->session->userdata('user_id');
        $namaDefault = !empty($mhs['nama_depan']) ? trim($mhs['nama_depan'] . ' ' . ($mhs['nama_belakang'] ?? '')) : ($this->session->userdata('name') ?: 'Mahasiswa');
        $namaLengkap = trim($this->input->post('nama_lengkap', true)) ?: $namaDefault;
        $email       = $this->session->userdata('email') ?: ($mhs['email'] ?? '');

        $tujuan_penerima  = trim($this->input->post('tujuan_penerima', true)) ?: 'Laboran';
        $unit_terkait     = trim($this->input->post('unit_terkait', true)) ?: (trim($this->input->post('unit_tujuan', true)) ?: 'Layanan Umum');
        $kategori         = trim($this->input->post('kategori', true));
        $kategori_lainnya = trim($this->input->post('kategori_lainnya', true));
        $prioritas        = trim($this->input->post('prioritas', true));
        $subjek           = trim($this->input->post('subjek', true));
        $deskripsi        = $this->input->post('deskripsi');

        $textOnly = trim(strip_tags($deskripsi));
        if (empty($namaLengkap) || empty($tujuan_penerima) || empty($unit_terkait) || empty($kategori) || empty($subjek) || empty($textOnly)) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => 'Semua field bertanda bintang (*) wajib diisi.']);
                return;
            }
            $this->session->set_flashdata('error', 'Semua field bertanda bintang (*) wajib diisi.');
            redirect('mahasiswa/ticketing/input');
            return;
        }

        $this->load->model('DosenTicketing_model');
        $recentTickets = $this->DosenTicketing_model->get_tickets($userId, $nim);
        $recentTicket = null;
        if (!empty($recentTickets)) {
            $latest = $recentTickets[0];
            if ($latest->subjek === $subjek && (time() - strtotime($latest->created_at)) <= 5) {
                $recentTicket = $latest;
            }
        }

        if ($recentTicket) {
            $msg = "Tiket Anda berhasil diajukan dengan Kode: <b>{$recentTicket->kode_tiket}</b> ditujukan kepada <b>" . htmlspecialchars($tujuan_penerima) . "</b>.";
            $this->session->set_flashdata('success', $msg);
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status'       => 'success',
                    'kode_tiket'   => $recentTicket->kode_tiket,
                    'message'      => $msg,
                    'redirect_url' => site_url('mahasiswa/ticketing/riwayat')
                ]);
                return;
            }
            redirect('mahasiswa/ticketing/riwayat');
            return;
        }

        if (preg_match('/lain/i', $kategori)) {
            if (!empty($kategori_lainnya)) {
                $kategori = $kategori . ': ' . $kategori_lainnya;
            } else {
                $err = 'Silakan tulis rincian topik kendala pada input "Detail Kategori Lainnya".';
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => $err]);
                    return;
                }
                $this->session->set_flashdata('error', $err);
                redirect('mahasiswa/ticketing/input');
                return;
            }
        }

        $lampiran_name = null;
        if (!empty($_FILES['lampiran']['name'])) {
            $uploadPath = FCPATH . 'uploads/ticketing/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $config['upload_path']   = $uploadPath;
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|zip|rar';
            $config['max_size']      = 5120;
            $config['encrypt_name']  = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('lampiran')) {
                $uploadData = $this->upload->data();
                $lampiran_name = $uploadData['file_name'];
            } else {
                $uploadError = $this->upload->display_errors('', '');
                $err = 'Gagal mengunggah lampiran: ' . $uploadError;
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['status' => 'error', 'message' => $err]);
                    return;
                }
                $this->session->set_flashdata('error', $err);
                redirect('mahasiswa/ticketing/input');
                return;
            }
        }

        $this->load->model('DosenTicketing_model');
        $kodeTiket = $this->DosenTicketing_model->generate_kode();

        $ticketData = [
            'kode_tiket'      => $kodeTiket,
            'id_user'         => $userId ?: $nim,
            'nama_dosen'      => $namaLengkap,
            'nama'            => $namaLengkap,
            'email'           => $email,
            'nidn'            => $nim,
            'tujuan_penerima' => $tujuan_penerima,
            'unit_terkait'    => $unit_terkait,
            'unit_tujuan'     => $unit_terkait,
            'kategori'        => $kategori,
            'prioritas'       => in_array($prioritas, ['Rendah', 'Sedang', 'Tinggi', 'Darurat']) ? $prioritas : 'Sedang',
            'subjek'          => $subjek,
            'deskripsi'       => $deskripsi,
            'lampiran'        => $lampiran_name,
            'status'          => 'Menunggu'
        ];

        $insertedId = $this->DosenTicketing_model->insert($ticketData);

        if ($insertedId) {
            $msg = "Tiket kendala berhasil diajukan dengan Kode: <b>{$kodeTiket}</b> ditujukan kepada <b>" . htmlspecialchars($tujuan_penerima) . "</b> (Lingkup Terkait: <b>" . htmlspecialchars($unit_terkait) . "</b>). Mohon pantau status respon secara berkala.";
            $this->session->set_flashdata('success', $msg);
            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    'status'       => 'success',
                    'kode_tiket'   => $kodeTiket,
                    'message'      => $msg,
                    'redirect_url' => site_url('mahasiswa/ticketing/riwayat')
                ]);
                return;
            }
            redirect('mahasiswa/ticketing/riwayat');
        } else {
            $err = 'Terjadi kesalahan sistem saat menyimpan tiket. Silakan coba beberapa saat lagi.';
            if ($this->input->is_ajax_request()) {
                echo json_encode(['status' => 'error', 'message' => $err]);
                return;
            }
            $this->session->set_flashdata('error', $err);
            redirect('mahasiswa/ticketing/input');
        }
    }

    public function ticketing_riwayat() {
        $nim = $this->_get_current_nim();
        $userId = $this->session->userdata('user_id');

        $this->load->model('DosenTicketing_model');
        $tickets = $this->DosenTicketing_model->get_tickets($userId, $nim);
        $stats   = $this->DosenTicketing_model->get_stats($userId, $nim);

        $data = [
            'title'       => 'Riwayat Tiket Kendala Saya — Mahasiswa IFIK',
            'active_menu' => 'ticketing_riwayat',
            'tickets'     => $tickets,
            'stats'       => $stats,
            'nim'         => $nim
        ];

        $this->load->view('mahasiswa/ticketing_riwayat', $data);
    }

    public function ticketing_detail($id_or_kode) {
        $this->load->model('DosenTicketing_model');
        $ticket = $this->DosenTicketing_model->get_by_id($id_or_kode);

        if (!$ticket) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Tiket tidak ditemukan.'
                ]));
        }

        $userId = $this->session->userdata('user_id');
        $nim   = $this->_get_current_nim();
        $roleId = (int)$this->session->userdata('role_id');

        if ($roleId !== 1 && $ticket->id_user != $userId && ($ticket->nidn ?? '') != $nim) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(403)
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki hak akses untuk melihat tiket ini.'
                ]));
        }

        $isHtml = (strpos($ticket->deskripsi, '<') !== false && strpos($ticket->deskripsi, '>') !== false);
        $deskripsiFormatted = $isHtml ? $ticket->deskripsi : nl2br(htmlspecialchars($ticket->deskripsi));

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => 'success',
                'data'    => [
                    'id'              => $ticket->id,
                    'kode_tiket'      => $ticket->kode_tiket,
                    'nama_dosen'      => $ticket->nama_dosen,
                    'nidn'            => $ticket->nidn ?? '-',
                    'label_identitas' => $ticket->label_identitas ?? 'NIM',
                    'tujuan_penerima' => $ticket->tujuan_penerima ?? 'Laboran',
                    'unit_terkait'    => $ticket->unit_terkait ?? ($ticket->unit_tujuan ?: 'Layanan IFIK'),
                    'unit_tujuan'     => $ticket->unit_terkait ?? ($ticket->unit_tujuan ?: 'Layanan IFIK'),
                    'kategori'        => $ticket->kategori,
                    'prioritas'       => $ticket->prioritas,
                    'subjek'          => $ticket->subjek,
                    'deskripsi'       => $deskripsiFormatted,
                    'lampiran'        => $ticket->lampiran,
                    'lampiran_url'    => $ticket->lampiran ? base_url('uploads/ticketing/' . $ticket->lampiran) : null,
                    'status'          => $ticket->status,
                    'tanggapan'       => $ticket->tanggapan ? nl2br(htmlspecialchars($ticket->tanggapan)) : null,
                    'catatan_proses'  => !empty($ticket->catatan_proses) ? nl2br(htmlspecialchars($ticket->catatan_proses)) : null,
                    'catatan_selesai' => !empty($ticket->catatan_selesai) ? nl2br(htmlspecialchars($ticket->catatan_selesai)) : null,
                    'catatan_tutup'   => !empty($ticket->catatan_tutup) ? nl2br(htmlspecialchars($ticket->catatan_tutup)) : null,
                    'tgl_diproses'    => !empty($ticket->tgl_diproses) ? date('d M Y H:i', strtotime($ticket->tgl_diproses)) : null,
                    'tgl_closed'      => !empty($ticket->tgl_closed) ? date('d M Y H:i', strtotime($ticket->tgl_closed)) : null,
                    'tgl_tanggapan'   => $ticket->tgl_tanggapan ? date('d M Y H:i', strtotime($ticket->tgl_tanggapan)) : null,
                    'created_at'      => date('d M Y H:i', strtotime($ticket->created_at)),
                    'updated_at'      => date('d M Y H:i', strtotime($ticket->updated_at))
                ]
            ]));
    }
        // =========================================================
    // PENILAIAN SIDANG (Preview 4) — P1 / P2 / U1 / U2
    // =========================================================

    /**
     * GET ?nim=xxx
     * Ambil detail nilai sidang + jadwal + dosen untuk modal.
     */
    public function get_detail_nilai_sidang_ajax() {
        header('Content-Type: application/json');

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
     * POST: nim, posisi (1=P1, 2=P2, 3=U1, 4=U2), nilai_akhir, detail_penilaian (JSON), catatan
     */
    public function simpan_nilai_sidang_ajax() {
        header('Content-Type: application/json');

        $nim         = $this->input->post('nim');
        $posisi      = (int)$this->input->post('posisi');
        $nilai       = $this->input->post('nilai_akhir');
        $catatan     = $this->input->post('catatan');
        $detail_raw  = $this->input->post('detail_penilaian');

        if (empty($nim) || !in_array($posisi, [1, 2, 3, 4])) {
            echo json_encode(['status' => false, 'message' => 'Parameter tidak lengkap atau posisi tidak valid.']);
            return;
        }

        if ($nilai === null || $nilai === '' || !is_numeric($nilai)) {
            echo json_encode(['status' => false, 'message' => 'Nilai akhir wajib diisi (0-100).']);
            return;
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
}