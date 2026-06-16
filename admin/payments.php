<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/config.php');
include('includes/checklogin.php');
check_admin_login();

// Get all payments
$payments = [];
$result = $mysqli->query("SELECT p.*, u.name as user_name, a.student_name, a.reg_no 
    FROM payments p 
    LEFT JOIN users u ON p.user_id = u.id 
    LEFT JOIN admission a ON p.admission_id = a.id 
    ORDER BY p.created_at DESC");
while ($row = $result->fetch_assoc()) {
    $payments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Admin - <?php echo SITE_NAME; ?></title>
    
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
        
        .status-badge.failed {
            background: #ffebee;
            color: #f44336;
        }
        
        /* ===================================
   MOBILE OPTIMIZED PAYMENTS PAGE
===================================*/
@media (max-width: 768px) {

    body {
        font-size: 14px;
    }

    /* Header stack */
    .header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 15px;
        padding: 15px;
    }

    .header h1 {
        font-size: 20px;
    }

    .header div {
        width: 100% !important;
    }

    #searchTable {
        width: 100%;
        font-size: 14px;
        padding: 8px 12px;
    }

    /* Main content spacing */
    .main-content {
        padding: 15px;
    }

    /* Card compact */
    .content-card {
        padding: 15px;
        border-radius: 12px;
    }

    .content-card h3 {
        font-size: 16px;
    }

    /* Table improvements */
    .table {
        font-size: 13px;
        min-width: 950px; /* force horizontal scroll */
    }

    .table th,
    .table td {
        padding: 8px;
        white-space: nowrap;
    }

    /* Status badge smaller */
    .status-badge {
        font-size: 11px;
        padding: 4px 8px;
    }

    /* Smooth horizontal scroll */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Hide scrollbar */
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
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <h1><i class="fas fa-credit-card"></i> Payment History</h1>
            <div style="flex: 0 0 300px;">
                <input type="text" id="searchTable" class="form-control" placeholder="Search payments..." style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
            </div>
        </div>
        
        <div class="content-card">
            <h3><i class="fas fa-list"></i> All Payments</h3>
            <div class="table-responsive">
                <table class="table" id="dataTable">
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
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No payments found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $index => $payment): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($payment['student_name'] ?: $payment['user_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($payment['reg_no'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                        $instalment_names = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
                                        $inst_num = isset($payment['instalment_number']) ? intval($payment['instalment_number']) : 0;
                                        echo isset($instalment_names[$inst_num]) ? $instalment_names[$inst_num] : 'Payment ' . $inst_num; 
                                        ?>
                                    </td>
                                    <td><strong>₹<?php echo number_format($payment['amount'] / 100, 2); ?></strong></td>
                                    <td><small style="color: #6c757d;"><?php echo htmlspecialchars($payment['razorpay_payment_id'] ?: 'N/A'); ?></small></td>
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
    document.getElementById('searchTable').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const table = document.getElementById('dataTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        Array.from(rows).forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
    </script>
</body>
</html>


