<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php'; // Include necessary libraries

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Writer\Pdf\Mpdf;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["docxFile"])) {
    $docxFile = $_FILES["docxFile"];

    // Check if the uploaded file is a DOCX file
    if ($docxFile["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
        // Generate a unique name for the PDF file
        $pdfFileName = uniqid("converted_", true) . ".pdf";

        // Specify the path where you want to save the PDF on your server
        $pdfFilePath = "./files/" . $pdfFileName;

        // Move the uploaded DOCX file to a temporary location
        $tempDocxPath = $docxFile["tmp_name"];

        // Create a new PhpWord instance
        $phpWord = new PhpWord();

        // Configure PhpWord to use mPDF
        Settings::setPdfRendererName(Settings::PDF_RENDERER_MPDF);
        Settings::setPdfRendererPath('vendor/mpdf/mpdf/');

        // Load the DOCX content
        $phpWord = IOFactory::load($tempDocxPath);

        // Create a PDF writer with mPDF and save the PDF to the specified file path
        $mpdfConfig = [
            'tempDir' => sys_get_temp_dir(),
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'arial',
        ];

        $pdfWriter = new Mpdf($phpWord, $mpdfConfig);
        $pdfWriter->save($pdfFilePath);

        // Prepare a JSON response
        $response = [
            'success' => true,
            'pdfLink' => "<a href=\"$pdfFilePath\" target=\"_blank\" rel=\"noopener noreferrer\">Download your PDF</a>",
 // Provide the path to the generated PDF
        ];

        // Set the content type to JSON
        header('Content-Type: application/json');

        // Output the JSON response
        echo json_encode($response);
    } else {
        // Prepare a JSON response for an invalid file format
        $response = [
            'success' => false,
            'errorMessage' => 'Invalid file format. Please upload a DOCX file.',
        ];

        // Set the content type to JSON
        header('Content-Type: application/json');

        // Output the JSON response
        echo json_encode($response);
    }
} else {
    // Prepare a JSON response for an invalid request
    $response = [
        'success' => false,
        'errorMessage' => 'Invalid request.',
    ];

    // Set the content type to JSON
    header('Content-Type: application/json');

    // Output the JSON response
    echo json_encode($response);
}
?>
