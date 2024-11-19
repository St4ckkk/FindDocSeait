<?php
require_once './core/sessionController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passkey = $_POST['passkey'] ?? '';

    if (!empty($passkey)) {
        $controller = new sessionController();
        $response = $controller->loginWithPasskey($passkey);

        echo json_encode($response);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Passkey is required']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>