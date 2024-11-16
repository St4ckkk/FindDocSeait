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
        $query = "INSERT INTO documents (submitted_by, office_id, document_type, details, purpose, recipient_office_id, document_path, status) 
                  VALUES (:submitted_by, :office_id, :document_type, :details, :purpose, :recipient_office_id, :document_path, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':submitted_by', $params['by']);
        $stmt->bindParam(':office_id', $params['office_id']);
        $stmt->bindParam(':document_type', $params['document_type']);
        $stmt->bindParam(':details', $params['details']);
        $stmt->bindParam(':purpose', $params['purpose']);
        $stmt->bindParam(':recipient_office_id', $params['to']);
        $stmt->bindParam(':document_path', $params['document_path']);
        $stmt->bindParam(':status', $params['status']);

        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to submit document'];
        }
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
}