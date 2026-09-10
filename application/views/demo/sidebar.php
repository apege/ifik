<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curved Animated Sidebar Demo - IFIK</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-amber-500 selection:text-white">

    <!-- Include Curved Sidebar Component -->
    <?php $this->load->view('components/curved_sidebar'); ?>

    <!-- Demo Hero Section -->
    <main class="flex-1 flex flex-col items-center justify-center text-center px-6 py-20 relative">
        <!-- Ambient Glowing Blur Background -->
        <div class="absolute w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none -top-20 -left-20"></div>
        <div class="absolute w-96 h-96 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none -bottom-20 -right-20"></div>

        <div class="relative z-10 max-w-3xl space-y-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/80 border border-slate-800 text-amber-400 text-xs font-bold tracking-wide">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-400"></i>
                Converted from React + Framer Motion (sidebar.tsx)
            </div>

            <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white leading-tight">
                Curved Animated <span class="bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500 bg-clip-text text-transparent">Sidebar Menu</span>
            </h1>

            <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                Sidebar animasi lengkung SVG dinamis dengan kurva Bezier elastis, efek kinetic wave pada huruf, tombol burger morphing 3D, dan transisi cubic-bezier (0.76, 0, 0.24, 1).
            </p>

            <div class="flex items-center justify-center gap-4 pt-4">
                <button type="button" onclick="if(window.curvedSidebarInstance) window.curvedSidebarInstance.toggle()" class="px-7 py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-sm rounded-2xl shadow-lg shadow-amber-500/25 transition-all transform active:scale-95 cursor-pointer flex items-center gap-2.5">
                    <i class="fa-solid fa-bars-staggered"></i> Buka Sidebar Sekarang
                </button>
            </div>
        </div>
    </main>

    <!-- Footer Note -->
    <footer class="p-6 text-center text-xs text-slate-500 border-t border-slate-900">
        &copy; <?= date('Y'); ?> Fakultas Industri Kreatif (IFIK) - Sistem Informasi Tugas Akhir
    </footer>

</body>
</html>
