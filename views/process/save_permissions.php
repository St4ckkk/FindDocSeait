<?php
include_once '../../core/documentController.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['user_id']) || !isset($data['permissions'])) {
        throw new Exception('Missing required parameters');
    }

    $user_id = intval($data['user_id']);
    $permissions = $data['permissions'];

    error_log("Saving permissions for user ID: " . $user_id . " with permissions: " . json_encode($permissions));

    $documentController = new documentController();
    $response = $documentController->saveUserPermissions($user_id, $permissions, $_SESSION['csrf_token']);

    if ($response) {
        echo json_encode(['status' => 'success']);
    } else {
        error_log("Failed to save permissions for user ID: " . $user_id);
        echo json_encode(['status' => 'error', 'message' => 'Failed to save permissions']);
    }
} catch (Exception $e) {
    error_log("Error saving permissions: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>