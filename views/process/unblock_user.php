<?php

include_once '../../core/VirtualIPManager.php';
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['user_id'])) {
            $user_id = $_POST['user_id'];
            $virtualIPManager = new VirtualIPManager();

            $virtual_ip = $virtualIPManager->getVirtualIP($user_id);
            error_log("Virtual IP: " . $virtual_ip);

            if ($virtual_ip) {
                $unblockResult = $virtualIPManager->unblockVirtualIP($virtual_ip);
                $resetResult = $virtualIPManager->resetLoginAttempts($user_id);

                error_log("Unblock Result: " . ($unblockResult ? 'Success' : 'Failure'));
                error_log("Reset Login Attempts Result: " . ($resetResult ? 'Success' : 'Failure'));

                if ($unblockResult && $resetResult) {
                    $response = ['status' => 'success'];
                } else {
                    $response['message'] = 'Failed to unblock the user';
                }
            } else {
                $response['message'] = 'Virtual IP not found';
            }
        } else {
            $response['message'] = 'Invalid request';
        }
    } else {
        $response['message'] = 'Invalid request method';
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>