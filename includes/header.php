<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCare Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        body { font-family: 'Prompt', sans-serif; background-color: #f8fafc; } 
        /* Custom scrollbar for better UI */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-slate-800">

<?php 
// ตรวจสอบว่าหน้าไหนเรียกใช้ header นี้ ถ้ามีตัวแปร $hide_sidebar = true จะไม่แสดง Sidebar
$hide_sidebar = isset($hide_sidebar) ? $hide_sidebar : false; 
?>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden transition-opacity opacity-0" onclick="toggleSidebar()"></div>

<?php if (!$hide_sidebar): ?>
    <!-- แทรก Sidebar -->
    <?php require_once 'sidebar.php'; ?>
<?php endif; ?>

<!-- Main Content Wrapper -->
<div class="flex-1 flex flex-col overflow-hidden <?php echo $hide_sidebar ? 'w-full' : ''; ?>">
    
    <!-- Top Header Navigation -->
    <header class="bg-white border-b border-slate-200 shadow-sm z-30 h-16 flex-shrink-0">
        <div class="h-full px-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <?php if (!$hide_sidebar): ?>
                    <!-- Hamburger Menu for Mobile -->
                    <button type="button" onclick="toggleSidebar()" class="md:hidden text-slate-500 hover:text-slate-700 focus:outline-none p-2 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <!-- Mobile Logo -->
                    <div class="md:hidden font-bold text-lg text-slate-800 flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-inner">P</div>
                        PharmaCare
                    </div>
                <?php endif; ?>
                
                <?php if ($hide_sidebar): ?>
                    <!-- ถ้าซ่อน Sidebar ให้แสดง Logo เล็กๆ ที่ Header แทน -->
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-inner">P</div>
                    <span class="font-bold text-xl text-slate-800 hidden sm:inline">PharmaCare</span>
                <?php endif; ?>
            </div>
            
            <!-- User Menu -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold leading-tight"><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'ผู้ใช้งาน'); ?></span>
                        <span class="text-xs text-slate-500 leading-tight"><?php echo htmlspecialchars($_SESSION['role'] ?? 'Pharmacist'); ?></span>
                    </div>
                </div>
                <a href="api/auth.php?action=logout" class="text-sm font-medium text-red-600 hover:text-red-700 px-3 py-2 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">ออกจากระบบ</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Scrollable Content Area -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 sm:p-6">
        <div class="max-w-7xl mx-auto space-y-6">

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('app-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    </script>
