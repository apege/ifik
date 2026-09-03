<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Dynamic Data Setup based on $lab_key
$labs_data = [
    'multimedia' => [
        'title' => 'Lab Multimedia & Game',
        'subtitle' => 'Fasilitas Komputer Kinerja Tinggi untuk Game Development, Animasi Digital, & VR',
        'badge' => 'Laboratorium Utama',
        'status' => 'Tersedia',
        'status_class' => 'status-open',
        'model' => base_url('assets/3D/' . rawurlencode('lab.multi media (1).glb')),
        'orbit' => '45deg 75deg 85%',
        'fov' => '22deg',
        'bg_gradient' => 'radial-gradient(circle at 50% 60%, rgba(216, 184, 150, 0.35) 0%, rgba(248, 243, 238, 0.95) 100%)',
        'border_color' => 'rgba(216, 184, 150, 0.45)',
        'glow_color' => 'rgba(216, 184, 150, 0.55)',
        'photo' => file_exists(FCPATH . 'assets/images/multimedia.jpg') ? base_url('assets/images/multimedia.jpg') : base_url('assets/images/lab_multimedia_real.jpg'),
        'photo_fallback' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1000&auto=format&fit=crop',
        'location' => 'Gedung Industri Kreatif - Lantai 3 (Ruang FIK-302)',
        'capacity' => '36 Unit Workstation',
        'hours' => 'Senin - Jumat | 08:00 - 17:00 WIB',
        'desc' => 'Laboratorium Multimedia & Game difasilitasi dengan komputer berspesifikasi tinggi yang dirancang khusus untuk memenuhi kebutuhan pengembangan game 3D modern, simulasi realitas virtual (VR), rendering animasi kompleks, serta perancangan media interaktif.',
        'specs' => [
            ['icon' => '💻', 'title' => 'Hardware Spesifikasi', 'desc' => 'Processor Intel Core i9 / AMD Ryzen 9, GPU NVIDIA RTX 4080 16GB, RAM 64GB DDR5, SSD NVMe 2TB.'],
            ['icon' => '🖥️', 'title' => 'Dual Monitor Setup', 'desc' => 'Setiap meja dilengkapi Dual Monitor 27 inch IPS 4K dengan akurasi warna 100% sRGB untuk kenyamanan multitasking.'],
            ['icon' => '🥽', 'title' => 'Perangkat VR & AR', 'desc' => 'Tersedia Meta Quest 3, HTC Vive Pro 2, dan Haptics Controllers untuk pengujian game VR.'],
            ['icon' => '⚙️', 'title' => 'Software Terinstal', 'desc' => 'Unreal Engine 5.4, Unity 3D, Autodesk Maya, Blender 4.2, Adobe Creative Cloud 2024, Substance Painter.']
        ],
        'rules' => [
            'Wajib melakukan booking slot peminjaman melalui sistem E-Ticketing IFIK.',
            'Dilarang membawa makanan dan minuman manis beresiko di area laboratorium.',
            'Penggunaan VR Headset wajib di bawah pengawasan asisten laboratorium.',
            'Menjaga kebersihan dan merapikan workstation setelah sesi selesai.'
        ]
    ],
    'aula' => [
        'title' => 'Aula Utama Fakultas',
        'subtitle' => 'Ruang Hall Serbaguna Berkapasitas Besar untuk Seminar, Pameran Karya, & Event',
        'badge' => 'Fasilitas Utama',
        'status' => 'Tersedia',
        'status_class' => 'status-open',
        'model' => base_url('assets/3D/Aula.glb'),
        'orbit' => '-135deg 75deg 85%',
        'fov' => '22deg',
        'bg_gradient' => 'radial-gradient(circle at 50% 60%, rgba(234, 88, 12, 0.22) 0%, rgba(254, 243, 237, 0.95) 100%)',
        'border_color' => 'rgba(234, 88, 12, 0.35)',
        'glow_color' => 'rgba(234, 88, 12, 0.45)',
        'photo' => file_exists(FCPATH . 'assets/images/Aula1.jpg') ? base_url('assets/images/Aula1.jpg') : (file_exists(FCPATH . 'assets/images/aula.jpg') ? base_url('assets/images/aula.jpg') : base_url('assets/images/lab_aula_real.jpg')),
        'photo_fallback' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1000&auto=format&fit=crop',
        'location' => 'Gedung Utama Fakultas Industri Kreatif - Lantai 1',
        'capacity' => '300+ Kursi Peserta',
        'hours' => 'Senin - Sabtu | 07:30 - 21:00 WIB',
        'desc' => 'Aula Utama Fakultas Industri Kreatif merupakan ruang pertemuan dan kegiatan serbaguna skala besar yang fleksibel untuk berbagai acara akademik maupun non-akademik, seperti pameran karya seni visual, seminar nasional, workshop kreatif, dan sidang terbuka.',
        'specs' => [
            ['icon' => '🔊', 'title' => 'Professional Audio', 'desc' => 'Line Array System Dolby Surround, Mixer Console Digital 32-Channel, serta 8 Set Mic Wireless.'],
            ['icon' => '📺', 'title' => 'Stage LED Video Wall', 'desc' => 'Layar P3 Indoor LED Video Wall berukuran 6x3 Meter beresolusi tinggi dengan Lightning Stage Rigging.'],
            ['icon' => '❄️', 'title' => 'Full Air Conditioner', 'desc' => 'Pendingin ruangan terpusat (Central AC) yang menjamin kenyamanan seluruh peserta acara.'],
            ['icon' => '🛋️', 'title' => 'Holding & Control Room', 'desc' => 'Dilengkapi Ruang VIP Transit Pembicara dan Operator Sound Control Booth khusus.']
        ],
        'rules' => [
            'Pengajuan izin tempat minimal H-7 sebelum tanggal pelaksanaan acara.',
            'Wajib menyertakan surat rekomendasi dari Kemahasiswaan atau Dekanat.',
            'Dilarang menempelkan perekat permanen pada LED Wall atau dinding panggung.',
            'Pembersihan dan sterilisasi panggung dilakukan bersama tim operasional usai acara.'
        ]
    ],
    'cintiq' => [
        'title' => 'Lab Tablet Cintiq',
        'subtitle' => 'Studio Digital Illustration, Concept Art, Komik, & 2D Animation',
        'badge' => 'Studio Ilustrasi',
        'status' => 'Tersedia',
        'status_class' => 'status-open',
        'model' => base_url('assets/3D/' . rawurlencode('lab tab cintiq (1).glb')),
        'orbit' => '45deg 75deg 85%',
        'fov' => '22deg',
        'bg_gradient' => 'radial-gradient(circle at 50% 60%, rgba(71, 130, 158, 0.25) 0%, rgba(240, 246, 250, 0.95) 100%)',
        'border_color' => 'rgba(71, 130, 158, 0.4)',
        'glow_color' => 'rgba(71, 130, 158, 0.5)',
        'photo' => file_exists(FCPATH . 'assets/images/sintiq.jpg') ? base_url('assets/images/sintiq.jpg') : (file_exists(FCPATH . 'assets/images/cintiq.jpg') ? base_url('assets/images/cintiq.jpg') : base_url('assets/images/lab_cintiq_real.jpg')),
        'photo_fallback' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?q=80&w=1000&auto=format&fit=crop',
        'location' => 'Gedung Industri Kreatif - Lantai 2 (Ruang FIK-205)',
        'capacity' => '30 Unit Cintiq Pro',
        'hours' => 'Senin - Jumat | 08:00 - 17:00 WIB',
        'desc' => 'Lab Tablet Cintiq disiapkan khusus bagi mahasiswa program studi Desain Komunikasi Visual dan Animasi untuk mengasah keahlian menggambar digital. Setiap station dilengkapi Pen Display profesional berakurasi warna tinggi.',
        'specs' => [
            ['icon' => '🎨', 'title' => 'Wacom Cintiq Pro 24', 'desc' => 'Pen Display 4K Ultra HD dengan akurasi warna Adobe RGB 99% dan permukaan kaca pro-etched.'],
            ['icon' => '✏️', 'title' => 'Wacom Pro Pen 2', 'desc' => 'Stylus pen tanpa baterai dengan 8,192 level sensitivitas tekanan dan dukungan kemiringan (tilt).'],
            ['icon' => '🖥️', 'title' => 'Lengan Stand Ergonomis', 'desc' => 'Flex Arm dapat diatur ketinggian dan kemiringannya sesuai kenyamanan posisi menggambar.'],
            ['icon' => '🖌️', 'title' => 'Software Drawing', 'desc' => 'Clip Studio Paint EX, Adobe Illustrator, Adobe Photoshop CC, TVPaint Animation, ZBrush 3D.']
        ],
        'rules' => [
            'Simpan Stylus Pen dan Kabel pen pendukung di tempat semula setelah selesai bekerja.',
            'Gunakan kain micro-fiber khusus saat membersihkan layar glass Cintiq.',
            'Dilarang menggunakan benda tajam yang berpotensi menggores permukaan layar.',
            'Cadangkan hasil karya digital di cloud storage pribadi sebelum meninggalkan lab.'
        ]
    ],
    'greenscreen' => [
        'title' => 'Lab Green Screen Studio',
        'subtitle' => 'Studio Produksi Virtual, Motion Capture, Video Editing, & Lighting Setup',
        'badge' => 'Studio Visual FX',
        'status' => 'Tersedia',
        'status_class' => 'status-open',
        'model' => base_url('assets/3D/greenscreen.glb'),
        'orbit' => '45deg 75deg 85%',
        'fov' => '22deg',
        'bg_gradient' => 'radial-gradient(circle at 50% 60%, rgba(34, 197, 94, 0.25) 0%, rgba(240, 253, 244, 0.95) 100%)',
        'border_color' => 'rgba(34, 197, 94, 0.4)',
        'glow_color' => 'rgba(34, 197, 94, 0.5)',
        'photo' => file_exists(FCPATH . 'assets/images/greenscreen.jpg') ? base_url('assets/images/greenscreen.jpg') : 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=1000&auto=format&fit=crop',
        'photo_fallback' => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?q=80&w=1000&auto=format&fit=crop',
        'location' => 'Gedung Industri Kreatif - Lantai 2 (Ruang FIK-208)',
        'capacity' => 'Studio Prod. & Lighting Rig',
        'hours' => 'Senin - Jumat | 08:00 - 17:00 WIB',
        'desc' => 'Lab Green Screen Studio dilengkapi dengan dinding Cyclorama Green Screen berukuran besar, sistem tata cahaya profesional Studio Lighting Rig, peredam suara khusus, serta kamera sinematik untuk kebutuhan pengambilan gambar Visual Effects (VFX), live streaming, dan produksi video profesional.',
        'specs' => [
            ['icon' => '🎥', 'title' => 'Cyclorama Wall', 'desc' => 'Dinding lengkung Chroma Key Green Screen seamless untuk penggantian latar visual secara real-time.'],
            ['icon' => '💡', 'title' => 'Studio Lighting Rig', 'desc' => 'Set lampu Aputure Studio LED Panel, Softbox, & Spotlights dengan kontrol DMX Console.'],
            ['icon' => '🎙️', 'title' => 'Audio Isolation', 'desc' => 'Peredam akustik dinding dan mikrofon wireless Telefunken / Rode Broadcast.'],
            ['icon' => '🎬', 'title' => 'Production Switcher', 'desc' => 'Blackmagic ATEM Mini Pro & Teleprompter untuk siaran langsung & produksi program tv.']
        ],
        'rules' => [
            'Gunakan sepatu studio khusus atau buka alas kaki sebelum melangkah di atas cyclorama green screen.',
            'Dilarang menggeser posisi lampu rig tanpa izin dan pendampingan teknisi studio.',
            'Matikan seluruh sakelar daya utama lighting dan AC setelah sesi produksi selesai.',
            'Menjaga kebersihan dan kerapian seluruh properti shooting.'
        ]
    ],
    'incubator' => [
        'title' => 'Lab Inkubator Bisnis & Tech',
        'subtitle' => 'Ruang Kolaborasi Startup, Ideasi Bisnis Kreatif, Pitching & Co-Working Space',
        'badge' => 'Inkubator Startup',
        'status' => 'Tersedia',
        'status_class' => 'status-open',
        'model' => base_url('assets/3D/' . rawurlencode('lab incubator.glb')),
        'orbit' => '45deg 75deg 85%',
        'fov' => '22deg',
        'bg_gradient' => 'radial-gradient(circle at 50% 60%, rgba(168, 85, 247, 0.25) 0%, rgba(250, 245, 255, 0.95) 100%)',
        'border_color' => 'rgba(168, 85, 247, 0.4)',
        'glow_color' => 'rgba(168, 85, 247, 0.5)',
        'photo' => file_exists(FCPATH . 'assets/images/incubator.jpg') ? base_url('assets/images/incubator.jpg') : 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1000&auto=format&fit=crop',
        'photo_fallback' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1000&auto=format&fit=crop',
        'location' => 'Gedung Industri Kreatif - Lantai 4 (Ruang FIK-401)',
        'capacity' => '50+ Seat Co-Working',
        'hours' => 'Senin - Sabtu | 08:00 - 20:00 WIB',
        'desc' => 'Lab Inkubator Bisnis & Tech dirancang sebagai hub inkubasi bagi mahasiswa dan peneliti yang sedang membangun proyek startup digital, inovasi teknologi, dan industri kreatif. Dilengkapi dengan area co-working fleksibel, ruang pitching investor, dan fasilitas pendampingan bisnis.',
        'specs' => [
            ['icon' => '🚀', 'title' => 'Flexible Co-Working', 'desc' => 'Meja modular yang mudah diatur ulang untuk kerja tim, diskusi kelompok, atau curah pendapat.'],
            ['icon' => '📊', 'title' => 'Pitching Arena', 'desc' => 'Ruang presentasi dilengkapi Smart TV 85 inch & High-Def Soundbar untuk pitching project.'],
            ['icon' => '🌐', 'title' => 'High-Speed Wifi 6E', 'desc' => 'Jaringan internet dedicated fiber optic berkecepatan hingga 1 Gbps untuk kelancaran riset.'],
            ['icon' => '☕', 'title' => 'Brainstorming Lounge', 'desc' => 'Area santai dengan papan whiteboard interaktif untuk diskusi konsep kreatif.']
        ],
        'rules' => [
            'Sesi penggunaan area pitching disarankan untuk dibooking H-1 melalui e-ticketing.',
            'Menjaga suasana kondusif dan saling menghormati antar tim startup pengguna area co-working.',
            'Membuang sampah pada tempat yang disediakan di area pantry luar lab.',
            'Merapikan kembali konfigurasi meja modular setelah sesi diskusi kelompok usai.'
        ]
    ],
    'mac' => [
        'title' => 'Lab Workstation Apple Mac',
        'subtitle' => 'Studio Editing Video, Color Grading, Sound Design, & Desktop Publishing',
        'badge' => 'Studio Apple Mac',
        'status' => 'Tersedia',
        'status_class' => 'status-open',
        'model' => base_url('assets/3D/' . rawurlencode('lab Mac (1).glb')),
        'orbit' => '45deg 75deg 85%',
        'fov' => '22deg',
        'bg_gradient' => 'radial-gradient(circle at 50% 60%, rgba(14, 165, 233, 0.25) 0%, rgba(240, 249, 255, 0.95) 100%)',
        'border_color' => 'rgba(14, 165, 233, 0.4)',
        'glow_color' => 'rgba(14, 165, 233, 0.5)',
        'photo' => file_exists(FCPATH . 'assets/images/mac.jpg') ? base_url('assets/images/mac.jpg') : 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1000&auto=format&fit=crop',
        'photo_fallback' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1000&auto=format&fit=crop',
        'location' => 'Gedung Industri Kreatif - Lantai 3 (Ruang FIK-305)',
        'capacity' => '32 Workstation Mac',
        'hours' => 'Senin - Jumat | 08:00 - 17:00 WIB',
        'desc' => 'Lab Workstation Apple Mac merupakan studio komputasi khusus berbasis macOS yang dioptimalkan untuk kebutuhan pasca-produksi film, color grading sinematik, desain tata letak penerbitan, komposisi musik digital, serta pengembangan aplikasi ekosistem Apple.',
        'specs' => [
            ['icon' => '🖥️', 'title' => 'Apple Mac Studio M2 Max', 'desc' => '32-core GPU, 64GB Unified Memory, SSD 1TB NVMe untuk rendering video ultra-cepat.'],
            ['icon' => '📺', 'title' => 'Apple Studio Display 27"', 'desc' => 'Layar Retina 5K dengan akurasi warna P3 dan teknologi True Tone.'],
            ['icon' => '🎛️', 'title' => 'DaVinci Resolve Console', 'desc' => 'Hardware Speed Editor & Studio Control Panel untuk color grading presisi.'],
            ['icon' => '🎵', 'title' => 'Pro Tools & Logic Pro', 'desc' => 'Audio Interface Logic Pro X & Final Cut Pro X terinstal penuh.']
        ],
        'rules' => [
            'Dilarang menginstal aplikasi secara tidak resmi tanpa persetujuan Admin IT/Laboran.',
            'Selalu log out dari akun Apple ID pribadi setelah selesai menggunakan komputer Mac.',
            'Simpan file pekerjaan pada folder data pribadi atau eksternal drive (HDD/SSD).',
            'Dilarang mencabut atau mengubah jalur kabel konektivitas Thunderbolt/USB-C.'
        ]
    ]
];

