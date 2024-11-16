<?php
require '../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

if (isset($_GET['path'])) {
    $filePath = $_GET['path'];
    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

    if ($fileType === 'pdf') {
        // Directly display PDF files
        header('Content-type: application/pdf');
        readfile($filePath);
    } elseif ($fileType === 'docx') {
        // Load and convert Word documents to HTML
        try {
            $phpWord = IOFactory::load($filePath);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            $htmlContent = '';
            ob_start();
            $htmlWriter->save('php://output');
            $htmlContent = ob_get_clean();
            echo $htmlContent;
        } catch (Exception $e) {
            echo 'Error loading file: ' . $e->getMessage();
        }
    } else {
        echo 'Unsupported file type.';
    }
} else {
    echo 'No document path provided.';
}
?>