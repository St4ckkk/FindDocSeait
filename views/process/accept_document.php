<?php
session_start(); // Ensure the session is started

include_once '../../core/documentController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../unauthorized.php');
        exit();
    }

    $documentController = new documentController();
    $document_id = $_POST['document_id'];
    $status = 'pending';



    $result = $documentController->acceptDocument($document_id, $status, $_SESSION['csrf_token']);
    echo json_encode($result);
}
?>