<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

// Check for payment success message
$payment_success = isset($_GET['payment']) && $_GET['payment'] === 'success';

// Get user's admission details
$admission = null;
$stmt = $mysqli->prepare("SELECT * FROM admission WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $admission = $result->fetch_assoc();
}
$stmt->close();

// Get recent payments
$recent_payments = [];
$stmt = $mysqli->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recent_payments[] = $row;
}
$stmt->close();

// Get payment statistics
$total_payments = 0;
$successful_payments = 0;
$stmt = $mysqli->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success FROM payments WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $total_payments = $row['total'];
    $successful_payments = $row['success'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1e3c72;
            margin: 0;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info span {
            color: #6c757d;
            font-size: 14px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-icon.blue {
            background: #e3f2fd;
            color: #2196f3;
        }
        
        .stat-icon.green {
            background: #e8f5e9;
            color: #4caf50;
        }
        
        .stat-icon.orange {
            background: #fff3e0;
            color: #ff9800;
        }
        
        .stat-icon.purple {
            background: #f3e5f5;
            color: #9c27b0;
        }
        
        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            color: #1e3c72;
            margin: 0;
        }
        
        .stat-card p {
            color: #6c757d;
            font-size: 14px;
            margin: 5px 0 0;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        
        .content-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .content-card h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f5f7fa;
        }
        
        .payment-item {
            padding: 15px 0;
            border-bottom: 1px solid #f5f7fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .payment-item:last-child {
            border-bottom: none;
        }
        
        .payment-info h5 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .payment-info p {
            font-size: 12px;
            color: #6c757d;
            margin: 5px 0 0;
        }
        
        .payment-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .payment-status.success {
            background: #e8f5e9;
            color: #4caf50;
        }
        
        .payment-status.failed {
            background: #ffebee;
            color: #f44336;
        }
        
        .payment-status.pending {
            background: #fff3e0;
            color: #ff9800;
        }
        
        .quick-actions {
            list-style: none;
            padding: 0;
        }
        
        .quick-actions li {
            margin-bottom: 10px;
        }
        
        .quick-actions a {
            display: block;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 10px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .quick-actions a:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        
        @media (max-width: 768px) {

    .content-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .header h1 {
        font-size: 22px;
    }

    .stat-card {
        padding: 18px;
    }

    .stat-card h3 {
        font-size: 24px;
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
            <h1>Dashboard</h1>
            <div class="user-info">
                <span>Hi, <?php echo htmlspecialchars($user_name); ?></span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
            </div>
        </div>
        
        <?php 
        $payment_success = isset($_GET['payment']) && $_GET['payment'] === 'success';
        $payment_type = isset($_GET['type']) ? $_GET['type'] : '';
        if ($payment_success): 
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; margin-bottom: 20px; font-size: 16px;">
                <i class="fas fa-check-circle"></i> <strong>Payment Successful!</strong> 
                <?php if ($payment_type === 'admission'): ?>
                    Your admission fee has been paid successfully. You can now proceed to pay hostel fees.
                <?php else: ?>
                    Your payment has been processed successfully. Receipt is available for download in Fees History.
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3><?php echo $successful_payments; ?></h3>
                <p>Successful Payments</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h3><?php echo $total_payments; ?></h3>
                <p>Total Payments</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <h3>₹<?php 
                    $total_paid = 0;
                    $stmt = $mysqli->prepare("SELECT SUM(amount) as total FROM payments WHERE user_id = ? AND status = 'success'");
                    $stmt->bind_param('i', $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    if ($row && isset($row['total']) && $row['total'] !== null) {
                        $total_paid = $row['total'] / 100;
                    }
                    $stmt->close();
                    echo number_format($total_paid, 2);
                ?></h3>
                <p>Total Paid</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3><?php echo $admission ? 'Complete' : 'Pending'; ?></h3>
                <p>Admission</p>
            </div>
        </div>
        
        <!-- Payment Process Info -->
        <div class="content-card" style="margin-bottom: 30px;">
            <h3><i class="fas fa-info-circle"></i> Online Payment Process</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color: #1e3c72; margin-bottom: 15px;"><i class="fas fa-list-ol"></i> Payment Steps:</h5>
                    <ol style="line-height: 2;">
                        <li>Complete your admission form</li>
                        <li>Pay fees in 3 instalments sequentially</li>
                        <li>1st Term: ₹15,000 (must be paid first)</li>
                        <li>2nd Term: ₹13,000 (unlocks after 1st payment)</li>
                        <li>3rd Term: ₹10,000 (unlocks after 2nd payment)</li>
                        <li>Download receipt after each successful payment</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h5 style="color: #1e3c72; margin-bottom: 15px;"><i class="fas fa-shield-alt"></i> Payment Security:</h5>
                    <ul style="line-height: 2;">
                        <li>All payments are processed securely through Razorpay</li>
                        <li>Your financial information is encrypted</li>
                        <li>Receipts are automatically generated after payment</li>
                        <li>Payment history is available in your dashboard</li>
                        <li>Support available for any payment issues</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <div class="content-card">
                <h3><i class="fas fa-history"></i> Recent Payments</h3>
                <?php if (empty($recent_payments)): ?>
                    <p style="color: #6c757d; text-align: center; padding: 20px;">No payments found</p>
                <?php else: ?>
                    <?php foreach ($recent_payments as $payment): ?>
                        <div class="payment-item">
                            <div class="payment-info">
                                <h5>Payment #<?php echo $payment['id']; ?></h5>
                                <p><?php echo date('d M Y, h:i A', strtotime($payment['created_at'])); ?></p>
                            </div>
                            <div>
                                <span class="payment-status <?php echo $payment['status']; ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                                <p style="text-align: right; margin-top: 5px; font-weight: 600; color: #1e3c72;">
                                    ₹<?php echo number_format($payment['amount'] / 100, 2); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="content-card">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                <ul class="quick-actions">
                    <?php if (!$admission): ?>
                    <li><a href="admission.php"><i class="fas fa-file-alt"></i> Complete Admission</a></li>
                    <?php endif; ?>
                    <li><a href="fees-payment.php"><i class="fas fa-credit-card"></i> Pay Fees</a></li>
                    <li><a href="fees-history.php"><i class="fas fa-history"></i> View Payment History</a></li>
                    <li><a href="my-profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
