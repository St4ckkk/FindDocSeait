<?php
require_once 'core/sessionController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $sessionController = new sessionController();
    $response = $sessionController->sendOtp($email);

    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>