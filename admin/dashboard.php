<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/config.php');
include('includes/checklogin.php');
check_admin_login();

$admin_name = $_SESSION['admin_name'];

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : "";




// Get statistics
$stats = [];

// Total students
$result = $mysqli->query("SELECT COUNT(DISTINCT user_id) as total FROM admission");
$stats['total_students'] = $result->fetch_assoc()['total'];

// Total collected fees
$result = $mysqli->query("SELECT SUM(amount) as total FROM payments WHERE status = 'success'");
$row = $result->fetch_assoc();
$stats['total_collected'] = $row['total'] ? $row['total'] / 100 : 0; // Convert from paise to rupees

// Pending payments
$result = $mysqli->query("SELECT COUNT(*) as total FROM payments WHERE status = 'pending'");
$stats['pending_payments'] = $result->fetch_assoc()['total'];

// Today's payments
$today = date('Y-m-d');
$result = $mysqli->query("SELECT COUNT(*) as total, SUM(amount) as amount FROM payments WHERE status = 'success' AND DATE(created_at) = '$today'");
$row = $result->fetch_assoc();
$stats['today_payments_count'] = $row['total'];
$stats['today_payments_amount'] = $row['amount'] ? $row['amount'] / 100 : 0;

// Recent transactions
$recent_transactions = [];
$where = "";
if ($search !== "") {
    $safe = $mysqli->real_escape_string($search);
    $where = "WHERE 
        a.student_name LIKE '%$safe%' OR
        a.reg_no LIKE '%$safe%' OR
        p.razorpay_payment_id LIKE '%$safe%'";
}

$result = $mysqli->query("
    SELECT p.*, u.name as user_name, a.student_name, a.reg_no 
    FROM payments p 
    LEFT JOIN users u ON p.user_id = u.id 
    LEFT JOIN admission a ON p.admission_id = a.id 
    $where
    ORDER BY p.created_at DESC 
    LIMIT 10
");

while ($row = $result->fetch_assoc()) {
    $recent_transactions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    
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
            padding: 12px;
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
        }
        
        .status-badge {
            padding: 5px 12px;
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

        .logo-image {
            position: absolute;
            top: 70px;
            left: 40px;
            max-width: 110px;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            z-index: 2;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* ===============================
   MOBILE OPTIMIZED DASHBOARD
=================================*/
@media (max-width: 768px) {

    body {
        font-size: 14px;
    }

    /* Header stacked */
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 15px;
    }

    .header h1 {
        font-size: 20px;
    }

    /* Main content padding reduce */
    .main-content {
        margin-left: 0;
        padding: 15px;
    }

    /* Stats single column */
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .stat-card {
        padding: 18px;
        border-radius: 12px;
    }

    .stat-card h3 {
        font-size: 24px;
    }

    .stat-card p {
        font-size: 13px;
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        font-size: 18px;
        margin-bottom: 10px;
    }

    /* Content card spacing */
    .content-card {
        padding: 15px;
        border-radius: 12px;
    }

    .content-card h3 {
        font-size: 16px;
    }

    /* Search box compact */
    #searchInput {
        font-size: 14px;
        padding: 8px 12px;
    }

    /* Table improvements */
    .table {
        font-size: 13px;
        min-width: 700px;
    }

    .table th,
    .table td {
        padding: 8px;
    }

    .status-badge {
        font-size: 11px;
        padding: 4px 8px;
    }

    /* Smooth table scroll */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive::-webkit-scrollbar {
        display: none;
    }

    .table-responsive {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
}
    </style>
</head>
<body>

    <!-- Include Admin Sidebar -->
    <?php include('../includes/admin_sidebar.php'); ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div>
                <span style="color: #6c757d;">Welcome, <?php echo htmlspecialchars($admin_name); ?></span>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo $stats['total_students']; ?></h3>
                <p>Total Students</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-rupee-sign"></i>
                </div>
                <h3>₹<?php echo number_format($stats['total_collected'], 2); ?></h3>
                <p>Total Collected Fees</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <h3><?php echo $stats['pending_payments']; ?></h3>
                <p>Pending Payments</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3><?php echo $stats['today_payments_count']; ?></h3>
                <p>Today's Payments (₹<?php echo number_format($stats['today_payments_amount'], 2); ?>)</p>
            </div>
        </div>

        
        
        <!-- Recent Transactions -->
        <div class="content-card">
            <h3><i class="fas fa-history"></i> Recent Transactions</h3>

            <div style="margin-bottom: 20px;">
                <input type="text"
                   id="searchInput"
                   class="form-control"
                   placeholder="Type to search name / reg no / payment id"
                   autocomplete="off"
                   style="padding: 10px 15px; border-radius: 8px; border: 1px solid #ddd;">
            </div>

            <div class="table-responsive">
                <table class="table" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Register No.</th>
                            <th>Instalment</th>
                            <th>Amount</th>
                            <th>Payment ID</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="transactionTable">

                        
                        <?php if (empty($recent_transactions)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No transactions found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_transactions as $index => $transaction): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($transaction['student_name'] ?: $transaction['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['reg_no'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                        $instalment_names = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
                                        $inst_num = isset($transaction['instalment_number']) ? intval($transaction['instalment_number']) : 0;
                                        echo isset($instalment_names[$inst_num]) ? $instalment_names[$inst_num] : 'Payment ' . $inst_num; 
                                        ?>
                                    </td>
                                    <td><strong>₹<?php echo number_format($transaction['amount'] / 100, 2); ?></strong></td>
                                    <td><small style="color: #6c757d;"><?php echo htmlspecialchars($transaction['razorpay_payment_id'] ?: 'N/A'); ?></small></td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($transaction['created_at'])); ?></td>
                                    <td>
                                        <?php 
                                        $status = isset($transaction['status']) ? $transaction['status'] : 'pending';
                                        $status_class = ($status === 'success') ? 'success' : (($status === 'failed') ? 'failed' : 'pending');
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const table = document.getElementById('transactionsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    Array.from(rows).forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>


</body>
</html>


