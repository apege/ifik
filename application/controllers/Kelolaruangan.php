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

        // Autentikasi Khusus Admin System (role_id = 1)
        if (!$this->session->userdata('logged_in')) {
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
        
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id !== 1 && $role_id !== 2) {
            if ($is_ajax) {
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    'status'  => 'error', 
                    'message' => 'Hanya Admin System dan Laboran yang memiliki hak akses untuk mengelola data ruangan.'
                ]);
                exit;
            }
            $this->session->set_flashdata('error', 'Hanya Admin System dan Laboran yang dapat mengakses halaman Kelola Ruangan.');
            redirect('dashboard');
        }
    }

    public function index()
    {
        redirect('adminheader?tab=fasilitas');
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
     * @param int|null $exclude_id ID fasilitas yang sedang diedit (diabaikan jika ada)
     * @return array|null Info konflik ['code' => ..., 'occupied_by' => ...] atau null jika aman
     */
    private function _check_room_conflicts($rooms_to_check, $exclude_id = null)
    {
        $this->db->select('id, nama_ruangan, kode_ruangan');
        if (!empty($exclude_id)) {
            $this->db->where('id !=', $exclude_id);
        }
        $existing = $this->db->get('ruangan')->result();

        $canonicalize = function($str) {
            return strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string)$str));
        };

        foreach ($rooms_to_check as $target) {
            $target_clean = strtoupper(trim($target));
            $target_canon = $canonicalize($target);
            if (empty($target_clean)) continue;

            foreach ($existing as $row) {
                if (empty($row->kode_ruangan)) continue;
                $row_rooms = array_filter(array_map('trim', explode(',', $row->kode_ruangan)));
                foreach ($row_rooms as $r) {
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

        // Format ruangan fisik (bisa satu atau lebih, dipisah koma)
        $rooms = array_filter(array_map('trim', explode(',', (string)$kode_ruangan)));
        $clean_kode_ruangan = !empty($rooms) ? implode(', ', array_map('strtoupper', $rooms)) : strtoupper(trim((string)$kode_ruangan));

        if (empty($nama_ruangan) || empty($clean_kode_ruangan) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan/Lab, Ruangan Fisik (Nomor LK), dan Kategori!']);
            return;
        }

        // Cek duplikasi ruangan fisik (1 ruangan fisik = 1 fasilitas)
        $conflict = $this->_check_room_conflicts(!empty($rooms) ? $rooms : [$clean_kode_ruangan]);
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

        $data_ruangan = array(
            'nama_ruangan'          => $nama_ruangan,
            'kode_ruangan'          => $clean_kode_ruangan,
            'id_kategori'           => $id_kategori,
            'kapasitas'             => $kapasitas ? $kapasitas : 30,
            'lokasi'                => $lokasi ? $lokasi : 'Gedung Sebatik (FIK)',
            'status'                => $status ? $status : 'Tersedia',
            'tagline'               => $tagline,
            'jumlah_unit'          => $jumlah_unit,
            'jam_operasional'       => $jam_operasional,
            'deskripsi'             => $deskripsi,
            'spesifikasi_fasilitas' => $spesifikasi_fasilitas,
            'tata_tertib'           => $tata_tertib
        );

        if ($foto_path) {
            $data_ruangan['foto'] = $foto_path;
        }
        if ($model_3d_path) {
            $data_ruangan['model_3d'] = $model_3d_path;
        }

        $insert = $this->db->insert('ruangan', $data_ruangan);

        if ($insert) {
            echo json_encode(['status' => 'success', 'message' => 'Ruangan baru & berkas berhasil ditambahkan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan ruangan baru.']);
        }
    }

    public function update($id)
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

        // Format ruangan fisik (bisa satu atau lebih, dipisah koma)
        $rooms = array_filter(array_map('trim', explode(',', (string)$kode_ruangan)));
        $clean_kode_ruangan = !empty($rooms) ? implode(', ', array_map('strtoupper', $rooms)) : strtoupper(trim((string)$kode_ruangan));

        if (empty($nama_ruangan) || empty($clean_kode_ruangan) || empty($id_kategori)) {
            echo json_encode(['status' => 'error', 'message' => 'Harap isi Nama Ruangan/Lab, Ruangan Fisik (Nomor LK), dan Kategori!']);
            return;
        }

        // Cek duplikasi ruangan fisik dengan mengecualikan ID yang sedang diedit
        $conflict = $this->_check_room_conflicts(!empty($rooms) ? $rooms : [$clean_kode_ruangan], $id);
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

        $data_ruangan = array(
            'nama_ruangan'          => $nama_ruangan,
            'kode_ruangan'          => $clean_kode_ruangan,
            'id_kategori'           => $id_kategori,
            'kapasitas'             => $kapasitas,
            'lokasi'                => $lokasi,
            'status'                => $status,
            'tagline'               => $tagline,
            'jumlah_unit'          => $jumlah_unit,
            'jam_operasional'       => $jam_operasional,
            'deskripsi'             => $deskripsi,
            'spesifikasi_fasilitas' => $spesifikasi_fasilitas,
            'tata_tertib'           => $tata_tertib
        );

        if ($foto_path) {
            $data_ruangan['foto'] = $foto_path;
        }
        if ($model_3d_path) {
            $data_ruangan['model_3d'] = $model_3d_path;
        }

        $this->db->where('id', $id);
        $update = $this->db->update('ruangan', $data_ruangan);

        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Data ruangan & berkas berhasil diperbarui!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data ruangan.']);
        }
    }

    public function delete($id)
    {
        header('Content-Type: application/json');

        $this->db->where('id', $id);
        $delete = $this->db->delete('ruangan');

        if ($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Ruangan berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus ruangan.']);
        }
    }
}
