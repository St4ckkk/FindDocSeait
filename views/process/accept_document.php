<?php
session_start(); // Ensure the session is started

include_once '../../core/documentController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $documentController = new documentController();
    $document_id = $_POST['document_id'];
    $status = 'pending';
    $accepted_by = $_SESSION['user_id']; // Assuming user_id is stored in session

    $result = $documentController->acceptDocument($document_id, $status, $_SESSION['csrf_token'], $accepted_by);
    echo json_encode($result);
}
?>