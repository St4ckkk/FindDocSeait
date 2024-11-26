<?php
session_start(); // Start the session at the beginning of the script
include_once '../../core/documentController.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['user_id']) || !isset($data['permissions'])) {
        throw new Exception('Missing required parameters');
    }

    if (!isset($_SESSION['csrf_token'])) {
        throw new Exception('CSRF token not found in session');
    }

    $user_id = intval($data['user_id']);
    $permissions = $data['permissions'];

    error_log("Saving permissions for user ID: " . $user_id . " with permissions: " . json_encode($permissions));

    $documentController = new documentController();
    $response = $documentController->saveUserPermissions($user_id, $permissions, $_SESSION['csrf_token']);

    echo json_encode($response);
} catch (Exception $e) {
    error_log("Error saving permissions: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>