<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminHeader extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Header_model');
        $this->load->helper(array('form', 'url'));

        // Pastikan hanya admin (role_id 1) yang bisa akses
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role_id') != 1) {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
            redirect('login');
        }
    }

    public function index()
    {
        $data['settings'] = $this->Header_model->get_settings();
        $data['slides'] = $this->Header_model->get_slides();
        $this->load->view('admin/header_settings', $data);
    }

    public function update_settings()
    {
        $title = $this->input->post('title', true);
        $description = $this->input->post('description', true);

        $update_data = [
            'title' => $title,
            'description' => $description,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Konfigurasi Upload Gambar Dekanat
        if (!empty($_FILES['dekanat_image']['name'])) {
            $config['upload_path']   = './assets/images/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = 'dekanat_' . time();

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if ($this->upload->do_upload('dekanat_image')) {
                $uploadData = $this->upload->data();
                $update_data['dekanat_image'] = $uploadData['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('adminheader');
                return;
            }
        }

        if ($this->Header_model->update_settings($update_data)) {
            $this->session->set_flashdata('success', 'Pengaturan teks dan gambar berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui pengaturan.');
        }

        redirect('adminheader');
    }

    public function add_slide()
    {
        $label = $this->input->post('label', true);
        
        if (empty($_FILES['media_file']['name'])) {
             $this->session->set_flashdata('error', 'Harap pilih file untuk diupload.');
             redirect('adminheader');
             return;
        }

        $file_ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
        if (in_array($file_ext, ['mp4', 'webm', 'ogg'])) {
            $media_type = 'video';
            $config['upload_path']   = './assets/vids/';
            $config['allowed_types'] = 'mp4|webm|ogg';
            $config['max_size']      = 20000; // 20MB
        } else {
            $media_type = 'image';
            $config['upload_path']   = './assets/images/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
            $config['max_size']      = 5048; // 5MB
        }
        $config['file_name'] = 'slide_' . time();

        // Cari urutan terakhir
        $slides = $this->Header_model->get_slides();
        $order_num = count($slides) + 1;
        
        $duration = (int)$this->input->post('duration');
        if ($duration < 1 || $media_type == 'video') $duration = 4;

        // Data teks overlay dari toggle ON/OFF
        $show_text           = (int)$this->input->post('show_text');
        $overlay_title       = $this->input->post('overlay_title', true);
        $overlay_description = $this->input->post('overlay_description'); // HTML dari TinyMCE — jangan di-escape

        $insert_data = [
            'label'               => $label,
            'media_type'          => $media_type,
            'order_num'           => $order_num,
            'duration'            => $duration,
            'show_text'           => $show_text,
            'overlay_title'       => $show_text ? $overlay_title       : null,
            'overlay_description' => $show_text ? $overlay_description : null,
            'created_at'          => date('Y-m-d H:i:s')
        ];

        $this->load->library('upload');
        $this->upload->initialize($config);

        if ($this->upload->do_upload('media_file')) {
            $uploadData = $this->upload->data();
            $insert_data['media_path'] = $uploadData['file_name'];

            if ($this->Header_model->add_slide($insert_data)) {
                $this->session->set_flashdata('success', 'Slide baru berhasil ditambahkan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan slide ke database.');
            }
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
        }

        redirect('adminheader');
    }

    public function edit_slide($id)
    {
        $slide = $this->Header_model->get_slide($id);
        if (!$slide) {
            $this->session->set_flashdata('error', 'Slide tidak ditemukan.');
            redirect('adminheader');
            return;
        }

        $label = $this->input->post('label', true);
        $duration = (int)$this->input->post('duration');
        if ($duration < 1) $duration = 4;
        
        $update_data = ['label' => $label, 'duration' => $duration];
        $media_type = $slide->media_type;

        // Jika upload file baru video, abaikan duration
        if (!empty($_FILES['media_file']['name'])) {
            $file_ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
            if (in_array($file_ext, ['mp4', 'webm', 'ogg'])) {
                $update_data['duration'] = 4;
            }
        } else {
             if ($slide->media_type == 'video') {
                 $update_data['duration'] = 4;
             }
        }

        // Cek file type otomatis
        if (!empty($_FILES['media_file']['name'])) {
            $file_ext = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));
            if (in_array($file_ext, ['mp4', 'webm', 'ogg'])) {
                $media_type = 'video';
                $config['upload_path']   = './assets/vids/';
                $config['allowed_types'] = 'mp4|webm|ogg';
                $config['max_size']      = 20000;
            } else {
                $media_type = 'image';
                $config['upload_path']   = './assets/images/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size']      = 5048;
            }
            $config['file_name'] = 'slide_' . time();
            
            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('media_file')) {
                $uploadData = $this->upload->data();
                $update_data['media_path'] = $uploadData['file_name'];
                $update_data['media_type'] = $media_type;

                // Delete old file if not default
                $old_path = ($slide->media_type == 'video') ? './assets/vids/' : './assets/images/';
                $old_file = $old_path . $slide->media_path;
                if (!in_array($slide->media_path, ['Fakultas.jpg', 'vidtelkom.mp4', 'background.png']) && file_exists($old_file)) {
                    unlink($old_file);
                }
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('adminheader');
                return;
            }
        }

        $this->db->where('id', $id);
        if ($this->db->update('header_slides', $update_data)) {
            $this->session->set_flashdata('success', 'Slide berhasil diupdate.');
        } else {
            $this->session->set_flashdata('error', 'Gagal update slide.');
        }
        redirect('adminheader');
    }

    public function delete_slide($id)
    {
        $slide = $this->Header_model->get_slide($id);
        if ($slide) {
            // Hapus file fisik
            $path = ($slide->media_type == 'video') ? './assets/vids/' : './assets/images/';
            $file_path = $path . $slide->media_path;
            
            // Jangan hapus file default bawaan
            if (!in_array($slide->media_path, ['Fakultas.jpg', 'vidtelkom.mp4', 'background.png']) && file_exists($file_path)) {
                unlink($file_path);
            }

            if ($this->Header_model->delete_slide($id)) {
                $this->session->set_flashdata('success', 'Slide berhasil dihapus.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menghapus slide.');
            }
        }
        redirect('adminheader');
    }
}
