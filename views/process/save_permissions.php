<?php
include_once '../../core/documentController.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$permissions = $data['permissions'];

$documentController = new documentController();
$response = $documentController->saveUserPermissions($user_id, $permissions);

echo json_encode($response);
?>