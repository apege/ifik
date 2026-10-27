<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mahasiswa_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url', 'fik_prodi'));

        $isAjax = $this->input->is_ajax_request() ||
                  (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // 1. Cek Login
        if (!$this->session->userdata('logged_in')) {
            if ($isAjax) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Sesi berakhir, silakan login kembali.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses portal Mahasiswa.');
            redirect('login');
            return;
        }

        // 2. Cek Role (Khusus fitur Mahasiswa: Role 4, atau Admin / Super Admin: Role 1, 22)
        // Pengecualian: method-method rekomendasi TA (bisa diakses oleh Dosen & Koordinator TA / Admin)
        $role_id = (int)$this->session->userdata('role_id');
        $current_method = $this->router->fetch_method();
        $isReviewerMethod = in_array($current_method, ['get_detail_nilai_sidang_ajax', 'simpan_nilai_sidang_ajax'], true);
        if ($isReviewerMethod && in_array($role_id, [1, 2, 3, 4, 5, 6, 9, 22], true)) {
            // Izinkan reviewer mengakses endpoint penilaian sidang
            return;
        }

        $isRekomenMethod = (stripos($current_method, 'rekomen') !== false);
        $shared_rekomen_methods = array(
            'ajax_get_rekomen_options',
            'submit_rekomendasi_sidang',
            'submit_rekomendasi_nonsidang',
            'ajax_get_all_rekomen_crud',
            'ajax_save_rekomen_option',
            'ajax_delete_rekomen_option',
            'ajax_delete_rekomen_category',
            'ajax_save_rekomen_field',
            'ajax_delete_rekomen_field'
        );

        if ($isRekomenMethod || in_array($current_method, $shared_rekomen_methods)) {
            // Metode rekomendasi & manajemen jalur diizinkan untuk semua role yang telah login
            // (Mahasiswa, Dosen, Koordinator TA, Admin Layanan, Super Admin)
            return;
        }

        if ($role_id !== 4 && $role_id !== 1 && $role_id !== 22) {
            if ($isAjax) {
                $this->output
                    ->set_status_header(403)
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['success' => false, 'message' => 'Akses ditolak. Halaman ini khusus untuk Mahasiswa.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus untuk Mahasiswa.');
            redirect('login');
            return;
        }
    }

    private function _get_current_nim() {
        $nim = $this->session->userdata('nim') ?: ($this->session->userdata('nidn_nim') ?: ($this->session->userdata('username') ?: ''));
        if (empty($nim) && $this->session->userdata('user_id')) {
            $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
            if ($user_tbl) {
                $u = $this->db->get_where($user_tbl, ['id' => $this->session->userdata('user_id')])->row_array();
                if ($u) {
                    $nim = !empty($u['nim']) ? $u['nim'] : (!empty($u['nidn_nim']) ? $u['nidn_nim'] : ($u['username'] ?? ''));
                }
            }
        }
        return $nim;
    }

    private function _get_current_user_id() {
        $userId = $this->session->userdata('user_id');
        if (!$userId) {
            $nim = $this->_get_current_nim();
            $user_tbl = $this->db->table_exists('user') ? 'user' : ($this->db->table_exists('users') ? 'users' : null);
            if ($nim && $user_tbl) {
                $this->db->group_start();
                if ($this->db->field_exists('nim', $user_tbl)) $this->db->where('nim', $nim);
                if ($this->db->field_exists('nidn_nim', $user_tbl)) $this->db->or_where('nidn_nim', $nim);
                if ($this->db->field_exists('username', $user_tbl)) $this->db->or_where('username', $nim);
                $this->db->group_end();
                $u = $this->db->select('id')->limit(1)->get($user_tbl)->row_array();
                if ($u && !empty($u['id'])) {
                    $userId = $u['id'];
                }
            }
        }
        return $userId ? $userId : null;
    }

    private function _do_upload($field_name, $config, &$error = null) {
        $this->upload->initialize($config);
        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
        $error = strip_tags($this->upload->display_errors('', ''));
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
                $salt = bin2hex(random_bytes(16));
                $password_hashed = password_hash($this->input->post('password_baru') . $salt, PASSWORD_BCRYPT);
                $this->Mahasiswa_model->update_password($nim, $password_hashed, $salt);
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

        $this->load->model('AdminLayanan_model');
        $data['title'] = $is_locked ? 'Detail / Ringkasan Formulir Tugas Akhir' : 'Edit Formulir Tugas Akhir (Revisi)';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $pendaftaran;
        $data['is_locked'] = $is_locked;
        $data['has_revisi'] = $has_revisi;
        $data['opsi_jenis_ta'] = $this->AdminLayanan_model->get_active_jenis_ta();

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

            $posted_jenis_ta = $this->input->post('jenis_ta');
            if (empty($posted_jenis_ta) && !empty($pendaftaran['jenis_ta'])) {
                $posted_jenis_ta = $pendaftaran['jenis_ta'];
            }
            $posted_judul_1 = $this->input->post('judul_1');
            if (empty($posted_judul_1) && !empty($pendaftaran['judul_1'])) {
                $posted_judul_1 = $pendaftaran['judul_1'];
            }
            $posted_judul_2 = $this->input->post('judul_2') ?: ($pendaftaran['judul_2'] ?? '');
            $posted_judul_3 = $this->input->post('judul_3') ?: ($pendaftaran['judul_3'] ?? '');
            $posted_judul_en = $this->input->post('judul_en') ?: ($pendaftaran['judul_en'] ?? '');
            $posted_konsentrasi = $this->input->post('konsentrasi_dkv') ?: ($pendaftaran['konsentrasi_dkv'] ?? '');

            $data_ta = array(
                'nim'                  => $nim,
                'is_submitted'         => 1,
                'jenis_ta'             => $posted_jenis_ta,
                'judul_1'              => $posted_judul_1,
                'judul_2'              => $posted_judul_2,
                'judul_3'              => $posted_judul_3,
                'judul_en'             => $posted_judul_en,
                'konsentrasi_dkv'      => $posted_konsentrasi,
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
        $upload_errors = [];

        foreach ($active_syarat as $sb) {
            $k = $sb['kode_berkas'];
            $f = 'file_' . $k;
            if (!empty($_FILES[$f]['name'])) {
                $err = null;
                $new_file = $this->_do_upload($f, $config, $err);
                if ($new_file) {
                    $uploaded_count++;
                    $this->AdminLayanan_model->save_student_berkas($nim, $k, $new_file, 'Pending');

                    // 2. Simpan ke file_pendaftaran jika ada
                    if ($this->db->table_exists('file_pendaftaran')) {
                        $u_id = $this->_get_current_user_id();
                        $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
                        $update_fp = [
                            'file'            => 'uploads/persyaratan_ta/' . $new_file,
                            'status_adminlaa' => 'Pending',
                            'view_adminlaa'   => 0,
                            'view_doswal'     => 0,
                            'date_edit'       => date('Y-m-d H:i:s')
                        ];
                        if (($pendaftaran['status_approval_wali'] ?? '') !== 'Approved') {
                            $update_fp['status_doswal'] = 'Pending';
                            $update_fp['komentar']      = '';
                        }

                        $ex_fp = $this->db->group_start()
                            ->where_in('id_mhs', $target_ids)
                            ->group_end()
                            ->like('nama', $k)
                            ->get('file_pendaftaran')->row_array();

                        if ($ex_fp) {
                            $this->db->where('id', $ex_fp['id'])->update('file_pendaftaran', $update_fp);
                        } else {
                            $fp_fields = $this->db->list_fields('file_pendaftaran');
                            $new_fp = $update_fp;
                            if (in_array('id', $fp_fields)) $new_fp['id'] = 'fp_' . $nim . '_' . $k;
                            if (in_array('id_mhs', $fp_fields)) $new_fp['id_mhs'] = $u_id;
                            if (in_array('nama', $fp_fields)) $new_fp['nama'] = $k;
                            if (in_array('date', $fp_fields)) $new_fp['date'] = date('Y-m-d H:i:s');
                            $this->db->insert('file_pendaftaran', $new_fp);
                        }
                    }

                    // 3. Simpan ke pendaftaran_ta jika tabel dan kolomnya ada
                    if ($this->db->table_exists('pendaftaran_ta')) {
                        if ($this->db->field_exists($f, 'pendaftaran_ta')) {
                            $updated_data[$f] = $new_file;
                        }
                        $col_status  = 'status_file_' . $k;
                        $col_review  = 'review_file_' . $k;
                        $col_catatan = 'catatan_file_' . $k;
                        if (($pendaftaran['status_approval_wali'] ?? '') !== 'Approved') {
                            if ($this->db->field_exists($col_status, 'pendaftaran_ta')) {
                                $updated_data[$col_status] = 'Pending';
                            }
                            if ($this->db->field_exists($col_review, 'pendaftaran_ta')) {
                                $updated_data[$col_review] = 0;
                            }
                            if ($this->db->field_exists($col_catatan, 'pendaftaran_ta')) {
                                $updated_data[$col_catatan] = '';
                            }
                        }
                        $col_legacy_status = 'status_' . $k;
                        if ($this->db->field_exists($col_legacy_status, 'pendaftaran_ta')) {
                            $updated_data[$col_legacy_status] = 'Pending';
                        }
                    }
                } else {
                    $label = !empty($sb['nama_berkas']) ? $sb['nama_berkas'] : strtoupper($k);
                    $upload_errors[] = $label . ' (' . ($err ?: 'harus PDF dan maksimal 5 MB') . ')';
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
                    $u_id = $this->_get_current_user_id();
                    $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
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
                $u_id = $this->_get_current_user_id();
                $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
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
                    $u_id = $this->_get_current_user_id();
                    $target_ids = array_values(array_unique(array_filter([$u_id, $nim])));
                    $this->db->where_in('id_mhs', $target_ids)->update('guidance', [
                        'komentar' => ''
                    ]);
                }
            }

            if ($this->db->table_exists('pendaftaran_ta')) {
                $this->db->where('nim', $nim)->update('pendaftaran_ta', $updated_data);
            }

            if (!empty($upload_errors)) {
                $msg = 'Sebagian berkas gagal diunggah: ' . implode('; ', $upload_errors) . '. Pastikan berkas berformat PDF dan ukuran maksimal 5 MB.';
                if ($uploaded_count > 0 || !empty($this->input->post('judul_1')) || !empty($this->input->post('jenis_ta'))) {
                    $this->session->set_flashdata('warning', $msg . ' Namun berkas yang valid telah berhasil disimpan.');
                } else {
                    $this->session->set_flashdata('error', $msg);
                }
            } else {
                $this->session->set_flashdata('success', 'Berhasil mengunggah ' . ($uploaded_count ? $uploaded_count . ' berkas perbaikan' : 'perubahan usulan') . ' untuk diverifikasi kembali!');
            }
        } else {
            if (!empty($upload_errors)) {
                $this->session->set_flashdata('error', 'Gagal mengunggah berkas: ' . implode('; ', $upload_errors) . '. Pastikan file berformat PDF dan ukuran maksimal 5 MB.');
            } else {
                $this->session->set_flashdata('error', 'Tidak ada file baru yang diunggah. Silakan pilih file PDF yang valid (Maksimal 5 MB).');
            }
        }

        redirect('mahasiswa');
    }

    /**
     * [MODUL TA] Halaman utama pendaftaran Tugas Akhir (TA) mahasiswa (Multi-Step Form Stepper).
     * Menangani Step 1 (Input Proposal), Step 2 (Upload Berkas Dinamis), Step 3 (Review & Final Submit).
     *
     * @return void
     * @affected_table pendaftaran_ta, file_pendaftaran_ta, syarat_berkas_ta
     */
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
            // Jika sudah pernah diajukan (is_submitted), form pendaftaran utama tetap terkunci.
            // Mahasiswa yang memiliki catatan revisi harus mengunggah perbaikan melalui menu Perbaikan Berkas di Dashboard.
            $is_locked = true;
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
        $data['opsi_jenis_ta']  = $this->AdminLayanan_model->get_active_jenis_ta();
        $data['student_berkas'] = $student_berkas;

        if ($this->input->post()) {
            if ($is_locked) {
                $this->session->set_flashdata('error', 'Formulir pendaftaran telah dikunci karena sudah diajukan. Untuk melakukan perbaikan berkas yang direvisi, silakan gunakan menu Perbaikan Berkas di Dashboard.');
                redirect('mahasiswa');
                return;
            }

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

            $is_final_submit = ($this->input->post('is_submitted') == '1' || $this->input->post('submit_action') === 'submit');

            $w_status  = isset($existing_ta['status_approval_wali']) ? $existing_ta['status_approval_wali'] : ($is_final_submit ? 'Pending' : 'Draft');
            $a_status  = isset($existing_ta['status_approval_admin']) ? $existing_ta['status_approval_admin'] : 'Pending';
            $k_status  = isset($existing_ta['status_approval_koor']) ? $existing_ta['status_approval_koor'] : 'Pending';
            $kk_status = isset($existing_ta['status_approval_kk']) ? $existing_ta['status_approval_kk'] : 'Pending';

            if ($w_status === 'Rejected') {
                $w_status = 'Pending';
            } elseif (!$is_final_submit) {
                $w_status = 'Draft';
            } elseif (empty($w_status) || $w_status === 'Draft') {
                $w_status = 'Pending';
            }

            $has_new_uploads = !empty($file_step3) || !empty($file_step4) || !empty($file_step5) || !empty($file_step6);
            if ($a_status === 'Rejected' || ($a_status === 'Approved' && $has_new_uploads) || $has_new_uploads) {
                $a_status = 'Pending';
            }

            if ($k_status === 'Rejected') $k_status = 'Pending';
            if ($kk_status === 'Rejected') $kk_status = 'Pending';

            if ($w_status === 'Draft') {
                $current_stage = 'Draft';
            } else if ($w_status !== 'Approved') {
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

            $posted_jenis_ta = $this->input->post('jenis_ta');
            if (empty($posted_jenis_ta) && !empty($existing_ta['jenis_ta'])) {
                $posted_jenis_ta = $existing_ta['jenis_ta'];
            }
            $posted_judul_1 = $this->input->post('judul_1');
            if (empty($posted_judul_1) && !empty($existing_ta['judul_1'])) {
                $posted_judul_1 = $existing_ta['judul_1'];
            }
            $posted_judul_2 = $this->input->post('judul_2') ?: ($existing_ta['judul_2'] ?? '');
            $posted_judul_3 = $this->input->post('judul_3') ?: ($existing_ta['judul_3'] ?? '');
            $posted_judul_en = $this->input->post('judul_en') ?: ($existing_ta['judul_en'] ?? '');
            $posted_konsentrasi = $this->input->post('konsentrasi_dkv') ?: ($existing_ta['konsentrasi_dkv'] ?? '');

            $data_ta = array(
                'nim'                  => $nim,
                'id_dosen_wali'        => $id_dosen_wali,
                'is_submitted'         => $is_final_submit ? 1 : 0,
                'jenis_ta'             => $posted_jenis_ta,
                'judul_1'              => $posted_judul_1,
                'judul_2'              => $posted_judul_2,
                'judul_3'              => $posted_judul_3,
                'judul_en'             => $posted_judul_en,
                'konsentrasi_dkv'      => $posted_konsentrasi,
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
        $has_ta = !empty($pendaftaran['judul_1']) || !empty($pendaftaran['jenis_ta']) || !empty($pendaftaran['is_submitted']) || !empty($pendaftaran['file_ksm']);

        if (!$pendaftaran || !$has_ta) {
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

    /**
     * [MODUL TA] AJAX Upload berkas persyaratan TA dengan penamaan file berbasis User ID dan validasi PDF 5MB.
     *
     * @return json Response success true/false, filename, dan link pratinjau berkas
     * @affected_table file_pendaftaran_ta
     */
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
            $mhs_konsentrasi = !empty($mhs['konsentrasi_dkv']) ? $mhs['konsentrasi_dkv'] : (!empty($mhs['prodi']) ? $mhs['prodi'] : '-');
            $mhs_id_kk = !empty($mhs['id_kk']) ? $mhs['id_kk'] : 1;

            $kode_berkas = str_replace('file_', '', $field_name);
            $clean_nim = preg_replace('/^(usr_mhs_|mhs_|usr_)/i', '', trim((string)$nim));
            $user_id_real = $this->_get_current_user_id();
            if (empty($user_id_real) || $user_id_real === $nim || is_numeric($user_id_real)) {
                $u_row = $this->db->get_where('user', ['nim' => $clean_nim])->row_array();
                if (!$u_row) {
                    $u_row = $this->db->get_where('user', ['id' => $clean_nim])->row_array();
                }
                if ($u_row && !empty($u_row['id'])) {
                    $user_id_real = $u_row['id'];
                }
            }
            $u_id_default = !empty($user_id_real) ? $user_id_real : ('usr_mhs_' . $clean_nim);
            $target_user_ids = array_values(array_unique(array_filter([
                (string)$user_id_real,
                (string)$nim,
                $clean_nim,
                $u_id_default,
                'usr_mhs_' . $clean_nim,
                'mhs_' . $clean_nim,
                'usr_' . $clean_nim
            ])));

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
                    $id_fp = 'fp_' . $clean_nim . '_' . $kode_berkas;
                    $ex_fp = $this->db->group_start()
                        ->where('id', $id_fp)
                        ->or_where('id', 'fp_' . $nim . '_' . $kode_berkas)
                        ->or_group_start()
                            ->where_in('id_mhs', $target_user_ids)
                            ->where('nama', $kode_berkas)
                        ->group_end()
                    ->group_end()
                    ->get('file_pendaftaran')->row_array();
                    $rel_file_path = 'uploads/persyaratan_ta/' . $file_name;

                    $fp_payload = array();
                    if (in_array('file', $fp_fields)) $fp_payload['file'] = $rel_file_path;
                    if (in_array('status_doswal', $fp_fields)) $fp_payload['status_doswal'] = 'Pending';
                    if (in_array('status_adminlaa', $fp_fields)) $fp_payload['status_adminlaa'] = 'Pending';
                    if (in_array('view_adminlaa', $fp_fields)) $fp_payload['view_adminlaa'] = 0;
                    if (in_array('view_doswal', $fp_fields)) $fp_payload['view_doswal'] = 0;
                    if (in_array('date_edit', $fp_fields)) $fp_payload['date_edit'] = date('Y-m-d H:i:s');

                    if ($ex_fp) {
                        if (!empty($fp_payload)) {
                            $this->db->where('id', $ex_fp['id'])->update('file_pendaftaran', $fp_payload);
                        }
                    } else {
                        if (in_array('id', $fp_fields)) $fp_payload['id'] = $id_fp;
                        if (in_array('id_mhs', $fp_fields)) $fp_payload['id_mhs'] = $u_id_default;
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

            // Pastikan entri guidance berstatus Draft ada jika belum terdaftar
            if ($this->db->table_exists('guidance')) {
                try {
                    $ex_g = $this->db->where_in('id_mhs', $target_user_ids)
                        ->order_by('date', 'DESC')
                        ->limit(1)
                        ->get('guidance')->row_array();
                    if (!$ex_g) {
                        $ex_g = $this->db->get_where('guidance', ['id' => 'gdn_' . $nim])->row_array();
                    }
                    if (!$ex_g) {
                        $g_fields = $this->db->list_fields('guidance');
                        $new_g = array();
                        if (in_array('id', $g_fields)) $new_g['id'] = 'gdn_' . $nim;
                        if (in_array('id_mhs', $g_fields)) $new_g['id_mhs'] = $u_id_default;
                        if (in_array('date', $g_fields)) $new_g['date'] = date('Y-m-d H:i:s');
                        if (in_array('tahun', $g_fields)) $new_g['tahun'] = date('Y');
                        if (in_array('keterangan', $g_fields)) $new_g['keterangan'] = 'Draft';
                        if (in_array('peminatan', $g_fields)) $new_g['peminatan'] = $mhs_konsentrasi;
                        $this->db->insert('guidance', $new_g);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error creating guidance draft in ajax_upload: ' . $e->getMessage());
                }
            }

            if (method_exists($this->AdminLayanan_model, 'clear_cache')) {
                $this->AdminLayanan_model->clear_cache();
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
        $clean_nim = preg_replace('/^(usr_mhs_|mhs_|usr_)/i', '', trim((string)$nim));
        $user_id_real = $this->_get_current_user_id();
        $target_ids = array_values(array_unique(array_filter([
            (string)$user_id_real,
            (string)$nim,
            $clean_nim,
            'usr_mhs_' . $clean_nim,
            'mhs_' . $clean_nim,
            'usr_' . $clean_nim
        ])));

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

        // 1. Hapus dari pendaftaran_berkas jika tabel ada
        if ($this->db->table_exists('pendaftaran_berkas')) {
            $this->db->where('nim', $nim)->where('kode_berkas', $kode_berkas)->delete('pendaftaran_berkas');
        }

        // 2. Kosongkan kolom di pendaftaran_ta jika tabel dan kolom ada
        if ($this->db->table_exists('pendaftaran_ta') && $this->db->field_exists($field_name, 'pendaftaran_ta')) {
            $this->db->where('nim', $nim)->update('pendaftaran_ta', [
                $field_name => '',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 3. PENTING: Hapus dari tabel file_pendaftaran dan unlink file fisiknya
        if ($this->db->table_exists('file_pendaftaran')) {
            $this->db->where_in('id_mhs', $target_ids);
            $this->db->group_start();
                $this->db->where('nama', $kode_berkas);
                $this->db->or_like('nama', $kode_berkas);
                $this->db->or_like('id', 'fp_' . $clean_nim . '_' . $kode_berkas);
                $this->db->or_like('id', 'fp_' . $nim . '_' . $kode_berkas);
                $this->db->or_like('id', '_' . $kode_berkas);
                $this->db->or_like('file', $kode_berkas);
                if ($kode_berkas === 'bebas_lab') {
                    $this->db->or_like('nama', 'bebas')->or_like('nama', 'lab');
                }
            $this->db->group_end();

            $fp_records = $this->db->get('file_pendaftaran')->result_array();

            if (!empty($fp_records)) {
                foreach ($fp_records as $rec) {
                    if (!empty($rec['file'])) {
                        $f_base = basename($rec['file']);
                        $paths_to_check = [
                            FCPATH . $rec['file'],
                            FCPATH . 'uploads/persyaratan_ta/' . $f_base,
                            FCPATH . 'uploads/pendaftaran_berkas/' . $f_base
                        ];
                        foreach ($paths_to_check as $p_chk) {
                            if (file_exists($p_chk) && is_file($p_chk)) {
                                @unlink($p_chk);
                            }
                        }
                    }
                    $this->db->where('id', $rec['id'])->delete('file_pendaftaran');
                }
            }

            // Hapus juga jika ada id persis
            $exact_ids = [
                'fp_' . $clean_nim . '_' . $kode_berkas,
                'fp_' . $nim . '_' . $kode_berkas
            ];
            $this->db->where_in('id', $exact_ids)->delete('file_pendaftaran');
        }

        // 4. Bersihkan cache di AdminLayanan_model agar pembacaan berikutnya bersih
        $this->load->model('AdminLayanan_model');
        if (method_exists($this->AdminLayanan_model, 'clear_cache')) {
            $this->AdminLayanan_model->clear_cache();
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

    /**
     * [MODUL TA] AJAX Auto-Save Draft formulir pendaftaran TA (Multi-Step Form: Step 1, 2, atau 3).
     * Menyimpan progres judul, bahasa inggris, jenis TA, dan nomor tahapan draft ke tabel pendaftaran_ta.
     *
     * @return json Response status success beserta draft_step tersimpan
     * @affected_table pendaftaran_ta
     */
    public function ajax_save_draft_ta() {
        $prev_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        try {
            $nim = $this->input->post('nim') ?: $this->_get_current_nim();
            $clean_nim = preg_replace('/^(usr_mhs_|mhs_|usr_)/i', '', trim((string)$nim));
            $user_id_real = $this->_get_current_user_id();
            if (empty($user_id_real) || $user_id_real === $nim || is_numeric($user_id_real)) {
                $u_row = $this->db->get_where('user', ['nim' => $clean_nim])->row_array();
                if (!$u_row) {
                    $u_row = $this->db->get_where('user', ['id' => $clean_nim])->row_array();
                }
                if ($u_row && !empty($u_row['id'])) {
                    $user_id_real = $u_row['id'];
                }
            }
            $u_id_default = !empty($user_id_real) ? $user_id_real : ('usr_mhs_' . $clean_nim);
            $target_ids = array_values(array_unique(array_filter([
                (string)$user_id_real,
                (string)$nim,
                $clean_nim,
                $u_id_default,
                'usr_mhs_' . $clean_nim,
                'mhs_' . $clean_nim,
                'usr_' . $clean_nim
            ])));

            $mhs = $this->Mahasiswa_model->get_mahasiswa($nim);
            $mhs_konsentrasi = !empty($mhs['konsentrasi_dkv']) ? $mhs['konsentrasi_dkv'] : (!empty($mhs['prodi']) ? $mhs['prodi'] : '-');
            $mhs_id_kk = !empty($mhs['id_kk']) ? $mhs['id_kk'] : 1;
            $existing_status = $this->Mahasiswa_model->get_status_pendaftaran($nim);

            $jenis_ta = $this->input->post('jenis_ta', true);
            if (empty($jenis_ta) && !empty($existing_status['jenis_ta'])) {
                $jenis_ta = $existing_status['jenis_ta'];
            }
            $judul_1  = $this->input->post('judul_1', true);
            if (empty($judul_1) && !empty($existing_status['judul_1'])) {
                $judul_1 = $existing_status['judul_1'];
            }
            $judul_2  = $this->input->post('judul_2', true);
            if (empty($judul_2) && !empty($existing_status['judul_2'])) {
                $judul_2 = $existing_status['judul_2'];
            }
            $judul_3  = $this->input->post('judul_3', true);
            if (empty($judul_3) && !empty($existing_status['judul_3'])) {
                $judul_3 = $existing_status['judul_3'];
            }
            $judul_en = $this->input->post('judul_en', true);
            if (empty($judul_en) && !empty($existing_status['judul_en'])) {
                $judul_en = $existing_status['judul_en'];
            }
            $konsentrasi_dkv = $this->input->post('konsentrasi_dkv', true) ?: ($existing_status['konsentrasi_dkv'] ?? $mhs_konsentrasi);

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
            $db_saved = false;

            if (is_array($post_data)) {
                foreach ($post_data as $key => $val) {
                    if (empty($val) || !is_string($val)) continue;
                    if (preg_match('/^file_([a-z0-9_]+)$/i', $key)) {
                        $clean_key = preg_replace('/_old$/i', '', $key);
                        $raw_kode  = str_replace('file_', '', $clean_key);
                        $col_name  = 'file_' . $raw_kode;
                        $val = trim($val);

                        if (!empty($val) && strtolower(substr($val, -4)) === '.pdf') {
                            $has_any_file = true;

                            // 1. Simpan ke pendaftaran_ta jika tabel & kolom ada
                            if ($this->db->table_exists('pendaftaran_ta') && $this->db->field_exists($col_name, 'pendaftaran_ta')) {
                                $data_update[$col_name] = $val;
                            }

                            // 2. Simpan ke model AdminLayanan (akan sinkron ke file_pendaftaran / pendaftaran_berkas)
                            if (method_exists($this->AdminLayanan_model, 'save_student_berkas')) {
                                $this->AdminLayanan_model->save_student_berkas($nim, $raw_kode, $val, 'Pending');
                                $db_saved = true;
                            }

                            // 3. Simpan ke tabel pendaftaran_berkas jika ada
                            if ($this->db->table_exists('pendaftaran_berkas')) {
                                $ex_b = $this->db->get_where('pendaftaran_berkas', ['nim' => $nim, 'kode_berkas' => $raw_kode])->row_array();
                                if ($ex_b) {
                                    $this->db->where('id', $ex_b['id'])->update('pendaftaran_berkas', [
                                        'file_name'         => $val,
                                        'status_verifikasi' => 'Pending',
                                        'updated_at'        => date('Y-m-d H:i:s')
                                    ]);
                                } else {
                                    $this->db->insert('pendaftaran_berkas', [
                                        'nim'               => $nim,
                                        'kode_berkas'       => $raw_kode,
                                        'file_name'         => $val,
                                        'status_verifikasi' => 'Pending',
                                        'created_at'        => date('Y-m-d H:i:s')
                                    ]);
                                }
                                $db_saved = true;
                            }

                            // 4. Pastikan file juga tersimpan langsung di tabel file_pendaftaran
                            if ($this->db->table_exists('file_pendaftaran')) {
                                $fp_fields = $this->db->list_fields('file_pendaftaran');
                                $id_fp = 'fp_' . $clean_nim . '_' . $raw_kode;
                                $rel_file_path = (strpos($val, 'uploads/') === 0) ? $val : ('uploads/persyaratan_ta/' . $val);

                                $ex_fp = $this->db->group_start()
                                    ->where('id', $id_fp)
                                    ->or_where('id', 'fp_' . $nim . '_' . $raw_kode)
                                    ->or_group_start()
                                        ->where_in('id_mhs', $target_ids)
                                        ->where('nama', $raw_kode)
                                    ->group_end()
                                ->group_end()
                                ->get('file_pendaftaran')->row_array();

                                $fp_payload = array();
                                if (in_array('file', $fp_fields)) $fp_payload['file'] = $rel_file_path;
                                if (in_array('status_doswal', $fp_fields)) $fp_payload['status_doswal'] = 'Pending';
                                if (in_array('status_adminlaa', $fp_fields)) $fp_payload['status_adminlaa'] = 'Pending';
                                if (in_array('view_adminlaa', $fp_fields)) $fp_payload['view_adminlaa'] = 0;
                                if (in_array('view_doswal', $fp_fields)) $fp_payload['view_doswal'] = 0;
                                if (in_array('date_edit', $fp_fields)) $fp_payload['date_edit'] = date('Y-m-d H:i:s');

                                if ($ex_fp) {
                                    $this->db->where('id', $ex_fp['id'])->update('file_pendaftaran', $fp_payload);
                                } else {
                                    if (in_array('id', $fp_fields)) $fp_payload['id'] = $id_fp;
                                    if (in_array('id_mhs', $fp_fields)) $fp_payload['id_mhs'] = $u_id_default;
                                    if (in_array('nama', $fp_fields)) $fp_payload['nama'] = $raw_kode;
                                    if (in_array('komentar', $fp_fields)) $fp_payload['komentar'] = '';
                                    if (in_array('date', $fp_fields)) $fp_payload['date'] = date('Y-m-d H:i:s');
                                    $this->db->insert('file_pendaftaran', $fp_payload);
                                }
                                $db_saved = true;
                            }
                        }
                    }
                }
            }

            $data_update['updated_at'] = date('Y-m-d H:i:s');

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
                $existing_g = $this->db->where_in('id_mhs', $target_ids)
                    ->order_by('date', 'DESC')
                    ->limit(1)
                    ->get('guidance')->row_array();
                if (!$existing_g) {
                    $existing_g = $this->db->get_where('guidance', ['id' => 'gdn_' . $nim])->row_array();
                }

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
                    } else {
                        if (in_array('id', $g_fields)) $g_data['id'] = 'gdn_' . $nim;
                        if (in_array('id_mhs', $g_fields)) $g_data['id_mhs'] = $u_id_default;
                        if (in_array('date', $g_fields)) $g_data['date'] = date('Y-m-d H:i:s');
                        if (in_array('tahun', $g_fields)) $g_data['tahun'] = date('Y');
                        if (in_array('keterangan', $g_fields)) $g_data['keterangan'] = 'Draft';
                        $this->db->insert('guidance', $g_data);
                        $db_saved = true;
                    }
                } elseif ($has_any_file && !$existing_g) {
                    // Buat guidance draft jika ada berkas yang diunggah
                    $g_data = array();
                    if (in_array('id', $g_fields)) $g_data['id'] = 'gdn_' . $nim;
                    if (in_array('id_mhs', $g_fields)) $g_data['id_mhs'] = $u_id_default;
                    if (in_array('date', $g_fields)) $g_data['date'] = date('Y-m-d H:i:s');
                    if (in_array('tahun', $g_fields)) $g_data['tahun'] = date('Y');
                    if (in_array('keterangan', $g_fields)) $g_data['keterangan'] = 'Draft';
                    if (in_array('peminatan', $g_fields)) $g_data['peminatan'] = $konsentrasi_dkv;
                    $this->db->insert('guidance', $g_data);
                    $db_saved = true;
                }
            }

            if (method_exists($this->AdminLayanan_model, 'clear_cache')) {
                $this->AdminLayanan_model->clear_cache();
            }

            $this->db->db_debug = $prev_debug;
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'db_saved' => $db_saved,
                    'message' => 'Draft formulir & berkas berhasil tersimpan di database server.'
                ]));
        } catch (\Throwable $e) {
            $this->db->db_debug = $prev_debug;
            log_message('error', 'ajax_save_draft_ta exception: ' . $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
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
                'Laboratorium Bengkel dan Studio' => [
                    'Fasilitas Ruangan / AC / Proyektor Lab',
                    'Perangkat Komputer / Hardware / Monitor',
                    'Drawing Tablet / Display Cintiq / Alat Digital',
                    'Software / Lisensi Aplikasi Praktikum & Studio',
                    'Jaringan & Koneksi Internet Lab',
                    'Peralatan & Bahan Praktikum Bengkel / Studio',
                    'Peminjaman & Pemeliharaan Alat Lab',
                    'Lain-lain (Laboratorium Bengkel dan Studio)'
                ],
                'Layanan Akademik - LAA' => [
                    'Surat Keterangan Mahasiswa / Pengantar Kuliah',
                    'Administrasi Nilai, KSM & Transkrip Akademik',
                    'Registrasi Mata Kuliah / Perubahan KRS',
                    'Pengajuan Cuti Akademik / Aktif Kembali',
                    'Jadwal Kuliah & Ruang Ujian (UTS/UAS)',
                    'Legalisir Ijazah, Transkrip & KTM',
                    'Lain-lain (Layanan Akademik - LAA)'
                ],
                'Kemahasiswaan' => [
                    'Beasiswa & Bantuan Finansial Mahasiswa',
                    'Kegiatan Ormawa, UKM & Himpunan Mahasiswa',
                    'Lomba, Kompetisi & Pencatatan Prestasi Mahasiswa',
                    'Surat Rekomendasi & Izin Kegiatan Mahasiswa',
                    'Pembinaan Karakter, Konseling & Tata Tertib',
                    'Asuransi & Kesejahteraan Mahasiswa',
                    'Lain-lain (Kemahasiswaan)'
                ],
                'Sekretariat' => [
                    'Penerbitan Surat Keputusan (SK) Dekanat',
                    'Pengesahan & Tanda Tangan Dokumen Pimpinan',
                    'Administrasi Persuratan Masuk & Keluar Fakultas',
                    'Disposisi & Permohonan Informasi Resmi',
                    'Peminjaman Fasilitas Rapat / Ruang Dekanat',
                    'Lain-lain (Sekretariat)'
                ],
                'SDM dan Keuangan' => [
                    'Pembayaran BPP / Registrasi Keuangan Kuliah',
                    'Dispensasi & Penyesuaian Tagihan Biaya Kuliah',
                    'Konfirmasi & Verifikasi Bukti Pembayaran',
                    'Administrasi Penggajian, Honor & Klaim (SDM)',
                    'Berkas Kepegawaian & Verifikasi Dosen / Tendik',
                    'Anggaran Operasional & Pengadaan Sarana Unit',
                    'Lain-lain (SDM dan Keuangan)'
                ],
                'Program Studi (DKV, DI, DP, KTF, SR, Film dan Animasi)' => [
                    'Kurikulum, Silabus & Mata Kuliah Program Studi',
                    'Bimbingan Akademik / Dosen Pembimbing Prodi',
                    'Pendaftaran, Seminar Proposal & Sidang TA / Skripsi',
                    'Pengajuan / Perubahan Judul & Pembimbing TA',
                    'Konversi SKS Magang / MBKM / Kerja Praktik (KP)',
                    'Surat Rekomendasi / Keterangan Program Studi',
                    'Lain-lain (Program Studi)'
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

        $unit_tujuan      = trim($this->input->post('unit_tujuan', true)) ?: (trim($this->input->post('tujuan_penerima', true)) ?: trim($this->input->post('unit_terkait', true)));
        if (empty($unit_tujuan)) {
            $unit_tujuan = 'Laboratorium Bengkel dan Studio';
        }
        $sub_prodi = trim($this->input->post('sub_prodi', true) ?? '');
        if (!empty($sub_prodi) && (strpos($unit_tujuan, 'Program Studi') !== false || strpos($unit_tujuan, 'Prodi') !== false)) {
            $unit_tujuan = "Program Studi - {$sub_prodi}";
        }
        $tujuan_penerima  = $unit_tujuan;
        $unit_terkait     = $unit_tujuan;
        $kategori         = trim($this->input->post('kategori', true));
        $kategori_lainnya = trim($this->input->post('kategori_lainnya', true));
        $prioritas        = trim($this->input->post('prioritas', true));
        $subjek           = trim($this->input->post('subjek', true));
        $deskripsi        = $this->input->post('deskripsi');

        $textOnly = trim(strip_tags($deskripsi));
        if (empty($namaLengkap) || empty($unit_tujuan) || empty($kategori) || empty($subjek) || empty($textOnly)) {
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
            'status'          => 'Dikirim'
        ];

        $insertedId = $this->DosenTicketing_model->insert($ticketData);

        if ($insertedId) {
            $msg = "Tiket kendala berhasil diajukan dengan Kode: <b>{$kodeTiket}</b> ditujukan kepada <b>" . htmlspecialchars($unit_tujuan) . "</b>. Mohon pantau status respon secara berkala.";
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

    // =========================================================
// PERSYARATAN SIDANG (DINAMIS)
// =========================================================

public function get_master_syarat_sidang_ajax() {
    header('Content-Type: application/json');
    $nim = $this->_get_current_nim();

    $this->load->model('AdminLayanan_model');
    $master   = $this->AdminLayanan_model->get_master_syarat_sidang();
    $uploaded = $this->AdminLayanan_model->get_berkas_pendaftaran_sidang($nim);

    echo json_encode([
        'status'   => true,
        'master'   => $master,
        'uploaded' => $uploaded
    ]);
}

public function upload_berkas_sidang_ajax() {
    header('Content-Type: application/json');
    $nim = $this->_get_current_nim();

    if (empty($_FILES['berkas']['name'])) {
        echo json_encode(['status' => false, 'message' => 'Tidak ada berkas yang dipilih.']);
        return;
    }

    $upload_dir = './uploads/persyaratan_ta/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $this->load->model('AdminLayanan_model');
    $this->load->library('upload');

    $uploaded_count = 0;
    $errors = [];

    foreach ($_FILES['berkas']['name'] as $kode => $origName) {
        if (empty($origName) || $_FILES['berkas']['error'][$kode] !== UPLOAD_ERR_OK) continue;

        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $new_name = $kode . '_' . $nim . '_' . time() . '_' . rand(100,999) . '.' . $ext;

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf|doc|docx|jpg|jpeg|png',
            'max_size'      => 5120,
            'file_name'     => $new_name,
        ];

        $this->upload->initialize($config);

        $_FILES['userfile'] = [
            'name'     => $_FILES['berkas']['name'][$kode],
            'type'     => $_FILES['berkas']['type'][$kode],
            'tmp_name' => $_FILES['berkas']['tmp_name'][$kode],
            'error'    => $_FILES['berkas']['error'][$kode],
            'size'     => $_FILES['berkas']['size'][$kode],
        ];

        if ($this->upload->do_upload('userfile')) {
            $fd = $this->upload->data();
            // Simpan ke pendaftaran_berkas + auto-sync ke file_pendaftaran
            $this->AdminLayanan_model->save_student_berkas(
                $nim, $kode, $fd['file_name'], 'Pending', null, ''
            );
            $uploaded_count++;
        } else {
            $errors[] = $kode . ': ' . $this->upload->display_errors('', '');
        }
    }

    if ($uploaded_count > 0) {
        echo json_encode([
            'status'  => true,
            'message' => $uploaded_count . ' berkas berhasil diunggah & menunggu verifikasi LAA.',
            'errors'  => $errors
        ]);
    } else {
        echo json_encode([
            'status'  => false,
            'message' => 'Gagal: ' . implode('; ', $errors)
        ]);
    }
}


    // =========================================================================
    // MODUL PERWALIAN & KONTRAK KRS MAHASISWA
    // =========================================================================

    /**
     * Halaman Utama Perwalian Mahasiswa (Tahap 1 Kontrak KRS & Riwayat Nilai)
     */
    public function perwalian() {
        $this->load->model('Perwalian_model');
        $nim = $this->_get_current_nim();

        if (empty($nim)) {
            $this->session->set_flashdata('error', 'Identitas NIM tidak ditemukan.');
            redirect('mahasiswa');
            return;
        }

        $tahun_ajaran = $this->input->get('tahun', true) ?: '2024/2025';
        $semester     = $this->input->get('semester', true) ?: 'Ganjil';

        $perwalian = $this->Perwalian_model->get_active_perwalian_by_nim($nim, $tahun_ajaran, $semester);
        $id_perwalian = (int)$perwalian['id_perwalian'];

        $mk_kontrak = $this->Perwalian_model->get_perwalian_detail($id_perwalian, 'akan_diambil');
        $mk_sudah   = $this->Perwalian_model->get_riwayat_mk_sudah_diambil($nim);

        // Auto-heal status_tahap2 sinkron dengan kondisi riil nilai mata kuliah
        $totalMK = count($mk_kontrak);
        $totalDinilai = 0;
        foreach ($mk_kontrak as $m) {
            if (!empty(trim((string)($m['nilai_huruf'] ?? '')))) {
                $totalDinilai++;
            }
        }

        if ($totalMK > 0) {
            $expectedSt2 = 'Belum Dinilai';
            if ($totalDinilai === $totalMK && ($perwalian['status_tahap2'] ?? '') === 'Selesai') {
                $expectedSt2 = 'Selesai';
            } elseif ($totalDinilai > 0) {
                $expectedSt2 = 'Dinilai';
            } else {
                $expectedSt2 = 'Belum Dinilai';
            }

            if (($perwalian['status_tahap2'] ?? '') !== $expectedSt2) {
                $this->db->where('id_perwalian', $id_perwalian)->update('perwalian', ['status_tahap2' => $expectedSt2]);
                $perwalian['status_tahap2'] = $expectedSt2;
            }
        }

        $data['title']              = 'Perwalian & Kontrak KRS - IFIK';
        $data['nim']                = $nim;
        $data['nama_mhs']           = $perwalian['nama_mahasiswa'];
        $data['perwalian']          = $perwalian;
        $data['mk_kontrak']         = $mk_kontrak;
        $data['total_sks_kontrak']  = array_sum(array_column($mk_kontrak, 'sks'));
        $data['mk_sudah']           = $mk_sudah;
        $data['master_mk']          = $this->Perwalian_model->get_mata_kuliah(['is_active' => 1]);
        $data['tahun_ajaran']       = $tahun_ajaran;
        $data['semester']           = $semester;

        $this->load->view('mahasiswa/perwalian', $data);
    }

    /**
     * AJAX: Upload Bukti Fisik/Resmi KRS dari SIAKAD/i-Gracias
     */
    public function perwalian_upload_krs() {
        $this->output->set_content_type('application/json');
        $this->load->model('Perwalian_model');
        $nim = $this->_get_current_nim();

        $id_perwalian = (int)$this->input->post('id_perwalian');
        if (!$id_perwalian) {
            $perwalian = $this->Perwalian_model->get_active_perwalian_by_nim($nim);
            $id_perwalian = (int)$perwalian['id_perwalian'];
        }

        $upload_dir = FCPATH . 'uploads/krs/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }

        $config['upload_path']   = $upload_dir;
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['max_size']      = 5120; // 5MB
        $config['file_name']     = 'krs_' . $nim . '_' . time();

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('file_krs')) {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => $this->upload->display_errors('', '')
            ]));
            return;
        }

        $uploadData = $this->upload->data();
        $filename   = $uploadData['file_name'];

        $this->Perwalian_model->update_bukti_krs($id_perwalian, $filename);

        $this->output->set_output(json_encode([
            'status'    => 'success',
            'message'   => 'Bukti KRS resmi berhasil diunggah.',
            'file_name' => $filename,
            'file_url'  => base_url('uploads/krs/' . $filename),
        ]));
    }

    /**
     * AJAX: Tambah Mata Kuliah ke Kontrak Mahasiswa
     */
    public function perwalian_tambah_mk() {
        $this->output->set_content_type('application/json');
        $this->load->model('Perwalian_model');
        $nim = $this->_get_current_nim();

        $id_perwalian = (int)$this->input->post('id_perwalian');
        $kode_mk      = strtoupper(trim($this->input->post('kode_mk', true)));

        if (!$id_perwalian) {
            $perwalian = $this->Perwalian_model->get_active_perwalian_by_nim($nim);
            $id_perwalian = (int)$perwalian['id_perwalian'];
        }

        if (empty($kode_mk)) {
            $this->output->set_output(json_encode(['status' => 'error', 'message' => 'Kode MK tidak boleh kosong.']));
            return;
        }

        $this->Perwalian_model->add_mk_to_kontrak($id_perwalian, $kode_mk);
        $this->output->set_output(json_encode([
            'status'  => 'success',
            'message' => 'Mata kuliah berhasil ditambahkan ke kontrak.'
        ]));
    }

    /**
     * AJAX: Hapus Mata Kuliah dari Kontrak Mahasiswa
     */
    public function perwalian_hapus_mk() {
        $this->output->set_content_type('application/json');
        $this->load->model('Perwalian_model');
        $nim = $this->_get_current_nim();

        $id_perwalian = (int)$this->input->post('id_perwalian');
        $kode_mk      = trim($this->input->post('kode_mk', true));

        if (!$id_perwalian) {
            $perwalian = $this->Perwalian_model->get_active_perwalian_by_nim($nim);
            $id_perwalian = (int)$perwalian['id_perwalian'];
        }

        $this->Perwalian_model->remove_mk_from_kontrak($id_perwalian, $kode_mk);
        $this->output->set_output(json_encode([
            'status'  => 'success',
            'message' => 'Mata kuliah berhasil dihapus dari kontrak.'
        ]));
    }

    /**
     * AJAX: Submit Pengajuan Perwalian Tahap 1 ke Dosen Wali
     */
    public function perwalian_submit() {
        $this->output->set_content_type('application/json');
        $this->load->model('Perwalian_model');
        $nim = $this->_get_current_nim();

        $id_perwalian = (int)$this->input->post('id_perwalian');
        $catatan      = trim($this->input->post('catatan_mahasiswa', true));

        if (!$id_perwalian) {
            $perwalian = $this->Perwalian_model->get_active_perwalian_by_nim($nim);
            $id_perwalian = (int)$perwalian['id_perwalian'];
        } else {
            $perwalian = $this->Perwalian_model->get_perwalian_by_id($id_perwalian);
        }

        // Validasi: Wajib ada minimal 1 MK dikontrak
        $mk_kontrak = $this->Perwalian_model->get_perwalian_detail($id_perwalian, 'akan_diambil');
        if (empty($mk_kontrak)) {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Anda belum memilih/mengontrak mata kuliah untuk semester ini.'
            ]));
            return;
        }

        // Validasi: Wajib ada file KRS bukti
        if (empty($perwalian['file_krs'])) {
            $this->output->set_output(json_encode([
                'status'  => 'error',
                'message' => 'Harap unggah bukti fisik/resmi KRS dari sistem kampus terlebih dahulu sebagai bukti sah.'
            ]));
            return;
        }

        $this->Perwalian_model->submit_perwalian_tahap1($id_perwalian, $catatan);

        $this->output->set_output(json_encode([
            'status'  => 'success',
            'message' => 'Pengajuan perwalian Tahap 1 berhasil dikirimkan ke Dosen Wali.'
        ]));
    }

    /**
     * AJAX: Update Nilai Per Mata Kuliah oleh Mahasiswa (Tahap 2)
     */
    public function perwalian_update_nilai() {
        $this->output->set_content_type('application/json');
        $this->load->model('Perwalian_model');

        $id_detail = (int)$this->input->post('id_detail', true);
        $nilai = strtoupper(trim((string)$this->input->post('nilai_huruf', true)));
        $status_kelulusan = trim((string)$this->input->post('status_kelulusan', true)) ?: null;

        if (!$id_detail) {
            $this->output->set_output(json_encode(['status' => false, 'message' => 'ID detail tidak valid.']));
            return;
        }

        $res = $this->Perwalian_model->update_nilai_detail($id_detail, $nilai, $status_kelulusan);
        $this->output->set_output(json_encode([
            'status'  => true,
            'message' => 'Nilai & status otomatis diperbarui.',
            'data'    => $res
        ]));
    }

    /**
     * AJAX: Import Nilai Mahasiswa dari Excel (Tahap 2)
     */
    public function perwalian_import_nilai() {
        @set_time_limit(300);
        $this->output->set_content_type('application/json');
        $this->load->model('Perwalian_model');

        $nim = $this->_get_current_nim();
        $rawInput = file_get_contents('php://input');
        $json = json_decode($rawInput, true);

        $rows = $json['rows'] ?? [];
        $id_perwalian = !empty($json['id_perwalian']) ? (int)$json['id_perwalian'] : null;

        if (empty($id_perwalian)) {
            $active = $this->Perwalian_model->get_active_perwalian_by_nim($nim);
            $id_perwalian = (int)($active['id_perwalian'] ?? 0);
        }

        if (empty($rows) || !is_array($rows)) {
            $this->output->set_output(json_encode([
                'status'  => false,
                'message' => 'Data baris nilai kosong atau format tidak sesuai.'
            ]));
            return;
        }

        $res = $this->Perwalian_model->import_nilai_single($id_perwalian, $rows);
        $this->output->set_output(json_encode($res));
    }

    /**
     * AJAX: Selesaikan & Kunci Perwalian Tahap 2 oleh Mahasiswa
     */
    public function perwalian_submit_tahap2() {
        $this->output->set_content_type('application/json');
        $this->load->model('Perwalian_model');

        $id_perwalian = (int)$this->input->post('id_perwalian', true);
        if (!$id_perwalian) {
            $nim = $this->_get_current_nim();
            $active = $this->Perwalian_model->get_active_perwalian_by_nim($nim);
            $id_perwalian = (int)($active['id_perwalian'] ?? 0);
        }

        if (!$id_perwalian) {
            $this->output->set_output(json_encode(['status' => false, 'message' => 'ID Perwalian tidak valid.']));
            return;
        }

        $res = $this->Perwalian_model->submit_perwalian_tahap2($id_perwalian);
        $this->output->set_output(json_encode($res));
    }
    // =========================================================
    // KERJA PRAKTIK (KP) - PENGAJUAN, PERSYARATAN DINAMIS & LAPORAN
    // =========================================================
    // MODUL PENGAJUAN & LAPORAN KERJA PRAKTIK (KP)
    // =========================================================

    /**
     * [MODUL KP] Halaman formulir pengajuan KP mahasiswa dan pemrosesan submit pendaftaran instansi.
     * Mengambil profil mahasiswa, konfigurasi persyaratan dinamis, serta kalkulasi tahapan workflow KP.
     *
     * @return void Me-render view mahasiswa/pengajuan_kp
     * @affected_table tb_kp, file_pendaftaran_kp, syarat_berkas_kp
     */
    public function pengajuan_kp() {
        $nim = $this->_get_current_nim();
        $user_id = $this->_get_current_user_id();

        $this->load->model('Kp_model');
        $mahasiswa = $this->Mahasiswa_model->get_mahasiswa($nim);
        $mhs_db    = $this->Kp_model->get_mahasiswa_db($nim, $user_id);
        $wf        = $this->Kp_model->get_workflow_summary($nim, $user_id);

        if ($this->input->post()) {
            $kp_existing = $wf['kp'];
            $is_form_locked = $wf['syarat_all_approved'] || $wf['has_pembimbing1'] || $wf['is_selesai'];

            if ($is_form_locked) {
                $this->session->set_flashdata('error', 'Formulir Pengajuan KP telah dikunci karena persyaratan sudah disetujui.');
                redirect('mahasiswa/pengajuan_kp');
                return;
            }

            $id_kp = $kp_existing['id_kp'] ?? ('kp_' . $nim);
            $peminatan = trim($this->input->post('peminatan') ?? '');
            if ($peminatan === '') {
                $peminatan = $mhs_db['prodi'] ?? '';
            }

            $cur_status_file = $kp_existing['status_file'] ?? '';
            $next_status_file = in_array($cur_status_file, [
                'Disetujui LAA', 'Revisi Persyaratan',
                'Sudah Plot Pembimbing', 'Sudah Upload Laporan', 'Revisi Laporan', 'Selesai'
            ]) ? $cur_status_file : 'Sudah Upload Persyaratan';

            $payload_kp = [
                'id_kp'             => $id_kp,
                'id_mhs'            => !empty($user_id) ? (string)$user_id : (string)$nim,
                'peminatan'         => $peminatan,
                'sks_lulus'         => (int)($this->input->post('sks_lulus') ?: ($kp_existing['sks_lulus'] ?? 0)),
                'sks_berjalan'      => (int)($this->input->post('sks_berjalan') ?: ($kp_existing['sks_berjalan'] ?? 0)),
                'nama_perusahaan'   => trim($this->input->post('nama_perusahaan') ?? ($kp_existing['nama_perusahaan'] ?? '')),
                'alamat_perusahaan' => trim($this->input->post('alamat_perusahaan') ?? ($kp_existing['alamat_perusahaan'] ?? '')),
                'kota'              => trim($this->input->post('kota') ?? ($kp_existing['kota'] ?? '')),
                'kode_pos'          => trim($this->input->post('kode_pos') ?? ($kp_existing['kode_pos'] ?? '')),
                'nama_tujuan'       => trim($this->input->post('nama_tujuan') ?? ($kp_existing['nama_tujuan'] ?? '')),
                'jabatan'           => trim($this->input->post('jabatan') ?? ($kp_existing['jabatan'] ?? '')),
                'posisi_pekerjaan'  => trim($this->input->post('posisi_pekerjaan') ?? ($kp_existing['posisi_pekerjaan'] ?? '')),
                'telp_fax'          => trim($this->input->post('telp_fax') ?? ($kp_existing['telp_fax'] ?? '')),
                'email_perusahaan'  => trim($this->input->post('email_perusahaan') ?? ($kp_existing['email_perusahaan'] ?? '')),
                'cp'                => trim($this->input->post('cp') ?? ($kp_existing['cp'] ?? '')),
                'jenis_kp'          => trim($this->input->post('jenis_kp') ?? ($kp_existing['jenis_kp'] ?? '')),
                'status_file'       => $next_status_file,
                'status_preview'    => 'Pending',
                'tahun'             => !empty($kp_existing['tahun']) ? $kp_existing['tahun'] : date('Y'),
                'date'              => !empty($kp_existing['date']) && $kp_existing['date'] !== '0000-00-00' ? $kp_existing['date'] : date('Y-m-d'),
            ];

            $this->Kp_model->save_kp($nim, $user_id, $payload_kp);

            // Proses upload berkas persyaratan KP dinamis (Step 2) jika ada file baru saat klik Kirim
            $syarat_list    = $wf['syarat_list'];
            $files_existing = $wf['files_syarat'];
            $upload_dir     = './uploads/persyaratan_kp/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $config = [
                'upload_path'   => $upload_dir,
                'allowed_types' => 'pdf',
                'max_size'      => 5120,
                'encrypt_name'  => false,
            ];
            $this->load->library('upload', $config);

            $uploaded_count = 0;
            $upload_errors  = [];

            foreach ($syarat_list as $sb) {
                $kode = $sb['kode_berkas'];
                $field_name = 'file_kp_' . $kode;

                $cur_file_row = $files_existing[$kode] ?? null;
                if ($cur_file_row && $this->Kp_model->is_status_approved($cur_file_row['status_adminlaa'] ?? '')) {
                    continue;
                }

                if (!empty($_FILES[$field_name]['name'])) {
                    $config['file_name'] = 'kp_' . $nim . '_' . $kode . '_' . time();
                    $err = null;
                    $new_filename = $this->_do_upload($field_name, $config, $err);
                    if ($new_filename) {
                        $this->Kp_model->save_file_pendaftaran_kp($nim, $user_id, $id_kp, $kode, $new_filename, 'Pending');
                        $uploaded_count++;
                    } else {
                        $upload_errors[] = ($sb['nama_berkas'] ?? $kode) . ': ' . ($err ?: 'Format harus PDF maks 5 MB');
                    }
                }
            }

            // Ubah seluruh berkas status 'Draft' menjadi 'Pending' agar masuk ke antrean Admin LAA
            $this->Kp_model->submit_files_pendaftaran_kp($nim, $user_id, $id_kp);

            if (!empty($upload_errors)) {
                $this->session->set_flashdata('error', 'Pengajuan KP dikirim, namun sebagian berkas gagal diunggah: ' . implode('; ', $upload_errors));
            } else {
                $this->session->set_flashdata('success', 'Pengajuan KP dan seluruh berkas persyaratan berhasil dikirim ke Admin LAA untuk diverifikasi!');
            }

            redirect('mahasiswa/pengajuan_kp');
            return;
        }

        $data['title']     = 'Pengajuan Kerja Praktik (KP)';
        $data['mahasiswa'] = $mahasiswa;
        $data['mhs_db']    = $mhs_db;
        $data['wf']        = $wf;
        $data['kp']        = $wf['kp'];
        $data['syarat_kp'] = $wf['syarat_list'];
        $data['files_kp']  = $wf['files_syarat'];

        $this->load->view('mahasiswa/pengajuan_kp', $data);
    }

    /**
     * [MODUL KP] AJAX Auto-Save Draft formulir pengajuan instansi KP mahasiswa.
     * Menyimpan data isian sementara ke tabel tb_kp tanpa memicu validasi upload berkas wajib.
     *
     * @return json Response status 'ok', id_kp, status_file, dan waktu tersimpan
     * @affected_table tb_kp
     */
    public function ajax_save_draft_kp() {
        header('Content-Type: application/json');
        $nim = $this->_get_current_nim();
        $user_id = $this->_get_current_user_id();

        $this->load->model('Kp_model');
        $mhs_db = $this->Kp_model->get_mahasiswa_db($nim, $user_id);
        $wf     = $this->Kp_model->get_workflow_summary($nim, $user_id);

        $kp_existing = $wf['kp'];
        $is_form_locked = $wf['syarat_all_approved'] || $wf['has_pembimbing1'] || $wf['is_selesai'];
        if ($is_form_locked) {
            echo json_encode(['status' => 'locked']);
            return;
        }

        $id_kp = $kp_existing['id_kp'] ?? ('kp_' . $nim);
        $peminatan = trim($this->input->post('peminatan') ?? '');
        if ($peminatan === '') {
            $peminatan = $kp_existing['peminatan'] ?? ($mhs_db['prodi'] ?? '');
        }

        $cur_status_file = $kp_existing['status_file'] ?? '';
        $is_already_submitted = in_array($cur_status_file, [
            'Sudah Upload Persyaratan', 'Disetujui LAA', 'Revisi Persyaratan',
            'Sudah Plot Pembimbing', 'Sudah Upload Laporan', 'Revisi Laporan', 'Selesai'
        ]);
        $next_status_file    = $is_already_submitted ? $cur_status_file : 'Draft';
        $next_status_preview = $is_already_submitted ? ($kp_existing['status_preview'] ?? 'Pending') : 'Draft';

        $payload_kp = [
            'id_kp'             => $id_kp,
            'id_mhs'            => !empty($user_id) ? (string)$user_id : (string)$nim,
            'peminatan'         => $peminatan,
            'sks_lulus'         => (int)($this->input->post('sks_lulus') !== null && $this->input->post('sks_lulus') !== '' ? $this->input->post('sks_lulus') : ($kp_existing['sks_lulus'] ?? 0)),
            'sks_berjalan'      => (int)($this->input->post('sks_berjalan') !== null && $this->input->post('sks_berjalan') !== '' ? $this->input->post('sks_berjalan') : ($kp_existing['sks_berjalan'] ?? 0)),
            'nama_perusahaan'   => trim($this->input->post('nama_perusahaan') ?? ($kp_existing['nama_perusahaan'] ?? '')),
            'alamat_perusahaan' => trim($this->input->post('alamat_perusahaan') ?? ($kp_existing['alamat_perusahaan'] ?? '')),
            'kota'              => trim($this->input->post('kota') ?? ($kp_existing['kota'] ?? '')),
            'kode_pos'          => trim($this->input->post('kode_pos') ?? ($kp_existing['kode_pos'] ?? '')),
            'nama_tujuan'       => trim($this->input->post('nama_tujuan') ?? ($kp_existing['nama_tujuan'] ?? '')),
            'jabatan'           => trim($this->input->post('jabatan') ?? ($kp_existing['jabatan'] ?? '')),
            'posisi_pekerjaan'  => trim($this->input->post('posisi_pekerjaan') ?? ($kp_existing['posisi_pekerjaan'] ?? '')),
            'telp_fax'          => trim($this->input->post('telp_fax') ?? ($kp_existing['telp_fax'] ?? '')),
            'email_perusahaan'  => trim($this->input->post('email_perusahaan') ?? ($kp_existing['email_perusahaan'] ?? '')),
            'cp'                => trim($this->input->post('cp') ?? ($kp_existing['cp'] ?? '')),
            'jenis_kp'          => trim($this->input->post('jenis_kp') ?? ($kp_existing['jenis_kp'] ?? '')),
            'status_file'       => $next_status_file,
            'status_preview'    => $next_status_preview,
            'tahun'             => !empty($kp_existing['tahun']) ? $kp_existing['tahun'] : date('Y'),
            'date'              => !empty($kp_existing['date']) && $kp_existing['date'] !== '0000-00-00' ? $kp_existing['date'] : date('Y-m-d'),
        ];

        $this->Kp_model->save_kp($nim, $user_id, $payload_kp);
        echo json_encode([
            'status'      => 'ok',
            'id_kp'       => $id_kp,
            'status_file' => $next_status_file,
            'saved_at'    => date('H:i:s')
        ]);
    }

    /**
     * [MODUL KP] AJAX Upload berkas persyaratan KP dinamis per kode_berkas.
     * Memvalidasi file PDF maks 5MB, menyimpan fisik file ke uploads/persyaratan_kp/, dan mencatat ke file_pendaftaran_kp.
     *
     * @return json Response status berhasil/gagal beserta filename dan info berkas
     * @affected_table file_pendaftaran_kp
     */
    public function ajax_upload_file_kp() {
        header('Content-Type: application/json');
        $nim = $this->_get_current_nim();
        $user_id = $this->_get_current_user_id();
        $kode_berkas = trim((string)($this->input->post('kode_berkas') ?? ''));

        if ($kode_berkas === '') {
            echo json_encode(['success' => false, 'message' => 'Kode berkas tidak valid.']);
            return;
        }

        $this->load->model('Kp_model');
        $mhs_db = $this->Kp_model->get_mahasiswa_db($nim, $user_id);
        $wf     = $this->Kp_model->get_workflow_summary($nim, $user_id);
        $kp_existing = $wf['kp'];
        $id_kp = $kp_existing['id_kp'] ?? ('kp_' . $nim);

        // Pastikan row tb_kp minimal berstatus Draft sudah ada di database
        if (!$kp_existing) {
            $this->Kp_model->save_kp($nim, $user_id, [
                'id_kp'          => $id_kp,
                'id_mhs'         => !empty($user_id) ? (string)$user_id : (string)$nim,
                'peminatan'      => $mhs_db['prodi'] ?? '',
                'status_file'    => 'Draft',
                'status_preview' => 'Draft',
                'tahun'          => date('Y'),
                'date'           => date('Y-m-d'),
            ]);
        }

        $cur_file_row = $wf['files_syarat'][$kode_berkas] ?? null;
        if ($cur_file_row && $this->Kp_model->is_status_approved($cur_file_row['status_adminlaa'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Berkas sudah disetujui LAA dan tidak dapat diubah.']);
            return;
        }

        $field_name = 'file_kp_' . $kode_berkas;
        if (empty($_FILES[$field_name]['name'])) {
            echo json_encode(['success' => false, 'message' => 'File PDF tidak ditemukan.']);
            return;
        }

        $upload_dir = './uploads/persyaratan_kp/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'pdf',
            'max_size'      => 5120,
            'encrypt_name'  => false,
            'file_name'     => 'kp_' . $nim . '_' . $kode_berkas . '_' . time(),
        ];
        $this->load->library('upload', $config);

        $err = null;
        $new_filename = $this->_do_upload($field_name, $config, $err);
        if (!$new_filename) {
            echo json_encode(['success' => false, 'message' => $err ?: 'Format file harus PDF (Maksimal 5 MB).']);
            return;
        }

        // Jika pengajuan KP masih Draft, status berkas di file_pendaftaran_kp juga 'Draft'.
        // Jika sudah pernah disubmit (misal perbaikan revisi), status berkas langsung 'Pending'.
        $cur_status_file = $kp_existing['status_file'] ?? 'Draft';
        $is_submitted_kp = in_array($cur_status_file, [
            'Sudah Upload Persyaratan', 'Disetujui LAA', 'Revisi Persyaratan',
            'Sudah Plot Pembimbing', 'Sudah Upload Laporan', 'Revisi Laporan', 'Selesai'
        ]);
        $status_laa = $is_submitted_kp ? 'Pending' : 'Draft';

        $this->Kp_model->save_file_pendaftaran_kp($nim, $user_id, $id_kp, $kode_berkas, $new_filename, $status_laa);

        echo json_encode([
            'success'     => true,
            'kode_berkas' => $kode_berkas,
            'file_name'   => $new_filename,
            'file_url'    => base_url('uploads/persyaratan_kp/' . $new_filename),
            'status_laa'  => $status_laa,
            'message'     => 'Berkas tersimpan di database (' . $status_laa . ').'
        ]);
    }

    /**
     * [MODUL KP] AJAX Hapus berkas persyaratan KP yang telah diunggah.
     * Berkas yang sudah disetujui LAA dilindungi dan tidak dapat dihapus.
     *
     * @return json Response success true/false
     * @affected_table file_pendaftaran_kp
     */
    public function ajax_delete_file_kp() {
        header('Content-Type: application/json');
        $nim = $this->_get_current_nim();
        $user_id = $this->_get_current_user_id();
        $kode_berkas = trim((string)($this->input->post('kode_berkas') ?? ''));

        if ($kode_berkas === '') {
            echo json_encode(['success' => false, 'message' => 'Kode berkas tidak valid.']);
            return;
        }

        $this->load->model('Kp_model');
        $files = $this->Kp_model->get_files_pendaftaran_kp($nim, null, $user_id);
        $f = $files[$kode_berkas] ?? null;
        if ($f && $this->Kp_model->is_status_approved($f['status_adminlaa'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Berkas yang sudah disetujui LAA tidak dapat dihapus.']);
            return;
        }

        $this->Kp_model->delete_file_pendaftaran_kp($nim, $user_id, $kode_berkas);
        echo json_encode(['success' => true, 'kode_berkas' => $kode_berkas]);
    }

    public function persyaratan_kp() {
        $nim = $this->_get_current_nim();
        $user_id = $this->_get_current_user_id();

        $this->load->model('Kp_model');
        $mahasiswa = $this->Mahasiswa_model->get_mahasiswa($nim);
        $mhs_db    = $this->Kp_model->get_mahasiswa_db($nim, $user_id);
        $wf        = $this->Kp_model->get_workflow_summary($nim, $user_id);

        if ($this->input->post()) {
            if (empty($wf['has_form'])) {
                $this->session->set_flashdata('error', 'Harap isi dan simpan Formulir Pengajuan KP terlebih dahulu sebelum mengunggah persyaratan.');
                redirect('mahasiswa/pengajuan_kp');
                return;
            }

            $kp_existing    = $wf['kp'];
            $syarat_list    = $wf['syarat_list'];
            $files_existing = $wf['files_syarat'];
            $id_kp          = $kp_existing['id_kp'] ?? ('kp_' . $nim);

            $upload_dir = './uploads/persyaratan_kp/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $config = [
                'upload_path'   => $upload_dir,
                'allowed_types' => 'pdf',
                'max_size'      => 5120,
                'encrypt_name'  => false,
            ];
            $this->load->library('upload', $config);

            $uploaded_count = 0;
            $upload_errors  = [];

            foreach ($syarat_list as $sb) {
                $kode = $sb['kode_berkas'];
                $field_name = 'file_kp_' . $kode;

                $cur_file_row = $files_existing[$kode] ?? null;
                if ($cur_file_row && $this->Kp_model->is_status_approved($cur_file_row['status_adminlaa'] ?? '')) {
                    continue;
                }

                if (!empty($_FILES[$field_name]['name'])) {
                    $config['file_name'] = 'kp_' . $nim . '_' . $kode . '_' . time();
                    $err = null;
                    $new_filename = $this->_do_upload($field_name, $config, $err);
                    if ($new_filename) {
                        $this->Kp_model->save_file_pendaftaran_kp($nim, $user_id, $id_kp, $kode, $new_filename);
                        $uploaded_count++;
                    } else {
                        $upload_errors[] = ($sb['nama_berkas'] ?? $kode) . ': ' . ($err ?: 'Format harus PDF maks 5 MB');
                    }
                }
            }

            $updated_files = $this->Kp_model->get_files_pendaftaran_kp($nim, $id_kp, $user_id);
            $all_req_uploaded = !empty($syarat_list) && !empty($updated_files);
            foreach ($syarat_list as $sb) {
                if ((int)($sb['is_required'] ?? 1) === 1) {
                    $k = $sb['kode_berkas'];
                    if (empty($updated_files[$k]['file'])) {
                        $all_req_uploaded = false;
                        break;
                    }
                }
            }

            $is_form_locked = $wf['syarat_all_approved'] || $wf['has_pembimbing1'] || $wf['is_selesai'];
            if (!$is_form_locked && $all_req_uploaded) {
                $this->Kp_model->save_kp($nim, $user_id, ['status_file' => 'Sudah Upload Persyaratan']);
            }

            if (!empty($upload_errors)) {
                $this->session->set_flashdata('error', 'Sebagian berkas gagal diunggah: ' . implode('; ', $upload_errors));
            } elseif ($uploaded_count > 0) {
                $this->session->set_flashdata('success', "Berhasil mengunggah {$uploaded_count} berkas persyaratan KP! Menunggu verifikasi Admin LAA.");
            } else {
                $this->session->set_flashdata('error', 'Pilih minimal 1 berkas PDF untuk diunggah.');
            }

            redirect('mahasiswa/persyaratan_kp');
            return;
        }

        $data['title']     = 'Tahap Upload Persyaratan Kerja Praktik (KP)';
        $data['mahasiswa'] = $mahasiswa;
        $data['mhs_db']    = $mhs_db;
        $data['wf']        = $wf;
        $data['kp']        = $wf['kp'];
        $data['syarat_kp'] = $wf['syarat_list'];
        $data['files_kp']  = $wf['files_syarat'];

        $this->load->view('mahasiswa/persyaratan_kp', $data);
    }

    public function hapus_berkas_kp($kode_berkas = '') {
        $nim = $this->_get_current_nim();
        $user_id = $this->_get_current_user_id();
        $kode_berkas = trim((string)$kode_berkas);

        if ($kode_berkas !== '') {
            $this->load->model('Kp_model');
            $files = $this->Kp_model->get_files_pendaftaran_kp($nim, null, $user_id);
            $f = $files[$kode_berkas] ?? null;
            if ($f && $this->Kp_model->is_status_approved($f['status_adminlaa'] ?? '')) {
                $this->session->set_flashdata('error', 'Berkas yang sudah disetujui LAA tidak dapat dihapus.');
            } else {
                $this->Kp_model->delete_file_pendaftaran_kp($nim, $user_id, $kode_berkas);
                $this->session->set_flashdata('success', 'Berkas persyaratan KP berhasil dihapus.');
            }
        }
        redirect('mahasiswa/pengajuan_kp?step=2');
    }

    /**
     * [MODUL KP] Pengunggahan Laporan Akhir KP dan Lembar Penilaian Pembimbing Lapangan.
     * Mengubah status_file di tb_kp menjadi 'Sudah Upload Laporan' dan menyimpan dokumen ke file_laporan_kp.
     *
     * @return void
     * @affected_table tb_kp, file_laporan_kp
     */
    public function laporan_kp() {
        $nim = $this->_get_current_nim();
        $user_id = $this->_get_current_user_id();

        $this->load->model('Kp_model');
        $mahasiswa = $this->Mahasiswa_model->get_mahasiswa($nim);
        $mhs_db    = $this->Kp_model->get_mahasiswa_db($nim, $user_id);
        $wf        = $this->Kp_model->get_workflow_summary($nim, $user_id);

        if ($this->input->post()) {
            if ($wf['laporan_is_approved']) {
                $this->session->set_flashdata('error', 'Laporan KP Anda sudah disetujui oleh LAA dan berstatus Selesai.');
                redirect('mahasiswa/laporan_kp');
                return;
            }

            if (!$wf['can_upload_laporan']) {
                $this->session->set_flashdata('error', 'Anda belum dapat mengunggah Laporan KP. Pastikan berkas persyaratan telah disetujui Admin LAA dan Pembimbing 1 telah ditetapkan oleh Koordinator KP.');
                redirect('mahasiswa/laporan_kp');
                return;
            }

            $id_kp = $wf['kp']['id_kp'] ?? ('kp_' . $nim);
            $poin_nilai = (int)($this->input->post('poin') ?: ($wf['laporan_item']['poin'] ?? 0));

            if (empty($_FILES['file_laporan_kp']['name'])) {
                $this->session->set_flashdata('error', 'Silakan pilih file PDF Laporan KP (beserta Form Penilaian KP) terlebih dahulu!');
                redirect('mahasiswa/laporan_kp');
                return;
            }

            $upload_dir = './uploads/laporan_kp/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $config = [
                'upload_path'   => $upload_dir,
                'allowed_types' => 'pdf',
                'max_size'      => 10240, // Maks 10 MB untuk laporan + form penilaian
                'file_name'     => 'laporan_kp_' . $nim . '_' . time(),
            ];
            $this->load->library('upload', $config);

            $err = null;
            $uploaded_file = $this->_do_upload('file_laporan_kp', $config, $err);
            if ($uploaded_file) {
                $this->Kp_model->save_file_laporan_kp($nim, $user_id, $id_kp, 'laporan_kp', $uploaded_file, $poin_nilai);
                if (!empty($wf['kp'])) {
                    $this->Kp_model->save_kp($nim, $user_id, ['status_file' => 'Sudah Upload Laporan']);
                }
                $this->session->set_flashdata('success', 'File Laporan KP (beserta Form Penilaian KP) berhasil diunggah! Menunggu verifikasi Admin LAA.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengunggah Laporan KP: ' . ($err ?: 'Pastikan file berformat PDF (Maks 10 MB).'));
            }

            redirect('mahasiswa/laporan_kp');
            return;
        }

        $data['title']     = 'Upload Laporan & Penilaian Kerja Praktik (KP)';
        $data['mahasiswa'] = $mahasiswa;
        $data['mhs_db']    = $mhs_db;
        $data['wf']        = $wf;
        $data['kp']        = $wf['kp'];
        $data['laporan']   = $wf['laporan_item'];

        $this->load->view('mahasiswa/laporan_kp', $data);
    }
}
