<?php
session_start();
include('../includes/config.php');
include('includes/checklogin.php');
check_admin_login();

// Handle report generation in separate page
if (isset($_GET['report_type'])) {
    $report_type = $_GET['report_type'];
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
    
    // Get payment data based on report type
    $where = "WHERE p.status = 'success' AND DATE(p.payment_date) BETWEEN '$date_from' AND '$date_to'";
    
    $query = "SELECT p.*, a.student_name, a.reg_no, a.course, u.name as user_name, u.email as user_email,
        r.receipt_number
        FROM payments p
        LEFT JOIN admission a ON p.admission_id = a.id
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN receipts r ON p.id = r.payment_id
        $where
        ORDER BY p.payment_date DESC";
    
    $result = $mysqli->query($query);
    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    
    // Generate report content
    require_once('../vendor/autoload.php');
    
    // Use DOMPDF if available, otherwise use mPDF
    $use_dompdf = false;
    
    if (class_exists('Dompdf\Dompdf')) {
        $dompdf = new \Dompdf\Dompdf();
        $use_dompdf = true;
    } else if (class_exists('\Mpdf\Mpdf')) {
        $mpdf = new \Mpdf\Mpdf([
            'margin_top' => 30,
            'margin_bottom' => 25,
            'margin_left' => 20,
            'margin_right' => 20
        ]);
    } else {
        // Fallback to simple HTML table if no PDF library available
        $use_html_table = true;
    }
    
    // Header
    $header = '
    <div style="text-align: center; border-bottom: 2px solid #1e3c72; padding-bottom: 10px;">
        <h2 style="color: #1e3c72; margin: 0;">' . SITE_NAME . '</h2>
        <h3 style="color: #2a5298; margin: 5px 0;">Payment Report</h3>
        <p style="margin: 0; color: #666;">';
    
    if ($report_type === 'daily') {
        $header .= 'Daily Report - ' . date('d M Y', strtotime($date_from));
    } elseif ($report_type === 'monthly') {
        $header .= 'Monthly Report - ' . date('F Y', strtotime($date_from));
    } else {
        $header .= 'Custom Report - ' . date('d M Y', strtotime($date_from)) . ' to ' . date('d M Y', strtotime($date_to));
    }
    
    $header .= '</p>
    </div>';
    
    // Set headers for mPDF only
    if (!$use_dompdf && !isset($use_html_table)) {
        $mpdf->SetHeader($header);
        
        // Footer
        $footer = '
        <div style="text-align: center; border-top: 1px solid #ddd; padding-top: 5px; font-size: 10px; color: #666;">
            Page {PAGENO} of {nbpg} | Generated on ' . date('d M Y H:i:s') . '
        </div>';
        
        $mpdf->SetFooter($footer);
    }
    
    // Content
    $html = '
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #1e3c72;
        }
    </style>';
    
    // Summary
    $total_amount = 0;
    $total_transactions = count($payments);
    
    foreach ($payments as $payment) {
        $total_amount += $payment['amount'];
    }
    
    $html .= '
    <div class="summary">
        <h4>Summary</h4>
        <p><strong>Total Transactions:</strong> ' . $total_transactions . '</p>
        <p><strong>Total Amount Collected:</strong> ₹' . number_format($total_amount / 100, 2) . '</p>
        <p><strong>Report Period:</strong> ' . date('d M Y', strtotime($date_from)) . ' to ' . date('d M Y', strtotime($date_to)) . '</p>
    </div>';
    
    // Table
    $html .= '
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student Name</th>
                <th>Register No.</th>
                <th>Course</th>
                <th>Payment Type</th>
                <th>Amount (₹)</th>
                <th>Payment ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($payments as $payment) {
        $payment_types = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
        $inst_num = isset($payment['instalment_number']) ? intval($payment['instalment_number']) : 0;
        $payment_type = isset($payment_types[$inst_num]) ? $payment_types[$inst_num] : 'Payment ' . $inst_num;
        
        $html .= '
            <tr>
                <td>' . date('d M Y H:i', strtotime($payment['payment_date'])) . '</td>
                <td>' . htmlspecialchars($payment['student_name'] ?: $payment['user_name']) . '</td>
                <td>' . htmlspecialchars($payment['reg_no'] ?: 'N/A') . '</td>
                <td>' . htmlspecialchars($payment['course'] ?: 'N/A') . '</td>
                <td>' . $payment_type . '</td>
                <td>' . number_format($payment['amount'] / 100, 2) . '</td>
                <td>' . htmlspecialchars($payment['razorpay_payment_id'] ?: 'N/A') . '</td>
                <td>' . strtoupper($payment['status']) . '</td>
            </tr>';
    }
    
    $html .= '
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5"><strong>Total</strong></td>
                <td><strong>₹' . number_format($total_amount / 100, 2) . '</strong></td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>';
    
    // Output PDF using appropriate library
    if ($use_dompdf && isset($dompdf)) {
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $filename = 'payment_report_' . $report_type . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $dompdf->stream($filename, array("Attachment" => false));
    } else if (!isset($use_html_table)) {
        // Use mPDF
        $mpdf->WriteHTML($html);
        $filename = 'payment_report_' . $report_type . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $mpdf->Output($filename, 'D');
    } else {
        // Fallback: Display as HTML table
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Payment Report</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Poppins', sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 40px 20px;
                }
                
                .report-header {
                    text-align: center;
                    color: white;
                    margin-bottom: 40px;
                }
                
                .report-header h1 {
                    font-size: 2.5rem;
                    font-weight: 700;
                    margin-bottom: 10px;
                }
                
                .report-header p {
                    font-size: 1.1rem;
                    opacity: 0.95;
                }
                
                .report-container {
                    background: white;
                    padding: 40px;
                    border-radius: 15px;
                    max-width: 1200px;
                    margin: 0 auto;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                }
                
                .action-buttons {
                    display: flex;
                    gap: 10px;
                    margin-bottom: 30px;
                    flex-wrap: wrap;
                }
                
                .btn-action {
                    padding: 10px 20px;
                    border-radius: 8px;
                    border: none;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 14px;
                }
                
                .btn-print {
                    background: #1e3c72;
                    color: white;
                }
                
                .btn-print:hover {
                    background: #162a52;
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
                }
                
                .summary {
                    margin: 30px 0;
                    padding: 25px;
                    background: white;
                    border: 2px solid #667eea;
                    border-radius: 12px;
                    color: #333;
                }
                
                .summary h4 {
                    font-weight: 700;
                    margin-bottom: 15px;
                    font-size: 1.3rem;
                    color: #1e3c72;
                }
                
                .summary-item {
                    margin: 10px 0;
                    font-size: 1rem;
                    color: #666;
                }
                
                .summary-item strong {
                    display: block;
                    color: #1e3c72;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                
                th, td {
                    padding: 12px 15px;
                    text-align: left;
                    border-bottom: 1px solid #e0e0e0;
                }
                
                th {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    font-weight: 600;
                }
                
                tbody tr:hover {
                    background-color: #f8f9fa;
                }
                
                .total-row {
                    font-weight: 600;
                    background: #f8f9fa;
                }
                
                .total-row td {
                    border-top: 2px solid #667eea;
                }
                
                .logos-section {
                    display: none;
                    justify-content: center;
                    align-items: center;
                    gap: 40px;
                    margin-bottom: 30px;
                    padding-bottom: 30px;
                    border-bottom: 2px solid #e0e0e0;
                }
                
                .logo-item {
                    text-align: center;
                }
                
                .logo-item img {
                    width: 150px;
                    height: 150px;
                    object-fit: contain;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    border-radius: 10px;
                    background: #f8f9fa;
                    padding: 10px;
                    transition: transform 0.3s ease;
                }
                
                .logo-item img:hover {
                    transform: scale(1.05);
                }
                
                @media print {
                    body { 
                        background: white; 
                        padding: 0;
                    }
                    .report-header {
                        color: #1e3c72;
                    }
                    .report-container {
                        box-shadow: none;
                        margin: 0;
                        padding: 0;
                    }
                    .action-buttons {
                        display: none;
                    }
                    .logos-section {
                        display: flex !important;
                    }
                }
                
                @media (max-width: 768px) {
                    .report-container {
                        padding: 20px;
                    }
                    
                    table {
                        font-size: 12px;
                    }
                    
                    th, td {
                        padding: 8px 10px;
                    }
                    
                    .logos-section {
                        gap: 20px;
                    }
                    
                    .logo-item img {
                        width: 100px;
                        height: 100px;
                    }
                }
                
                @media print {
                    .logos-section {
                        display: flex !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class="report-header">
                <h1><i class="fas fa-file-chart-line"></i> Payment Report</h1>
                <p><?php echo SITE_NAME; ?></p>
            </div>
            
            <div class="report-container">
                <div class="logos-section">
                    <div class="logo-item">
                        <img src="../images/aac.png" alt="AAC Logo">
                    </div>
                    <div class="logo-item">
                        <img src="../images/aac_hostel.jpeg" alt="IHS Logo">
                    </div>
                </div>
                
                <div class="action-buttons">
                    <button onclick="window.print()" class="btn-action btn-print">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
                
                <?php echo $html; ?>
            </div>
        </body>
        </html>
        <?php
    }
    exit();
}
?>
