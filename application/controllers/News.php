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
            // Fallback sample news mapping matching sample items from berita.php
            $sample_news = array(
                1 => array(
                    'id'           => 1,
                    'judul'        => 'Pameran Karya Mahasiswa FIK 2026 Sukses Digelar',
                    'kategori'     => 'Akademik & Event',
                    'penulis'      => 'Tim Redaksi FIK Portal',
                    'tanggal'      => '2026-08-12',
                    'gambar'       => 'assets/images/Fakultas.jpg',
                    'excerpt'      => 'Ratusan karya inovatif dari mahasiswa dipamerkan dalam ajang tahunan yang dihadiri oleh praktisi industri kreatif terkemuka.',
                    'konten'       => '<p>Fakultas Industri Kreatif kembali membuktikan komitmennya dalam mencetak talenta-talenta muda berbakat melalui ajang tahunan yang paling ditunggu, yakni "Pameran Karya Mahasiswa FIK 2026". Acara yang berlangsung meriah selama tiga hari berturut-turut ini sukses menarik perhatian tidak hanya civitas akademika, tetapi juga para praktisi dan pelaku industri kreatif nasional.</p><p>Dengan mengusung tema <em>"Future Intersection: Where Art Meets Technology"</em>, pameran kali ini menghadirkan lebih dari 200 karya inovatif. Mulai dari instalasi seni interaktif, prototipe desain produk futuristik, hingga eksplorasi WebGL dan realitas virtual (VR) yang memungkinkan pengunjung masuk ke dalam dunia digital tanpa batas dan berinteraksi langsung dengan karya-karya visual tingkat tinggi.</p><blockquote>"Karya-karya yang dipamerkan tahun ini benar-benar melampaui ekspektasi kami. Mahasiswa tidak hanya berpikir tentang estetika visual, tetapi juga memprioritaskan fungsionalitas dan interaksi manusia dengan teknologi di masa depan," <br><br><span style="font-size:1rem; color:#64748b; font-style:normal;">— Dekan Fakultas Industri Kreatif</span></blockquote><p>Selain pameran karya, acara ini juga diramaikan dengan berbagai sesi seminar, <em>workshop</em>, dan <em>talkshow</em> yang menghadirkan narasumber ternama dari berbagai perusahaan teknologi dan studio desain terkemuka di Indonesia.</p>',
                    'published'    => 1,
                    'border_style' => 'swirl'
                ),
                2 => array(
                    'id'           => 2,
                    'judul'        => 'Workshop Desain Interaktif Bersama Pakar UI/UX',
                    'kategori'     => 'Workshop & Skill',
                    'penulis'      => 'Tim Redaksi FIK Portal',
                    'tanggal'      => '2026-08-05',
                    'gambar'       => 'assets/images/multimedia.jpg',
                    'excerpt'      => 'Mahasiswa diajak untuk mendalami tren UI/UX dan interaksi 3D web modern dalam workshop intensif selama dua hari.',
                    'konten'       => '<p>Fakultas Industri Kreatif sukses menggelar Workshop Desain Interaktif yang berfokus pada perkembangan tren UI/UX terkini serta integrasi komponen 3D interaktif berbasis web. Workshop ini menghadirkan praktisi senior UI/UX dari berbagai perusahaan teknologi terkemuka.</p><p>Para peserta diberikan kesempatan untuk mempraktikkan langsung pembuatan antarmuka digital modern yang mengutamakan User Experience (UX), aksesibilitas, dan performa tinggi.</p>',
                    'published'    => 1,
                    'border_style' => 'neon'
                ),
                3 => array(
                    'id'           => 3,
                    'judul'        => 'Peluncuran Sistem Layanan Terpadu IFIK Versi Baru',
                    'kategori'     => 'Pengumuman',
                    'penulis'      => 'Tim IT FIK',
                    'tanggal'      => '2026-07-28',
                    'gambar'       => 'assets/images/ifik_portal_3d_render.jpg',
                    'excerpt'      => 'Sistem IFIK kini hadir dengan wajah baru yang lebih premium, responsif, dan interaktif untuk memudahkan seluruh civitas akademika.',
                    'konten'       => '<p>Fakultas Industri Kreatif resmi meluncurkan pembaruan besar pada Sistem Layanan Terpadu IFIK Portal. Versi baru ini menyajikan antarmuka yang lebih intuitif, animasi yang smooth, serta integrasi layanan administrasi yang lebih cepat dan efisien.</p><p>Sistem baru ini dirancang untuk mendukung kebutuhan mahasiswa, dosen wali, koordinator TA, hingga pimpinan fakultas dalam memantau dan mengelola berkas administrasi secara real-time.</p>',
                    'published'    => 1,
                    'border_style' => 'geometric'
                ),
                4 => array(
                    'id'           => 4,
                    'judul'        => 'Prestasi Gemilang Tim Riset FIK di Tingkat Nasional',
                    'kategori'     => 'Prestasi',
                    'penulis'      => 'Humas FIK',
                    'tanggal'      => '2026-07-15',
                    'gambar'       => 'assets/images/Aula1.jpg',
                    'excerpt'      => 'Penelitian kolaboratif dosen dan mahasiswa tentang pemanfaatan AI dalam desain komunikasi visual berhasil memenangkan hibah.',
                    'konten'       => '<p>Tim riset kolaboratif yang terdiri dari dosen dan mahasiswa Fakultas Industri Kreatif berhasil meraih penghargaan bergengsi dalam kompetisi inovasi teknologi tingkat nasional. Riset yang diusung mengkaji penerapan kecerdasan buatan (AI) dalam mempercepat proses ideasi dan produksi desain komunikasi visual.</p><p>Pencapaian ini membuktikan kualitas riset dan komitmen FIK dalam memajukan ilmu pengetahuan dan teknologi di bidang industri kreatif.</p>',
                    'published'    => 1,
                    'border_style' => 'polaroid'
                ),
                5 => array(
                    'id'           => 5,
                    'judul'        => 'Kunjungan Studi Industri Kreatif ke Studio Animasi',
                    'kategori'     => 'Kunjungan Industri',
                    'penulis'      => 'Tim Redaksi FIK Portal',
                    'tanggal'      => '2026-07-02',
                    'gambar'       => 'assets/images/Fakultas.jpg',
                    'excerpt'      => 'Mahasiswa semester akhir berkesempatan melihat langsung alur kerja produksi animasi 3D kelas dunia dan berdiskusi.',
                    'konten'       => '<p>Dalam rangka memperluas wawasan praktis, mahasiswa Fakultas Industri Kreatif melakukan kunjungan studi ke studio animasi ternama. Kegiatan ini bertujuan memberikan gambaran nyata mengenai pipeline produksi animasi 3D, pengelolaan aset visual, serta standar kerja di dunia industri.</p><p>Diharapkan pengalaman ini dapat menginspirasi para mahasiswa dalam menyelesaikan tugas akhir dan mempersiapkan diri menghadapi tantangan profesional setelah lulus.</p>',
                    'published'    => 1,
                    'border_style' => 'none'
                ),
            );

            if (isset($sample_news[(int)$id])) {
                $berita = (object)$sample_news[(int)$id];
            } else {
                $berita = (object) array(
                    'id'           => $id,
                    'judul'        => 'Berita Informatif FIK #' . $id,
                    'kategori'     => 'Informasi',
                    'penulis'      => 'Tim Redaksi FIK Portal',
                    'tanggal'      => date('Y-m-d'),
                    'gambar'       => 'assets/images/background.png',
                    'excerpt'      => 'Informasi terbaru mengenai kegiatan dan pengumuman di lingkungan Fakultas Industri Kreatif.',
                    'konten'       => '<p>Berikut adalah rincian informasi dan berita terkini dari Fakultas Industri Kreatif Telkom University.</p>',
                    'published'    => 1,
                    'border_style' => 'none'
                );
            }
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
