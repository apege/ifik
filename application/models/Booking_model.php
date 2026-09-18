<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_kategori()
    {
        if ($this->db->table_exists('kategori_ruangan')) {
            return $this->db->get('kategori_ruangan')->result();
        }
        if ($this->db->table_exists('kategori')) {
            return $this->db->get('kategori')->result();
        }
        return [];
    }

    
    public function get_all_ruangan()
    {
        $this->db->select("r.id, r.id_kategori, r.ruangan, r.kapasitas, r.akses, r.images, r.date, k.nama_kategori");
        $this->db->from("ruangan r");
        if ($this->db->table_exists("kategori_ruangan")) {
            $this->db->join("kategori_ruangan k", "k.id = r.id_kategori", "left");
        } elseif ($this->db->table_exists("kategori")) {
            $this->db->join("kategori k", "k.id_kategori = r.id_kategori", "left");
        }
        $this->db->order_by("r.id", "ASC");
        $results = $this->db->get()->result();
        foreach ($results as &$r) {
            $r->nama_ruangan = $r->ruangan;
            $r->kode_ruangan = $r->id;
            $r->status = !empty($r->akses) ? $r->akses : "Tersedia";
            $r->foto = $r->images;
            $r->lokasi = "Gedung Sebatik (FIK)";
            $r->model_3d = "";
            $r->tagline = "";
            $r->jumlah_unit = ($r->kapasitas ?: "0") . " Orang";
            $r->jam_operasional = "08:00 - 17:00 WIB";
            $r->deskripsi = "";
            $r->spesifikasi_fasilitas = "";
            $r->tata_tertib = "";
            $r->nama_kategori = !empty($r->nama_kategori) ? $r->nama_kategori : "Umum";
            $this->parse_room_media($r);
        }
        return $results;
    }

    public function parse_room_media(&$r)
    {
        if (!$r) return;
        $raw = isset($r->images) ? trim((string)$r->images) : (isset($r->foto) ? trim((string)$r->foto) : "");
        if (strpos($raw, "|") !== false) {
            list($foto, $model) = explode("|", $raw, 2);
            $r->foto = !empty($foto) ? trim($foto) : "";
            $r->model_3d = !empty($model) ? trim($model) : "";
        } else {
            $ext = strtolower(pathinfo($raw, PATHINFO_EXTENSION));
            if (in_array($ext, ["glb", "gltf", "fbx", "obj"])) {
                $r->foto = "";
                $r->model_3d = $raw;
            } else {
                $r->foto = $raw;
                if (!isset($r->model_3d) || empty($r->model_3d)) {
                    $r->model_3d = "";
                }
            }
        }
    }

