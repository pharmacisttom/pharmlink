<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php require_once 'includes/header.php'; ?>

<!-- เนื้อหาของ Inquiry Form -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative">
    
    <!-- ฝั่งซ้าย: ฟอร์มบันทึกข้อมูล -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sticky top-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-indigo-600"></div>
            
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-600">
                        <i class="fa-solid fa-phone-volume text-lg"></i>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">ข้อมูลผู้โทรสอบถาม</h2>
                </div>
                
                <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200" title="เริ่มจับเวลาเมื่อพิมพ์หรือค้นหา">
                    <span class="animate-pulse text-indigo-600"><i class="fa-solid fa-stopwatch"></i></span>
                    <span id="caseTimer" class="font-mono text-lg font-bold text-slate-700 tracking-wider">00:00</span>
                </div>
            </div>

            <form id="inquiryForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อ-สกุล ผู้สอบถาม *</label>
                    <input type="text" id="inquirer_name" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">โรงพยาบาลที่ติดต่อมา *</label>
                    <input type="text" id="hospital" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm">
                    <div id="suggest_hospitals" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">จังหวัด *</label>
                        <input type="text" id="province" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">เบอร์โทร *</label>
                        <input type="tel" id="phone_number" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ตำแหน่ง *</label>
                    <input type="text" id="position" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm" placeholder="เช่น เภสัชกร, พยาบาลวิชาชีพ">
                    <div id="suggest_positions" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">เลขใบประกอบวิชาชีพ (ถ้ามี)</label>
                    <input type="text" id="license_number" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">วัตถุประสงค์การขอข้อมูล (HA/PDPA) *</label>
                    <textarea id="inquiry_reason" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm" placeholder="เช่น เพื่อการรักษาต่อเนื่อง, ตรวจสอบประวัติแพ้ยาก่อนสั่งยา"></textarea>
                    <div id="suggest_reasons" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 flex items-start gap-3 mt-4">
                    <input type="checkbox" id="pdpa_check" required class="mt-1 w-4 h-4 text-indigo-600 rounded">
                    <label for="pdpa_check" class="text-xs text-blue-800 leading-relaxed">
                        ข้าพเจ้ายืนยันว่าการสืบค้นข้อมูลนี้เป็นไปเพื่อประโยชน์ในการรักษาพยาบาลผู้ป่วยอย่างต่อเนื่อง และจะปกปิดข้อมูลเป็นความลับตามมาตรฐาน HA และ PDPA
                    </label>
                </div>
                
                <input type="hidden" id="patient_hn" value="">
                
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                        บันทึกข้อมูลการโทร
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ฝั่งขวา: ค้นหาผู้ป่วยจาก HIS -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass text-indigo-600"></i> ค้นหาผู้ป่วยจากระบบ HIS
            </h2>
            <div class="flex gap-3 relative">
                <input type="text" id="searchInput" autofocus class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-shadow text-sm pr-10" placeholder="กรอก HN, เลขบัตร 13 หลัก, หรือ ชื่อ-สกุล...">
                <button type="button" onclick="document.getElementById('searchInput').value=''; document.getElementById('searchInput').focus();" class="absolute right-[100px] top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-2">
                    <i class="fa-solid fa-times"></i>
                </button>
                <button onclick="searchPatient()" class="px-6 py-3 bg-slate-800 text-white font-semibold rounded-xl hover:bg-slate-900 transition-colors shadow-sm whitespace-nowrap">
                    ค้นหา
                </button>
            </div>
        </div>

        <!-- ผลการค้นหา -->
        <div id="searchResults" class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                            <th class="p-4 pl-6">HN</th>
                            <th class="p-4">CID</th>
                            <th class="p-4">ชื่อ-สกุล</th>
                            <th class="p-4 text-right pr-6">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="resultList" class="divide-y divide-slate-100 text-sm">
                        <!-- จะถูกเติมด้วย JS -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    let timerInterval = null;
    let secondsElapsed = 0;
    let isTimerRunning = false;

    function formatTime(sec) {
        const m = Math.floor(sec / 60).toString().padStart(2, '0');
        const s = (sec % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function startTimer() {
        if (!isTimerRunning) {
            isTimerRunning = true;
            document.getElementById('caseTimer').classList.add('text-indigo-600');
            timerInterval = setInterval(() => {
                secondsElapsed++;
                document.getElementById('caseTimer').innerText = formatTime(secondsElapsed);
            }, 1000);
        }
    }

    function resetTimer() {
        clearInterval(timerInterval);
        isTimerRunning = false;
        secondsElapsed = 0;
        document.getElementById('caseTimer').innerText = '00:00';
        document.getElementById('caseTimer').classList.remove('text-indigo-600');
    }

    // เริ่มเวลาเมื่อมีการพิมพ์หรือคลิก input ใดๆ ในฟอร์ม
    document.getElementById('inquiryForm').addEventListener('input', startTimer);
    document.getElementById('searchInput').addEventListener('input', startTimer);

    async function searchPatient() {
        startTimer();
        const q = document.getElementById('searchInput').value;
        if (!q) return Swal.fire('แจ้งเตือน', 'กรุณาระบุคำค้นหา', 'warning');

        Swal.fire({ title: 'กำลังค้นหา...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

        try {
            const res = await fetch(`api/search_patient.php?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            Swal.close();

            if (data.success) {
                const tbody = document.getElementById('resultList');
                tbody.innerHTML = '';
                if (data.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" class="p-8 text-center text-slate-500">ไม่พบข้อมูลผู้ป่วย</td></tr>`;
                } else {
                    data.data.forEach(pt => {
                        tbody.innerHTML += `
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 pl-6 font-mono text-indigo-600 font-semibold">${pt.hn}</td>
                                <td class="p-4 text-slate-500">${pt.cid}</td>
                                <td class="p-4 font-medium text-slate-800">${pt.fname} ${pt.lname}</td>
                                <td class="p-4 text-right pr-6">
                                    <button onclick="selectPatient('${pt.hn}', '${pt.fname} ${pt.lname}')" class="px-4 py-2 bg-indigo-50 text-indigo-600 font-medium rounded-lg hover:bg-indigo-100 transition-colors text-xs flex items-center justify-end gap-2 ms-auto">
                                        <i class="fa-solid fa-file-medical"></i> เลือกและดูประวัติ
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('searchResults').classList.remove('hidden');
            } else {
                Swal.fire('ข้อผิดพลาด', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
        }
    }

    // Smart Suggestions Algorithm
    async function loadSuggestions() {
        try {
            const res = await fetch('api/get_suggestions.php');
            const data = await res.json();
            
            if (data.success) {
                // Render Hospitals
                const hospContainer = document.getElementById('suggest_hospitals');
                data.data.hospitals.forEach(h => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-full hover:bg-indigo-100 transition-colors border border-indigo-100';
                    btn.innerText = h.hospital;
                    btn.onclick = () => {
                        document.getElementById('hospital').value = h.hospital;
                        document.getElementById('province').value = h.province;
                        // Focus next field
                        document.getElementById('phone_number').focus();
                    };
                    hospContainer.appendChild(btn);
                });

                // Render Positions
                const posContainer = document.getElementById('suggest_positions');
                data.data.positions.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'px-3 py-1 bg-emerald-50 text-emerald-600 text-xs font-medium rounded-full hover:bg-emerald-100 transition-colors border border-emerald-100';
                    btn.innerText = p.position;
                    btn.onclick = () => {
                        document.getElementById('position').value = p.position;
                        document.getElementById('license_number').focus();
                    };
                    posContainer.appendChild(btn);
                });

                // Render Reasons
                const reasonContainer = document.getElementById('suggest_reasons');
                data.data.reasons.forEach(r => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'px-3 py-1 bg-amber-50 text-amber-700 text-xs font-medium rounded-full hover:bg-amber-100 transition-colors border border-amber-100';
                    btn.innerText = r.inquiry_reason;
                    btn.onclick = () => {
                        document.getElementById('inquiry_reason').value = r.inquiry_reason;
                        document.getElementById('pdpa_check').focus();
                    };
                    reasonContainer.appendChild(btn);
                });
            }
        } catch (error) {
            console.error('Failed to load suggestions', error);
        }
    }

    // Load suggestions on init
    loadSuggestions();

    // Trigger search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            searchPatient();
        }
    });

    function selectPatient(hn, name) {
        document.getElementById('patient_hn').value = hn;
        
        Swal.fire({
            icon: 'info',
            title: 'นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)',
            html: `
                <div class="text-left text-sm space-y-2 mt-2 bg-slate-50 p-4 rounded-lg border border-slate-200 text-slate-700">
                    <p>คุณกำลังจะเข้าถึงข้อมูลประวัติการรักษาของผู้ป่วย <b>${name} (HN: ${hn})</b></p>
                    <p>ข้อมูลทั้งหมดถือเป็นความลับทางการแพทย์ ตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA)</p>
                    <p class="font-bold text-red-600 mt-2">ข้าพเจ้ายืนยันว่า:</p>
                    <ul class="list-disc pl-5">
                        <li>เข้าถึงข้อมูลเพื่อประโยชน์ในการรักษาพยาบาลเท่านั้น</li>
                        <li>จะปกปิดข้อมูลเป็นความลับ ไม่เผยแพร่ต่อผู้ไม่มีส่วนเกี่ยวข้อง</li>
                        <li>รับทราบว่าระบบมีการบันทึกประวัติการเข้าถึงข้อมูล (Audit Trail)</li>
                    </ul>
                </div>
            `,
            confirmButtonText: 'ข้าพเจ้ายอมรับและเข้าดูประวัติ',
            confirmButtonColor: '#4f46e5',
            showCancelButton: true,
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'เลือกผู้ป่วยเรียบร้อย',
                    text: 'อย่าลืมกรอกข้อมูลผู้โทรและ "วัตถุประสงค์" ในแบบฟอร์มด้านซ้ายมือเพื่อบันทึกลงระบบนะครับ',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
                    window.open(`patient_profile.php?hn=${hn}`, '_blank');
                });
            } else {
                document.getElementById('patient_hn').value = '';
            }
        });
    }

    document.getElementById('inquiryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // หยุดเวลาขณะบันทึก
        if (isTimerRunning) clearInterval(timerInterval);

        const payload = {
            inquirer_name: document.getElementById('inquirer_name').value,
            hospital: document.getElementById('hospital').value,
            province: document.getElementById('province').value,
            position: document.getElementById('position').value,
            license_number: document.getElementById('license_number').value,
            phone_number: document.getElementById('phone_number').value,
            inquiry_reason: document.getElementById('inquiry_reason').value,
            patient_hn: document.getElementById('patient_hn').value,
            duration_seconds: secondsElapsed
        };

        Swal.fire({ title: 'กำลังบันทึก...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

        try {
            const res = await fetch('api/save_inquiry.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            if (data.success) {
                Swal.fire('สำเร็จ!', `บันทึกข้อมูลเรียบร้อยแล้ว<br><span class="text-sm text-slate-500">ใช้เวลาไปทั้งหมด ${formatTime(secondsElapsed)} นาที</span>`, 'success').then(() => {
                    e.target.reset();
                    document.getElementById('patient_hn').value = '';
                    document.getElementById('searchResults').classList.add('hidden');
                    document.getElementById('searchInput').value = '';
                    resetTimer();
                });
            } else {
                Swal.fire('ข้อผิดพลาด', data.message, 'error');
                if (isTimerRunning) {
                    timerInterval = setInterval(() => {
                        secondsElapsed++;
                        document.getElementById('caseTimer').innerText = formatTime(secondsElapsed);
                    }, 1000);
                }
            }
        } catch (error) {
            Swal.fire('ข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ล้มเหลว', 'error');
            if (isTimerRunning) {
                timerInterval = setInterval(() => {
                    secondsElapsed++;
                    document.getElementById('caseTimer').innerText = formatTime(secondsElapsed);
                }, 1000);
            }
        }
    });
</script>
<?php require_once 'includes/footer.php'; ?>
