<?php
include_once 'Database.php';

class documentController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function submitDocument($params)
    {
        $query = "INSERT INTO documents (submitted_by, office_id, document_type, details, purpose, recipient_office_id, document_path, status, tracking_number) 
                  VALUES (:submitted_by, :office_id, :document_type, :details, :purpose, :recipient_office_id, :document_path, :status, :tracking_number)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':submitted_by', $params['by']);
        $stmt->bindParam(':office_id', $params['office_id']);
        $stmt->bindParam(':document_type', $params['document_type']);
        $stmt->bindParam(':details', $params['details']);
        $stmt->bindParam(':purpose', $params['purpose']);
        $stmt->bindParam(':recipient_office_id', $params['to']);
        $stmt->bindParam(':document_path', $params['document_path']);
        $stmt->bindParam(':status', $params['status']);
        $stmt->bindParam(':tracking_number', $params['tracking_number']);

        if ($stmt->execute()) {
            $this->logDocumentChange($params['tracking_number'], 'Document Submitted', 'Document request received and logged in the system');
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to submit document'];
        }
    }

    public function acceptDocument($document_id, $status)
    {
        $query = "UPDATE documents SET status = :status WHERE id = :document_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':document_id', $document_id);

        if ($stmt->execute()) {
            $tracking_number = $this->getTrackingNumberById($document_id);
            $office_name = $this->getOfficeNameByDocumentId($document_id);
            $this->logDocumentChange($tracking_number, 'Request Approved', "Document request reviewed and approved by the $office_name");
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update document status'];
        }
    }

    private function getTrackingNumberById($document_id)
    {
        $query = "SELECT tracking_number FROM documents WHERE id = :document_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':document_id', $document_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['tracking_number'];
    }

    private function getOfficeNameByDocumentId($document_id)
    {
        $query = "SELECT offices.name AS office_name 
                  FROM documents 
                  JOIN offices ON documents.office_id = offices.office_id 
                  WHERE documents.id = :document_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':document_id', $document_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['office_name'];
    }

    private function logDocumentChange($tracking_number, $title, $message)
    {
        $query = "INSERT INTO tracking_logs (tracking_number, submitted_by, office_id, document_type, details, purpose, recipient_office_id, recipient_general_id, document_path, status, title, message, created_at, updated_at)
              SELECT tracking_number, submitted_by, office_id, document_type, details, purpose, recipient_office_id, recipient_general_id, document_path, status, :title, :message, created_at, updated_at
              FROM documents WHERE tracking_number = :tracking_number";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tracking_number', $tracking_number);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    }

    public function getDocumentByTrackingNumber($tracking_number)
    {
        $query = "SELECT documents.*, users.fullname AS submitted_by_name, offices.name AS office_name 
                  FROM documents 
                  JOIN users ON documents.submitted_by = users.id 
                  JOIN offices ON documents.office_id = offices.office_id 
                  WHERE documents.tracking_number = :tracking_number";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tracking_number', $tracking_number);
        $stmt->execute();

        $document = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debugging: Log or print the document array
        error_log(print_r($document, true));

        return $document;
    }

    public function getTrackingLogsByTrackingNumber($tracking_number)
    {
        $query = "SELECT tracking_logs.*, offices.name AS office_name 
                  FROM tracking_logs 
                  JOIN offices ON tracking_logs.office_id = offices.office_id 
                  WHERE tracking_logs.tracking_number = :tracking_number 
                  ORDER BY tracking_logs.created_at ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tracking_number', $tracking_number);
        $stmt->execute();

        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $logs;
    }

    public function getSubmittedDocuments($office_id)
    {
        $query = "SELECT documents.*, users.fullname AS submitted_by_name 
                  FROM documents 
                  JOIN users ON documents.submitted_by = users.id 
                  WHERE documents.office_id = :office_id AND documents.status = 'submitted'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':office_id', $office_id);
        $stmt->execute();

        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $documents;
    }

    public function getPendingDocuments($office_id)
    {
        $query = "SELECT documents.*, users.fullname AS submitted_by_name 
                  FROM documents 
                  JOIN users ON documents.submitted_by = users.id 
                  WHERE documents.office_id = :office_id AND documents.status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':office_id', $office_id);
        $stmt->execute();

        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $documents;
    }
}