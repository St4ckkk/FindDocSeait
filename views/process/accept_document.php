<?php
include_once '../../core/documentController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documentController = new documentController();
    $document_id = $_POST['document_id'];
    $status = 'pending';

    $result = $documentController->acceptDocument($document_id, $status);
    echo json_encode($result);
}
?>