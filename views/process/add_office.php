<?php
include_once '../../core/userController.php';

header('Content-Type: application/json'); // Ensure the response is JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = [
        'name' => $_POST['name']
    ];

    $userController = new userController();
    $result = $userController->addOffice($params);

    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}