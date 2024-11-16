<?php
include_once '../../core/userController.php';

header('Content-Type: application/json'); // Ensure the response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = [
        'fullname' => $_POST['fullname'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'email' => $_POST['email'],
        'role_id' => $_POST['role_id']
    ];

    $userController = new userController();
    $result = $userController->addAdmin($params);

    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}