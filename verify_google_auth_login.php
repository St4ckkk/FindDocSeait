<?php
require 'vendor/autoload.php';
include_once './core/sessionController.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['authCode'])) {
        $email = $_POST['email'];
        $authCode = $_POST['authCode'];

        $controller = new sessionController();
        $result = $controller->verifyGoogleAuthenticatorLogin($email, $authCode);

        if ($result['status'] === 'success') {
            $response = ['status' => 'success', 'message' => 'Google Authenticator verified successfully'];
        } else {
            $response = ['status' => 'error', 'message' => 'Invalid Google Authenticator code'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Missing parameters'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>