$active_key = isset($labs_data[$lab_key]) ? $lab_key : null;

// If not found in hardcoded array, look up from DB
if (!$active_key && !empty($all_ruangan)) {
    foreach ($all_ruangan as $r) {
        $n = strtolower(trim($r->nama_ruangan));
        $c = strtolower(trim($r->kode_ruangan));

        // Map to key the same way as lab.php / header.php
        if (strpos($n, 'multimedia') !== false) $rkey = 'multimedia';
        elseif (strpos($n, 'aula') !== false) $rkey = 'aula';
        elseif (strpos($n, 'cintiq') !== false || strpos($n, 'tablet') !== false || strpos($n, 'sablon') !== false) $rkey = 'cintiq';
        elseif (strpos($n, 'green') !== false) $rkey = 'greenscreen';
        elseif (strpos($n, 'inkubator') !== false || strpos($n, 'incubator') !== false) $rkey = 'incubator';
        elseif (strpos($n, 'mac') !== false || strpos($n, '3d printing') !== false) $rkey = 'mac';
        else {
            $rkey = preg_replace('/[^a-z0-9]/', '', $c);
            if (empty($rkey)) $rkey = 'room_' . $r->id;
        }

        if ($rkey === $lab_key || (string)$r->id === (string)$lab_key || 'room_' . $r->id === (string)$lab_key || $c === (string)$lab_key || $n === (string)$lab_key) {
            $active_key = $lab_key;
            $img_url = !empty($r->foto) ? (strpos($r->foto, 'http') === 0 ? $r->foto : base_url($r->foto)) : base_url('assets/images/multimedia.jpg');
            $model_url = !empty($r->model_3d) ? (strpos($r->model_3d, 'http') === 0 ? $r->model_3d : base_url($r->model_3d)) : '';

            $labs_data[$lab_key] = [
                'id_ruangan'   => $r->id,
                'title'        => $r->nama_ruangan,
                'subtitle'     => !empty($r->tagline) ? $r->tagline : 'Fasilitas Ruangan Fakultas Industri Kreatif',
                'badge'        => 'Laboratorium FIK',
                'status'       => $r->status ?? 'Tersedia',
                'status_class' => 'status-open',
                'model'        => $model_url,
                'orbit'        => '45deg 75deg 85%',
                'fov'          => '22deg',
                'bg_gradient'  => 'radial-gradient(circle at 50% 60%, rgba(234, 88, 12, 0.18) 0%, rgba(255, 251, 245, 0.97) 100%)',
                'border_color' => 'rgba(234, 88, 12, 0.3)',
                'glow_color'   => 'rgba(234, 88, 12, 0.4)',
                'photo'        => $img_url,
                'photo_fallback' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1000&auto=format&fit=crop',
                'location'     => !empty($r->lokasi) ? $r->lokasi : 'Gedung Fakultas Industri Kreatif',
                'capacity'     => !empty($r->jumlah_unit) ? $r->jumlah_unit : (!empty($r->kapasitas) ? $r->kapasitas . ' Orang' : '-'),
                'hours'        => !empty($r->jam_operasional) ? $r->jam_operasional : 'Senin - Jumat | 08:00 - 17:00 WIB',
                'desc'         => !empty($r->deskripsi) ? $r->deskripsi : 'Fasilitas ruangan praktikum dan perkuliahan di Fakultas Industri Kreatif.',
                'specs'        => !empty($r->spesifikasi_fasilitas)
                    ? array_map(function($s) {
                        return ['icon' => '⚙️', 'title' => 'Spesifikasi', 'desc' => trim($s)];
                      }, array_filter(explode("\n", $r->spesifikasi_fasilitas)))
                    : [['icon' => '🏫', 'title' => 'Fasilitas', 'desc' => 'Informasi fasilitas lengkap tersedia di lokasi.']],
                'rules'        => !empty($r->tata_tertib)
                    ? array_filter(array_map('trim', explode("\n", $r->tata_tertib)))
                    : ['Ikuti tata tertib yang berlaku di ruangan.'],
            ];
            break;
        }
    }
}

