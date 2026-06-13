<?php
include 'config/database.php';
session_start();

// LOGIKA KEAMANAN (TIDAK BERUBAH): Memastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$patients = [];

if ($search_query !== '') {
    // LOGIKA KEAMANAN (TIDAK BERUBAH): Prepared Statement anti SQLi
    $search_param = "%" . $search_query . "%";
    
    // MENGGUNAKAN: tabel 'patients' dan kolom 'name' sesuai database asli-mu
    $stmt = $conn->prepare("SELECT id, name, nik, diagnosis FROM patients WHERE name LIKE ? OR diagnosis LIKE ?");
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
} else {
    // MENGGUNAKAN: tabel 'patients' dan kolom 'name' agar data muncul
    $result = mysqli_query($conn, "SELECT id, name, nik, diagnosis FROM patients");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $patients[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Pasien - MediTrust</title>
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
                
                <div class="relative z-10 flex items-center justify-between mb-4">
                    <a href="dashboard.php" 
                       class="inline-flex items-center gap-1.5 text-xs text-blue-200 hover:text-white bg-white/10 hover:bg-white/20 py-1.5 px-4 rounded-xl transition-all cursor-pointer font-medium">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Kembali ke Dashboard</span>
                    </a>
                    
                    <div class="flex items-center gap-1.5 text-xs text-blue-300 font-medium">
                        <i class="fa-solid fa-house-medical text-[11px]"></i>
                        <span class="font-bold text-white/90">Medi<span class="text-blue-300">Trust</span></span>
                    </div>
                </div>
                
                <h1 class="text-2xl font-bold tracking-tight relative z-10">Pencarian Rekam Medis Pasien</h1>
            </div>

            <div class="p-6 md:p-10 space-y-6 bg-white">
                
                <form action="" method="GET" class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <input type="text" name="query" autocomplete="off"
                               value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-9 pr-3 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-100 transition-all duration-200"
                               placeholder="Cari nama pasien atau diagnosis...">
                    </div>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 rounded-xl text-xs transition-all duration-150 shadow-sm flex items-center gap-1.5 cursor-pointer shrink-0 active:scale-[0.98]">
                        <span>Cari</span>
                    </button>
                </form>

                <div class="overflow-x-auto border border-blue-200/80 rounded-xl shadow-inner shadow-blue-50/50">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-blue-50/60 border-b border-blue-200 text-blue-950 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-4">Nama Pasien</th>
                                <th class="py-3.5 px-4">NIK</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-blue-100 text-slate-700">
                            <?php if (count($patients) > 0): ?>
                                <?php foreach ($patients as $p): ?>
                                    <tr class="hover:bg-blue-50/30 transition-colors">
                                        <td class="py-3.5 px-4 font-medium text-slate-900">
                                            <?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="py-3.5 px-4 font-mono text-slate-500 tracking-tight">
                                            <code><?php echo htmlspecialchars(substr($p['nik'], 0, 15) . '...', ENT_QUOTES, 'UTF-8'); ?></code>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <a href="detail_pasien.php?id=<?php echo urlencode($p['id']); ?>" 
                                               class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white py-1 px-3.5 rounded-lg text-[11px] font-semibold transition-all duration-150 cursor-pointer border border-blue-100 hover:border-blue-600 shadow-sm">
                                                <span>Lihat Detail</span>
                                                <i class="fa-solid fa-chevron-right text-[9px] opacity-70"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="py-8 px-4 text-center text-slate-400 font-medium">
                                        <i class="fa-solid fa-folder-open text-lg block mb-2 opacity-60"></i>
                                        Data pasien tidak ditemukan.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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