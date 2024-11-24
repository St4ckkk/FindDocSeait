<?php
require 'vendor/autoload.php';
include_once './core/sessionController.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new sessionController();
    $email = $_POST['email']; // Replace with the actual user ID if needed
    $result = $controller->generateGoogleAuthSecret($email);

    if ($result['status'] === 'success') {
        $response = ['status' => 'success', 'qrCodeUrl' => $result['qrCodeUrl']];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to generate Google Authenticator secret'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>