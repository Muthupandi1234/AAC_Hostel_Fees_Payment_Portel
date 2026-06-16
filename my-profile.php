<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

$user_id = $_SESSION['user_id'];

// Get user details
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Get admission details
$admission = null;
$stmt = $mysqli->prepare("SELECT * FROM admission WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $admission = $result->fetch_assoc();
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo SITE_NAME; ?></title>
    
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
        
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }
        
        .profile-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .profile-card h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f5f7fa;
        }
        
        .profile-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #f5f7fa;
        }
        
        .profile-item:last-child {
            border-bottom: none;
        }
        
        .profile-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 14px;
        }
        
        .profile-value {
            color: #333;
            font-size: 14px;
            text-align: right;
        }
        
        @media (max-width: 768px) {

    .profile-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .profile-value {
        text-align: left;
    }

    .profile-card {
        padding: 20px;
    }

    .header {
        flex-direction: column;
        gap: 10px;
        text-align: center;
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
            <h1>My Profile</h1>
        </div>
        
        <div class="profile-grid">
            <!-- Personal Data Card -->
            <div class="profile-card">
                <h3><i class="fas fa-user"></i> Personal Data</h3>
                <div class="profile-item">
                    <span class="profile-label">Name</span>
                    <span class="profile-value"><?php echo htmlspecialchars($user['name']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Email</span>
                    <span class="profile-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Phone</span>
                    <span class="profile-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Account Created</span>
                    <span class="profile-value"><?php echo date('d M Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
            
            <!-- Admission Data Card -->
            <?php if ($admission): ?>
            <div class="profile-card">
                <h3><i class="fas fa-file-alt"></i> Admission Data</h3>
                <div class="profile-item">
                    <span class="profile-label">Student Name</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['student_name']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Register Number</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['reg_no']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Course</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['course']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Year</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['year'] ?? '-'); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Room Number</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['room_no'] ?? '-'); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Seater</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['seater'] ?? '-'); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Mess Status</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['mess_status'] ?? '-'); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Joining Date</span>
                    <?php echo date('d M Y', strtotime($admission['date_of_joining'])); ?>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Duration</span>
                    <?php echo htmlspecialchars($admission['duration_of_stay']); ?>

                </div>
                <!-- <div class="profile-item">
                    <span class="profile-label">Fees Per Month</span>
                    <span class="profile-value">₹<?php echo number_format($admission['feespm'], 2); ?></span>
                </div> -->
            </div>
            
            <!-- Address Card -->
            <div class="profile-card">
                <h3><i class="fas fa-map-marker-alt"></i> Address for Communication</h3>
                <div class="profile-item">
                    <span class="profile-label">Phone</span>
                    <span class="profile-value"><?php echo htmlspecialchars($admission['phone']); ?></span>
                </div>
                <div class="profile-item">
                    <span class="profile-label">Address</span><br><br>
                    <?php echo htmlspecialchars($admission['residential_address']); ?>

                </div>
            </div>
            <?php else: ?>
            <div class="profile-card">
                <h3><i class="fas fa-file-alt"></i> Admission Data</h3>
                <p style="color: #6c757d; text-align: center; padding: 20px;">No admission data found. Please complete your admission.</p>
                <a href="admission.php" class="btn btn-primary" style="width: 100%;">Complete Admission</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

