<!-- Sidebar Layout -->
<aside id="app-sidebar" class="w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col shadow-xl z-50 transition-transform duration-300 absolute md:relative inset-y-0 left-0 -translate-x-full md:translate-x-0 h-full">
    <!-- Logo Section -->
    <div class="h-16 flex items-center px-6 bg-slate-950 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-inner">P</div>
            <span class="font-bold text-xl tracking-wide">PharmaCare</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">ระบบหลัก</p>
        
        <?php 
        $current_page = basename($_SERVER['PHP_SELF']); 
        ?>
        
        <!-- Menu 1: ค้นหาและบันทึก -->
        <a href="inquiry_form.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?php echo $current_page == 'inquiry_form.php' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fa-solid fa-magnifying-glass text-lg <?php echo $current_page == 'inquiry_form.php' ? 'text-white' : 'text-slate-400 group-hover:text-indigo-400'; ?>"></i>
            <span class="font-medium">ค้นหาและบันทึกข้อมูล</span>
        </a>

        <!-- Menu 2: แดชบอร์ด -->
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group <?php echo $current_page == 'dashboard.php' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white'; ?>">
            <i class="fa-solid fa-chart-pie text-lg <?php echo $current_page == 'dashboard.php' ? 'text-white' : 'text-slate-400 group-hover:text-indigo-400'; ?>"></i>
            <span class="font-medium">แดชบอร์ดสรุปผล</span>
        </a>

    </nav>

    <!-- Bottom Sidebar Info -->
    <div class="p-4 bg-slate-950 border-t border-slate-800">
        <div class="flex items-center gap-3 text-sm text-slate-400 mb-3">
            <i class="fa-solid fa-shield-halved text-indigo-500"></i>
            <span>HA / PDPA Standard</span>
        </div>
        
        <!-- Connection Status -->
        <div class="pt-3 border-t border-slate-800 space-y-2">
            <p class="text-[10px] uppercase text-slate-500 font-semibold tracking-wider">Connection Status</p>
            <div class="flex items-center gap-2 text-xs">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-slate-300 font-mono">HIS: <?php echo htmlspecialchars($his_host ?? 'Unknown'); ?></span>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="relative flex h-2 w-2">
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-slate-300 font-mono">APP: <?php echo htmlspecialchars($app_host ?? 'Unknown'); ?></span>
            </div>
        </div>
    </div>
</aside>
