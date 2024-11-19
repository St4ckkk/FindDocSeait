<?php
include_once './core/sessionController.php';

header('Content-Type: application/json');

$sessionController = new sessionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

        // Validate email format
        if (!$email) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email format'
            ]);
            exit;
        }

        // Send passkey without CAPTCHA verification
        try {
            $result = $sessionController->sendPasskey($email);
            echo json_encode($result);
        } catch (Exception $e) {
            error_log('Error sending passkey: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to send passkey. Please try again later.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameters'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>