// Final fallback: jika masih tidak ditemukan, pakai multimedia
if (!$active_key) {
    $active_key = 'multimedia';
}

$lab = $labs_data[$active_key];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lab['title'] ?> - Fakultas Industri Kreatif (IFIK)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Model Viewer CDN -->
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>

    <style>
        @media (pointer: fine) {
            *, *::before, *::after, html, body, a, button, input, select, textarea, label, summary, model-viewer, model-viewer::part(default-canvas), [role="button"], [onclick] {
                cursor: none !important;
            }
        }

        :root {
            --primary: #ea580c;
            --primary-hover: #c2410c;
            --bg-color: #090d16;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(234, 88, 12, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
            background-image: 
                radial-gradient(at 0% 0%, rgba(234, 88, 12, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 50%, rgba(234, 88, 12, 0.08) 0px, transparent 50%);
        }

        /* Container Main Page */
        .page-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 120px 24px 60px 24px;
        }

        /* Breadcrumb Bar */
        .breadcrumb-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            color: #94a3b8;
        }

        .breadcrumb-bar a {
            color: #ea580c;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease;
        }

        .breadcrumb-bar a:hover {
            color: #f97316;
        }

        /* Main Glass Card Wrapper */
        .detail-card-main {
            background: #ffffff;
            border: 1px solid rgba(234, 88, 12, 0.18);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            color: var(--text-main);
            position: relative;
        }

        /* Layout Grid: 3D Showcase (Kiri) & Info Detail (Kanan) */
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 40px;
            align-items: center;
        }

        /* 3D Model Display Card */
        .lab-showcase-box {
            position: relative;
            width: 100%;
            height: 480px;
            border-radius: 24px;
            background: radial-gradient(circle at 50% 60%, rgba(234, 88, 12, 0.08) 0%, rgba(248, 250, 252, 0.95) 100%);
            border: 1px solid rgba(234, 88, 12, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
        }

        /* Backlight Glow di belakang model */
        .lab-showcase-box::before {
            content: '';
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 280px;
            height: 220px;
            background: radial-gradient(circle, rgba(234, 88, 12, 0.25) 0%, rgba(251, 146, 60, 0.08) 50%, transparent 75%);
            filter: blur(35px);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        /* Indicators Garis Dempetan dengan 3 State: Passed (Border Oren), Active (Solid Lebar Tebal), Upcoming (Biasa) */
        .showcase-line-indicators {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4px; /* Dempetan seperti di gambar */
            margin-top: 18px;
        }

        .detail-line {
            width: 36px;
            height: 5px;
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.12);
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
            box-sizing: border-box;
        }

        .detail-line:hover {
            background: rgba(234, 88, 12, 0.3);
        }

        /* State 1: Line sebelum nya yang sudah dilewati (Hanya Border Oren) */
        .detail-line.passed {
            background: transparent;
            border: 1.5px solid #ea580c;
        }

        /* State 2: Line aktif saat ini (Solid Oren Lebar & Tebal) */
        .detail-line.active {
            width: 80px;
            height: 6px;
            background: #ea580c;
            border: 1px solid #ea580c;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(234, 88, 12, 0.45);
        }

        .showcase-content-view {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 10;
            overflow: hidden;
            border-radius: 24px;
        }

        .showcase-content-view img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 24px;
            transition: transform 0.4s ease;
        }

        .showcase-content-view img:hover {
            transform: scale(1.03);
        }

        .lab-showcase-box model-viewer {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 10;
        }

        .badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(234, 88, 12, 0.1);
            color: #ea580c;
            border: 1px solid rgba(234, 88, 12, 0.25);
            margin-bottom: 12px;
        }

        .lab-title-main {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.2;
            margin-bottom: 10px;
            font-family: 'Outfit', sans-serif;
        }

        .lab-subtitle {
            font-size: 1.05rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        /* Quick Meta Badges */
        .meta-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 14px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
        }

        .meta-item span.icon {
            font-size: 1.1rem;
        }

        .desc-paragraph {
            font-size: 0.96rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-primary-action {
            background: var(--primary);
            color: #ffffff;
            padding: 14px 28px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px rgba(234, 88, 12, 0.3);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary-action:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(234, 88, 12, 0.4);
        }

        .btn-secondary-action {
            background: #f1f5f9;
            color: #334155;
            padding: 14px 24px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .btn-secondary-action:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Section Title */
        .section-header-block {
            margin-top: 50px;
            margin-bottom: 24px;
        }

        .section-header-block h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            font-family: 'Outfit', sans-serif;
        }

        /* Specs Cards Grid */
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .spec-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 24px;
            transition: all 0.3s ease;
        }

        .spec-card:hover {
            border-color: rgba(234, 88, 12, 0.3);
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(234, 88, 12, 0.08);
            transform: translateY(-3px);
        }

        .spec-card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .spec-card-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(234, 88, 12, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .spec-card h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
        }

        .spec-card p {
            font-size: 0.88rem;
            color: #64748b;
            line-height: 1.6;
        }

        /* Rules Block */
        .rules-block {
            margin-top: 40px;
            background: #fff7ed;
            border: 1px solid rgba(234, 88, 12, 0.25);
            border-radius: 20px;
            padding: 28px;
        }

        .rules-block h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ea580c;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rules-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .rules-list li {
            font-size: 0.92rem;
            color: #475569;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
        }

        .rules-list li::before {
            content: '✓';
            color: #ea580c;
            font-weight: 800;
        }

        /* Switch Lab Navigation */
        .nav-switch-labs {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .switch-link {
            text-decoration: none;
            color: #ea580c;
            font-weight: 700;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: transform 0.2s ease;
        }

        .switch-link:hover {
            transform: translateX(4px);
        }

        @media (max-width: 992px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }
            .specs-grid {
                grid-template-columns: 1fr;
            }
            .detail-card-main {
                padding: 24px;
            }
            .lab-showcase-box {
                height: 340px;
            }
        }
    </style>
