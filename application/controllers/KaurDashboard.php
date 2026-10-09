<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KaurDashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->helper('url');
    }

    public function index()
    {
        redirect('kaur/approval');
    }

    public function approval()
    {
        $data['title'] = 'Persetujuan Resmi & Surat QR - Ka. Ur / Kepala Lab';
        $data['peminjaman'] = $this->Booking_model->get_all_peminjaman();
        
        $this->load->view('kaur/approval/index', $data);
    }

    public function live_data()
    {
        header('Content-Type: application/json');
        $data = $this->Booking_model->get_all_peminjaman();
        
        $totalCount = count($data);
        $pendingCount = 0;
        $laboranCount = 0;
        $kaurCount = 0;
        $adminCount = 0;
        $rejectedCount = 0;

        $indoMonths = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $formatted = [];

        foreach ($data as $row) {
            $st = $row->status;
            $statusCategory = 'pending';
            if (strpos($st, 'Laboran') !== false) {
                $statusCategory = 'laboran';
                $laboranCount++;
            } elseif (strpos($st, 'Ka. Ur') !== false || strpos($st, 'Kaur') !== false) {
                $statusCategory = 'kaur';
                $kaurCount++;
            } elseif (strpos($st, 'Admin') !== false) {
                $statusCategory = 'admin';
                $adminCount++;
            } elseif ($st === 'Ditolak') {
                $statusCategory = 'rejected';
                $rejectedCount++;
            } else {
                $pendingCount++;
            }

            // Format tanggal
            $tglM = $row->tanggal_mulai;
            $tglS = $row->tanggal_selesai;
            $dM = (int)date('j', strtotime($tglM));
            $mM = $indoMonths[(int)date('n', strtotime($tglM))];
            $yM = date('Y', strtotime($tglM));
            $tglFormatted = "{$dM} {$mM} {$yM}";

            if ($tglM !== $tglS && !empty($tglS)) {
                $dS = (int)date('j', strtotime($tglS));
                $mS = $indoMonths[(int)date('n', strtotime($tglS))];
                $yS = date('Y', strtotime($tglS));
                if ($yM === $yS && $mM === $mS) {
                    $tglFormatted = "{$dM} - {$dS} {$mM} {$yM}";
                } else {
                    $tglFormatted = "{$dM} {$mM} {$yM} s/d {$dS} {$mS} {$yS}";
                }
            }

            $waktuFormatted = substr($row->jam_mulai, 0, 5) . ' - ' . substr($row->jam_selesai, 0, 5);

            $formatted[] = [
                'id' => (string)$row->id,
                'id_user' => (string)($row->id_user ?? ''),
                'id_ruangan' => (string)($row->id_ruangan ?? ''),
                'nama_lengkap' => $row->nama_lengkap,
                'nama_ruangan' => $row->nama_ruangan,
                'kode_ruangan' => $row->kode_ruangan,
                'lokasi' => $row->lokasi,
                'kapasitas' => $row->kapasitas,
                'nama_kategori' => $row->nama_kategori,
                'keterangan' => $row->keterangan,
                'tanggal_mulai' => $row->tanggal_mulai,
                'tanggal_selesai' => $row->tanggal_selesai,
                'tanggal_formatted' => $tglFormatted,
                'jam_mulai' => $row->jam_mulai,
                'jam_selesai' => $row->jam_selesai,
                'waktu_formatted' => $waktuFormatted,
                'status' => $row->status,
                'status_category' => $statusCategory,
                'alasan_penolakan' => $row->alasan_penolakan,
                'created_at' => $row->created_at,
                'created_at_formatted' => date('d/m/Y H:i', strtotime($row->created_at))
            ];
        }

        echo json_encode([
            'status' => 'success',
            'totalCount' => $totalCount,
            'pendingCount' => $pendingCount,
            'laboranCount' => $laboranCount,
            'kaurCount' => $kaurCount,
            'adminCount' => $adminCount,
            'rejectedCount' => $rejectedCount,
            'data' => $formatted
        ]);
    }

    public function approve($id)
    {
        header('Content-Type: application/json');

        // Validasi: Cek apakah akun Ka. Ur sudah memiliki Tanda Tangan Digital yang tersimpan
        if (!$this->Booking_model->has_signature($this->session->userdata('user_id'), 2)) {
            echo json_encode([
                'status'          => 'error',
                'needs_signature' => true,
                'signature_url'   => site_url('kaur/tanda-tangan'),
                'message'         => 'Anda belum memiliki Tanda Tangan Digital! Silakan buat atau unggah tanda tangan terlebih dahulu sebelum menyetujui permohonan agar surat izin resmi dapat disahkan dan diterbitkan.'
            ]);
            return;
        }

        $status = 'Disetujui Ka. Ur';

        $update = $this->Booking_model->update_status($id, $status);
        if($update) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Peminjaman berhasil Disetujui Resmi oleh Ka. Ur / Kepala Lab! Surat ber-QR Code siap diterbitkan.',
                'surat_url' => site_url('kaur/surat/' . $id)
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyetujui peminjaman']);
        }
    }

    public function reject($id)
    {
        header('Content-Type: application/json');
        $alasan = $this->input->post('alasan_penolakan', true);

        if (empty(trim($alasan))) {
            echo json_encode(['status' => 'error', 'message' => 'Catatan alasan penolakan wajib diisi!']);
            return;
        }

        $update = $this->Booking_model->update_status($id, 'Ditolak', $alasan);
        if($update) {
            echo json_encode(['status' => 'success', 'message' => 'Peminjaman resmi ditolak!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menolak peminjaman']);
        }
    }

    public function batch_approve()
    {
        header('Content-Type: application/json');
        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih setidaknya satu data peminjaman!']);
            return;
        }

        // Validasi: Cek apakah akun Ka. Ur sudah memiliki Tanda Tangan Digital yang tersimpan
        if (!$this->Booking_model->has_signature($this->session->userdata('user_id'), 2)) {
            echo json_encode([
                'status'          => 'error',
                'needs_signature' => true,
                'signature_url'   => site_url('kaur/tanda-tangan'),
                'message'         => 'Anda belum memiliki Tanda Tangan Digital! Silakan buat atau unggah tanda tangan terlebih dahulu sebelum menyetujui permohonan agar surat izin resmi dapat disahkan dan diterbitkan.'
            ]);
            return;
        }

        $status = 'Disetujui Ka. Ur';
        $update = $this->Booking_model->batch_update_status($ids, $status);
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' permohonan berhasil Disetujui Resmi oleh Ka. Ur!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyetujui data terpilih']);
        }
    }

    public function batch_reject()
    {
        header('Content-Type: application/json');
        $ids = $this->input->post('ids');
        $alasan = $this->input->post('alasan_penolakan', true);

        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih setidaknya satu data peminjaman!']);
            return;
        }

        if (empty(trim($alasan))) {
            echo json_encode(['status' => 'error', 'message' => 'Catatan alasan penolakan wajib diisi!']);
            return;
        }

        $update = $this->Booking_model->batch_update_status($ids, 'Ditolak', $alasan);
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' permohonan berhasil ditolak!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menolak data terpilih']);
        }
    }

    public function delete($id)
    {
        header('Content-Type: application/json');
        $delete = $this->Booking_model->delete_booking($id);
        if($delete) {
            echo json_encode(['status' => 'success', 'message' => 'Data permohonan berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    public function batch_delete()
    {
        header('Content-Type: application/json');
        $ids = $this->input->post('ids');
        if (empty($ids) || !is_array($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilih setidaknya satu data peminjaman!']);
            return;
        }

        $delete = $this->Booking_model->batch_delete_booking($ids);
        if ($delete) {
            echo json_encode(['status' => 'success', 'message' => count($ids) . ' data peminjaman berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data terpilih']);
        }
    }

    public function surat($id)
    {
        $data['booking'] = $this->Booking_model->get_booking_by_id($id);

        if (!$data['booking']) {
            show_404();
            return;
        }

        $cleanId = preg_replace('/[^0-9]/', '', (string)$data['booking']->id);
        if (empty($cleanId)) $cleanId = '0001';

        $data['title'] = 'Surat Resmi Peminjaman Ruangan - ' . ($data['booking']->kode_ruangan ?? 'IFIK');
        $data['nomor_surat'] = 'SURAT/LAB-IFIK/' . date('Y', strtotime($data['booking']->created_at)) . '/' . sprintf('%04d', (int)$cleanId);
        $data['qr_data'] = site_url('verifikasi/surat/' . $id);
        $data['penandatangan'] = $this->Booking_model->get_penandatangan($data['booking']->status);

        $this->load->view('kaur/surat_resmi', $data);
    }

    /**
     * Halaman Pengaturan Tanda Tangan Digital Kepala Urusan (Kaur)
     */
    public function tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', ['id' => $user_id])->row();
            if (!$user) {
                $user = $this->db->get_where('user', ['username' => 'kaur'])->row();
            }
        }
        if (!$user && $this->db->table_exists('users')) {
            $user = $this->db->get_where('users', ['id' => $user_id])->row();
        }

        $nip_kaur = $user ? (!empty($user->nip) ? $user->nip : (!empty($user->nidn_nim) ? $user->nidn_nim : (!empty($user->nim) ? $user->nim : '19820315002'))) : '19820315002';
        $nama_kaur = $user ? $user->name : ($this->session->userdata('name') ?: 'Kepala Urusan Lab & Sarpras');
        $tanda_tangan = $user ? ($user->ttd ?? ($user->tanda_tangan ?? null)) : null;

        $data = [
            'title'        => 'Pengaturan Tanda Tangan Digital - Ka. Ur / Kepala Lab',
            'user'         => $user,
            'nama'         => $nama_kaur,
            'nip'          => $nip_kaur,
            'tanda_tangan' => $tanda_tangan
        ];

        $this->load->view('kaur/tanda_tangan', $data);
    }

    /**
     * Simpan Tanda Tangan Digital Kaur (Canvas Base64 atau Upload File)
     */
    public function simpan_tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', ['id' => $user_id])->row();
            if (!$user) {
                $user = $this->db->get_where('user', ['username' => 'kaur'])->row();
            }
        }
        if (!$user && $this->db->table_exists('users')) {
            $user = $this->db->get_where('users', ['id' => $user_id])->row();
        }

        $nip_kaur = $user ? (!empty($user->nip) ? $user->nip : (!empty($user->nidn_nim) ? $user->nidn_nim : (!empty($user->nim) ? $user->nim : 'kaur'))) : 'kaur';
        $tipe = $this->input->post('tipe'); // 'canvas' atau 'upload'

        $uploadDir = FCPATH . 'uploads/signatures/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = '';

        if ($tipe === 'canvas') {
            $signatureData = $this->input->post('signature_data');
            if (empty($signatureData) || strpos($signatureData, 'data:image/png;base64,') !== 0) {
                $this->session->set_flashdata('error', 'Silakan goreskan tanda tangan pada canvas terlebih dahulu.');
                redirect('kaur/tanda-tangan');
                return;
            }

            // Decode base64 image
            $imageData = str_replace('data:image/png;base64,', '', $signatureData);
            $imageData = str_replace(' ', '+', $imageData);
            $decoded = base64_decode($imageData);

            if (!$decoded) {
                $this->session->set_flashdata('error', 'Format gambar tanda tangan tidak valid.');
                redirect('kaur/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_kaur ?: 'kaur');
            $filename = 'ttd_kaur_' . $cleanNip . '_' . time() . '.png';
            file_put_contents($uploadDir . $filename, $decoded);

        } else {
            // Upload file
            if (empty($_FILES['file_ttd']['name'])) {
                $this->session->set_flashdata('error', 'Pilih file gambar tanda tangan terlebih dahulu.');
                redirect('kaur/tanda-tangan');
                return;
            }

            $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $nip_kaur ?: 'kaur');
            $config = [
                'upload_path'   => $uploadDir,
                'allowed_types' => 'png|jpg|jpeg',
                'max_size'      => 3072, // 3MB
                'file_name'     => 'ttd_kaur_' . $cleanNip . '_' . time()
            ];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file_ttd')) {
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];
            } else {
                $err = $this->upload->display_errors('', '');
                $this->session->set_flashdata('error', 'Gagal mengunggah file tanda tangan: ' . $err);
                redirect('kaur/tanda-tangan');
                return;
            }
        }

        if (!empty($filename)) {
            // Hapus file tanda tangan lama jika ada
            $oldTtd = $user ? ($user->ttd ?? ($user->tanda_tangan ?? null)) : null;
            if (!empty($oldTtd) && file_exists($uploadDir . $oldTtd)) {
                @unlink($uploadDir . $oldTtd);
            }

            if ($this->db->table_exists('user')) {
                $updateData = [];
                if ($this->db->field_exists('ttd', 'user')) $updateData['ttd'] = $filename;
                if ($this->db->field_exists('tanda_tangan', 'user')) $updateData['tanda_tangan'] = $filename;
                
                if (!empty($updateData)) {
                    $this->db->group_start();
                    $this->db->where('id', $user_id);
                    if ($user && !empty($user->username)) {
                        $this->db->or_where('username', $user->username);
                    }
                    $this->db->or_where('role_id', 2);
                    $this->db->group_end();
                    $this->db->update('user', $updateData);
                }
            }
            if ($this->db->table_exists('users')) {
                $updateData = [];
                if ($this->db->field_exists('ttd', 'users')) $updateData['ttd'] = $filename;
                if ($this->db->field_exists('tanda_tangan', 'users')) $updateData['tanda_tangan'] = $filename;
                if (!empty($updateData)) {
                    $this->db->where('id', $user_id)->update('users', $updateData);
                }
            }
            $this->session->set_flashdata('success', 'Tanda tangan digital Ka. Ur berhasil disimpan dan siap disematkan pada surat resmi!');
        }

        redirect('kaur/tanda-tangan');
    }

    /**
     * Hapus Tanda Tangan Digital Kaur
     */
    public function hapus_tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', ['id' => $user_id])->row();
            if (!$user) {
                $user = $this->db->get_where('user', ['username' => 'kaur'])->row();
            }
        }
        if (!$user && $this->db->table_exists('users')) {
            $user = $this->db->get_where('users', ['id' => $user_id])->row();
        }
        $oldTtd = $user ? ($user->ttd ?? ($user->tanda_tangan ?? null)) : null;

        if (!empty($oldTtd)) {
            $filePath = FCPATH . 'uploads/signatures/' . $oldTtd;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            if ($this->db->table_exists('user')) {
                $updateData = [];
                if ($this->db->field_exists('ttd', 'user')) $updateData['ttd'] = null;
                if ($this->db->field_exists('tanda_tangan', 'user')) $updateData['tanda_tangan'] = null;
                if (!empty($updateData)) {
                    $this->db->group_start();
                    $this->db->where('id', $user_id);
                    if ($user && !empty($user->username)) {
                        $this->db->or_where('username', $user->username);
                    }
                    $this->db->or_where('role_id', 2);
                    $this->db->group_end();
                    $this->db->update('user', $updateData);
                }
            }
            if ($this->db->table_exists('users')) {
                $updateData = [];
                if ($this->db->field_exists('ttd', 'users')) $updateData['ttd'] = null;
                if ($this->db->field_exists('tanda_tangan', 'users')) $updateData['tanda_tangan'] = null;
                if (!empty($updateData)) {
                    $this->db->where('id', $user_id)->update('users', $updateData);
                }
            }
            $this->session->set_flashdata('success', 'Tanda tangan digital berhasil dihapus.');
        }

        redirect('kaur/tanda-tangan');
    }

    /**
     * Unduh File Tanda Tangan Digital Kaur
     */
    public function download_tanda_tangan()
    {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $user = null;
        if ($this->db->table_exists('user')) {
            $user = $this->db->get_where('user', ['id' => $user_id])->row();
            if (!$user) {
                $user = $this->db->get_where('user', ['username' => 'kaur'])->row();
            }
        }
        if (!$user && $this->db->table_exists('users')) {
            $user = $this->db->get_where('users', ['id' => $user_id])->row();
        }

        $tanda_tangan = $user ? ($user->ttd ?? ($user->tanda_tangan ?? null)) : null;

        if (empty($tanda_tangan)) {
            $this->session->set_flashdata('error', 'Belum ada tanda tangan yang tersimpan untuk diunduh.');
            redirect('kaur/tanda-tangan');
            return;
        }

        $filePath = FCPATH . 'uploads/signatures/' . $tanda_tangan;
        if (!file_exists($filePath)) {
            $this->session->set_flashdata('error', 'File tanda tangan fisik tidak ditemukan di server.');
            redirect('kaur/tanda-tangan');
            return;
        }

        $this->load->helper('download');
        $namaBersih = preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name ?? 'kaur');
        $cleanNip = preg_replace('/[^a-zA-Z0-9_-]/', '', $user->nip ?? ($user->nidn_nim ?? 'nip'));
        $downloadName = 'TTD_Kaur_' . $namaBersih . '_' . $cleanNip . '.png';

        force_download($downloadName, file_get_contents($filePath));
    }

    /**
     * Modul Approval Peminjaman Barang untuk Ka. Ur / Kepala Lab
     */
    public function barang()
    {
        $this->load->model('PeminjamanBarang_model', 'Peminjaman_model');
        $this->load->helper(['loan_progress', 'scm_date', 'fik_prodi']);

        $tab = $this->input->get('tab', true) ?: 'pending';
        $filters = ['action_role' => 'kaur'];

        if ($tab === 'pending') {
            $filters['status'] = 'Menunggu ACC Kaur';
        } elseif ($tab === 'approved') {
            $filters['status_in'] = ['Disetujui (Menunggu Pengambilan)', 'Disetujui (Menunggu Finalisasi QR)', 'Sedang Dipinjam', 'Selesai', 'Dikembalikan'];
        } elseif ($tab === 'rejected') {
            $filters['status'] = 'Ditolak';
        }

        $data['title'] = 'Approval Peminjaman Barang - Ka. Ur / Kepala Lab';
        $data['active_tab'] = $tab;
        $data['pending_count'] = $this->Peminjaman_model->count_actionable_peminjaman('kaur');
        $data['peminjaman_list'] = $this->Peminjaman_model->get_visible_peminjaman($filters, 50, 0);

        $this->load->view('kaur/barang/index', $data);
    }

    public function approve_barang($group_id)
    {
        $this->load->model('PeminjamanBarang_model', 'Peminjaman_model');
        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('kaur/barang');
        }

        $catatan = trim((string)$this->input->post('catatan_kaur', true));
        $is_external = (($peminjaman->jenis_peminjaman ?? '') === 'luar_kampus' || ($peminjaman->jenis_peminjaman ?? '') === 'external');

        if ($is_external) {
            $update = [
                'status' => 'Menunggu ACC Wadek',
                'status_kaur' => 'Disetujui',
                'catatan_kaur' => $catatan,
                'tgl_approve_kaur' => date('Y-m-d H:i:s'),
                'id_approver_kaur' => $this->session->userdata('id_user') ?: $this->session->userdata('username'),
                'status_wadek1' => 'Pending',
            ];
        } else {
            $update = [
                'status' => 'Disetujui (Menunggu Pengambilan)',
                'status_kaur' => 'Disetujui',
                'catatan_kaur' => $catatan,
                'tgl_approve_kaur' => date('Y-m-d H:i:s'),
                'id_approver_kaur' => $this->session->userdata('id_user') ?: $this->session->userdata('username'),
                'qr_locked' => 1,
            ];
        }

        $ok = $this->Peminjaman_model->approve_group_with_reservation(
            $group_id,
            ['Menunggu ACC Kaur', 'Menunggu Verifikasi Laboran'],
            $update
        );

        if ($ok) {
            if (!empty($peminjaman->id_user)) {
                $notif_pesan = $is_external
                    ? 'Pengajuan peminjaman barang luar kampus Anda telah disetujui Kaur dan diteruskan untuk persetujuan Wakil Dekan (Wadek).'
                    : 'Pengajuan peminjaman barang Anda telah disetujui resmi oleh Ka. Ur / Kepala Lab. Silakan ambil barang di Laboratorium.';
                $this->Peminjaman_model->create_notifikasi(
                    null,
                    $peminjaman->id_user,
                    'Peminjaman Disetujui Kaur',
                    $notif_pesan,
                    site_url('peminjaman_barang/riwayat')
                );
            }
            if ($is_external) {
                $this->Peminjaman_model->create_notifikasi(
                    'wadek',
                    null,
                    'Pengajuan Eksternal Menunggu ACC Wadek',
                    ($peminjaman->nama_peminjam ?? 'Peminjam') . ' mengajukan peminjaman luar kampus yang menunggu persetujuan Anda.',
                    site_url('wadek/peminjaman')
                );
                $this->session->set_flashdata('success', 'Pengajuan berhasil disetujui Ka. Ur dan diteruskan ke Wakil Dekan (Wadek).');
            } else {
                $this->Peminjaman_model->create_notifikasi(
                    'laboran',
                    null,
                    'Barang Siap Diserahkan',
                    ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah di-ACC Kaur. Barang siap diserahterimakan.',
                    site_url('peminjamanbarang/scanner')
                );
                $this->session->set_flashdata('success', 'Pengajuan berhasil disetujui resmi oleh Ka. Ur! Status sekarang siap untuk serah terima fisik barang.');
            }
        } else {
            $this->session->set_flashdata('error', 'Gagal menyetujui pengajuan peminjaman barang.');
        }
        redirect('kaur/barang');
    }

    public function reject_barang($group_id)
    {
        $this->load->model('PeminjamanBarang_model', 'Peminjaman_model');
        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('kaur/barang');
        }

        $catatan = trim((string)$this->input->post('catatan_kaur', true) ?: 'Ditolak oleh Kepala Urusan');
        $update = [
            'status' => 'Ditolak',
            'status_kaur' => 'Ditolak',
            'catatan_kaur' => $catatan,
            'tgl_approve_kaur' => date('Y-m-d H:i:s'),
            'id_approver_kaur' => $this->session->userdata('id_user') ?: $this->session->userdata('username'),
        ];

        $ok = $this->Peminjaman_model->reject_group_and_release($group_id, $update, ['Menunggu ACC Kaur', 'Menunggu Verifikasi Laboran']);
        if ($ok) {
            if (!empty($peminjaman->id_user)) {
                $this->Peminjaman_model->create_notifikasi(
                    null,
                    $peminjaman->id_user,
                    'Peminjaman Ditolak Kaur',
                    'Pengajuan peminjaman barang Anda ditolak oleh Kaur: ' . $catatan,
                    site_url('peminjaman_barang/riwayat')
                );
            }
            $this->session->set_flashdata('success', 'Pengajuan peminjaman barang berhasil ditolak.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menolak pengajuan.');
        }
        redirect('kaur/barang');
    }
}