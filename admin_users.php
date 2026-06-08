<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">จัดการผู้ใช้งานระบบ</h1>
        <p class="text-slate-500 text-sm mt-1">เพิ่ม/แก้ไข บัญชีผู้ใช้งาน เภสัชกร และผู้ดูแลระบบ</p>
    </div>
    <button onclick="openModal('create')" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold shadow-sm hover:bg-indigo-700 transition-colors flex items-center gap-2 text-sm">
        <i class="fa-solid fa-user-plus"></i> เพิ่มผู้ใช้งาน
    </button>
</div>

<!-- Table Container -->
<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600 font-semibold uppercase tracking-wider text-xs border-b border-slate-200">
                <tr>
                    <th class="p-4 pl-6">ID</th>
                    <th class="p-4">Username</th>
                    <th class="p-4">ชื่อ-สกุล</th>
                    <th class="p-4">บทบาท (Role)</th>
                    <th class="p-4">สถานะ</th>
                    <th class="p-4 text-right pr-6">จัดการ</th>
                </tr>
            </thead>
            <tbody id="userList" class="divide-y divide-slate-100">
                <!-- Data will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg border border-slate-200 overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800">เพิ่มผู้ใช้งาน</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <form id="userForm" class="p-6 space-y-4">
            <input type="hidden" id="userId">
            <input type="hidden" id="actionType" value="create">
            
            <div id="usernameGroup">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Username <span class="text-red-500">*</span></label>
                <input type="text" id="username" required class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อ-สกุล <span class="text-red-500">*</span></label>
                <input type="text" id="fullname" required class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
            </div>
            
            <div id="passwordGroup">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">รหัสผ่าน <span class="text-red-500">*</span></label>
                <input type="password" id="password" class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="รหัสผ่านใหม่">
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">บทบาท (Role) <span class="text-red-500">*</span></label>
                    <select id="role" class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm appearance-none">
                        <option value="pharmacist">Pharmacist</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="statusGroup">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">สถานะ</label>
                    <select id="active" class="w-full px-4 py-2 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none text-sm appearance-none">
                        <option value="1">🟢 เปิดใช้งาน (Active)</option>
                        <option value="0">🔴 ปิดใช้งาน (Inactive)</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex gap-3 justify-end">
                <button type="button" onclick="closeModal()" class="px-5 py-2 text-slate-600 font-medium hover:bg-slate-100 rounded-xl transition-colors text-sm">ยกเลิก</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-medium hover:bg-indigo-700 rounded-xl shadow-sm transition-colors text-sm">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>

<script>
let usersData = [];

// โหลดข้อมูลเมื่อเปิดหน้า
document.addEventListener('DOMContentLoaded', loadUsers);

