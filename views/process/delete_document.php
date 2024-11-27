<?php
include_once '../../core/documentController.php';
session_start();

if (!isset($_SESSION['csrf_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$documentController = new documentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $document_id = $_POST['document_id'];
    $csrfToken = $_SESSION['csrf_token'];

    $result = $documentController->deleteDocument($document_id, $csrfToken);

    echo json_encode($result);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>