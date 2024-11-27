<?php
session_start();
require_once "database.php";

// Verify CSRF token
if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || $_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die(json_encode(['error' => 'Invalid CSRF token']));
}

// Check if user is logged in
if (!isset($_SESSION["user"]) || !isset($_SESSION["id"])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

// Get and validate input
$input = json_decode(file_get_contents('php://input'), true);
$log_id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

if (!$log_id) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid log ID']));
}

// Prepare and execute query
$query = "SELECT l.*, a.email 
          FROM login_logs l 
          LEFT JOIN administrators a ON l.user_id = a.id 
          WHERE l.id = ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $log_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($log = $result->fetch_assoc()) {
        // Sanitize data before sending
        $log = array_map('htmlspecialchars', $log);
        echo json_encode($log);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Log entry not found']);
    }
    
    $stmt->close();
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}

$conn->close();