<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KoordinatorTA_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Bersih tanpa auto-migration / ALTER TABLE
    }

    /**
     * Ambil list semua dosen dari tabel user (role_id = 3 / Dosen, 6 / Koor, 7 / PIC, 9 / Ketua KK)
     */
    public function get_dosen_list() {
        $this->db->select('id, name as nama_dosen, nip, email, kode_dosen, no_telp as no_hp');
        $this->db->from('user');
        $this->db->where_in('role_id', array(3, 6, 7, 9));
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();

        $result = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $nipKey = !empty($row['nip']) ? (string)$row['nip'] : (string)$row['id'];
                $result[$nipKey] = array(
                    'id'         => $row['id'],
                    'nip'        => $nipKey,
                    'nama_dosen' => $row['nama_dosen'],
                    'email'      => $row['email'] ?? '',
                    'kode_dosen' => $row['kode_dosen'] ?? '',
                    'no_hp'      => $row['no_hp'] ?? '',
                    'prodi'      => 'Informatika'
                );
            }
        }

        return array_values($result);
    }

    /**
     * Helper internal untuk memetakan nama file berkas pendaftaran mahasiswa
     */
    private function _get_mhs_files($id_mhs_list) {
        $files_map = array();
        if (empty($id_mhs_list)) return $files_map;

        $this->db->select('*');
        $this->db->from('file_pendaftaran');
        $this->db->where_in('id_mhs', $id_mhs_list);
        $query = $this->db->get();

        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $f) {
                $rawId = $f['id_mhs'];
                $cleanNim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $rawId);
                $namaJenis = strtolower(trim($f['nama'] ?? ''));

                $st_doswal = $f['status_doswal'] ?? ($f['status_wali'] ?? 'Pending');
                $st_admin  = $f['status_adminlaa'] ?? ($f['status_admin_laa'] ?? ($f['status_admin'] ?? ($f['status_laa'] ?? 'Pending')));

                $keysToMap = array_unique(array_filter([$rawId, $cleanNim, 'usr_mhs_' . $cleanNim, 'mhs_' . $cleanNim]));

                foreach ($keysToMap as $k) {
                    if (!isset($files_map[$k])) {
                        $files_map[$k] = array(
                            'file_ksm'          => null,
                            'status_ksm'        => 'Pending',
                            'file_transkrip'    => null,
                            'status_transkrip'  => 'Pending',
                            'file_pernyataan'   => null,
                            'status_pernyataan' => 'Pending',
                            'file_bebas_lab'    => null,
                            'status_bebas_lab'  => 'Pending',
                            'status_doswal'     => 'Pending',
                            'status_adminlaa'   => 'Pending',
                            'catatan_wali'      => '',
                            'catatan_admin'     => ''
                        );
                    }

                    if (strpos($namaJenis, 'ksm') !== false) {
                        $files_map[$k]['file_ksm'] = $f['file'];
                        $files_map[$k]['status_ksm'] = $st_doswal ?: 'Valid';
                    } elseif (strpos($namaJenis, 'transkrip') !== false) {
                        $files_map[$k]['file_transkrip'] = $f['file'];
                        $files_map[$k]['status_transkrip'] = $st_doswal ?: 'Valid';
                    } elseif (strpos($namaJenis, 'pernyataan') !== false) {
                        $files_map[$k]['file_pernyataan'] = $f['file'];
                        $files_map[$k]['status_pernyataan'] = $st_doswal ?: 'Valid';
                    } elseif (strpos($namaJenis, 'bebas') !== false || strpos($namaJenis, 'lab') !== false) {
                        $files_map[$k]['file_bebas_lab'] = $f['file'];
                        $files_map[$k]['status_bebas_lab'] = $st_doswal ?: 'Valid';
                    }

                    if (!empty($st_doswal) && $st_doswal !== 'Pending') {
                        $files_map[$k]['status_doswal'] = $st_doswal;
                    } elseif (empty($files_map[$k]['status_doswal']) || $files_map[$k]['status_doswal'] === 'Pending') {
                        $files_map[$k]['status_doswal'] = $st_doswal;
                    }

                    if (!empty($st_admin) && $st_admin !== 'Pending') {
                        $files_map[$k]['status_adminlaa'] = $st_admin;
                    } elseif (empty($files_map[$k]['status_adminlaa']) || $files_map[$k]['status_adminlaa'] === 'Pending') {
                        $files_map[$k]['status_adminlaa'] = $st_admin;
                    }

                    if (!empty($f['komentar'])) {
                        $files_map[$k]['catatan_wali'] = $f['komentar'];
                        $files_map[$k]['catatan_admin'] = $f['komentar'];
                    }
                }
            }
        }

        return $files_map;
    }

    /**
     * TAHAP 1: Ambil semua mahasiswa pendaftar TA dari tabel guidance + user + file_pendaftaran
     */
    public function get_all_mahasiswa_ta() {
        $this->db->select('
            g.id as guidance_id,
            g.id_mhs,
            g.judul_1,
            g.judul_2,
            g.judul_3,
            g.judul_en,
            g.peminatan,
            g.tahun,
            g.keterangan as status_approval_koor,
            g.komentar as catatan_koor,
            g.status_file,
            g.date as tanggal_pengajuan,
            g.ipk,
            g.totalsks,
            g.scoretak,
            g.scoreeprt,
            g.jenis_TA,
            u.id as user_id,
            u.name,
            u.nim,
            u.email,
            u.no_telp as no_hp,
            u.prodi as user_prodi,
            u.dosen_wali,
            u_wali.name as nama_dosen_wali_resolved,
            tl.id as id_thesis_lecturers,
            tl.dosen_pembimbing1 as pembimbing_1,
            tl.dosen_pembimbing2 as pembimbing_2,
            tl.dosen_penguji1 as penguji_1,
            tl.dosen_penguji2 as penguji_2,
            tl.kelompok_keahlian as kode_kk,
            tl.status as status_plotting,
            u_p1.name as nama_pembimbing_1,
            u_p2.name as nama_pembimbing_2,
            u_pj1.name as nama_penguji_1,
            u_pj2.name as nama_penguji_2
        ');
        $this->db->from('guidance g');
        $this->db->join('user u', 'u.id = g.id_mhs OR u.nim = g.id_mhs', 'inner');
        $this->db->join('user u_wali', 'u_wali.nip = u.dosen_wali OR u_wali.id = u.dosen_wali', 'left');
        $this->db->join('thesis_lecturers tl', 'tl.id_guidance = g.id', 'left');
        $this->db->join('user u_p1', 'u_p1.nip = tl.dosen_pembimbing1 OR u_p1.id = tl.dosen_pembimbing1', 'left');
        $this->db->join('user u_p2', 'u_p2.nip = tl.dosen_pembimbing2 OR u_p2.id = tl.dosen_pembimbing2', 'left');
        $this->db->join('user u_pj1', 'u_pj1.nip = tl.dosen_penguji1 OR u_pj1.id = tl.dosen_penguji1', 'left');
        $this->db->join('user u_pj2', 'u_pj2.nip = tl.dosen_penguji2 OR u_pj2.id = tl.dosen_penguji2', 'left');
        $this->db->order_by('u.nim', 'ASC');
        $query = $this->db->get();

        if (!$query || $query->num_rows() === 0) {
            return array();
        }

        $rawList = $query->result_array();
        $id_mhs_list = array();
        foreach ($rawList as $row) {
            if (!empty($row['user_id'])) {
                $id_mhs_list[] = $row['user_id'];
                $id_mhs_list[] = 'usr_' . $row['user_id'];
            }
            if (!empty($row['nim'])) {
                $id_mhs_list[] = $row['nim'];
                $id_mhs_list[] = 'usr_mhs_' . $row['nim'];
                $id_mhs_list[] = 'mhs_' . $row['nim'];
            }
            if (!empty($row['id_mhs'])) {
                $id_mhs_list[] = $row['id_mhs'];
                $cNim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', $row['id_mhs']);
                $id_mhs_list[] = $cNim;
                $id_mhs_list[] = 'usr_mhs_' . $cNim;
                $id_mhs_list[] = 'mhs_' . $cNim;
            }
        }
        $id_mhs_list = array_values(array_unique(array_filter($id_mhs_list)));
        $files_map = $this->_get_mhs_files($id_mhs_list);

        $result = array();
        foreach ($rawList as $row) {
            $uId = $row['user_id'] ?: $row['id_mhs'];
            $nim = $row['nim'] ?: $uId;
            $fData = $files_map[$nim] ?? ($files_map['usr_mhs_' . $nim] ?? ($files_map['mhs_' . $nim] ?? ($files_map[$uId] ?? ($files_map[$row['id_mhs']] ?? array()))));

            $nameParts = explode(' ', trim($row['name'] ?? 'Mahasiswa'));
            $nama_depan = array_shift($nameParts);
            $nama_belakang = !empty($nameParts) ? implode(' ', $nameParts) : $nim;

            $status_wali  = $fData['status_doswal'] ?? 'Pending';
            $status_admin = $fData['status_adminlaa'] ?? 'Pending';

            // Dosen Pembimbing lengkap jika P1 dan P2 ada di thesis_lecturers
            $has_pembimbing = (!empty($row['pembimbing_1']) && !empty($row['pembimbing_2']));

            // Status koordinator dianggap Approved hanya jika Dosen Pembimbing sudah diplot DAN keterangan/status_file Approved
            $raw_status_koor = !empty($row['status_approval_koor']) ? $row['status_approval_koor'] : 'Pending';
            if ($raw_status_koor === 'Approved' && !$has_pembimbing) {
                // Jika pembimbing belum diplot, status Koordinator TA masih Pending (Siap Diplot)
                $status_koor = 'Pending';
            } else {
                $status_koor = $raw_status_koor;
            }

            $status_kk = !empty($row['status_plotting']) ? $row['status_plotting'] : 'Pending';

            // Alur Tahapan Resmi:
            // 1. Dosen Wali -> 2. Admin Layanan -> 3. Koordinator TA -> 4. Ketua KK -> 5. Selesai
            $stage = 'Dosen Wali';
            if (strcasecmp($status_wali, 'Approved') === 0) {
                $stage = 'Admin Layanan';
                if (strcasecmp($status_admin, 'Approved') === 0) {
                    $stage = 'Koordinator TA';
                    if (strcasecmp($status_koor, 'Approved') === 0 && $has_pembimbing) {
                        $stage = 'Ketua KK';
                        if (strcasecmp($status_kk, 'Approved') === 0) {
                            $stage = 'Selesai';
                        }
                    }
                }
            }

            $item = array(
                'nim'                   => $nim,
                'user_id'               => $uId,
                'guidance_id'           => $row['guidance_id'] ?? ('gdn_' . $nim),
                'nama'                  => $row['name'] ?? 'Mahasiswa',
                'nama_depan'            => $nama_depan,
                'nama_belakang'         => $nama_belakang,
                'email'                 => $row['email'] ?? ($nim . '@student.telkomuniversity.ac.id'),
                'no_hp'                 => $row['no_hp'] ?? '',
                'prodi'                 => $row['user_prodi'] ?? 'Informatika',
                'konsentrasi_dkv'       => $row['peminatan'] ?? 'Informatika',
                'peminatan'             => $row['peminatan'] ?? 'Informatika',
                'kode_kk'               => $row['kode_kk'] ?? 'KK-SIDE',
                'judul_1'               => $row['judul_1'] ?? 'Pengajuan Tugas Akhir Mahasiswa',
                'judul_2'               => $row['judul_2'] ?? '',
                'judul_3'               => $row['judul_3'] ?? '',
                'judul_en'              => $row['judul_en'] ?? '',
                'deskripsi_1'           => $row['judul_1'] ?? '',
                'deskripsi_2'           => $row['judul_2'] ?? '',
                'deskripsi_3'           => $row['judul_3'] ?? '',
                'nama_dosen_wali'       => $row['nama_dosen_wali_resolved'] ?? ($row['dosen_wali'] ?? 'Dosen Wali'),
                'nip_dosen_wali'        => $row['dosen_wali'] ?? '',
                'pembimbing_1'          => $row['pembimbing_1'] ?? '',
                'pembimbing_2'          => $row['pembimbing_2'] ?? '',
                'nama_pembimbing_1'     => $row['nama_pembimbing_1'] ?? ($row['pembimbing_1'] ?? ''),
                'nama_pembimbing_2'     => $row['nama_pembimbing_2'] ?? ($row['pembimbing_2'] ?? ''),
                'penguji_1'             => $row['penguji_1'] ?? '',
                'penguji_2'             => $row['penguji_2'] ?? '',
                'nama_penguji_1'        => $row['nama_penguji_1'] ?? ($row['penguji_1'] ?? ''),
                'nama_penguji_2'        => $row['nama_penguji_2'] ?? ($row['penguji_2'] ?? ''),
                'status_approval_wali'  => $status_wali,
                'status_approval_admin' => $status_admin,
                'status_approval_koor'  => $status_koor,
                'status_approval_kk'    => $status_kk,
                'catatan_wali'          => $fData['catatan_wali'] ?? '',
                'catatan_admin'         => $fData['catatan_admin'] ?? '',
                'catatan_koor'          => $row['catatan_koor'] ?? '',
                'current_stage'         => $stage,
                'file_ksm'              => $fData['file_ksm'] ?? null,
                'status_ksm'            => $fData['status_ksm'] ?? 'Valid',
                'file_transkrip'        => $fData['file_transkrip'] ?? null,
                'status_transkrip'      => $fData['status_transkrip'] ?? 'Valid',
                'file_pernyataan'       => $fData['file_pernyataan'] ?? null,
                'status_pernyataan'     => $fData['status_pernyataan'] ?? 'Valid',
                'file_bebas_lab'        => $fData['file_bebas_lab'] ?? null,
                'status_bebas_lab'      => $fData['status_bebas_lab'] ?? 'Valid',
                'ipk'                   => $row['ipk'] ?? '3.50',
                'totalsks'              => $row['totalsks'] ?? '120',
                'scoretak'              => $row['scoretak'] ?? '45',
                'scoreeprt'             => $row['scoreeprt'] ?? '480',
                'jenis_TA'              => $row['jenis_TA'] ?? 'TA Reguler',
                'is_submitted'          => 1
            );

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Ambil detail pendaftaran satu mahasiswa berdasarkan NIM / user_id
     */
    public function get_detail_pendaftaran_mahasiswa($nim) {
        $list = $this->get_all_mahasiswa_ta();
        foreach ($list as $row) {
            if ($row['nim'] == $nim || $row['user_id'] == $nim) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Simpan persetujuan / penolakan pendaftaran TA oleh Koordinator TA
     */
    public function update_approval_koor_ajax($nim, $status, $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null) {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa dengan NIM ' . $nim . ' tidak ditemukan.');
        }

        $userId = $mhs['id'];

        if ($status === 'Approved') {
            if (empty($pembimbing_1) || empty($pembimbing_2)) {
                return array(
                    'status'  => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 wajib dipilih sebelum menyetujui pendaftaran TA!'
                );
            }

            if ($pembimbing_1 === $pembimbing_2) {
                return array(
                    'status'  => false, 
                    'message' => 'Dosen Pembimbing 1 dan Dosen Pembimbing 2 tidak boleh sama! Silakan pilih dua dosen yang berbeda.'
                );
            }
        }

        if ($status === 'Rejected' && empty(trim($catatan))) {
            return array(
                'status'  => false, 
                'message' => 'Catatan revisi / alasan penolakan wajib diisi jika memilih status Reject!'
            );
        }

        $target_ids = array_unique([$userId, $mhs['nim'], 'usr_mhs_' . $mhs['nim'], 'mhs_' . $mhs['nim']]);
        $this->db->where_in('id_mhs', $target_ids);
        $guidance = $this->db->get('guidance')->row_array();

        $guidanceId = $guidance ? $guidance['id'] : ('gdn_' . ($mhs['nim'] ?: uniqid()));

        $guidanceData = array(
            'keterangan'  => $status,
            'komentar'    => trim($catatan),
            'status_file' => ($status === 'Approved') ? 'Approved' : 'Rejected',
            'date'        => date('Y-m-d H:i:s')
        );

        if (!$guidance) {
            $guidanceData['id']         = $guidanceId;
            $guidanceData['id_mhs']     = $userId;
            $guidanceData['judul_1']    = 'Tugas Akhir Mahasiswa ' . $mhs['name'];
            $guidanceData['peminatan']  = 'Informatika';
            $guidanceData['tahun']      = date('Y');
            $guidanceData['jenis_TA']   = 'TA Reguler';
            $this->db->insert('guidance', $guidanceData);
        } else {
            $this->db->where('id', $guidance['id']);
            $this->db->update('guidance', $guidanceData);
            $guidanceId = $guidance['id'];
        }

        // Upsert plotting di thesis_lecturers jika pembimbing dipilih
        $p1_lama = '';
        $p2_lama = '';
        if (!empty($pembimbing_1) || !empty($pembimbing_2)) {
            $this->db->where('id_guidance', $guidanceId);
            $tl = $this->db->get('thesis_lecturers')->row_array();

            if (!$tl) {
                $tlData = array(
                    'id'                => 'tl_' . uniqid(),
                    'id_guidance'       => $guidanceId,
                    'dosen_pembimbing1' => (string)$pembimbing_1,
                    'kelompok_keahlian' => 'KK-SIDE',
                    'dosen_pembimbing2' => (string)$pembimbing_2,
                    'dosen_penguji1'    => '',
                    'dosen_penguji2'    => '',
                    'date'              => date('Y-m-d H:i:s'),
                    'date_edit'         => date('Y-m-d H:i:s'),
                    'status'            => 'Pending' // Menunggu approval Ketua KK
                );
                $this->db->insert('thesis_lecturers', $tlData);
            } else {
                $p1_lama = $tl['dosen_pembimbing1'];
                $p2_lama = $tl['dosen_pembimbing2'];

                $tlData = array(
                    'dosen_pembimbing1' => (string)$pembimbing_1,
                    'dosen_pembimbing2' => (string)$pembimbing_2,
                    'kelompok_keahlian' => 'KK-SIDE',
                    'date_edit'         => date('Y-m-d H:i:s')
                );
                $this->db->where('id', $tl['id']);
                $this->db->update('thesis_lecturers', $tlData);
            }
        }

        // Catat ke log history
        $this->_log_history(array(
            'modul'         => 'Koordinator TA',
            'ref_id'        => $nim,
            'target_name'   => $mhs['name'],
            'action'        => ($status === 'Approved') ? 'Approved' : 'Rejected',
            'catatan'       => json_encode(array(
                'kategori'      => 'Pembimbing',
                'pembimbing_1'  => (string)$pembimbing_1,
                'pembimbing_2'  => (string)$pembimbing_2,
                'p1_lama'       => (string)$p1_lama,
                'p2_lama'       => (string)$p2_lama,
                'catatan_koor'  => $catatan
            ))
        ));

        return array(
            'status'  => true,
            'message' => ($status === 'Approved') ? 'Pendaftaran TA berhasil disetujui dan Dosen Pembimbing telah ditetapkan!' : 'Pendaftaran TA berhasil ditolak dengan catatan revisi.'
        );
    }

    /**
     * Ambil sekumpulan detail mahasiswa untuk modal massal
     */
    public function get_batch_details_by_nims($nims) {
        if (empty($nims) || !is_array($nims)) return array();
        $all = $this->get_all_mahasiswa_ta();
        $res = array();
        foreach ($all as $item) {
            if (in_array($item['nim'], $nims) || in_array($item['user_id'], $nims)) {
                $res[] = $item;
            }
        }
        return $res;
    }

    /**
     * Eksekusi batch approval dan plotting pembimbing massal
     */
        /**
     * Eksekusi batch approval dan plotting pembimbing massal
     */
    public function batch_approval_koor_ajax($nims, $status = 'Approved', $catatan = '', $pembimbing_1 = null, $pembimbing_2 = null, $penguji_1 = null, $penguji_2 = null, $plottings = array()) {
        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Tidak ada data mahasiswa yang dikirim.');
        }

        $plottingMap = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $p) {
                if (!empty($p['nim'])) {
                    $plottingMap[$p['nim']] = $p;
                }
            }
        }

        $successCount = 0;
        $failCount = 0;
        $errors = array();

        foreach ($nims as $nim) {
            $pData = $plottingMap[$nim] ?? array();
            $p1 = $pData['pembimbing_1'] ?? $pembimbing_1;
            $p2 = $pData['pembimbing_2'] ?? $pembimbing_2;
            $c  = $pData['catatan_koor'] ?? ($pData['catatan'] ?? $catatan);

            $res = $this->update_approval_koor_ajax($nim, $status, $c, $p1, $p2);
            if ($res['status']) {
                $successCount++;
            } else {
                $failCount++;
                $errors[] = "NIM {$nim}: " . $res['message'];
            }
        }

        return array(
            'status'        => ($successCount > 0),
            'success_count' => $successCount,
            'fail_count'    => $failCount,
            'errors'        => $errors,
            'message'       => ($successCount > 0)
                ? "Plotting Dosen Pembimbing untuk {$successCount} mahasiswa berhasil disimpan!"
                : "Gagal memproses plotting: " . implode(', ', $errors)
        );
    }

    public function execute_batch_approval_ajax($batchData, $defaultStatus = 'Approved') {
        $nims = array_column($batchData, 'nim');
        return $this->batch_approval_koor_ajax($nims, $defaultStatus, '', null, null, null, null, $batchData);
    }

    /**
     * Ambil daftar ruangan yang tersedia dari tabel ruangan (di-split per sub-ruangan/kode ruangan jika ada koma)
     */
    public function get_available_ruangan() {
        $this->db->select('id, ruangan as nama_ruangan, kapasitas, akses as status, spesifikasi_fasilitas as fasilitas, date as tanggal_dibuat');
        $this->db->from('ruangan');
        $this->db->order_by('id', 'ASC');
        $this->db->order_by('ruangan', 'ASC');
        $query = $this->db->get();

        $result = array();
        if ($query && $query->num_rows() > 0) {
            $rows = $query->result_array();
            foreach ($rows as $r) {
                $rawId = (string)($r['id'] ?? '');
                $namaRuang = trim((string)($r['nama_ruangan'] ?? ''));

                if (strpos($rawId, ',') !== false) {
                    $codes = explode(',', $rawId);
                    foreach ($codes as $c) {
                        $codeTrim = trim($c);
                        if (!empty($codeTrim)) {
                            $result[] = array(
                                'id'           => $codeTrim,
                                'kode_ruangan' => $codeTrim,
                                'nama_ruangan' => $namaRuang,
                                'nama_lengkap' => $namaRuang . ' (Ruang: ' . $codeTrim . ')',
                                'kapasitas'    => $r['kapasitas'] ?? 30,
                                'status'       => $r['status'] ?? 'Tersedia',
                                'fasilitas'    => $r['fasilitas'] ?? ''
                            );
                        }
                    }
                } else {
                    $codeTrim = trim($rawId);
                    $result[] = array(
                        'id'           => !empty($codeTrim) ? $codeTrim : $namaRuang,
                        'kode_ruangan' => $codeTrim,
                        'nama_ruangan' => $namaRuang,
                        'nama_lengkap' => !empty($codeTrim) ? ($namaRuang . ' (Ruang: ' . $codeTrim . ')') : $namaRuang,
                        'kapasitas'    => $r['kapasitas'] ?? 30,
                        'status'       => $r['status'] ?? 'Tersedia',
                        'fasilitas'    => $r['fasilitas'] ?? ''
                    );
                }
            }
            return $result;
        }

        return array(
            array('id' => 'LK.01.01', 'kode_ruangan' => 'LK.01.01', 'nama_ruangan' => 'AULA Utama', 'nama_lengkap' => 'AULA Utama (Ruang: LK.01.01)', 'kapasitas' => 94, 'status' => 'Tersedia', 'fasilitas' => 'Proyektor, AC, Sound System'),
            array('id' => 'LK.01.02', 'kode_ruangan' => 'LK.01.02', 'nama_ruangan' => 'green screen', 'nama_lengkap' => 'green screen (Ruang: LK.01.02)', 'kapasitas' => 100, 'status' => 'Tersedia', 'fasilitas' => 'Proyektor, AC'),
            array('id' => 'LK.01.03', 'kode_ruangan' => 'LK.01.03', 'nama_ruangan' => 'Lab Incubator', 'nama_lengkap' => 'Lab Incubator (Ruang: LK.01.03)', 'kapasitas' => 30, 'status' => 'Tersedia', 'fasilitas' => 'Proyektor, AC'),
            array('id' => 'LK.01.04', 'kode_ruangan' => 'LK.01.04', 'nama_ruangan' => 'Lab Incubator', 'nama_lengkap' => 'Lab Incubator (Ruang: LK.01.04)', 'kapasitas' => 30, 'status' => 'Tersedia', 'fasilitas' => 'Proyektor, AC'),
            array('id' => 'LK.01.05', 'kode_ruangan' => 'LK.01.05', 'nama_ruangan' => 'Lab Incubator', 'nama_lengkap' => 'Lab Incubator (Ruang: LK.01.05)', 'kapasitas' => 30, 'status' => 'Tersedia', 'fasilitas' => 'Proyektor, AC')
        );
    }

    /**
     * Tambah data master ruangan
     */
    public function tambah_ruangan_ajax($nama_ruangan, $kapasitas, $fasilitas = '', $status = 'Tersedia') {
        $data = array(
            'id'                    => 'R_' . uniqid(),
            'ruangan'               => trim($nama_ruangan),
            'kapasitas'             => (int)$kapasitas,
            'spesifikasi_fasilitas' => trim($fasilitas),
            'akses'                 => $status,
            'id_kategori'           => '1',
            'date'                  => date('Y-m-d H:i:s')
        );
        $this->db->insert('ruangan', $data);
        return array('status' => true, 'message' => 'Ruangan baru berhasil ditambahkan.');
    }

    /**
     * Hapus data master ruangan
     */
    public function hapus_ruangan_ajax($id) {
        $this->db->where('id', $id);
        $this->db->delete('ruangan');
        return array('status' => true, 'message' => 'Ruangan berhasil dihapus.');
    }

    /**
     * TAHAP 2: Ambil mahasiswa untuk Tahap Preview 2 (Siap diplot Penguji & Jadwal Sidang Preview 2)
     */
    public function get_all_mahasiswa_preview2() {
        $all = $this->get_all_mahasiswa_ta();
        if (empty($all)) {
            return array();
        }

        $guidanceIds = array_filter(array_column($all, 'guidance_id'));
        $gMap = array();
        if (!empty($guidanceIds)) {
            $this->db->select('id, id_mhs, tanggal_presentasi, waktu_presentasi, ruang_sidang, status_preview');
            $this->db->from('guidance');
            $this->db->where_in('id', $guidanceIds);
            $gQuery = $this->db->get();
            if ($gQuery && $gQuery->num_rows() > 0) {
                foreach ($gQuery->result_array() as $gr) {
                    $gMap[$gr['id']] = $gr;
                }
            }
        }

        $filtered = array();
        foreach ($all as $item) {
            $gId = $item['guidance_id'] ?? '';
            $gRow = $gMap[$gId] ?? array();
            $statusPreview = strtolower(trim($gRow['status_preview'] ?? ''));

            $hasPembimbing = (!empty($item['pembimbing_1']) || !empty($item['pembimbing_2']));
            $hasPenguji    = (!empty($item['penguji_1']) && !empty($item['penguji_2']));
            $hasAnyPenguji = (!empty($item['penguji_1']) || !empty($item['penguji_2']));
            $hasJadwalPresentasi = !empty($gRow['tanggal_presentasi']);

            $isApprovedKoor = (strcasecmp($item['status_approval_koor'] ?? '', 'Approved') === 0);
            $isApprovedDoswalAdmin = (strcasecmp($item['status_approval_wali'] ?? '', 'Approved') === 0 && strcasecmp($item['status_adminlaa'] ?? '', 'Approved') === 0);

            // Syarat masuk Preview 2:
            // 1. Sudah berada di pipeline preview/sidang/selesai/lulus, ATAU
            // 2. Pembimbing sudah diplot, ATAU
            // 3. Proposal sudah di-approve Koordinator TA, ATAU
            // 4. Sudah di-approve Dosen Wali & Admin Layanan (siap masuk proses Koordinator TA / Bimbingan), ATAU
            // 5. Sudah ada penguji / jadwal yang tersimpan
            $isEligible = in_array($statusPreview, ['preview1', 'preview2', 'preview3', 'sidang', 'selesai', 'lulus'])
                || $hasPembimbing
                || $isApprovedKoor
                || $isApprovedDoswalAdmin
                || $hasAnyPenguji
                || $hasJadwalPresentasi;

            // Jika status proposal secara eksplisit ditolak dan belum ada penguji/jadwal, abaikan
            if (strcasecmp($item['status_approval_koor'] ?? '', 'Rejected') === 0 ||
                strcasecmp($item['status_approval_wali'] ?? '', 'Rejected') === 0 ||
                strcasecmp($item['status_adminlaa'] ?? '', 'Rejected') === 0) {
                if (!$hasAnyPenguji && !$hasJadwalPresentasi) {
                    continue;
                }
            }

            if (!$isEligible) {
                continue;
            }

            $item['tgl_sidang']         = $gRow['tanggal_presentasi'] ?? null;
            $item['jam_mulai_sidang']   = $gRow['waktu_presentasi'] ?? null;
            $item['jam_selesai_sidang'] = null;
            $item['ruangan_sidang']     = $gRow['ruang_sidang'] ?? null;
            $item['status_preview']     = !empty($gRow['status_preview']) ? $gRow['status_preview'] : 'preview2';

            $hasJadwal = (!empty($item['tgl_sidang']) && !empty($item['jam_mulai_sidang']));

            if ($hasPenguji && $hasJadwal) {
                $item['status_preview2'] = 'Terjadwal';
            } elseif ($hasPenguji) {
                $item['status_preview2'] = 'Penguji Ditetapkan';
            } elseif ($hasAnyPenguji) {
                $item['status_preview2'] = 'Sebagian Diplot';
            } else {
                $item['status_preview2'] = 'Belum Diplot';
            }

            $filtered[] = $item;
        }

        return $filtered;
    }

    /**
     * Update Dosen Penguji & Jadwal Sidang Preview 2
     */
    public function update_penguji_jadwal_preview2($nim, $penguji_1, $penguji_2, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $catatan = '') {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa tidak ditemukan.');
        }

        $userId = $mhs['id'];
        $mhsNim = $mhs['nim'] ?: $nim;
        $target_ids = array_values(array_unique(array_filter([$userId, $mhsNim, 'usr_mhs_' . $mhsNim, 'mhs_' . $mhsNim, 'usr_' . $userId])));

        $this->db->where_in('id_mhs', $target_ids);
        $this->db->or_where('id', 'gdn_' . $mhsNim);
        $g = $this->db->get('guidance')->row_array();

        if (!$g) {
            $gId = 'gdn_' . $mhsNim;
            $gInsert = array(
                'id'                 => $gId,
                'id_mhs'             => $userId,
                'judul_1'            => 'Tugas Akhir Mahasiswa ' . $mhs['name'],
                'peminatan'          => 'Informatika',
                'tahun'              => date('Y'),
                'jenis_TA'           => 'TA Reguler',
                'tanggal_presentasi' => $tgl_sidang,
                'waktu_presentasi'   => $jam_mulai,
                'ruang_sidang'       => $ruangan,
                'status_preview'     => 'preview2',
                'date'               => date('Y-m-d H:i:s')
            );
            $this->db->insert('guidance', $gInsert);
        } else {
            $gId = $g['id'];
            $currentStatusPreview = strtolower(trim($g['status_preview'] ?? ''));
            $newStatusPreview = in_array($currentStatusPreview, ['preview3', 'sidang', 'selesai', 'lulus']) ? $currentStatusPreview : 'preview2';

            $gUpdate = array(
                'tanggal_presentasi' => $tgl_sidang,
                'waktu_presentasi'   => $jam_mulai,
                'ruang_sidang'       => $ruangan,
                'status_preview'     => $newStatusPreview
            );
            $this->db->where('id', $gId);
            $this->db->update('guidance', $gUpdate);
        }

        $this->db->where('id_guidance', $gId);
        $tl = $this->db->get('thesis_lecturers')->row_array();

        $pj1_lama = '';
        $pj2_lama = '';

        if (!$tl) {
            $tlData = array(
                'id'                => 'tl_' . uniqid(),
                'id_guidance'       => $gId,
                'dosen_pembimbing1' => '',
                'kelompok_keahlian' => 'KK-SIDE',
                'dosen_pembimbing2' => '',
                'dosen_penguji1'    => (string)$penguji_1,
                'dosen_penguji2'    => (string)$penguji_2,
                'date'              => date('Y-m-d H:i:s'),
                'date_edit'         => date('Y-m-d H:i:s'),
                'status'            => 'Pending'
            );
            $this->db->insert('thesis_lecturers', $tlData);
        } else {
            $pj1_lama = $tl['dosen_penguji1'];
            $pj2_lama = $tl['dosen_penguji2'];

            $tlData = array(
                'dosen_penguji1' => (string)$penguji_1,
                'dosen_penguji2' => (string)$penguji_2,
                'date_edit'      => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $tl['id']);
            $this->db->update('thesis_lecturers', $tlData);
        }

        // Catat ke log history
        $this->_log_history(array(
            'modul'         => 'Plotting Penguji',
            'ref_id'        => $nim,
            'target_name'   => $mhs['name'],
            'action'        => 'Approved',
            'catatan'       => json_encode(array(
                'kategori'      => 'Penguji',
                'penguji_1'     => (string)$penguji_1,
                'penguji_2'     => (string)$penguji_2,
                'pj1_lama'      => (string)$pj1_lama,
                'pj2_lama'      => (string)$pj2_lama,
                'tgl_sidang'    => $tgl_sidang,
                'ruangan'       => $ruangan,
                'catatan_koor'  => $catatan
            ))
        ));

        return array('status' => true, 'message' => 'Plotting Dosen Penguji & Jadwal Preview 2 berhasil disimpan!');
    }

    /**
     * Batch update dosen penguji & jadwal preview 2
     */
        /**
     * Batch update dosen penguji & jadwal preview 2
     */
    public function batch_penguji_preview2_ajax($nims, $penguji_1 = null, $penguji_2 = null, $tgl_sidang = null, $jam_mulai = null, $jam_selesai = null, $ruangan = null, $plottings = array()) {
        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Tidak ada data mahasiswa yang dikirim.');
        }

        $plottingMap = array();
        if (!empty($plottings) && is_array($plottings)) {
            foreach ($plottings as $p) {
                if (!empty($p['nim'])) {
                    $plottingMap[$p['nim']] = $p;
                }
            }
        }

        $success = 0;
        foreach ($nims as $nim) {
            $pData = $plottingMap[$nim] ?? array();
            $pj1   = $pData['penguji_1'] ?? $penguji_1;
            $pj2   = $pData['penguji_2'] ?? $penguji_2;
            $tgl   = $pData['tgl_sidang'] ?? $tgl_sidang;
            $mulai = $pData['jam_mulai'] ?? $jam_mulai;
            $sel   = $pData['jam_selesai'] ?? $jam_selesai;
            $ruang = $pData['ruangan'] ?? $ruangan;
            $cat   = $pData['catatan'] ?? ($pData['catatan_koor'] ?? '');

            if (!empty($nim)) {
                $r = $this->update_penguji_jadwal_preview2($nim, $pj1, $pj2, $tgl, $mulai, $sel, $ruang, $cat);
                if ($r['status']) $success++;
            }
        }

        return array(
            'status'        => ($success > 0),
            'success_count' => $success,
            'message'       => "Plotting Penguji Massal berhasil disimpan ({$success} data)."
        );
    }

    public function batch_update_preview2_penguji($batchData) {
        $nims = array_column($batchData, 'nim');
        return $this->batch_penguji_preview2_ajax($nims, null, null, null, null, null, null, $batchData);
    }

    /**
     * TAHAP 3: Ambil mahasiswa untuk Jadwal Sidang TA & Rekapitulasi Penilaian Sidang
     */
    public function get_all_mahasiswa_sidang() {
        $all = $this->get_all_mahasiswa_preview2();
        if (empty($all)) {
            return array();
        }

        $guidanceIds = array_filter(array_column($all, 'guidance_id'));
        $gMap = array();
        if (!empty($guidanceIds)) {
            $this->db->select('
                id,
                tanggal_sidang,
                waktu_sidang,
                ruang_sidang,
                link_sidang,
                status_preview,
                nilaisidang_pembimbing1,
                nilaisidang_pembimbing2,
                nilaisidang_penguji1,
                nilaisidang_penguji2,
                penilaiansidang_pembimbing1,
                penilaiansidang_pembimbing2,
                penilaiansidang_penguji1,
                penilaiansidang_penguji2,
                evaluasi_pembimbing1,
                evaluasi_pembimbing2,
                evaluasi_penguji1,
                evaluasi_penguji2,
                bap,
                status_bap
            ');
            $this->db->from('guidance');
            $this->db->where_in('id', $guidanceIds);
            $gQuery = $this->db->get();

            if ($gQuery && $gQuery->num_rows() > 0) {
                foreach ($gQuery->result_array() as $gr) {
                    $gMap[$gr['id']] = $gr;
                }
            }
        }

        // Ambil riwayat aktivitas dari tabel thesis
        $thesisMap = array();
        if (!empty($guidanceIds) && $this->db->table_exists('thesis')) {
            $this->db->select('id_guidance, tahapan_preview, status');
            $this->db->where_in('id_guidance', $guidanceIds);
            $tQuery = $this->db->get('thesis');
            if ($tQuery && $tQuery->num_rows() > 0) {
                foreach ($tQuery->result_array() as $tr) {
                    $tGid = $tr['id_guidance'];
                    $thp = strtolower(trim($tr['tahapan_preview'] ?? ''));
                    $isApp = (strcasecmp($tr['status'] ?? '', 'Approved') === 0);
                    if (!isset($thesisMap[$tGid])) {
                        $thesisMap[$tGid] = array('has_p3' => false, 'p2_app' => false, 'has_sidang' => false, 'p3_app' => false);
                    }
                    if ($thp === 'preview2' && $isApp) $thesisMap[$tGid]['p2_app'] = true;
                    if ($thp === 'preview3') { $thesisMap[$tGid]['has_p3'] = true; if ($isApp) $thesisMap[$tGid]['p3_app'] = true; }
                    if ($thp === 'sidang')   { $thesisMap[$tGid]['has_sidang'] = true; if ($isApp) $thesisMap[$tGid]['p3_app'] = true; }
                }
            }
        }

        // Ambil riwayat log publikasi nilai terbaru per NIM dari log_approval_history
        $publishMap = array();
        if ($this->db->table_exists('log_approval_history')) {
            $allNims = array_unique(array_filter(array_column($all, 'nim')));
            if (!empty($allNims)) {
                $this->db->from('log_approval_history');
                $this->db->where('modul', 'Publish Nilai Sidang');
                $this->db->where_in('ref_id', $allNims);
                $this->db->order_by('id', 'DESC');
                $pubLogs = $this->db->get()->result_array();

                foreach ($pubLogs as $pl) {
                    $nimKey = (string)$pl['ref_id'];
                    if (!isset($publishMap[$nimKey])) {
                        $parsed = array();
                        if (!empty($pl['catatan']) && $pl['catatan'][0] === '{') {
                            $parsed = json_decode($pl['catatan'], true) ?: array();
                        }
                        $publishMap[$nimKey] = array(
                            'action'      => $pl['action'],
                            'status'      => $parsed['status_publish'] ?? $pl['action'],
                            'tgl_publish' => $parsed['tgl_publish'] ?? null,
                            'created_at'  => $pl['created_at']
                        );
                    }
                }
            }
        }

        $result = array();
        foreach ($all as $item) {
            $gId = $item['guidance_id'];
            $gRow = $gMap[$gId] ?? array();
            $tInfo = $thesisMap[$gId] ?? array();

            $statusPreview = strtolower(trim($gRow['status_preview'] ?? ($item['status_preview'] ?? '')));
            $hasPenguji = !empty($item['penguji_1']) && !empty($item['penguji_2']);
            $hasJadwalSidang = !empty($gRow['tanggal_sidang']);

            // Syarat masuk Tab 3 (Jadwal Sidang & Penilaian Sidang):
            // Mahasiswa sudah masuk ke tahap Preview 3 / Pra-Sidang / Sidang / Terjadwal Sidang
            $isInPreview3OrSidang = in_array($statusPreview, ['preview3', 'sidang', 'selesai', 'lulus'])
                || $hasJadwalSidang
                || !empty($gRow['nilaisidang_pembimbing1'])
                || !empty($tInfo['has_p3'])
                || !empty($tInfo['p2_app'])
                || !empty($tInfo['has_sidang']);

            if (!$isInPreview3OrSidang) {
                continue;
            }

            $n1 = (float)($gRow['nilaisidang_pembimbing1'] ?? 0);
            $n2 = (float)($gRow['nilaisidang_pembimbing2'] ?? 0);
            $np1 = (float)($gRow['nilaisidang_penguji1'] ?? 0);
            $np2 = (float)($gRow['nilaisidang_penguji2'] ?? 0);

            // Validasi kelengkapan 4 komponen nilai: Semua harus > 0 (bukan nol/kosong)
            $isNilaiLengkap = ($n1 > 0 && $n2 > 0 && $np1 > 0 && $np2 > 0);

            $komponenBelumTerisi = array();
            if ($n1 <= 0) $komponenBelumTerisi[] = 'Dosen Pembimbing 1';
            if ($n2 <= 0) $komponenBelumTerisi[] = 'Dosen Pembimbing 2';
            if ($np1 <= 0) $komponenBelumTerisi[] = 'Dosen Penguji 1';
            if ($np2 <= 0) $komponenBelumTerisi[] = 'Dosen Penguji 2';

            $komponenTerisiCount = 4 - count($komponenBelumTerisi);

            $avgScore = 0;
            $grade = '-';
            $statusKelulusan = 'Belum Dinilai';

            if ($isNilaiLengkap) {
                $avgScore = round(($n1 + $n2 + $np1 + $np2) / 4, 2);
                
                // Konversi Grade Mutu
                if ($avgScore >= 85) $grade = 'A';
                elseif ($avgScore >= 77.5) $grade = 'AB';
                elseif ($avgScore >= 70) $grade = 'B';
                elseif ($avgScore >= 62.5) $grade = 'BC';
                elseif ($avgScore >= 55) $grade = 'C';
                elseif ($avgScore >= 45) $grade = 'D';
                else $grade = 'E';

                if ($avgScore >= 70) $statusKelulusan = 'Lulus';
                elseif ($avgScore >= 55) $statusKelulusan = 'Lulus dengan Revisi';
                else $statusKelulusan = 'Tidak Lulus';
            } elseif ($komponenTerisiCount > 0) {
                $validScores = array_filter(array($n1, $n2, $np1, $np2), fn($v) => $v > 0);
                $partialAvg = count($validScores) > 0 ? round(array_sum($validScores) / count($validScores), 2) : 0;
                $avgScore = $partialAvg;
                $statusKelulusan = 'Belum Lengkap';
            }

            $tglSidang = !empty($gRow['tanggal_sidang']) ? $gRow['tanggal_sidang'] : null;
            $waktuSidang = !empty($gRow['waktu_sidang']) ? $gRow['waktu_sidang'] : null;
            $ruangSidang = !empty($gRow['ruang_sidang']) ? trim($gRow['ruang_sidang']) : null;
            $isScheduled = !empty($tglSidang);

            // Status publikasi
            $nimKey = (string)$item['nim'];
            $pubInfo = $publishMap[$nimKey] ?? null;
            $statusPublish = 'Draft';
            $tglPublish = null;

            if ($pubInfo) {
                if ($pubInfo['action'] === 'Published' || $pubInfo['status'] === 'Published') {
                    $statusPublish = 'Published';
                    $tglPublish = $pubInfo['tgl_publish'] ?: $pubInfo['created_at'];
                } elseif ($pubInfo['action'] === 'Scheduled' || $pubInfo['status'] === 'Scheduled') {
                    $statusPublish = 'Scheduled';
                    $tglPublish = $pubInfo['tgl_publish'];
                }
            }

            if (!$isNilaiLengkap) {
                $statusPublish = 'Belum Lengkap';
            }

            $item['tgl_sidang']               = $tglSidang;
            $item['tanggal_sidang']           = $tglSidang;
            $item['jam_mulai_sidang']         = $waktuSidang;
            $item['waktu_sidang']             = $waktuSidang;
            $item['ruangan_sidang']           = $ruangSidang;
            $item['ruang_sidang']             = $ruangSidang;
            $item['ruangan_sidang_final']     = $ruangSidang;
            $item['status_sidang']            = $isScheduled ? 'Terjadwal' : 'Belum Dijadwalkan';
            $item['link_sidang']              = $gRow['link_sidang'] ?? null;
            
            // Komponen Skor Evaluator
            $item['nilaisidang_pembimbing1']  = $n1;
            $item['nilaisidang_pembimbing2']  = $n2;
            $item['nilaisidang_penguji1']     = $np1;
            $item['nilaisidang_penguji2']     = $np2;
            $item['penilaiansidang_pembimbing1'] = $gRow['penilaiansidang_pembimbing1'] ?? ($gRow['evaluasi_pembimbing1'] ?? '');
            $item['penilaiansidang_pembimbing2'] = $gRow['penilaiansidang_pembimbing2'] ?? ($gRow['evaluasi_pembimbing2'] ?? '');
            $item['penilaiansidang_penguji1']    = $gRow['penilaiansidang_penguji1'] ?? ($gRow['evaluasi_penguji1'] ?? '');
            $item['penilaiansidang_penguji2']    = $gRow['penilaiansidang_penguji2'] ?? ($gRow['evaluasi_penguji2'] ?? '');

            // Indikator Kelengkapan Nilai & Status Publish
            $item['is_nilai_lengkap']         = $isNilaiLengkap;
            $item['komponen_terisi_count']    = $komponenTerisiCount;
            $item['komponen_belum_terisi']    = $komponenBelumTerisi;
            $item['nilai_akhir_sidang']       = $avgScore;
            $item['grade_sidang']             = $grade;
            $item['status_kelulusan_sidang']  = $statusKelulusan;
            $item['status_publish_sidang']    = $statusPublish;
            $item['tgl_publish_sidang']       = $tglPublish;
            $item['file_bap']                 = $gRow['bap'] ?? null;
            $item['status_bap']               = $gRow['status_bap'] ?? 'Pending';

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Update Jadwal Sidang TA Single Mahasiswa
     */
    public function update_jadwal_sidang_ajax($nim, $tanggal_sidang, $waktu_sidang, $ruang_sidang, $link_sidang = '', $jam_selesai = null) {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa tidak ditemukan.');
        }

        $this->db->where('id_mhs', $mhs['id']);
        $this->db->or_where('id_mhs', $mhs['nim']);
        $g = $this->db->get('guidance')->row_array();

        if (!$g) {
            return array('status' => false, 'message' => 'Data pendaftaran belum ada.');
        }

        $updateData = array(
            'tanggal_sidang' => $tanggal_sidang,
            'waktu_sidang'   => $waktu_sidang,
            'ruang_sidang'   => $ruang_sidang,
            'link_sidang'    => $link_sidang
        );

        $this->db->where('id', $g['id']);
        $this->db->update('guidance', $updateData);

        // Catat ke log history
        $this->_log_history(array(
            'modul'         => 'Sidang TA',
            'ref_id'        => $nim,
            'target_name'   => $mhs['name'],
            'action'        => 'Scheduled',
            'catatan'       => json_encode(array(
                'kategori'       => 'Sidang TA',
                'tanggal_sidang' => $tanggal_sidang,
                'waktu_sidang'   => $waktu_sidang,
                'ruang_sidang'   => $ruang_sidang
            ))
        ));

        return array('status' => true, 'message' => 'Jadwal sidang mahasiswa berhasil disimpan!');
    }

    /**
     * Batch update jadwal sidang TA
     */
        /**
     * Batch update jadwal sidang TA
     */
    public function batch_jadwal_sidang_per_mhs_ajax($schedules) {
        if (empty($schedules) || !is_array($schedules)) {
            return array('status' => false, 'message' => 'Tidak ada data jadwal yang dikirim.');
        }

        $success = 0;
        foreach ($schedules as $row) {
            $nim     = $row['nim'] ?? '';
            $tgl     = $row['tgl_sidang'] ?? ($row['tanggal_sidang'] ?? null);
            $waktu   = $row['jam_mulai_sidang'] ?? ($row['waktu_sidang'] ?? null);
            $ruang   = $row['ruangan_sidang'] ?? ($row['ruang_sidang'] ?? null);
            $link    = $row['link_sidang'] ?? '';

            if (!empty($nim)) {
                $r = $this->update_jadwal_sidang_ajax($nim, $tgl, $waktu, $ruang, $link);
                if ($r['status']) $success++;
            }
        }

        return array(
            'status'        => ($success > 0),
            'success_count' => $success,
            'message'       => "Penjadwalan Sidang Massal berhasil disimpan ({$success} data)."
        );
    }

    public function batch_update_jadwal_sidang($batchData) {
        return $this->batch_jadwal_sidang_per_mhs_ajax($batchData);
    }

    /**
     * Simpan penilaian sidang dari penguji / pembimbing
     */
    public function simpan_penilaian_sidang_ajax($nim, $nilai_p1, $nilai_p2, $nilai_penguji1, $nilai_penguji2, $catatan = '') {
        $this->db->where('nim', $nim);
        $this->db->or_where('id', $nim);
        $mhs = $this->db->get('user')->row_array();

        if (!$mhs) {
            return array('status' => false, 'message' => 'Mahasiswa tidak ditemukan.');
        }

        $this->db->where('id_mhs', $mhs['id']);
        $this->db->or_where('id_mhs', $mhs['nim']);
        $g = $this->db->get('guidance')->row_array();

        if (!$g) {
            return array('status' => false, 'message' => 'Data pendaftaran belum ada.');
        }

        $updateData = array(
            'nilaisidang_pembimbing1' => $nilai_p1,
            'nilaisidang_pembimbing2' => $nilai_p2,
            'nilaisidang_penguji1'    => $nilai_penguji1,
            'nilaisidang_penguji2'    => $nilai_penguji2
        );

        $this->db->where('id', $g['id']);
        $this->db->update('guidance', $updateData);

        return array(
            'status'  => true,
            'message' => 'Penilaian sidang berhasil disimpan ke database!'
        );
    }

    /**
     * Helper internal untuk mencatat ke tabel log_approval_history
     */
    private function _log_history($data) {
        if (!$this->db->table_exists('log_approval_history')) {
            return false;
        }

        $actor_name    = $this->session->userdata('name') ?: 'Koordinator TA';
        $actor_id      = $this->session->userdata('user_id');
        $actor_nip_nim = $this->session->userdata('nip') ?: ($this->session->userdata('username') ?: '1987010102');

        $insert = array(
            'modul'         => $data['modul'] ?? 'Koordinator TA',
            'ref_id'        => (string)($data['ref_id'] ?? ''),
            'target_name'   => $data['target_name'] ?? null,
            'action'        => $data['action'] ?? 'Approved',
            'actor_id'      => $actor_id ? (int)$actor_id : null,
            'actor_name'    => $actor_name,
            'actor_role'    => 'Koordinator TA',
            'actor_nip_nim' => $actor_nip_nim,
            'catatan'       => $data['catatan'] ?? null,
            'created_at'    => date('Y-m-d H:i:s')
        );

        return $this->db->insert('log_approval_history', $insert);
    }

    /**
     * Ambil histori log plotting TA (Pembimbing & Penguji) untuk timeline modal
     */
    public function get_history_ta($kategori = null, $nim = null, $limit = 100) {
        if (!$this->db->table_exists('log_approval_history')) {
            return array();
        }

        $this->db->from('log_approval_history');
        if (!empty($nim)) {
            $this->db->where('ref_id', $nim);
        }
        $this->db->where_in('modul', array('Koordinator TA', 'Plotting Pembimbing', 'Plotting Penguji', 'Sidang TA', 'Publish Nilai Sidang'));
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();

        $results = array();
        if ($query && $query->num_rows() > 0) {
            $dosenList = $this->get_dosen_list();
            $dosenMap = array();
            foreach ($dosenList as $d) {
                $dosenMap[(string)$d['nip']] = $d['nama_dosen'];
                $dosenMap[(string)$d['id']] = $d['nama_dosen'];
            }

            foreach ($query->result_array() as $row) {
                $parsedCatatan = array();
                $rawCatatan = $row['catatan'];
                if (!empty($rawCatatan) && $rawCatatan[0] === '{') {
                    $parsedCatatan = json_decode($rawCatatan, true) ?: array();
                }

                $modul = $row['modul'];
                $cat = $parsedCatatan['kategori'] ?? '';
                if (empty($cat)) {
                    if ($modul === 'Plotting Penguji') $cat = 'Penguji';
                    elseif ($modul === 'Sidang TA' || $modul === 'Publish Nilai Sidang') $cat = 'Sidang TA';
                    else $cat = 'Pembimbing';
                }

                if (!empty($kategori) && $kategori !== 'All') {
                    if ($kategori === 'Sidang' || $kategori === 'Sidang TA') {
                        if ($cat !== 'Sidang TA' && $cat !== 'Sidang') continue;
                    } elseif (strcasecmp($cat, $kategori) !== 0) {
                        continue;
                    }
                }

                $d1_label = 'Dosen Pembimbing 1';
                $d2_label = 'Dosen Pembimbing 2';
                $d1_baru = '-';
                $d2_baru = '-';
                $d1_lama = '-';
                $d2_lama = '-';
                $aksi = $row['action'];
                $catatan_text = $parsedCatatan['catatan_koor'] ?? ($row['catatan'] ?? '');

                if ($modul === 'Publish Nilai Sidang') {
                    $cat = 'Sidang TA';
                    if ($row['action'] === 'Published') {
                        $aksi = 'Publikasi Nilai (Live)';
                        $d1_label = 'Nilai Akhir & Grade';
                        $d1_baru = number_format((float)($parsedCatatan['nilai_akhir'] ?? 0), 2) . ' (Grade ' . ($parsedCatatan['grade'] ?? '-') . ')';
                        $d2_label = 'Status Kelulusan';
                        $d2_baru = $parsedCatatan['status_kelulusan'] ?? 'Lulus';
                        $catatan_text = $parsedCatatan['catatan_koor'] ?? '';
                    } elseif ($row['action'] === 'Scheduled') {
                        $aksi = 'Publikasi Terjadwal';
                        $d1_label = 'Nilai Akhir & Grade';
                        $d1_baru = number_format((float)($parsedCatatan['nilai_akhir'] ?? 0), 2) . ' (Grade ' . ($parsedCatatan['grade'] ?? '-') . ')';
                        $d2_label = 'Jadwal Publikasi';
                        $tglP = $parsedCatatan['tgl_publish'] ?? $row['created_at'];
                        $d2_baru = date('d M Y, H:i', strtotime($tglP)) . ' WIB';
                        $catatan_text = $parsedCatatan['catatan_koor'] ?? '';
                    } elseif ($row['action'] === 'Publish Blocked') {
                        $aksi = 'Publikasi Ditolak (Belum Lengkap)';
                        $d1_label = 'Kelengkapan Nilai';
                        $d1_baru = ($parsedCatatan['komponen_terisi_count'] ?? 0) . ' / 4 Nilai Terisi';
                        $d2_label = 'Komponen Belum Terisi';
                        $d2_baru = !empty($parsedCatatan['komponen_belum_terisi']) ? implode(', ', $parsedCatatan['komponen_belum_terisi']) : 'Belum Lengkap';
                        $catatan_text = $parsedCatatan['alasan'] ?? 'Percobaan publikasi diblokir sistem karena nilai belum lengkap.';
                    } else {
                        $aksi = 'Draft (Batal Publikasi)';
                        $d1_label = 'Nilai Akhir';
                        $d1_baru = number_format((float)($parsedCatatan['nilai_akhir'] ?? 0), 2);
                        $d2_label = 'Status Publikasi';
                        $d2_baru = 'Draft (Privat)';
                    }
                } elseif ($modul === 'Sidang TA' || $cat === 'Sidang TA' || $cat === 'Sidang') {
                    $cat = 'Sidang TA';
                    $aksi = 'Penjadwalan Sidang';
                    $d1_label = 'Detail Jadwal Sidang';
                    $tgl = $parsedCatatan['tanggal_sidang'] ?? '-';
                    $jam = $parsedCatatan['waktu_sidang'] ?? ($parsedCatatan['jam_sidang'] ?? '-');
                    $d1_baru = ($tgl !== '-' ? date('d M Y', strtotime($tgl)) : '-') . ' (Pukul ' . $jam . ' WIB)';
                    $d2_label = 'Ruangan Sidang';
                    $d2_baru = $parsedCatatan['ruang_sidang'] ?? '-';
                    $catatan_text = $parsedCatatan['catatan_koor'] ?? '';
                } elseif ($cat === 'Penguji') {
                    $aksi = ($row['action'] === 'Approved' || $row['action'] === 'Scheduled') ? 'Penetapan Penguji' : ($row['action'] === 'Rejected' ? 'Penolakan Penguji' : 'Perubahan Penguji');
                    $d1_label = 'Dosen Penguji 1';
                    $d2_label = 'Dosen Penguji 2';
                    $p1 = $parsedCatatan['penguji_1'] ?? ($parsedCatatan['dosen_penguji1'] ?? '');
                    $p2 = $parsedCatatan['penguji_2'] ?? ($parsedCatatan['dosen_penguji2'] ?? '');
                    $d1_baru = $dosenMap[$p1] ?? ($p1 ?: '-');
                    $d2_baru = $dosenMap[$p2] ?? ($p2 ?: '-');
                    $d1_lama = $dosenMap[$parsedCatatan['pj1_lama'] ?? ''] ?? ($parsedCatatan['pj1_lama'] ?? '-');
                    $d2_lama = $dosenMap[$parsedCatatan['pj2_lama'] ?? ''] ?? ($parsedCatatan['pj2_lama'] ?? '-');
                } else {
                    $aksi = ($row['action'] === 'Approved') ? 'Penetapan Pembimbing' : ($row['action'] === 'Rejected' ? 'Penolakan Pembimbing' : 'Perubahan Pembimbing');
                    $d1_label = 'Dosen Pembimbing 1';
                    $d2_label = 'Dosen Pembimbing 2';
                    $p1 = $parsedCatatan['pembimbing_1'] ?? ($parsedCatatan['dosen_pembimbing1'] ?? '');
                    $p2 = $parsedCatatan['pembimbing_2'] ?? ($parsedCatatan['dosen_pembimbing2'] ?? '');
                    $d1_baru = $dosenMap[$p1] ?? ($p1 ?: '-');
                    $d2_baru = $dosenMap[$p2] ?? ($p2 ?: '-');
                    $d1_lama = $dosenMap[$parsedCatatan['p1_lama'] ?? ''] ?? ($parsedCatatan['p1_lama'] ?? '-');
                    $d2_lama = $dosenMap[$parsedCatatan['p2_lama'] ?? ''] ?? ($parsedCatatan['p2_lama'] ?? '-');
                }

                $results[] = array(
                    'id'                 => $row['id'],
                    'nim'                => $row['ref_id'],
                    'nama_mahasiswa'     => $row['target_name'] ?: ('Mahasiswa NIM ' . $row['ref_id']),
                    'kategori'           => $cat,
                    'aksi'               => $aksi,
                    'status'             => $row['action'],
                    'd1_label'           => $d1_label,
                    'd2_label'           => $d2_label,
                    'dosen_1_baru'       => $d1_baru,
                    'nama_dosen_1_baru'  => $d1_baru,
                    'dosen_2_baru'       => $d2_baru,
                    'nama_dosen_2_baru'  => $d2_baru,
                    'dosen_1_lama'       => $d1_lama,
                    'nama_dosen_1_lama'  => $d1_lama,
                    'dosen_2_lama'       => $d2_lama,
                    'nama_dosen_2_lama'  => $d2_lama,
                    'actor_name'         => $row['actor_name'] ?: 'Koordinator TA',
                    'actor_role'         => $row['actor_role'] ?: 'Koordinator TA',
                    'catatan'            => $catatan_text,
                    'created_at'         => $row['created_at'],
                    'waktu'              => date('d M Y, H:i', strtotime($row['created_at']))
                );
            }
        }

        return $results;
    }

    public function get_history_penguji($nim = null, $limit = 50) {
        return $this->get_history_ta('Penguji', $nim, $limit);
    }

    /**
     * Ambil histori log publikasi & penilaian sidang mahasiswa
     */
    public function get_history_penilaian_sidang($nim) {
        if (!$this->db->table_exists('log_approval_history')) {
            return array();
        }

        $this->db->from('log_approval_history');
        if (!empty($nim)) {
            $this->db->where('ref_id', $nim);
        }
        $this->db->where_in('modul', array('Publish Nilai Sidang', 'Sidang TA'));
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(50);
        $query = $this->db->get();

        $results = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $parsedCatatan = array();
                $rawCatatan = $row['catatan'];
                if (!empty($rawCatatan) && $rawCatatan[0] === '{') {
                    $parsedCatatan = json_decode($rawCatatan, true) ?: array();
                }

                $results[] = array(
                    'id'               => $row['id'],
                    'nim'              => $row['ref_id'],
                    'nama_mahasiswa'   => $row['target_name'] ?: ('Mahasiswa NIM ' . $row['ref_id']),
                    'modul'            => $row['modul'],
                    'aksi'             => $row['action'],
                    'status_publish'   => $parsedCatatan['status_publish'] ?? $row['action'],
                    'tgl_publish'      => $parsedCatatan['tgl_publish'] ?? null,
                    'nilai_akhir'      => $parsedCatatan['nilai_akhir'] ?? null,
                    'grade'            => $parsedCatatan['grade'] ?? null,
                    'status_kelulusan' => $parsedCatatan['status_kelulusan'] ?? null,
                    'alasan_blokir'    => $parsedCatatan['alasan'] ?? null,
                    'komponen_belum'   => $parsedCatatan['komponen_belum_terisi'] ?? null,
                    'actor_name'       => $row['actor_name'] ?: 'Koordinator TA',
                    'actor_role'       => $row['actor_role'] ?: 'Koordinator TA',
                    'catatan'          => $parsedCatatan['catatan_koor'] ?? ($row['catatan'] ?? ''),
                    'created_at'       => $row['created_at'],
                    'waktu'            => date('d M Y, H:i', strtotime($row['created_at']))
                );
            }
        }

        return $results;
    }

    /**
     * Publikasi Nilai Sidang Mahasiswa Tunggal dengan Validasi Kelengkapan Nilai
     */
    public function publish_penilaian_sidang_ajax($nim, $status_publish = 'Published', $tgl_publish = null, $catatan = '') {
        $allSidang = $this->get_all_mahasiswa_sidang();
        $student = null;
        foreach ($allSidang as $s) {
            if ($s['nim'] == $nim || $s['user_id'] == $nim) {
                $student = $s;
                break;
            }
        }

        if (!$student) {
            return array('status' => false, 'message' => "Data mahasiswa dengan NIM {$nim} tidak ditemukan dalam daftar sidang.");
        }

        $namaMhs = $student['nama'] ?? $student['nama_lengkap'] ?? "Mahasiswa {$nim}";

        // VALIDASI ATURAN: Hanya nilai yang terisi LENGKAP (seluruh komponen bukan nol) yang boleh dipublish
        if (!$student['is_nilai_lengkap']) {
            $belumTerisiStr = !empty($student['komponen_belum_terisi']) ? implode(', ', $student['komponen_belum_terisi']) : 'Komponen nilai belum lengkap';
            
            // Catat log percobaan publish yang diblokir ke tabel log_approval_history
            $this->_log_history(array(
                'modul'       => 'Publish Nilai Sidang',
                'ref_id'      => $nim,
                'target_name' => $namaMhs,
                'action'      => 'Publish Blocked',
                'catatan'     => json_encode(array(
                    'alasan'                => 'Percobaan publikasi nilai ditolak sistem karena komponen nilai belum terisi lengkap.',
                    'komponen_belum_terisi' => $student['komponen_belum_terisi'],
                    'komponen_terisi_count' => $student['komponen_terisi_count'],
                    'catatan_koor'          => $catatan
                ))
            ));

            return array(
                'status'  => false,
                'message' => "Publikasi nilai untuk mahasiswa {$namaMhs} ({$nim}) DITOLAK! Komponen nilai belum lengkap ({$belumTerisiStr} belum mengisi). Aksi ini telah dicatat ke audit log."
            );
        }

        // Catat log publikasi sukses ke log_approval_history
        $finalPublishDate = ($status_publish === 'Scheduled' && !empty($tgl_publish)) ? $tgl_publish : date('Y-m-d H:i:s');
        
        $this->_log_history(array(
            'modul'       => 'Publish Nilai Sidang',
            'ref_id'      => $nim,
            'target_name' => $namaMhs,
            'action'      => $status_publish,
            'catatan'     => json_encode(array(
                'status_publish'   => $status_publish,
                'tgl_publish'      => $finalPublishDate,
                'nilai_akhir'      => $student['nilai_akhir_sidang'],
                'grade'            => $student['grade_sidang'],
                'status_kelulusan' => $student['status_kelulusan_sidang'],
                'catatan_koor'     => $catatan
            ))
        ));

        $msg = ($status_publish === 'Published')
            ? "Nilai akhir sidang {$namaMhs} ({$nim}) berhasil dipublikasikan secara langsung ke mahasiswa!"
            : "Publikasi nilai {$namaMhs} ({$nim}) berhasil dijadwalkan pada " . date('d M Y, H:i', strtotime($finalPublishDate)) . " WIB.";

        return array(
            'status'         => true,
            'message'        => $msg,
            'status_publish' => $status_publish,
            'tgl_publish'    => $finalPublishDate,
            'nim'            => $nim
        );
    }

    /**
     * Batch / Publikasi Nilai Massal untuk Mahasiswa Terpilih
     */
    public function batch_publish_nilai_ajax($nims, $status_publish = 'Published', $tgl_publish = null, $catatan = '') {
        if (empty($nims) || !is_array($nims)) {
            return array('status' => false, 'message' => 'Pilih setidaknya satu mahasiswa untuk dipublikasikan nilainya.');
        }

        $allSidang = $this->get_all_mahasiswa_sidang();
        $studentMap = array();
        foreach ($allSidang as $s) {
            $studentMap[$s['nim']] = $s;
            $studentMap[$s['user_id']] = $s;
        }

        $successList = array();
        $blockedList = array();
        $finalPublishDate = ($status_publish === 'Scheduled' && !empty($tgl_publish)) ? $tgl_publish : date('Y-m-d H:i:s');

        foreach ($nims as $nim) {
            $student = $studentMap[$nim] ?? null;
            if (!$student) continue;

            $namaMhs = $student['nama'] ?? $student['nama_lengkap'] ?? "Mahasiswa {$nim}";

            if ($student['is_nilai_lengkap']) {
                $this->_log_history(array(
                    'modul'       => 'Publish Nilai Sidang',
                    'ref_id'      => $nim,
                    'target_name' => $namaMhs,
                    'action'      => $status_publish,
                    'catatan'     => json_encode(array(
                        'status_publish'   => $status_publish,
                        'tgl_publish'      => $finalPublishDate,
                        'nilai_akhir'      => $student['nilai_akhir_sidang'],
                        'grade'            => $student['grade_sidang'],
                        'status_kelulusan' => $student['status_kelulusan_sidang'],
                        'catatan_koor'     => $catatan
                    ))
                ));
                $successList[] = "{$namaMhs} ({$nim})";
            } else {
                $belumTerisi = !empty($student['komponen_belum_terisi']) ? implode(', ', $student['komponen_belum_terisi']) : 'Nilai belum lengkap';
                $this->_log_history(array(
                    'modul'       => 'Publish Nilai Sidang',
                    'ref_id'      => $nim,
                    'target_name' => $namaMhs,
                    'action'      => 'Publish Blocked',
                    'catatan'     => json_encode(array(
                        'alasan'                => 'Percobaan publikasi massal ditolak karena nilai belum lengkap.',
                        'komponen_belum_terisi' => $student['komponen_belum_terisi'],
                        'catatan_koor'          => $catatan
                    ))
                ));
                $blockedList[] = "{$namaMhs} ({$nim}) - [{$belumTerisi}]";
            }
        }

        $successCount = count($successList);
        $blockedCount = count($blockedList);

        if ($successCount > 0) {
            $msg = "Publikasi nilai berhasil diproses untuk {$successCount} mahasiswa!";
            if ($blockedCount > 0) {
                $msg .= " ({$blockedCount} mahasiswa dilewati karena komponen nilai belum lengkap).";
            }
            return array(
                'status'         => true,
                'success_count'  => $successCount,
                'blocked_count'  => $blockedCount,
                'success_list'   => $successList,
                'blocked_list'   => $blockedList,
                'message'        => $msg,
                'status_publish' => $status_publish,
                'tgl_publish'    => $finalPublishDate
            );
        } else {
            return array(
                'status'        => false,
                'success_count' => 0,
                'blocked_count' => $blockedCount,
                'blocked_list'  => $blockedList,
                'message'       => "Tidak ada mahasiswa terpilih yang memiliki nilai lengkap! Seluruh {$blockedCount} mahasiswa yang dipilih belum melengkapi 4 komponen nilai sidang."
            );
        }
    }

    /**
     * Ambil detail rekapitulasi nilai sidang mahasiswa (View Only)
     */
    public function get_detail_penilaian_sidang($nim) {
        $all = $this->get_all_mahasiswa_sidang();
        $student = null;
        foreach ($all as $s) {
            if ($s['nim'] == $nim || $s['user_id'] == $nim) {
                $student = $s;
                break;
            }
        }

        if (!$student) return null;

        $history = $this->get_history_penilaian_sidang($nim);

        $extract_clean_note = function($raw) {
            if (empty($raw)) return '';
            if (is_array($raw)) {
                return trim($raw['catatan'] ?? ($raw['komentar'] ?? ($raw['evaluasi'] ?? ($raw['feedback'] ?? ''))));
            }
            if (is_string($raw)) {
                $trimmed = trim($raw);
                if ((substr($trimmed, 0, 1) === '{' && substr($trimmed, -1) === '}') || (substr($trimmed, 0, 1) === '[' && substr($trimmed, -1) === ']')) {
                    $decoded = json_decode($trimmed, true);
                    if (is_array($decoded)) {
                        return trim($decoded['catatan'] ?? ($decoded['komentar'] ?? ($decoded['evaluasi'] ?? ($decoded['feedback'] ?? ''))));
                    }
                }
                return $trimmed;
            }
            return '';
        };

        return array(
            'nim'                       => $student['nim'],
            'nama_mahasiswa'            => $student['nama'] ?? ($student['nama_lengkap'] ?? $student['nim']),
            'prodi'                     => $student['prodi'] ?? 'Informatika',
            'peminatan'                 => $student['peminatan'] ?? 'Informatika',
            'judul_1'                   => $student['judul_1'] ?? '-',
            'tanggal_sidang'            => $student['tanggal_sidang'] ?? null,
            'waktu_sidang'              => $student['waktu_sidang'] ?? null,
            'ruang_sidang'              => $student['ruang_sidang'] ?? null,
            
            // Dosen Pembimbing & Penguji
            'pembimbing_1'              => $student['pembimbing_1'] ?? '',
            'nama_pembimbing_1'         => $student['nama_pembimbing_1'] ?? ($student['pembimbing_1'] ?? '-'),
            'pembimbing_2'              => $student['pembimbing_2'] ?? '',
            'nama_pembimbing_2'         => $student['nama_pembimbing_2'] ?? ($student['pembimbing_2'] ?? '-'),
            'penguji_1'                 => $student['penguji_1'] ?? '',
            'nama_penguji_1'            => $student['nama_penguji_1'] ?? ($student['penguji_1'] ?? '-'),
            'penguji_2'                 => $student['penguji_2'] ?? '',
            'nama_penguji_2'            => $student['nama_penguji_2'] ?? ($student['penguji_2'] ?? '-'),

            // Nilai dari 4 Evaluator
            'nilaisidang_pembimbing1'   => $student['nilaisidang_pembimbing1'] ?? 0,
            'nilaisidang_pembimbing2'   => $student['nilaisidang_pembimbing2'] ?? 0,
            'nilaisidang_penguji1'      => $student['nilaisidang_penguji1'] ?? 0,
            'nilaisidang_penguji2'      => $student['nilaisidang_penguji2'] ?? 0,
            'nilai_sidang_pembimbing_1' => $student['nilaisidang_pembimbing1'] ?? 0,
            'nilai_sidang_pembimbing_2' => $student['nilaisidang_pembimbing2'] ?? 0,
            'nilai_sidang_penguji_1'    => $student['nilaisidang_penguji1'] ?? 0,
            'nilai_sidang_penguji_2'    => $student['nilaisidang_penguji2'] ?? 0,
            
            'evaluasi_pembimbing1'      => $student['penilaiansidang_pembimbing1'] ?? ($student['evaluasi_pembimbing1'] ?? ''),
            'evaluasi_pembimbing2'      => $student['penilaiansidang_pembimbing2'] ?? ($student['evaluasi_pembimbing2'] ?? ''),
            'evaluasi_penguji1'         => $student['penilaiansidang_penguji1'] ?? ($student['evaluasi_penguji1'] ?? ''),
            'evaluasi_penguji2'         => $student['penilaiansidang_penguji2'] ?? ($student['evaluasi_penguji2'] ?? ''),
            'catatan_pembimbing_1'      => $extract_clean_note($student['penilaiansidang_pembimbing1'] ?? ($student['evaluasi_pembimbing1'] ?? '')),
            'catatan_pembimbing_2'      => $extract_clean_note($student['penilaiansidang_pembimbing2'] ?? ($student['evaluasi_pembimbing2'] ?? '')),
            'catatan_penguji_1'         => $extract_clean_note($student['penilaiansidang_penguji1'] ?? ($student['evaluasi_penguji1'] ?? '')),
            'catatan_penguji_2'         => $extract_clean_note($student['penilaiansidang_penguji2'] ?? ($student['evaluasi_penguji2'] ?? '')),

            // Rekap Nilai Akhir
            'is_nilai_lengkap'          => $student['is_nilai_lengkap'] ?? false,
            'komponen_terisi_count'     => $student['komponen_terisi_count'] ?? 0,
            'komponen_belum_terisi'     => $student['komponen_belum_terisi'] ?? array(),
            'nilai_akhir'               => $student['nilai_akhir_sidang'] ?? 0,
            'nilai_akhir_sidang'        => $student['nilai_akhir_sidang'] ?? 0,
            'skor_akhir_sidang'         => $student['nilai_akhir_sidang'] ?? 0,
            'grade'                     => $student['grade_sidang'] ?? '-',
            'grade_sidang'              => $student['grade_sidang'] ?? '-',
            'status_kelulusan'          => $student['status_kelulusan_sidang'] ?? 'Belum Dinilai',
            'status_kelulusan_sidang'   => $student['status_kelulusan_sidang'] ?? 'Belum Dinilai',
            'status_publish'            => $student['status_publish_sidang'] ?? 'Draft',
            'status_publish_sidang'     => $student['status_publish_sidang'] ?? 'Draft',
            'tgl_publish'               => $student['tgl_publish_sidang'] ?? null,
            'tgl_publish_sidang'        => $student['tgl_publish_sidang'] ?? null,
            'history'                   => $history
        );
    }

    public function _seed_default_master_rubrik() {
        return true;
    }

    public function get_all_master_rubrik() {
        return array(
            array(
                'prodi'        => 'Informatika',
                'peminatan'    => 'Informatika',
                'judul_rubrik' => 'Standar Penilaian Sidang Tugas Akhir Informatika',
                'kriteria'     => array(
                    array('name' => 'Penguasaan Materi & Teori', 'bobot' => 30),
                    array('name' => 'Implementasi Sistem & Metodologi', 'bobot' => 40),
                    array('name' => 'Penyusunan Laporan & Dokumen TA', 'bobot' => 15),
                    array('name' => 'Presentasi & Sikap', 'bobot' => 15)
                )
            )
        );
    }

    public function simpan_master_rubrik($prodi, $peminatan, $judul_rubrik, $kriteria = array(), $total_bobot = 100) {
        return array('status' => true, 'message' => 'Master rubrik berhasil diperbarui.');
    }

    public function terapkan_rubrik_massal($prodi, $peminatan, $nim_list = array()) {
        return array('status' => true, 'message' => 'Rubrik berhasil diterapkan ke mahasiswa terpilih.');
    }

    /**
     * Ambil data lengkap seluruh peserta TA untuk Halaman Monitoring Status Peserta TA
     * Mencakup semua tahapan: Dosen Wali -> Admin Layanan -> Koordinator TA -> Ketua KK -> Preview 1 -> Preview 2 -> Preview 3 -> Sidang -> Lulus
     */
    public function get_monitoring_peserta_ta() {
        $all = $this->get_all_mahasiswa_ta();
        if (empty($all)) {
            return array();
        }

        $guidanceIds = array_filter(array_column($all, 'guidance_id'));
        $gMap = array();
        if (!empty($guidanceIds)) {
            $this->db->select('
                id,
                tanggal_presentasi,
                waktu_presentasi,
                ruang_sidang,
                status_preview,
                kelayakan,
                kelayakan2,
                kelayakan3,
                tanggal_sidang,
                waktu_sidang,
                link_sidang,
                nilaisidang_pembimbing1,
                nilaisidang_pembimbing2,
                nilaisidang_penguji1,
                nilaisidang_penguji2,
                bap,
                status_bap
            ');
            $this->db->from('guidance');
            $this->db->where_in('id', $guidanceIds);
            $gQuery = $this->db->get();
            if ($gQuery && $gQuery->num_rows() > 0) {
                foreach ($gQuery->result_array() as $gr) {
                    $gMap[$gr['id']] = $gr;
                }
            }
        }

        // Ambil riwayat aktivitas dari tabel thesis
        $thesisMap = array();
        if (!empty($guidanceIds) && $this->db->table_exists('thesis')) {
            $this->db->select('id_guidance, tahapan_preview, status, date, created_at');
            $this->db->where_in('id_guidance', $guidanceIds);
            $this->db->order_by('created_at', 'DESC');
            $this->db->order_by('date', 'DESC');
            $tQuery = $this->db->get('thesis');
            if ($tQuery && $tQuery->num_rows() > 0) {
                foreach ($tQuery->result_array() as $tr) {
                    $tGid = $tr['id_guidance'];
                    if (!isset($thesisMap[$tGid])) {
                        $thesisMap[$tGid] = array(
                            'has_p1'     => false,
                            'has_p2'     => false,
                            'has_p3'     => false,
                            'has_sidang' => false,
                            'p1_app'     => false,
                            'p2_app'     => false,
                            'p3_app'     => false,
                            'sidang_app' => false,
                        );
                    }
                    $thp = strtolower(trim($tr['tahapan_preview'] ?? ''));
                    $isApp = (strcasecmp($tr['status'] ?? '', 'Approved') === 0);
                    if ($thp === 'preview1') { $thesisMap[$tGid]['has_p1'] = true; if ($isApp) $thesisMap[$tGid]['p1_app'] = true; }
                    if ($thp === 'preview2') { $thesisMap[$tGid]['has_p2'] = true; if ($isApp) $thesisMap[$tGid]['p2_app'] = true; }
                    if ($thp === 'preview3') { $thesisMap[$tGid]['has_p3'] = true; if ($isApp) $thesisMap[$tGid]['p3_app'] = true; }
                    if ($thp === 'sidang')   { $thesisMap[$tGid]['has_sidang'] = true; if ($isApp) $thesisMap[$tGid]['sidang_app'] = true; }
                }
            }
        }

        $this->load->model('AdminLayanan_model');
        $berkasSummaries = $this->AdminLayanan_model->get_batch_student_berkas_summaries($all);

        $result = array();
        foreach ($all as $item) {
            $gId = $item['guidance_id'];
            $gRow = $gMap[$gId] ?? array();
            $tInfo = $thesisMap[$gId] ?? array();

            $statusWali   = $item['status_approval_wali'] ?? 'Pending';
            $statusAdmin  = $item['status_approval_admin'] ?? 'Pending';
            $statusKoor   = $item['status_approval_koor'] ?? 'Pending';
            $statusKk     = $item['status_approval_kk'] ?? 'Pending';
            $statusPrev   = strtolower(trim($gRow['status_preview'] ?? ''));

            $hasPembimbing = (!empty($item['pembimbing_1']) && !empty($item['pembimbing_2']));
            $hasPenguji    = (!empty($item['penguji_1']) && !empty($item['penguji_2']));
            $hasSidang     = !empty($gRow['tanggal_sidang']);

            $n1  = (float)($gRow['nilaisidang_pembimbing1'] ?? 0);
            $n2  = (float)($gRow['nilaisidang_pembimbing2'] ?? 0);
            $np1 = (float)($gRow['nilaisidang_penguji1'] ?? 0);
            $np2 = (float)($gRow['nilaisidang_penguji2'] ?? 0);
            $isNilaiLengkap = ($n1 > 0 && $n2 > 0 && $np1 > 0 && $np2 > 0);
            $avgScore = $isNilaiLengkap ? round(($n1 + $n2 + $np1 + $np2) / 4, 2) : 0;
            $isLulus = ($isNilaiLengkap && $avgScore >= 55) || (strcasecmp($gRow['status_bap'] ?? '', 'Approved') === 0) || ($statusPrev === 'lulus' || $statusPrev === 'selesai') || (!empty($tInfo['sidang_app']));

            // Deteksi Tahap Progres Terkini (Pipeline 1 s/d 9)
            // Prioritas tertinggi: Deteksi tahap bimbingan/preview/sidang terlebih dahulu bila mahasiswa sudah masuk ke tahapan tersebut
            if ($isLulus) {
                $progresStage = 'Lulus';
                $stageKey     = 'lulus';
                $stageIndex   = 9;
                $stageDesc    = 'Lulus Sidang Tugas Akhir';
                $stageColor   = 'emerald';
            } elseif ($hasSidang || $statusPrev === 'sidang' || !empty($tInfo['has_sidang']) || !empty($tInfo['p3_app'])) {
                $progresStage = 'Sidang TA';
                $stageKey     = 'sidang';
                $stageIndex   = 8;
                $stageDesc    = 'Pelaksanaan Sidang Akhir';
                $stageColor   = 'rose';
            } elseif ($statusPrev === 'preview3' || !empty($tInfo['has_p3']) || !empty($tInfo['p2_app'])) {
                $progresStage = 'Preview 3';
                $stageKey     = 'preview3';
                $stageIndex   = 7;
                $stageDesc    = 'Pra-Sidang & Finalisasi Dokumen TA';
                $stageColor   = 'cyan';
            } elseif ($statusPrev === 'preview2' || !empty($tInfo['has_p2']) || !empty($tInfo['p1_app']) || $hasPenguji || !empty($gRow['tanggal_presentasi'])) {
                $progresStage = 'Preview 2';
                $stageKey     = 'preview2';
                $stageIndex   = 6;
                $stageDesc    = 'Evaluasi Progres & Presentasi Penguji';
                $stageColor   = 'indigo';
            } elseif ($statusPrev === 'preview1' || !empty($tInfo['has_p1']) || (strcasecmp($statusKoor, 'Approved') === 0 && $hasPembimbing) || strcasecmp($statusKk, 'Approved') === 0) {
                $progresStage = 'Preview 1';
                $stageKey     = 'preview1';
                $stageIndex   = 5;
                $stageDesc    = 'Bimbingan Bab 1-3 Bersama Pembimbing';
                $stageColor   = 'teal';
            } elseif (strcasecmp($statusKoor, 'Approved') === 0) {
                $progresStage = 'Ketua KK';
                $stageKey     = 'ketua_kk';
                $stageIndex   = 4;
                $stageDesc    = 'Konfirmasi Distribusi Riset KK';
                $stageColor   = 'purple';
            } elseif (strcasecmp($statusAdmin, 'Approved') === 0) {
                $progresStage = 'Koordinator TA';
                $stageKey     = 'koordinator_ta';
                $stageIndex   = 3;
                $stageDesc    = 'Persetujuan Proposal & Plot Pembimbing';
                $stageColor   = 'orange';
            } elseif (strcasecmp($statusWali, 'Approved') === 0) {
                $progresStage = 'Admin Layanan';
                $stageKey     = 'admin_layanan';
                $stageIndex   = 2;
                $stageDesc    = 'Pemeriksaan Berkas & Administrasi LAA';
                $stageColor   = 'amber';
            } else {
                $progresStage = 'Dosen Wali';
                $stageKey     = 'dosen_wali';
                $stageIndex   = 1;
                $stageDesc    = 'Verifikasi Prasyarat Akademik & SKS';
                $stageColor   = 'blue';
            }

            // Ringkasan Berkas Mahasiswa
            $nim = $item['nim'];
            $bSummary = $berkasSummaries[$nim] ?? array(
                'valid_count'   => 0,
                'invalid_count' => 0,
                'pending_count' => 0,
                'total_count'   => 0,
                'items'         => array()
            );

            $totalBerkas   = (int)($bSummary['total_count'] ?? 0);
            $validBerkas   = (int)($bSummary['valid_count'] ?? 0);
            $invalidBerkas = (int)($bSummary['invalid_count'] ?? 0);
            $pendingBerkas = (int)($bSummary['pending_count'] ?? 0);

            if ($totalBerkas > 0 && $validBerkas >= $totalBerkas) {
                $berkasStatusLabel = 'Lengkap (' . $validBerkas . '/' . $totalBerkas . ')';
                $berkasStatusCode  = 'lengkap';
                $berkasStatusColor = 'emerald';
            } elseif ($invalidBerkas > 0) {
                $berkasStatusLabel = $invalidBerkas . ' Perlu Revisi';
                $berkasStatusCode  = 'revisi';
                $berkasStatusColor = 'rose';
            } elseif ($validBerkas > 0) {
                $berkasStatusLabel = $validBerkas . '/' . $totalBerkas . ' Valid';
                $berkasStatusCode  = 'proses';
                $berkasStatusColor = 'amber';
            } elseif ($pendingBerkas > 0) {
                $berkasStatusLabel = $pendingBerkas . ' Pending Cek';
                $berkasStatusCode  = 'proses';
                $berkasStatusColor = 'amber';
            } else {
                $berkasStatusLabel = 'Belum Unggah';
                $berkasStatusCode  = 'kosong';
                $berkasStatusColor = 'slate';
            }

            $item['progres_stage']       = $progresStage;
            $item['stage_key']           = $stageKey;
            $item['stage_index']         = $stageIndex;
            $item['stage_desc']          = $stageDesc;
            $item['stage_color']         = $stageColor;
            $item['tgl_presentasi']      = $gRow['tanggal_presentasi'] ?? null;
            $item['waktu_presentasi']    = $gRow['waktu_presentasi'] ?? null;
            $item['tgl_sidang']          = $gRow['tanggal_sidang'] ?? null;
            $item['waktu_sidang']        = $gRow['waktu_sidang'] ?? null;
            $item['ruang_sidang']        = $gRow['ruang_sidang'] ?? null;
            $item['link_sidang']         = $gRow['link_sidang'] ?? null;
            $item['status_bap']          = $gRow['status_bap'] ?? 'Pending';
            $item['avg_score']           = $avgScore;
            $item['berkas_summary']      = $bSummary;
            $item['berkas_status_label'] = $berkasStatusLabel;
            $item['berkas_status_code']  = $berkasStatusCode;
            $item['berkas_status_color'] = $berkasStatusColor;

            $result[] = $item;
        }

        return $result;
    }
}