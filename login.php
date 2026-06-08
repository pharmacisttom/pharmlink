<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: inquiry_form.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Pharmalink Inquiry System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Prompt', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-slate-50">

<div class="min-h-screen flex flex-col lg:flex-row">
    
    <!-- ฝั่งซ้าย: ข้อมูลระบบและขั้นตอนการทำงาน -->
    <div class="hidden lg:flex lg:w-1/2 bg-slate-900 text-white flex-col justify-center px-12 xl:px-24 relative overflow-hidden">
        <!-- พื้นหลังตกแต่ง -->
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-60"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-60"></div>
        
        <div class="relative z-10 max-w-lg">
            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full glass-panel mb-8">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-sm font-medium tracking-wide text-indigo-100">ระบบเชื่อมต่อ HIS Online</span>
            </div>
            
            <h1 class="text-4xl xl:text-5xl font-bold mb-6 leading-tight">ยินดีต้อนรับสู่ <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-blue-400">Pharmalink</span></h1>
            <p class="text-slate-300 text-lg mb-12 leading-relaxed">ระบบค้นหาและบันทึกการขอข้อมูลประวัติการรักษาผู้ป่วยผ่านสายโทรศัพท์ ออกแบบมาเพื่อเพิ่มความรวดเร็วและรองรับมาตรฐาน PDPA/HA</p>
            
            <h3 class="text-xl font-semibold mb-6 text-indigo-200">ขั้นตอนการทำงาน</h3>
            
            <div class="space-y-6">
                <!-- Step 1 -->
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 flex-shrink-0 flex items-center justify-center text-indigo-300 font-bold text-xl border border-indigo-500/30">1</div>
                    <div>
                        <h4 class="text-lg font-semibold text-white">รับสายและตรวจสอบสิทธิ์</h4>
                        <p class="text-slate-400 mt-1 text-sm leading-relaxed">บันทึกข้อมูลเบื้องต้นของผู้โทร วัตถุประสงค์การขอข้อมูล และกดยืนยันการปฏิบัติตาม พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล (PDPA)</p>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex-shrink-0 flex items-center justify-center text-blue-300 font-bold text-xl border border-blue-500/30">2</div>
                    <div>
                        <h4 class="text-lg font-semibold text-white">สืบค้นประวัติจาก HIS</h4>
                        <p class="text-slate-400 mt-1 text-sm leading-relaxed">ค้นหาผู้ป่วยด้วย HN, ชื่อ หรือเลขบัตรประชาชน ระบบจะดึงประวัติการใช้ยา ผลแล็บ แพ้ยา และการรักษาแบบ Real-time</p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="flex gap-5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex-shrink-0 flex items-center justify-center text-emerald-300 font-bold text-xl border border-emerald-500/30">3</div>
                    <div>
                        <h4 class="text-lg font-semibold text-white">บันทึกเวลาและวิเคราะห์ (Dashboard)</h4>
                        <p class="text-slate-400 mt-1 text-sm leading-relaxed">ระบบจะจับเวลาการให้บริการอัตโนมัติ (AHT) และสรุปข้อมูลสถิติการใช้งานทั้งหมดผ่านหน้าแดชบอร์ด</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ฝั่งขวา: ฟอร์ม Login -->
    <div class="w-full lg:w-1/2 min-h-screen flex items-center justify-center bg-slate-50 p-6 sm:p-12 relative">
        <!-- โลโก้มือถือ -->
        <div class="absolute top-8 left-8 lg:hidden flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">P</div>
            <span class="font-bold text-2xl text-slate-800">Pharmalink</span>
        </div>

        <div class="max-w-md w-full bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-12 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute -top-16 -right-16 w-32 h-32 bg-indigo-50 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-blue-50 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="hidden lg:flex w-16 h-16 bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl items-center justify-center text-white font-bold text-3xl shadow-lg shadow-indigo-600/30 mb-8">
                    P
                </div>
                <h2 class="text-3xl font-bold text-slate-800 mb-2">เข้าสู่ระบบ</h2>
                <p class="text-slate-500 mb-8">กรุณากรอกข้อมูลเพื่อเข้าใช้งานระบบ</p>
                
                <form id="loginForm" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">ชื่อผู้ใช้งาน (Username)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-user text-slate-400"></i>
                            </div>
                            <input type="text" id="username" required class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" placeholder="กรอก Username ของคุณ">
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-semibold text-slate-700">รหัสผ่าน (Password)</label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-400"></i>
                            </div>
                            <input type="password" id="password" required class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none" placeholder="••••••••">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-4 bg-slate-900 text-white font-semibold rounded-xl hover:bg-indigo-600 focus:ring-4 focus:ring-indigo-200 transition-all active:scale-[0.98] shadow-md mt-6 flex justify-center items-center gap-2 group">
                        <span>เข้าสู่ระบบ</span>
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-xs text-slate-400">มีปัญหาในการเข้าสู่ระบบ? ติดต่อผู้ดูแลระบบ (Admin)</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> กำลังตรวจสอบ...';

        try {
            const response = await fetch('api/auth.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'เข้าสู่ระบบสำเร็จ',
                    text: `ยินดีต้อนรับคุณ ${data.user.fullname}`,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'inquiry_form.php';
                });
            } else {
                Swal.fire({ icon: 'error', title: 'ไม่สำเร็จ', text: data.message });
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
</body>
</html>
