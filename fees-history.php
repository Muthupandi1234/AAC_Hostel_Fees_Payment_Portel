<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get all payments for this user
$payments = [];
$stmt = $mysqli->prepare("SELECT p.*, a.student_name, a.reg_no 
    FROM payments p 
    LEFT JOIN admission a ON p.admission_id = a.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees History - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            color: #333;
        }
        

        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1e3c72;
            margin: 0;
        }
        
        .history-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .history-card h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f5f7fa;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: #f8f9fa;
        }
        
        .table thead th {
            font-weight: 600;
            color: #1e3c72;
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-badge.success {
            background: #e8f5e9;
            color: #4caf50;
        }
        
        .status-badge.pending {
            background: #fff3e0;
            color: #ff9800;
        }
        
        .status-badge.failed {
            background: #ffebee;
            color: #f44336;
        }
        
        .btn-receipt {
            background: #667eea;
            border: none;
            color: white;
            padding: 6px 15px;
            border-radius: 6px;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-receipt:hover {
            background: #5568d3;
            color: white;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {

    .main-content {
        margin-left: 0;
        padding: 15px;
    }

    .header {
        padding: 15px;
    }

    .header h1 {
        font-size: 20px;
    }

    .history-card {
        padding: 20px;
    }

    .table {
        font-size: 13px;
    }

    .table th,
    .table td {
        padding: 10px;
        white-space: nowrap;
    }

    .btn-receipt {
        padding: 5px 10px;
        font-size: 11px;
    }

}
    </style>
</head>
<body>
    <!-- Include User Sidebar -->
    <?php include('includes/user_sidebar.php'); ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-history"></i> Fees Payment History</h1>
        </div>
        
        <div class="history-card">
            <h3><i class="fas fa-list"></i> All Payments</h3>
            
            <?php if (empty($payments)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>No Payment History</h4>
                    <p>You haven't made any payments yet. <a href="fees-payment.php">Make your first payment</a></p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Instalment</th>
                                <th>Amount</th>
                                <th>Payment ID</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $index => $payment): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <strong>
                                            <?php 
                                            $instalment_names = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
                                            $inst_num = isset($payment['instalment_number']) ? intval($payment['instalment_number']) : 0;
                                            echo isset($instalment_names[$inst_num]) ? $instalment_names[$inst_num] : 'Payment ' . $inst_num; 
                                            ?>
                                        </strong>
                                    </td>
                                    <td><strong>₹<?php echo number_format($payment['amount'] / 100, 2); ?></strong></td>
                                    <td>
                                        <small style="color: #6c757d;">
                                            <?php echo htmlspecialchars($payment['razorpay_payment_id'] ?: 'N/A'); ?>
                                        </small>
                                    </td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($payment['created_at'])); ?></td>
                                    <td>
                                        <?php 
                                        $status = isset($payment['status']) ? $payment['status'] : 'pending';
                                        $status_class = ($status === 'success') ? 'success' : (($status === 'failed') ? 'failed' : 'pending');
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($status === 'success'): ?>
                                            <?php if ($inst_num == 0): ?>
                                                <a href="download-receipt.php?id=<?php echo $payment['id']; ?>" class="btn-receipt" target="_blank" style="background: #28a745;">
                                                    <i class="fas fa-download"></i> Admission Receipt
                                                </a>
                                            <?php else: ?>
                                                <a href="download-receipt.php?id=<?php echo $payment['id']; ?>" class="btn-receipt" target="_blank">
                                                    <i class="fas fa-download"></i> <?php echo $instalment_names[$inst_num]; ?> Receipt
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


