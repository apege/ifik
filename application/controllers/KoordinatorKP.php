<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * =========================================================================================
 * CONTROLLER: KOORDINATOR KERJA PRAKTIK (KP) - PLOTTING PEMBIMBING 1
 * Path: application/controllers/KoordinatorKP.php
 * Pengembang: Nazril Rizki
 * =========================================================================================
 * 
 * Deskripsi:
 * Controller ini bertanggung jawab atas alur kerja penetapan (plotting) Dosen Pembimbing 1
 * bagi mahasiswa Kerja Praktik (KP) di lingkungan Fakultas Industri Kreatif.
 * 
 * Aturan Bisnis & Otorisasi:
 * 1. Hak Akses Khusus: Role 23 (Koordinator KP), Role 1 (Admin System), dan Role 22 (Super Admin).
 * 2. Prasyarat Tampil: Mahasiswa yang muncul pada antarmuka HANYA mahasiswa yang seluruh berkas
 *    persyaratannya telah disetujui (Approved) oleh Admin Layanan (LAA) pada Tab 1.
 * 3. Kapabilitas Plotting: Mendukung penetapan pembimbing tunggal (single) maupun massal (bulk/batch).
 * 4. Sinkronisasi Status: Saat pembimbing di-plot, status pengajuan mahasiswa otomatis diperbarui
 *    menjadi "Sudah Plot Pembimbing".
 */
class KoordinatorKP extends CI_Controller {

    /**
     * Konstruktor KoordinatorKP
     * 
     * Memuat model, library, dan helper yang dibutuhkan serta bertindak sebagai gerbang pengaman (guard)
     * yang memvalidasi sesi autentikasi dan otorisasi role pengguna. Mendukung deteksi request AJAX
     * untuk memberikan respon status HTTP 401/403 berformat JSON.
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        // 1. Memuat dependensi utama model, helper form/url/text, dan library session
        $this->load->model('Kp_model');
        $this->load->helper(array('form', 'url', 'text'));
        $this->load->library('session');

        // 2. Deteksi otomatis apakah request datang melalui AJAX / Fetch API
        $isAjax = $this->input->is_ajax_request() ||
                  (strpos($this->uri->uri_string(), 'ajax_') !== false) ||
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // 3. Validasi status login pengguna
        if (!$this->session->userdata('logged_in')) {
            if ($isAjax) {
                $this->output->set_status_header(401)->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Sesi berakhir. Silakan login kembali.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Silakan login terlebih dahulu untuk mengakses menu Koordinator KP.');
            redirect('login');
            return;
        }

        // 4. Validasi Role: Khusus Role 23 (Koordinator KP), Role 1 (Admin System), atau Role 22 (Super Admin)
        $role_id = (int)$this->session->userdata('role_id');
        if ($role_id !== 23 && $role_id !== 1 && $role_id !== 22) {
            if ($isAjax) {
                $this->output->set_status_header(403)->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 'error', 'message' => 'Akses ditolak. Halaman ini khusus untuk Koordinator KP dan Admin.']));
                exit;
            }
            $this->session->set_flashdata('error', 'Akses ditolak! Halaman ini khusus untuk Koordinator KP dan Admin.');
            redirect('login');
            return;
        }
    }

    /**
     * Index: Default redirect ke menu Plotting Pembimbing KP
     * 
     * @access public
     * @return void
     */
    public function index() {
        $this->plotting();
    }

    /**
     * Halaman Utama: Plotting Dosen Pembimbing 1 KP
     * 
     * Mengambil kumpulan data mahasiswa KP yang sudah di-ACC persyaratannya oleh Admin LAA
     * melalui Kp_model->get_mahasiswa_for_plotting_kp(), daftar seluruh dosen pembimbing yang aktif,
     * membaca parameter sub-tab aktif (?subtab=belum_diplot|sudah_diplot), lalu me-render view koordinator_kp/plotting.
     * 
     * @access public
     * @return void
     */
    public function plotting() {
        // Ambil data pengajuan mahasiswa terverifikasi yang terbagi atas grup 'belum_diplot' dan 'sudah_diplot'
        $data_plotting = $this->Kp_model->get_mahasiswa_for_plotting_kp();
        
        // Ambil daftar dosen aktif berkategori pembimbing (Role 2, 3, 6, 7, 9, 23)
        $dosen_list    = $this->Kp_model->get_dosen_list_kp();
        
        // Sub-tab navigasi aktif (default ke 'belum_diplot')
        $sub_tab       = $this->input->get('subtab') ?: 'belum_diplot';

        // Susun payload data untuk dikirim ke antarmuka view
        $data['title']        = 'Plotting Pembimbing 1 Kerja Praktik (KP) - Koordinator KP';
        $data['sub_tab']      = $sub_tab;
        $data['belum_diplot'] = $data_plotting['belum_diplot'];
        $data['sudah_diplot'] = $data_plotting['sudah_diplot'];
        $data['total']        = $data_plotting['total'];
        $data['dosen_list']   = $dosen_list;

        $this->load->view('koordinator_kp/plotting', $data);
    }

