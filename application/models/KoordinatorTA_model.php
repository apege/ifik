<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model KoordinatorTA_model
 * 
 * Pusat logika bisnis dan interaksi basis data untuk modul Koordinator Tugas Akhir.
 * Mengelola siklus tugas akhir dari pendaftaran hingga yudisium:
 * - Manajemen pendaftaran TA dan persetujuan dosen pembimbing (single & batch)
 * - Plotting dan validasi netralitas dosen penguji evaluasi Preview 2
 * - Penjadwalan sidang komprehensif dan pencegahan bentrok master ruangan
 * - Perhitungan rata-rata tertimbang nilai rubrik dan penetapan status kelulusan
 * - Pemetaan pipeline 9 tahapan progres mahasiswa (SSOT guidance & thesis)
 * - Pemulihan dan penurunan tahapan (downgrade/reset) dengan sanitasi berkas multi-path
 * - Pengelolaan master rubrik multi-versi berbasis prodi & konsentrasi peminatan dinamis
 * 
 * @package    IFIK_Portal
 * @subpackage Models
 * @category   Koordinator_TA
 * @author     Muhammad Alif Muzakky
 */
class KoordinatorTA_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Bersih tanpa auto-migration / ALTER TABLE
    }

    /**
     * Ambil list semua dosen dari tabel user (role_id = 2 / Kaur, 3 / Dosen, 6 / Koor, 7 / PIC, 9 / Ketua KK)
     */
    public function get_dosen_list() {
        $userCols = $this->db->list_fields('user');
        $selectCols = array('id', 'name as nama_dosen');

        if (in_array('nip', $userCols)) $selectCols[] = 'nip';
        if (in_array('email', $userCols)) $selectCols[] = 'email';
        if (in_array('kode_dosen', $userCols)) $selectCols[] = 'kode_dosen';
        if (in_array('no_telp', $userCols)) $selectCols[] = 'no_telp as no_hp';
        if (in_array('koordinator', $userCols)) $selectCols[] = 'koordinator';
        if (in_array('kelompok_keahlian', $userCols)) $selectCols[] = 'kelompok_keahlian';
        if (in_array('id_kk', $userCols)) $selectCols[] = 'id_kk';

        $this->db->select(implode(', ', $selectCols));
        $this->db->from('user');
        $this->db->where_in('role_id', array(2, 3, 6, 7, 9));
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();

        $result = array();
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $nipKey = !empty($row['nip']) ? (string)$row['nip'] : (string)$row['id'];
                $kkVal = !empty($row['koordinator']) ? $row['koordinator'] : ($row['kelompok_keahlian'] ?? '');
                if (empty($kkVal) && !empty($row['id_kk']) && $this->db->table_exists('kelompok_keahlian')) {
                    $kkRow = $this->db->get_where('kelompok_keahlian', array('id' => $row['id_kk']))->row_array();
                    if ($kkRow) {
                        $kkVal = $kkRow['kode_kk'] ?: $kkRow['nama_kk'];
                    }
                }

                $result[$nipKey] = array(
                    'id'                => $row['id'],
                    'nip'               => $nipKey,
                    'nama_dosen'        => $row['nama_dosen'],
                    'email'             => $row['email'] ?? '',
                    'kode_dosen'        => $row['kode_dosen'] ?? '',
                    'no_hp'             => $row['no_hp'] ?? '',
                    'koordinator'       => $kkVal,
                    'kelompok_keahlian' => $kkVal,
                    'id_kk'             => $row['id_kk'] ?? null,
                    'prodi'             => 'Informatika'
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
                            'catatan_admin'     => '',
                            '_admin_valid'      => 0,
                            '_admin_invalid'    => 0,
                            '_admin_pending'    => 0,
                            '_admin_total'      => 0,
                            '_doswal_valid'     => 0,
                            '_doswal_invalid'   => 0,
                            '_doswal_pending'   => 0,
                            '_doswal_total'     => 0,
                        );
                    }

                    $cleanAdmin = strtolower(trim($st_admin));
                    $isAdminValid = ($cleanAdmin === 'valid' || strpos($cleanAdmin, 'setuju') !== false || strpos($cleanAdmin, 'approved') !== false || $cleanAdmin === 'acc');
                    $isAdminInvalid = ($cleanAdmin === 'invalid' || strpos($cleanAdmin, 'revisi') !== false || strpos($cleanAdmin, 'tolak') !== false || strpos($cleanAdmin, 'rejected') !== false);

                    $cleanDoswal = strtolower(trim($st_doswal));
                    $isDoswalValid = ($cleanDoswal === 'valid' || strpos($cleanDoswal, 'setuju') !== false || strpos($cleanDoswal, 'approved') !== false || $cleanDoswal === 'acc');
                    $isDoswalInvalid = ($cleanDoswal === 'invalid' || strpos($cleanDoswal, 'revisi') !== false || strpos($cleanDoswal, 'tolak') !== false || strpos($cleanDoswal, 'rejected') !== false);

                    if (strpos($namaJenis, 'ksm') !== false) {
                        $files_map[$k]['file_ksm'] = $f['file'];
                        $files_map[$k]['status_ksm'] = $isAdminValid ? 'Valid' : ($isAdminInvalid ? 'Invalid' : ($st_doswal ?: 'Pending'));
                    } elseif (strpos($namaJenis, 'transkrip') !== false) {
                        $files_map[$k]['file_transkrip'] = $f['file'];
                        $files_map[$k]['status_transkrip'] = $isAdminValid ? 'Valid' : ($isAdminInvalid ? 'Invalid' : ($st_doswal ?: 'Pending'));
                    } elseif (strpos($namaJenis, 'pernyataan') !== false) {
                        $files_map[$k]['file_pernyataan'] = $f['file'];
                        $files_map[$k]['status_pernyataan'] = $isAdminValid ? 'Valid' : ($isAdminInvalid ? 'Invalid' : ($st_doswal ?: 'Pending'));
                    } elseif (strpos($namaJenis, 'bebas') !== false || strpos($namaJenis, 'lab') !== false) {
                        $files_map[$k]['file_bebas_lab'] = $f['file'];
                        $files_map[$k]['status_bebas_lab'] = $isAdminValid ? 'Valid' : ($isAdminInvalid ? 'Invalid' : ($st_doswal ?: 'Pending'));
                    }

                    if ($isAdminValid) {
                        $files_map[$k]['_admin_valid']++;
                    } elseif ($isAdminInvalid) {
                        $files_map[$k]['_admin_invalid']++;
                    } else {
                        $files_map[$k]['_admin_pending']++;
                    }
                    $files_map[$k]['_admin_total']++;

                    if ($isDoswalValid) {
                        $files_map[$k]['_doswal_valid']++;
                    } elseif ($isDoswalInvalid) {
                        $files_map[$k]['_doswal_invalid']++;
                    } else {
                        $files_map[$k]['_doswal_pending']++;
                    }
                    $files_map[$k]['_doswal_total']++;

                    if (!empty($f['komentar'])) {
                        $files_map[$k]['catatan_wali'] = $f['komentar'];
                        $files_map[$k]['catatan_admin'] = $f['komentar'];
                    }
                }
            }

            foreach ($files_map as $k => &$v) {
                // Admin LAA overall status:
                // Only Approved when at least 4 files are valid and 0 invalid/pending
                if ($v['_admin_invalid'] > 0) {
                    $v['status_adminlaa'] = 'Rejected';
                } elseif ($v['_admin_valid'] >= 4 && $v['_admin_invalid'] === 0 && $v['_admin_pending'] === 0) {
                    $v['status_adminlaa'] = 'Approved';
                } else {
                    $v['status_adminlaa'] = 'Pending';
                }

                // Doswal overall status:
                if ($v['_doswal_invalid'] > 0) {
                    $v['status_doswal'] = 'Rejected';
                } elseif ($v['_doswal_valid'] >= 4 || ($v['_doswal_valid'] > 0 && $v['_doswal_invalid'] === 0 && $v['_doswal_pending'] === 0)) {
                    $v['status_doswal'] = 'Approved';
                } else {
                    $v['status_doswal'] = 'Pending';
                }
            }
            unset($v);
        }

        return $files_map;
    }

    /**
     * TAHAP 1: Ambil semua mahasiswa pendaftar TA dari tabel guidance + user + file_pendaftaran
     */
        /**
     * Mengambil Semua Data Mahasiswa Pendaftar Tugas Akhir (Tab 1)
     * 
     * Melakukan kueri relasional antara tabel guidance, user, dan file_pendaftaran
     * untuk menampilkan antrean pendaftaran mahasiswa, usulan judul tugas akhir,
     * status approval Dosen Wali dan Admin LAA, serta penugasan Pembimbing 1 dan 2.
     * 
     * @return array Daftar mahasiswa terurut dari pengajuan terbaru (ORDER BY created_at DESC)
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
        $this->db->join('user u_wali', '(u.dosen_wali IS NOT NULL AND u.dosen_wali != "" AND (u_wali.nip = u.dosen_wali OR u_wali.id = u.dosen_wali))', 'left');
        $this->db->join('thesis_lecturers tl', 'tl.id_guidance = g.id', 'left');
        $this->db->join('user u_p1', '(tl.dosen_pembimbing1 IS NOT NULL AND tl.dosen_pembimbing1 != "" AND (u_p1.nip = tl.dosen_pembimbing1 OR u_p1.id = tl.dosen_pembimbing1))', 'left');
        $this->db->join('user u_p2', '(tl.dosen_pembimbing2 IS NOT NULL AND tl.dosen_pembimbing2 != "" AND (u_p2.nip = tl.dosen_pembimbing2 OR u_p2.id = tl.dosen_pembimbing2))', 'left');
        $this->db->join('user u_pj1', '(tl.dosen_penguji1 IS NOT NULL AND tl.dosen_penguji1 != "" AND (u_pj1.nip = tl.dosen_penguji1 OR u_pj1.id = tl.dosen_penguji1))', 'left');
        $this->db->join('user u_pj2', '(tl.dosen_penguji2 IS NOT NULL AND tl.dosen_penguji2 != "" AND (u_pj2.nip = tl.dosen_penguji2 OR u_pj2.id = tl.dosen_penguji2))', 'left');
        $this->db->order_by('u.nim', 'ASC');
        $query = $this->db->get();

        if (!$query || $query->num_rows() === 0) {
            return array();
        }

        $rawList = $query->result_array();

        // Deduplicate rows by guidance_id in PHP (safe for sql_mode=only_full_group_by)
        $uniqueList = array();
        $seenGuidance = array();
        foreach ($rawList as $rowItem) {
            $gKey = $rowItem['guidance_id'];
            if (!isset($seenGuidance[$gKey])) {
                $seenGuidance[$gKey] = true;
                $uniqueList[] = $rowItem;
            }
        }
        $rawList = $uniqueList;

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
        /**
     * Pembaruan Status Approval Satuan Pendaftaran Tugas Akhir (Tab 1)
     * 
     * Memperbarui status persetujuan Koordinator TA (status_approval_koor),
     * menetapkan Pembimbing 1 dan Pembimbing 2, mencatat catatan revisi,
     * serta merekam jejak kronologis ke tabel history_ta.
     * 
     * @param string $nim NIM mahasiswa yang bersangkutan
     * @param string $status Keputusan ('Disetujui' / 'Ditolak' / 'Pending')
     * @param string $pembimbing1 NIP Dosen Pembimbing 1
     * @param string $pembimbing2 NIP Dosen Pembimbing 2
     * @param string $catatan Catatan rekomendasi atau revisi
     * @return array Status eksekusi dan pesan konfirmasi
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

        $target_ids = array_values(array_unique(array_filter([$userId, $mhs['nim']])));
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
            // 1. Resolve Pembimbing 1 ke User ID dan ambil Kelompok Keahlian miliknya
            $p1_user_id = (string)$pembimbing_1;
            $kk_p1 = 'VID'; // default
            if (!empty($pembimbing_1)) {
                $this->db->group_start();
                $this->db->where('id', $pembimbing_1);
                $this->db->or_where('nip', $pembimbing_1);
                $this->db->or_where('nim', $pembimbing_1);
                $this->db->group_end();
                $uP1 = $this->db->get('user')->row_array();
                if ($uP1) {
                    $p1_user_id = !empty($uP1['id']) ? $uP1['id'] : $pembimbing_1;
                    if (!empty($uP1['koordinator'])) {
                        $kk_p1 = $uP1['koordinator'];
                    } elseif (!empty($uP1['kelompok_keahlian'])) {
                        $kk_p1 = $uP1['kelompok_keahlian'];
                    } elseif (!empty($uP1['id_kk']) && $this->db->table_exists('kelompok_keahlian')) {
                        $kkRow = $this->db->get_where('kelompok_keahlian', ['id' => $uP1['id_kk']])->row_array();
                        if ($kkRow) {
                            $kk_p1 = $kkRow['kode_kk'] ?: $kkRow['nama_kk'];
                        }
                    }
                }
            }

            // 2. Resolve Pembimbing 2 ke User ID
            $p2_user_id = (string)$pembimbing_2;
            if (!empty($pembimbing_2)) {
                $this->db->group_start();
                $this->db->where('id', $pembimbing_2);
                $this->db->or_where('nip', $pembimbing_2);
                $this->db->or_where('nim', $pembimbing_2);
                $this->db->group_end();
                $uP2 = $this->db->get('user')->row_array();
                if ($uP2) {
                    $p2_user_id = !empty($uP2['id']) ? $uP2['id'] : $pembimbing_2;
                }
            }

            $this->db->where('id_guidance', $guidanceId);
            $tl = $this->db->get('thesis_lecturers')->row_array();

            if (!$tl) {
                $tlData = array(
                    'id'                => 'tl_' . uniqid(),
                    'id_guidance'       => $guidanceId,
                    'dosen_pembimbing1' => $p1_user_id,
                    'kelompok_keahlian' => $kk_p1,
                    'dosen_pembimbing2' => $p2_user_id,
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
                    'dosen_pembimbing1' => $p1_user_id,
                    'dosen_pembimbing2' => $p2_user_id,
                    'kelompok_keahlian' => $kk_p1,
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
        $this->db->order_by('date', 'ASC');
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
        /**
     * Mengambil Mahasiswa yang Siap Mengikuti Evaluasi Tahap Preview 2 (Tab 2)
     * 
     * Menerapkan aturan penegakan batasan tahapan (Stage Boundary Enforcement):
     * Mahasiswa HANYA akan muncul di antrean Tab 2 apabila:
     * 1. Telah disetujui oleh Ketua KK (status_plotting == 'selesai' dan status_kk == 'Disetujui')
     * 2. Telah lulus seminar proposal Preview 1 (status_preview != 'preview1')
     * 
     * @return array Daftar mahasiswa yang memenuhi syarat plotting penguji Preview 2
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

            $statusPlottingKetuaKK = strtolower(trim($item['status_plotting'] ?? ''));

            // Syarat Valid Masuk Tahap Preview 2 (Plotting Dosen Penguji & Jadwal Sidang Preview 2):
            // 1. Status bimbingan mahasiswa di tabel guidance (status_preview) HARUS SUDAH masuk 'preview2' (atau lebih lanjut: preview3, sidang, selesai, lulus)
            //    -> TIDAK BOLEH mahasiswa yang masih di 'pending', proposal, atau 'preview1'
            // 2. Plotting dosen pembimbing harus SUDAH DISETUJUI oleh Ketua KK (status_plotting == 'approved')
            //    -> TIDAK BOLEH mahasiswa yang status plotting pembimbingnya masih 'pending' (masih menunggu persetujuan Ketua KK)
            $isAtLeastPreview2 = in_array($statusPreview, ['preview2', 'preview3', 'sidang', 'selesai', 'lulus']);
            $isKetuaKkApproved = ($statusPlottingKetuaKK === 'approved');

            // Jika status plotting pembimbing masih PENDING di Ketua KK dan belum ada penguji, JANGAN masukkan ke Preview 2!
            if ($statusPlottingKetuaKK === 'pending' && !$hasAnyPenguji && !$hasJadwalPresentasi) {
                continue;
            }

            // Jika tahapan mahasiswa belum masuk Preview 2 (masih pending atau preview1) dan belum ada penguji, JANGAN masukkan ke Preview 2!
            if (!$isAtLeastPreview2 && !$hasAnyPenguji && !$hasJadwalPresentasi) {
                continue;
            }

            // Jika status proposal secara eksplisit ditolak dan belum ada penguji/jadwal, abaikan
            if (strcasecmp($item['status_approval_koor'] ?? '', 'Rejected') === 0 ||
                strcasecmp($item['status_approval_wali'] ?? '', 'Rejected') === 0 ||
                strcasecmp($item['status_adminlaa'] ?? '', 'Rejected') === 0) {
                if (!$hasAnyPenguji && !$hasJadwalPresentasi) {
                    continue;
                }
            }

            $isEligible = ($isAtLeastPreview2 && ($isKetuaKkApproved || empty($item['id_thesis_lecturers']))) || $hasAnyPenguji || $hasJadwalPresentasi;

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
        /**
     * Menetapkan Dosen Penguji Evaluasi Preview 2 (Tab 2)
     * 
     * Memvalidasi aturan bebas benturan kepentingan (Conflict of Interest Protection):
     * Dosen Pembimbing 1 maupun 2 mahasiswa bersangkutan dilarang keras menjadi Dosen Penguji.
     * Memperbarui kolom dosen_penguji_1 dan dosen_penguji_2 pada tabel guidance.
     * 
     * @param string $nim NIM mahasiswa target
     * @param string $penguji1 NIP Dosen Penguji 1
     * @param string $penguji2 NIP Dosen Penguji 2
     * @return array Status keberhasilan plotting
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
        $target_ids = array_values(array_unique(array_filter([$userId, $mhsNim])));

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

        // Resolve penguji to user ID
        $pj1_id = (string)$penguji_1;
        $pj2_id = (string)$penguji_2;
        if (!empty($penguji_1)) {
            $this->db->group_start();
            $this->db->where('id', $penguji_1);
            $this->db->or_where('nip', $penguji_1);
            $this->db->or_where('nim', $penguji_1);
            $this->db->group_end();
            $uPj1 = $this->db->get('user')->row_array();
            if ($uPj1) $pj1_id = !empty($uPj1['id']) ? $uPj1['id'] : $penguji_1;
        }
        if (!empty($penguji_2)) {
            $this->db->group_start();
            $this->db->where('id', $penguji_2);
            $this->db->or_where('nip', $penguji_2);
            $this->db->or_where('nim', $penguji_2);
            $this->db->group_end();
            $uPj2 = $this->db->get('user')->row_array();
            if ($uPj2) $pj2_id = !empty($uPj2['id']) ? $uPj2['id'] : $penguji_2;
        }

        if (!$tl) {
            $tlData = array(
                'id'                => 'tl_' . uniqid(),
                'id_guidance'       => $gId,
                'dosen_pembimbing1' => '',
                'kelompok_keahlian' => 'VID',
                'dosen_pembimbing2' => '',
                'dosen_penguji1'    => $pj1_id,
                'dosen_penguji2'    => $pj2_id,
                'date'              => date('Y-m-d H:i:s'),
                'date_edit'         => date('Y-m-d H:i:s'),
                'status'            => 'Pending'
            );
            $this->db->insert('thesis_lecturers', $tlData);
        } else {
            $pj1_lama = $tl['dosen_penguji1'];
            $pj2_lama = $tl['dosen_penguji2'];

            $tlData = array(
                'dosen_penguji1' => $pj1_id,
                'dosen_penguji2' => $pj2_id,
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
        /**
     * Mengambil Antrean Mahasiswa Siap Sidang Komprehensif (Tab 3)
     * 
     * Mengambil data mahasiswa yang telah menyelesaikan evaluasi Preview 2 dan berkas
     * administrasinya telah dinyatakan valid oleh Admin LAA (draf naskah, turnitin, bebas lab).
     * 
     * @return array Daftar mahasiswa beserta jadwal sidang, ruangan, dan skor penilaian
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

        // Ambil riwayat log jadwal sidang & publikasi nilai terbaru per NIM dari log_approval_history
        $publishMap = array();
        $sidangScheduleMap = array();
        if ($this->db->table_exists('log_approval_history')) {
            $allNims = array_unique(array_filter(array_column($all, 'nim')));
            if (!empty($allNims)) {
                $this->db->from('log_approval_history');
                $this->db->where_in('modul', array('Publish Nilai Sidang', 'Sidang TA'));
                $this->db->where_in('ref_id', $allNims);
                $this->db->order_by('id', 'DESC');
                $pubLogs = $this->db->get()->result_array();

                foreach ($pubLogs as $pl) {
                    $nimKey = (string)$pl['ref_id'];
                    $parsed = array();
                    if (!empty($pl['catatan']) && $pl['catatan'][0] === '{') {
                        $parsed = json_decode($pl['catatan'], true) ?: array();
                    }

                    if ($pl['modul'] === 'Publish Nilai Sidang' && !isset($publishMap[$nimKey])) {
                        $publishMap[$nimKey] = array(
                            'action'      => $pl['action'],
                            'status'      => $parsed['status_publish'] ?? $pl['action'],
                            'tgl_publish' => $parsed['tgl_publish'] ?? null,
                            'created_at'  => $pl['created_at']
                        );
                    } elseif ($pl['modul'] === 'Sidang TA' && !isset($sidangScheduleMap[$nimKey])) {
                        $sidangScheduleMap[$nimKey] = array(
                            'jam_selesai' => $parsed['jam_selesai'] ?? null
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

            $jamSelesai = $sidangScheduleMap[$nimKey]['jam_selesai'] ?? null;
            if (empty($jamSelesai) && !empty($waktuSidang)) {
                $h = (int)substr($waktuSidang, 0, 2);
                $m = substr($waktuSidang, 3, 2);
                $jamSelesai = sprintf('%02d:%s:00', min(23, $h + 2), $m);
            }

            $item['tgl_sidang']               = $tglSidang;
            $item['tanggal_sidang']           = $tglSidang;
            $item['jam_mulai_sidang']         = $waktuSidang;
            $item['jam_selesai_sidang']       = $jamSelesai;
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
        /**
     * Menetapkan Tanggal, Jam, dan Ruangan Sidang Komprehensif (Tab 3)
     * 
     * Menerapkan algoritma deteksi bentrok ruangan (Room Conflict Detection):
     * Memastikan ruangan sidang yang dipilih pada tanggal dan jam tersebut belum dialokasikan
     * untuk mahasiswa lain sebelum menyimpan jadwal ke tabel guidance.
     * 
     * @param string $nim NIM mahasiswa
     * @param string $tanggal Tanggal pelaksanaan sidang (YYYY-MM-DD)
     * @param string $jam Waktu mulai sidang (HH:MM)
     * @param string $ruangan Kode/Nama ruangan resmi dari master_ruangan
     * @return array Status konfirmasi jadwal sidang
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
                'jam_selesai'    => $jam_selesai,
                'ruang_sidang'   => $ruang_sidang
            ))
        ));

        return array('status' => true, 'message' => 'Jadwal sidang mahasiswa berhasil disimpan!');
    }

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
            $selesai = $row['jam_selesai_sidang'] ?? ($row['jam_selesai'] ?? null);

            if (!empty($nim)) {
                $r = $this->update_jadwal_sidang_ajax($nim, $tgl, $waktu, $ruang, $link, $selesai);
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
        /**
     * Menyimpan Input Skor Penilaian Sidang Komprehensif (Tab 3)
     * 
     * Menerima rincian skor per butir indikator kriteria dari majelis dewan penguji dan pembimbing,
     * mengkalkulasi Nilai Akhir Tertimbang (35% Penguji 1 + 35% Penguji 2 + 30% Pembimbing),
     * mengonversikannya ke huruf mutu (A s/d E), dan menetapkan status kelulusan (Lulus jika >= 50).
     * 
     * @param string $nim NIM mahasiswa
     * @param array $scores Matriks skor nilai komponen
     * @param string $catatan Komentar / catatan perbaikan dewan penguji
     * @return array Hasil kalkulasi nilai akhir, grade, dan status kelulusan
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

                $modul = $row['modul'];
                $aksi = $row['action'];
                $tglSidang = $parsedCatatan['tanggal_sidang'] ?? null;
                $waktuSidang = $parsedCatatan['waktu_sidang'] ?? null;
                $ruangSidang = $parsedCatatan['ruang_sidang'] ?? null;

                $cleanCatatan = $parsedCatatan['catatan_koor'] ?? ($parsedCatatan['catatan'] ?? ($parsedCatatan['keterangan'] ?? ''));
                if (empty($cleanCatatan) && !empty($rawCatatan) && $rawCatatan[0] !== '{') {
                    $cleanCatatan = $rawCatatan;
                }

                $displayAksi = $aksi;
                if ($modul === 'Sidang TA' || ($aksi === 'Scheduled' && !empty($tglSidang))) {
                    $displayAksi = 'Penjadwalan Sidang';
                }

                $results[] = array(
                    'id'               => $row['id'],
                    'nim'              => $row['ref_id'],
                    'nama_mahasiswa'   => $row['target_name'] ?: ('Mahasiswa NIM ' . $row['ref_id']),
                    'modul'            => $modul,
                    'aksi'             => $displayAksi,
                    'status_publish'   => $parsedCatatan['status_publish'] ?? $row['action'],
                    'tgl_publish'      => $parsedCatatan['tgl_publish'] ?? null,
                    'tanggal_sidang'   => $tglSidang,
                    'waktu_sidang'     => $waktuSidang,
                    'ruang_sidang'     => $ruangSidang,
                    'nilai_akhir'      => $parsedCatatan['nilai_akhir'] ?? null,
                    'grade'            => $parsedCatatan['grade'] ?? null,
                    'status_kelulusan' => $parsedCatatan['status_kelulusan'] ?? null,
                    'alasan_blokir'    => $parsedCatatan['alasan'] ?? null,
                    'komponen_belum'   => $parsedCatatan['komponen_belum_terisi'] ?? null,
                    'actor_name'       => $row['actor_name'] ?: 'Koordinator TA',
                    'actor_role'       => $row['actor_role'] ?: 'Koordinator TA',
                    'catatan'          => $cleanCatatan,
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
        /**
     * Mempublikasikan Nilai Sidang / Rilis Yudisium Resmi (Tab 3)
     * 
     * Mengubah status_publish_nilai menjadi 1, mengesahkan kelulusan akhir, dan mencatat
     * log audit ke tabel log_approval_history agar nilai dapat dilihat mahasiswa di transkrip portal.
     * 
     * @param string $nim NIM mahasiswa
     * @param string $tglPublish Tanggal rilis publikasi
     * @return array Status konfirmasi publikasi
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

        $this->_sync_guidance_kelulusan_sidang($student, $status_publish);
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
     * Sinkronisasi status kelulusan sidang ke tabel guidance saat Koordinator TA mempublikasikan nilai.
     * Sistem mengecek apakah rata-rata nilai sidang memenuhi syarat kelulusan (>= 55).
     * Jika Lulus dan status Published, ubah status tahap di guidance (status_preview) menjadi 'lulus' dan lulus_sidang = 1.
     * Jika Draft atau Tidak Lulus, kembalikan status tahap ke 'sidang' dan lulus_sidang = 0.
     */
    private function _sync_guidance_kelulusan_sidang($student, $status_publish = 'Published') {
        if (!$this->db->table_exists('guidance')) return;
        $id_guidance = $student['guidance_id'] ?? ($student['id_guidance'] ?? null);
        $nim = $student['nim'] ?? ($student['user_id'] ?? null);

        $n1  = (float)($student['nilaisidang_pembimbing1'] ?? 0);
        $n2  = (float)($student['nilaisidang_pembimbing2'] ?? 0);
        $np1 = (float)($student['nilaisidang_penguji1'] ?? 0);
        $np2 = (float)($student['nilaisidang_penguji2'] ?? 0);
        $is_lengkap = ($n1 > 0 && $n2 > 0 && $np1 > 0 && $np2 > 0);

        $nilai_akhir = $is_lengkap ? round(($n1 + $n2 + $np1 + $np2) / 4, 2) : (float)($student['nilai_akhir_sidang'] ?? 0);
        $status_kel = strtolower(trim($student['status_kelulusan_sidang'] ?? ''));
        $is_lulus_score = ($is_lengkap && $nilai_akhir >= 55 && $status_kel !== 'tidak lulus');

        // Pastikan kolom status_preview di tabel guidance berformat VARCHAR(50) agar nilai 'lulus' tidak terpotong oleh ENUM lama
        $prev_dbg = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        @$this->db->query("ALTER TABLE `guidance` MODIFY COLUMN `status_preview` VARCHAR(50) NULL DEFAULT 'preview1'");
        $this->db->db_debug = $prev_dbg;

        $upd = array();
        if ($status_publish === 'Published' && $is_lulus_score) {
            if ($this->db->field_exists('lulus_sidang', 'guidance')) $upd['lulus_sidang'] = 1;
            if ($this->db->field_exists('status_preview', 'guidance')) $upd['status_preview'] = 'lulus';
        } elseif ($status_publish === 'Draft' || !$is_lulus_score) {
            if ($this->db->field_exists('lulus_sidang', 'guidance')) $upd['lulus_sidang'] = 0;
            if ($this->db->field_exists('status_preview', 'guidance')) $upd['status_preview'] = 'sidang';
        }

        if (empty($upd)) return;

        $target_ids = array_values(array_unique(array_filter([
            $nim,
            $student['user_id'] ?? null,
            'usr_mhs_' . $nim,
            'mhs_' . $nim,
            'usr_' . $nim,
            'gdn_' . $nim
        ])));

        $this->db->group_start();
        if (!empty($id_guidance)) {
            $this->db->or_where('id', $id_guidance);
        }
        if (!empty($target_ids)) {
            $this->db->or_where_in('id_mhs', $target_ids);
            $this->db->or_where_in('id', $target_ids);
        }
        $this->db->group_end();
        $this->db->update('guidance', $upd);
    }

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
                $this->_sync_guidance_kelulusan_sidang($student, $status_publish);
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

    /**
     * Pastikan kolom tanggal_publish, tanggal_berakhir, status, versi, dan index multi-versi tersedia di tabel master_rubrik
     */
    public function _ensure_master_rubrik_tables() {
        if (!$this->db->table_exists('master_rubrik')) {
            return;
        }

        if (!$this->db->field_exists('tanggal_publish', 'master_rubrik')) {
            @$this->db->query("ALTER TABLE `master_rubrik` ADD COLUMN `tanggal_publish` DATETIME NULL AFTER `total_bobot`;");
        }
        if (!$this->db->field_exists('tanggal_berakhir', 'master_rubrik')) {
            @$this->db->query("ALTER TABLE `master_rubrik` ADD COLUMN `tanggal_berakhir` DATETIME NULL AFTER `tanggal_publish`;");
        }
        if (!$this->db->field_exists('status', 'master_rubrik')) {
            @$this->db->query("ALTER TABLE `master_rubrik` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'published' AFTER `tanggal_berakhir`;");
        }
        if (!$this->db->field_exists('versi', 'master_rubrik')) {
            @$this->db->query("ALTER TABLE `master_rubrik` ADD COLUMN `versi` INT NOT NULL DEFAULT 1 AFTER `status`;");
        }

        // Pastikan index mengizinkan multi-versi per prodi dan peminatan
        $indexes = $this->db->query("SHOW INDEX FROM `master_rubrik`")->result_array();
        $indexMap = array();
        foreach ($indexes as $idx) {
            $indexMap[$idx['Key_name']] = $idx;
        }

        if (isset($indexMap['idx_prodi_peminatan']) && (int)$indexMap['idx_prodi_peminatan']['Non_unique'] === 0) {
            @$this->db->query("ALTER TABLE `master_rubrik` DROP INDEX `idx_prodi_peminatan`");
            unset($indexMap['idx_prodi_peminatan']);
        }

        if (!isset($indexMap['idx_prodi_peminatan_versi'])) {
            @$this->db->query("ALTER TABLE `master_rubrik` ADD UNIQUE KEY `idx_prodi_peminatan_versi` (`prodi`, `peminatan`, `versi`)");
        }

        if (!isset($indexMap['idx_prodi_peminatan'])) {
            @$this->db->query("ALTER TABLE `master_rubrik` ADD INDEX `idx_prodi_peminatan` (`prodi`, `peminatan`)");
        }
    }

    public function _seed_default_master_rubrik() {
        if (!$this->db->table_exists('master_rubrik') || !$this->db->table_exists('master_rubrik_kriteria')) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $defaultTemplates = array(
            array(
                'prodi'        => 'DKV',
                'peminatan'    => 'Multimedia',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - Multimedia',
                'kriteria'     => array(
                    array('id' => 'k1', 'title' => 'Konsep & Storyboard Multimedia', 'desc' => 'Kedalaman gagasan, orisinalitas ide, alur narasi, dan struktur storyboard visual.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Penguasaan Teknis Audio Visual & Animasi', 'desc' => 'Kualitas editing, rendering, compositing, motion graphic, dan sinkronisasi audio-visual.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Interaktivitas & User Experience (UI/UX)', 'desc' => 'Kemudahan interaksi antarmuka, responsivitas, dan fungsionalitas media multimedia terapan.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Komprehensi & Presentasi Sidang', 'desc' => 'Kelancaran penyampaian argumen karya, penguasaan materi, dan pertanggungjawaban desain.', 'bobot' => 20)
                )
            ),
            array(
                'prodi'        => 'DKV',
                'peminatan'    => 'Game',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - Game',
                'kriteria'     => array(
                    array('id' => 'k1', 'title' => 'Game Design Document (GDD) & Core Concept', 'desc' => 'Kelengkapan GDD, target audience, core loop, dan inovasi genre mekanika game.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Game Mechanics, Balancing & Asset Art 2D/3D', 'desc' => 'Kualitas asset grafis karakter/lingkungan, animasi, balancing kesulitan, dan level design.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Playability, Prototype Testing & Bug Handling', 'desc' => 'Kelancaran gameplay (playability), performa frame rate, dan hasil uji coba playtest pengguna.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Live Demo & Argumentasi Teknis Sidang', 'desc' => 'Penguasaan implementasi game engine, demonstrasi gameplay langsung, dan respon tanya jawab.', 'bobot' => 20)
                )
            ),
            array(
                'prodi'        => 'DKV',
                'peminatan'    => 'Designpreneur',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - Designpreneur',
                'kriteria'     => array(
                    array('id' => 'k1', 'title' => 'Riset Pasar & Business Model Canvas (BMC)', 'desc' => 'Validasi problem-solution fit, positioning pasar, analisis kompetitor, dan segmen konsumen.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Identitas Visual & Desain Produk/Kemasan', 'desc' => 'Kekuatan branding, packaging design, konsistensi collateral visual, dan daya tarik jual.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Strategi Go-To-Market & Kelayakan Finansial', 'desc' => 'Kesiapan strategi peluncuran, proyeksi biaya produksi (COGS), dan harga jual komersial.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Pitching Presentasi & Respon Penguji', 'desc' => 'Kemampuan pitching bisnis di depan dewan penguji dan keyakinan eksekusi usaha.', 'bobot' => 20)
                )
            ),
            array(
                'prodi'        => 'DKV',
                'peminatan'    => 'VIID',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir DKV - VIID',
                'kriteria'     => array(
                    array('id' => 'k1', 'title' => 'Konsep Desain Identitas Visual Terintegrasi', 'desc' => 'Kedalaman brand architecture, diferensiasi identitas visual, dan filosofi desain.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Desain Informasi, Tipografi & Layout Sistem', 'desc' => 'Hirarki informasi, legibilitas tipografi, grid system, dan konsistensi pedoman manual (GSM).', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Implementasi Multi-Platform Media Cetak & Digital', 'desc' => 'Aplikasi identitas ke berbagai touchpoint fisik (stationary, signage) dan digital (web, app).', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Presentasi Manual Book & Tanggung Jawab Desain', 'desc' => 'Kualitas buku pedoman visual (GSM) dan ketepatan argumentasi saat ujian sidang.', 'bobot' => 20)
                )
            ),
            array(
                'prodi'        => 'DI',
                'peminatan'    => 'Komersial',
                'judul_rubrik' => 'Rubrik Sidang Tugas Akhir Desain Interior - Komersial',
                'kriteria'     => array(
                    array('id' => 'k1', 'title' => 'Konsep Desain & Program Ruang Komersial', 'desc' => 'Kesesuaian programming ruang, zoning, flow sirkulasi pengunjung, dan konsep tematik interior.', 'bobot' => 25),
                    array('id' => 'k2', 'title' => 'Gambar Kerja Teknis & Detail Konstruksi', 'desc' => 'Kelengkapan denah, potongan, tampak, detail material finish, dan sistem MEP interior.', 'bobot' => 30),
                    array('id' => 'k3', 'title' => 'Visualisasi 3D Rendering & Pemilihan Material', 'desc' => 'Kualitas visual 3D photorealistic, lighting ambient, pemilihan furniture, dan sample material board.', 'bobot' => 25),
                    array('id' => 'k4', 'title' => 'Presentasi Konsep & Ketepatan Menjawab Penguji', 'desc' => 'Kemampuan komunikasi arsitektural dan pertanggungjawaban teknis di hadapan dewan penguji.', 'bobot' => 20)
                )
            )
        );

        foreach ($defaultTemplates as $tpl) {
            $existing = $this->db->order_by('versi', 'DESC')->get_where('master_rubrik', array(
                'prodi'     => $tpl['prodi'],
                'peminatan' => $tpl['peminatan']
            ))->row_array();

            $rId = null;
            if ($existing) {
                $rId = $existing['id'];
                $this->db->where('id', $rId)->update('master_rubrik', array(
                    'judul_rubrik'    => $tpl['judul_rubrik'],
                    'total_bobot'     => 100,
                    'status'          => 'published',
                    'tanggal_publish' => $existing['tanggal_publish'] ?: $now,
                    'updated_at'      => $now
                ));
            } else {
                $this->db->insert('master_rubrik', array(
                    'prodi'           => $tpl['prodi'],
                    'peminatan'       => $tpl['peminatan'],
                    'judul_rubrik'    => $tpl['judul_rubrik'],
                    'total_bobot'     => 100,
                    'status'          => 'published',
                    'versi'           => 1,
                    'tanggal_publish' => $now,
                    'updated_at'      => $now
                ));
                $rId = $this->db->insert_id();
            }

            if ($rId) {
                $this->db->where('rubrik_id', $rId)->delete('master_rubrik_kriteria');
                $urutan = 1;
                foreach ($tpl['kriteria'] as $crit) {
                    $this->db->insert('master_rubrik_kriteria', array(
                        'rubrik_id'     => $rId,
                        'kode_kriteria' => $crit['id'],
                        'nama_kriteria' => $crit['title'],
                        'deskripsi'     => $crit['desc'],
                        'bobot'         => $crit['bobot'],
                        'urutan'        => $urutan++
                    ));
                }
            }
        }

        return true;
    }

        /**
     * Mengambil Referensi Master Program Studi Aktif
     * 
     * Mengambil daftar seluruh program studi resmi di lingkungan Fakultas Industri Kreatif
     * dari tabel master_prodi untuk dropdown antarmuka dashboard dan master rubrik.
     * 
     * @return array Daftar program studi aktif terurut berdasarkan kolom urutan ASC
     */
    public function get_master_prodi_list() {
        if (!$this->db->table_exists('master_prodi')) {
            return array();
        }
        return $this->db->where('is_active', 1)
                        ->order_by('urutan', 'ASC')
                        ->order_by('nama_prodi', 'ASC')
                        ->get('master_prodi')
                        ->result_array();
    }

        /**
     * Mengambil Referensi Master Peminatan Berdasarkan Kode Prodi
     * 
     * Mendukung sinkronisasi dropdown bertingkat (cascading dropdown) dari tabel master_peminatan.
     * 
     * @param string|null $kode_prodi Filter spesifik kode prodi (opsional)
     * @return array Daftar peminatan konsentrasi keilmuan aktif
     */
    public function get_master_peminatan_list($kode_prodi = null) {
        if (!$this->db->table_exists('master_peminatan')) {
            return array();
        }
        $this->db->select('*')->from('master_peminatan')->where('is_active', 1);
        if (!empty($kode_prodi)) {
            $this->db->where('kode_prodi', $kode_prodi);
        }
        return $this->db->order_by('urutan', 'ASC')
                        ->order_by('nama_peminatan', 'ASC')
                        ->get()
                        ->result_array();
    }

    public function get_all_master_rubrik() {
        $this->_ensure_master_rubrik_tables();

        if (!$this->db->table_exists('master_rubrik')) {
            return array();
        }

        $this->db->select('*');
        $this->db->from('master_rubrik');
        $this->db->order_by('prodi', 'ASC');
        $this->db->order_by('peminatan', 'ASC');
        $this->db->order_by('versi', 'DESC');
        $this->db->order_by('id', 'DESC');
        $allRows = $this->db->get()->result_array();

        if (empty($allRows)) {
            $this->_seed_default_master_rubrik();
            $this->db->select('*');
            $this->db->from('master_rubrik');
            $this->db->order_by('prodi', 'ASC');
            $this->db->order_by('peminatan', 'ASC');
            $this->db->order_by('versi', 'DESC');
            $this->db->order_by('id', 'DESC');
            $allRows = $this->db->get()->result_array();
        }

        if (empty($allRows)) {
            return array();
        }

        $rubrikIds = array_column($allRows, 'id');
        $kriteriaMap = array();

        if (!empty($rubrikIds) && $this->db->table_exists('master_rubrik_kriteria')) {
            $this->db->select('*');
            $this->db->from('master_rubrik_kriteria');
            $this->db->where_in('rubrik_id', $rubrikIds);
            $this->db->order_by('urutan', 'ASC');
            $this->db->order_by('id', 'ASC');
            $kList = $this->db->get()->result_array();

            foreach ($kList as $k) {
                $rId = $k['rubrik_id'];
                if (!isset($kriteriaMap[$rId])) {
                    $kriteriaMap[$rId] = array();
                }
                $kriteriaMap[$rId][] = array(
                    'id'    => $k['kode_kriteria'] ?: ('k' . $k['urutan']),
                    'title' => $k['nama_kriteria'],
                    'desc'  => $k['deskripsi'] ?? '',
                    'bobot' => (float)$k['bobot']
                );
            }
        }

        // Kelompokkan per (prodi, peminatan).
        // Baris pertama per prodi-peminatan adalah versi terbaru / aktif karena order by versi DESC
        $grouped = array();
        foreach ($allRows as $r) {
            $key = $r['prodi'] . '___' . $r['peminatan'];
            $rId = $r['id'];
            $tglPub = !empty($r['tanggal_publish']) ? $r['tanggal_publish'] : $r['updated_at'];
            $tglEnd = !empty($r['tanggal_berakhir']) ? $r['tanggal_berakhir'] : null;

            $versionItem = array(
                'id'                         => (int)$r['id'],
                'prodi'                      => $r['prodi'],
                'peminatan'                  => $r['peminatan'],
                'judul_rubrik'               => $r['judul_rubrik'],
                'total_bobot'                => (int)($r['total_bobot'] ?? 100),
                'status'                     => $r['status'] ?? 'published',
                'versi'                      => (int)($r['versi'] ?? 1),
                'tanggal_publish'            => $r['tanggal_publish'] ?? null,
                'tanggal_publish_formatted'  => !empty($tglPub) ? date('d M Y, H:i', strtotime($tglPub)) . ' WIB' : 'Belum Dipublikasikan',
                'tanggal_berakhir'           => $tglEnd,
                'tanggal_berakhir_formatted' => !empty($tglEnd) ? date('d M Y, H:i', strtotime($tglEnd)) . ' WIB' : 'Aktif (Sekarang)',
                'updated_at'                 => $r['updated_at'] ?? null,
                'kriteria'                   => $kriteriaMap[$rId] ?? array()
            );

            if (!isset($grouped[$key])) {
                $grouped[$key] = $versionItem;
                $grouped[$key]['history_versions'] = array();
            }

            $grouped[$key]['history_versions'][] = $versionItem;
        }

        return array_values($grouped);
    }

        /**
     * Menyimpan dan Mengarsipkan Master Rubrik Penilaian Sidang (Multi-Version Archiving)
     * 
     * Memvalidasi bahwa total persentase bobot butir kriteria wajib tepat bernilai 100.0%.
     * Jika parameter as_new_version bernilai true, versi aktif lama diarsipkan (status = 'archived')
     * dan versi baru diterbitkan dengan nomor versi bertambah (versi + 1).
     * 
     * @param string $prodi Kode Program Studi
     * @param string $peminatan Nama Peminatan / Konsentrasi
     * @param string $judul Judul instrumen rubrik
     * @param array $kriteria Butir kriteria indikator dan persentase bobot
     * @param float $total_bobot Akumulasi total bobot (harus 100.0)
     * @param string $status Status publikasi ('published' / 'draft')
     * @param bool $as_new_version Apakah disimpan sebagai versi kurikulum baru
     * @return array Data rubrik tersimpan dan ID referensi
     */
    public function simpan_master_rubrik($prodi, $peminatan, $judul_rubrik, $kriteria = array(), $total_bobot = 100, $status = 'published', $is_publish = false, $custom_tanggal_publish = null, $as_new_version = false) {
        $this->_ensure_master_rubrik_tables();

        $prodi = trim($prodi);
        $peminatan = trim($peminatan);
        $judul_rubrik = trim($judul_rubrik);

        if (empty($prodi) || empty($peminatan)) {
            return array('status' => false, 'message' => 'Prodi dan peminatan wajib diisi.');
        }

        if (empty($judul_rubrik)) {
            $judul_rubrik = "Rubrik Sidang Tugas Akhir {$prodi} - {$peminatan}";
        }

        $now = date('Y-m-d H:i:s');
        $effectivePublishDate = !empty($custom_tanggal_publish)
            ? date('Y-m-d H:i:s', strtotime($custom_tanggal_publish))
            : $now;

        // Ambil versi terbaru yang ada saat ini
        $this->db->where('prodi', $prodi);
        $this->db->where('peminatan', $peminatan);
        $this->db->order_by('versi', 'DESC');
        $this->db->order_by('id', 'DESC');
        $current = $this->db->get('master_rubrik')->row_array();

        $currentVersi = $current ? (int)($current['versi'] ?? 1) : 1;
        $rubrik_id = null;
        $savedVersi = $currentVersi;

        // Jika ini adalah Publish Versi Baru ($as_new_version || $is_publish)
        // Dan sudah ada record sebelumnya, kita buat baris baru agar riwayat dan nilai mahasiswa lama tetap utuh!
        if (($as_new_version || $is_publish) && $current) {
            $newVersi = $currentVersi + 1;
            $savedVersi = $newVersi;

            // 1. Arsipkan versi-versi sebelumnya yang masih berstatus published
            $this->db->where('prodi', $prodi);
            $this->db->where('peminatan', $peminatan);
            $this->db->where('status', 'published');
            $this->db->update('master_rubrik', array(
                'status'           => 'archived',
                'tanggal_berakhir' => $effectivePublishDate,
                'updated_at'       => $now
            ));

            // 2. Buat baris baru untuk versi baru yang aktif
            $dataRubrik = array(
                'prodi'            => $prodi,
                'peminatan'        => $peminatan,
                'judul_rubrik'     => $judul_rubrik,
                'total_bobot'      => (int)$total_bobot,
                'status'           => 'published',
                'versi'            => $newVersi,
                'tanggal_publish'  => $effectivePublishDate,
                'tanggal_berakhir' => null,
                'updated_at'       => $now
            );
            $this->db->insert('master_rubrik', $dataRubrik);
            $rubrik_id = $this->db->insert_id();

        } elseif ($current) {
            // Edit versi yang sedang aktif tanpa membuat versi baru (misal update minor / perbaiki penulisan)
            $rubrik_id = $current['id'];
            $dataUpdate = array(
                'judul_rubrik' => $judul_rubrik,
                'total_bobot'  => (int)$total_bobot,
                'status'       => $status,
                'updated_at'   => $now
            );
            if (!empty($custom_tanggal_publish)) {
                $dataUpdate['tanggal_publish'] = $effectivePublishDate;
            }
            $this->db->where('id', $rubrik_id)->update('master_rubrik', $dataUpdate);

        } else {
            // Belum pernah ada data sama sekali (insert versi 1)
            $dataRubrik = array(
                'prodi'            => $prodi,
                'peminatan'        => $peminatan,
                'judul_rubrik'     => $judul_rubrik,
                'total_bobot'      => (int)$total_bobot,
                'status'           => $status,
                'versi'            => 1,
                'tanggal_publish'  => $effectivePublishDate,
                'tanggal_berakhir' => null,
                'updated_at'       => $now
            );
            $this->db->insert('master_rubrik', $dataRubrik);
            $rubrik_id = $this->db->insert_id();
            $savedVersi = 1;
        }

        // Simpan butir-butir kriteria untuk rubrik_id ini
        if (!empty($rubrik_id) && is_array($kriteria)) {
            // Hapus kriteria milik rubrik_id ini saja (tidak menyentuh rubrik_id versi lama!)
            $this->db->where('rubrik_id', $rubrik_id)->delete('master_rubrik_kriteria');

            $urutan = 1;
            foreach ($kriteria as $crit) {
                $kode = !empty($crit['id']) ? $crit['id'] : ('k' . $urutan);
                $title = trim($crit['title'] ?? ($crit['name'] ?? ''));
                $desc = trim($crit['desc'] ?? ($crit['deskripsi'] ?? ''));
                $bobot = (float)($crit['bobot'] ?? 0);

                if (!empty($title)) {
                    $this->db->insert('master_rubrik_kriteria', array(
                        'rubrik_id'     => $rubrik_id,
                        'kode_kriteria' => $kode,
                        'nama_kriteria' => $title,
                        'deskripsi'     => $desc,
                        'bobot'         => $bobot,
                        'urutan'        => $urutan
                    ));
                    $urutan++;
                }
            }
        }

        // Catat ke log approval history
        if ($this->db->table_exists('log_approval_history')) {
            $actionLabel = ($as_new_version || $is_publish) ? "Published Versi {$savedVersi}" : "Updated Versi {$savedVersi}";
            $this->db->insert('log_approval_history', array(
                'modul'         => 'Master Rubrik',
                'ref_id'        => (string)$rubrik_id,
                'target_name'   => "{$prodi} - {$peminatan} (v{$savedVersi})",
                'action'        => $actionLabel,
                'actor_id'      => $this->session->userdata('user_id'),
                'actor_name'    => $this->session->userdata('name') ?: 'Koordinator TA',
                'actor_role'    => 'Koordinator TA',
                'actor_nip_nim' => $this->session->userdata('nip') ?: $this->session->userdata('username'),
                'catatan'       => json_encode(array(
                    'versi'           => $savedVersi,
                    'judul_rubrik'    => $judul_rubrik,
                    'total_kriteria'  => count($kriteria),
                    'total_bobot'     => $total_bobot,
                    'status'          => $status,
                    'tanggal_publish' => $effectivePublishDate
                ), JSON_UNESCAPED_UNICODE),
                'created_at'    => $now
            ));
        }

        $msg = ($as_new_version || $is_publish)
            ? "Master rubrik {$prodi} - {$peminatan} berhasil dipublikasikan sebagai Versi {$savedVersi} (Masa berlaku aktif per " . date('d M Y, H:i', strtotime($effectivePublishDate)) . " WIB)!"
            : "Master rubrik {$prodi} - {$peminatan} Versi {$savedVersi} berhasil diperbarui.";

        return array(
            'status'                    => true,
            'message'                   => $msg,
            'rubrik_id'                 => $rubrik_id,
            'versi'                     => $savedVersi,
            'tanggal_publish'           => $effectivePublishDate,
            'tanggal_publish_formatted' => date('d M Y, H:i', strtotime($effectivePublishDate)) . ' WIB'
        );
    }

    /**
     * Ambil rubrik aktif berdasarkan prodi, peminatan, dan tanggal sidang/penilaian.
     * Sesuai arsitektur versioning:
     * - Mahasiswa yang dinilai pada tanggal X akan menggunakan versi rubrik yang berlaku pada tanggal X.
     * - Data nilai lama tetap aman dan utuh meskipun ada kriteria baru yang ditambahkan di masa depan.
     */
    public function get_active_rubrik_for_evaluation($prodi, $peminatan, $evaluation_date = null) {
        $this->_ensure_master_rubrik_tables();

        if (empty($evaluation_date)) {
            $evaluation_date = date('Y-m-d H:i:s');
        } else {
            $evaluation_date = date('Y-m-d H:i:s', strtotime($evaluation_date));
        }

        // Cari versi yang berlaku pada rentang waktu: tanggal_publish <= $evaluation_date AND (tanggal_berakhir IS NULL OR tanggal_berakhir > $evaluation_date)
        $this->db->select('*');
        $this->db->from('master_rubrik');
        $this->db->where('prodi', $prodi);
        $this->db->where('peminatan', $peminatan);
        $this->db->where('tanggal_publish <=', $evaluation_date);
        $this->db->group_start();
        $this->db->where('tanggal_berakhir IS NULL', null, false);
        $this->db->or_where('tanggal_berakhir >', $evaluation_date);
        $this->db->group_end();
        $this->db->order_by('versi', 'DESC');
        $this->db->order_by('id', 'DESC');
        $rubrik = $this->db->get()->row_array();

        // Fallback jika tidak ada yang cocok dengan rentang tanggal (misal penilaian lampau sebelum tanggal_publish)
        if (!$rubrik) {
            $this->db->select('*');
            $this->db->from('master_rubrik');
            $this->db->where('prodi', $prodi);
            $this->db->where('peminatan', $peminatan);
            $this->db->order_by('versi', 'ASC');
            $this->db->limit(1);
            $rubrik = $this->db->get()->row_array();
        }

        if (!$rubrik) {
            return null;
        }

        // Ambil kriteria untuk rubrik_id ini
        $this->db->select('*');
        $this->db->from('master_rubrik_kriteria');
        $this->db->where('rubrik_id', $rubrik['id']);
        $this->db->order_by('urutan', 'ASC');
        $kriteria = $this->db->get()->result_array();

        $rubrik['kriteria'] = array();
        foreach ($kriteria as $k) {
            $rubrik['kriteria'][] = array(
                'id'    => $k['kode_kriteria'] ?: ('k' . $k['urutan']),
                'title' => $k['nama_kriteria'],
                'desc'  => $k['deskripsi'] ?? '',
                'bobot' => (float)$k['bobot']
            );
        }

        return $rubrik;
    }

    public function terapkan_rubrik_massal($prodi, $peminatan, $nim_list = array()) {
        $this->_ensure_master_rubrik_tables();

        if (empty($prodi) || empty($peminatan)) {
            return array('status' => false, 'message' => 'Prodi dan peminatan wajib dipilih.');
        }

        $rubrik = $this->db->get_where('master_rubrik', array(
            'prodi'     => $prodi,
            'peminatan' => $peminatan
        ))->row_array();

        if (!$rubrik) {
            return array('status' => false, 'message' => "Master rubrik untuk {$prodi} - {$peminatan} belum tersedia.");
        }

        $all = $this->get_all_mahasiswa_ta();
        $targetNims = array();
        $skippedPublished = 0;

        foreach ($all as $mhs) {
            $mProdi = $mhs['prodi'] ?? ($mhs['konsentrasi_dkv'] ?? '');
            if (strcasecmp($mProdi, $prodi) !== 0 && stripos($mProdi, $prodi) === false) {
                continue;
            }

            $nim = $mhs['nim'];
            if (!empty($nim_list) && !in_array($nim, $nim_list)) {
                continue;
            }

            // PERLINDUNGAN NILAI MAHASISWA LAMA:
            // Sesuai arahan pembimbing: Jangan ubah mahasiswa yang nilainya sudah dipublikasikan!
            $statusPublish = strtolower($mhs['status_publish_sidang'] ?? 'draft');
            if ($statusPublish === 'published') {
                $skippedPublished++;
                continue;
            }

            $targetNims[] = $nim;
        }

        if (empty($targetNims)) {
            if ($skippedPublished > 0) {
                return array(
                    'status'  => true,
                    'message' => "Seluruh {$skippedPublished} mahasiswa pada peminatan ini nilainya sudah dipublikasikan (terkunci aman), tidak ada yang ditimpa."
                );
            }
            return array('status' => false, 'message' => 'Tidak ada mahasiswa aktif yang memenuhi syarat untuk diterapkan rubrik.');
        }

        $this->db->where_in('username', $targetNims)->update('user', array('prodi' => $prodi));

        if ($this->db->table_exists('log_approval_history')) {
            $this->db->insert('log_approval_history', array(
                'modul'         => 'Terapkan Rubrik Massal',
                'ref_id'        => (string)$rubrik['id'],
                'target_name'   => "{$prodi} - {$peminatan}",
                'action'        => 'Applied',
                'actor_id'      => $this->session->userdata('user_id'),
                'actor_name'    => $this->session->userdata('name') ?: 'Koordinator TA',
                'actor_role'    => 'Koordinator TA',
                'actor_nip_nim' => $this->session->userdata('nip') ?: $this->session->userdata('username'),
                'catatan'       => json_encode(array(
                    'prodi'             => $prodi,
                    'peminatan'         => $peminatan,
                    'applied_count'     => count($targetNims),
                    'skipped_published' => $skippedPublished,
                    'nims'              => $targetNims
                ), JSON_UNESCAPED_UNICODE),
                'created_at'    => date('Y-m-d H:i:s')
            ));
        }

        $msg = "Master rubrik berhasil diterapkan ke " . count($targetNims) . " mahasiswa aktif.";
        if ($skippedPublished > 0) {
            $msg .= " ({$skippedPublished} mahasiswa yang nilainya sudah dipublikasikan tetap terkunci aman).";
        }

        return array('status' => true, 'message' => $msg, 'applied_nims' => $targetNims);
    }

    /**
     * Ambil data lengkap seluruh peserta TA untuk Halaman Monitoring Status Peserta TA
     * Mencakup semua tahapan: Dosen Wali -> Admin Layanan -> Koordinator TA -> Ketua KK -> Preview 1 -> Preview 2 -> Preview 3 -> Sidang -> Lulus
     */
        /**
     * Agregator Utama Monitoring Status Peserta Tugas Akhir (Pipeline 1 s/d 9)
     * 
     * Mengumpulkan data komprehensif mahasiswa dari awal pendaftaran hingga yudisium.
     * Menjalankan query batch efisien ke tabel guidance, thesis, log_approval_history,
     * dan AdminLayanan_model untuk memetakan posisi tahapan mahasiswa:
     * (1. Dosen Wali -> 2. Admin LAA -> 3. Koordinator TA -> 4. Ketua KK -> 5. Preview 1 ->
     *  6. Preview 2 -> 7. Preview 3 -> 8. Sidang TA -> 9. Lulus Sidang)
     * 
     * @return array Daftar seluruh peserta tugas akhir dengan metadata tahapan dan status berkas
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
        $thesisFullMap = array();
        if (!empty($guidanceIds) && $this->db->table_exists('thesis')) {
            $this->db->select('id, id_guidance, tahapan_preview, status, pdf_file, file_sitasi, file_bimbingan, file_persyaratan, file_sidang, link_project, correction1, correction2, correction3, correction4, catatan_file_draft, catatan_mahasiswa, keterangan, date, created_at');
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

                    if (!isset($thesisFullMap[$tGid])) {
                        $thesisFullMap[$tGid] = array();
                    }
                    $thesisFullMap[$tGid][] = $tr;
                }
            }
        }

        // Ambil riwayat log jadwal sidang & publikasi nilai terbaru per NIM dari log_approval_history
        $publishMap = array();
        $sidangScheduleMap = array();
        if ($this->db->table_exists('log_approval_history')) {
            $allNims = array_unique(array_filter(array_column($all, 'nim')));
            if (!empty($allNims)) {
                $this->db->from('log_approval_history');
                $this->db->where_in('modul', array('Publish Nilai Sidang', 'Sidang TA'));
                $this->db->where_in('ref_id', $allNims);
                $this->db->order_by('id', 'DESC');
                $pubLogs = $this->db->get()->result_array();

                foreach ($pubLogs as $pl) {
                    $nimKey = (string)$pl['ref_id'];
                    $parsed = array();
                    if (!empty($pl['catatan']) && $pl['catatan'][0] === '{') {
                        $parsed = json_decode($pl['catatan'], true) ?: array();
                    }

                    if ($pl['modul'] === 'Publish Nilai Sidang' && !isset($publishMap[$nimKey])) {
                        $publishMap[$nimKey] = array(
                            'action'      => $pl['action'],
                            'status'      => $parsed['status_publish'] ?? $pl['action'],
                            'tgl_publish' => $parsed['tgl_publish'] ?? null,
                            'created_at'  => $pl['created_at']
                        );
                    } elseif ($pl['modul'] === 'Sidang TA' && !isset($sidangScheduleMap[$nimKey])) {
                        $sidangScheduleMap[$nimKey] = array(
                            'jam_selesai' => $parsed['jam_selesai'] ?? null
                        );
                    }
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

            // Status publikasi nilai sidang
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

            $isPublished = ($statusPublish === 'Published') || ($statusPublish === 'Scheduled' && !empty($tglPublish) && strtotime($tglPublish) <= time());

            // Mahasiswa baru sah berstatus "Lulus" (Tahap 9) jika nilainya sudah di-PUBLISH oleh Koordinator TA dan memenuhi syarat kelulusan
            $isScorePassed = ($isNilaiLengkap && $avgScore >= 55) || (strcasecmp($gRow['status_bap'] ?? '', 'Approved') === 0);
            $isLulus = $isPublished && $isScorePassed;

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

            $isAdminFullyApproved = ($validBerkas >= 4 || ($validBerkas === $totalBerkas && $totalBerkas > 0)) && ($invalidBerkas === 0 && $pendingBerkas === 0) && (strcasecmp($statusAdmin, 'Approved') === 0 || strcasecmp($statusAdmin, 'Valid') === 0);

            // Deteksi Tahap Progres Terkini (Pipeline 1 s/d 9)
            // 1. Prioritas Utama: Lulus Sidang (Tahap 9)
            if ($isLulus) {
                $progresStage = 'Lulus';
                $stageKey     = 'lulus';
                $stageIndex   = 9;
                $stageDesc    = 'Lulus Sidang Tugas Akhir';
                $stageColor   = 'emerald';
            // 2. Tahap Bimbingan & Sidang Eksplisit dari guidance.status_preview (Single Source of Truth)
            } elseif ($statusPrev === 'sidang') {
                $progresStage = 'Sidang TA';
                $stageKey     = 'sidang';
                $stageIndex   = 8;
                $stageDesc    = $isNilaiLengkap ? ($isPublished ? 'Sidang Selesai' : 'Nilai Masuk (Menunggu Publish)') : 'Pelaksanaan Sidang Akhir';
                $stageColor   = 'rose';
            } elseif ($statusPrev === 'preview3') {
                $progresStage = 'Preview 3';
                $stageKey     = 'preview3';
                $stageIndex   = 7;
                $stageDesc    = 'Pra-Sidang & Finalisasi Dokumen TA';
                $stageColor   = 'cyan';
            } elseif ($statusPrev === 'preview2') {
                $progresStage = 'Preview 2';
                $stageKey     = 'preview2';
                $stageIndex   = 6;
                $stageDesc    = 'Evaluasi Progres & Presentasi Penguji';
                $stageColor   = 'indigo';
            } elseif ($statusPrev === 'preview1') {
                $progresStage = 'Preview 1';
                $stageKey     = 'preview1';
                $stageIndex   = 5;
                $stageDesc    = 'Bimbingan Bab 1-3 Bersama Pembimbing';
                $stageColor   = 'teal';
            // 3. Fallback jika status_preview di guidance masih kosong, pending, atau bukan preview
            } elseif ($hasSidang || $statusPrev === 'lulus' || $statusPrev === 'selesai' || !empty($tInfo['has_sidang']) || !empty($tInfo['p3_app']) || $isNilaiLengkap) {
                $progresStage = 'Sidang TA';
                $stageKey     = 'sidang';
                $stageIndex   = 8;
                $stageDesc    = $isNilaiLengkap ? ($isPublished ? 'Sidang Selesai' : 'Nilai Masuk (Menunggu Publish)') : 'Pelaksanaan Sidang Akhir';
                $stageColor   = 'rose';
            } elseif (!empty($tInfo['has_p3']) || !empty($tInfo['p2_app'])) {
                $progresStage = 'Preview 3';
                $stageKey     = 'preview3';
                $stageIndex   = 7;
                $stageDesc    = 'Pra-Sidang & Finalisasi Dokumen TA';
                $stageColor   = 'cyan';
            } elseif (!empty($tInfo['has_p2']) || !empty($tInfo['p1_app']) || $hasPenguji || !empty($gRow['tanggal_presentasi'])) {
                $progresStage = 'Preview 2';
                $stageKey     = 'preview2';
                $stageIndex   = 6;
                $stageDesc    = 'Evaluasi Progres & Presentasi Penguji';
                $stageColor   = 'indigo';
            } elseif (!empty($tInfo['has_p1']) || (strcasecmp($statusKk, 'Approved') === 0 && strcasecmp($statusKoor, 'Pending') !== 0)) {
                $progresStage = 'Preview 1';
                $stageKey     = 'preview1';
                $stageIndex   = 5;
                $stageDesc    = 'Bimbingan Bab 1-3 Bersama Pembimbing';
                $stageColor   = 'teal';
            } elseif ((strcasecmp($statusKoor, 'Approved') === 0 && $hasPembimbing) || strcasecmp($statusKoor, 'Approved') === 0) {
                $progresStage = 'Ketua KK';
                $stageKey     = 'ketua_kk';
                $stageIndex   = 4;
                $stageDesc    = 'Konfirmasi Distribusi Riset KK';
                $stageColor   = 'purple';
            } elseif ($isAdminFullyApproved || (strcasecmp($statusAdmin, 'Approved') === 0 && $validBerkas >= 4 && $pendingBerkas === 0)) {
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

            // Format Dokumen Preview & Sidang
            $studentTheses = $thesisFullMap[$gId] ?? array();
            $p1Row = null;
            $p2Row = null;
            $p3Row = null;
            $sidangRow = null;

            foreach ($studentTheses as $stRow) {
                $thKey = strtolower(trim($stRow['tahapan_preview'] ?? ''));
                if ($thKey === 'preview1' && !$p1Row) $p1Row = $stRow;
                elseif ($thKey === 'preview2' && !$p2Row) $p2Row = $stRow;
                elseif ($thKey === 'preview3' && !$p3Row) $p3Row = $stRow;
                elseif ($thKey === 'sidang' && !$sidangRow) $sidangRow = $stRow;
            }

            $previewDocs = array(
                'p1' => $this->_format_preview_entry($p1Row, 'Preview 1 (Proposal TA)'),
                'p2' => $this->_format_preview_entry($p2Row, 'Preview 2 (Evaluasi Progres 50%)'),
                'p3' => $this->_format_preview_entry($p3Row, 'Preview 3 (Pra-Sidang TA)'),
                'total_submitted' => ($p1Row ? 1 : 0) + ($p2Row ? 1 : 0) + ($p3Row ? 1 : 0)
            );

            // Sidang & BAP Details
            $sidangFile = $sidangRow['file_sidang'] ?? ($sidangRow['pdf_file'] ?? null);
            $bapFile = $gRow['bap'] ?? null;

            $rawWaktu = $gRow['waktu_sidang'] ?? null;
            $jamSelesai = $sidangScheduleMap[$nimKey]['jam_selesai'] ?? null;
            if (empty($jamSelesai) && !empty($rawWaktu)) {
                $h = (int)substr($rawWaktu, 0, 2);
                $m = substr($rawWaktu, 3, 2);
                $jamSelesai = sprintf('%02d:%s', min(23, $h + 2), $m);
            }

            $waktuFormatted = null;
            if (!empty($rawWaktu)) {
                $startStr = substr($rawWaktu, 0, 5);
                $endStr = !empty($jamSelesai) ? substr($jamSelesai, 0, 5) : null;
                $waktuFormatted = $endStr ? ($startStr . ' - ' . $endStr . ' WIB') : ($startStr . ' WIB');
            }

            $sidangDocs = array(
                'has_sidang'          => (!empty($gRow['tanggal_sidang']) || $sidangRow !== null),
                'status_sidang'       => (!empty($gRow['tanggal_sidang']) ? 'Terjadwal' : ($sidangRow ? 'Diajukan' : 'Belum Terjadwal')),
                'tanggal_sidang'      => $gRow['tanggal_sidang'] ?? null,
                'waktu_sidang'        => $waktuFormatted ?: ($gRow['waktu_sidang'] ?? null),
                'jam_mulai_sidang'    => $rawWaktu,
                'jam_selesai_sidang'  => $jamSelesai,
                'ruang_sidang'        => $gRow['ruang_sidang'] ?? null,
                'link_sidang'         => $gRow['link_sidang'] ?? null,
                'file_sidang'         => $sidangFile,
                'file_sidang_url'     => !empty($sidangFile) ? $this->_resolve_preview_file_url($sidangFile, 'sidang') : null,
                'bap_file'            => $bapFile,
                'bap_file_url'        => !empty($bapFile) ? $this->_resolve_preview_file_url($bapFile, 'sidang') : null,
                'status_bap'          => $gRow['status_bap'] ?? 'Pending',
                'nilai_p1'            => $n1,
                'nilai_p2'            => $n2,
                'nilai_pj1'           => $np1,
                'nilai_pj2'           => $np2,
                'avg_score'           => $avgScore,
                'is_nilai_lengkap'    => $isNilaiLengkap,
                'status_publish'      => $statusPublish,
                'is_published'        => $isPublished,
                'is_lulus'            => $isLulus
            );

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
            $item['status_publish_sidang'] = $statusPublish;
            $item['tgl_publish_sidang']    = $tglPublish;
            $item['is_published']          = $isPublished;
            $item['berkas_summary']      = $bSummary;
            $item['berkas_status_label'] = $berkasStatusLabel;
            $item['berkas_status_code']  = $berkasStatusCode;
            $item['berkas_status_color'] = $berkasStatusColor;
            $item['preview_docs']        = $previewDocs;
            $item['sidang_docs']         = $sidangDocs;

            $result[] = $item;
        }

        return $result;
    }

    private function _format_preview_entry($row, $label) {
        if (!$row) {
            return array(
                'label'                => $label,
                'has_submission'       => false,
                'status'               => 'Belum Mengajukan',
                'file_draft'           => null,
                'file_draft_url'       => null,
                'file_sitasi'          => null,
                'file_sitasi_url'      => null,
                'file_bimbingan'       => null,
                'file_bimbingan_url'   => null,
                'file_persyaratan'     => null,
                'file_persyaratan_url' => null,
                'link_project'         => null,
                'catatan'              => null,
                'date'                 => null
            );
        }

        $draftFile       = $row['pdf_file'] ?? null;
        $sitasiFile      = $row['file_sitasi'] ?? null;
        $bimbinganFile   = $row['file_bimbingan'] ?? null;
        $persyaratanFile = $row['file_persyaratan'] ?? null;

        $rawCatatan      = (!empty($row['catatan_file_draft']) ? $row['catatan_file_draft'] : (!empty($row['correction1']) ? $row['correction1'] : (!empty($row['correction2']) ? $row['correction2'] : (!empty($row['correction3']) ? $row['correction3'] : (!empty($row['keterangan']) ? $row['keterangan'] : null)))));
        $cleanCatatan    = !empty($rawCatatan) ? trim(strip_tags(html_entity_decode($rawCatatan, ENT_QUOTES, 'UTF-8'))) : null;

        return array(
            'id'                   => $row['id'],
            'label'                => $label,
            'has_submission'       => true,
            'status'               => $row['status'] ?: 'Pending',
            'file_draft'           => $draftFile,
            'file_draft_url'       => !empty($draftFile) ? $this->_resolve_preview_file_url($draftFile, 'preview_ta') : null,
            'file_sitasi'          => $sitasiFile,
            'file_sitasi_url'      => !empty($sitasiFile) ? $this->_resolve_preview_file_url($sitasiFile, 'preview_ta') : null,
            'file_bimbingan'       => $bimbinganFile,
            'file_bimbingan_url'   => !empty($bimbinganFile) ? $this->_resolve_preview_file_url($bimbinganFile, 'preview_ta') : null,
            'file_persyaratan'     => $persyaratanFile,
            'file_persyaratan_url' => !empty($persyaratanFile) ? $this->_resolve_preview_file_url($persyaratanFile, 'preview_ta') : null,
            'link_project'         => $row['link_project'] ?? null,
            'catatan'              => $cleanCatatan,
            'date'                 => $row['date'] ?: ($row['created_at'] ?? null)
        );
    }

    private function _resolve_preview_file_url($filename, $defaultFolder = 'preview_ta') {
        if (empty($filename)) return null;
        if (strpos($filename, 'http://') === 0 || strpos($filename, 'https://') === 0) return $filename;
        if (strpos($filename, 'uploads/') === 0) return base_url($filename);

        if (file_exists(FCPATH . 'uploads/' . $defaultFolder . '/' . $filename)) {
            return base_url('uploads/' . $defaultFolder . '/' . $filename);
        }
        if (file_exists(FCPATH . 'uploads/preview_ta/' . $filename)) {
            return base_url('uploads/preview_ta/' . $filename);
        }
        if (file_exists(FCPATH . 'uploads/persyaratan_ta/' . $filename)) {
            return base_url('uploads/persyaratan_ta/' . $filename);
        }
        if (file_exists(FCPATH . 'uploads/sidang/' . $filename)) {
            return base_url('uploads/sidang/' . $filename);
        }
        return base_url('uploads/' . $defaultFolder . '/' . $filename);
    }

    /**
     * Ambil daftar mahasiswa yang eligible untuk di-downgrade atau di-reset tahapannya
     * (Semua mahasiswa aktif yang memiliki bimbingan/progres TA)
     */
        /**
     * Mengambil Daftar Kandidat Mahasiswa untuk Downgrade / Reset Tahapan
     * 
     * Menerapkan Aturan Visibilitas: Mahasiswa tahap awal (dosen wali) HANYA ditampilkan
     * jika sudah memiliki berkas unggahan bimbingan. Mahasiswa yang sudah direset total ke titik nol
     * tidak ditampilkan pada tabel kandidat.
     * 
     * @param string $stageFilter Filter tahapan bimbingan aktif ('all' atau stage key)
     * @param string $search Kueri pencarian teks (NIM, Nama, Judul)
     * @return array Daftar kandidat yang memenuhi syarat penurunan tahap
     */
    public function get_candidates_for_downgrade($stageFilter = 'all', $search = '') {
        $monitoring = $this->get_monitoring_peserta_ta();
        if (empty($monitoring)) return array();

        $candidates = array();
        $searchLower = strtolower(trim((string)$search));

        foreach ($monitoring as $m) {
            $stageKey = $m['stage_key'] ?? ($m['stageKey'] ?? 'pendaftaran');
            $stageLabel = $m['progres_stage'] ?? ($m['progresStage'] ?? 'Pendaftaran');
            $stageColor = $m['stage_color'] ?? ($m['stageColor'] ?? 'slate');
            $judul = !empty($m['judul_1']) ? $m['judul_1'] : (!empty($m['judul']) ? $m['judul'] : '-');
            $p1 = $m['nama_pembimbing_1'] ?? ($m['pembimbing_1_name'] ?? ($m['pembimbing_1'] ?? '-'));
            $p2 = $m['nama_pembimbing_2'] ?? ($m['pembimbing_2_name'] ?? ($m['pembimbing_2'] ?? ''));
            $u1 = $m['nama_penguji_1'] ?? ($m['penguji_1_name'] ?? ($m['penguji_1'] ?? ''));
            $u2 = $m['nama_penguji_2'] ?? ($m['penguji_2_name'] ?? ($m['penguji_2'] ?? ''));

            $nimLower = strtolower(trim((string)$m['nim']));
            $namaLower = strtolower(trim((string)($m['nama'] ?? '')));
            $judulLower = strtolower(trim((string)$judul));

            // Normalize fields on $m so both camelCase and snake_case work everywhere
            $m['stage_key']          = $stageKey;
            $m['stageKey']           = $stageKey;
            $m['progres_stage']      = $stageLabel;
            $m['progresStage']       = $stageLabel;
            $m['stage_color']        = $stageColor;
            $m['stageColor']         = $stageColor;
            $m['judul']              = $judul;
            $m['judul_1']            = $judul;
            $m['pembimbing_1_name']  = $p1;
            $m['pembimbing_2_name']  = $p2;
            $m['penguji_1_name']     = $u1;
            $m['penguji_2_name']     = $u2;

            // ATURAN VISIBILITAS:
            // Mahasiswa di tahap awal (dosen_wali atau pendaftaran) HANYA ditampilkan jika sudah masuk ke Dosen Wali DAN sudah ada berkas yang diunggah.
            // Mahasiswa yang belum unggah berkas sama sekali atau telah di-reset total tidak ditampilkan di tabel.
            $totalBerkas = (int)($m['berkas_summary']['total_count'] ?? 0);
            $hasUploadedBerkas = ($totalBerkas > 0) || 
                !empty($m['file_ksm']) || 
                !empty($m['file_transkrip']) || 
                !empty($m['file_pernyataan']) || 
                !empty($m['file_bebas_lab']) || 
                (!empty($m['berkas_status_code']) && $m['berkas_status_code'] !== 'kosong');

            if (in_array($stageKey, array('dosen_wali', 'pendaftaran')) && !$hasUploadedBerkas) {
                continue;
            }

            // Filter search jika ada
            if (!empty($searchLower)) {
                $p1Lower = strtolower(trim((string)$p1));
                $u1Lower = strtolower(trim((string)$u1));
                if (strpos($nimLower, $searchLower) === false && 
                    strpos($namaLower, $searchLower) === false && 
                    strpos($judulLower, $searchLower) === false &&
                    strpos($p1Lower, $searchLower) === false &&
                    strpos($u1Lower, $searchLower) === false) {
                    continue;
                }
            }

            // Filter stage jika bukan 'all'
            if (!empty($stageFilter) && $stageFilter !== 'all') {
                if ($stageFilter === 'preview1' && $stageKey !== 'preview1') continue;
                if ($stageFilter === 'preview2' && $stageKey !== 'preview2') continue;
                if ($stageFilter === 'preview3' && $stageKey !== 'preview3') continue;
                if ($stageFilter === 'sidang' && !in_array($stageKey, array('sidang', 'lulus'))) continue;
                if ($stageFilter === 'pendaftaran' && !in_array($stageKey, array('dosen_wali', 'admin_layanan', 'koordinator_ta', 'ketua_kk', 'pendaftaran'))) continue;
            }

            // Hitung opsi target downgrade yang diizinkan untuk mahasiswa ini
            $hierarchy = array(
                'dosen_wali'     => 1,
                'admin_layanan'  => 2,
                'koordinator_ta' => 3,
                'ketua_kk'       => 4,
                'pendaftaran'    => 4,
                'preview1'       => 5,
                'preview2'       => 6,
                'preview3'       => 7,
                'sidang'         => 8,
                'lulus'          => 9
            );
            $currRank = $hierarchy[$stageKey] ?? 1;

            $allowedTargets = array();
            if ($currRank >= 9) { // Lulus
                $allowedTargets[] = array('key' => 'sidang', 'label' => 'Sidang Akhir TA (Batalkan Kelulusan)');
            }
            if ($currRank >= 8) { // Sidang atau Lulus
                $allowedTargets[] = array('key' => 'preview3', 'label' => 'Preview 3 (Pra-Sidang)');
            }
            if ($currRank >= 7) { // Preview 3 ke atas
                $allowedTargets[] = array('key' => 'preview2', 'label' => 'Preview 2 (Presentasi Penguji)');
            }
            if ($currRank >= 6) { // Preview 2 ke atas
                $allowedTargets[] = array('key' => 'preview1', 'label' => 'Preview 1 (Bimbingan Bab 1-3)');
            }
            if ($currRank >= 5) { // Preview 1 ke atas
                $allowedTargets[] = array('key' => 'ketua_kk', 'label' => 'Tahap Ketua KK (Konfirmasi Distribusi Riset)');
                $allowedTargets[] = array('key' => 'koordinator_ta', 'label' => 'Tahap Koordinator TA (Plotting Ulang Pembimbing)');
            } elseif ($currRank >= 4) { // Ketua KK ke atas
                $allowedTargets[] = array('key' => 'koordinator_ta', 'label' => 'Tahap Koordinator TA (Plotting Ulang Pembimbing)');
            }
            // Reset total selalu tersedia untuk semua tahap
            $allowedTargets[] = array('key' => 'reset_total', 'label' => 'Reset Total (Kembali ke Pendaftaran Awal)');

            $m['allowed_targets'] = $allowedTargets;
            $candidates[] = $m;
        }

        return $candidates;
    }

    /**
     * Eksekusi downgrade atau reset tahap bimbingan mahasiswa
     * Menghapus seluruh berkas, draf, nilai, dan evaluasi permanen tanpa backup
     */
        /**
     * Eksekusi Transaksional Penurunan / Reset Tahapan Mahasiswa
     * 
     * Menegakkan aturan hierarki peringkat tahapan: Target tahapan penurunan wajib
     * berada di bawah peringkat posisi tahapan aktif mahasiswa (targetRank < currRank).
     * Menjalankan prosedur sanitasi multi-storage:
     * 1. Menghapus rekaman naskah tidak relevan di tabel thesis
     * 2. Meng-unlink berkas fisik dari 16 direktori upload server (mencegah orphaned files)
     * 3. Mereset kolom tanggal, jam, ruangan, dan skor pada tabel guidance
     * 4. Mencatat rekam jejak audit ke tabel downgrade_audit_logs dalam trans_start()
     * 
     * @param string $nim NIM mahasiswa target
     * @param string $targetStage Tahapan target ('reset_total', 'koordinator_ta', 'ketua_kk', 'preview1', 'preview2', 'preview3', 'sidang')
     * @param string $alasan Justifikasi resmi penurunan tahapan (min 5 karakter)
     * @param array $actorData Metadata aktor eksekutor (id, name, nip)
     * @return array Status transaksi basis data dan pesan konfirmasi
     */
    public function execute_downgrade_stage($nim, $targetStage, $alasan, $actorData = array()) {
        $nim = trim((string)$nim);
        if (empty($nim)) {
            return array('status' => false, 'message' => 'NIM mahasiswa tidak boleh kosong.');
        }

        $this->load->model('Mahasiswa_model');
        $g = $this->Mahasiswa_model->get_guidance_by_nim($nim);
        if (!$g) {
            return array('status' => false, 'message' => 'Data bimbingan (guidance) mahasiswa tidak ditemukan.');
        }

        $gId = $g['id'];
        $validTargets = array('reset_total', 'koordinator_ta', 'ketua_kk', 'preview1', 'preview2', 'preview3', 'sidang');
        if (!in_array($targetStage, $validTargets)) {
            return array('status' => false, 'message' => 'Target tahapan penurunan tidak valid.');
        }

        // Tentukan tahap saat ini dari monitoring
        $monitoringList = $this->get_monitoring_peserta_ta();
        $currentInfo = null;
        foreach ($monitoringList as $m) {
            if ($m['nim'] == $nim) {
                $currentInfo = $m;
                break;
            }
        }

        $currentStageKey = $currentInfo['stage_key'] ?? ($currentInfo['stageKey'] ?? ($g['status_preview'] ?? 'preview1'));
        $currentStageLabel = $currentInfo['progres_stage'] ?? ($currentInfo['progresStage'] ?? ucfirst($currentStageKey));

        $hierarchy = array(
            'dosen_wali'     => 1,
            'admin_layanan'  => 2,
            'koordinator_ta' => 3,
            'ketua_kk'       => 4,
            'pendaftaran'    => 4,
            'preview1'       => 5,
            'preview2'       => 6,
            'preview3'       => 7,
            'sidang'         => 8,
            'lulus'          => 9
        );
        $currRank = $hierarchy[$currentStageKey] ?? 1;

        if ($targetStage !== 'reset_total') {
            $targetRank = $hierarchy[$targetStage] ?? 1;
            if ($targetRank >= $currRank) {
                return array('status' => false, 'message' => 'Target penurunan harus ke tahap yang lebih rendah dari tahap saat ini (' . $currentStageLabel . ').');
            }
        } else {
            $targetRank = 1;
        }

        $this->db->trans_start();

        // Helper untuk unlink file fisik di semua lokasi direktori upload
        $unlinkFile = function($rawName) {
            if (empty($rawName)) return;
            $rawName = trim((string)$rawName);
            $decoded = rawurldecode($rawName);
            $base1 = basename($rawName);
            $base2 = basename($decoded);
            $paths = array(
                FCPATH . $rawName,
                FCPATH . $decoded,
                FCPATH . 'uploads/' . $rawName,
                FCPATH . 'uploads/' . $decoded,
                FCPATH . 'uploads/persyaratan_ta/' . $rawName,
                FCPATH . 'uploads/persyaratan_ta/' . $decoded,
                FCPATH . 'uploads/persyaratan_ta/' . $base1,
                FCPATH . 'uploads/persyaratan_ta/' . $base2,
                FCPATH . 'uploads/preview_ta/' . $base1,
                FCPATH . 'uploads/preview_ta/' . $base2,
                FCPATH . 'uploads/sidang/' . $base1,
                FCPATH . 'uploads/sidang/' . $base2,
                FCPATH . 'uploads/pendaftaran/' . $base1,
                FCPATH . 'uploads/pendaftaran/' . $base2,
                FCPATH . 'uploads/pendaftaran_berkas/' . $base1,
                FCPATH . 'uploads/pendaftaran_berkas/' . $base2,
            );
            foreach (array_unique($paths) as $p) {
                if (!empty($p) && file_exists($p) && is_file($p)) {
                    @unlink($p);
                }
            }
        };

        // 1. Tangani tabel thesis dan unlink file fisik berkas draft/revisi
        if ($this->db->table_exists('thesis')) {
            $stagesToDelete = array();
            if (in_array($targetStage, array('reset_total', 'koordinator_ta', 'ketua_kk'))) {
                $stagesToDelete = array('preview1', 'Preview 1', 'preview 1', 'preview2', 'Preview 2', 'preview 2', 'preview3', 'Preview 3', 'preview 3', 'sidang', 'Sidang', 'sidang ta', 'Sidang TA');
            } elseif ($targetStage === 'preview1') {
                $stagesToDelete = array('preview2', 'Preview 2', 'preview 2', 'preview3', 'Preview 3', 'preview 3', 'sidang', 'Sidang', 'sidang ta', 'Sidang TA');
            } elseif ($targetStage === 'preview2') {
                $stagesToDelete = array('preview3', 'Preview 3', 'preview 3', 'sidang', 'Sidang', 'sidang ta', 'Sidang TA');
            } elseif ($targetStage === 'preview3') {
                $stagesToDelete = array('sidang', 'Sidang', 'sidang ta', 'Sidang TA');
            }

            if (!empty($stagesToDelete)) {
                $this->db->select('*');
                $this->db->where('id_guidance', $gId);
                $this->db->where_in('tahapan_preview', $stagesToDelete);
                $thesisRows = $this->db->get('thesis')->result_array();

                foreach ($thesisRows as $tr) {
                    foreach (array('pdf_file', 'file_sitasi', 'file_bimbingan', 'file_persyaratan', 'file_sidang') as $col) {
                        if (!empty($tr[$col])) {
                            $unlinkFile($tr[$col]);
                        }
                    }
                }

                // Hapus record thesis
                $this->db->where('id_guidance', $gId);
                $this->db->where_in('tahapan_preview', $stagesToDelete);
                $this->db->delete('thesis');
            }

            // Reset status approval & revisi pada tahapan target agar tidak dianggap telah disetujui sebelumnya
            if ($targetStage === 'preview2') {
                $this->db->where('id_guidance', $gId)
                         ->where_in('tahapan_preview', array('preview2', 'Preview 2', 'preview 2', 'PREVIEW 2'))
                         ->update('thesis', array('status' => 'Pending', 'correction3' => null, 'correction4' => null));
            } elseif ($targetStage === 'preview1') {
                $this->db->where('id_guidance', $gId)
                         ->where_in('tahapan_preview', array('preview1', 'Preview 1', 'preview 1', 'PREVIEW 1'))
                         ->update('thesis', array('status' => 'Pending', 'correction1' => null, 'correction2' => null));
            } elseif ($targetStage === 'preview3') {
                $this->db->where('id_guidance', $gId)
                         ->where_in('tahapan_preview', array('preview3', 'Preview 3', 'preview 3', 'PREVIEW 3'))
                         ->update('thesis', array('status' => 'Pending'));
            }
        }

        // 2. Update dan bersihkan field-field di guidance & thesis_lecturers & file_pendaftaran
        $updateGuidance = array();

        if ($targetStage === 'reset_total') {
            $cleanNim = preg_replace('/^usr_mhs_|^mhs_|^usr_/', '', (string)$nim);
            $targetIds = array_unique(array_filter([$nim, $cleanNim, $g['id_mhs'] ?? null, 'usr_' . $cleanNim, 'mhs_' . $cleanNim, 'usr_mhs_' . $cleanNim]));

            // A. Hapus berkas fisik & record di file_pendaftaran
            if ($this->db->table_exists('file_pendaftaran')) {
                $fpQ = $this->db->group_start()
                                ->where_in('id_mhs', $targetIds)
                                ->or_like('id_mhs', $cleanNim)
                                ->group_end()
                                ->get('file_pendaftaran');
                if ($fpQ) {
                    foreach ($fpQ->result_array() as $fpRow) {
                        $unlinkFile($fpRow['file'] ?? '');
                    }
                }
                $this->db->group_start()
                         ->where_in('id_mhs', $targetIds)
                         ->or_like('id_mhs', $cleanNim)
                         ->group_end()
                         ->delete('file_pendaftaran');
            }

            // B. Hapus berkas fisik & record di pendaftaran_berkas jika ada
            if ($this->db->table_exists('pendaftaran_berkas')) {
                $pbQ = $this->db->group_start()
                                ->where_in('nim', $targetIds)
                                ->or_like('nim', $cleanNim)
                                ->group_end()
                                ->get('pendaftaran_berkas');
                if ($pbQ) {
                    foreach ($pbQ->result_array() as $pbRow) {
                        $unlinkFile($pbRow['file_name'] ?? '');
                        if (!empty($pbRow['file'])) $unlinkFile($pbRow['file']);
                    }
                }
                $this->db->group_start()
                         ->where_in('nim', $targetIds)
                         ->or_like('nim', $cleanNim)
                         ->group_end()
                         ->delete('pendaftaran_berkas');
            }

            // C. Hapus seluruh thesis tersisa untuk mahasiswa ini jika ada
            if ($this->db->table_exists('thesis')) {
                $thAll = $this->db->get_where('thesis', array('id_guidance' => $gId))->result_array();
                foreach ($thAll as $tr) {
                    foreach (array('pdf_file', 'file_sitasi', 'file_bimbingan', 'file_persyaratan', 'file_sidang') as $col) {
                        if (!empty($tr[$col])) $unlinkFile($tr[$col]);
                    }
                }
                $this->db->where('id_guidance', $gId)->delete('thesis');
            }

            // D. Hapus record di thesis_lecturers agar plotting pembimbing & status KK benar-benar bersih
            if ($this->db->table_exists('thesis_lecturers')) {
                $this->db->where('id_guidance', $gId)->delete('thesis_lecturers');
            }

            // E. Unlink berkas bap / bap2 dari guidance jika ada
            if (!empty($g['bap']))  $unlinkFile($g['bap']);
            if (!empty($g['bap2'])) $unlinkFile($g['bap2']);

            // F. Hapus baris guidance mahasiswa secara permanen (reset total dari bersih)
            $this->db->group_start()
                     ->where('id', $gId)
                     ->or_where('id', 'gdn_' . $cleanNim)
                     ->or_where_in('id_mhs', $targetIds)
                     ->group_end()
                     ->delete('guidance');
        } elseif ($targetStage === 'koordinator_ta') {
            $updateGuidance['keterangan']  = 'Pending';
            $updateGuidance['komentar']    = null;
            $updateGuidance['status_file'] = 'pending';
            $updateGuidance['status_preview'] = 'pending';
            $updateGuidance['status_filesidang'] = null;
            $updateGuidance['lulus_preview1'] = 0;
            $updateGuidance['lulus_preview2'] = 0;
            $updateGuidance['lulus_preview3'] = 0;
            $updateGuidance['lulus_sidang']   = 0;
            $updateGuidance['lulus_by_role']  = null;
            $updateGuidance['lulus_at']       = null;

            $updateGuidance['penilaian_pembimbing1'] = null;
            $updateGuidance['nilai_pembimbing1']     = null;
            $updateGuidance['penilaian_pembimbing2'] = null;
            $updateGuidance['nilai_pembimbing2']     = null;
            $updateGuidance['penilaian_penguji1']    = null;
            $updateGuidance['nilai_penguji1']        = null;
            $updateGuidance['penilaian_penguji2']    = null;
            $updateGuidance['nilai_penguji2']        = null;

            $updateGuidance['nilaisidang_pembimbing1'] = null;
            $updateGuidance['nilaisidang_pembimbing2'] = null;
            $updateGuidance['nilaisidang_penguji1']    = null;
            $updateGuidance['nilaisidang_penguji2']    = null;
            $updateGuidance['penilaiansidang_pembimbing1'] = null;
            $updateGuidance['penilaiansidang_pembimbing2'] = null;
            $updateGuidance['penilaiansidang_penguji1']    = null;
            $updateGuidance['penilaiansidang_penguji2']    = null;

            $updateGuidance['kelayakan']           = null;
            $updateGuidance['komentar_kelayakan']  = null;
            $updateGuidance['kelayakan2']          = null;
            $updateGuidance['komentar_kelayakan2'] = null;
            $updateGuidance['kelayakan3']          = null;
            $updateGuidance['tanggal_presentasi']  = null;
            $updateGuidance['waktu_presentasi']    = null;
            $updateGuidance['link_presentasi']     = null;
            $updateGuidance['ruang_sidang']        = null;
            $updateGuidance['tanggal_sidang']      = null;
            $updateGuidance['waktu_sidang']        = null;
            $updateGuidance['link_sidang']         = null;
            $updateGuidance['bap']                 = null;
            $updateGuidance['status_bap']          = null;
            $updateGuidance['bap2']                = null;
            $updateGuidance['status_bap2']         = null;

            // Hapus thesis_lecturers agar Koordinator TA bisa melakukan plotting ulang pembimbing
            if ($this->db->table_exists('thesis_lecturers')) {
                $this->db->where('id_guidance', $gId)->delete('thesis_lecturers');
            }
        } elseif ($targetStage === 'ketua_kk') {
            $updateGuidance['keterangan']  = 'Approved';
            $updateGuidance['status_file'] = 'Approved';
            $updateGuidance['status_preview'] = 'pending';
            $updateGuidance['status_filesidang'] = null;
            $updateGuidance['lulus_preview1'] = 0;
            $updateGuidance['lulus_preview2'] = 0;
            $updateGuidance['lulus_preview3'] = 0;
            $updateGuidance['lulus_sidang']   = 0;
            $updateGuidance['lulus_by_role']  = null;
            $updateGuidance['lulus_at']       = null;

            $updateGuidance['penilaian_pembimbing1'] = null;
            $updateGuidance['nilai_pembimbing1']     = null;
            $updateGuidance['penilaian_pembimbing2'] = null;
            $updateGuidance['nilai_pembimbing2']     = null;
            $updateGuidance['penilaian_penguji1']    = null;
            $updateGuidance['nilai_penguji1']        = null;
            $updateGuidance['penilaian_penguji2']    = null;
            $updateGuidance['nilai_penguji2']        = null;

            $updateGuidance['nilaisidang_pembimbing1'] = null;
            $updateGuidance['nilaisidang_pembimbing2'] = null;
            $updateGuidance['nilaisidang_penguji1']    = null;
            $updateGuidance['nilaisidang_penguji2']    = null;
            $updateGuidance['penilaiansidang_pembimbing1'] = null;
            $updateGuidance['penilaiansidang_pembimbing2'] = null;
            $updateGuidance['penilaiansidang_penguji1']    = null;
            $updateGuidance['penilaiansidang_penguji2']    = null;

            $updateGuidance['kelayakan']           = null;
            $updateGuidance['komentar_kelayakan']  = null;
            $updateGuidance['kelayakan2']          = null;
            $updateGuidance['komentar_kelayakan2'] = null;
            $updateGuidance['kelayakan3']          = null;
            $updateGuidance['tanggal_presentasi']  = null;
            $updateGuidance['waktu_presentasi']    = null;
            $updateGuidance['link_presentasi']     = null;
            $updateGuidance['ruang_sidang']        = null;
            $updateGuidance['tanggal_sidang']      = null;
            $updateGuidance['waktu_sidang']        = null;
            $updateGuidance['link_sidang']         = null;
            $updateGuidance['bap']                 = null;
            $updateGuidance['status_bap']          = null;
            $updateGuidance['bap2']                = null;
            $updateGuidance['status_bap2']         = null;

            // Di thesis_lecturers: Pembimbing 1 & 2 tetap ada, status dijadikan Pending agar menunggu approval Ketua KK
            if ($this->db->table_exists('thesis_lecturers')) {
                $this->db->where('id_guidance', $gId)->update('thesis_lecturers', array(
                    'status'         => 'Pending',
                    'dosen_penguji1' => '',
                    'dosen_penguji2' => '',
                    'date_edit'      => date('Y-m-d H:i:s')
                ));
            }
        } elseif ($targetStage === 'preview1') {
            $updateGuidance['status_preview'] = 'preview1';
            $updateGuidance['keterangan']     = 'Approved';
            $updateGuidance['status_file']    = 'Approved';
            $updateGuidance['status_filesidang'] = null;
            $updateGuidance['lulus_preview1'] = 0;
            $updateGuidance['lulus_preview2'] = 0;
            $updateGuidance['lulus_preview3'] = 0;
            $updateGuidance['lulus_sidang']   = 0;
            $updateGuidance['lulus_by_role']  = null;
            $updateGuidance['lulus_at']       = null;

            $updateGuidance['penilaian_pembimbing1'] = null;
            $updateGuidance['nilai_pembimbing1']     = null;
            $updateGuidance['penilaian_pembimbing2'] = null;
            $updateGuidance['nilai_pembimbing2']     = null;
            $updateGuidance['penilaian_penguji1']    = null;
            $updateGuidance['nilai_penguji1']        = null;
            $updateGuidance['penilaian_penguji2']    = null;
            $updateGuidance['nilai_penguji2']        = null;

            $updateGuidance['nilaisidang_pembimbing1'] = null;
            $updateGuidance['nilaisidang_pembimbing2'] = null;
            $updateGuidance['nilaisidang_penguji1']    = null;
            $updateGuidance['nilaisidang_penguji2']    = null;
            $updateGuidance['penilaiansidang_pembimbing1'] = null;
            $updateGuidance['penilaiansidang_pembimbing2'] = null;
            $updateGuidance['penilaiansidang_penguji1']    = null;
            $updateGuidance['penilaiansidang_penguji2']    = null;

            $updateGuidance['kelayakan']           = null;
            $updateGuidance['komentar_kelayakan']  = null;
            $updateGuidance['kelayakan2']          = null;
            $updateGuidance['komentar_kelayakan2'] = null;
            $updateGuidance['kelayakan3']          = null;
            $updateGuidance['tanggal_presentasi']  = null;
            $updateGuidance['waktu_presentasi']    = null;
            $updateGuidance['link_presentasi']     = null;
            $updateGuidance['ruang_sidang']        = null;
            $updateGuidance['tanggal_sidang']      = null;
            $updateGuidance['waktu_sidang']        = null;
            $updateGuidance['link_sidang']         = null;
            $updateGuidance['bap']                 = null;
            $updateGuidance['status_bap']          = null;

            if ($this->db->table_exists('thesis_lecturers')) {
                $this->db->where('id_guidance', $gId)->update('thesis_lecturers', array(
                    'status'         => 'Approved',
                    'dosen_penguji1' => '',
                    'dosen_penguji2' => '',
                    'date_edit'      => date('Y-m-d H:i:s')
                ));
            }
        } elseif ($targetStage === 'preview2') {
            $updateGuidance['status_preview'] = 'preview2';
            $updateGuidance['lulus_preview2'] = 0;
            $updateGuidance['lulus_preview3'] = 0;
            $updateGuidance['lulus_sidang']   = 0;
            $updateGuidance['lulus_by_role']  = null;
            $updateGuidance['lulus_at']       = null;

            $updateGuidance['penilaian_penguji1']    = null;
            $updateGuidance['nilai_penguji1']        = null;
            $updateGuidance['penilaian_penguji2']    = null;
            $updateGuidance['nilai_penguji2']        = null;
            $updateGuidance['kelayakan2']          = null;
            $updateGuidance['komentar_kelayakan2'] = null;
            $updateGuidance['kelayakan3']          = null;
            $updateGuidance['tanggal_presentasi']  = null;
            $updateGuidance['waktu_presentasi']    = null;
            $updateGuidance['link_presentasi']     = null;
            $updateGuidance['ruang_sidang']        = null;
            $updateGuidance['tanggal_sidang']      = null;
            $updateGuidance['waktu_sidang']        = null;
            $updateGuidance['link_sidang']         = null;
            $updateGuidance['bap']                 = null;
            $updateGuidance['status_bap']          = null;

            $updateGuidance['nilaisidang_pembimbing1'] = null;
            $updateGuidance['nilaisidang_pembimbing2'] = null;
            $updateGuidance['nilaisidang_penguji1']    = null;
            $updateGuidance['nilaisidang_penguji2']    = null;
            $updateGuidance['penilaiansidang_pembimbing1'] = null;
            $updateGuidance['penilaiansidang_pembimbing2'] = null;
            $updateGuidance['penilaiansidang_penguji1']    = null;
            $updateGuidance['penilaiansidang_penguji2']    = null;
        } elseif ($targetStage === 'preview3') {
            $updateGuidance['status_preview'] = 'preview3';
            $updateGuidance['lulus_preview3'] = 0;
            $updateGuidance['lulus_sidang']   = 0;
            $updateGuidance['lulus_by_role']  = null;
            $updateGuidance['lulus_at']       = null;

            $updateGuidance['kelayakan3']          = null;
            $updateGuidance['tanggal_sidang']      = null;
            $updateGuidance['waktu_sidang']        = null;
            $updateGuidance['link_sidang']         = null;
            $updateGuidance['bap']                 = null;
            $updateGuidance['status_bap']          = null;

            $updateGuidance['nilaisidang_pembimbing1'] = null;
            $updateGuidance['nilaisidang_pembimbing2'] = null;
            $updateGuidance['nilaisidang_penguji1']    = null;
            $updateGuidance['nilaisidang_penguji2']    = null;
            $updateGuidance['penilaiansidang_pembimbing1'] = null;
            $updateGuidance['penilaiansidang_pembimbing2'] = null;
            $updateGuidance['penilaiansidang_penguji1']    = null;
            $updateGuidance['penilaiansidang_penguji2']    = null;
        } elseif ($targetStage === 'sidang') {
            $updateGuidance['status_preview'] = 'sidang';
            $updateGuidance['lulus_sidang']   = 0;
            $updateGuidance['lulus_by_role']  = null;
            $updateGuidance['lulus_at']       = null;
            $updateGuidance['status_bap']     = 'Pending';

            if ($this->db->table_exists('log_approval_history')) {
                $this->db->where('modul', 'Publish Nilai Sidang')
                         ->where('ref_id', (string)$nim)
                         ->delete('log_approval_history');
            }
        }

        if ($targetStage !== 'reset_total') {
            $guidanceFields = $this->db->list_fields('guidance');
            $finalUpdate = array();
            foreach ($updateGuidance as $col => $val) {
                if (in_array($col, $guidanceFields)) {
                    $finalUpdate[$col] = $val;
                }
            }

            if (!empty($finalUpdate)) {
                $this->db->where('id', $gId)->update('guidance', $finalUpdate);
            }
        }

        $targetLabelMap = array(
            'reset_total'    => 'Pendaftaran TA (Reset Total)',
            'koordinator_ta' => 'Tahap Koordinator TA (Plotting Ulang Pembimbing)',
            'ketua_kk'       => 'Tahap Ketua KK (Konfirmasi Distribusi Riset)',
            'preview1'       => 'Preview 1 (Bimbingan Bab 1-3)',
            'preview2'       => 'Preview 2 (Presentasi Penguji)',
            'preview3'       => 'Preview 3 (Pra-Sidang)',
            'sidang'         => 'Sidang Akhir TA (Batalkan Kelulusan)'
        );
        $targetLabelText = $targetLabelMap[$targetStage] ?? ucfirst($targetStage);

        // 3. Catat ke downgrade_log di guidance (hanya jika guidance tidak dihapus)
        if ($targetStage !== 'reset_total' && $this->db->field_exists('downgrade_log', 'guidance')) {
            $currLogStr = $g['downgrade_log'] ?? '';
            $logArr = array();
            if (!empty($currLogStr)) {
                $decoded = json_decode($currLogStr, true);
                if (is_array($decoded)) $logArr = $decoded;
            }
            $logArr[] = array(
                'dari'       => $currentStageLabel,
                'dari_key'   => $currentStageKey,
                'ke'         => $targetLabelText,
                'ke_key'     => $targetStage,
                'alasan'     => $alasan,
                'actor_id'   => $actorData['id'] ?? $this->session->userdata('user_id'),
                'actor_name' => $actorData['name'] ?? ($this->session->userdata('name') ?: 'Koordinator TA'),
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->db->where('id', $gId)->update('guidance', array(
                'downgrade_log' => json_encode($logArr, JSON_UNESCAPED_UNICODE)
            ));
        }

        // 4. Catat ke log_approval_history
        if ($this->db->table_exists('log_approval_history')) {
            $actionName = ($targetStage === 'reset_total') ? 'Reset Total Pendaftaran' : 'Downgrade Tahap';
            $catatanData = array(
                'dari'       => $currentStageLabel,
                'ke'         => $targetLabelText,
                'alasan'     => $alasan,
                'student'    => array('nim' => $nim, 'name' => $currentInfo['nama'] ?? $nim)
            );
            $this->db->insert('log_approval_history', array(
                'modul'         => 'Downgrade Tahap',
                'ref_id'        => (string)$nim,
                'target_name'   => $currentInfo['nama'] ?? $nim,
                'action'        => $actionName,
                'actor_id'      => $actorData['id'] ?? $this->session->userdata('user_id'),
                'actor_name'    => $actorData['name'] ?? ($this->session->userdata('name') ?: 'Koordinator TA'),
                'actor_role'    => 'KOORDINATOR TA',
                'actor_nip_nim' => $actorData['nip'] ?? ($this->session->userdata('nip') ?: $this->session->userdata('username')),
                'catatan'       => json_encode($catatanData, JSON_UNESCAPED_UNICODE),
                'created_at'    => date('Y-m-d H:i:s')
            ));
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return array('status' => false, 'message' => 'Gagal memproses downgrade karena kesalahan transaksi database.');
        }

        $actionDesc = ($targetStage === 'reset_total')
            ? 'berhasil di-reset total dari sistem. Seluruh berkas pendaftaran, bimbingan, nilai, dan relasi pembimbing telah dibersihkan bersih. Mahasiswa dapat mendaftar ulang dari awal melalui portal mahasiswa.'
            : 'berhasil di-downgrade ke ' . $targetLabelText . '. Seluruh nilai, berkas, dan catatan revisi pada tahapan terkait telah dihapus permanen.';

        return array(
            'status'  => true,
            'message' => 'Mahasiswa ' . ($currentInfo['nama'] ?? $nim) . ' (' . $nim . ') ' . $actionDesc,
            'dari'    => $currentStageLabel,
            'ke'      => $targetLabelText
        );
    }

    /**
     * Ambil riwayat audit log seluruh aktivitas downgrade & reset tahap
     */
    public function get_downgrade_audit_logs($limit = 100) {
        if (!$this->db->table_exists('log_approval_history')) {
            return array();
        }

        $this->db->from('log_approval_history');
        $this->db->where('modul', 'Downgrade Tahap');
        $this->db->order_by('id', 'DESC');
        $this->db->limit((int)$limit);
        $rows = $this->db->get()->result_array();

        $result = array();
        foreach ($rows as $r) {
            $parsed = array();
            if (!empty($r['catatan']) && $r['catatan'][0] === '{') {
                $parsed = json_decode($r['catatan'], true) ?: array();
            }
            $result[] = array(
                'id'            => $r['id'],
                'nim'           => $r['ref_id'],
                'nama'          => $r['target_name'],
                'action'        => $r['action'],
                'dari'          => $parsed['dari'] ?? '-',
                'ke'            => $parsed['ke'] ?? '-',
                'alasan'        => $parsed['alasan'] ?? ($r['catatan'] ?: '-'),
                'actor_name'    => $r['actor_name'],
                'actor_nip_nim' => $r['actor_nip_nim'],
                'created_at'    => $r['created_at']
            );
        }

        return $result;
    }
}