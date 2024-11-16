<?php
session_start();

include_once '../../core/documentController.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $documentController = new documentController();

    $params = [
        'by' => $_SESSION['user_id'], // Ensure this is the correct user ID
        'office_id' => $_SESSION['office_id'],
        'document_type' => $_POST['document_type'],
        'details' => $_POST['details'],
        'purpose' => $_POST['purpose'],
        'to' => $_POST['office'],
        'document_path' => '', // This will be updated after file upload
        'status' => 'submitted'
    ];

    // Handle file upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $targetDir = "../../uploads/";
        $fileName = basename($_FILES['document']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow only PDF files
        if ($fileType == 'pdf') {
            if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFilePath)) {
                $params['document_path'] = $targetFilePath;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload document']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Only PDF files are allowed']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No document uploaded or upload error']);
        exit();
    }

    $result = $documentController->submitDocument($params);
    echo json_encode($result);
}
?>