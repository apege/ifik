<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->_ensure_columns_exist();
    }

    private function _ensure_columns_exist() {
        if (!$this->db->table_exists('pendaftaran_ta')) return;
        $fields = $this->db->list_fields('pendaftaran_ta');
        $new_cols = array(
            'pembimbing_1'         => "VARCHAR(50) DEFAULT NULL",
            'pembimbing_2'         => "VARCHAR(50) DEFAULT NULL",
            'penguji_1'            => "VARCHAR(50) DEFAULT NULL",
            'penguji_2'            => "VARCHAR(50) DEFAULT NULL",
            'tgl_sidang'           => "DATE DEFAULT NULL",
            'jam_mulai_sidang'     => "TIME DEFAULT NULL",
            'jam_selesai_sidang'   => "TIME DEFAULT NULL",
            'ruangan_sidang'       => "VARCHAR(100) DEFAULT NULL",
            'status_approval_koor' => "VARCHAR(50) DEFAULT 'Pending'",
            'catatan_koor'         => "TEXT DEFAULT NULL",
            'current_stage'        => "VARCHAR(100) DEFAULT 'Koordinator TA'",
        );
        foreach ($new_cols as $col => $type) {
            if (!in_array($col, $fields)) {
                $this->db->query("ALTER TABLE `pendaftaran_ta` ADD COLUMN `{$col}` {$type}");
            }
        }
    }

    // Ambil list semua dosen pembimbing dari database (role: dosen)
    public function get_dosen_list() {
        $list = array();

        // 1. Ambil dari tabel users yang memiliki role 'dosen'
        if ($this->db->table_exists('users') && $this->db->table_exists('roles')) {
            $this->db->select('u.id, u.name as nama_dosen, u.nidn_nim as nip, u.email, u.no_hp');
            $this->db->from('users u');
            $this->db->join('roles r', 'r.id = u.role_id');
            $this->db->where('r.name', 'dosen');
            $this->db->where('u.status', 'active');
            $this->db->order_by('u.name', 'ASC');
            $usersDosen = $this->db->get()->result_array();
            
            foreach ($usersDosen as $ud) {
                if (!empty($ud['nip'])) {
                    $list[$ud['nip']] = array(
                        'nip' => (string)$ud['nip'],
                        'nama_dosen' => $ud['nama_dosen'],
                        'email' => $ud['email'] ?? '',
                        'prodi' => 'Informatika'
                    );
                }
            }
        }

        // 2. Lengkapi juga dari tabel dosen_wali
        if ($this->db->table_exists('dosen_wali')) {
            $dwList = $this->db->get('dosen_wali')->result_array();
            foreach ($dwList as $dw) {
                if (!empty($dw['nip']) && !isset($list[$dw['nip']])) {
                    $list[$dw['nip']] = array(
                        'nip' => (string)$dw['nip'],
                        'nama_dosen' => $dw['nama_dosen'],
                        'email' => $dw['email'] ?? '',
                        'prodi' => $dw['jurusan'] ?? 'Informatika'
                    );
                }
            }
        }

        return array_values($list);
    }

    // Get List All Mahasiswa Mendaftar TA untuk Koordinator TA (Real Database Records)
    public function get_all_mahasiswa_ta() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $this->db->select('m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.alamat, m.kota, m.provinsi, m.email, m.no_hp, p.*, dw1.nama_dosen as nama_pembimbing_1, dw2.nama_dosen as nama_pembimbing_2');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->join('dosen_wali dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('dosen_wali dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();

        // Normalisasi data
        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['konsentrasi_dkv'])) {
                $row['konsentrasi_dkv'] = 'Informatika';
            }
            if (empty($row['status_approval_koor'])) {
                $row['status_approval_koor'] = 'Pending';
            }
            if (empty($row['current_stage'])) {
                $row['current_stage'] = 'Koordinator TA';
            }
        }

        return $result;
    }

    // Get Detail Mahasiswa dan Pendaftaran TA untuk Koordinator TA
    public function get_detail_pendaftaran_mahasiswa($nim) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return null;
        }

        $this->db->select('m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.alamat as mhs_alamat, m.kota, m.provinsi, m.email, m.no_hp, p.*, dw1.nama_dosen as nama_pembimbing_1, dw2.nama_dosen as nama_pembimbing_2');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->join('dosen_wali dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('dosen_wali dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->where('p.nim', $nim);
        $query = $this->db->get();
        $row = $query->row_array();

        if (!$row && $this->db->table_exists('mahasiswa')) {
            $this->db->where('nim', $nim);
            $row = $this->db->get('mahasiswa')->row_array();
        }

        if ($row) {
            if (empty($row['alamat']) && !empty($row['mhs_alamat'])) {
                $row['alamat'] = $row['mhs_alamat'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['status_approval_koor'])) {
                $row['status_approval_koor'] = 'Pending';
            }
            if (empty($row['current_stage'])) {
                $row['current_stage'] = 'Koordinator TA';
            }
        }

        return $row;
    }

    // Approval / Reject Pendaftaran TA oleh Koordinator TA dengan AJAX & Validasi Pembimbing
    public function update_approval_koor_ajax($nim, $status, $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        // Cek data pendaftaran mahasiswa
        $this->db->where('nim', $nim);
        $curr = $this->db->get('pendaftaran_ta')->row_array();
        if (!$curr) {
            return array('status' => false, 'message' => 'Data pendaftaran mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
        }

        // Validasi: Harus sudah disetujui Dosen Wali & Admin LAA sebelum Koordinator TA bisa approve
        if ($status === 'Approved') {
            $statusWali = $curr['status_approval_wali'] ?? 'Pending';
            if ($statusWali !== 'Approved') {
                return array(
                    'status' => false, 
                    'message' => 'Persetujuan ditolak! Berkas mahasiswa harus terlebih dahulu disetujui oleh Dosen Wali.'
                );
            }

            $statusAdmin = $curr['status_approval_admin'] ?? 'Pending';
            if ($statusAdmin !== 'Approved') {
                return array(
                    'status' => false, 
                    'message' => 'Persetujuan ditolak! Berkas mahasiswa harus terlebih dahulu disetujui oleh Admin Layanan Akademik (LAA).'
                );
            }

            // Validasi Dosen Pembimbing 1 & 2 wajib dipilih
            if (empty($pembimbing_1) || empty($pembimbing_2)) {
                return array(
                    'status' => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 wajib dipilih sebelum menyetujui pendaftaran TA!'
                );
            }

            // Validasi: Pembimbing 1 dan Pembimbing 2 TIDAK BOLEH SAMA
            if ($pembimbing_1 === $pembimbing_2) {
                return array(
                    'status' => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 tidak boleh sama! Silakan pilih dua dosen yang berbeda.'
                );
            }
        }

        // Validasi Catatan saat Reject
        if ($status === 'Rejected' && empty(trim($catatan))) {
            return array(
                'status' => false, 
                'message' => 'Catatan revisi / alasan penolakan wajib diisi jika memilih status Reject!'
            );
        }

        $data = array(
            'status_approval_koor' => $status, // 'Approved' / 'Rejected'
            'catatan_koor'         => trim($catatan),
            'updated_at'           => date('Y-m-d H:i:s')
        );

        if ($status === 'Approved') {
            $data['pembimbing_1']  = $pembimbing_1;
            $data['pembimbing_2']  = $pembimbing_2;
            $data['current_stage'] = 'Ketua KK';
        } else {
            $data['current_stage'] = 'Admin Layanan';
        }

        $this->db->where('nim', $nim);
        $update = $this->db->update('pendaftaran_ta', $data);

        if ($update) {
            return array(
                'status'  => true, 
                'message' => ($status === 'Approved') ? 'Pendaftaran TA berhasil disetujui dan Dosen Pembimbing telah ditetapkan!' : 'Pendaftaran TA berhasil ditolak dengan catatan revisi.'
            );
        } else {
            return array('status' => false, 'message' => 'Gagal memperbarui database.');
        }
    }

    // Ambil detail lengkap sekumpulan mahasiswa terpilih (untuk Cek Dokumen Massal)
    public function get_batch_details_by_nims($nims) {
        if (empty($nims) || !is_array($nims)) return array();

        $this->db->select('
            p.*, 
            m.nama_depan, 
            m.nama_belakang, 
            m.prodi, 
            m.konsentrasi_dkv as m_konsentrasi, 
            m.email as m_email, 
            m.no_hp as m_no_hp,
            m.nip_dosen_wali,
            dw.nama_dosen as nama_dosen_wali
        ');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->join('dosen_wali dw', 'dw.nip = m.nip_dosen_wali', 'left');
        $this->db->where_in('p.nim', $nims);
        return $this->db->get()->result_array();
    }

    // Approval Pendaftaran TA Massal (Batch / Multi-Select) oleh Koordinator TA dengan Opsi Per-Mahasiswa
    public function batch_approval_koor_ajax($nims, $status = 'Approved', $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null, $penguji_1 = null, $penguji_2 = null, $plottings = array()) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Tidak ada mahasiswa yang dipilih.');
        }

        // Format $plottings jika dikirim dalam bentuk array list [{nim, pemb1, pemb2}, ...]
        $plottingsMap = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $item) {
                if (is_array($item) && !empty($item['nim'])) {
                    $plottingsMap[(string)$item['nim']] = $item;
                }
            }
        }

        $successCount = 0;
        $failedList = array();

        foreach ($nims as $nim) {
            $nimStr = (string)$nim;
            $this->db->where('nim', $nimStr);
            $curr = $this->db->get('pendaftaran_ta')->row_array();
            if (!$curr) {
                $failedList[] = "NIM {$nimStr} (Data tidak ditemukan)";
                continue;
            }

            // Validasi per-mahasiswa saat Approve: harus disetujui Dosen Wali & Admin LAA
            if ($status === 'Approved') {
                $statusWali = $curr['status_approval_wali'] ?? 'Pending';
                $statusAdmin = $curr['status_approval_admin'] ?? 'Pending';

                if (strcasecmp($statusWali, 'Approved') !== 0) {
                    $failedList[] = "NIM {$nimStr} (Belum disetujui Dosen Wali)";
                    continue;
                }
                if (strcasecmp($statusAdmin, 'Approved') !== 0) {
                    $failedList[] = "NIM {$nimStr} (Belum diverifikasi Admin Layanan)";
                    continue;
                }
            }

            // Tentukan Dosen Pembimbing untuk mahasiswa ini (individual vs global)
            $p1 = $pembimbing_1;
            $p2 = $pembimbing_2;
            $cat = $catatan;

            if (isset($plottingsMap[$nimStr])) {
                $p1 = $plottingsMap[$nimStr]['pembimbing_1'] ?? $p1;
                $p2 = $plottingsMap[$nimStr]['pembimbing_2'] ?? $p2;
                if (isset($plottingsMap[$nimStr]['catatan_koor'])) {
                    $cat = $plottingsMap[$nimStr]['catatan_koor'];
                }
            }

            if ($status === 'Approved') {
                if (empty($p1) || empty($p2)) {
                    $failedList[] = "NIM {$nimStr} (Pembimbing 1 & 2 wajib dipilih)";
                    continue;
                }
                if ($p1 === $p2) {
                    $failedList[] = "NIM {$nimStr} (Pembimbing 1 dan 2 tidak boleh dosen yang sama)";
                    continue;
                }
            }

            $data = array(
                'status_approval_koor' => $status,
                'catatan_koor'         => trim($cat),
                'updated_at'           => date('Y-m-d H:i:s')
            );

            if ($status === 'Approved') {
                $data['pembimbing_1']  = $p1;
                $data['pembimbing_2']  = $p2;
                if (!empty($penguji_1)) $data['penguji_1'] = $penguji_1;
                if (!empty($penguji_2)) $data['penguji_2'] = $penguji_2;
                $data['current_stage'] = 'Ketua KK';
            } else {
                $data['current_stage'] = 'Admin Layanan';
            }

            $this->db->where('nim', $nimStr);
            if ($this->db->update('pendaftaran_ta', $data)) {
                $successCount++;
            } else {
                $failedList[] = "NIM {$nimStr} (Gagal update database)";
            }
        }

        if ($successCount === 0) {
            $msg = 'Gagal memproses approval massal.';
            if (!empty($failedList)) {
                $msg .= ' Alasan: ' . implode(', ', $failedList);
            }
            return array('status' => false, 'message' => $msg);
        }

        $resMsg = "Berhasil memproses {$successCount} mahasiswa sekaligus!";
        if (!empty($failedList)) {
            $resMsg .= " (" . count($failedList) . " dilewati: " . implode(', ', $failedList) . ")";
        }

        return array(
            'status' => true,
            'message' => $resMsg,
            'success_count' => $successCount,
            'failed_list' => $failedList
        );
    }

    // =========================================================
    // FITUR TAHAP PREVIEW 2 (PLOT DOSEN PENGUJI & JADWAL SIDANG)
    // =========================================================

    // Ambil daftar ruangan yang aktif / tersedia
    public function get_available_ruangan() {
        if (!$this->db->table_exists('ruangan')) {
            return array(
                array('id' => 1, 'kode_ruangan' => 'IK.01.05', 'nama_ruangan' => 'AULA Utama', 'lokasi' => 'Gedung Utama FIK Lantai 1', 'kapasitas' => 35),
                array('id' => 2, 'kode_ruangan' => 'IK.03.05', 'nama_ruangan' => 'Lab 3D printing Utama', 'lokasi' => 'Gedung FIK Lantai 3', 'kapasitas' => 35),
                array('id' => 3, 'kode_ruangan' => 'IK.01.09', 'nama_ruangan' => 'Lab Audio Utama', 'lokasi' => 'Gedung FIK Lantai 1', 'kapasitas' => 35),
                array('id' => 4, 'kode_ruangan' => 'IK.02.01', 'nama_ruangan' => 'Ruang Sidang 1 FIK', 'lokasi' => 'Gedung FIK Lantai 2', 'kapasitas' => 20),
                array('id' => 5, 'kode_ruangan' => 'IK.02.02', 'nama_ruangan' => 'Ruang Sidang 2 FIK', 'lokasi' => 'Gedung FIK Lantai 2', 'kapasitas' => 20)
            );
        }
        $query = $this->db->select('id, kode_ruangan, nama_ruangan, lokasi, kapasitas')
                          ->from('ruangan')
                          ->where('status', 'Tersedia')
                          ->order_by('nama_ruangan', 'ASC')
                          ->get();
        return $query->result_array();
    }

    // Ambil daftar mahasiswa yang berada di tahap Preview 2
    public function get_all_mahasiswa_preview2() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $this->db->select('
            m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.email, m.no_hp,
            p.id as id_pendaftaran, p.nim, p.judul_1, p.pembimbing_1, p.pembimbing_2,
            p.penguji_1, p.penguji_2, p.tgl_sidang, p.jam_mulai_sidang, p.jam_selesai_sidang, p.ruangan_sidang,
            p.status_approval_koor, p.status_approval_kk, p.current_stage, p.is_bimbingan_unlocked,
            dw1.nama_dosen as nama_pembimbing_1,
            dw2.nama_dosen as nama_pembimbing_2,
            dp1.nama_dosen as nama_penguji_1,
            dp2.nama_dosen as nama_penguji_2,
            r.nama_ruangan as detail_nama_ruangan
        ');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->join('dosen_wali dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('dosen_wali dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('dosen_wali dp1', 'dp1.nip = p.penguji_1', 'left');
        $this->db->join('dosen_wali dp2', 'dp2.nip = p.penguji_2', 'left');
        $this->db->join('ruangan r', 'r.nama_ruangan = p.ruangan_sidang OR r.kode_ruangan = p.ruangan_sidang', 'left');
        
        // Hanya mahasiswa yang telah disetujui pendaftarannya oleh Koordinator TA & memiliki Dosen Pembimbing lengkap
        $this->db->where('p.status_approval_koor', 'Approved');
        $this->db->where('p.pembimbing_1 IS NOT NULL', null, false);
        $this->db->where("p.pembimbing_1 !=", "");
        $this->db->where('p.pembimbing_2 IS NOT NULL', null, false);
        $this->db->where("p.pembimbing_2 !=", "");
        
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();

        // Normalisasi data & tentukan status plotting preview 2
        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            if (empty($row['konsentrasi_dkv']) && !empty($row['prodi_mhs'])) {
                $row['konsentrasi_dkv'] = $row['prodi_mhs'];
            }
            if (empty($row['konsentrasi_dkv'])) {
                $row['konsentrasi_dkv'] = 'Informatika';
            }

            // Cek status plotting penguji & jadwal
            $hasPenguji = (!empty($row['penguji_1']) && !empty($row['penguji_2']));
            $hasJadwal  = (!empty($row['tgl_sidang']) && !empty($row['ruangan_sidang']));

            if ($hasPenguji && $hasJadwal) {
                $row['status_preview2'] = 'Terjadwal'; // Sudah diplot Penguji & Ruangan
            } else if ($hasPenguji) {
                $row['status_preview2'] = 'Penguji Ditetapkan'; // Penguji sudah, jadwal belum
            } else {
                $row['status_preview2'] = 'Belum Diplot'; // Menunggu penetapan penguji
            }
        }

        return $result;
    }

    // Update Penguji & Jadwal Sidang Preview 2 untuk Mahasiswa Tunggal
    public function update_penguji_jadwal_preview2($nim, $penguji_1, $penguji_2, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $catatan = '') {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nim) || empty($penguji_1) || empty($penguji_2)) {
            return array('status' => false, 'message' => 'NIM, Dosen Penguji 1, dan Dosen Penguji 2 wajib diisi!');
        }

        if ($penguji_1 === $penguji_2) {
            return array('status' => false, 'message' => 'Dosen Penguji 1 dan Dosen Penguji 2 tidak boleh orang yang sama!');
        }

        // Ambil data pendaftaran untuk cek Dosen Pembimbing
        $this->db->where('nim', $nim);
        $curr = $this->db->get('pendaftaran_ta')->row_array();
        if (!$curr) {
            return array('status' => false, 'message' => 'Data pendaftaran mahasiswa tidak ditemukan.');
        }

        // Validasi: Penguji tidak boleh sama dengan Pembimbing
        if ($penguji_1 === $curr['pembimbing_1'] || $penguji_1 === $curr['pembimbing_2'] || 
            $penguji_2 === $curr['pembimbing_1'] || $penguji_2 === $curr['pembimbing_2']) {
            return array('status' => false, 'message' => 'Dosen Penguji tidak boleh sama dengan Dosen Pembimbing mahasiswa!');
        }

        $data = array(
            'penguji_1'          => $penguji_1,
            'penguji_2'          => $penguji_2,
            'tgl_sidang'         => !empty($tgl_sidang) ? $tgl_sidang : null,
            'jam_mulai_sidang'   => !empty($jam_mulai) ? $jam_mulai : null,
            'jam_selesai_sidang' => !empty($jam_selesai) ? $jam_selesai : null,
            'ruangan_sidang'     => !empty($ruangan) ? $ruangan : null,
            'updated_at'         => date('Y-m-d H:i:s')
        );

        $this->db->where('nim', $nim);
        $update = $this->db->update('pendaftaran_ta', $data);

        if ($update) {
            // Update / Insert ke ta_penguji jika tabel ada
            if ($this->db->table_exists('ta_penguji')) {
                $this->db->where('nim', $nim);
                $existPenguji = $this->db->get('ta_penguji')->row_array();
                if ($existPenguji) {
                    $this->db->where('nim', $nim)->update('ta_penguji', array(
                        'id_penguji_1' => $penguji_1,
                        'id_penguji_2' => $penguji_2
                    ));
                } else {
                    $this->db->insert('ta_penguji', array(
                        'nim'          => $nim,
                        'id_penguji_1' => $penguji_1,
                        'id_penguji_2' => $penguji_2,
                        'created_at'   => date('Y-m-d H:i:s')
                    ));
                }
            }

            // Update / Insert ke ta_jadwal_sidang jika tabel ada dan tanggal diisi
            if (!empty($tgl_sidang) && $this->db->table_exists('ta_jadwal_sidang')) {
                $this->db->where('nim', $nim);
                $existJadwal = $this->db->get('ta_jadwal_sidang')->row_array();
                if ($existJadwal) {
                    $this->db->where('nim', $nim)->update('ta_jadwal_sidang', array(
                        'tanggal' => $tgl_sidang,
                        'waktu'   => $jam_mulai,
                        'ruangan' => $ruangan
                    ));
                } else {
                    $this->db->insert('ta_jadwal_sidang', array(
                        'nim'        => $nim,
                        'tanggal'    => $tgl_sidang,
                        'waktu'      => $jam_mulai,
                        'ruangan'    => $ruangan,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                }
            }

            return array('status' => true, 'message' => 'Dosen Penguji & Jadwal Sidang Preview 2 berhasil ditetapkan!');
        } else {
            return array('status' => false, 'message' => 'Gagal memperbarui database.');
        }
    }

    // Batch Plotting Penguji Preview 2 Massal (Mendukung Per-Mahasiswa Plotting)
    public function batch_penguji_preview2_ajax($nims, $penguji_1 = null, $penguji_2 = null, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $plottings = array()) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Pilih setidaknya satu mahasiswa.');
        }

        // Create a lookup from $plottings if provided
        $plot_map = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $p) {
                if (!empty($p['nim'])) {
                    $plot_map[$p['nim']] = $p;
                }
            }
        }

        $successCount = 0;
        $failedList = array();

        foreach ($nims as $nim) {
            $p1 = $plot_map[$nim]['penguji_1'] ?? $penguji_1;
            $p2 = $plot_map[$nim]['penguji_2'] ?? $penguji_2;

            if (empty($p1) || empty($p2)) {
                $failedList[] = "NIM {$nim} (Penguji belum lengkap)";
                continue;
            }

            if ($p1 === $p2) {
                $failedList[] = "NIM {$nim} (Penguji 1 & 2 sama)";
                continue;
            }

            $res = $this->update_penguji_jadwal_preview2($nim, $p1, $p2);
            if ($res['status']) {
                $successCount++;
            } else {
                $failedList[] = "NIM {$nim} ({$res['message']})";
            }
        }

        if ($successCount > 0) {
            $msg = "Berhasil menetapkan Dosen Penguji untuk {$successCount} mahasiswa.";
            if (!empty($failedList)) {
                $msg .= " Namun " . count($failedList) . " mahasiswa gagal: " . implode(', ', $failedList);
            }
            return array('status' => true, 'message' => $msg, 'success_count' => $successCount);
        } else {
            return array('status' => false, 'message' => 'Gagal menetapkan penguji: ' . implode(', ', $failedList));
        }
    }

    // =========================================================
    // TAHAP 3: PENJADWALAN SIDANG TA & MANAJEMEN RUANGAN DINAMIS
    // =========================================================

    // Ambil daftar mahasiswa yang mendaftar sidang / siap dijadwalkan sidang
    public function get_all_mahasiswa_sidang() {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array();
        }

        $this->db->select('
            m.nama_depan, m.nama_belakang, m.konsentrasi_dkv as prodi_mhs, m.email, m.no_hp,
            p.id as id_pendaftaran, p.nim, p.judul_1, p.pembimbing_1, p.pembimbing_2,
            p.penguji_1, p.penguji_2, p.tgl_sidang, p.jam_mulai_sidang, p.jam_selesai_sidang, p.ruangan_sidang,
            p.status_approval_koor, p.status_approval_kk, p.current_stage,
            dw1.nama_dosen as nama_pembimbing_1,
            dw2.nama_dosen as nama_pembimbing_2,
            dp1.nama_dosen as nama_penguji_1,
            dp2.nama_dosen as nama_penguji_2,
            r.nama_ruangan as detail_nama_ruangan,
            r.kode_ruangan as detail_kode_ruangan,
            r.lokasi as detail_lokasi_ruangan,
            ps.id as id_pendaftaran_sidang,
            ps.status as status_verifikasi_sidang
        ');
        $this->db->from('pendaftaran_ta p');
        $this->db->join('mahasiswa m', 'm.nim = p.nim', 'left');
        $this->db->join('dosen_wali dw1', 'dw1.nip = p.pembimbing_1', 'left');
        $this->db->join('dosen_wali dw2', 'dw2.nip = p.pembimbing_2', 'left');
        $this->db->join('dosen_wali dp1', 'dp1.nip = p.penguji_1', 'left');
        $this->db->join('dosen_wali dp2', 'dp2.nip = p.penguji_2', 'left');
        $this->db->join('ruangan r', 'r.nama_ruangan = p.ruangan_sidang OR r.kode_ruangan = p.ruangan_sidang', 'left');
        $this->db->join('ta_pendaftaran_sidang ps', 'ps.nim = p.nim', 'left');
        
        // Mahasiswa yang telah disetujui Koordinator TA
        $this->db->where('p.status_approval_koor', 'Approved');
        $this->db->order_by('p.created_at', 'DESC');
        $query = $this->db->get();

        $result = $query->result_array();

        foreach ($result as &$row) {
            if (empty($row['nama_depan']) && empty($row['nama_belakang'])) {
                $row['nama_depan'] = 'Mahasiswa';
                $row['nama_belakang'] = $row['nim'];
            }
            $row['nama_lengkap'] = trim($row['nama_depan'] . ' ' . $row['nama_belakang']);
            $row['prodi'] = !empty($row['prodi_mhs']) ? $row['prodi_mhs'] : 'Informatika';

            $hasJadwal = (!empty($row['tgl_sidang']) && !empty($row['jam_mulai_sidang']) && !empty($row['ruangan_sidang']));
            if ($hasJadwal) {
                $row['status_sidang'] = 'Terjadwal';
            } else {
                $row['status_sidang'] = 'Belum Dijadwalkan';
            }
        }

        return $result;
    }

    // Tambah Ruangan Sidang Baru Dinamis
    public function tambah_ruangan_ajax($kode_ruangan, $nama_ruangan, $lokasi = '', $kapasitas = 30) {
        if (!$this->db->table_exists('ruangan')) {
            return array('status' => false, 'message' => 'Tabel ruangan tidak ditemukan.');
        }

        $kode_ruangan = strtoupper(trim($kode_ruangan));
        $nama_ruangan = trim($nama_ruangan);

        if (empty($kode_ruangan) || empty($nama_ruangan)) {
            return array('status' => false, 'message' => 'Kode Ruangan dan Nama Ruangan wajib diisi!');
        }

        // Cek duplikasi kode ruangan
        $this->db->where('kode_ruangan', $kode_ruangan);
        $exist = $this->db->get('ruangan')->row_array();
        if ($exist) {
            return array('status' => false, 'message' => "Kode ruangan '{$kode_ruangan}' sudah ada di sistem!");
        }

        $id_kategori = 1;
        if ($this->db->table_exists('kategori_ruangan')) {
            $kat = $this->db->get('kategori_ruangan')->row_array();
            if ($kat) $id_kategori = $kat['id'];
        }

        $data = array(
            'id_kategori'   => $id_kategori,
            'kode_ruangan'  => $kode_ruangan,
            'nama_ruangan'  => $nama_ruangan,
            'lokasi'        => !empty($lokasi) ? trim($lokasi) : 'Gedung Utama FIK',
            'kapasitas'     => intval($kapasitas) > 0 ? intval($kapasitas) : 30,
            'status'        => 'Tersedia',
            'created_at'    => date('Y-m-d H:i:s')
        );

        $inserted = $this->db->insert('ruangan', $data);
        if ($inserted) {
            $newId = $this->db->insert_id();
            return array(
                'status'  => true,
                'message' => "Ruangan '{$nama_ruangan}' ({$kode_ruangan}) berhasil ditambahkan!",
                'data'    => array_merge(['id' => $newId], $data)
            );
        } else {
            return array('status' => false, 'message' => 'Gagal menyimpan ruangan ke database.');
        }
    }

    // Hapus Ruangan Sidang Dinamis
    public function hapus_ruangan_ajax($id_ruangan) {
        if (!$this->db->table_exists('ruangan')) {
            return array('status' => false, 'message' => 'Tabel ruangan tidak ditemukan.');
        }

        $this->db->where('id', $id_ruangan);
        $ruang = $this->db->get('ruangan')->row_array();
        if (!$ruang) {
            return array('status' => false, 'message' => 'Ruangan tidak ditemukan.');
        }

        $this->db->where('id', $id_ruangan);
        $deleted = $this->db->delete('ruangan');

        if ($deleted) {
            return array('status' => true, 'message' => "Ruangan '{$ruang['nama_ruangan']}' berhasil dihapus.");
        } else {
            return array('status' => false, 'message' => 'Gagal menghapus ruangan.');
        }
    }

    // Update Jadwal Sidang Single Mahasiswa
    public function update_jadwal_sidang_ajax($nim, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nim) || empty($tgl_sidang) || empty($jam_mulai) || empty($ruangan)) {
            return array('status' => false, 'message' => 'NIM, Tanggal Sidang, Jam Mulai, dan Ruangan Sidang wajib diisi.');
        }

        $data = array(
            'tgl_sidang'         => $tgl_sidang,
            'jam_mulai_sidang'   => $jam_mulai,
            'jam_selesai_sidang' => $jam_selesai,
            'ruangan_sidang'     => $ruangan
        );

        $this->db->where('nim', $nim);
        $update = $this->db->update('pendaftaran_ta', $data);

        if ($update) {
            // Update / Insert ke ta_jadwal_sidang jika tabel ada
            if ($this->db->table_exists('ta_jadwal_sidang')) {
                $this->db->where('nim', $nim);
                $existJadwal = $this->db->get('ta_jadwal_sidang')->row_array();
                if ($existJadwal) {
                    $this->db->where('nim', $nim)->update('ta_jadwal_sidang', array(
                        'tanggal' => $tgl_sidang,
                        'waktu'   => $jam_mulai,
                        'ruangan' => $ruangan
                    ));
                } else {
                    $this->db->insert('ta_jadwal_sidang', array(
                        'nim'        => $nim,
                        'tanggal'    => $tgl_sidang,
                        'waktu'      => $jam_mulai,
                        'ruangan'    => $ruangan,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                }
            }

            return array('status' => true, 'message' => 'Jadwal Sidang Tugas Akhir berhasil ditetapkan!');
        } else {
            return array('status' => false, 'message' => 'Gagal memperbarui jadwal sidang.');
        }
    }

    // Batch Update Jadwal Sidang Massal (Multi-Select)
    public function batch_jadwal_sidang_ajax($nims, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan) {
        if (!$this->db->table_exists('pendaftaran_ta')) {
            return array('status' => false, 'message' => 'Tabel pendaftaran_ta tidak ditemukan.');
        }

        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Pilih setidaknya satu mahasiswa.');
        }

        if (empty($tgl_sidang) || empty($jam_mulai) || empty($ruangan)) {
            return array('status' => false, 'message' => 'Tanggal, jam mulai, dan ruangan sidang wajib diisi.');
        }

        $successCount = 0;
        foreach ($nims as $nim) {
            $res = $this->update_jadwal_sidang_ajax($nim, $tgl_sidang, $jam_mulai, $jam_selesai, $ruangan);
            if ($res['status']) {
                $successCount++;
            }
        }

        return array(
            'status'        => true,
            'message'       => "Berhasil menetapkan jadwal sidang untuk {$successCount} mahasiswa!",
            'success_count' => $successCount
        );
    }
}
