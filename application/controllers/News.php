<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form', 'file']);
        $this->load->library(['session', 'upload']);
        $this->load->model('News_model');
    }

    // ─── Helper: cek role admin ────────────────────────────────────────────
    private function _is_admin()
    {
        return $this->session->userdata('role_id') == 1;
    }

    private function _require_admin()
    {
        if (!$this->_is_admin()) {
            redirect('dashboard');
        }
    }

    // ─── PUBLIC: Halaman detail berita ─────────────────────────────────────
    public function detail($id = null)
    {
        if ($id === null) {
            redirect('dashboard');
        }

        $berita = $this->News_model->get_by_id($id);

        if (!$berita) {
            // Fallback mock berita jika ID belum ada di database
            $berita = (object) array(
                'id'           => $id,
                'judul'        => 'Pameran Karya & Inovasi Mahasiswa FIK 2026 Sukses Digelar',
                'kategori'     => 'Akademik & Event',
                'penulis'      => 'Tim Redaksi FIK Portal',
                'tanggal'      => date('Y-m-d'),
                'gambar'       => 'assets/images/ifik_portal_3d_render.jpg',
                'excerpt'      => 'Ratusan karya inovatif dari mahasiswa dipamerkan dalam ajang tahunan yang dihadiri oleh praktisi industri kreatif terkemuka.',
                'konten'       => '<p>Fakultas Industri Kreatif (FIK) Telkom University kembali menggelar ajang pameran karya tahunan mahasiswa yang menampilkan ratusan karya desain, animasi, sinematografi, dan produk digital interaktif.</p><p>Acara yang berlangsung selama tiga hari ini menarik antusiasme lebih dari 1.500 pengunjung, termasuk perwakilan dari studio industri kreatif ternama, investor startup, dan civitas akademika.</p><p>Dekan FIK menyampaikan apresiasi tinggi atas dedikasi para mahasiswa dalam menghasilkan karya berstandar industri yang siap bersaing di pasar global.</p>',
                'published'    => 1,
                'border_style' => 'none'
            );
        }

        $data['berita'] = $berita;
        $this->load->view('news/detail', $data);
    }

    // ─── ADMIN: Halaman Newsroom ────────────────────────────────────────────
    public function index()
    {
        $data['all_berita'] = $this->News_model->get_all();
        $this->load->view('news/admin_newsroom', $data);
    }

    // ─── ADMIN: Save (Insert / Update) ─────────────────────────────────────
    public function save()
    {
        header('Content-Type: application/json');

        $id       = $this->input->post('id');
        $judul    = $this->input->post('judul', true);
        $kategori = $this->input->post('kategori', true);
        $excerpt  = $this->input->post('excerpt', true);
        $konten   = $this->input->post('konten');   // Boleh HTML
        $tanggal  = $this->input->post('tanggal', true);
        $published = (int)$this->input->post('published');

        if (empty($judul) || empty($tanggal)) {
            echo json_encode(['status' => 'error', 'message' => 'Judul dan tanggal wajib diisi!']);
            return;
        }

        // ─── Upload gambar (jika ada) ───────────────────────────────────
        $gambar_path = null;
        if (!empty($_FILES['gambar']['name'])) {
            // Pastikan folder ada
            $upload_dir = FCPATH . 'uploads/news/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $config_upload = [
                'upload_path'   => $upload_dir,
                'allowed_types' => 'jpg|jpeg|png|webp|gif',
                'max_size'      => 10240, // 10MB
                'file_name'     => 'news_' . time() . '_' . bin2hex(random_bytes(4)),
                'overwrite'     => false,
            ];
            $this->upload->initialize($config_upload);

            if ($this->upload->do_upload('gambar')) {
                $upload_data = $this->upload->data();
                $gambar_path = 'uploads/news/' . $upload_data['file_name'];

                // Hapus gambar lama jika update
                if ($id) {
                    $old = $this->News_model->get_by_id($id);
                    if ($old && $old->gambar && strpos($old->gambar, 'uploads/news/') !== false) {
                        @unlink(FCPATH . $old->gambar);
                    }
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Upload gambar gagal: ' . $this->upload->display_errors('', '')]);
                return;
            }
        }

        $border_style = $this->input->post('border_style', true);
        if (!$border_style) $border_style = 'none';

        $data = [
            'judul'        => $judul,
            'kategori'     => $kategori ?: 'Berita Acara',
            'excerpt'      => $excerpt,
            'konten'       => $konten,
            'tanggal'      => $tanggal,
            'published'    => $published,
            'border_style' => $border_style,
        ];
        if ($gambar_path !== null) {
            $data['gambar'] = $gambar_path;
        }

        if ($id) {
            // UPDATE
            $ok = $this->News_model->update_news($id, $data);
            echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $ok ? 'Berita berhasil diperbarui!' : 'Gagal memperbarui berita.']);
        } else {
            // INSERT
            $ok = $this->News_model->insert_news($data);
            $new_id = $this->db->insert_id();
            echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $ok ? 'Berita berhasil ditambahkan!' : 'Gagal menyimpan berita.', 'id' => $new_id]);
        }
    }

    // ─── ADMIN: Delete ─────────────────────────────────────────────────────
    public function delete($id)
    {
        header('Content-Type: application/json');

        $ok = $this->News_model->delete_news($id);
        echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $ok ? 'Berita berhasil dihapus!' : 'Gagal menghapus berita.']);
    }

    // ─── ADMIN: Toggle publish ─────────────────────────────────────
    public function toggle($id)
    {
        header('Content-Type: application/json');

        $ok = $this->News_model->toggle_publish($id);
        if ($ok) {
            $berita = $this->News_model->get_by_id($id);
            $status = $berita ? ($berita->published ? 'published' : 'unpublished') : 'unknown';
            echo json_encode(['status' => 'success', 'published' => $berita ? (int)$berita->published : 0, 'message' => $status === 'published' ? 'Berita dipublikasikan!' : 'Berita disembunyikan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status.']);
        }
    }

    // ─── PUBLIC API: JSON untuk dashboard ──────────────────────────────────
    public function get_all_json()
    {
        header('Content-Type: application/json');
        $list = $this->News_model->get_published();
        $result = [];
        foreach ($list as $b) {
            $result[] = [
                'id'       => $b->id,
                'judul'    => $b->judul,
                'kategori' => $b->kategori,
                'excerpt'  => $b->excerpt,
                'gambar'   => $b->gambar ? base_url($b->gambar) : base_url('assets/images/background.png'),
                'tanggal'  => $this->_format_tanggal($b->tanggal),
                'url'      => base_url('index.php/news/detail/' . $b->id),
            ];
        }
        echo json_encode($result);
    }

    // ─── Helper: Format tanggal ke Bahasa Indonesia ─────────────────────────
    private function _format_tanggal($date_str)
    {
        $bulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
            '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
            '10' => 'Oktober', '11' => 'November',  '12' => 'Desember'
        ];
        $parts = explode('-', $date_str);
        if (count($parts) === 3) {
            return (int)$parts[2] . ' ' . ($bulan[$parts[1]] ?? '') . ' ' . $parts[0];
        }
        return $date_str;
    }
}
