<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - MediTrust</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Font Awesome CDN untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <!-- FIX: Kotak luar max-w-3xl dengan full gradasi biru-indigo mewah -->
    <div class="bg-gradient-to-br from-blue-700 to-indigo-950 w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden relative min-h-[450px] flex flex-col justify-between p-8 md:p-12 text-white border border-indigo-900">
        
        <!-- Ornamen dekoratif melingkar samar di latar belakang -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <!-- Bagian Logo Atas -->
        <div class="relative z-10 flex items-center justify-center gap-2">
            <span class="bg-white/10 p-1.5 rounded-lg backdrop-blur-md border border-white/10">
                <i class="fa-solid fa-house-medical text-xs text-blue-300"></i>
            </span>
            <span class="text-base font-bold tracking-tight">Medi<span class="text-blue-300">Trust</span></span>
        </div>

        <!-- Bagian Tengah: Teks Sambutan Terpusat (Center) -->
        <div class="relative z-10 text-center space-y-6 my-auto py-6">
            <div class="space-y-2">
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Selamat Datang di MediTrust</h1>
                <p class="text-xs text-blue-200/90 max-w-md mx-auto font-light leading-relaxed">
                    Sistem Manajemen Rekam Medis.
                </p>
            </div>

            <!-- Bagian Tombol Putih dengan Garis Tepi Biru Kontras (Dinamis PHP) -->
            <div class="w-full max-w-xs mx-auto pt-2">
                <?php if(!isset($_SESSION['user'])): ?>
                    <!-- KONDISI 1: Belum Login -> Tombol Masuk Sebagai Dokter -->
                    <a href="login.php" 
                       class="w-full bg-white hover:bg-blue-50 text-blue-700 font-bold py-3 px-4 rounded-xl text-xs transition-all duration-150 shadow-md border border-blue-200 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer text-center">
                        <i class="fa-solid fa-user-md text-[11px] text-blue-600"></i>
                        <span>Masuk sebagai Dokter</span>
                    </a>
                <?php else: ?>
                    <!-- KONDISI 2: Sudah Login -> Tombol Kembali ke Dashboard -->
                    <a href="dashboard.php" 
                       class="w-full bg-white hover:bg-blue-50 text-blue-700 font-bold py-3 px-4 rounded-xl text-xs transition-all duration-150 shadow-md border border-blue-200 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer text-center">
                        <i class="fa-solid fa-chart-pie text-[11px] text-blue-600"></i>
                        <span>Kembali ke Dashboard</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Hak Cipta di Bawah Box -->
        <div class="relative z-10 text-center text-[10px] text-blue-300/60 pt-4 border-t border-white/5">
            &copy; 2026 MediTrust System - Sistem Informasi Bisnis Polinema
        </div>

    </div>

</body>
</html>