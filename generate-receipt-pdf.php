<?php
session_start();
include('includes/config.php');

// Check if user is logged in (either user or admin)
$is_user = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['admin_id']);

if (!$is_user && !$is_admin) {
    die('Unauthorized access');
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

// Generate receipt number
$receipt_number = 'RCP-' . date('Y') . '-' . str_pad($payment_id, 6, '0', STR_PAD_LEFT);

// Instalment names
$instalment_names = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
$inst_num = isset($payment['instalment_number']) ? intval($payment['instalment_number']) : 0;
$instalment_name = isset($instalment_names[$inst_num]) ? 
    $instalment_names[$inst_num] : 'Payment ' . $inst_num;

// Generate HTML receipt that can be printed to PDF
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
            background: #f5f5f5;
        }
        
        .print-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e3c72;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        
        .header-content {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }
        
        .header-content h1 {
            color: #1e3c72;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header-content p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .receipt-content {
            margin: 30px 0;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-table td:first-child {
            font-weight: 600;
            color: #333;
            width: 40%;
        }
        
        .info-table td:last-child {
            color: #666;
            text-align: right;
        }
        
        .amount-section {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            margin: 30px 0;
        }
        
        .amount-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .amount-value {
            font-size: 36px;
            font-weight: bold;
            color: #1e3c72;
        }
        
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            line-height: 1.6;
        }
        
        .status-badge {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            
            .print-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header with Logos -->
        <div class="header-section">
            <img src="images/aac.png" alt="AAC Logo" class="logo">
            <div class="header-content">
                <h1><?php echo SITE_NAME; ?></h1>
                <p>Payment Receipt</p>
            </div>
            <img src="images/aac_hostel.jpeg" alt="AAC Hostel Logo" class="logo">
        </div>
        
        <!-- Receipt Content -->
        <div class="receipt-content">
            <table class="info-table">
                <tr>
                    <td>Receipt Number:</td>
                    <td><strong><?php echo htmlspecialchars($receipt_number); ?></strong></td>
                </tr>
                <tr>
                    <td>Student Name:</td>
                    <td><?php echo htmlspecialchars($payment['student_name'] ?: $payment['user_name']); ?></td>
                </tr>
                <tr>
                    <td>Register Number:</td>
                    <td><?php echo htmlspecialchars($payment['reg_no'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Course:</td>
                    <td><?php echo htmlspecialchars($payment['course'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Department:</td>
                    <td><?php echo htmlspecialchars($payment['department'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Room Number:</td>
                    <td><?php echo htmlspecialchars($payment['room_number'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Payment Type:</td>
                    <td><strong><?php echo htmlspecialchars($instalment_name); ?></strong></td>
                </tr>
                <tr>
                    <td>Payment ID:</td>
                    <td><?php echo htmlspecialchars($payment['razorpay_payment_id'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Order ID:</td>
                    <td><?php echo htmlspecialchars($payment['razorpay_order_id'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td>Payment Date:</td>
                    <td><?php echo date('d M Y, h:i A', strtotime($payment['created_at'])); ?></td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td><span class="status-badge">Paid</span></td>
                </tr>
            </table>
        </div>
        
        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-label">Amount Paid</div>
            <div class="amount-value">₹<?php echo number_format($payment['amount'] / 100, 2); ?></div>
        </div>
        
        <!-- Footer -->
        <div class="footer-section">
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>Generated on <?php echo date('d M Y, h:i A'); ?></p>
            <p>For any queries, please contact the administration.</p>
        </div>
    </div>
    
    <script>
        // Trigger print dialog when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
