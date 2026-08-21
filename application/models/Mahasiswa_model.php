<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get Data Mahasiswa berdasarkan NIM atau User ID
    public function get_mahasiswa($nim) {
        if (!$this->db->table_exists('mahasiswa')) {
            return array(
                'nim' => $nim,
                'nama_depan' => 'Rivan',
                'nama_belakang' => 'Arshavin',
                'alamat' => 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung',
                'kota' => 'Bandung',
                'provinsi' => 'Jawa Barat',
                'latitude' => '-6.973000',
                'longitude' => '107.630000',
                'konsentrasi_dkv' => 'Desain Grafis'
            );
        }
        $query = $this->db->get_where('mahasiswa', array('nim' => $nim));
        return $query->row_array() ?: array(
            'nim' => $nim,
            'nama_depan' => 'Rivan',
            'nama_belakang' => 'Arshavin',
            'alamat' => 'Jl. Telekomunikasi No. 1, Terusan Buah Batu, Bandung',
            'kota' => 'Bandung',
            'provinsi' => 'Jawa Barat'
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
        $this->db->select('p.*, w.nama_dosen as nama_dosen_wali');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('dosen_wali w', 'w.id = p.id_dosen_wali', 'left');
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
        $this->db->where('nim', $nim);
        return $this->db->delete('pendaftaran_ta');
    }
}
