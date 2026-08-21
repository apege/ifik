<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mahasiswa_model');
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
    }

    // Dashboard Mahasiswa & Overview Status Approval Chain
    public function index() {
        $nim = $this->session->userdata('nim') ? $this->session->userdata('nim') : '1301210001'; // Mock NIM
        $data['title'] = 'Dashboard Mahasiswa';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $this->load->view('mahasiswa/dashboard', $data);
    }

    // Detail Lengkap Pendaftaran Tugas Akhir (Single Page View)
    public function detail_pendaftaran() {
        $nim = $this->session->userdata('nim') ? $this->session->userdata('nim') : '1301210001';
        $data['title'] = 'Detail Pengajuan Tugas Akhir';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        $this->load->view('mahasiswa/detail_pendaftaran', $data);
    }

    // Formulir Edit Pendaftaran TA (Single Page Non-Wizard Form)
    public function edit_pendaftaran() {
        $nim = $this->session->userdata('nim') ? $this->session->userdata('nim') : '1301210001';
        $data['title'] = 'Edit Formulir Tugas Akhir';
        $data['mahasiswa'] = $this->Mahasiswa_model->get_mahasiswa($nim);
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

        if ($this->input->post()) {
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
                'status_approval_wali' => 'Pending',
                'current_stage'        => 'Dosen Wali',
                'created_at'           => date('Y-m-d H:i:s')
            );

            $this->Mahasiswa_model->save_pendaftaran_ta($data_ta);
            $this->session->set_flashdata('success', 'Perubahan formulir Tugas Akhir berhasil disimpan!');
            redirect('mahasiswa');
            return;
        }

        $this->load->view('mahasiswa/edit_pendaftaran', $data);
    }

    // Fitur Geodata Mahasiswa
    public function geodata() {
        $nim = $this->session->userdata('nim') ? $this->session->userdata('nim') : '1301210001';
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
        $nim = $this->session->userdata('nim') ? $this->session->userdata('nim') : '1301210001';
        $data['title'] = 'Pendaftaran Tugas Akhir (6 Step)';
        $data['pendaftaran'] = $this->Mahasiswa_model->get_status_pendaftaran($nim);

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
                'status_approval_wali' => 'Pending',
                'current_stage'        => 'Dosen Wali',
                'created_at'           => date('Y-m-d H:i:s')
            );

            $this->Mahasiswa_model->save_pendaftaran_ta($data_ta);
            $this->session->set_flashdata('success', 'Pendaftaran Tugas Akhir berhasil dikirim!');
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
        $nim = $this->session->userdata('nim') ? $this->session->userdata('nim') : '1301210001';
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
        $nim = $this->session->userdata('nim') ? $this->session->userdata('nim') : '1301210001';
        $this->Mahasiswa_model->reset_pendaftaran_ta($nim);
        $this->session->set_flashdata('success', 'Pengajuan Tugas Akhir berhasil di-reset!');
        redirect('mahasiswa');
    }
}
