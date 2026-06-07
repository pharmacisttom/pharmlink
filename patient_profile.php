<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$hn = $_GET['hn'] ?? '';
if (!$hn) {
    die("ไม่พบข้อมูล HN");
}

// กำหนดให้ซ่อน Sidebar ในหน้านี้
$hide_sidebar = false;
require_once 'includes/header.php';
?>
<style>
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .tab-btn { border-bottom: 2px solid transparent; }
    .tab-btn.active { border-bottom-color: #4f46e5; color: #4f46e5; font-weight: 600; }
</style>
<body class="bg-slate-50 min-h-screen pb-12">
    
    <nav class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">ประวัติผู้ป่วย (Patient Profile)</h1>
                    <p class="text-blue-100 text-sm mt-0.5"><span class="bg-blue-600 px-2 py-0.5 rounded text-xs font-semibold uppercase tracking-wider mr-2">HA Standard</span> ระบบเชื่อมต่อฐานข้อมูล HIS</p>
                </div>
                <!-- HA Standard: Two Identifiers -->
                <div class="bg-white/10 px-6 py-3 rounded-2xl backdrop-blur-sm border border-white/20 shadow-inner flex flex-col items-end">
                    <div class="font-mono text-xl font-bold" id="header-hn">HN: <?php echo htmlspecialchars($hn); ?></div>
                    <div class="text-sm font-medium mt-1 text-blue-50" id="header-name">กำลังโหลดข้อมูล...</div>
                    <div class="text-xs text-blue-200 mt-0.5" id="header-cid"></div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        
        <!-- Tabs Header -->
        <div class="bg-white rounded-t-2xl shadow-sm border border-slate-200 border-b-0 flex overflow-x-auto">
            <button onclick="switchTab('tab-allergy')" id="btn-allergy" class="tab-btn active px-6 py-4 text-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors whitespace-nowrap">
                ⚠️ ประวัติแพ้ยา <span id="count-allergy" class="ml-1 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-bold">0</span>
            </button>
            <button onclick="switchTab('tab-meds-opd')" id="btn-meds-opd" class="tab-btn px-6 py-4 text-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors whitespace-nowrap">
                💊 ประวัติยา (OPD) <span id="count-meds-opd" class="ml-1 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-xs font-bold">0</span>
            </button>
            <button onclick="switchTab('tab-meds-ipd')" id="btn-meds-ipd" class="tab-btn px-6 py-4 text-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors whitespace-nowrap">
                🛌 ประวัติการนอน รพ. (IPD) <span id="count-meds-ipd" class="ml-1 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-xs font-bold">0</span>
            </button>
            <button onclick="switchTab('tab-diag')" id="btn-diag" class="tab-btn px-6 py-4 text-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors whitespace-nowrap">
                🩺 การวินิจฉัย <span id="count-diag" class="ml-1 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-xs font-bold">0</span>
            </button>
            <button onclick="switchTab('tab-lab')" id="btn-lab" class="tab-btn px-6 py-4 text-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors whitespace-nowrap">
                🔬 ผล Lab
            </button>
            <button onclick="switchTab('tab-appt')" id="btn-appt" class="tab-btn px-6 py-4 text-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors whitespace-nowrap">
                📅 การนัดหมาย
            </button>
            <button onclick="switchTab('tab-pi')" id="btn-pi" class="tab-btn px-6 py-4 text-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors whitespace-nowrap">
                📝 บันทึกการรักษา (PI) <span id="count-pi" class="ml-1 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-xs font-bold">0</span>
            </button>
        </div>

        <!-- Tabs Content -->
        <div class="bg-white rounded-b-2xl shadow-sm border border-slate-200 p-6 min-h-[400px] relative">
            
            <div id="loading" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex flex-col items-center justify-center rounded-b-2xl">
                <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                <p class="mt-4 text-indigo-600 font-medium">กำลังโหลดข้อมูลจากระบบ HIS...</p>
            </div>

            <!-- Tab: แพ้ยา -->
            <div id="tab-allergy" class="tab-content active">
                <h3 class="text-lg font-bold text-red-600 mb-4 flex items-center gap-2">⚠️ ประวัติแพ้ยา</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-red-50 text-red-700 text-sm">
                            <tr><th class="p-3">วันที่บันทึก</th><th class="p-3">ชื่อยา</th><th class="p-3">อาการแพ้</th></tr>
                        </thead>
                        <tbody id="tb-allergy" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: ประวัติยา OPD -->
            <div id="tab-meds-opd" class="tab-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">💊 ประวัติการได้รับยาผู้ป่วยนอก (OPD)</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-500">แสดงล่าสุด:</span>
                        <select id="limit-opd" onchange="loadData()" class="text-sm border border-slate-300 rounded-lg px-2 py-1 outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="30">30 รายการ</option>
                            <option value="60">60 รายการ</option>
                            <option value="90">90 รายการ</option>
                            <option value="100">100 รายการ</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-sm">
                            <tr><th class="p-3">วันที่</th><th class="p-3">ประเภท</th><th class="p-3">ชื่อยา</th><th class="p-3">จำนวน</th></tr>
                        </thead>
                        <tbody id="tb-meds-opd" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: ประวัติยา IPD -->
            <div id="tab-meds-ipd" class="tab-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-slate-800">🛌 ประวัติการได้รับยาผู้ป่วยใน (IPD)</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-500">แสดงล่าสุด:</span>
                        <select id="limit-ipd" onchange="loadData()" class="text-sm border border-slate-300 rounded-lg px-2 py-1 outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="30">30 รายการ</option>
                            <option value="60">60 รายการ</option>
                            <option value="90">90 รายการ</option>
                            <option value="100">100 รายการ</option>
                        </select>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-sm">
                            <tr><th class="p-3">วันที่</th><th class="p-3">ประเภท</th><th class="p-3">ชื่อยา</th><th class="p-3">จำนวน</th><th class="p-3">วันนอน (LOS)</th></tr>
                        </thead>
                        <tbody id="tb-meds-ipd" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: วินิจฉัย -->
            <div id="tab-diag" class="tab-content">
                <h3 class="text-lg font-bold text-slate-800 mb-4">🩺 ประวัติการวินิจฉัย (Diagnosis)</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-sm">
                            <tr><th class="p-3">วันที่</th><th class="p-3">ประเภท</th><th class="p-3">ICD-10</th><th class="p-3">โรค/การวินิจฉัย</th></tr>
                        </thead>
                        <tbody id="tb-diag" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Lab -->
            <div id="tab-lab" class="tab-content">
                <h3 class="text-lg font-bold text-slate-800 mb-4">🔬 ประวัติผลการตรวจทางห้องปฏิบัติการ (Lab)</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-sm">
                            <tr><th class="p-3">วันที่รายงาน</th><th class="p-3">ประเภท</th><th class="p-3">รายการตรวจ</th><th class="p-3">ผลตรวจ</th></tr>
                        </thead>
                        <tbody id="tb-lab" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: นัดหมาย -->
            <div id="tab-appt" class="tab-content">
                <h3 class="text-lg font-bold text-slate-800 mb-4">📅 ประวัติการนัดหมาย</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-sm">
                            <tr><th class="p-3">วันที่นัด</th><th class="p-3">เวลา</th><th class="p-3">ห้องตรวจ/คลินิก</th><th class="p-3">หมายเหตุ</th></tr>
                        </thead>
                        <tbody id="tb-appt" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: PI -->
            <div id="tab-pi" class="tab-content">
                <h3 class="text-lg font-bold text-slate-800 mb-4">📝 บันทึกการรักษา (Present Illness)</h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-600 text-sm">
                            <tr><th class="p-3">วันที่</th><th class="p-3">อาการสำคัญ (PI)</th><th class="p-3">การรักษา (TX)</th><th class="p-3">ผู้บันทึก</th></tr>
                        </thead>
                        <tbody id="tb-pi" class="divide-y divide-slate-100 text-sm text-slate-700"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        const hn = '<?php echo $hn; ?>';

        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Extracts the ID suffix, e.g., 'tab-meds-opd' -> 'meds-opd'
            const suffix = tabId.replace('tab-', '');
            document.getElementById(`btn-${suffix}`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function renderTable(dataArray, tbodyId, columns, emptyMsg = "ไม่มีข้อมูล") {
            const tbody = document.getElementById(tbodyId);
            if (!dataArray || dataArray.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${columns.length}" class="p-6 text-center text-slate-400">${emptyMsg}</td></tr>`;
                return;
            }
            
            // List of common High Alert Drugs (HAD) in Thailand
            const hadKeywords = ['insulin', 'warfarin', 'heparin', 'digoxin', 'potassium', 'fentanyl', 'morphine', 'adrenaline', 'noradrenaline', 'dopamine', 'amiodarone'];

            tbody.innerHTML = dataArray.map(item => {
                return `<tr class="hover:bg-slate-50">` + 
                    columns.map(col => {
                        let val = item[col];
                        
                        if (col === 'type') {
                            val = val === 'IPD' ? `<span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs">IPD</span>` : `<span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">OPD</span>`;
                        }
                        
                        // High Alert Drug Logic
                        if (col === 'drug_name' && typeof val === 'string') {
                            const isHAD = hadKeywords.some(kw => val.toLowerCase().includes(kw));
                            if (isHAD) {
                                val = `<span class="text-red-600 font-bold flex items-center gap-1">⚠️ ${val} <span class="bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded uppercase">High Alert</span></span>`;
                            }
                        }

                        if (col === 'los') {
                            val = val ? `${val} วัน` : '-';
                        }

                        return `<td class="p-3 border-b border-slate-50">${val || '-'}</td>`;
                    }).join('') + 
                `</tr>`;
            }).join('');
        }

        async function loadData() {
            try {
                // Get the limit from either dropdown (they are synced or you can read the active one, but we fetch the max of both for simplicity or sync them)
                // Let's sync them or just read limit-opd (assuming they share the same limit logic per patient fetch)
                const limitSelect = document.getElementById('limit-opd');
                const limit = limitSelect ? limitSelect.value : 30;
                // Sync the other dropdown just in case
                if (document.getElementById('limit-ipd')) document.getElementById('limit-ipd').value = limit;

                document.getElementById('loading').style.display = 'flex';
                
                const res = await fetch(`api/get_patient_history.php?hn=${hn}&limit=${limit}`);
                const result = await res.json();
                
                document.getElementById('loading').style.display = 'none';

                if (result.success) {
                    const d = result.data;
                    
                    // Populate Patient Demographics (Two Identifiers)
                    if (d.patient) {
                        document.getElementById('header-name').innerText = `👤 ${d.patient.fullname} (${d.patient.sex}, ${d.patient.age} ปี)`;
                        document.getElementById('header-cid').innerText = `CID: ${d.patient.cid}`;
                    } else {
                        document.getElementById('header-name').innerText = 'ไม่พบข้อมูลส่วนตัวในฐานข้อมูล';
                    }

                    // Update Counts
                    document.getElementById('count-allergy').innerText = d.allergies.length;
                    document.getElementById('count-meds-opd').innerText = d.medications_opd.length;
                    document.getElementById('count-meds-ipd').innerText = d.medications_ipd.length;
                    document.getElementById('count-diag').innerText = d.diagnoses.length;
                    document.getElementById('count-pi').innerText = d.pi_records ? d.pi_records.length : 0;

                    // HA Standard: Extremely prominent allergy alert
                    if(d.allergies.length > 0) {
                        document.getElementById('count-allergy').className = "ml-1 bg-red-600 text-white py-0.5 px-2 rounded-full text-xs font-bold animate-pulse";
                        // Prevent multi-firing swal if we just reload limit
                        if (!window.allergyAlertShown) {
                            Swal.fire({
                                title: 'คำเตือน: ผู้ป่วยมีประวัติแพ้ยา!',
                                text: 'กรุณาตรวจสอบประวัติการแพ้ยาก่อนให้คำแนะนำหรือสั่งยาเสมอ',
                                icon: 'warning',
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'รับทราบ'
                            });
                            window.allergyAlertShown = true;
                        }
                    } else {
                        document.getElementById('count-allergy').className = "ml-1 bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-xs font-bold";
                    }

                    // Render Tables
                    renderTable(d.allergies, 'tb-allergy', ['date', 'drug_name', 'symptom'], 'ไม่มีประวัติแพ้ยา');
                    renderTable(d.medications_opd, 'tb-meds-opd', ['date', 'type', 'drug_name', 'qty']);
                    renderTable(d.medications_ipd, 'tb-meds-ipd', ['date', 'type', 'drug_name', 'qty', 'los'], 'ไม่มีประวัติการนอนโรงพยาบาล');
                    renderTable(d.diagnoses, 'tb-diag', ['date', 'type', 'icd10', 'name']);
                    renderTable(d.labs, 'tb-lab', ['date', 'type', 'test_name', 'result']);
                    renderTable(d.appointments, 'tb-appt', ['date', 'time', 'clinic', 'note']);
                    renderTable(d.pi_records, 'tb-pi', ['date', 'pi', 'tx', 'user'], 'ไม่มีบันทึกการรักษา');

                } else {
                    Swal.fire('ข้อผิดพลาด', result.message, 'error');
                }
            } catch(e) {
                document.getElementById('loading').style.display = 'none';
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อดึงประวัติได้', 'error');
            }
        }

        window.onload = loadData;
    </script>
</body>
</html>
