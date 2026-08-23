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
                'status_approval_wali' => $w_status,
                'status_approval_admin'=> $a_status,
                'status_approval_koor' => $k_status,
                'status_approval_kk'   => $kk_status,
                'current_stage'        => $current_stage,
                'created_at'           => isset($existing_ta['created_at']) ? $existing_ta['created_at'] : date('Y-m-d H:i:s')
            );

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
}
