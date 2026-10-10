<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller: PeminjamanBarang (Project IFIK - Modul Laboran)
 * Diadaptasi persis dari application/controllers/admin/Peminjaman.php di SCM FIK.
 */
class PeminjamanBarang extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'upload']);
        $this->load->helper(['url', 'scm_date']);
        $this->load->model('PeminjamanBarang_model', 'Peminjaman_model');
        $this->load->model('Aset_model');
    }

    public function index() {
        redirect('peminjamanbarang/scanner');
    }

    public function scanner() {
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id === 2) {
            redirect('kaur/barang');
            return;
        }
        $data['title'] = 'Scanner QR Serah Terima';
        $data['scanner_label'] = 'Scanner QR Peminjaman';
        $data['scanner_desc'] = 'Scan QR transaksi dari akun peminjam untuk proses serah barang.';
        $data['back_url'] = site_url('peminjamanbarang');
        $data['back_label'] = 'Data Peminjaman';
        $this->load->view('laboran/barang/scanner_qr', $data);
    }

    public function serah_terima($group_id) {
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id === 2) {
            $this->session->set_flashdata('info', 'Halaman serah terima barang khusus untuk petugas Laboran. Untuk persetujuan resmi, silakan gunakan menu Approval Kaur.');
            redirect('kaur/barang');
            return;
        }

        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi dari QR tidak ditemukan.');
            redirect('peminjamanbarang/scanner');
        }

        if (in_array(($peminjaman->status ?? ''), ['Sedang Dipinjam', 'Dipinjam'], true)) {
            $data['title'] = 'Validasi Pengembalian Barang';
            $data['peminjaman'] = $peminjaman;
            $data['qr_valid'] = true;
            $data['qr_message'] = $this->qr_message_for($peminjaman);
            $this->load->view('laboran/barang/validasi_pengembalian', $data);
            return;
        }

        $st = (string)($peminjaman->status ?? '');
        $status_kaur = (string)($peminjaman->status_kaur ?? 'Pending');
        $status_kaprodi = (string)($peminjaman->status_kaprodi ?? 'Pending');

        if (in_array($st, ['Selesai', 'Dikembalikan', 'Ditolak'], true)) {
            $workflow_stage = 'invalid';
            $qr_valid = false;
            $qr_message = $this->qr_message_for($peminjaman);
        } elseif ($st === 'Menunggu ACC Kaprodi' && $status_kaprodi !== 'Disetujui') {
            $workflow_stage = 'menunggu_acc';
            $qr_valid = false;
            $qr_message = 'Pengajuan masih menunggu persetujuan Kaprodi. Barang belum boleh diserahterimakan.';
        } else {
            // Semua status pengajuan (Menunggu Verifikasi Laboran, Menunggu ACC Kaur, Disetujui, dll)
            // langsung siap untuk diverifikasi dan dikonfirmasi serah terima oleh Laboran di meja lab
            $workflow_stage = 'serah_terima';
            $qr_valid = true;
            $qr_message = 'Pengajuan siap untuk verifikasi fisik dan serah terima barang oleh Laboran.';
        }

        $data['title'] = 'Serah Terima & Verifikasi Barang';
        $data['peminjaman'] = $peminjaman;
        $data['workflow_stage'] = $workflow_stage;
        $data['qr_payload'] = $this->Peminjaman_model->get_qr_payload($group_id);
        $data['qr_valid'] = $qr_valid;
        $data['qr_message'] = $qr_message;
        $this->load->view('laboran/barang/serah_terima', $data);
    }

    public function verifikasi_laboran($group_id) {
        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('peminjamanbarang/scanner');
        }

        $catatan = trim((string)($this->input->post('catatan_serah', true) ?: $this->input->post('catatan_verifikasi', true) ?: ''));
        $update = [
            'status' => 'Menunggu ACC Kaur',
            'status_laboran' => 'Disetujui',
            'catatan_laboran' => $catatan,
            'tgl_approve_laboran' => date('Y-m-d H:i:s'),
            'id_approver_laboran' => $this->session->userdata('id_user') ?: $this->session->userdata('username'),
            'status_kaur' => 'Pending',
        ];

        $ok = $this->Peminjaman_model->approve_group_with_reservation(
            $group_id,
            ['Menunggu Verifikasi Laboran', 'Menunggu Pengecekan Laboran', 'Menunggu Persetujuan', 'Menunggu ACC Kaprodi'],
            $update
        );

        if ($ok) {
            if (!empty($peminjaman->id_user)) {
                $this->Peminjaman_model->create_notifikasi(
                    null,
                    $peminjaman->id_user,
                    'Verifikasi Laboran Selesai',
                    'Pengajuan peminjaman barang Anda telah diverifikasi oleh Laboran dan saat ini diteruskan ke Kaur untuk persetujuan resmi.',
                    site_url('peminjaman_barang/riwayat')
                );
            }
            $this->Peminjaman_model->create_notifikasi(
                'kaur',
                null,
                'Pengajuan Menunggu ACC Kaur',
                ($peminjaman->nama_peminjam ?? 'Peminjam') . ' sudah diverifikasi Laboran dan menunggu persetujuan Anda.',
                site_url('kaur/barang')
            );
            $this->session->set_flashdata('success', 'Pengajuan berhasil diverifikasi Laboran dan diteruskan ke Kaur untuk persetujuan resmi.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses verifikasi atau reservasi stok barang tidak mencukupi.');
        }
        redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
    }

    public function approve_kaur($group_id) {
        $roleId = (int)$this->session->userdata('role_id');
        if ($roleId !== 2 && $roleId !== 1 && $roleId !== 22) {
            $this->session->set_flashdata('error', 'Akses ditolak! Hanya Kepala Urusan (Kaur) yang berhak menyetujui tahap ini.');
            redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
            return;
        }

        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('peminjamanbarang/scanner');
            return;
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
            $this->session->set_flashdata('error', 'Gagal menyetujui pengajuan.');
        }
        redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
    }

    public function tolak_kaur($group_id) {
        $roleId = (int)$this->session->userdata('role_id');
        if ($roleId !== 2 && $roleId !== 1 && $roleId !== 22) {
            $this->session->set_flashdata('error', 'Akses ditolak! Hanya Kepala Urusan (Kaur) yang berhak menolak tahap ini.');
            redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
            return;
        }

        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('peminjamanbarang/scanner');
            return;
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
            $this->session->set_flashdata('success', 'Pengajuan berhasil ditolak.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menolak pengajuan.');
        }
        redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
    }

    public function proses_serah($group_id) {
        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan.');
            redirect('peminjamanbarang/scanner');
        }

        // Cek bahwa transaksi belum ditolak atau sudah selesai
        if (in_array(($peminjaman->status ?? ''), ['Ditolak', 'Selesai', 'Dikembalikan', 'Sedang Dipinjam'], true)) {
            $this->session->set_flashdata('error', 'Status transaksi (' . $peminjaman->status . ') tidak valid untuk serah terima.');
            redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
            return;
        }

        if (($peminjaman->status_kaprodi ?? '') !== 'Disetujui' && ($peminjaman->status ?? '') === 'Menunggu ACC Kaprodi') {
            $this->session->set_flashdata('error', 'Pengajuan belum disetujui oleh Kaprodi.');
            redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
            return;
        }

        $this->db->trans_start();
        $locked_peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id_for_update($group_id);
        if (!$locked_peminjaman) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'QR transaksi sudah dipakai atau tidak lagi aktif.');
            redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
        }
        $peminjaman = $locked_peminjaman;
        $items = !empty($peminjaman->detail_barang) ? $peminjaman->detail_barang : [$peminjaman];

        // Terapkan jumlah yang diedit laboran (tidak boleh melebihi jumlah pinjam asli)
        $items = $this->apply_edited_jumlah($items);

        $evidence = $this->upload_multiple_evidence('foto_serah');
        if ($evidence === false) {
            $this->db->trans_rollback();
            redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
        }

        foreach ($items as $item) {
            if (!$this->Peminjaman_model->convert_reservation_to_borrowed($item->id_peminjaman, $item->jumlah_pinjam)) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('error', 'Reservasi stok tidak valid atau sudah berubah. Silakan periksa kembali transaksi.');
                redirect('peminjamanbarang/serah_terima/' . rawurlencode($group_id));
            }
            if ((int) $item->jumlah_pinjam > 0) {
                $this->Aset_model->increment_total_peminjaman($item->id_aset);
            }
        }
        $laboran_id = $this->session->userdata('id_user') ?: $this->session->userdata('username');
        $this->Peminjaman_model->update_group_status($group_id, [
            'status' => 'Sedang Dipinjam',
            'status_laboran' => 'Disetujui',
            'status_kaur' => 'Disetujui',
            'qr_locked' => 1,
            'tgl_approve_laboran' => date('Y-m-d H:i:s'),
            'id_approver_laboran' => $laboran_id,
            'catatan_laboran' => trim((string) $this->input->post('catatan_serah', true)),
        ]);
        foreach ($evidence as $file) {
            $this->Peminjaman_model->insert_evidence([
                'id_peminjaman' => $peminjaman->id_peminjaman,
                'group_id' => $peminjaman->group_id,
                'jenis' => 'serah_terima',
                'nama_file' => $file['path'],
                'original_name' => $file['original_name'],
                'uploaded_by' => $laboran_id,
            ]);
        }
        if (!empty($evidence)) {
            $this->Peminjaman_model->update_group_status($group_id, ['foto_bukti' => $evidence[0]['path']]);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() && !empty($peminjaman->id_user)) {
            $this->Peminjaman_model->create_notifikasi(
                null,
                $peminjaman->id_user,
                'Barang sudah dipinjam',
                'Serah terima barang sudah dikonfirmasi Laboran. Gunakan QR transaksi yang sama saat pengembalian.',
                site_url('peminjamanbarang/riwayat')
            );
        }

        $this->session->set_flashdata($this->db->trans_status() ? 'success' : 'error', $this->db->trans_status() ? 'Barang berhasil diserahkan ke peminjam.' : 'Gagal memproses serah terima.');
        redirect('peminjamanbarang/scanner');
    }

    public function validasi_pengembalian($group_id) {
        $group_id = rawurldecode($group_id);
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id($group_id);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Transaksi dari QR pengembalian tidak ditemukan.');
            redirect('peminjamanbarang/scanner');
        }

        $data['title'] = 'Validasi Pengembalian Barang';
        $data['peminjaman'] = $peminjaman;
        $data['qr_valid'] = in_array(($peminjaman->status ?? ''), ['Sedang Dipinjam', 'Dipinjam'], true);
        $data['qr_message'] = $this->qr_message_for($peminjaman);
        $this->load->view('laboran/barang/validasi_pengembalian', $data);
    }

    public function kembalikan($id_peminjaman) {
        $peminjaman = $this->Peminjaman_model->get_peminjaman_by_id($id_peminjaman);
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data peminjaman tidak ditemukan.');
            redirect('peminjamanbarang/scanner');
        }

        $kondisi_kembali = $this->input->post('kondisi_saat_kembali', true) ?: null;
        if (!in_array($kondisi_kembali, ['Baik', 'Rusak', 'Hilang'], true)) {
            $this->session->set_flashdata('error', 'Kondisi pengembalian wajib dipilih dengan benar.');
            redirect('peminjamanbarang/validasi_pengembalian/' . rawurlencode($peminjaman->group_id));
        }

        $catatan_pengembalian = trim((string) $this->input->post('catatan_pengembalian', true));
        if (in_array($kondisi_kembali, ['Rusak', 'Hilang'], true)) {
            if ($catatan_pengembalian === '') {
                $this->session->set_flashdata('error', 'Keterangan wajib diisi jika kondisi barang Rusak atau Hilang.');
                redirect('peminjamanbarang/validasi_pengembalian/' . rawurlencode($peminjaman->group_id));
            }
        }

        $this->db->trans_start();
        $group_id = $peminjaman->group_id ?: 'single-' . $id_peminjaman;
        $locked_peminjaman = $this->Peminjaman_model->get_peminjaman_by_group_id_for_update($group_id);
        if (!$locked_peminjaman || !in_array($locked_peminjaman->status, ['Sedang Dipinjam', 'Dipinjam'], true)) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', 'QR transaksi sudah dipakai atau peminjaman sudah selesai dikembalikan.');
            redirect('peminjamanbarang/scanner');
        }
        $peminjaman = $locked_peminjaman;
        $items = !empty($peminjaman->detail_barang) ? $peminjaman->detail_barang : [$peminjaman];

        $foto_pengembalian = $this->upload_evidence_pengembalian();
        if ($foto_pengembalian === false) {
            $this->db->trans_rollback();
            redirect('peminjamanbarang/validasi_pengembalian/' . rawurlencode($group_id));
        }

        foreach ($items as $item) {
            if (!empty($item->id_aset)) {
                $make_available = $kondisi_kembali === 'Baik';
                if (!$this->Peminjaman_model->return_stock_allocation($item->id_peminjaman, $item->jumlah_pinjam, $make_available)) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error', 'Gagal memproses alokasi stok pengembalian.');
                    redirect('peminjamanbarang/validasi_pengembalian/' . rawurlencode($group_id));
                }
            }
        }

        $laboran_id = $this->session->userdata('id_user') ?: $this->session->userdata('user_id');
        $update_data = [
            'status' => 'Selesai',
            'kondisi_saat_kembali' => $kondisi_kembali,
            'catatan_laboran' => $catatan_pengembalian !== '' ? $catatan_pengembalian : null,
            'tanggal_kembali_actual' => date('Y-m-d'),
            'tgl_approve_laboran' => date('Y-m-d H:i:s'),
            'id_approver_laboran' => $laboran_id,
        ];
        if (!empty($foto_pengembalian)) {
            $update_data['foto_pengembalian'] = $foto_pengembalian;
        }

        $this->Peminjaman_model->update_group_status($group_id, $update_data);
        $this->db->trans_complete();

        if ($this->db->trans_status() && !empty($peminjaman->id_user)) {
            $this->Peminjaman_model->create_notifikasi(
                null,
                $peminjaman->id_user,
                'Pengembalian selesai',
                'Pengembalian barang untuk transaksi ' . $group_id . ' sudah selesai diverifikasi Laboran. Kondisi: ' . $kondisi_kembali,
                site_url('peminjamanbarang/riwayat')
            );
        }

        $this->session->set_flashdata($this->db->trans_status() ? 'success' : 'error', $this->db->trans_status() ? 'Pengembalian barang berhasil dicatat dan stok telah disesuaikan.' : 'Gagal memproses pengembalian barang.');
        redirect('peminjamanbarang/scanner');
    }

    private function apply_edited_jumlah(array $items) {
        $jumlah_edit = $this->input->post('jumlah_barang', true);
        if (empty($jumlah_edit) || !is_array($jumlah_edit)) {
            return $items;
        }

        foreach ($items as $item) {
            $kode = $item->kode_aset ?? null;
            $jumlah_asli = (int) ($item->jumlah_pinjam ?? 0);

            if ($kode !== null && array_key_exists($kode, $jumlah_edit)) {
                $jumlah_baru = (int) $jumlah_edit[$kode];
                $jumlah_baru = max(0, min($jumlah_baru, $jumlah_asli));
                $item->jumlah_pinjam = $jumlah_baru;
            }
        }

        return $items;
    }

    private function upload_multiple_evidence($field) {
        if (empty($_FILES[$field]['name'])) {
            return [];
        }

        $path = './assets/uploads/bukti_serah/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $files = [];
        foreach ((array) $_FILES[$field]['name'] as $index => $name) {
            if (trim((string) $name) === '') {
                continue;
            }
            $_FILES['single_evidence'] = [
                'name' => $_FILES[$field]['name'][$index],
                'type' => $_FILES[$field]['type'][$index],
                'tmp_name' => $_FILES[$field]['tmp_name'][$index],
                'error' => $_FILES[$field]['error'][$index],
                'size' => $_FILES[$field]['size'][$index],
            ];
            $this->upload->initialize([
                'upload_path' => $path,
                'allowed_types' => 'jpg|jpeg|png',
                'max_size' => 5120,
                'encrypt_name' => true,
            ]);
            if (!$this->upload->do_upload('single_evidence')) {
                $this->session->set_flashdata('error', 'Upload foto gagal: ' . $this->upload->display_errors('', ''));
                return false;
            }
            $uploaded = $this->upload->data();
            $files[] = [
                'path' => 'assets/uploads/bukti_serah/' . $uploaded['file_name'],
                'original_name' => $uploaded['client_name'],
            ];
        }
        return $files;
    }

    private function upload_evidence_pengembalian() {
        if (empty($_FILES['foto_pengembalian']['name'])) {
            return null;
        }
        $path = './assets/uploads/pengembalian/';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $this->upload->initialize([
            'upload_path' => $path,
            'allowed_types' => 'jpg|jpeg|png',
            'max_size' => 5120,
            'encrypt_name' => true,
        ]);
        if (!$this->upload->do_upload('foto_pengembalian')) {
            $this->session->set_flashdata('error', 'Upload foto pengembalian gagal: ' . $this->upload->display_errors('', ''));
            return false;
        }
        $data = $this->upload->data();
        return 'assets/uploads/pengembalian/' . $data['file_name'];
    }

    private function qr_message_for($peminjaman) {
        if (($peminjaman->status ?? '') === 'Selesai' || ($peminjaman->status ?? '') === 'Dikembalikan') {
            return 'Transaksi sudah selesai dikembalikan. QR ini sudah tidak berlaku.';
        }
        if (($peminjaman->status ?? '') === 'Sedang Dipinjam' || ($peminjaman->status ?? '') === 'Dipinjam') {
            return 'Barang sedang aktif dipinjam. Silakan periksa fisik dan proses pengembalian.';
        }
        return 'QR terbaca dan siap diproses.';
    }

    public function kalender() {
        redirect('peminjaman_barang/kalender');
    }

    public function get_updated_peminjaman() {
        redirect('peminjaman_barang/get_updated_peminjaman');
    }
}
