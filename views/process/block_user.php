<?php
include_once '../../core/VirtualIPManager.php';

ob_start(); // Start output buffering

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ip_address']) && isset($_POST['user_id'])) {
        $ip_address = $_POST['ip_address'];
        $user_id = $_POST['user_id'];
        $virtualIPManager = new VirtualIPManager();

        if ($virtualIPManager->blockVirtualIP($ip_address)) {
            $response = ['status' => 'success'];
        } else {
            $response['message'] = 'Failed to block the user';
        }
    } else {
        $response['message'] = 'Invalid request';
    }
} else {
    $response['message'] = 'Invalid request method';
}

ob_end_clean(); // Clean (erase) the output buffer and turn off output buffering

header('Content-Type: application/json');
echo json_encode($response);
?>