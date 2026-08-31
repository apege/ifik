<?php
$bulan_map = array('01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember');
$tgl = isset($berita->tanggal) ? $berita->tanggal : '';
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) {
    $parts = explode('-', $tgl);
    $tgl_formatted = (int)$parts[2] . ' ' . ($bulan_map[$parts[1]] ?? '') . ' ' . $parts[0];
} else {
    $tgl_formatted = !empty($tgl) ? $tgl : date('d Januari Y');
}

$kategori = !empty($berita->kategori) ? $berita->kategori : 'Berita Acara';
$judul = !empty($berita->judul) ? $berita->judul : 'Detail Berita';
$gambar_url = !empty($berita->gambar) ? base_url($berita->gambar) : base_url('assets/images/background.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul) ?> - Fakultas Industri Kreatif</title>
    <!-- Font Inter untuk kesan modern & premium -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #fbf7f1;
            --text-color: #1e293b;
            --accent-color: #ea580c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Navigasi / Header Transparan & Blur */
        nav {
            position: fixed;
            top: 0; left: 0; width: 100%;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            background: rgba(251, 247, 241, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            transition: color 0.3s ease;
        }
        
        .back-btn:hover { 
            color: var(--accent-color); 
        }
        
        .back-btn svg { 
            width: 22px; 
            height: 22px; 
            transition: transform 0.3s ease; 
        }
        
        .back-btn:hover svg { 
            transform: translateX(-5px); 
        }

        /* Hero Image Besar & Parallax */
        .article-hero {
            position: relative;
            width: 100vw;
            height: 65vh;
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Parallax effect */
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        /* Gradasi agar teks menyatu dengan hero */
        .hero-overlay {
            position: absolute;
            bottom: 0; left: 0; width: 100%; height: 60%;
            background: linear-gradient(to top, rgba(251,247,241,1) 0%, rgba(251,247,241,0.5) 50%, transparent 100%);
        }

        /* Kontainer Utama Artikel */
        .article-container {
            max-width: 850px;
            margin: -100px auto 100px auto;
            position: relative;
            z-index: 10;
            padding: 0 40px;
        }

        .article-meta {
            color: var(--accent-color);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.95rem;
            margin-bottom: 20px;
            display: inline-block;
            background: rgba(234, 88, 12, 0.1);
            padding: 8px 16px;
            border-radius: 50px;
        }

        .article-title {
            font-size: 3.8rem;
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 40px;
            color: #0f172a;
            text-shadow: 0 5px 25px rgba(255,255,255,0.9);
        }

        .article-content {
            font-size: 1.18rem;
            line-height: 1.9;
            color: #334155;
            text-align: justify;
        }

        .article-content p { 
            margin-bottom: 30px; 
        }

        .article-content p:first-of-type::first-letter {
            font-size: 4rem;
            float: left;
            line-height: 0.8;
            margin-right: 12px;
            color: var(--accent-color);
            font-weight: 900;
        }

        .article-content blockquote {
            border-left: 6px solid var(--accent-color);
            padding-left: 30px;
            font-style: italic;
            font-size: 1.35rem;
            margin: 50px 0;
            color: #1e293b;
            background: linear-gradient(90deg, rgba(234, 88, 12, 0.08), transparent);
            padding: 30px;
            border-radius: 0 20px 20px 0;
            font-weight: 600;
        }
        
        .article-content img {
            width: 100%;
            border-radius: 24px;
            margin: 40px 0;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transition: transform 0.5s ease;
        }

        .article-content img:hover {
            transform: scale(1.02);
        }
        
        /* Footer Artikel */
        footer {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            border-top: 1px solid rgba(0,0,0,0.05);
            font-weight: 500;
        }
        
        /* Animasi masuk sederhana (Fade In Up) */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-up {
            animation: fadeInUp 1s cubic-bezier(0.25, 1, 0.5, 1) forwards;
        }
    </style>
</head>
<body>

    <nav>
        <!-- Mengarah kembali ke halaman dashboard sebelumnya -->
        <a href="<?= base_url('index.php/dashboard') ?>" class="back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Dashboard
        </a>
    </nav>

    <!-- Gambar Besar Parallax -->
    <div class="article-hero" style="background-image: url('<?= $gambar_url ?>');">
        <div class="hero-overlay"></div>
    </div>

    <!-- Konten Berita -->
    <div class="article-container animate-up">
        <span class="article-meta"><?= htmlspecialchars($kategori) ?> &bull; <?= htmlspecialchars($tgl_formatted) ?></span>
        <h1 class="article-title"><?= htmlspecialchars($judul) ?></h1>
        
        <div class="article-content">
            <?php if (!empty($berita->konten)): ?>
                <?= $berita->konten ?>
            <?php else: ?>
                <p><?= nl2br(htmlspecialchars($berita->excerpt ?? '')) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        &copy; <?= date('Y') ?> IFIK Portal. Seluruh hak cipta dilindungi.
    </footer>

</body>
</html>
