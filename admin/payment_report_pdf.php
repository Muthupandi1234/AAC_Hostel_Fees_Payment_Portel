<?php
require_once('../includes/config.php');
require_once('../tcpdf/tcpdf.php');

$date_from = $_GET['from'] ?? date('Y-m-d');
$date_to   = $_GET['to'] ?? date('Y-m-d');
$term      = $_GET['term'] ?? '';

// ---------- UNION SQL ----------
$sql = "
SELECT * FROM (
    SELECT
        student_name,
        department,
        year,
        phone,
        admission_year,
        amount,
        razorpay_payment_id,
        created_at,
        'INDEX_ADMISSION' AS payment_type
    FROM index_admissions
    WHERE payment_status='PAID'
    AND DATE(created_at) BETWEEN '$date_from' AND '$date_to'

    UNION ALL

    SELECT
        student_name,
        department,
        year,
        phone,
        admission_year,
        amount,
        razorpay_payment_id,
        created_at,
        'LOGIN_ADMISSION' AS payment_type
    FROM login_admissions
    WHERE payment_status='PAID'
    AND DATE(created_at) BETWEEN '$date_from' AND '$date_to'

    UNION ALL

    SELECT
        student_name,
        department,
        year,
        phone,
        admission_year,
        amount,
        razorpay_payment_id,
        created_at,
        CONCAT('TERM_', term_number) AS payment_type
    FROM term_payments
    WHERE payment_status='PAID'
    AND DATE(created_at) BETWEEN '$date_from' AND '$date_to'
) AS all_payments
";

// ---------- TERM FILTER ----------
if ($term != '') {
    $sql .= " WHERE payment_type='$term'";
}

$sql .= " ORDER BY created_at DESC";

$result = $mysqli->query($sql);

// ---------- TCPDF ----------
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Hostel Fees Portal');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Payment Report');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Hostel Payment Report', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 8, "From: $date_from   To: $date_to", 0, 1, 'C');
$pdf->Ln(3);

// ---------- TABLE HEADER ----------
$pdf->SetFont('helvetica', 'B', 9);
$headers = ['#','Student','Dept','Year','Phone','Adm Year','Type','Amount','Payment ID','Date'];
$widths  = [8,35,25,15,25,20,30,20,45,25];

foreach ($headers as $i => $h) {
    $pdf->Cell($widths[$i], 8, $h, 1, 0, 'C');
}
$pdf->Ln();

// ---------- TABLE DATA ----------
$pdf->SetFont('helvetica', '', 9);
$i = 1;
$total = 0;

while ($row = $result->fetch_assoc()) {
    $pdf->Cell($widths[0], 8, $i++, 1);
    $pdf->Cell($widths[1], 8, $row['student_name'], 1);
    $pdf->Cell($widths[2], 8, $row['department'], 1);
    $pdf->Cell($widths[3], 8, $row['year'], 1);
    $pdf->Cell($widths[4], 8, $row['phone'], 1);
    $pdf->Cell($widths[5], 8, $row['admission_year'], 1);
    $pdf->Cell($widths[6], 8, $row['payment_type'], 1);
    $pdf->Cell($widths[7], 8, number_format($row['amount'],2), 1, 0, 'R');
    $pdf->Cell($widths[8], 8, $row['razorpay_payment_id'], 1);
    $pdf->Cell($widths[9], 8, date('d-m-Y', strtotime($row['created_at'])), 1);
    $pdf->Ln();
    $total += $row['amount'];
}

// ---------- TOTAL ----------
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(array_sum($widths)-45, 9, 'Total Amount', 1);
$pdf->Cell(45, 9, '₹ '.number_format($total,2), 1, 1, 'R');

$pdf->Output('payment_report.pdf', 'I');
exit;
