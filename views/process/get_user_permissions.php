<?php
include_once '../../core/userController.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

$user_id = $_GET['user_id'];
$userController = new userController();
$permissions = $userController->getUserPermissions($user_id);

if ($permissions !== false) {
    echo json_encode(['status' => 'success', 'permissions' => $permissions]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch user permissions']);
}
?>