</head>
<body>

    <!-- Include Navbar Partial -->
    <?php $this->load->view('partials/navbar'); ?>

    <div class="page-container">
        
        <!-- Breadcrumb Navigation -->
        <div class="breadcrumb-bar">
            <a href="<?= site_url('dashboard') ?>">&larr; Kembali ke Beranda Dashboard</a>
            <span>/</span>
            <span>Detail Laboratorium &amp; Fasilitas</span>
            <span>/</span>
            <span style="color: #ffffff; font-weight: 600;"><?= $lab['title'] ?></span>
        </div>

        <!-- Main Detail Card -->
        <div class="detail-card-main">
            
            <!-- Hero Grid -->
            <div class="hero-grid">
                
                <!-- Left Column: 3D Interactive Model Showcase & Photo Slider -->
                <div>
                    <div class="lab-showcase-box" id="showcaseBox" style="background: <?= $lab['bg_gradient'] ?>; border-color: <?= $lab['border_color'] ?>;">
                        <style>
                            .lab-showcase-box::before {
                                background: radial-gradient(circle, <?= $lab['glow_color'] ?> 0%, rgba(251, 146, 60, 0.05) 50%, transparent 75%) !important;
                            }
                        </style>

                        <!-- Mode View 1: Foto Asli (Default Tampil Pertama) -->
                        <div class="showcase-content-view" id="viewPhoto" style="display: block;">
                            <img id="labRealPhotoImg" 
                                 src="<?= $lab['photo'] ?>" 
                                 alt="Foto Asli <?= $lab['title'] ?>" 
                                 onerror="this.onerror=null; this.src='<?= $lab['photo_fallback'] ?>';" />
                        </div>

                        <!-- Mode View 2: 3D Model (Tampil Kedua jika ada) -->
                        <?php if (!empty($lab['model'])): ?>
                        <div class="showcase-content-view" id="view3D" style="display: none;">
                            <model-viewer 
                                id="labDetailViewer"
                                src="<?= $lab['model'] ?>" 
                                alt="3D Visual <?= $lab['title'] ?>" 
                                bounds="tight"
                                camera-orbit="<?= $lab['orbit'] ?>"
                                camera-target="<?= isset($lab['target']) ? $lab['target'] : 'auto auto auto' ?>"
                                field-of-view="<?= $lab['fov'] ?>"
                                camera-controls 
                                touch-action="none"
                                shadow-intensity="1.5" 
                                shadow-softness="0.8"
                                exposure="1.2"
                                interaction-prompt="none"
                                style="background-color: transparent;">
                            </model-viewer>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Indicator Line Bar Murni (Line 1: Foto Asli, Line 2: Model 3D) -->
                    <?php if (!empty($lab['model'])): ?>
                    <div class="showcase-line-indicators">
                        <span class="detail-line active" id="detailLine0" onclick="switchShowcaseMode('photo')" title="Foto Dokumentasi Asli"></span>
                        <span class="detail-line" id="detailLine1" onclick="switchShowcaseMode('3d')" title="Model 3D Interaktif"></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Lab Info & Actions -->
                <div>
                    <div class="badge-tag">
                        <span>●</span> <?= $lab['badge'] ?>
                    </div>

                    <h1 class="lab-title-main"><?= $lab['title'] ?></h1>
                    <p class="lab-subtitle"><?= $lab['subtitle'] ?></p>

                    <!-- Meta Badges -->
                    <div class="meta-list">
                        <div class="meta-item">
                            <span class="icon">📍</span>
                            <span><?= $lab['location'] ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="icon">💺</span>
                            <span><?= $lab['capacity'] ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="icon">⏰</span>
                            <span><?= $lab['hours'] ?></span>
                        </div>
                    </div>

                    <p class="desc-paragraph"><?= $lab['desc'] ?></p>

                </div>

            </div>

            <!-- Specs Grid Block -->
            <div class="section-header-block">
                <h2>Fasilitas &amp; Spesifikasi Perangkat</h2>
            </div>

            <div class="specs-grid">
                <?php foreach ($lab['specs'] as $spec): ?>
                    <div class="spec-card">
                        <div class="spec-card-head">
                            <div class="spec-card-icon"><?= $spec['icon'] ?></div>
                            <h3><?= $spec['title'] ?></h3>
                        </div>
                        <p><?= $spec['desc'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Rules Block -->
            <div class="rules-block">
                <h3>⚠️ Tata Tertib &amp; Ketentuan Pengguna</h3>
                <ul class="rules-list">
                    <?php foreach ($lab['rules'] as $rule): ?>
                        <li><?= $rule ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Switch Labs Footer Links -->
            <?php
                $lab_order = ['multimedia', 'aula', 'cintiq', 'greenscreen', 'incubator', 'mac'];
                $lab_labels = [
                    'multimedia' => 'Lab Multimedia & Game',
                    'aula'       => 'Aula Utama Fakultas',
                    'cintiq'     => 'Lab Tablet Cintiq',
                    'greenscreen'=> 'Lab Green Screen Studio',
                    'incubator'  => 'Lab Inkubator Bisnis & Tech',
                    'mac'        => 'Lab Workstation Apple Mac',
                ];
                $count = count($lab_order);
                $current_pos = array_search($active_key, $lab_order);
                $prev_key = $lab_order[($current_pos - 1 + $count) % $count];
                $next_key = $lab_order[($current_pos + 1) % $count];
            ?>
            <div class="nav-switch-labs">
                <?php if ($prev_key): ?>
                    <a href="<?= site_url('dashboard/lab_detail/' . $prev_key) ?>" class="switch-link">&larr; <?= $lab_labels[$prev_key] ?></a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <?php if ($next_key): ?>
                    <a href="<?= site_url('dashboard/lab_detail/' . $next_key) ?>" class="switch-link"><?= $lab_labels[$next_key] ?> &rarr;</a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <!-- Include Footer Partial -->
    <?php $this->load->view('partials/footer'); ?>

    <script>
        let currentShowcaseMode = 'photo';

        function toggleShowcasePrevNext() {
            if (currentShowcaseMode === 'photo') {
                switchShowcaseMode('3d');
            } else {
                switchShowcaseMode('photo');
            }
        }

        function switchShowcaseMode(mode) {
            currentShowcaseMode = mode;
            const view3D = document.getElementById('view3D');
            const viewPhoto = document.getElementById('viewPhoto');
            const line0 = document.getElementById('detailLine0');
            const line1 = document.getElementById('detailLine1');

            if (mode === 'photo') {
                if (viewPhoto) viewPhoto.style.display = 'block';
                if (view3D) view3D.style.display = 'none';
                if (line0) { line0.classList.add('active'); line0.classList.remove('passed'); }
                if (line1) { line1.classList.remove('active'); line1.classList.remove('passed'); }
            } else {
                if (viewPhoto) viewPhoto.style.display = 'none';
                if (view3D) view3D.style.display = 'block';
                if (line0) { line0.classList.remove('active'); line0.classList.add('passed'); }
                if (line1) { line1.classList.add('active'); line1.classList.remove('passed'); }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;

            const defaultOrbit = viewer.getAttribute('camera-orbit') || '45deg 75deg 44%';
            const defaultFov = viewer.getAttribute('field-of-view') || '17deg';
            let resetTimer = null;

            // 1. Double Click / Double Tap -> Toggle Zoom In / Zoom Out
            let isZoomedIn = false;
            viewer.addEventListener('dblclick', (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                viewer.interpolationDecay = 120;
                let currentFov = parseFloat(viewer.fieldOfView || viewer.getFieldOfView()) || 17;

                if (!isZoomedIn && currentFov > 13) {
                    viewer.fieldOfView = '10deg'; // Zoom in murni di posisi tengah
                    isZoomedIn = true;
                } else {
                    viewer.fieldOfView = defaultFov; // Zoom out kembali normal
                    isZoomedIn = false;
                }
            }, true);

            // 2. Touch Gestures 2 Jari
            let touchStartX = 0;
            let touchStartY = 0;

            viewer.addEventListener('touchstart', (e) => {
                viewer.interpolationDecay = 80;
                if (e.touches.length === 2) {
                    touchStartX = (e.touches[0].clientX + e.touches[1].clientX) / 2;
                    touchStartY = (e.touches[0].clientY + e.touches[1].clientY) / 2;
                }
            }, { passive: true });

            viewer.addEventListener('touchmove', (e) => {
                if (e.touches.length === 2) {
                    let currentX = (e.touches[0].clientX + e.touches[1].clientX) / 2;
                    let currentY = (e.touches[0].clientY + e.touches[1].clientY) / 2;
                    let diffX = currentX - touchStartX;
                    let diffY = currentY - touchStartY;

                    if (Math.abs(diffX) > 5) {
                        let currentFov = parseFloat(viewer.fieldOfView || viewer.getFieldOfView()) || 17;
                        let deltaFov = diffX > 0 ? -1.0 : 1.0;
                        let newFov = Math.max(6, Math.min(32, currentFov + deltaFov));
                        viewer.interpolationDecay = 60;
                        viewer.fieldOfView = `${newFov}deg`;

                        const curTarget = viewer.getCameraTarget();
                        if (curTarget) {
                            let nextX = curTarget.x - diffX * 0.005;
                            let nextY = curTarget.y + diffY * 0.005;
                            viewer.cameraTarget = `${nextX}m ${nextY}m ${curTarget.z}m`;
                        }

                        touchStartX = currentX;
                        touchStartY = currentY;
                    }
                }
            }, { passive: true });

            // 3. Mouse / Pointer Dragging (Pan ke Kiri & Kanan via Shift + Klik / Klik Kanan)
            let isShiftPressed = false;
            let prevX = 0, prevY = 0;

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Shift') isShiftPressed = true;
            });
            window.addEventListener('keyup', (e) => {
                if (e.key === 'Shift') isShiftPressed = false;
            });

            viewer.addEventListener('pointerdown', (e) => {
                viewer.interpolationDecay = 80;
                prevX = e.clientX;
                prevY = e.clientY;
            });

            viewer.addEventListener('pointermove', (e) => {
                if ((e.buttons === 1 && (isShiftPressed || e.shiftKey)) || e.buttons === 2 || e.buttons === 4) {
                    let dx = e.clientX - prevX;
                    let dy = e.clientY - prevY;
                    prevX = e.clientX;
                    prevY = e.clientY;

                    const curTarget = viewer.getCameraTarget();
                    if (curTarget) {
                        let factor = 0.008;
                        let nextX = curTarget.x - dx * factor;
                        let nextY = curTarget.y + dy * factor;
                        viewer.cameraTarget = `${nextX}m ${nextY}m ${curTarget.z}m`;
                    }
                }
            });

            viewer.addEventListener('contextmenu', (e) => {
                e.preventDefault();
            });

            viewer.addEventListener('camera-change', (e) => {
                if (e.detail && e.detail.source === 'user-interaction') {
                    viewer.interpolationDecay = 80;
                    clearTimeout(resetTimer);
                    resetTimer = setTimeout(() => {
                        viewer.interpolationDecay = 400;
                        viewer.cameraOrbit = defaultOrbit;
                        viewer.cameraTarget = 'auto auto auto';
                    }, 5000);
                }
            });
        });

        function rotate3dModel(deltaDeg) {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;
            viewer.interpolationDecay = 120;
            const orbit = viewer.getCameraOrbit();
            if (orbit) {
                let thetaDeg = (orbit.theta * 180) / Math.PI;
                let phiDeg = (orbit.phi * 180) / Math.PI;
                let newTheta = thetaDeg + deltaDeg;
                viewer.cameraOrbit = `${newTheta}deg ${phiDeg}deg ${orbit.radius}m`;
            }
        }

        function zoom3dModel(deltaFov) {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;
            viewer.interpolationDecay = 120;
            let currentFov = parseFloat(viewer.fieldOfView || viewer.getFieldOfView()) || 17;
            let newFov = Math.max(6, Math.min(32, currentFov + deltaFov));
            viewer.fieldOfView = `${newFov}deg`;
        }

        function reset3dModel() {
            const viewer = document.getElementById('labDetailViewer');
            if (!viewer) return;
            viewer.interpolationDecay = 200;
            const defaultOrbit = viewer.getAttribute('camera-orbit') || '45deg 75deg 44%';
            const defaultFov = viewer.getAttribute('field-of-view') || '17deg';
            viewer.cameraOrbit = defaultOrbit;
            viewer.cameraTarget = 'auto auto auto';
            viewer.fieldOfView = defaultFov;
        }
    </script>
</body>
</html>
