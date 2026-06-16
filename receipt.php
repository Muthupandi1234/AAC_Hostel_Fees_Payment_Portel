<?php
require_once('../tcpdf/tcpdf.php');
include('includes/config.php');


if (!isset($_GET['admission_id'])) {
    die("Invalid access");
}

$admission_id = intval($_GET['admission_id']);

// Fetch admission details
$result = $mysqli->query("
    SELECT * FROM new_admission
    WHERE id = $admission_id AND payment_status='PAID'
");

$data = $result->fetch_assoc();

if (!$data) {
    die("Receipt not found");
}

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AAC Hostel');
$pdf->SetTitle('Admission Fee Receipt');
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Add college logos to top corners
$left_logo = 'images/aac.png';
$right_logo = 'images/aac_hostel.jpeg';

if (file_exists($left_logo)) {
    // Left corner logo
    $pdf->Image($left_logo, 15, 10, 35, 35);
}

if (file_exists($right_logo)) {
    // Right corner logo
    $pdf->Image($right_logo, 160, 10, 35, 35);
}

$html = '
<br><br><br>
<h2 align="center">AAC Hostel</h2>
<h4 align="center">Admission Fee Receipt</h4>
<hr>

<table cellpadding="6">
<tr><td><strong>Student Name</strong></td><td>'.$data['student_name'].'</td></tr>
<tr><td><strong>Department</strong></td><td>'.$data['department'].'</td></tr>
<tr><td><strong>Year</strong></td><td>'.$data['year'].'</td></tr>
<tr><td><strong>Phone</strong></td><td>'.$data['phone'].'</td></tr>
<tr><td><strong>Admission Year</strong></td><td>'.$data['admission_year'].'</td></tr>
<tr><td><strong>Amount Paid</strong></td><td>₹ '.$data['amount'].'</td></tr>
<tr><td><strong>Payment ID</strong></td><td>'.$data['razorpay_payment_id'].'</td></tr>
<tr><td><strong>Date</strong></td><td>'.$data['created_at'].'</td></tr>
</table>

<br><br>
<p align="center"><em>This is a system generated receipt.</em></p>
';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Admission_Receipt_'.$admission_id.'.pdf', 'D'); // D = Download
exit;