async function loadUsers() {
    try {
        const res = await fetch('api/admin_users.php?action=list');
        const data = await res.json();
        
        if(data.success) {
            usersData = data.data;
            renderTable();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    } catch(e) {
        Swal.fire('Error', 'ไม่สามารถเชื่อมต่อดึงข้อมูลผู้ใช้ได้', 'error');
    }
}

function renderTable() {
    const tbody = document.getElementById('userList');
    tbody.innerHTML = '';
    
    if(usersData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-slate-400">ยังไม่มีข้อมูล</td></tr>`;
        return;
    }
    
    usersData.forEach(u => {
        const isCurrent = u.id == <?php echo $_SESSION['user_id']; ?>;
        const roleBadge = u.role === 'admin' 
            ? '<span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-bold">Admin</span>' 
            : '<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">Pharmacist</span>';
            
        const activeBadge = u.active == 1 
            ? '<span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs font-bold"><i class="fa-solid fa-circle text-[8px] mr-1"></i>Active</span>' 
            : '<span class="bg-rose-100 text-rose-700 px-2 py-0.5 rounded text-xs font-bold"><i class="fa-solid fa-circle text-[8px] mr-1"></i>Inactive</span>';

        tbody.innerHTML += `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="p-4 pl-6 text-slate-500 font-mono">#${u.id}</td>
                <td class="p-4 font-semibold text-slate-800">${u.userlogin} ${isCurrent ? '<span class="text-[10px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded ml-1">You</span>' : ''}</td>
                <td class="p-4 text-slate-600">${u.fullname}</td>
                <td class="p-4">${roleBadge}</td>
                <td class="p-4">${activeBadge}</td>
                <td class="p-4 text-right pr-6 space-x-2">
                    <button onclick='openModal("edit", ${JSON.stringify(u)})' class="text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50 transition-colors" title="แก้ไข">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button onclick="resetPassword(${u.id}, '${u.userlogin}')" class="text-amber-500 hover:text-amber-700 p-2 rounded-lg hover:bg-amber-50 transition-colors" title="รีเซ็ตรหัสผ่าน">
                        <i class="fa-solid fa-key"></i>
                    </button>
                    ${!isCurrent ? `
                    <button onclick="deleteUser(${u.id}, '${u.userlogin}')" class="text-rose-500 hover:text-rose-700 p-2 rounded-lg hover:bg-rose-50 transition-colors" title="ลบผู้ใช้">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                    ` : ''}
                </td>
            </tr>
        `;
    });
}

function openModal(action, user = null) {
    const modal = document.getElementById('userModal');
    const title = document.getElementById('modalTitle');
    const actionType = document.getElementById('actionType');
    
    // Reset Form
    document.getElementById('userForm').reset();
    document.getElementById('usernameGroup').style.display = 'block';
    document.getElementById('passwordGroup').style.display = 'block';
    document.getElementById('statusGroup').style.display = 'block';
    
    if (action === 'create') {
        title.innerText = 'เพิ่มผู้ใช้งานใหม่';
        actionType.value = 'create';
        document.getElementById('username').required = true;
        document.getElementById('password').required = true;
        document.getElementById('statusGroup').style.display = 'none'; // New users are always active
    } else if (action === 'edit' && user) {
        title.innerText = `แก้ไขผู้ใช้งาน: ${user.userlogin}`;
        actionType.value = 'edit';
        document.getElementById('userId').value = user.id;
        
        // Hide Username and Password fields
        document.getElementById('usernameGroup').style.display = 'none';
        document.getElementById('passwordGroup').style.display = 'none';
        document.getElementById('username').required = false;
        document.getElementById('password').required = false;
        
        // Fill data
        document.getElementById('fullname').value = user.fullname;
        document.getElementById('role').value = user.role;
        document.getElementById('active').value = user.active;
    }
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

// Close modal on click outside
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.getElementById('userForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const action = document.getElementById('actionType').value;
    let url = 'api/admin_users.php?action=create';
    let payload = {};
    
    if (action === 'create') {
        payload = {
            userlogin: document.getElementById('username').value,
            fullname: document.getElementById('fullname').value,
            password: document.getElementById('password').value,
            role: document.getElementById('role').value
        };
    } else {
        url = 'api/admin_users.php?action=update';
        payload = {
            id: document.getElementById('userId').value,
            fullname: document.getElementById('fullname').value,
            role: document.getElementById('role').value,
            active: document.getElementById('active').value
        };
    }
    
    try {
        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        
        if (data.success) {
            Swal.fire('สำเร็จ', data.message, 'success');
            closeModal();
            loadUsers();
        } else {
            Swal.fire('ข้อผิดพลาด', data.message, 'error');
        }
    } catch (err) {
        Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
    }
});

async function resetPassword(id, username) {
    const { value: newPassword } = await Swal.fire({
        title: `รีเซ็ตรหัสผ่าน`,
        text: `ระบุรหัสผ่านใหม่สำหรับ: ${username}`,
        input: 'password',
        inputPlaceholder: 'รหัสผ่านใหม่',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันการเปลี่ยน',
        cancelButtonText: 'ยกเลิก',
        inputValidator: (value) => {
            if (!value) return 'คุณต้องระบุรหัสผ่าน!';
            if (value.length < 6) return 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        }
    });

    if (newPassword) {
        try {
            Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const res = await fetch('api/admin_users.php?action=reset_password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, new_password: newPassword })
            });
            const data = await res.json();
            
            if (data.success) {
                Swal.fire('สำเร็จ', data.message, 'success');
            } else {
                Swal.fire('ข้อผิดพลาด', data.message, 'error');
            }
        } catch (err) {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
        }
    }
}

function deleteUser(id, username) {
    Swal.fire({
        title: 'ยืนยันการลบ',
        text: `คุณต้องการลบผู้ใช้ "${username}" ใช่หรือไม่? (ระวัง: หากผู้ใช้นี้เคยบันทึกประวัติการโทร อาจจะลบไม่ได้เนื่องจากมีข้อมูลผูกพันอยู่ ให้ใช้วิธีปิดใช้งานแทน)`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                Swal.fire({ title: 'กำลังลบ...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                const res = await fetch('api/admin_users.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const data = await res.json();
                
                if (data.success) {
                    Swal.fire('ลบสำเร็จ!', data.message, 'success');
                    loadUsers();
                } else {
                    Swal.fire('ข้อผิดพลาด', data.message, 'error');
                }
            } catch (err) {
                Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error');
            }
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
