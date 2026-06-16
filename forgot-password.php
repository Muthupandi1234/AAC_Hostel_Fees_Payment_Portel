<?php
session_start();
include('includes/config.php');

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

if ($step == 1 && isset($_POST['step1'])) {
    $email = trim($_POST['email']);
    
    if (!empty($email)) {
        $stmt = $mysqli->prepare("SELECT id, email, security_question FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $_SESSION['reset_user_id'] = $user['id'];
            $_SESSION['reset_email'] = $user['email'];
            
            if (!empty($user['security_question'])) {
                header("Location: forgot-password.php?step=2");
                exit();
            } else {
                $error = 'Security question not set. Please contact admin.';
            }
        } else {
            $error = 'Email not found in our system.';
        }
        $stmt->close();
    } else {
        $error = 'Please enter your email address.';
    }
}

if ($step == 2 && isset($_POST['step2'])) {
    $security_answer = trim($_POST['security_answer']);
    $user_id = $_SESSION['reset_user_id'];
    
    if (!empty($security_answer)) {
        $stmt = $mysqli->prepare("SELECT security_answer FROM users WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Case-insensitive comparison
            if (strtolower(trim($user['security_answer'])) === strtolower(trim($security_answer))) {
                header("Location: forgot-password.php?step=3");
                exit();
            } else {
                $error = 'Incorrect security answer. Please try again.';
            }
        }
        $stmt->close();
    } else {
        $error = 'Please enter your security answer.';
    }
}

if ($step == 3 && isset($_POST['step3'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['reset_user_id'];
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill all fields.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_email']);
            $success = 'Password reset successfully! Redirecting to login...';
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>";
        } else {
            $error = 'Error resetting password. Please try again.';
        }
        $stmt->close();
    }
}

// Get security question for step 2
$security_question = '';
if ($step == 2 && isset($_SESSION['reset_user_id'])) {
    $user_id = $_SESSION['reset_user_id'];
    $stmt = $mysqli->prepare("SELECT security_question FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $security_question = $user['security_question'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo SITE_NAME; ?></title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .forgot-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 50px;
        }
        
        .forgot-title {
            font-size: 28px;
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .forgot-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 14px;
            text-align: center;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .step.active {
            background: #667eea;
            color: white;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-back {
            background: #6c757d;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-back:hover {
            background: #5a6268;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .security-question-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .security-question-box strong {
            color: #1e3c72;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <h2 class="forgot-title"><i class="fas fa-key"></i> Forgot Password</h2>
        <p class="forgot-subtitle">Follow the steps to reset your password</p>
        
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'completed' : ''; ?>">1</div>
            <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'completed' : ''; ?>">2</div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">3</div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <!-- Step 1: Enter Email -->
        <?php if ($step == 1): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your registered email" required>
                </div>
                
                <button type="submit" name="step1" class="btn btn-submit">
                    <i class="fas fa-arrow-right"></i> Next
                </button>
                
                <a href="index.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </form>
        <?php endif; ?>
        
        <!-- Step 2: Security Question -->
        <?php if ($step == 2): ?>
            <div class="security-question-box">
                <strong>Security Question:</strong><br>
                <?php echo htmlspecialchars($security_question); ?>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Your Answer</label>
                    <input type="text" name="security_answer" class="form-control" placeholder="Enter your answer" required>
                </div>
                
                <button type="submit" name="step2" class="btn btn-submit">
                    <i class="fas fa-arrow-right"></i> Verify
                </button>
                
                <a href="forgot-password.php?step=1" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        <?php endif; ?>
        
        <!-- Step 3: Reset Password -->
        <?php if ($step == 3): ?>
            <form method="POST" action="" id="resetForm">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Enter new password (min 6 characters)" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required minlength="6">
                </div>
                
                <button type="submit" name="step3" class="btn btn-submit">
                    <i class="fas fa-check"></i> Reset Password
                </button>
                
                <a href="forgot-password.php?step=2" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            Remember your password? <a href="index.php">Login here</a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Client-side validation for step 3
        <?php if ($step == 3): ?>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const newPassword = document.querySelector('input[name="new_password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
