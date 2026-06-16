<?php
session_start();
include('includes/config.php');

// Check if user is logged in (either user or admin)
$is_user = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['admin_id']);

if (!$is_user && !$is_admin) {
    header("Location: index.php");
    exit();
}

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($payment_id <= 0) {
    die('Invalid payment ID');
}

// Get payment details
$stmt = $mysqli->prepare("SELECT p.*, u.name as user_name, u.email as user_email, 
    a.student_name, a.reg_no, a.course, a.department, a.room_number
    FROM payments p 
    LEFT JOIN users u ON p.user_id = u.id 
    LEFT JOIN admission a ON p.admission_id = a.id 
    WHERE p.id = ?");
$stmt->bind_param('i', $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('Payment not found');
}

$payment = $result->fetch_assoc();
$stmt->close();

// Check if user has permission to view this receipt
if ($is_user && $payment['user_id'] != $_SESSION['user_id']) {
    die('Unauthorized access');
}

// Only generate receipt for successful payments
if ($payment['status'] !== 'success') {
    die('Receipt can only be generated for successful payments');
}

// Check if receipt already exists (and if a PDF is already generated)
$stmt = $mysqli->prepare("SELECT id, receipt_number, pdf_path FROM receipts WHERE payment_id = ?");
$stmt->bind_param('i', $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$existing_receipt = $result->fetch_assoc();
$stmt->close();

// ---------------- RECEIPT TYPE LOGIC ----------------
$receipt_type = $existing_receipt['receipt_type'] ?? 'SINGLE';

$total_amount = $payment['amount']; // default
$instalment_name = '';
$instalment_list = [];

if ($receipt_type === 'ALL_TERMS') {

    // Fetch all instalments paid in this transaction
    $stmt = $mysqli->prepare("
        SELECT instalment_number, amount
        FROM payments
        WHERE razorpay_payment_id = ?
        ORDER BY instalment_number
    ");
    $stmt->bind_param('s', $payment['razorpay_payment_id']);
    $stmt->execute();
    $res = $stmt->get_result();

    $total_amount = 0;

    while ($row = $res->fetch_assoc()) {
        $total_amount += $row['amount'];

        if ($row['instalment_number'] == 1) $instalment_list[] = '1st Term';
        if ($row['instalment_number'] == 2) $instalment_list[] = '2nd Term';
        if ($row['instalment_number'] == 3) $instalment_list[] = '3rd Term';
    }
    $stmt->close();

    $instalment_name = 'All 3 Terms (' . implode(', ', $instalment_list) . ')';

} else {

    // Existing SINGLE logic
    $instalment_names = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
    $inst_num = intval($payment['instalment_number']);
    $instalment_name = $instalment_names[$inst_num] ?? 'Payment';
}


// Generate receipt number if not exists
if (!$existing_receipt) {
    $receipt_number = 'RCP-' . date('Y') . '-' . str_pad($payment_id, 6, '0', STR_PAD_LEFT);
    
    $stmt = $mysqli->prepare("INSERT INTO receipts (payment_id, user_id, admission_id, receipt_number) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiis', $payment_id, $payment['user_id'], $payment['admission_id'], $receipt_number);
    $stmt->execute();
    $receipt_id = $mysqli->insert_id;
    $stmt->close();
    $pdf_path = null;
} else {
    $receipt_number = $existing_receipt['receipt_number'];
    $receipt_id = $existing_receipt['id'];
    $pdf_path = $existing_receipt['pdf_path'] ?? null;
}

// If a PDF path exists and file is present, serve it as a download
if (!empty($pdf_path) && file_exists(__DIR__ . '/' . $pdf_path)) {
    $fullpath = __DIR__ . '/' . $pdf_path;
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($fullpath) . '"');
    header('Content-Length: ' . filesize($fullpath));
    readfile($fullpath);
    exit;
}

// Instalment names
$instalment_names = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
$inst_num = isset($payment['instalment_number']) ? intval($payment['instalment_number']) : 0;
$instalment_name = isset($instalment_names[$inst_num]) ? 
    $instalment_names[$inst_num] : 'Payment ' . $inst_num;

// Generate PDF using simple HTML to PDF approach
// For production, you might want to use a library like TCPDF or FPDF
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo $receipt_number; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #1e3c72;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .receipt-header h1 {
            color: #1e3c72;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .receipt-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .receipt-info {
            margin-bottom: 30px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
            width: 40%;
        }
        
        .info-value {
            color: #666;
            width: 60%;
            text-align: right;
        }
        
        .amount-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0;
            text-align: center;
        }
        
        .amount-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .amount-value {
            font-size: 36px;
            font-weight: 700;
            color: #1e3c72;
        }
        
        .receipt-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            background: #4caf50;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e3c72;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        
        .header-center {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }
        
        .header-center h1 {
            color: #1e3c72;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header-center p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .receipt-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-download-pdf {
            background: #1e3c72;
            color: white;
        }
        
        .btn-download-pdf:hover {
            background: #162a52;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .btn-print {
            background: #667eea;
            color: white;
        }
        
        .btn-print:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt-container {
                box-shadow: none;
            }
            
            .receipt-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header-logos">
            <img src="images/aac.png" alt="AAC Logo" class="logo">
            <div class="header-center">
                <h1><?php echo SITE_NAME; ?></h1>
                <p>Payment Receipt</p>
            </div>
            <img src="images/aac_hostel.jpeg" alt="AAC Hostel Logo" class="logo">
        </div>
        
        <div class="receipt-info">
            <div class="receipt-actions">
                <button class="btn-action btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
            </div>
            <div class="info-row">
                <span class="info-label">Receipt Number:</span>
                <span class="info-value"><strong><?php echo htmlspecialchars($receipt_number); ?></strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Student Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($payment['student_name'] ?: $payment['user_name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Register Number:</span>
                <span class="info-value"><?php echo htmlspecialchars($payment['reg_no'] ?: 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Course:</span>
                <span class="info-value"><?php echo htmlspecialchars($payment['course'] ?: 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Department:</span>
                <span class="info-value"><?php echo htmlspecialchars($payment['department'] ?: 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Room Number:</span>
                <span class="info-value"><?php echo htmlspecialchars($payment['room_number'] ?: 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Type:</span>
                <span class="info-value"><strong><?php echo htmlspecialchars($instalment_name); ?></strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment ID:</span>
                <span class="info-value"><?php echo htmlspecialchars($payment['razorpay_payment_id'] ?: 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Order ID:</span>
                <span class="info-value"><?php echo htmlspecialchars($payment['razorpay_order_id'] ?: 'N/A'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Date:</span>
                <span class="info-value"><?php echo date('d M Y, h:i A', strtotime($payment['created_at'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value"><span class="status-badge">Paid</span></span>
            </div>
        </div>
        
        <div class="amount-section">
            <div class="amount-label">Amount Paid</div>
            <div class="amount-value">₹<?php echo number_format($total_amount / 100, 2); ?></div>

        </div>
        
        <div class="receipt-footer">
    <p>This is a computer-generated receipt and does not require a signature.</p>

    <?php if ($receipt_type === 'ALL_TERMS'): ?>
        <p style="margin-top:8px;font-weight:600;color:#1e3c72;">
            This receipt covers 1st, 2nd and 3rd Term payments.
        </p>
    <?php endif; ?>

    <p style="margin-top: 10px;">Generated on <?php echo date('d M Y, h:i A'); ?></p>
    <p style="margin-top: 5px;">For any queries, please contact the administration.</p>
</div>


    </div>
    
    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>


