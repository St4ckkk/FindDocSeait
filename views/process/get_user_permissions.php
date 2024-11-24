<?php
include_once '../../core/userController.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

$user_id = intval($_GET['user_id']);
error_log("Fetching permissions for user ID: " . $user_id);

$userController = new userController();
$permissions = $userController->getUserPermissions($user_id);

if ($permissions !== false) {
    echo json_encode(['status' => 'success', 'permissions' => $permissions]);
} else {
    error_log("Failed to fetch permissions for user ID: " . $user_id);
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch user permissions']);
}
?>