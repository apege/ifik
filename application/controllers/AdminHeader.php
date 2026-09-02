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
        $this->load->model('Booking_model');
        $data['settings'] = $this->Header_model->get_settings();
        $data['slides']   = $this->Header_model->get_slides();
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        
        // Fetch data ruangan dengan nama kategori
        $this->db->select('ruangan.*, kategori_ruangan.nama_kategori');
        $this->db->from('ruangan');
        $this->db->join('kategori_ruangan', 'kategori_ruangan.id = ruangan.id_kategori', 'left');
        $this->db->order_by('ruangan.id', 'ASC');
        $data['ruangan'] = $this->db->get()->result();

        $data['active_tab'] = $this->input->get('tab', true) === 'fasilitas' ? 'fasilitas' : 'header';

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
        
        if (empty($_FILES['media_files']['name'][0])) {
             $this->session->set_flashdata('error', 'Harap pilih file untuk diupload.');
             redirect('adminheader');
             return;
        }

        $durations = $this->input->post('durations');
        $overlay_title = $this->input->post('overlay_title', true);
        $overlay_description = $this->input->post('overlay_description');

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
        $overlay_description = $this->input->post('overlay_description');

        $update_data = [
            'label'               => $label,
            'overlay_title'       => $overlay_title,
            'overlay_description' => $overlay_description,
        ];

        $this->db->where('id', $id);
        if ($this->db->update('header_slides', $update_data)) {
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