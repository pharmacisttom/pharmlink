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
    <title>เข้าสู่ระบบ - PharmaCare Inquiry System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <div class="mx-auto w-16 h-16 bg-white/20 rounded-2xl backdrop-blur-sm border border-white/10 shadow-inner flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.071 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white tracking-tight">ระบบบันทึกประวัติยา</h2>
                <p class="text-blue-100 mt-1 text-sm font-medium">เข้าสู่ระบบเพื่อใช้งาน</p>
            </div>
        </div>

        <div class="p-8">
            <form id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อผู้ใช้งาน (Username)</label>
                    <input type="text" id="username" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow outline-none" placeholder="กรอก Username">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">รหัสผ่าน (Password)</label>
                    <input type="password" id="password" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow outline-none" placeholder="กรอก Password">
                </div>
                
                <button type="submit" class="w-full py-3.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all active:scale-[0.98] shadow-md shadow-indigo-600/20">
                    เข้าสู่ระบบ
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

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
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้' });
            }
        });
    </script>
</body>
</html>
