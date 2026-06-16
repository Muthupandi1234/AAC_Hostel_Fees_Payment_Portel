<?php
session_start();
include('../includes/config.php');
include('includes/checklogin.php');
check_admin_login();

// Get today's and monthly stats for dashboard
$today = date('Y-m-d');
$month_start = date('Y-m-01');

// Today's payments
$today_result = $mysqli->query("SELECT COUNT(*) as count, SUM(amount) as total FROM payments WHERE status = 'success' AND DATE(payment_date) = '$today'");
$today_stats = $today_result->fetch_assoc();

// Monthly payments
$month_result = $mysqli->query("SELECT COUNT(*) as count, SUM(amount) as total FROM payments WHERE status = 'success' AND DATE(payment_date) >= '$month_start'");
$month_stats = $month_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reports - Admin - <?php echo SITE_NAME; ?></title>
    
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

/* Header */
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

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 12px;
}

.stat-icon.blue { background: #e3f2fd; color: #2196f3; }
.stat-icon.green { background: #e8f5e9; color: #4caf50; }
.stat-icon.orange { background: #fff3e0; color: #ff9800; }
.stat-icon.purple { background: #f3e5f5; color: #9c27b0; }

.stat-card h3 {
    font-size: 28px;
    font-weight: 700;
    color: #1e3c72;
    margin: 0;
}

.stat-card p {
    color: #6c757d;
    font-size: 14px;
    margin: 5px 0 0;
}

/* Content Cards */
.content-card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 25px;
}

.content-card h3 {
    font-size: 20px;
    font-weight: 600;
    color: #1e3c72;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f5f7fa;
}

/* Report Section */
.report-section {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s;
}

.report-section:hover {
    border-color: #1e3c72;
    box-shadow: 0 5px 15px rgba(30, 60, 114, 0.1);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.report-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e3c72;
}

.report-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.report-stat {
    text-align: center;
    flex: 1 1 100px;
}

.report-stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #1e3c72;
}

.report-stat-label {
    font-size: 12px;
    color: #6c757d;
    text-transform: uppercase;
}

/* Buttons */
.btn-download {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-download:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
    color: white;
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .report-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .report-stats {
        flex-direction: column;
        gap: 10px;
    }

    .btn-download {
        width: 100%;
        justify-content: center;
    }

    .content-card {
        padding: 15px;
    }

    .stat-card {
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .header h1 {
        font-size: 22px;
    }

    .stat-card h3 {
        font-size: 24px;
    }

    .report-stat-value {
        font-size: 18px;
    }

    .report-title {
        font-size: 15px;
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
            <h1><i class="fas fa-file-download"></i> Payment Reports</h1>
        </div>
        
        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3><?php echo $today_stats['count']; ?></h3>
                <p>Today's Transactions (₹<?php echo number_format(($today_stats['total'] ?: 0) / 100, 2); ?>)</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3><?php echo $month_stats['count']; ?></h3>
                <p>Monthly Transactions (₹<?php echo number_format(($month_stats['total'] ?: 0) / 100, 2); ?>)</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h3>PDF</h3>
                <p>Download Format Available</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Analytics</h3>
                <p>Comprehensive Reports</p>
            </div>
        </div>
        
        <div class="content-card">
            <h3><i class="fas fa-chart-bar"></i> Generate Reports</h3>
            
            <!-- Daily Report -->
            <div class="report-section">
                <div class="report-header">
                    <div class="report-title">
                        <i class="fas fa-calendar-day"></i> Daily Report
                    </div>
                    <a href="generate_report.php?report_type=daily&date_from=<?php echo $today; ?>&date_to=<?php echo $today; ?>" target="_blank" class="btn-download">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
                <div class="report-stats">
                    <div class="report-stat">
                        <div class="report-stat-value"><?php echo $today_stats['count']; ?></div>
                        <div class="report-stat-label">Transactions</div>
                    </div>
                    <div class="report-stat">
                        <div class="report-stat-value">₹<?php echo number_format(($today_stats['total'] ?: 0) / 100, 2); ?></div>
                        <div class="report-stat-label">Amount</div>
                    </div>
                    <div class="report-stat">
                        <div class="report-stat-value"><?php echo date('d M Y'); ?></div>
                        <div class="report-stat-label">Date</div>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Report -->
            <div class="report-section">
                <div class="report-header">
                    <div class="report-title">
                        <i class="fas fa-calendar-alt"></i> Monthly Report
                    </div>
                    <a href="generate_report.php?report_type=monthly&date_from=<?php echo $month_start; ?>&date_to=<?php echo date('Y-m-d'); ?>" target="_blank" class="btn-download">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
                <div class="report-stats">
                    <div class="report-stat">
                        <div class="report-stat-value"><?php echo $month_stats['count']; ?></div>
                        <div class="report-stat-label">Transactions</div>
                    </div>
                    <div class="report-stat">
                        <div class="report-stat-value">₹<?php echo number_format(($month_stats['total'] ?: 0) / 100, 2); ?></div>
                        <div class="report-stat-label">Amount</div>
                    </div>
                    <div class="report-stat">
                        <div class="report-stat-value"><?php echo date('F Y'); ?></div>
                        <div class="report-stat-label">Month</div>
                    </div>
                </div>
            </div>
            
            <!-- Custom Report -->
            <div class="report-section">
                <div class="report-header">
                    <div class="report-title">
                        <i class="fas fa-calendar-range"></i> Custom Date Range
                    </div>
                </div>
                <form method="GET" action="generate_report.php" target="_blank" class="row g-3">
                    <input type="hidden" name="report_type" value="custom">
                    
                    <div class="col-md-4">
                        <label for="custom_date_from" class="form-label">From Date</label>
                        <input type="date" id="custom_date_from" name="date_from" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="custom_date_to" class="form-label">To Date</label>
                        <input type="date" id="custom_date_to" name="date_to" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label><br>
                        <button type="submit" class="btn-download w-100">
                            <i class="fas fa-download"></i> Download Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Set default values for custom date range
        document.getElementById('custom_date_from').value = '<?php echo date('Y-m-01'); ?>';
        document.getElementById('custom_date_to').value = '<?php echo date('Y-m-d'); ?>';
    </script>
</body>
</html>
