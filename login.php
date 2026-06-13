<?php
include 'config/database.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // LOGIKA KEAMANAN (TIDAK BERUBAH): Mencegah SQLi Bypass
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // LOGIKA KEAMANAN (TIDAK BERUBAH): Mengamankan Broken Authentication
        if (password_verify($password, $user['password']) || md5($password) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password yang Anda masukkan salah.";
        }
    } else {
        $error = "Username tidak terdaftar.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MediTrust</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden grid md:grid-cols-12 min-h-[480px]">
        
        <div class="hidden md:flex md:col-span-5 bg-gradient-to-br from-blue-700 to-indigo-950 p-6 flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-30 h-30 bg-white/10 rounded-full blur-xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-1">
                    <span class="bg-white/20 p-1.5 rounded-lg backdrop-blur-sm">
                        <i class="fa-solid fa-house-medical text-base text-blue-300"></i>
                    </span>
                    <span class="text-xl font-bold tracking-tight">Medi<span class="text-blue-300">Trust</span></span>
                </div>
            </div>

            <div class="relative z-10 my-auto py-4">
                <h1 class="text-xl font-bold leading-tight mb-2">Catatan Medis Digital</h1>
                <p class="text-blue-100 text-xs leading-relaxed font-light">Aplikasi manajemen riwayat kesehatan pasien.</p>
            </div>

            <div class="relative z-10 text-[10px] text-blue-300 font-light border-t border-white/10 pt-2">
                <i class="fa-solid fa-shield-halved mr-1"></i> Layanan Medis Digital
            </div>
        </div>

        <div class="col-span-12 md:col-span-7 p-6 md:p-10 flex flex-col justify-center bg-white">
            <div class="mb-6">
                <div class="flex md:hidden items-center gap-2 mb-2">
                    <i class="fa-solid fa-house-medical text-xl text-blue-600"></i>
                    <span class="text-lg font-bold text-slate-800">MediTrust</span>
                </div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Selamat Datang</h2>
            </div>

            <?php if (!empty($error)): ?>
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-3 flex items-start gap-2 mb-4 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 text-sm"></i>
                    <div>
                        <h4 class="text-xs font-semibold text-rose-800">Gagal Otentikasi</h4>
                        <p class="text-[11px] text-rose-600 mt-0.5"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 mb-1">ID Pengguna (Username)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <input type="text" name="username" required autocomplete="off"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                               placeholder="Masukkan username">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold uppercase tracking-wider text-slate-600 mb-1">Kata Sandi (Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-3 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                               placeholder="••••••••••••">
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-xl text-xs transition-all duration-200 shadow-md shadow-blue-600/10 hover:shadow-lg hover:shadow-blue-600/20 active:scale-[0.99] flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Login</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-[10px] text-slate-400 border-t border-slate-100 pt-4">
                &copy; 2026 MediTrust System - Sistem Informasi Bisnis Polinema
            </div>
        </div>

    </div>

</body>
</html>