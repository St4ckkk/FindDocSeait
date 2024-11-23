<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReader;
use setasign\Fpdi\PdfParser\StreamReader;

class PDF_Rotate extends Fpdi
{
    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) {
            $x = $this->x;
        }
        if ($y == -1) {
            $y = $this->y;
        }
        if ($this->angle != 0) {
            $this->_out('Q');
        }
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.5F %.5F cm 1 0 0 1 %.5F %.5F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
}

session_start();
if (isset($_GET['path'])) {
    error_log('Debug: Path is set');
    $filePath = $_GET['path'];
    $fileType = pathinfo($filePath, PATHINFO_EXTENSION);

    if ($fileType === 'pdf') {
        error_log('Debug: Processing PDF file');
        try {
            $pdf = new PDF_Rotate();
            $pageCount = $pdf->setSourceFile($filePath);
            error_log('Debug: Page count is ' . $pageCount);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

                if (isset($_SESSION['authorized'])) {
                    // Add centered watermark before the page content
                    $pdf->SetFont('Helvetica', 'B', 100);
                    $pdf->SetTextColor(200, 200, 200); // Light orange with alpha

                    // Calculate center position
                    $watermarkText = 'SEAIT';
                    $textWidth = $pdf->GetStringWidth($watermarkText);
                    $centerX = ($size['width'] - $textWidth) / 2;
                    $centerY = $size['height'] / 2;

                    // Add diagonal watermark text in center
                    $pdf->Rotate(45, $centerX + ($textWidth / 2), $centerY);
                    $pdf->Text($centerX, $centerY, $watermarkText);
                    $pdf->Rotate(0);

                    // Add centered logo watermark
                    // $logoPath = '../assets/img/seait-logo.png';
                    // $logoWidth = 100;
                    // $logoHeight = 100;
                    // $logoX = ($size['width'] - $logoWidth) / 2;
                    // $logoY = ($size['height'] - $logoHeight) / 2;
                    // $pdf->Image($logoPath, $logoX, $logoY, $logoWidth, $logoHeight);
                }

                // Now add the page content on top
                $pdf->useTemplate($templateId);

                // Add hidden steganographic logo
                $logoPath = '../assets/img/seait-logo.png';
                $miniLogoSize = 1;
                for ($x = 0; $x < $size['width']; $x += 100) {
                    for ($y = 0; $y < $size['height']; $y += 100) {
                        $pdf->Image($logoPath, $x, $y, $miniLogoSize, $miniLogoSize);
                    }
                }
            }

            // Output the PDF
            header('Content-type: application/pdf');
            $pdf->Output('I', 'preview.pdf');
        } catch (Exception $e) {
            error_log('Error processing PDF file: ' . $e->getMessage());
            echo 'Error processing PDF file: ' . $e->getMessage();
        }
    } elseif ($fileType === 'docx') {
        error_log('Debug: Processing DOCX file');
        try {
            $phpWord = IOFactory::load($filePath);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            ob_start();
            $htmlWriter->save('php://output');
            $htmlContent = ob_get_clean();
            echo $htmlContent;
        } catch (Exception $e) {
            error_log('Error loading file: ' . $e->getMessage());
            echo 'Error loading file: ' . $e->getMessage();
        }
    } else {
        error_log('Unsupported file type');
        echo 'Unsupported file type.';
    }
} else {
    error_log('No document path provided');
    echo 'No document path provided.';
}
?>