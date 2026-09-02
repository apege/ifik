<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get Data Mahasiswa berdasarkan NIM atau User ID
    public function get_mahasiswa($nim) {
        $session_name = $this->session->userdata('name');
        $session_nim  = $this->session->userdata('nim') ?: $this->session->userdata('nidn_nim');
        
        // Ambil data dari tabel users jika tersedia
        $user_row = null;
        if (!empty($nim)) {
            $user_row = $this->db->get_where('users', array('nidn_nim' => $nim))->row_array();
        }
        if (!$user_row && $this->session->userdata('user_id')) {
            $user_row = $this->db->get_where('users', array('id' => $this->session->userdata('user_id')))->row_array();
        }

        $full_name = !empty($user_row['name']) ? $user_row['name'] : ($session_name ?: 'Mahasiswa');
        $name_parts = explode(' ', trim($full_name), 2);
        $nama_depan_default = $name_parts[0] ?? 'Mahasiswa';
        $nama_belakang_default = $name_parts[1] ?? '';

        $data_mhs = null;
        if ($this->db->table_exists('mahasiswa') && !empty($nim)) {
            $query = $this->db->get_where('mahasiswa', array('nim' => $nim));
            $data_mhs = $query->row_array();
        }

        if ($data_mhs) {
            // Jika nama_depan di tabel mahasiswa kosong atau ingin diselaraskan dengan akun login
            if (empty($data_mhs['nama_depan']) || (!empty($user_row['name']) && $user_row['name'] !== 'Rivan Arshavin')) {
                $data_mhs['nama_depan'] = $nama_depan_default;
                $data_mhs['nama_belakang'] = $nama_belakang_default;
            }
            if (empty($data_mhs['nim'])) {
                $data_mhs['nim'] = $nim ?: $session_nim;
            }
            return $data_mhs;
        }

        return array(
            'nim' => $nim ?: ($session_nim ?: '1301210001'),
            'nama_depan' => $nama_depan_default,
            'nama_belakang' => $nama_belakang_default,
            'alamat' => 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung',
            'kota' => 'Bandung',
            'provinsi' => 'Jawa Barat',
            'latitude' => '-6.973000',
            'longitude' => '107.630000',
            'konsentrasi_dkv' => 'Desain Komunikasi Visual',
            'prodi' => 'Desain Komunikasi Visual'
        );
    }

    // Simpan atau update geodata mahasiswa
    public function update_geodata($nim, $data_geodata) {
        if (!$this->db->table_exists('mahasiswa')) return true;
        $this->db->where('nim', $nim);
        return $this->db->update('mahasiswa', $data_geodata);
    }

    // Simpan Pendaftaran TA 6-Step
    public function save_pendaftaran_ta($data_ta) {
        if (!$this->db->table_exists('pendaftaran_ta')) return true;
        $existing = $this->db->get_where('pendaftaran_ta', array('nim' => $data_ta['nim']))->row_array();
        if ($existing) {
            $this->db->where('nim', $data_ta['nim']);
            return $this->db->update('pendaftaran_ta', $data_ta);
        } else {
            return $this->db->insert('pendaftaran_ta', $data_ta);
        }
    }

    // Get Status Pendaftaran & Approval Chain
    public function get_status_pendaftaran($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array(
                'status_approval_wali' => 'Pending',
                'status_approval_admin' => 'Pending',
                'status_approval_koor' => 'Pending',
                'status_approval_kk' => 'Pending',
                'current_stage' => 'Dosen Wali'
            );
        }
        $has_dw = $this->db->table_exists('dosen_wali') && $this->db->field_exists('id_dosen_wali', 'pendaftaran_ta');

        $this->db->select('p.*' . ($has_dw ? ', w.nama_dosen as nama_dosen_wali' : ''));
        $this->db->from('pendaftaran_ta p');
        if ($has_dw) {
            $this->db->join('dosen_wali w', 'w.id = p.id_dosen_wali', 'left');
        }
        $this->db->where('p.nim', $nim);
        $query = $this->db->get();
        return $query->row_array() ?: array(
            'status_approval_wali' => 'Pending',
            'status_approval_admin' => 'Pending',
            'status_approval_koor' => 'Pending',
            'status_approval_kk' => 'Pending'
        );
    }

    // Update Ganti Password Mahasiswa
    public function update_password($nim, $hashed_password) {
        $this->db->where('nim', $nim);
        return $this->db->update('users', array('password' => $hashed_password));
    }

    // Reset atau Hapus Pendaftaran TA
    public function reset_pendaftaran_ta($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) return true;

        // Bersihkan berkas fisik yang pernah diunggah jika ada
        $existing = $this->db->get_where('pendaftaran_ta', ['nim' => $nim])->row_array();
        if ($existing) {
            $files_to_delete = ['file_ksm', 'file_transkrip', 'file_pernyataan', 'file_bebas_lab'];
            $upload_path = FCPATH . 'uploads/persyaratan_ta/';
            foreach ($files_to_delete as $field) {
                if (!empty($existing[$field])) {
                    $filepath = $upload_path . $existing[$field];
                    if (file_exists($filepath) && is_file($filepath)) {
                        @unlink($filepath);
                    }
                }
            }
        }

        $this->db->where('nim', $nim);
        return $this->db->delete('pendaftaran_ta');
    }

    // Ambil riwayat upload berkas preview TA
    public function get_riwayat_preview($nim, $tahap = 'Preview 1') {
        if (!$this->db->table_exists('bimbingan_preview')) return [];
        $this->db->where('nim', $nim);
        if ($tahap) {
            $this->db->where('tahap_preview', $tahap);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('bimbingan_preview')->result_array();
    }

    // Simpan upload draft berkas preview baru
    public function save_upload_preview($data) {
        if (!$this->db->table_exists('bimbingan_preview')) return false;
        return $this->db->insert('bimbingan_preview', $data);
    }

    // Hitung total upload pada tahap tertentu (untuk validasi minimal 1x upload)
    public function count_upload_preview($nim, $tahap = 'Preview 1') {
        if (!$this->db->table_exists('bimbingan_preview')) return 0;
        $this->db->where('nim', $nim);
        $this->db->where('tahap_preview', $tahap);
        return $this->db->count_all_results('bimbingan_preview');
    }

    // Cek status terbaru kelayakan Preview 1
    public function get_latest_preview_status($nim, $tahap = 'Preview 1') {
        if (!$this->db->table_exists('bimbingan_preview')) return null;
        $this->db->where('nim', $nim);
        $this->db->where('tahap_preview', $tahap);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('bimbingan_preview')->row_array();
    }

    // Mendapatkan nama dosen pembimbing dan penguji asli
    public function get_pembimbing_penguji($nim) {
        $result = array(
            'pembimbing_1' => '',
            'pembimbing_2' => '',
            'penguji_1' => '',
            'penguji_2' => ''
        );

        if ($this->db->table_exists('pendaftaran_ta')) {
            $this->db->select('pembimbing_1, pembimbing_2, penguji_1, penguji_2');
            $this->db->where('nim', $nim);
            $pt = $this->db->get('pendaftaran_ta')->row_array();

            if ($pt) {
                $result['pembimbing_1'] = $this->_get_dosen_name($pt['pembimbing_1']);
                $result['pembimbing_2'] = $this->_get_dosen_name($pt['pembimbing_2']);
                $result['penguji_1'] = $this->_get_dosen_name($pt['penguji_1']);
                $result['penguji_2'] = $this->_get_dosen_name($pt['penguji_2']);
            }
        }
        
        return $result;
    }

    private function _get_dosen_name($nip) {
        if (empty($nip)) return '';
        if ($this->db->table_exists('users')) {
            $u = $this->db->get_where('users', ['nidn_nim' => $nip])->row_array();
            if ($u && !empty($u['name'])) return $u['name'];
        }
        if ($this->db->table_exists('dosen_wali')) {
            $dw = $this->db->get_where('dosen_wali', ['nip' => $nip])->row_array();
            if ($dw && !empty($dw['nama_dosen'])) return $dw['nama_dosen'];
        }
        return '';
    }

    // Mengambil daftar mahasiswa bimbingan bagi seorang Dosen
    public function get_students_by_dosen($dosen_id, $posisi = 1) {
        if (!$this->db->table_exists('pendaftaran_ta')) return [];
        
        $nip_dosen = '';
        if ($this->db->table_exists('users')) {
            $u = $this->db->get_where('users', ['id' => $dosen_id])->row_array();
            if ($u) {
                $nip_dosen = $u['nidn_nim'];
            }
        }

        $has_konsentrasi = $this->db->field_exists('konsentrasi_dkv', 'pendaftaran_ta');
        $select = 'pt.nim, pt.judul_1 as judul, COALESCE(u.name, pt.nim) as nama_mahasiswa';
        if ($has_konsentrasi) {
            $select .= ', pt.konsentrasi_dkv';
        }
        $this->db->select($select);
        $this->db->from('pendaftaran_ta pt');
        $this->db->join('users u', 'u.nidn_nim = pt.nim', 'left');
        
        if ($posisi == 1) {
            $this->db->where('pt.pembimbing_1', $nip_dosen);
        } else {
            $this->db->where('pt.pembimbing_2', $nip_dosen);
        }
        return $this->db->get()->result_array();
    }

    // Update status preview dan catatan dosen
    public function update_review_preview($id, $data) {
        if (!$this->db->table_exists('bimbingan_preview')) return false;
        $this->db->where('id', $id);
        return $this->db->update('bimbingan_preview', $data);
    }
}
