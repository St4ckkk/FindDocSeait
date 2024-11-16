<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'core/documentController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tracking_number = $_POST['tracking_number'];
    $documentController = new documentController();
    $document = $documentController->getDocumentByTrackingNumber($tracking_number);

    if ($document) {
        // Fetch tracking logs
        $trackingLogs = $documentController->getTrackingLogsByTrackingNumber($tracking_number);

        // Add status-specific messages
        $statusMessages = [
            'submitted' => 'Document request submitted and being reviewed',
            'approved' => 'Document request reviewed and approved by the ' . (!empty($document['office_name']) ? $document['office_name'] : 'tae'),
            'processing' => 'Document preparation and verification in progress',
            'pending' => 'Awaiting final verification and signatures',
            'completed' => 'Document processing completed and ready for pickup'
        ];

        $document['status_message'] = $statusMessages[$document['status']] ?? 'Status not available';

        echo json_encode(['status' => 'success', 'document' => $document, 'tracking_logs' => $trackingLogs]);
    } else {
        error_log("Document not found for tracking number: " . $tracking_number, 0);
        echo json_encode(['status' => 'error', 'message' => 'Document not found']);
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD'], 0);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>