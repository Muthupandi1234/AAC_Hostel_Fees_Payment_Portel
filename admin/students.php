<?php
session_start();
include('../includes/config.php');
include('includes/checklogin.php');
check_admin_login();

// Get all students with admission details
$students = [];
$result = $mysqli->query("SELECT a.*, u.name as user_name, u.email as user_email, u.phone as user_phone,
    (SELECT COUNT(*) FROM payments WHERE user_id = a.user_id AND status = 'success') as paid_instalments,
    (SELECT SUM(amount) FROM payments WHERE user_id = a.user_id AND status = 'success') as total_paid
    FROM admission a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC");
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - Admin - <?php echo SITE_NAME; ?></title>
    
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
        
        /* ===================================
   MOBILE OPTIMIZED STUDENT PAGE
===================================*/
@media (max-width: 768px) {

    body {
        font-size: 14px;
    }

    /* Header stacked */
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

    /* Main content padding reduce */
    .main-content {
        margin-left: 0;
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
        min-width: 850px; /* force horizontal scroll */
    }

    .table th,
    .table td {
        padding: 8px;
    }

    /* Badge compact */
    .badge {
        font-size: 11px;
        padding: 4px 6px;
    }

    /* Smooth scroll */
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
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <h1><i class="fas fa-users"></i> Student List</h1>
            <div style="flex: 0 0 300px;">
                <input type="text" id="searchTable" class="form-control" placeholder="Search students..." style="border-radius: 8px; padding: 10px 15px; border: 1px solid #ddd;">
            </div>
        </div>
        
        <div class="content-card">
            <h3><i class="fas fa-list"></i> All Students</h3>
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Register No.</th>
                            <th>Course</th>
                            <th>Department</th>
                            <th>Room No.</th>
                            <th>Paid Instalments</th>
                            <th>Total Paid</th>
                            <th>Date of Joining</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted">No students found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($student['student_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student['reg_no']); ?></td>
                                    <td><?php echo htmlspecialchars($student['course']); ?></td>
                                    <td><?php echo htmlspecialchars($student['department']); ?></td>
                                    <td><?php echo htmlspecialchars($student['room_number'] ?: 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $student['paid_instalments']; ?>/3</span>
                                    </td>
                                    <td><strong>₹<?php echo number_format(($student['total_paid'] ?: 0) / 100, 2); ?></strong></td>
                                    <td><?php echo date('d M Y', strtotime($student['date_of_joining'])); ?></td>
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


