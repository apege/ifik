<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Tentang FIK') ?> — IFIK Portal</title>
    <meta name="description" content="Informasi lengkap tentang Fakultas Industri Kreatif (FIK) dan IFIK Portal — layanan digital untuk civitas akademika.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #fbf7f1;
            --text: #1e293b;
            --accent: #ea580c;
            --accent-light: rgba(234,88,12,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* ===== NAV ===== */
        nav {
            position: fixed;
            top: 0; left: 0; width: 100%;
            padding: 18px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            background: rgba(251, 247, 241, 0.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(234,88,12,0.12);
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            transition: color 0.3s;
        }
        .back-btn svg { width: 20px; height: 20px; transition: transform 0.3s; }
        .back-btn:hover { color: var(--accent); }
        .back-btn:hover svg { transform: translateX(-5px); }
        .nav-logo {
            font-weight: 900;
            font-size: 1.1rem;
            color: var(--accent);
            letter-spacing: 1px;
            text-decoration: none;
        }

        /* ===== HERO ===== */
        .hero {
            position: relative;
            width: 100vw;
            height: 60vh;
            background-image: url('<?= base_url("assets/images/Fakultas.jpg") ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: flex-end;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, var(--bg) 0%, rgba(251,247,241,0.2) 50%, rgba(0,0,0,0.45) 100%);
        }
        .hero-badge {
            position: relative;
            z-index: 2;
            padding: 0 60px 50px;
        }
        .hero-badge span {
            display: inline-block;
            background: var(--accent);
            color: #fff;
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 8px 18px;
            border-radius: 50px;
            margin-bottom: 14px;
        }
        .hero-badge h1 {
            font-size: clamp(2rem, 5vw, 4rem);
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            text-shadow: 0 4px 20px rgba(0,0,0,0.5);
            max-width: 700px;
        }

        /* ===== ARTICLE ===== */
        .article-wrap {
            max-width: 860px;
            margin: 0 auto 120px;
            padding: 60px 40px 0;
        }
        .article-content {
            font-size: 1.12rem;
            line-height: 1.95;
            color: #334155;
            text-align: justify;
        }

        /* Render konten TinyMCE atau plain text */
        .article-content h1, .article-content h2, .article-content h3 {
            color: #0f172a; font-weight: 800;
            margin: 40px 0 14px; line-height: 1.3;
        }
        .article-content h1 { font-size: 2rem; }
        .article-content h2 { font-size: 1.5rem; }
        .article-content h3 { font-size: 1.2rem; }
        .article-content p { margin-bottom: 26px; }
        .article-content p:first-of-type::first-letter {
            font-size: 4rem;
            float: left;
            line-height: 0.8;
            margin-right: 12px;
            color: var(--accent);
            font-weight: 900;
        }
        .article-content ul, .article-content ol {
            margin: 0 0 26px 26px;
        }
        .article-content li { margin-bottom: 8px; }
        .article-content blockquote {
            border-left: 5px solid var(--accent);
            margin: 40px 0;
            padding: 20px 28px;
            background: var(--accent-light);
            border-radius: 0 16px 16px 0;
            font-style: italic;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .article-content img {
            width: 100%;
            border-radius: 20px;
            margin: 36px 0;
            box-shadow: 0 16px 40px rgba(0,0,0,0.1);
        }
        .article-content a { color: var(--accent); font-weight: 600; }

        /* ===== FOOTER ===== */
        footer {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            border-top: 1px solid rgba(0,0,0,0.05);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* ===== ANIMATION ===== */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-up { animation: fadeUp 0.9s cubic-bezier(0.25,1,0.5,1) forwards; }

        @media (max-width: 768px) {
            nav { padding: 14px 20px; }
            .hero-badge { padding: 0 20px 40px; }
            .article-wrap { padding: 40px 20px 0; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="<?= base_url('dashboard') ?>" class="back-btn">
            <svg fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Dashboard
        </a>
        <a href="<?= base_url() ?>" class="nav-logo">IFIK</a>
    </nav>

    <!-- HERO -->
    <div class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-badge">
            <span>Tentang Kami</span>
            <h1><?= htmlspecialchars($header_settings->title ?? 'Fakultas Industri Kreatif') ?></h1>
        </div>
    </div>

    <!-- KONTEN LENGKAP -->
    <div class="article-wrap animate-up">
        <div class="article-content">
            <?php
                $desc = $header_settings->description ?? '';
                // Deteksi apakah konten adalah plain text atau HTML (dari TinyMCE)
                if (!empty($desc) && strip_tags($desc) === $desc) {
                    // Plain text — bagi per baris kosong menjadi paragraf
                    $paragraphs = preg_split('/\n\s*\n/', trim($desc));
                    foreach ($paragraphs as $p) {
                        if (trim($p) !== '') {
                            echo '<p>' . nl2br(htmlspecialchars(trim($p))) . '</p>';
                        }
                    }
                } else {
                    // HTML dari TinyMCE — render langsung
                    echo $desc;
                }
            ?>
        </div>
    </div>

    <footer>
        &copy; <?= date('Y') ?> IFIK Portal — Fakultas Industri Kreatif. Seluruh hak cipta dilindungi.
    </footer>

</body>
</html>
