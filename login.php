<?php
include_once './core/sessionController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sessionController = new sessionController();
    $result = $sessionController->login($username, $password);

    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}