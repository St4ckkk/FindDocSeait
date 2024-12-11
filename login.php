<?php
include_once './core/sessionController.php';

header('Content-Type: application/json');

$sessionController = new sessionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['otp'])) {
        $otp = $_POST['otp'];
        $result = $sessionController->verifyOtp($otp);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'getOtp') {
        $result = $sessionController->getOtp();
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $result = $sessionController->login($username, $password);
    }

    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}