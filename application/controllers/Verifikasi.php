<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifikasi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Booking_model');
        $this->load->helper('url');
    }

    /**
     * Halaman Publik Surat Resmi Peminjaman Ruangan (Langsung Format Surat PDF Resmi)
     */
    public function surat($id)
    {
        $id = (int)$id;

        // Ambil data peminjaman beserta detail ruangan & kategori
        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan, ruangan.lokasi, ruangan.kapasitas, kategori_ruangan.nama_kategori');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->join('kategori_ruangan', 'kategori_ruangan.id = ruangan.id_kategori', 'left');
        $this->db->where('peminjaman.id', $id);
        $data['booking'] = $this->db->get()->row();

        if (!$data['booking']) {
            show_404();
            return;
        }

        $data['title'] = 'Surat Resmi Peminjaman Ruangan - ' . ($data['booking']->kode_ruangan ?? 'IFIK');
        $data['nomor_surat'] = 'SURAT/LAB-IFIK/' . date('Y', strtotime($data['booking']->created_at)) . '/' . sprintf('%04d', $data['booking']->id);
        $data['qr_data'] = site_url('verifikasi/surat/' . $id);
        $data['penandatangan'] = $this->Booking_model->get_penandatangan($data['booking']->status);

        $this->load->view('kaur/surat_resmi', $data);
    }

    /**
     * Tampilan Dokumen Asli / Cetak Surat Resmi (PDF Printable View)
     */
    public function cetak($id)
    {
        $id = (int)$id;

        $this->db->select('peminjaman.*, ruangan.nama_ruangan, ruangan.kode_ruangan, ruangan.lokasi, ruangan.kapasitas, kategori_ruangan.nama_kategori');
        $this->db->from('peminjaman');
        $this->db->join('ruangan', 'ruangan.id = peminjaman.id_ruangan', 'left');
        $this->db->join('kategori_ruangan', 'kategori_ruangan.id = ruangan.id_kategori', 'left');
        $this->db->where('peminjaman.id', $id);
        $data['booking'] = $this->db->get()->row();

        if (!$data['booking']) {
            show_404();
            return;
        }

        $data['title'] = 'Surat Resmi Peminjaman Ruangan - ' . ($data['booking']->kode_ruangan ?? 'IFIK');
        $data['nomor_surat'] = 'SURAT/LAB-IFIK/' . date('Y', strtotime($data['booking']->created_at)) . '/' . sprintf('%04d', $data['booking']->id);
        $data['qr_data'] = site_url('verifikasi/surat/' . $id);
        $data['penandatangan'] = $this->Booking_model->get_penandatangan($data['booking']->status);

        $this->load->view('kaur/surat_resmi', $data);
    }
}
