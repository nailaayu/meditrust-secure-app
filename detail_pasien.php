<?php
include 'config/database.php';
session_start();

// LOGIKA KEAMANAN (TIDAK BERUBAH): Memastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$patient = null;

if ($id > 0) {
    // LOGIKA KEAMANAN (TIDAK BERUBAH): Prepared statement untuk mengunci detail data pasien
    $stmt = $conn->prepare("SELECT id, name, nik, diagnosis FROM patients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $patient = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pasien - MediTrust</title>
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
                    <a href="search.php" 
                       class="inline-flex items-center gap-1.5 text-xs text-blue-200 hover:text-white bg-white/10 hover:bg-white/20 py-1.5 px-4 rounded-xl transition-all cursor-pointer font-medium">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i>
                        <span>Kembali ke Pencarian</span>
                    </a>
                    
                    <div class="flex items-center gap-1.5 text-xs text-blue-300 font-medium">
                        <i class="fa-solid fa-house-medical text-[11px]"></i>
                        <span class="font-bold text-white/90">Medi<span class="text-blue-300">Trust</span></span>
                    </div>
                </div>
                
                <h1 class="text-2xl font-bold tracking-tight relative z-10">Detail Rekam Medis Pasien</h1>
            </div>

            <div class="p-6 md:p-10 space-y-6 bg-white">
                
                <?php if ($patient): ?>
                    <div class="bg-blue-50/30 border border-blue-200 rounded-xl overflow-hidden shadow-sm">
                        
                        <div class="grid grid-cols-1 md:grid-cols-12 border-b border-blue-100/70 p-4 items-center gap-2">
                            <div class="md:col-span-4 text-[11px] font-bold uppercase tracking-wider text-blue-900/80 flex items-center gap-2">
                                <i class="fa-solid fa-id-card text-blue-500 text-xs w-4"></i>
                                <span>Nama Pasien</span>
                            </div>
                            <div class="md:col-span-8 text-xs font-semibold text-slate-900 bg-white md:bg-transparent p-2 md:p-0 rounded-lg border border-slate-100 md:border-transparent">
                                <?php echo htmlspecialchars($patient['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 p-4 items-center gap-2">
                            <div class="md:col-span-4 text-[11px] font-bold uppercase tracking-wider text-blue-900/80 flex items-center gap-2">
                                <i class="fa-solid fa-stethoscope text-blue-500 text-xs w-4"></i>
                                <span>Diagnosis Penyakit</span>
                            </div>
                            <div class="md:col-span-8 text-xs font-medium text-slate-800 bg-white md:bg-transparent p-2 md:p-0 rounded-lg border border-slate-100 md:border-transparent leading-relaxed">
                                <?php echo htmlspecialchars($patient['diagnosis'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3 text-xs text-amber-800">
                        <i class="fa-solid fa-circle-exclamation text-base text-amber-500"></i>
                        <span>Data pasien tidak ditemukan atau ID salah.</span>
                    </div>
                <?php endif; ?>

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