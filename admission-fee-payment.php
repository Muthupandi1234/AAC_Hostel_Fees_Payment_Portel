<?php
session_start();
include('includes/config.php');
include('includes/checklogin.php');
check_login();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Check if a specific admission_id was passed (after a fresh form submission)
$admission = null;
if (isset($_GET['admission_id']) && intval($_GET['admission_id']) > 0) {
    $admission_id_get = intval($_GET['admission_id']);
    $stmt = $mysqli->prepare("SELECT * FROM admission WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param('ii', $admission_id_get, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admission = $result->fetch_assoc();
    }
    $stmt->close();
}

// If no specific admission passed, use the latest admission for the user
if (!$admission) {
    $stmt = $mysqli->prepare("SELECT * FROM admission WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admission = $result->fetch_assoc();
    }
    $stmt->close();
}

// Check if admission fee already paid
$admission_fee_paid = false;
$stmt = $mysqli->prepare("SELECT id FROM payments WHERE user_id = ? AND instalment_number = 0 AND status = 'success'");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $admission_fee_paid = true;
}
$stmt->close();

// Get user details
$stmt = $mysqli->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$admission_fee = 100; // ₹100 admission fee
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Fee Payment - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Razorpay Checkout -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    
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
        
        .payment-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .amount-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .amount-box .amount-label {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .amount-box .amount-value {
            font-size: 42px;
            font-weight: 700;
        }
        
        .student-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        
        .info-value {
            color: #333;
        }
        
        .form-check {
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        
        .btn-pay {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 18px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-pay:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
        }
        
        .btn-pay:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
       @media (max-width: 768px) {

    .main-content {
        margin-left: 0;
        padding: 15px;
    }

    .header {
        padding: 15px;
        text-align: center;
    }

    .header h1 {
        font-size: 20px;
    }

    .payment-card {
        padding: 20px;
        max-width: 100%;
        border-radius: 12px;
    }

    .amount-box {
        padding: 20px;
    }

    .amount-box .amount-value {
        font-size: 28px;
    }

    .student-info {
        padding: 15px;
    }

    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .form-check {
        padding: 15px;
    }

    .btn-pay {
        font-size: 15px;
        padding: 12px;
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
            <h1><i class="fas fa-money-bill-wave"></i> Admission Fee Payment</h1>
        </div>
        
        <?php if (!$admission): ?>
            <div class="alert alert-warning" style="border-radius: 10px; max-width: 600px; margin: 0 auto;">
                <i class="fas fa-exclamation-triangle"></i> Please complete your admission form first.
                <a href="admission.php" class="alert-link">Go to Admission</a>
            </div>
        <?php elseif ($admission_fee_paid): ?>
            <?php
            // Get admission fee payment ID for receipt download
            $stmt = $mysqli->prepare("SELECT id FROM payments WHERE user_id = ? AND instalment_number = 0 AND status = 'success' ORDER BY created_at DESC LIMIT 1");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admission_payment = $result->fetch_assoc();
            $stmt->close();
            ?>
            <div class="alert alert-success" style="border-radius: 10px; max-width: 600px; margin: 0 auto;">
                <i class="fas fa-check-circle"></i> <strong>Admission Fee Paid!</strong> Your admission fee has been paid successfully.
                <div style="margin-top: 15px;">
                    <a href="fees-payment.php" class="btn btn-success" style="margin-right: 10px;">
                        <i class="fas fa-credit-card"></i> Proceed to Fees Payment
                    </a>
                    <?php if ($admission_payment): ?>
                        <a href="download-receipt.php?id=<?php echo $admission_payment['id']; ?>" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-download"></i> Download Admission Receipt
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="payment-card">
                <div class="student-info">
                    <h5 style="color: #1e3c72; margin-bottom: 15px;"><i class="fas fa-user"></i> Student Information</h5>
                    <div class="info-row">
                        <span class="info-label">Student Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admission['student_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Register Number:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admission['reg_no']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Course:</span>
                        <span class="info-value"><?php echo htmlspecialchars($admission['course']); ?></span>
                    </div>
                </div>
                
                <div class="amount-box">
                    <div class="amount-label">Admission Fee</div>
                    <div class="amount-value">₹<?php echo number_format($admission_fee, 2); ?></div>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmCheck" required>
                    <label class="form-check-label" for="confirmCheck">
                        I confirm and proceed to pay ₹<?php echo number_format($admission_fee, 2); ?> as admission fee
                    </label>
                </div>
                
                <button type="button" id="payButton" class="btn btn-pay" disabled>
                    <i class="fas fa-lock"></i> Pay ₹<?php echo number_format($admission_fee, 2); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Enable/disable pay button based on checkbox
        document.getElementById('confirmCheck')?.addEventListener('change', function() {
            const payBtn = document.getElementById('payButton');
            if (payBtn) {
                payBtn.disabled = !this.checked;
            }
        });
        
        document.getElementById('payButton')?.addEventListener('click', function() {
            initiatePayment();
        });
        
        function initiatePayment() {
            // Create order via AJAX
            fetch('create_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: <?php echo $admission_fee * 100; ?>, // Amount in paise
                    user_id: <?php echo $user_id; ?>,
                    instalment_number: 0, // 0 for admission fee
                    admission_id: <?php echo $admission ? $admission['id'] : 'null'; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Initialize Razorpay checkout
                    const options = {
                        key: '<?php echo RAZORPAY_KEY_ID; ?>',
                        amount: data.amount,
                        currency: 'INR',
                        name: '<?php echo SITE_NAME; ?>',
                        description: 'Admission Fee Payment',
                        order_id: data.order_id,
                        handler: function(response) {
                            // Payment successful, verify on server
                            verifyPayment(response);
                        },
                        prefill: {
                            name: '<?php echo htmlspecialchars($user['name']); ?>',
                            email: '<?php echo htmlspecialchars($user['email']); ?>',
                            contact: '<?php echo htmlspecialchars($admission ? $admission['phone'] : ''); ?>'
                        },
                        theme: {
                            color: '#667eea'
                        },
                        modal: {
                            ondismiss: function() {
                                console.log('Payment cancelled');
                            }
                        }
                    };
                    
                    const rzp = new Razorpay(options);
                    rzp.open();
                } else {
                    alert('Error creating order: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error initiating payment. Please try again.');
            });
        }
        
        function verifyPayment(response) {
            // Verify payment on server
            fetch('verify_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature: response.razorpay_signature,
                    user_id: <?php echo $user_id; ?>,
                    admission_id: <?php echo $admission ? $admission['id'] : 'null'; ?>,
                    instalment_number: 0 // 0 for admission fee
                })
            })
            .then(response => {
                // Handle both HTTP status and JSON response
                return response.json().then(data => ({
                    status: response.status,
                    data: data
                }));
            })
            .then(result => {
                // Check if the server returned a successful payment verification
                if (result.data.status === 'success') {
                    // If server returned a receipt URL, trigger automatic download
                    if (result.data.receipt_url) {
                        // Create invisible link and click it to prompt download
                        const a = document.createElement('a');
                        a.href = result.data.receipt_url;
                        a.target = '_blank';
                        a.rel = 'noopener';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    }

                    // Show success message
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success alert-dismissible fade show';
                    successMsg.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);';
                    successMsg.innerHTML = '<i class="fas fa-check-circle"></i> <strong>Payment Successful!</strong><br>Your admission fee has been paid successfully.';
                    document.body.appendChild(successMsg);
                    
                    setTimeout(() => {
                        // Force reload to check updated admission fee status
                        window.location.href = 'fees-payment.php?payment=success&type=admission&refresh=' + Date.now();
                    }, 2000);
                } else {
                    // Payment verification failed
                    alert('Payment verification failed: ' + result.data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error verifying payment. Please contact support.');
            });
        }
    </script>
</body>
</html>
