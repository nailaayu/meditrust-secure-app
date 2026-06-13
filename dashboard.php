<?php
session_start();

// LOGIKA KEAMANAN (TIDAK BERUBAH): Memastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MediTrust</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden border border-slate-100 relative min-h-[480px] flex flex-col justify-between">
        
        <div>
            <div class="bg-gradient-to-br from-blue-700 to-indigo-950 p-6 md:p-10 text-white relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                
                <div class="relative z-10 flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2">
                        <span class="bg-white/20 p-1.5 rounded-lg backdrop-blur-sm">
                            <i class="fa-solid fa-house-medical text-sm text-blue-300"></i>
                        </span>
                        <span class="text-lg font-bold tracking-tight">Medi<span class="text-blue-300">Trust</span></span>
                    </div>
                    
                    <a href="logout.php" 
                       class="bg-rose-500/20 hover:bg-rose-600 text-rose-200 hover:text-white border border-rose-500/30 hover:border-rose-600 text-xs font-semibold py-1.5 px-4 rounded-xl transition-all duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm">
                        <i class="fa-solid fa-right-from-bracket text-[10px]"></i>
                        <span>Logout</span>
                    </a>
                </div>
                
                <h1 class="text-2xl font-bold tracking-tight relative z-10">Selamat Datang di MediTrust</h1>
            </div>

            <div class="p-6 md:p-10 space-y-6 bg-white">
                
                <div class="bg-blue-50/50 border border-blue-200/80 rounded-xl p-4 flex items-center gap-3 shadow-inner shadow-blue-100/30">
                    <div class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs shrink-0 shadow-sm shadow-blue-600/20">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="text-xs text-slate-800 font-medium tracking-wide">
                        Halo, <span class="font-bold text-blue-900"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></span>!
                    </div>
                </div>

                <div class="pt-2">
                    <a href="search.php" 
                       class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl text-xs transition-all duration-200 shadow-md shadow-blue-600/10 hover:shadow-lg hover:shadow-blue-600/20 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer text-center">
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                        <span>Cari & Lihat Data Pasien</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-10 pt-0 text-center text-[10px] text-slate-400">
            <div class="border-t border-slate-100 pt-4">
                &copy; 2026 MediTrust System - Sistem Informasi Bisnis Polinema
            </div>
        </div>

    </div>

</body>
</html>