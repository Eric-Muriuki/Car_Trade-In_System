<?php
// Include the main TCPDF library (adjust the path if necessary)
require_once __DIR__ . '/libs/tcpdf/tcpdf.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('YourAppName');
$pdf->SetAuthor('Your Name or Company');
$pdf->SetTitle('Sample PDF Report');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// Set default header data
$pdf->SetHeaderData('', 0, 'Sample TCPDF Document', 'Generated using TCPDF in PHP');

// Set header and footer fonts
$pdf->setHeaderFont(['helvetica', '', 10]);
$pdf->setFooterFont(['helvetica', '', 8]);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// Set margins (left, top, right)
$pdf->SetMargins(15, 27, 15);

// Set header and footer margins
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Set image scale factor
$pdf->setImageScale(1.25);

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add a page
$pdf->AddPage();

// Add content: Title
$pdf->Write(0, 'Hello, this is a sample PDF document created using TCPDF.', '', 0, 'L', true, 0, false, false, 0);

// Add some more text
$html = <<<EOD
<h2>Welcome to TCPDF!</h2>
<p>This is an example of how to generate PDF files with PHP using the TCPDF library.</p>
<ul>
    <li>Supports UTF-8</li>
    <li>Allows multiple pages</li>
    <li>Includes images, tables, barcodes, and more</li>
</ul>
<p>You can customize this template as per your requirements.</p>
EOD;

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document to browser
$pdf->Output('sample_report.pdf', 'I'); // 'I' = inline in browser, 'D' = force download

