<?php
// api/create_request.php

require_once 'db_connect.php';

header('Content-Type: application/json');

// Handle preflight requests for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Please use POST.']);
    exit();
}

// Get the raw POST data
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// Validate required fields
$required_fields = ['requester_id', 'drug_id', 'quantity', 'urgency_level'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit();
    }
}

// Sanitize inputs
$requester_id = intval($input['requester_id']);
$drug_id = intval($input['drug_id']);
$quantity = intval($input['quantity']);
$urgency_level = htmlspecialchars(strip_tags($input['urgency_level']));
$status = 'Request'; // Initial status

// Allowed urgency levels
$allowed_urgency = ['Low', 'Medium', 'High', 'Emergency'];
if (!in_array($urgency_level, $allowed_urgency)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid urgency level.']);
    exit();
}

try {
    // Begin transaction
    $app_pdo->beginTransaction();

    // 1. Insert into transactions table
    $query = "INSERT INTO transactions (requester_id, drug_id, quantity, urgency_level, status) 
              VALUES (:requester_id, :drug_id, :quantity, :urgency_level, :status)";
    
    $stmt = $app_pdo->prepare($query);
    
    // Bind parameters
    $stmt->bindParam(':requester_id', $requester_id, PDO::PARAM_INT);
    $stmt->bindParam(':drug_id', $drug_id, PDO::PARAM_INT);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':urgency_level', $urgency_level);
    $stmt->bindParam(':status', $status);
    
    if ($stmt->execute()) {
        $ticket_id = $app_pdo->lastInsertId();

        // 2. Insert into transaction_logs table
        $log_query = "INSERT INTO transaction_logs (ticket_id, status, changed_by, notes) 
                      VALUES (:ticket_id, :status, :changed_by, :notes)";
        $log_stmt = $app_pdo->prepare($log_query);
        
        $notes = "Initial drug request created.";
        $log_stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $log_stmt->bindParam(':status', $status);
        $log_stmt->bindParam(':changed_by', $requester_id, PDO::PARAM_INT);
        $log_stmt->bindParam(':notes', $notes);
        
        $log_stmt->execute();
        
        // Commit transaction
        $app_pdo->commit();
        
        http_response_code(201); // Created
        echo json_encode([
            'success' => true, 
            'message' => 'Drug request created successfully.',
            'ticket_id' => $ticket_id
        ]);
    } else {
        // Rollback on failure
        $app_pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create request.']);
    }

} catch (PDOException $e) {
    // Rollback if exception occurs
    if ($app_pdo->inTransaction()) {
        $app_pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
