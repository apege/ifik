<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DosenTicketing_model extends CI_Model {

    private $table = 'tb_ticketing';

    public function __construct() {
        parent::__construct();
        if ($this->db->table_exists('tb_ticketing')) {
            $this->table = 'tb_ticketing';
        } elseif ($this->db->table_exists('dosen_ticketing')) {
            $this->table = 'dosen_ticketing';
        }
    }

    /**
     * Generate unique ticket code: TIK-YYYYMMDD-XXXX
     */
    public function generate_kode() {
        $datePrefix = 'TIK-' . date('Ymd') . '-';
        do {
            $randomStr = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $kode = $datePrefix . $randomStr;
            $this->db->group_start();
            $this->db->where('id', $kode);
            if ($this->db->field_exists('kode_tiket', $this->table)) {
                $this->db->or_where('kode_tiket', $kode);
            }
            $this->db->group_end();
            $exists = $this->db->count_all_results($this->table);
        } while ($exists > 0);

        return $kode;
    }

    /**
     * Insert new ticket mapping to tb_ticketing or dosen_ticketing
     */
    public function insert($data) {
        $kode = !empty($data['kode_tiket']) ? $data['kode_tiket'] : $this->generate_kode();

        if ($this->table === 'tb_ticketing') {
            // Pemetaan data ke kolom tabel asli tb_ticketing:
            // id, id_user, nama, email, ruangan, unit, kategori, isi_ticketing, tgl_ticketing, status, file_pendukung, keterangan
            $statusMapped = 'Dikirim';
            if (isset($data['status'])) {
                $rawSt = trim($data['status']);
                if ($rawSt === 'Sedang Diproses' || $rawSt === 'Diproses') {
                    $statusMapped = 'Sedang Diproses';
                } elseif ($rawSt === 'Closed' || $rawSt === 'Selesai' || $rawSt === 'Ditutup') {
                    $statusMapped = 'Closed';
                }
            }

            // Dapatkan email pengirim jika belum ada di data
            $userEmail = '';
            if (!empty($data['email'])) {
                $userEmail = $data['email'];
            } elseif (!empty($data['id_user'])) {
                $u = $this->db->get_where('user', ['id' => $data['id_user']])->row();
                if ($u && !empty($u->email)) $userEmail = $u->email;
            }

            $isi = '';
            if (!empty($data['subjek'])) {
                $isi .= "<strong>" . strip_tags($data['subjek']) . "</strong><br>";
            }
            $isi .= ($data['deskripsi'] ?? '');
            if (!empty($data['custom_fields_data'])) {
                $cf = json_decode($data['custom_fields_data'], true);
                if (is_array($cf)) {
                    $isi .= "<br><br><strong>Detail Tambahan:</strong><ul>";
                    foreach ($cf as $item) {
                        $isi .= "<li>" . htmlspecialchars($item['label'] ?? '') . ": " . htmlspecialchars($item['value'] ?? '') . "</li>";
                    }
                    $isi .= "</ul>";
                }
            }

            $targetUnit = !empty($data['unit_tujuan']) ? $data['unit_tujuan'] : (!empty($data['tujuan_penerima']) ? $data['tujuan_penerima'] : (!empty($data['unit_terkait']) ? $data['unit_terkait'] : (!empty($data['unit']) ? $data['unit'] : 'Laboratorium Bengkel dan Studio')));
            $tujuanPenerima = $targetUnit;
            $unitTerkait    = $targetUnit;

            $prioritas = !empty($data['prioritas']) ? $data['prioritas'] : 'Sedang';
            $metaTags = '[CREATED:' . date('Y-m-d H:i:s') . '][PRIORITAS:' . $prioritas . ']';

            $insertData = [
                'id'              => $kode,
                'id_user'         => (string)($data['id_user'] ?? $data['nidn'] ?? '0'),
                'nama'            => $data['nama_dosen'] ?? ($data['nama'] ?? 'Dosen'),
                'email'           => $userEmail ?: '-',
                'ruangan'         => $data['ruangan'] ?? '-',
                'unit'            => $tujuanPenerima,
                'tujuan_penerima' => $tujuanPenerima,
                'unit_terkait'    => $unitTerkait,
                'kategori'        => $data['kategori'] ?? 'Umum',
                'prioritas'       => $prioritas,
                'isi_ticketing'   => $isi,
                'tgl_ticketing'   => date('Y-m-d H:i:s'),
                'tgl_diproses'    => '1970-01-01 00:00:00',
                'tgl_closed'      => '1970-01-01 00:00:00',
                'status'          => $statusMapped,
                'file_pendukung'  => $data['lampiran'] ?? ($data['file_pendukung'] ?? ''),
                'keterangan'      => $metaTags
            ];

            // Defensive filtering: hanya masukkan field yang benar-benar ada di tabel (mencegah error jika schema di NAS berbeda)
            $existingFields = $this->db->list_fields('tb_ticketing');
            if (!empty($existingFields)) {
                $insertData = array_intersect_key($insertData, array_flip($existingFields));
            }

            $this->db->insert('tb_ticketing', $insertData);
            return $kode;
        } else {
            if (empty($data['kode_tiket'])) {
                $data['kode_tiket'] = $kode;
            }
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Get tickets with standardized properties
     */
    public function get_tickets($user_id = null, $nidn = null) {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) {
                    $this->db->where('id_user', (string)$user_id);
                }
                if ($nidn !== null) {
                    $this->db->or_where('id_user', (string)$nidn);
                }
                $this->db->group_end();
            }
            $this->db->order_by('tgl_ticketing', 'DESC');
            $this->db->order_by('id', 'DESC');
            $raw = $this->db->get()->result();

            return array_map([$this, '_format_row'], $raw);
        }

        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start();
            $this->db->where('id_user', $user_id);
            $this->db->or_where('nidn', $nidn);
            $this->db->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ticket by ID or kode_tiket
     */
    public function get_by_id($id_or_kode) {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            $this->db->where('id', $id_or_kode);
            $row = $this->db->get()->row();
            return $row ? $this->_format_row($row) : null;
        }

        $this->db->from($this->table);
        if (is_numeric($id_or_kode)) {
            $this->db->where('id', (int)$id_or_kode);
        } else {
            $this->db->where('kode_tiket', $id_or_kode);
        }
        return $this->db->get()->row();
    }

    /**
     * Get ticket statistics
     */
    public function get_stats($user_id = null, $nidn = null) {
        $stats = [
            'total'    => 0,
            'menunggu' => 0,
            'diproses' => 0,
            'selesai'  => 0,
            'ditutup'  => 0
        ];

        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $stats['total'] = $this->db->count_all_results();

            // 1. Dikirim
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $this->db->where('status', 'Dikirim');
            $stats['dikirim'] = $this->db->count_all_results();
            $stats['menunggu'] = $stats['dikirim'];

            // 2. Sedang Diproses
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $this->db->where('status', 'Sedang Diproses');
            $stats['sedang_diproses'] = $this->db->count_all_results();
            $stats['diproses'] = $stats['sedang_diproses'];

            // 3. Closed
            $this->db->from('tb_ticketing');
            if ($user_id !== null || $nidn !== null) {
                $this->db->group_start();
                if ($user_id !== null) $this->db->where('id_user', (string)$user_id);
                if ($nidn !== null) $this->db->or_where('id_user', (string)$nidn);
                $this->db->group_end();
            }
            $this->db->where('status', 'Closed');
            $stats['closed'] = $this->db->count_all_results();
            $stats['selesai'] = $stats['closed'];
            $stats['ditutup'] = 0;

            return $stats;
        }

        // Total
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $stats['total'] = $this->db->count_all_results();

        // Menunggu
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Menunggu');
        $stats['menunggu'] = $this->db->count_all_results();

        // Diproses
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Diproses');
        $stats['diproses'] = $this->db->count_all_results();

        // Selesai
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Selesai');
        $stats['selesai'] = $this->db->count_all_results();

        // Ditutup
        $this->db->from($this->table);
        if ($user_id !== null && $nidn !== null) {
            $this->db->group_start()->where('id_user', $user_id)->or_where('nidn', $nidn)->group_end();
        } elseif ($user_id !== null) {
            $this->db->where('id_user', $user_id);
        } elseif ($nidn !== null) {
            $this->db->where('nidn', $nidn);
        }
        $this->db->where('status', 'Ditutup');
        $stats['ditutup'] = $this->db->count_all_results();

        return $stats;
    }

    /**
     * Get tickets routed to specific recipient role (Laboran, Kaur, Admin LAA)
     */
    public function get_respon_tickets($filterStatus = 'all', $search = '', $recipientRole = 'Kaur', $specificFilter = '') {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            
            // Recipient Role Filter
            $this->_apply_recipient_filter($recipientRole);

            // Specific Sub Filter (e.g. Prodi DKV/DI/DP/etc)
            if (!empty($specificFilter)) {
                $this->db->group_start();
                $this->db->like('unit', $specificFilter);
                $this->db->or_like('tujuan_penerima', $specificFilter);
                $this->db->or_like('unit_terkait', $specificFilter);
                $this->db->group_end();
            }

            // Status Filter (3 Status Sesuai DB)
            if (!empty($filterStatus) && $filterStatus !== 'all') {
                if ($filterStatus === 'Dikirim' || $filterStatus === 'Menunggu') {
                    $this->db->where('status', 'Dikirim');
                } elseif ($filterStatus === 'Sedang Diproses' || $filterStatus === 'Diproses') {
                    $this->db->where('status', 'Sedang Diproses');
                } elseif ($filterStatus === 'Closed' || $filterStatus === 'Selesai' || $filterStatus === 'Ditutup') {
                    $this->db->where('status', 'Closed');
                }
            }

            // Search
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('id', $search);
                $this->db->or_like('nama', $search);
                $this->db->or_like('kategori', $search);
                $this->db->or_like('isi_ticketing', $search);
                $this->db->or_like('unit_terkait', $search);
                $this->db->group_end();
            }

            $this->db->order_by('tgl_ticketing', 'DESC');
            $this->db->order_by('id', 'DESC');
            $raw = $this->db->get()->result();

            return array_map([$this, '_format_row'], $raw);
        }

        // Fallback for dosen_ticketing (if table exists)
        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('unit_tujuan', 'Kaur');
        $this->db->or_like('unit_tujuan', 'Kepala Urusan');
        $this->db->or_like('unit_tujuan', 'Ka. Ur');
        $this->db->or_like('unit_tujuan', 'Semua');
        $this->db->or_like('unit_tujuan', 'Laboran, Kaur, Admin LAA');
        $this->db->group_end();

        if (!empty($filterStatus) && $filterStatus !== 'all') {
            $this->db->where('status', $filterStatus);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode_tiket', $search);
            $this->db->or_like('nama_dosen', $search);
            $this->db->or_like('subjek', $search);
            $this->db->or_like('kategori', $search);
            $this->db->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Apply recipient role filter based on Laboran, Kaur, or Admin LAA
     */
    private function _apply_recipient_filter($recipientRole = 'Kaur') {
        $rRole = strtolower(trim((string)$recipientRole));
        if (strpos($rRole, 'lab') !== false || strpos($rRole, 'bengkel') !== false || strpos($rRole, 'studio') !== false) {
            $this->db->group_start();
            $this->db->where('tujuan_penerima', 'Laboran');
            $this->db->or_like('tujuan_penerima', 'Laboran');
            $this->db->or_like('tujuan_penerima', 'Laboratorium');
            $this->db->or_like('tujuan_penerima', 'Bengkel');
            $this->db->or_like('tujuan_penerima', 'Studio');
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', 'Laboran');
            $this->db->or_like('unit', 'Laboratorium');
            $this->db->or_like('unit', 'Bengkel');
            $this->db->or_like('unit', 'Studio');
            $this->db->or_like('unit', 'Lab');
            $this->db->or_like('unit', 'Sarpras');
            $this->db->group_end();
        } elseif (strpos($rRole, 'laa') !== false || strpos($rRole, 'akademik') !== false) {
            $this->db->group_start();
            $this->db->where('tujuan_penerima', 'Admin LAA');
            $this->db->or_like('tujuan_penerima', 'Admin LAA');
            $this->db->or_like('tujuan_penerima', 'LAA');
            $this->db->or_like('tujuan_penerima', 'Layanan Akademik');
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', 'Admin LAA');
            $this->db->or_like('unit', 'LAA');
            $this->db->or_like('unit', 'Layanan Akademik');
            $this->db->or_like('unit', 'Layanan');
            $this->db->group_end();
        } elseif (strpos($rRole, 'kemahasiswaan') !== false || strpos($rRole, 'mhs') !== false) {
            $this->db->group_start();
            $this->db->where('tujuan_penerima', 'Kemahasiswaan');
            $this->db->or_like('tujuan_penerima', 'Kemahasiswaan');
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', 'Kemahasiswaan');
            $this->db->group_end();
        } elseif (strpos($rRole, 'sekr') !== false || strpos($rRole, 'fak') !== false) {
            $this->db->group_start();
            $this->db->where('tujuan_penerima', 'Sekretariat');
            $this->db->or_like('tujuan_penerima', 'Sekretariat');
            $this->db->or_like('tujuan_penerima', 'Sekre Fak');
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', 'Sekretariat');
            $this->db->or_like('unit', 'Sekre Fak');
            $this->db->group_end();
        } elseif (strpos($rRole, 'sdm') !== false || strpos($rRole, 'keuangan') !== false) {
            $this->db->group_start();
            $this->db->where('tujuan_penerima', 'SDM dan Keuangan');
            $this->db->or_like('tujuan_penerima', 'SDM');
            $this->db->or_like('tujuan_penerima', 'Keuangan');
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', 'SDM dan Keuangan');
            $this->db->or_like('unit', 'SDM');
            $this->db->or_like('unit', 'Keuangan');
            $this->db->group_end();
        } elseif (strpos($rRole, 'prodi') !== false) {
            $this->db->group_start();
            $this->db->where('tujuan_penerima', 'Program Studi');
            $this->db->or_like('tujuan_penerima', 'Program Studi');
            $this->db->or_like('tujuan_penerima', 'Prodi');
            $this->db->or_like('tujuan_penerima', 'DKV');
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', 'Program Studi');
            $this->db->or_like('unit', 'Prodi');
            $this->db->or_like('unit', 'Admin Prodi');
            $this->db->group_end();
        } else { // Fallback / Kaur
            $this->db->group_start();
            $this->db->where('tujuan_penerima', $recipientRole);
            $this->db->or_where('tujuan_penerima', 'Kepala Urusan');
            $this->db->or_where('tujuan_penerima', 'Dosen Kaur');
            $this->db->or_like('tujuan_penerima', $recipientRole);
            $this->db->or_like('tujuan_penerima', 'Semua');
            $this->db->or_like('unit', $recipientRole);
            $this->db->group_end();
        }
    }

    /**
     * Count tickets routed to specific recipient role by status
     */
    public function count_respon_tickets($status = 'all', $recipientRole = 'Kaur', $specificFilter = '') {
        if ($this->table === 'tb_ticketing') {
            $this->db->from('tb_ticketing');
            $this->_apply_recipient_filter($recipientRole);

            if (!empty($specificFilter)) {
                $this->db->group_start();
                $this->db->like('unit', $specificFilter);
                $this->db->or_like('tujuan_penerima', $specificFilter);
                $this->db->or_like('unit_terkait', $specificFilter);
                $this->db->group_end();
            }

            if ($status !== 'all') {
                if ($status === 'Dikirim' || $status === 'Menunggu') {
                    $this->db->where('status', 'Dikirim');
                } elseif ($status === 'Sedang Diproses' || $status === 'Diproses') {
                    $this->db->where('status', 'Sedang Diproses');
                } elseif ($status === 'Closed' || $status === 'Selesai' || $status === 'Ditutup') {
                    $this->db->where('status', 'Closed');
                }
            }
            return (int)$this->db->count_all_results();
        }

        $this->db->from($this->table);
        $this->db->group_start();
        $this->db->like('unit_tujuan', 'Dosen');
        $this->db->or_like('unit_tujuan', 'Wali');
        $this->db->or_like('unit_tujuan', 'Koordinator');
        $this->db->or_like('unit_tujuan', 'Ketua KK');
        $this->db->group_end();

        if ($status !== 'all') {
            $this->db->where('status', $status);
        }
        return (int)$this->db->count_all_results();
    }

    /**
     * Update ticket response and status
     */
    public function update_respon($id, $status, $tanggapan = '') {
        if ($this->table === 'tb_ticketing') {
            // Ambil row saat ini untuk mempertahankan catatan dari tahap sebelumnya
            $this->db->from('tb_ticketing');
            $this->db->where('id', (string)$id);
            $curr = $this->db->get()->row();
            $currKet = $curr ? ($curr->keterangan ?? '') : '';

            $prevCreated = '';
            $prevPrioritas = '';
            $prevProses = '';
            $prevSelesai = '';
            $prevTutup = '';

            if (preg_match('/\[CREATED:\s*([^\]]+)\]/is', $currKet, $mC)) {
                $prevCreated = trim($mC[1]);
            }
            if (preg_match('/\[PRIORITAS:\s*([^\]]+)\]/is', $currKet, $mPr)) {
                $prevPrioritas = trim($mPr[1]);
            }
            if (preg_match('/\[PROSES\]\s*(.*?)(?=\[(SELESAI|DITUTUP)\]|$)/is', $currKet, $mP)) {
                $prevProses = trim($mP[1]);
            }
            if (preg_match('/\[SELESAI\]\s*(.*?)(?=\[(PROSES|DITUTUP)\]|$)/is', $currKet, $mS)) {
                $prevSelesai = trim($mS[1]);
            }
            if (preg_match('/\[DITUTUP\]\s*(.*?)(?=\[(PROSES|SELESAI)\]|$)/is', $currKet, $mD)) {
                $prevTutup = trim($mD[1]);
            }

            // Fallback untuk legacy record yang belum menggunakan tag [PROSES] / [SELESAI]
            if (empty($prevProses) && empty($prevSelesai) && !empty($currKet)) {
                $cleanOld = trim(str_replace(['[DITUTUP]', '[CREATED:' . $prevCreated . ']', '[PRIORITAS:' . $prevPrioritas . ']'], '', $currKet));
                if ($curr && $curr->status === 'Sedang Diproses') {
                    $prevProses = $cleanOld;
                } elseif ($curr && $curr->status === 'Closed') {
                    $prevSelesai = $cleanOld;
                }
            }

            $rawStatus = trim($status);
            if ($rawStatus === 'Sedang Diproses' || $rawStatus === 'Diproses') {
                $statusMapped = 'Sedang Diproses';
            } elseif ($rawStatus === 'Closed' || $rawStatus === 'Selesai' || $rawStatus === 'Ditutup') {
                $statusMapped = 'Closed';
            } else {
                $statusMapped = 'Dikirim';
            }

            $updateData = ['status' => $statusMapped];
            $now = date('Y-m-d H:i:s');

            if ($statusMapped === 'Sedang Diproses') {
                if (empty($curr->tgl_diproses) || in_array($curr->tgl_diproses, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) {
                    $updateData['tgl_diproses'] = $now;
                }
            } elseif ($statusMapped === 'Closed') {
                if (empty($curr->tgl_diproses) || in_array($curr->tgl_diproses, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) {
                    $updateData['tgl_diproses'] = $now;
                }
                $updateData['tgl_closed'] = $now;
            }

            $cleanInput = trim(str_replace(['[PROSES]', '[SELESAI]', '[DITUTUP]', '[CLOSED]'], '', $tanggapan));

            $parts = [];
            if (!empty($prevCreated)) {
                $parts[] = '[CREATED:' . $prevCreated . ']';
            }
            if (!empty($prevPrioritas)) {
                $parts[] = '[PRIORITAS:' . $prevPrioritas . ']';
            }

            if ($statusMapped === 'Sedang Diproses') {
                $activeProses = ($cleanInput !== '') ? $cleanInput : $prevProses;
                if (!empty($activeProses)) {
                    $parts[] = '[PROSES] ' . $activeProses;
                }
            } elseif ($statusMapped === 'Closed') {
                $activeClosed = ($cleanInput !== '') ? $cleanInput : ($prevSelesai ?: ($prevTutup ?: ''));
                if (!empty($prevProses)) {
                    $parts[] = '[PROSES] ' . $prevProses;
                }
                if (!empty($activeClosed)) {
                    $parts[] = '[CLOSED] ' . $activeClosed;
                }
            }

            $updateData['keterangan'] = implode("\n", $parts);

            $this->db->where('id', (string)$id);
            return $this->db->update('tb_ticketing', $updateData);
        }

        $updateData = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($tanggapan !== '') {
            $updateData['tanggapan'] = $tanggapan;
            $updateData['tgl_tanggapan'] = date('Y-m-d H:i:s');
        } elseif ($status === 'Menunggu') {
            $updateData['tanggapan'] = null;
            $updateData['tgl_tanggapan'] = null;
        }

        if (is_numeric($id)) {
            $this->db->where('id', (int)$id);
        } else {
            $this->db->where('kode_tiket', $id);
        }
        return $this->db->update($this->table, $updateData);
    }

    /**
     * Standardize tb_ticketing row object to Dosen/Mahasiswa/Laboran ticketing view fields
     */
    public function format_row($row) {
        if (!$row) return null;

        $r = clone $row;
        $r->id              = $row->id;
        $r->kode_tiket      = $row->id;
        $r->nama_dosen      = $row->nama ?? 'Dosen';
        $rawPenerima        = !empty($row->tujuan_penerima) ? $row->tujuan_penerima : (!empty($row->unit) ? $row->unit : 'Laboratorium Bengkel dan Studio');
        if ($rawPenerima === 'Dosen Kaur') {
            $rawPenerima = 'Kaur';
        }
        $r->tujuan_penerima = $rawPenerima;
        $r->unit_terkait    = !empty($row->unit_terkait) ? $row->unit_terkait : (!empty($row->unit) ? $row->unit : $rawPenerima);
        $r->unit_tujuan     = !empty($row->unit_tujuan) ? $row->unit_tujuan : $r->tujuan_penerima;
        $r->kategori        = $row->kategori ?? 'Umum';

        $rawKeterangan      = $row->keterangan ?? '';
        $prioritasParsed    = null;
        if (preg_match('/\[PRIORITAS:\s*([^\]]+)\]/is', $rawKeterangan, $mPrio)) {
            $prioritasParsed = trim($mPrio[1]);
        }
        $r->prioritas       = $prioritasParsed ?: (!empty($row->prioritas) ? $row->prioritas : 'Sedang');

        // Resolve NIDN / NIM / NIP dan Identitas Pengirim (agar tidak undefined property $t->nidn)
        $resolvedNidn   = !empty($row->nidn) ? $row->nidn : '';
        $roleSender     = 'Dosen';
        $labelIdentitas = 'NIDN';

        if (empty($resolvedNidn) && !empty($row->id_user)) {
            static $userMapCache = [];
            $uid = (string)$row->id_user;

            if (!array_key_exists($uid, $userMapCache)) {
                $userRec = null;
                if ($this->db->table_exists('user')) {
                    $userRec = $this->db->select('id, nim, nip, kode_dosen, role_id, name')
                        ->get_where('user', ['id' => $row->id_user])
                        ->row();
                    if (!$userRec && is_numeric($row->id_user)) {
                        $userRec = $this->db->select('id, nim, nip, kode_dosen, role_id, name')
                            ->get_where('user', ['role_id' => $row->id_user])
                            ->row();
                    }
                }
                $userMapCache[$uid] = $userRec;
            } else {
                $userRec = $userMapCache[$uid];
            }

            if ($userRec) {
                $resolvedNidn = !empty($userRec->nim) ? $userRec->nim : (!empty($userRec->nip) ? $userRec->nip : (!empty($userRec->kode_dosen) ? $userRec->kode_dosen : ''));
                if ((int)$userRec->role_id === 3) {
                    $roleSender     = 'Mahasiswa';
                    $labelIdentitas = 'NIM';
                } elseif ((int)$userRec->role_id === 4) {
                    $roleSender     = 'Dosen';
                    $labelIdentitas = 'NIDN';
                } elseif (in_array((int)$userRec->role_id, [1, 2, 5, 21])) {
                    $roleSender     = 'Staff/Tendik';
                    $labelIdentitas = 'NIP';
                }
            } else {
                $resolvedNidn = $uid;
            }
        }

        // Cek heuristik jika kategori Kemahasiswaan atau format NIM
        if ($roleSender === 'Dosen' && ($r->kategori === 'Kemahasiswaan' || (strlen($resolvedNidn) >= 9 && is_numeric($resolvedNidn)))) {
            $roleSender     = 'Mahasiswa';
            $labelIdentitas = 'NIM';
        }

        $r->nidn            = !empty($resolvedNidn) ? $resolvedNidn : '-';
        $r->nim             = $r->nidn;
        $r->nidn_nim        = $r->nidn;
        $r->role_sender     = $roleSender;
        $r->label_identitas = $labelIdentitas;
        $r->email           = $row->email ?? '-';
        $r->ruangan         = $row->ruangan ?? '-';

        // Ekstrak subjek dan deskripsi dari isi_ticketing
        $rawIsi = $row->isi_ticketing ?? '';
        $r->subjek = 'Kendala ' . ($row->kategori ?? 'Layanan');
        $r->deskripsi = $rawIsi;

        if (preg_match('/<strong>(.*?)<\/strong><br>(.*)/is', $rawIsi, $m)) {
            $r->subjek = trim(strip_tags($m[1]));
            $r->deskripsi = trim($m[2]);
        }

        // Status langsung sesuai database: Dikirim, Sedang Diproses, Closed
        $rawSt = trim((string)($row->status ?? ''));
        if ($rawSt === 'Sedang Diproses' || $rawSt === 'Diproses') {
            $r->status = 'Sedang Diproses';
        } elseif ($rawSt === 'Closed' || $rawSt === 'Selesai' || $rawSt === 'Ditutup') {
            $r->status = 'Closed';
        } else {
            $r->status = 'Dikirim';
        }

        $r->lampiran      = !empty($row->file_pendukung) ? $row->file_pendukung : null;
        $rawKeterangan    = $row->keterangan ?? '';

        // Ekstrak catatan terpisah untuk masing-masing tahap (Sedang Diproses vs Closed)
        $catatan_proses  = '';
        $catatan_closed  = '';

        if (preg_match('/\[PROSES\]\s*(.*?)(?=\[(SELESAI|DITUTUP|CLOSED)\]|$)/is', $rawKeterangan, $mP)) {
            $catatan_proses = trim($mP[1]);
        }
        if (preg_match('/\[(CLOSED|SELESAI|DITUTUP)\]\s*(.*?)(?=\[(PROSES)\]|$)/is', $rawKeterangan, $mC)) {
            $catatan_closed = trim($mC[2]);
        }

        // Fallback untuk legacy data yang belum memiliki tag
        if (empty($catatan_proses) && empty($catatan_closed) && !empty($rawKeterangan)) {
            $cleanOld = trim(str_replace(['[DITUTUP]', '[CLOSED]'], '', $rawKeterangan));
            if ($r->status === 'Sedang Diproses') {
                $catatan_proses = $cleanOld;
            } elseif ($r->status === 'Closed') {
                $catatan_closed = $cleanOld;
            }
        }

        $r->catatan_proses  = $catatan_proses ?: null;
        $r->catatan_closed  = $catatan_closed ?: null;
        $r->catatan_selesai = $catatan_closed ?: null; // alias
        $r->catatan_tutup   = $catatan_closed ?: null; // alias

        // Tanggapan aktif spesifik untuk tahap saat ini
        if ($r->status === 'Sedang Diproses') {
            $r->tanggapan = $catatan_proses ?: null;
            $r->tgl_tanggapan = (!empty($row->tgl_diproses) && !in_array($row->tgl_diproses, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) ? $row->tgl_diproses : null;
        } elseif ($r->status === 'Closed') {
            $r->tanggapan = $catatan_closed ?: ($catatan_proses ?: null);
            $tglClosedVal = (!empty($row->tgl_closed) && !in_array($row->tgl_closed, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) ? $row->tgl_closed : null;
            $tglProsesVal = (!empty($row->tgl_diproses) && !in_array($row->tgl_diproses, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) ? $row->tgl_diproses : null;
            $r->tgl_tanggapan = $tglClosedVal ?: $tglProsesVal;
        } else {
            $r->tanggapan = null;
            $r->tgl_tanggapan = null;
        }

        $r->tgl_diproses = (!empty($row->tgl_diproses) && !in_array($row->tgl_diproses, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) ? $row->tgl_diproses : null;
        $r->tgl_closed   = (!empty($row->tgl_closed) && !in_array($row->tgl_closed, ['0000-00-00 00:00:00', '1970-01-01 00:00:00'])) ? $row->tgl_closed : null;

        $createdAtReal = null;
        if (preg_match('/\[CREATED:\s*([^\]]+)\]/is', $rawKeterangan, $mC)) {
            $createdAtReal = trim($mC[1]);
        } elseif (!empty($row->tgl_ticketing) && strlen(trim($row->tgl_ticketing)) > 10 && $row->tgl_ticketing !== '0000-00-00 00:00:00') {
            $createdAtReal = $row->tgl_ticketing;
        }

        $r->created_at    = $createdAtReal ?: (!empty($row->tgl_ticketing) ? ($row->tgl_ticketing . ' 08:00:00') : date('Y-m-d H:i:s'));
        $r->updated_at    = $tglTanggapan ?: $r->created_at;

        return $r;
    }

    public function _format_row($row) {
        return $this->format_row($row);
    }
}

