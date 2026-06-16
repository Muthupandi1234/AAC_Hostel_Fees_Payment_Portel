<?php
session_start();
include('../includes/config.php');
include('includes/checklogin.php');
check_admin_login();

// Get all successful payments with receipt info
$receipts = [];
$result = $mysqli->query("SELECT p.*, u.name as user_name, a.student_name, a.reg_no,
    (SELECT receipt_number FROM receipts WHERE payment_id = p.id LIMIT 1) as receipt_number
    FROM payments p 
    LEFT JOIN users u ON p.user_id = u.id 
    LEFT JOIN admission a ON p.admission_id = a.id 
    WHERE p.status = 'success'
    ORDER BY p.created_at DESC");
while ($row = $result->fetch_assoc()) {
    $receipts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts - Admin - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* ===================================
   GLOBAL STYLES
===================================*/
/* ===================================
   GLOBAL STYLES
===================================*/
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

.main-content {
    padding: 20px 30px;
}

/* Header */
.header {
    background: white;
    padding: 20px 30px;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.header h1 {
    font-size: 28px;
    font-weight: 600;
    color: #1e3c72;
}

/* Card */
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

/* Table */
.table {
    margin-bottom: 0;
    width: 100%;
    border-collapse: collapse;
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

/* Download Button */
.btn-receipt {
    background: #667eea;
    border: none;
    color: white;
    padding: 6px 15px;
    border-radius: 6px;
    font-size: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s;
}

.btn-receipt:hover {
    background: #5568d3;
    color: white;
}

/* Search Input */
#searchTable {
    border-radius: 8px;
    padding: 10px 15px;
    border: 1px solid #ddd;
    width: 100%;
}

/* Responsive Table Wrapper */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Hide scrollbar for clean look */
.table-responsive::-webkit-scrollbar {
    display: none;
}
.table-responsive {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* ===================================
   MOBILE VIEW – RECEIPTS PAGE
===================================*/
@media (max-width: 992px) {
    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
    }

    .header h1 {
        font-size: 22px;
    }

    .main-content {
        padding: 15px;
    }

    .content-card {
        padding: 15px;
    }

    .content-card h3 {
        font-size: 18px;
        margin-bottom: 15px;
    }

    .btn-receipt {
        padding: 8px 12px;
        font-size: 13px;
    }
}

@media (max-width: 768px) {
    .table th, .table td {
        white-space: nowrap; /* keep table horizontal */
        font-size: 13px;
        padding: 8px;
    }

    /* Make download button full-width */
    .btn-receipt {
        display: block;
        width: 100%;
        text-align: center;
        padding: 10px 0;
        margin: 8px 0;
        font-size: 14px;
    }

    #searchTable {
        width: 100%;
        margin-top: 5px;
    }
}

@media (max-width: 576px) {
    .header h1 {
        font-size: 18px;
    }

    .table th, .table td {
        font-size: 12px;
        padding: 6px;
    }

    .btn-receipt {
        font-size: 12px;
        padding: 8px 0;
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
            <h1><i class="fas fa-file-invoice"></i> Payment Receipts</h1>
            <div style="flex: 0 0 300px;">
                <input type="text" id="searchTable" class="form-control" placeholder="Search receipts..." style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
            </div>
        </div>
        
        <div class="content-card">
            <h3><i class="fas fa-list"></i> All Receipts</h3>
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
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($receipts)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No receipts found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($receipts as $index => $receipt): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($receipt['student_name'] ?: $receipt['user_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($receipt['reg_no'] ?: 'N/A'); ?></td>
                                    <td>
                                        <?php 
                                        $instalment_names = [0 => 'Admission Fee', 1 => '1st Term', 2 => '2nd Term', 3 => '3rd Term'];
                                        $inst_num = isset($receipt['instalment_number']) ? intval($receipt['instalment_number']) : 0;
                                        echo isset($instalment_names[$inst_num]) ? $instalment_names[$inst_num] : 'Payment ' . $inst_num; 
                                        ?>
                                    </td>
                                    <td><strong>₹<?php echo number_format($receipt['amount'] / 100, 2); ?></strong></td>
                                    <td><small style="color: #6c757d;"><?php echo htmlspecialchars($receipt['razorpay_payment_id'] ?: 'N/A'); ?></small></td>
                                    <td><?php echo date('d M Y', strtotime($receipt['created_at'])); ?></td>
                                    <td>
                                        <a href="../download-receipt.php?id=<?php echo $receipt['id']; ?>" class="btn-receipt" target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
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


