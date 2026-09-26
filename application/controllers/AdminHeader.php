<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminHeader extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Header_model');
        $this->load->helper(array('form', 'url'));

         // Pastikan hanya admin (1), kaur (2), atau laboran (21) yang bisa akses
         $role_id = (int)$this->session->userdata('role_id');
         if (!$this->session->userdata('logged_in') || ($role_id !== 1 && $role_id !== 2 && $role_id !== 21)) {
             $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
             redirect('login');
         }
    }

    public function index()
    {
        $this->load->model('Booking_model');
        $data['settings'] = $this->Header_model->get_settings();
        $data['slides']   = $this->Header_model->get_slides();
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        
        // Fetch data ruangan dengan nama kategori (digrupkan per fasilitas)
        $data['ruangan'] = $this->Booking_model->get_all_ruangan_grouped();

        $data['active_tab'] = $this->input->get('tab', true) === 'fasilitas' ? 'fasilitas' : 'header';

        $this->load->view('admin/header_settings', $data);
    }

    public function update_settings()
    {
        $title = $this->input->post('title', true);
        $description = trim(strip_tags($this->input->post('description')));

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
        
        if (empty($_FILES['media_files']['name'][0])) {
             $this->session->set_flashdata('error', 'Harap pilih file untuk diupload.');
             redirect('adminheader');
             return;
        }

        $durations = $this->input->post('durations');
        $overlay_title = $this->input->post('overlay_title', true);
        $overlay_description = trim(strip_tags($this->input->post('overlay_description')));

        $slides = $this->Header_model->get_slides();
        $order_num = count($slides) + 1;

        $this->load->library('upload');
        
        $uploaded_files = [];
        $files_count = count($_FILES['media_files']['name']);
        
        for ($i = 0; $i < $files_count; $i++) {
            $_FILES['file']['name']     = $_FILES['media_files']['name'][$i];
            $_FILES['file']['type']     = $_FILES['media_files']['type'][$i];
            $_FILES['file']['tmp_name'] = $_FILES['media_files']['tmp_name'][$i];
            $_FILES['file']['error']    = $_FILES['media_files']['error'][$i];
            $_FILES['file']['size']     = $_FILES['media_files']['size'][$i];
            
            $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if (in_array($file_ext, ['mp4', 'webm', 'ogg'])) {
                $type = 'video';
                $config['upload_path']   = './assets/vids/';
                $config['allowed_types'] = 'mp4|webm|ogg';
                $config['max_size']      = 20000;
            } else {
                $type = 'image';
                $config['upload_path']   = './assets/images/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size']      = 5048;
            }
            $config['file_name'] = 'slide_' . time() . '_' . $i;

            $this->upload->initialize($config);
            
            if ($this->upload->do_upload('file')) {
                $uploadData = $this->upload->data();
                $uploaded_files[] = [
                    'file' => $uploadData['file_name'],
                    'type' => $type,
                    'duration' => isset($durations[$i]) ? (int)$durations[$i] : 3
                ];
            }
        }
        
        if (count($uploaded_files) > 0) {
            $insert_data = [
                'label'               => $label,
                'media_type'          => (count($uploaded_files) > 1) ? 'multi' : $uploaded_files[0]['type'],
                'media_path'          => json_encode($uploaded_files),
                'order_num'           => $order_num,
                'duration'            => 0,
                'show_text'           => 1,
                'overlay_title'       => $overlay_title,
                'overlay_description' => $overlay_description,
                'created_at'          => date('Y-m-d H:i:s')
            ];

            if ($this->Header_model->add_slide($insert_data)) {
                $this->session->set_flashdata('success', 'Slide baru berhasil ditambahkan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan slide ke database.');
            }
        } else {
            $this->session->set_flashdata('error', 'Gagal mengupload file media.');
        }

        redirect('adminheader');
    }

    /**
     * Edit Slide — versi AJAX (tanpa reload)
     * Menerima POST: id, label, overlay_title, overlay_description
     */
    public function edit_slide_ajax()
    {
        $id = $this->input->post('id');
        $slide = $this->Header_model->get_slide($id);
        if (!$slide) {
            echo json_encode(['status' => 'error', 'message' => 'Slide tidak ditemukan.']);
            return;
        }

        $label               = $this->input->post('label', true);
        $overlay_title       = $this->input->post('overlay_title', true);
        $overlay_description = trim(strip_tags($this->input->post('overlay_description')));

        // Parse existing media items to keep
        $existing_media_json = $this->input->post('existing_media');
        $kept_media = [];
        if (!empty($existing_media_json)) {
            $decoded_kept = json_decode($existing_media_json, true);
            if (is_array($decoded_kept)) {
                $kept_media = $decoded_kept;
            }
        }

        // Remove deleted files from disk
        $old_media_json = json_decode($slide->media_path, true);
        if (is_array($old_media_json)) {
            $kept_files = array_column($kept_media, 'file');
            foreach ($old_media_json as $item) {
                if (isset($item['file']) && !in_array($item['file'], $kept_files)) {
                    $path = ($item['type'] === 'video') ? './assets/vids/' : './assets/images/';
                    if (file_exists($path . $item['file'])) {
                        @unlink($path . $item['file']);
                    }
                }
            }
        } elseif (!empty($slide->media_path)) {
            $kept_files = array_column($kept_media, 'file');
            if (!in_array($slide->media_path, $kept_files)) {
                $path = ($slide->media_type === 'video') ? './assets/vids/' : './assets/images/';
                if (file_exists($path . $slide->media_path)) {
                    @unlink($path . $slide->media_path);
                }
            }
        }

        // Process newly uploaded files
        $uploaded_files = [];
        if (!empty($_FILES['edit_media_files']['name'][0])) {
            $durations = $this->input->post('edit_durations');
            $files_count = count($_FILES['edit_media_files']['name']);
            $this->load->library('upload');

            for ($i = 0; $i < $files_count; $i++) {
                $_FILES['file']['name']     = $_FILES['edit_media_files']['name'][$i];
                $_FILES['file']['type']     = $_FILES['edit_media_files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['edit_media_files']['tmp_name'][$i];
                $_FILES['file']['error']    = $_FILES['edit_media_files']['error'][$i];
                $_FILES['file']['size']     = $_FILES['edit_media_files']['size'][$i];
                
                $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                if (in_array($file_ext, ['mp4', 'webm', 'ogg'])) {
                    $type = 'video';
                    $config['upload_path']   = './assets/vids/';
                    $config['allowed_types'] = 'mp4|webm|ogg';
                    $config['max_size']      = 20000;
                } else {
                    $type = 'image';
                    $config['upload_path']   = './assets/images/';
                    $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                    $config['max_size']      = 5048;
                }
                $config['file_name'] = 'slide_' . time() . '_' . $i;

                $this->upload->initialize($config);
                
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $uploaded_files[] = [
                        'file' => $uploadData['file_name'],
                        'type' => $type,
                        'duration' => isset($durations[$i]) ? (int)$durations[$i] : 3
                    ];
                }
            }
        }

        $final_media = array_merge($kept_media, $uploaded_files);

        $update_data = [
            'label'               => $label,
            'overlay_title'       => $overlay_title,
            'overlay_description' => $overlay_description,
        ];

        if (!empty($final_media)) {
            $update_data['media_type'] = (count($final_media) > 1) ? 'multi' : $final_media[0]['type'];
            $update_data['media_path'] = json_encode($final_media);
        }

        $this->db->where('id', $id);
        if ($this->db->update('tb_panel', $update_data)) {
            echo json_encode(['status' => 'success', 'message' => 'Slide berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui slide.']);
        }
    }

    /**
     * Edit Slide — versi lama (fallback, tidak dipakai oleh AJAX)
     */
    public function edit_slide($id)
    {
        $slide = $this->Header_model->get_slide($id);
        if (!$slide) {
            $this->session->set_flashdata('error', 'Slide tidak ditemukan.');
            redirect('adminheader');
            return;
        }

        $label               = $this->input->post('label', true);
        $overlay_title       = $this->input->post('overlay_title', true);
        $overlay_description = $this->input->post('overlay_description');

        $update_data = [
            'label'               => $label,
            'overlay_title'       => $overlay_title,
            'overlay_description' => $overlay_description,
        ];

        // [CHANGED] header_slides -> tb_panel
        $this->db->where('id', $id);
        if ($this->db->update('tb_panel', $update_data)) {
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
            $decoded = json_decode($slide->media_path, true);
            if (is_array($decoded) && isset($decoded[0]['file'])) {
                foreach ($decoded as $item) {
                    $path = ($item['type'] == 'video') ? './assets/vids/' : './assets/images/';
                    $file_path = $path . $item['file'];
                    if (file_exists($file_path)) unlink($file_path);
                }
            } else {
                $path = ($slide->media_type == 'video') ? './assets/vids/' : './assets/images/';
                $file_path = $path . $slide->media_path;
                if (!in_array($slide->media_path, ['Fakultas.jpg', 'vidtelkom.mp4', 'background.png']) && file_exists($file_path)) {
                    unlink($file_path);
                }
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