public function get_ruangan_by_kategori($id_kategori)
    {
        $this->db->select('ruangan.*, ruangan.ruangan AS nama_ruangan, ruangan.id AS kode_ruangan');
        $this->db->where('id_kategori', $id_kategori);
        return $this->db->get('ruangan')->result();
    }

    public function get_all_slot_waktu()
    {
        if ($this->db->table_exists('slot_waktu')) {
            $this->db->order_by('urutan', 'ASC');
            return $this->db->get('slot_waktu')->result();
        }
        return [];
    }

    /**
     * Query dasar untuk mengambil data peminjaman dari tabel booking dengan join ruangan, kategori, & user
     */
    private function _base_booking_query()
    {
        $this->db->select("
            booking.id,
            booking.id_peminjam AS id_user,
            booking.id_peminjam,
            booking.id_ruangan,
            COALESCE(
                NULLIF(TRIM(u.name), ''),
                NULLIF(TRIM(CONCAT(m.nama_depan, ' ', COALESCE(m.nama_belakang, ''))), ''),
                'Mahasiswa / Civitas IFIK'
            ) AS nama_lengkap,
            COALESCE(ruangan.ruangan, booking.id_ruangan) AS nama_ruangan,
            ruangan.id AS kode_ruangan,
            'Gedung Sebatik (FIK)' AS lokasi,
            COALESCE(ruangan.kapasitas, 30) AS kapasitas,
            ruangan.foto,
            COALESCE(kategori_ruangan.nama_kategori, 'Ruangan') AS nama_kategori,
            booking.keterangan,
            booking.date AS tanggal_mulai,
            booking.date AS tanggal_selesai,
            booking.date,
            COALESCE(SUBSTRING_INDEX(booking.time, ' - ', 1), '08:00:00') AS jam_mulai,
            COALESCE(SUBSTRING_INDEX(booking.time, ' - ', -1), '12:00:00') AS jam_selesai,
            booking.time,
            booking.status,
            COALESCE(booking.komentar, '') AS alasan_penolakan,
            COALESCE(booking.komentar, '') AS komentar,
            COALESCE(booking.date_created, NOW()) AS created_at,
            COALESCE(booking.date_created, NOW()) AS date_created,
            booking.laboran,
            booking.tanggal_accepted,
            booking.date_declined
        ", FALSE);
        $this->db->from('booking');
        $this->db->join('user u', '(BINARY u.id = BINARY booking.id_peminjam OR BINARY u.nim = BINARY booking.id_peminjam OR BINARY u.nip = BINARY booking.id_peminjam)', 'left', FALSE);
        $this->db->join('mahasiswa m', 'BINARY m.nim = BINARY booking.id_peminjam', 'left', FALSE);
        $this->db->join('ruangan', 'BINARY ruangan.id = BINARY booking.id_ruangan', 'left', FALSE);
        $this->db->join('kategori_ruangan', 'BINARY kategori_ruangan.id = BINARY ruangan.id_kategori', 'left', FALSE);
    }

    public function get_all_peminjaman()
    {
        $this->_base_booking_query();
        $this->db->order_by('booking.date_created', 'DESC');
        return $this->db->get()->result();
    }

    public function get_booking_by_id($id)
    {
        $this->_base_booking_query();
        $this->db->where('booking.id', $id);
        $res = $this->db->get()->row();
        if (!$res && $this->db->table_exists('peminjaman')) {
            $this->db->select("
                peminjaman.id,
                peminjaman.id_user,
                peminjaman.id_ruangan,
                peminjaman.nama_lengkap,
                COALESCE(ruangan.ruangan, peminjaman.id_ruangan) AS nama_ruangan,
                ruangan.id AS kode_ruangan,
                ruangan.id_kategori,
                'Gedung Sebatik (FIK)' AS lokasi,
                COALESCE(ruangan.kapasitas, 30) AS kapasitas,
                ruangan.foto,
                COALESCE(kategori_ruangan.nama_kategori, 'Ruangan') AS nama_kategori,
                peminjaman.keterangan,
                peminjaman.tanggal_mulai,
                peminjaman.tanggal_selesai,
                peminjaman.tanggal_mulai AS date,
                peminjaman.jam_mulai,
                peminjaman.jam_selesai,
                CONCAT(peminjaman.jam_mulai, ' - ', peminjaman.jam_selesai) AS time,
                peminjaman.status,
                peminjaman.alasan_penolakan,
                peminjaman.created_at,
                peminjaman.created_at AS date_created
            ", FALSE);
            $this->db->from('peminjaman');
            $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
            $this->db->join('kategori_ruangan', 'kategori_ruangan.id = ruangan.id_kategori', 'left');
            $this->db->where('peminjaman.id', $id);
            $res = $this->db->get()->row();
        }
        return $res;
    }

    public function get_peminjaman_by_user($user_id, $nama_lengkap = null)
    {
        $this->_base_booking_query();

        $this->db->group_start();
        if (!empty($user_id)) {
            $this->db->where('booking.id_peminjam', (string)$user_id);
        }
        if (!empty($nama_lengkap)) {
            $cleanName = strtolower(trim($nama_lengkap));
            if (!empty($user_id)) {
                $this->db->or_where('LOWER(TRIM(u.name))', $cleanName);
            } else {
                $this->db->where('LOWER(TRIM(u.name))', $cleanName);
            }
        }
        $this->db->group_end();

        $this->db->order_by('booking.date_created', 'DESC');
        return $this->db->get()->result();
    }

    public function cancel_booking($id, $user_id = null, $nama_lengkap = null)
    {
        $this->db->where('id', $id);
        $this->db->group_start();
        $this->db->where('status', 'Pending');
        $this->db->or_where('status', 'Menunggu Persetujuan');
        $this->db->group_end();

        if (!empty($user_id)) {
            $this->db->where('id_peminjam', (string)$user_id);
        }

        $res = $this->db->delete('booking');

        if ($this->db->table_exists('peminjaman')) {
            $this->db->where('id', $id);
            $this->db->delete('peminjaman');
        }

        return $res;
    }

    public function insert_booking($data)
    {
        $this->db->trans_start();

        $bookingId = $data['id'] ?? ('bkg_' . uniqid());
        $userId    = $data['id_user'] ?? ($data['id_peminjam'] ?? $this->session->userdata('nim') ?? $this->session->userdata('id'));
        $nama      = $data['nama_lengkap'] ?? $this->session->userdata('name');
        $ruangId   = $data['id_ruangan'] ?? '';
        $ket       = $data['keterangan'] ?? '';
        $tglM      = $data['tanggal_mulai'] ?? ($data['date'] ?? date('Y-m-d'));
        $tglS      = $data['tanggal_selesai'] ?? $tglM;
        $jM        = $data['jam_mulai'] ?? '08:00:00';
        $jS        = $data['jam_selesai'] ?? '12:00:00';
        $timeStr   = $data['time'] ?? (substr($jM, 0, 5) . ' - ' . substr($jS, 0, 5));
        $status    = $data['status'] ?? 'Pending';
        $created   = $data['created_at'] ?? ($data['date_created'] ?? date('Y-m-d H:i:s'));

        $bookingData = array(
            'id'               => (string)$bookingId,
            'id_peminjam'      => (string)$userId,
            'id_ruangan'       => (string)$ruangId,
            'date'             => $tglM,
            'date_declined'    => null,
            'time'             => $timeStr,
            'keterangan'       => $ket,
            'status'           => $status,
            'date_created'     => $created,
            'laboran'          => null,
            'komentar'         => $data['komentar'] ?? ($data['alasan_penolakan'] ?? null),
            'tanggal_accepted' => null
        );

        $this->db->insert('booking', $bookingData);

        if ($this->db->table_exists('peminjaman')) {
            $peminjamanData = array(
                'id_user'          => is_numeric($userId) ? (int)$userId : null,
                'id_ruangan'       => is_numeric($ruangId) ? (int)$ruangId : 1,
                'nama_lengkap'     => $nama,
                'keterangan'       => $ket,
                'tanggal_mulai'    => $tglM,
                'tanggal_selesai'  => $tglS,
                'jam_mulai'        => $jM,
                'jam_selesai'      => $jS,
                'status'           => $status,
                'alasan_penolakan' => $data['alasan_penolakan'] ?? ($data['komentar'] ?? null),
                'created_at'       => $created
            );
            $this->db->insert('peminjaman', $peminjamanData);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_status($id, $status, $alasan = null, $laboran = null)
    {
        $data = array('status' => $status);
        if ($alasan !== null) {
            $data['komentar'] = $alasan;
        }

        $adminName = $laboran ?: ($this->session->userdata('name') ?: 'Petugas Laboran');
        if (stripos($status, 'Disetujui') !== false || stripos($status, 'Approved') !== false) {
            $data['tanggal_accepted'] = date('Y-m-d');
            $data['laboran']          = $adminName;
        } elseif (stripos($status, 'Ditolak') !== false || stripos($status, 'Reject') !== false) {
            $data['date_declined']    = date('Y-m-d');
            $data['laboran']          = $adminName;
        }

        $this->db->where('id', $id);
        $res = $this->db->update('booking', $data);

        if ($this->db->table_exists('peminjaman')) {
            $pData = array('status' => $status);
            if ($alasan !== null) $pData['alasan_penolakan'] = $alasan;
            $this->db->where('id', $id);
            $this->db->update('peminjaman', $pData);
        }

        return $res;
    }

    public function batch_update_status($ids, $status, $alasan = null)
    {
        if (empty($ids) || !is_array($ids)) return false;

        $data = array('status' => $status);
        if ($alasan !== null) {
            $data['komentar'] = $alasan;
        }

        $adminName = $this->session->userdata('name') ?: 'Petugas Laboran';
        if (stripos($status, 'Disetujui') !== false) {
            $data['tanggal_accepted'] = date('Y-m-d');
            $data['laboran']          = $adminName;
        } elseif (stripos($status, 'Ditolak') !== false) {
            $data['date_declined']    = date('Y-m-d');
            $data['laboran']          = $adminName;
        }

        $this->db->where_in('id', $ids);
        $res = $this->db->update('booking', $data);

        if ($this->db->table_exists('peminjaman')) {
            $pData = array('status' => $status);
            if ($alasan !== null) $pData['alasan_penolakan'] = $alasan;
            $this->db->where_in('id', $ids);
            $this->db->update('peminjaman', $pData);
        }

        return $res;
    }

    public function delete_booking($id)
    {
        $this->db->where('id', $id);
        $res = $this->db->delete('booking');

        if ($this->db->table_exists('peminjaman')) {
            $this->db->where('id', $id);
            $this->db->delete('peminjaman');
        }

        return $res;
    }

    public function batch_delete_booking($ids)
    {
        if (empty($ids) || !is_array($ids)) return false;
        $this->db->where_in('id', $ids);
        $res = $this->db->delete('booking');

        if ($this->db->table_exists('peminjaman')) {
            $this->db->where_in('id', $ids);
            $this->db->delete('peminjaman');
        }

        return $res;
    }

    public function get_approved_bookings()
    {
        $this->_base_booking_query();
        $this->db->order_by('booking.date ASC, SUBSTRING_INDEX(booking.time, " - ", 1) ASC', '', FALSE);
        return $this->db->get()->result();
    }

    /**
     * Check if a room booking conflicts with existing non-rejected/non-cancelled bookings
     * Booking yang ditolak atau dibatalkan TIDAK dihitung sebagai bentrok
     */
    public function check_conflict($id_ruangan, $tanggal_mulai, $tanggal_selesai, $jam_mulai, $jam_selesai, $ignore_id = null)
    {
        $this->_base_booking_query();
        $this->db->where('booking.id_ruangan', $id_ruangan);
        
        // Pengecualian mutlak: Status Ditolak atau Dibatalkan tidak menyebabkan bentrok
        $this->db->where_not_in('booking.status', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan', 'Reject', 'Rejected']);
        $this->db->where("booking.status NOT LIKE '%Ditolak%'", NULL, FALSE);
        $this->db->where("booking.status NOT LIKE '%Dibatalkan%'", NULL, FALSE);

        // Check date range overlap
        $this->db->where('booking.date <=', $tanggal_selesai);
        $this->db->where('booking.date >=', $tanggal_mulai);

        // Check time range overlap (strict overlap: start < existing_end AND end > existing_start)
        $this->db->where('COALESCE(SUBSTRING_INDEX(booking.time, " - ", 1), "08:00:00") <', $jam_selesai);
        $this->db->where('COALESCE(SUBSTRING_INDEX(booking.time, " - ", -1), "12:00:00") >', $jam_mulai);

        if ($ignore_id !== null) {
            $this->db->where('booking.id !=', $ignore_id);
        }

        return $this->db->get()->result();
    }

    /**
     * Dapatkan informasi penandatangan resmi surat (Laboran vs Ka. Ur) beserta tanda tangan digitalnya
     */
    public function get_penandatangan($status = '')
    {
        $is_laboran = (stripos($status, 'Laboran') !== false);
        $target_role = $is_laboran ? 21 : 2; // 21 = Laboran, 2 = Kepala Urusan

        $current_user_id = $this->session->userdata('user_id');
        $current_role_id = (int)$this->session->userdata('role_id');
        
        $penandatangan = null;

        if ($current_user_id && ($current_role_id === $target_role || $current_role_id === 1)) {
            $user = $this->db->get_where('user', ['id' => $current_user_id])->row();
            if (!$user) {
                $user = $this->db->get_where('user', ['nim' => $current_user_id])->row();
            }
            if ($user) {
                $penandatangan = [
                    'role_id'       => $user->role_id,
                    'jabatan'       => $is_laboran ? 'Laboran / Pengelola Laboratorium' : 'Kepala Urusan Laboratorium',
                    'jabatan_resmi' => $is_laboran ? 'Laboran / Petugas Pengelola Fasilitas Laboratorium' : 'Kepala Urusan / Kepala Laboratorium',
                    'nama'          => $user->name,
                    'nip'           => $user->nip ?: ($user->nim ?: '-'),
                    'tanda_tangan'  => $user->ttd ?? null
                ];
            }
        }

        if (!$penandatangan) {
            $this->db->where('role_id', $target_role);
            $this->db->order_by("(ttd IS NOT NULL AND ttd != '')", 'DESC', false);
            $this->db->order_by('id', 'ASC');
            $user = $this->db->get('user')->row();

            if ($user) {
                $penandatangan = [
                    'role_id'       => $user->role_id,
                    'jabatan'       => $is_laboran ? 'Laboran / Pengelola Laboratorium' : 'Kepala Urusan Laboratorium',
                    'jabatan_resmi' => $is_laboran ? 'Laboran / Petugas Pengelola Fasilitas Laboratorium' : 'Kepala Urusan / Kepala Laboratorium',
                    'nama'          => $user->name,
                    'nip'           => $user->nip ?: ($user->nim ?: '-'),
                    'tanda_tangan'  => $user->ttd ?? null
                ];
            } else {
                $penandatangan = [
                    'role_id'       => $target_role,
                    'jabatan'       => $is_laboran ? 'Laboran / Pengelola Laboratorium' : 'Kepala Urusan Laboratorium',
                    'jabatan_resmi' => $is_laboran ? 'Laboran / Petugas Pengelola Fasilitas Laboratorium' : 'Kepala Urusan / Kepala Laboratorium',
                    'nama'          => $is_laboran ? 'Laboran FIK' : 'Kaur / Ka. Lab FIK',
                    'nip'           => $is_laboran ? '19850101004' : '198203152010121002',
                    'tanda_tangan'  => null
                ];
            }
        }

        return $penandatangan;
    }
}

