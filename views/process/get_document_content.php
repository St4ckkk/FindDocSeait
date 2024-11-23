<?php
require '../../vendor/autoload.php';
include_once '../../core/documentController.php';

session_start();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $documentId = $_GET['id'];
    $documentController = new documentController();
    $documentPath = $documentController->getDocumentPathById($documentId);
    $fileType = pathinfo($documentPath, PATHINFO_EXTENSION);

    if ($fileType === 'pdf') {
        try {
            $docxPath = $documentPath . '.docx';
            $venvPath = '../../.venv'; // Update this to the path of your virtual environment
            $pythonScriptPath = '../../.venv/convert_pdf_to_word.py'; // Update this to the path of your Python script

            // Command to activate the virtual environment and run the Python script
            $command = "source $venvPath/Scripts/activate && python $pythonScriptPath " . escapeshellarg($documentPath) . " " . escapeshellarg($docxPath);

            // Debugging information
            error_log("Executing command: $command");
            error_log("Document path: $documentPath");
            error_log("Docx path: $docxPath");
            error_log("Virtual environment path: $venvPath");
            error_log("Python script path: $pythonScriptPath");

            exec($command . ' 2>&1', $output, $return_var); // Capture both stdout and stderr

            // Debugging information
            error_log("Command output: " . implode("\n", $output));
            error_log("Command return value: $return_var");

            if ($return_var !== 0) {
                throw new Exception("Failed to convert PDF to Word. Command output: " . implode("\n", $output));
            }

            $htmlContent = file_get_contents($docxPath);
            if ($htmlContent === false) {
                throw new Exception("Failed to read Word file.");
            }

            echo json_encode(['status' => 'success', 'content' => $htmlContent]);
        } catch (Exception $e) {
            error_log('Error processing PDF file: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Error processing PDF file: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unsupported file type.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No document ID provided.']);
}
?>