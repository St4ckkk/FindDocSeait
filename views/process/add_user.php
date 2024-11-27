<?php
include_once '../../core/userController.php';

session_start();
if (!isset($_SESSION['csrf_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$data = $_POST;
$userController = new userController();
$result = $userController->addAdmin($data);

echo json_encode($result);
?>