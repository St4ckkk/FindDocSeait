<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../../core/documentController.php';

header('Content-Type: application/json');

session_start();

try {
    error_log('share_document.php: Script started');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log('share_document.php: POST request received');

        if (!isset($_POST['shareDocumentId']) || !isset($_POST['shareOffice'])) {
            error_log('share_document.php: Missing required POST parameters');
            echo json_encode(['status' => 'error', 'message' => 'Document ID and Office ID are required']);
            exit;
        }

        $document_id = $_POST['shareDocumentId'];
        $office_id = $_POST['shareOffice'];

        error_log('share_document.php: Document ID: ' . $document_id);
        error_log('share_document.php: Office ID: ' . $office_id);

        $documentController = new documentController();
        $result = $documentController->shareDocument($document_id, $office_id, $_SESSION['csrf_token']);

        if ($result['status'] === 'success') {
            error_log('share_document.php: Document shared successfully');
            echo json_encode(['status' => 'success']);
        } else {
            error_log('share_document.php: Error sharing document: ' . $result['message']);
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        }
    } else {
        error_log('share_document.php: Invalid request method');
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    }
} catch (Exception $e) {
    error_log('share_document.php: Exception: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred']);
}
?>