<?php
include_once '../../core/userController.php';

session_start();
if (!isset($_SESSION['csrf_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$role_id = $data['role_id'];

$userController = new userController();
$result = $userController->updateUserRole($user_id, $role_id);

echo json_encode($result);
?>