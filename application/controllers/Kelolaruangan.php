<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelolaruangan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url', 'file'));
        $this->load->model('Booking_model');
        
        $is_ajax = $this->input->is_ajax_request() || 
                   $this->input->get_request_header('X-Requested-With') === 'XMLHttpRequest' ||
                   (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // Autentikasi Khusus Admin System (role_id = 1) atau Laboran (role_id = 2)
        if (!$this->session->userdata('logged_in')) {
            // Untuk mempermudah testing di development/local jika database belum ada user
            if (ENVIRONMENT !== 'production') {
                $this->session->set_userdata([
                    'user_id'   => '1',
                    'role_id'   => 1,
                    'name'      => 'Admin FIK',
                    'email'     => 'admin@telkomuniversity.ac.id',
                    'logged_in' => TRUE
                ]);
            } else {
                if ($is_ajax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    http_response_code(401);
                    echo json_encode([
                        'status'   => 'error', 
                        'message'  => 'Sesi login telah berakhir. Silakan login kembali.',
                        'redirect' => base_url('login')
                    ]);
                    exit;
                }
                redirect('login');
            }
        }
        
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id !== 1 && $role_id !== 2 && $role_id !== 21) {
            if ($is_ajax) {
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    'status'  => 'error', 
                    'message' => 'Hanya Admin System, Ka. Ur, dan Laboran yang memiliki hak akses untuk mengelola data ruangan.'
                ]);
                exit;
            }
            $this->session->set_flashdata('error', 'Hanya Admin System, Ka. Ur, dan Laboran yang dapat mengakses halaman Kelola Ruangan.');
            redirect('dashboard');
        }
    }

    public function index()
    {
        $data['title']    = 'Kelola Data Ruangan — Admin FIK';
        $data['kategori'] = $this->Booking_model->get_all_kategori();
        $data['ruangan']  = $this->Booking_model->get_all_ruangan_grouped();

        $this->load->view('admin/ruangan/index', $data);
    }


    private function _upload_file($field_name, $upload_path, $allowed_types)
    {
        if (empty($_FILES[$field_name]['name'])) {
            return null;
        }

        $full_dir = FCPATH . rtrim($upload_path, '/') . '/';
        if (!is_dir($full_dir)) {
            @mkdir($full_dir, 0777, true);
        }

        $config['upload_path']   = $full_dir;
        $config['allowed_types'] = '*'; // Allow all valid images & 3D binary files (.glb, .fbx, .gltf, .obj)
        $config['max_size']      = 102400; // Max 100MB for 3D models
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload');
        $this->upload->initialize($config, TRUE);

        if ($this->upload->do_upload($field_name)) {
            $data = $this->upload->data();
            return rtrim($upload_path, '/') . '/' . $data['file_name'];
        } else {
            $err = $this->upload->display_errors('', '');
            log_message('error', 'Upload Error (' . $field_name . '): ' . $err);
        }
        return null;
    }

    /**
     * Memeriksa apakah ada ruangan fisik yang sudah digunakan oleh fasilitas lain.
     * 1 nomor ruangan fisik hanya boleh digunakan oleh 1 fasilitas.
     * 
     * @param array $rooms_to_check Daftar kode ruangan fisik (misal: ['LK 01', 'LK 02'])
     * @param int|string|null $exclude_id ID fasilitas yang sedang diedit (diabaikan jika ada)
     * @param string|null $exclude_name Nama fasilitas yang sedang diedit
     * @return array|null Info konflik ['code' => ..., 'occupied_by' => ...] atau null jika aman
     */
    private function _check_room_conflicts($rooms_to_check, $exclude_id = null, $exclude_name = null)
    {
        $existing = $this->Booking_model->get_all_ruangan();
        if (!empty($exclude_id) || !empty($exclude_name)) {
            $existing = array_filter($existing, function($row) use ($exclude_id, $exclude_name) {
                if (!empty($exclude_id) && ((string)$row->id === (string)$exclude_id || (string)($row->kode_ruangan ?? '') === (string)$exclude_id)) {
                    return false;
                }
                if (!empty($exclude_name) && strcasecmp(trim((string)$row->nama_ruangan), trim((string)$exclude_name)) === 0) {
                    return false;
                }
                return true;
            });
        }

        $canonicalize = function($str) {
            return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string)$str));
        };

        foreach ($rooms_to_check as $target) {
            $target_clean = strtoupper(trim($target));
            $target_canon = $canonicalize($target);
            if (empty($target_clean)) continue;

            foreach ($existing as $row) {
                $rowCodes = array_filter(array_map('trim', explode(',', (string)($row->kode_ruangan ?: $row->id))));
                foreach ($rowCodes as $r) {
                    $r_clean = strtoupper(trim($r));
                    $r_canon = $canonicalize($r);

                    if ($target_clean === $r_clean || (!empty($target_canon) && $target_canon === $r_canon)) {
                        return [
                            'code'        => $target_clean,
                            'occupied_by' => $row->nama_ruangan
                        ];
                    }
                }
            }
        }

        return null;
    }

    public function tambah()
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        $nama_ruangan    = $this->input->post('nama_ruangan', true);
        $kode_ruangan    = $this->input->post('kode_ruangan', true);
        $id_kategori     = $this->input->post('id_kategori', true);
        $kapasitas       = $this->input->post('kapasitas', true);
        $lokasi          = $this->input->post('lokasi', true);
        $status          = $this->input->post('status', true);
        $tagline         = $this->input->post('tagline', true);
        $jumlah_unit     = $this->input->post('jumlah_unit', true);
        $jam_operasional = $this->input->post('jam_operasional', true);
        $deskripsi       = $this->input->post('deskripsi', true);
        $spesifikasi_fasilitas = $this->input->post('spesifikasi_fasilitas', true);
        $tata_tertib     = $this->input->post('tata_tertib', true);

        // Format ruangan fisik (bisa satu atau lebih kode dipisah koma)
        $rooms = array_values(array_filter(array_map('trim', explode(',', (string)$kode_ruangan))));
        if (empty($rooms) && !empty($kode_ruangan)) {
            $rooms = [strtoupper(trim((string)$kode_ruangan))];
        }

        if (empty($nama_ruangan) || empty($rooms) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan/Lab, Ruangan Fisik (Nomor LK), dan Kategori!']);
            return;
        }

        // Cek duplikasi ruangan fisik (1 nomor ruangan fisik hanya untuk 1 baris/fasilitas)
        $conflict = $this->_check_room_conflicts($rooms);
        if ($conflict) {
            echo json_encode([
                'status'  => 'error',
                'message' => "Ruangan fisik '{$conflict['code']}' sudah digunakan oleh fasilitas '{$conflict['occupied_by']}'! Satu nomor ruangan fisik hanya dapat digunakan oleh 1 fasilitas."
            ]);
            return;
        }

        // Upload foto & 3D model jika diupload
        $foto_path     = $this->_upload_file('foto', 'uploads/ruangan/foto/', 'jpg|jpeg|png|webp|gif');
        $model_3d_path = $this->_upload_file('model_3d', 'uploads/ruangan/models/', 'glb|fbx|gltf|obj|bin');

        $fields = $this->db->list_fields('ruangan');
        $base_data = array();
        if (in_array('ruangan', $fields)) $base_data['ruangan'] = $nama_ruangan;
        if (in_array('nama_ruangan', $fields)) $base_data['nama_ruangan'] = $nama_ruangan;
        if (in_array('id_kategori', $fields)) $base_data['id_kategori'] = $id_kategori;
        if (in_array('kapasitas', $fields)) $base_data['kapasitas'] = $kapasitas ? $kapasitas : 30;
        if (in_array('lokasi', $fields)) $base_data['lokasi'] = $lokasi ? $lokasi : 'Gedung Sebatik (FIK)';
        if (in_array('status', $fields)) $base_data['status'] = $status ? $status : 'Tersedia';
        if (in_array('akses', $fields)) $base_data['akses'] = $status ? $status : 'Tersedia';
        if (in_array('tagline', $fields)) $base_data['tagline'] = $tagline;
        if (in_array('jumlah_unit', $fields)) $base_data['jumlah_unit'] = $jumlah_unit;
        if (in_array('jam_operasional', $fields)) $base_data['jam_operasional'] = $jam_operasional;
        if (in_array('deskripsi', $fields)) $base_data['deskripsi'] = $deskripsi;
        if (in_array('spesifikasi_fasilitas', $fields)) $base_data['spesifikasi_fasilitas'] = $spesifikasi_fasilitas;
        if (in_array('tata_tertib', $fields)) $base_data['tata_tertib'] = $tata_tertib;
        if (in_array('date', $fields)) $base_data['date'] = date('Y-m-d H:i:s');

        // Handle gambar/images (support kolom foto & model_3d atau kolom images kombinasi)
        if (in_array('images', $fields)) {
            $combined = '';
            if ($foto_path && $model_3d_path) $combined = $foto_path . '|' . $model_3d_path;
            elseif ($foto_path) $combined = $foto_path;
            elseif ($model_3d_path) $combined = '|' . $model_3d_path;
            if ($combined) $base_data['images'] = $combined;
        } else {
            if ($foto_path && in_array('foto', $fields)) $base_data['foto'] = $foto_path;
            if ($model_3d_path && in_array('model_3d', $fields)) $base_data['model_3d'] = $model_3d_path;
        }

        // Insert masing-masing kode ruangan fisik sebagai 1 baris tersendiri
        $inserted = 0;
        foreach ($rooms as $single_code) {
            $clean_code = strtoupper(trim($single_code));
            if (empty($clean_code)) continue;

            $row_data = $base_data;
            if (in_array('id', $fields)) $row_data['id'] = $clean_code;
            if (in_array('kode_ruangan', $fields)) $row_data['kode_ruangan'] = $clean_code;

            if ($this->db->insert('ruangan', $row_data)) {
                $inserted++;
            }
        }

        if ($inserted > 0) {
            $msg = ($inserted > 1) 
                ? "{$inserted} ruangan fisik berhasil ditambahkan (1 baris per kode ruangan)!" 
                : "Ruangan baru berhasil ditambahkan!";
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan ruangan baru.']);
        }
    }

    public function update($id = null)
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if (empty($id)) {
            $id = $this->input->post('id', true);
        }

        $nama_ruangan    = $this->input->post('nama_ruangan', true);
        $kode_ruangan    = $this->input->post('kode_ruangan', true);
        $id_kategori     = $this->input->post('id_kategori', true);
        $kapasitas       = $this->input->post('kapasitas', true);
        $lokasi          = $this->input->post('lokasi', true);
        $status          = $this->input->post('status', true);
        $tagline         = $this->input->post('tagline', true);
        $jumlah_unit     = $this->input->post('jumlah_unit', true);
        $jam_operasional = $this->input->post('jam_operasional', true);
        $deskripsi       = $this->input->post('deskripsi', true);
        $spesifikasi_fasilitas = $this->input->post('spesifikasi_fasilitas', true);
        $tata_tertib     = $this->input->post('tata_tertib', true);

        // Format ruangan fisik (bisa satu atau lebih, dipisah koma)
        $rooms = array_values(array_filter(array_map('trim', explode(',', (string)$kode_ruangan))));
        if (empty($rooms) && !empty($kode_ruangan)) {
            $rooms = [strtoupper(trim((string)$kode_ruangan))];
        }

        if (empty($nama_ruangan) || empty($rooms) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan/Lab, Ruangan Fisik (Nomor LK), dan Kategori!']);
            return;
        }

        // Ambil data lama fasilitas yang sedang diedit
        $old_room = $this->db->get_where('ruangan', ['id' => $id])->row();
        $old_name = $old_room ? $old_room->ruangan : '';

        // Cek duplikasi ruangan fisik dengan mengecualikan fasilitas yang sedang diedit
        $conflict = $this->_check_room_conflicts($rooms, $id, $old_name);
        if ($conflict) {
            echo json_encode([
                'status'  => 'error',
                'message' => "Ruangan fisik '{$conflict['code']}' sudah digunakan oleh fasilitas '{$conflict['occupied_by']}'! Satu nomor ruangan fisik hanya dapat digunakan oleh 1 fasilitas."
            ]);
            return;
        }

        // Upload foto & 3D model jika diubah
        $foto_path     = $this->_upload_file('foto', 'uploads/ruangan/foto/', 'jpg|jpeg|png|webp|gif');
        $model_3d_path = $this->_upload_file('model_3d', 'uploads/ruangan/models/', 'glb|fbx|gltf|obj|bin');

        $fields = $this->db->list_fields('ruangan');
        $base_data = array();
        if (in_array('ruangan', $fields)) $base_data['ruangan'] = $nama_ruangan;
        if (in_array('nama_ruangan', $fields)) $base_data['nama_ruangan'] = $nama_ruangan;
        if (in_array('id_kategori', $fields)) $base_data['id_kategori'] = $id_kategori;
        if (in_array('kapasitas', $fields)) $base_data['kapasitas'] = $kapasitas ? $kapasitas : 30;
        if (in_array('lokasi', $fields)) $base_data['lokasi'] = $lokasi ? $lokasi : 'Gedung Sebatik (FIK)';
        if (in_array('status', $fields)) $base_data['status'] = $status ? $status : 'Tersedia';
        if (in_array('akses', $fields)) $base_data['akses'] = $status ? $status : 'Tersedia';
        if (in_array('tagline', $fields)) $base_data['tagline'] = $tagline;
        if (in_array('jumlah_unit', $fields)) $base_data['jumlah_unit'] = $jumlah_unit;
        if (in_array('jam_operasional', $fields)) $base_data['jam_operasional'] = $jam_operasional;
        if (in_array('deskripsi', $fields)) $base_data['deskripsi'] = $deskripsi;
        if (in_array('spesifikasi_fasilitas', $fields)) $base_data['spesifikasi_fasilitas'] = $spesifikasi_fasilitas;
        if (in_array('tata_tertib', $fields)) $base_data['tata_tertib'] = $tata_tertib;
        if (in_array('date', $fields)) $base_data['date'] = date('Y-m-d H:i:s');

        // Handle gambar/images
        if (in_array('images', $fields)) {
            $old_images = $old_room ? (string)$old_room->images : '';
            $old_foto = ''; $old_model = '';
            if (strpos($old_images, '|') !== false) {
                list($old_foto, $old_model) = explode('|', $old_images, 2);
            } else {
                $ext = strtolower(pathinfo($old_images, PATHINFO_EXTENSION));
                if (in_array($ext, ['glb', 'gltf', 'fbx', 'obj'])) { $old_model = $old_images; }
                else { $old_foto = $old_images; }
            }
            $final_foto = $foto_path ? $foto_path : $old_foto;
            $final_model = $model_3d_path ? $model_3d_path : $old_model;
            $combined = '';
            if ($final_foto && $final_model) $combined = $final_foto . '|' . $final_model;
            elseif ($final_foto) $combined = $final_foto;
            elseif ($final_model) $combined = '|' . $final_model;
            if ($combined) $base_data['images'] = $combined;
        } else {
            if ($foto_path && in_array('foto', $fields)) $base_data['foto'] = $foto_path;
            elseif ($old_room && !empty($old_room->foto) && in_array('foto', $fields)) $base_data['foto'] = $old_room->foto;
            
            if ($model_3d_path && in_array('model_3d', $fields)) $base_data['model_3d'] = $model_3d_path;
            elseif ($old_room && !empty($old_room->model_3d) && in_array('model_3d', $fields)) $base_data['model_3d'] = $old_room->model_3d;
        }

        // Hapus baris lama fasilitas ini sebelum memasukkan baris baru per kode ruangan fisik
        if (!empty($old_name)) {
            $this->db->where('ruangan', $old_name);
            $this->db->delete('ruangan');
        }
        $this->db->where('id', $id);
        $this->db->delete('ruangan');

        // Insert row baru per kode ruangan fisik
        $inserted = 0;
        foreach ($rooms as $single_code) {
            $clean_code = strtoupper(trim($single_code));
            if (empty($clean_code)) continue;

            $row_data = $base_data;
            if (in_array('id', $fields)) $row_data['id'] = $clean_code;
            if (in_array('kode_ruangan', $fields)) $row_data['kode_ruangan'] = $clean_code;

            if ($this->db->insert('ruangan', $row_data)) {
                $inserted++;
            }
        }

        if ($inserted > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Data ruangan & berkas berhasil diperbarui!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data ruangan.']);
        }
    }

    public function delete($id = null)
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if (empty($id)) {
            $id = $this->input->post('id', true);
        }

        $room = $this->db->get_where('ruangan', ['id' => $id])->row();
        if ($room && !empty($room->ruangan)) {
            $this->db->where('ruangan', $room->ruangan);
            $delete = $this->db->delete('ruangan');
        } else {
            $this->db->where('id', $id);
            $delete = $this->db->delete('ruangan');
        }

        if ($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Ruangan berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus ruangan.']);
        }
    }
}
