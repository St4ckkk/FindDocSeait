<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

try {
    if (!isset($_SESSION['authorized']) || !$_SESSION['authorized']) {
        if (!isset($_GET['path'])) {
            throw new Exception('No file path provided');
        }

        $filePath = $_GET['path'];

        // Construct the absolute path from the relative path stored in the database
        $absolutePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $filePath);

        // Debugging statement to check the constructed path
        error_log('Constructed file path: ' . $absolutePath);

        if ($absolutePath === false || !file_exists($absolutePath)) {
            throw new Exception('File not found: ' . $absolutePath);
        }

        if (!is_readable($absolutePath)) {
            throw new Exception('File not readable: ' . $absolutePath);
        }

        $watermarkText = 'CONFIDENTIAL';
        $pdf = new FPDI();

        try {
            $pageCount = $pdf->setSourceFile($absolutePath);
        } catch (Exception $e) {
            throw new Exception('Error loading PDF: ' . $e->getMessage());
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            $specs = $pdf->getTemplateSize($tplIdx);
            $pdf->AddPage($specs['h'] > $specs['w'] ? 'P' : 'L', array($specs['w'], $specs['h']));
            $pdf->useTemplate($tplIdx);
            $pdf->SetFont('Helvetica', 'B', 50);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetXY(($specs['w'] - 100) / 2, ($specs['h'] - 20) / 2);
            $pdf->_out('q');
            $pdf->_out('0.5 gs');
            $pdf->Cell(100, 20, $watermarkText, 0, 0, 'C');
            $pdf->_out('Q');
        }

        $pdf->Output('I', 'watermarked_document.pdf');
    } else {
        if (!isset($_GET['path'])) {
            throw new Exception('No file path provided');
        }

        $filePath = $_GET['path'];

        // Construct the absolute path from the relative path stored in the database
        $absolutePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $filePath);

        // Debugging statement to check the constructed path
        error_log('Constructed file path: ' . $absolutePath);

        if ($absolutePath === false || !file_exists($absolutePath)) {
            throw new Exception('File not found: ' . $absolutePath);
        }

        if (!is_readable($absolutePath)) {
            throw new Exception('File not readable: ' . $absolutePath);
        }

        header('Content-Type: application/pdf');
        readfile($absolutePath);
        exit;
    }
} catch (Exception $e) {
    header('Content-Type: text/html; charset=utf-8');
    die('Error: ' . htmlspecialchars($e->getMessage()));
}