    /**
     * AJAX Endpoint: Simpan Plotting Pembimbing 1 (Single atau Massal)
     * 
     * Menerima array atau string NIM mahasiswa terpilih dan NIP/identifier dosen pembimbing.
     * Melakukan iterasi pembaruan ke tabel tb_kp, memperbarui status pengajuan menjadi "Sudah Plot Pembimbing",
     * me-resolve nama lengkap dosen, dan mengembalikan respon JSON terstruktur.
     * 
     * @access public
     * @return void Mengembalikan output JSON {status, message, count}
     */
    public function ajax_plot_pembimbing() {
        // 1. Ambil data input POST NIM mahasiswa (mendukung single string atau array massal)
        $nims             = $this->input->post('nims');
        $dosen_identifier = trim((string)$this->input->post('dosen_identifier'));

        if (empty($nims)) {
            $nim = trim((string)$this->input->post('nim'));
            if (!empty($nim)) {
                $nims = [$nim];
            }
        }
        if (is_string($nims)) {
            $nims = explode(',', $nims);
        }

        // 2. Validasi kelengkapan parameter NIM
        if (empty($nims) || !is_array($nims)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'NIM Mahasiswa wajib disertakan.']));
            return;
        }

        // 3. Validasi pemilihan Dosen Pembimbing 1
        if (empty($dosen_identifier)) {
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => false, 'message' => 'Silakan pilih Dosen Pembimbing 1 terlebih dahulu.']));
            return;
        }

        // 4. Lakukan looping pembaruan Dosen Pembimbing 1 untuk setiap mahasiswa yang dipilih
        $success_count = 0;
        foreach ($nims as $n) {
            $n = trim((string)$n);
            if (!empty($n)) {
                $res = $this->Kp_model->plot_pembimbing_kp($n, $dosen_identifier);
                if (!empty($res['status'])) {
                    $success_count++;
                }
            }
        }

        // 5. Terjemahkan identifier dosen menjadi nama lengkap bergelar untuk pesan konfirmasi yang ramah
        $dosen_nama = $this->Kp_model->resolve_dosen_name($dosen_identifier);
        $msg = count($nims) === 1
            ? "Dosen Pembimbing 1 ({$dosen_nama}) berhasil ditetapkan untuk mahasiswa NIM {$nims[0]}."
            : "Berhasil menetapkan Dosen Pembimbing 1 ({$dosen_nama}) untuk {$success_count} mahasiswa terpilih secara massal!";

        // 6. Kembalikan respon JSON sukses
        $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => $msg,
                'count'   => $success_count
            ]));
    }

    /**
     * AJAX Endpoint: Ambil data realtime Plotting KP
     * 
     * Mengembalikan payload JSON terbaru berisi data mahasiswa grup belum_diplot dan sudah_diplot.
     * Digunakan oleh fungsi JavaScript reloadPlottingData() untuk menyinkronkan antarmuka tabel
     * secara langsung tanpa perlu melakukan refresh halaman penuh pada peramban.
     * 
     * @access public
     * @return void Mengembalikan output JSON {status, belum_diplot, sudah_diplot, total}
     */
    public function ajax_get_plotting_data() {
        $data_plotting = $this->Kp_model->get_mahasiswa_for_plotting_kp();
        $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status'       => true,
                'belum_diplot' => $data_plotting['belum_diplot'],
                'sudah_diplot' => $data_plotting['sudah_diplot'],
                'total'        => $data_plotting['total']
            ]));
    }
}
