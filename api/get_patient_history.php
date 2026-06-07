<?php
// api/get_patient_history.php
require_once 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$hn = $_GET['hn'] ?? '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 30;
// Allowable limits
if (!in_array($limit, [30, 60, 90, 100])) {
    $limit = 30;
}

if (empty(trim($hn))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing HN']);
    exit();
}

try {
    // 0. ข้อมูลทั่วไปผู้ป่วย (Patient Demographics) สำหรับ Patient Identification (HA Standard)
    $stmt_pt = $his_pdo->prepare("SELECT hn, cardid AS cid, ptfname AS fname, ptlname AS lname, ptsex AS sex, ptdob AS birthdate FROM pt.pt WHERE hn = ? LIMIT 1");
    $stmt_pt->execute([$hn]);
    $pt_info = $stmt_pt->fetch();
    
    $patient_demographics = null;
    if ($pt_info) {
        // คำนวณอายุ
        $age = '-';
        if (!empty($pt_info['birthdate'])) {
            $birthDate = new DateTime($pt_info['birthdate']);
            $today = new DateTime('today');
            $age = $birthDate->diff($today)->y;
        }
        $patient_demographics = [
            'hn' => $pt_info['hn'],
            'cid' => $pt_info['cid'],
            'fullname' => decodeThai($pt_info['fname'] ?? '') . ' ' . decodeThai($pt_info['lname'] ?? ''),
            'sex' => $pt_info['sex'] === '1' ? 'ชาย' : ($pt_info['sex'] === '2' ? 'หญิง' : $pt_info['sex']),
            'age' => $age
        ];
    }

    // 1. แพ้ยา (Allergies) - pt.ptallergy
    $stmt_allergy = $his_pdo->prepare("SELECT listname AS agent, symptom, daterecord AS begin_date FROM pt.ptallergy WHERE hn = :hn");
    $stmt_allergy->execute([':hn' => $hn]);
    $allergies = [];
    while ($row = $stmt_allergy->fetch()) {
        $allergies[] = [
            'drug_name' => decodeThai($row['agent'] ?? ''),
            'symptom' => decodeThai($row['symptom'] ?? ''),
            'date' => $row['begin_date']
        ];
    }

    // 2. ประวัติการวินิจฉัย (Diagnosis) - opd.odiag และ ipd.idiag
    // รวมข้อมูลทั้ง OPD และ IPD
    $stmt_dx = $his_pdo->prepare("
        (SELECT regdate AS date, diag AS icd10, descrip AS name, 'OPD' as type FROM opd.odiag WHERE hn = :hn1)
        UNION ALL
        (SELECT dsc_date AS date, diag AS icd10, descrip AS name, 'IPD' as type FROM ipd.idiag WHERE hn = :hn2)
        ORDER BY date DESC LIMIT 15
    ");
    $stmt_dx->execute([':hn1' => $hn, ':hn2' => $hn]);
    $diagnoses = [];
    while ($row = $stmt_dx->fetch()) {
        $diagnoses[] = [
            'date' => $row['date'],
            'icd10' => $row['icd10'],
            'name' => decodeThai($row['name'] ?? ''),
            'type' => $row['type']
        ];
    }

    // 3. ประวัติยาเดิม (Medications) แยกเป็น OPD และ IPD
    // OPD
    $stmt_med_opd = $his_pdo->prepare("
        SELECT regdate AS date, namedrug AS drug_name, amount AS qty, 'OPD' as type 
        FROM opd.drug_order_opd 
        WHERE hn = :hn 
        ORDER BY date DESC LIMIT " . $limit . "
    ");
    $stmt_med_opd->execute([':hn' => $hn]);
    $medications_opd = [];
    while ($row = $stmt_med_opd->fetch()) {
        $medications_opd[] = [
            'date' => $row['date'],
            'drug_name' => decodeThai($row['drug_name'] ?? ''),
            'qty' => $row['qty'],
            'type' => $row['type']
        ];
    }

    // IPD
    $stmt_med_ipd = $his_pdo->prepare("
        SELECT d.orderdate AS date, d.namedrug AS drug_name, d.amount AS qty, 'IPD' as type,
               DATEDIFF(IFNULL(i.datedsc, CURDATE()), i.dateadm) AS los
        FROM ipd.drug_order_ipd d
        LEFT JOIN ipd.ipd i ON d.an = i.an
        WHERE d.hn = :hn 
        ORDER BY d.orderdate DESC LIMIT " . $limit . "
    ");
    $stmt_med_ipd->execute([':hn' => $hn]);
    $medications_ipd = [];
    while ($row = $stmt_med_ipd->fetch()) {
        $medications_ipd[] = [
            'date' => $row['date'],
            'drug_name' => decodeThai($row['drug_name'] ?? ''),
            'qty' => $row['qty'],
            'type' => $row['type'],
            'los' => $row['los'] !== null ? max((int)$row['los'], 1) : null
        ];
    }

    // 4. Lab - opd.result_lab_opd และ ipd.result_lab_ipd
    $stmt_lab = $his_pdo->prepare("
        (SELECT COALESCE(date_result, regdate) AS date, labname AS test_name, result_lab AS result, 'OPD' as type FROM opd.result_lab_opd WHERE hn = :hn1)
        UNION ALL
        (SELECT COALESCE(date_result, orderdate) AS date, labname AS test_name, result_lab AS result, 'IPD' as type FROM ipd.result_lab_ipd WHERE hn = :hn2)
        ORDER BY date DESC LIMIT 20
    ");
    $stmt_lab->execute([':hn1' => $hn, ':hn2' => $hn]);
    $labs = [];
    while ($row = $stmt_lab->fetch()) {
        $labs[] = [
            'date' => $row['date'],
            'test_name' => decodeThai($row['test_name'] ?? ''),
            'result' => decodeThai($row['result'] ?? ''),
            'type' => $row['type']
        ];
    }

    // 5. การนัด (Appointments) - pt.ptappoint และ hos.roomno
    $stmt_appt = $his_pdo->prepare("
        SELECT a.appointdate AS nextdate, a.timeappoint AS nexttime, r.roomname AS clinic, a.causeappoint AS note 
        FROM pt.ptappoint a
        LEFT JOIN hos.roomno r ON a.toroomappoint = r.roomcode
        WHERE a.hn = :hn 
        ORDER BY a.appointdate DESC LIMIT 10
    ");
    $stmt_appt->execute([':hn' => $hn]);
    $appointments = [];
    while ($row = $stmt_appt->fetch()) {
        $appointments[] = [
            'date' => $row['nextdate'],
            'time' => $row['nexttime'],
            'clinic' => decodeThai($row['clinic'] ?? ''),
            'note' => decodeThai($row['note'] ?? '')
        ];
    }

    // 6. บันทึกการรักษา (Present Illness / PI) - opd.opd_pi
    $stmt_pi = $his_pdo->prepare("
        SELECT regdate AS date, pi, tx, reguser 
        FROM opd.opd_pi 
        WHERE hn = :hn AND (pi IS NOT NULL OR tx IS NOT NULL)
        ORDER BY regdate DESC LIMIT 15
    ");
    $stmt_pi->execute([':hn' => $hn]);
    $pi_records = [];
    while ($row = $stmt_pi->fetch()) {
        // Only add if there's actual text content to avoid empty rows
        if (trim($row['pi']) !== '' || trim($row['tx']) !== '') {
            $pi_records[] = [
                'date' => $row['date'],
                'pi' => decodeThai($row['pi'] ?? ''),
                'tx' => decodeThai($row['tx'] ?? ''),
                'user' => decodeThai($row['reguser'] ?? '')
            ];
        }
    }

    // บันทึก Log
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $log_stmt = $app_pdo->prepare("INSERT INTO pharmalink_logs (user_id, action, details, ip_address) VALUES (?, 'VIEW_HISTORY', ?, ?)");
    $log_stmt->execute([$_SESSION['user_id'], "Viewed history for HN: {$hn}", $ip_address]);

    echo json_encode([
        'success' => true,
        'data' => [
            'patient' => $patient_demographics,
            'allergies' => $allergies,
            'diagnoses' => $diagnoses,
            'medications_opd' => $medications_opd,
            'medications_ipd' => $medications_ipd,
            'labs' => $labs,
            'appointments' => $appointments,
            'pi_records' => $pi_records
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'HIS Database error: ' . $e->getMessage()]);
}
?>
