<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/config.php');
include('includes/checklogin.php');
check_login();

// Prevent caching to ensure fresh data
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

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

// Get user details
$stmt = $mysqli->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Check if admission fee is paid (required before instalments)
$admission_fee_paid = false;
$admission_fee_data = null;
$stmt = $mysqli->prepare("SELECT id, instalment_number, status FROM payments WHERE user_id = ? AND instalment_number = 0 AND status = 'success' LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $admission_fee_paid = true;
    $admission_fee_data = $result->fetch_assoc();
}
$stmt->close();

// Check which instalments are already paid
$paid_instalments = [];

$stmt = $mysqli->prepare("
    SELECT instalment_number 
    FROM payments 
    WHERE user_id = ? 
    AND status = 'success' 
    AND instalment_number > 0
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $paid_instalments[] = (int)$row['instalment_number'];
}
$stmt->close();

$pending_terms = [];
$pending_total = 0;

$term_amounts = [
    1 => INSTALMENT_1,
    2 => INSTALMENT_2,
    3 => INSTALMENT_3
];

foreach ($term_amounts as $term => $amount) {
    if (!in_array($term, $paid_instalments)) {
        $pending_terms[] = $term;
        $pending_total += $amount;
    }
}

$pending_terms_count = count($pending_terms);







// Instalment amounts with sequential unlocking
// 1st instalment unlocks only after admission fee is paid
$instalments = [
    1 => ['amount' => INSTALMENT_1, 'name' => '1st Term', 'paid' => in_array(1, $paid_instalments), 'unlocked' => $admission_fee_paid],
    2 => ['amount' => INSTALMENT_2, 'name' => '2nd Term', 'paid' => in_array(2, $paid_instalments), 'unlocked' => in_array(1, $paid_instalments)],
    3 => ['amount' => INSTALMENT_3, 'name' => '3rd Term', 'paid' => in_array(3, $paid_instalments), 'unlocked' => in_array(2, $paid_instalments)]
];

// Check if all 3 instalments are already paid
$all_terms_paid = in_array(1, $paid_instalments)
               && in_array(2, $paid_instalments)
               && in_array(3, $paid_instalments);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Payment - <?php echo SITE_NAME; ?></title>
    
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
        
        .instalment-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .instalment-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .instalment-card.paid {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }
        
        .instalment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .instalment-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e3c72;
        }
        
        .instalment-amount {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
        }
        
        .instalment-status {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .instalment-status.paid {
            background: #4caf50;
            color: white;
        }
        
        .instalment-status.pending {
            background: #ff9800;
            color: white;
        }
        
        .student-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
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
            margin: 20px 0;
            padding: 15px;
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
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
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
    }

    .header h1 {
        font-size: 20px;
    }

    .instalment-card {
        padding: 20px;
    }

    .instalment-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .instalment-amount {
        font-size: 22px;
    }

    .student-info {
        padding: 15px;
    }

    .info-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .btn-pay {
        font-size: 14px;
        padding: 10px;
    }

    .modal-body {
        padding: 20px !important;
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
            <h1><i class="fas fa-credit-card"></i> Online Fees Payment</h1>
        </div>
        
        <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <strong>Payment Successful!</strong> 
                <?php if (isset($_GET['type']) && $_GET['type'] === 'admission'): ?>
                    Your admission fee has been paid. The instalment payments are now unlocked!
                <?php else: ?>
                    Your payment has been processed. The page will refresh to show updated status.
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                // Force hard reload to bypass cache and refresh payment status from database
                setTimeout(function() {
                    // Remove query parameters and reload fresh to check database
                    window.location.href = 'fees-payment.php';
                }, 2500);
            </script>
        <?php endif; ?>
        
        
        
        <?php if (!$admission): ?>
            <div class="alert alert-warning" style="border-radius: 10px;">
                <i class="fas fa-exclamation-triangle"></i> Please complete your admission first before making payment.
                <a href="admission.php" class="alert-link">Go to Admission</a>
            </div>
        <?php elseif (!$admission_fee_paid): ?>
            <div class="alert alert-info" style="border-radius: 10px;">
                <i class="fas fa-info-circle"></i> Please pay the admission fee (₹100) first to unlock the instalment payments.
                <div style="margin-top: 15px;">
                    <a href="admission-fee-payment.php" class="btn btn-primary">
                        <i class="fas fa-money-bill-wave"></i> Pay Admission Fee (₹100)
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Show Admission Fee Status -->
            <div class="alert alert-success" style="border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <strong>Admission Fee Paid</strong> - You can now pay the instalment fees below.
                <?php if (isset($admission_fee_data)): ?>
                    <a href="download-receipt.php?id=<?php echo $admission_fee_data['id']; ?>" class="btn btn-sm btn-outline-success ms-2" target="_blank">
                        <i class="fas fa-download"></i> Download Admission Receipt
                    </a>
                <?php endif; ?>
            </div>
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
                    <span class="info-value"><?php echo htmlspecialchars($admission['course']); ?> - <?php echo htmlspecialchars($admission['department']); ?></span>
                        </div>
                <div class="info-row">
                    <span class="info-label">Room Number:</span>
                    <span class="info-value"><?php echo htmlspecialchars($admission['room_number']); ?></span>
                        </div>
                    </div>
                    
            <h3 style="color: #1e3c72; margin-bottom: 20px;">
                <i class="fas fa-list-ol"></i> Fee Instalments
                <?php if ($admission_fee_paid): ?>
                    <span class="badge bg-success ms-2" style="font-size: 12px; padding: 5px 10px;">Unlocked</span>
                <?php endif; ?>
            </h3>

            <!-- Pay All 3 Terms Button - Always Visible -->
        <?php if (!$all_terms_paid && $pending_total > 0 && $pending_terms_count > 1): ?>

    <div style="margin-bottom: 30px;">
        <button type="button"
                class="btn btn-primary btn-lg"
                data-bs-toggle="modal"
                data-bs-target="#payAllModal"
                style="border-radius:10px;padding:15px 30px;width:100%;font-weight:600;">
            
            <i class="fas fa-money-bill-wave"></i>
            Pay Pending Terms (₹<?php echo number_format($pending_total); ?>)

            <div style="font-size:13px;margin-top:6px;">
                Covers:
                <?php foreach ($pending_terms as $t): ?>
                    <?php echo $t; ?><?php echo ($t==1?'st':($t==2?'nd':'rd')); ?> Term
                <?php endforeach; ?>
            </div>
        </button>
    </div>
<?php endif; ?>


            
            <?php foreach ($instalments as $num => $instalment): ?>
                <div class="instalment-card <?php echo $instalment['paid'] ? 'paid' : ''; ?>">
                    <div class="instalment-header">
                        <div>
                            <div class="instalment-title"><?php echo $instalment['name']; ?></div>
                            <div class="instalment-amount">₹<?php echo number_format($instalment['amount'], 2); ?></div>
                        </div>
                        <div>
                            <span class="instalment-status <?php echo $instalment['paid'] ? 'paid' : 'pending'; ?>">
                                <?php echo $instalment['paid'] ? '<i class="fas fa-check-circle"></i> Paid' : '<i class="fas fa-clock"></i> Pending'; ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($instalment['paid']): ?>
                        <div class="alert alert-success" style="margin: 0; border-radius: 10px;">
                            <i class="fas fa-check-circle"></i> <strong>Paid Successfully!</strong> This instalment has been completed.
                            <div style="margin-top: 10px;">
                                <?php
                                // Get payment ID for this instalment
                                $stmt = $mysqli->prepare("SELECT id FROM payments WHERE user_id = ? AND instalment_number = ? AND status = 'success' ORDER BY created_at DESC LIMIT 1");
                                $stmt->bind_param('ii', $user_id, $num);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $instalment_payment = $result->fetch_assoc();
                                $stmt->close();
                                if ($instalment_payment):
                                ?>
                                    <a href="download-receipt.php?id=<?php echo $instalment_payment['id']; ?>" class="btn btn-sm btn-outline-success" target="_blank">
                                        <i class="fas fa-download"></i> Download <?php echo $instalment['name']; ?> Receipt
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif (!$instalment['unlocked']): ?>
                        <div class="alert alert-warning" style="margin: 0; border-radius: 10px;">
                            <i class="fas fa-lock"></i> <strong>Locked</strong> - Please complete previous instalment(s) to unlock this payment.
                        </div>
                    <?php else: ?>
                    <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmCheck<?php echo $num; ?>" required>
                            <label class="form-check-label" for="confirmCheck<?php echo $num; ?>">
                                I confirm and proceed to pay ₹<?php echo number_format($instalment['amount'], 2); ?> for <?php echo $instalment['name']; ?>
                        </label>
                    </div>
                    
                        <button type="button" class="btn btn-pay" id="payButton<?php echo $num; ?>" 
                                data-instalment="<?php echo $num; ?>" 
                                data-amount="<?php echo $instalment['amount']; ?>"
                                data-name="<?php echo $instalment['name']; ?>"
                                data-bs-toggle="modal" 
                                data-bs-target="#paymentModal<?php echo $num; ?>"
                                disabled>
                            <i class="fas fa-lock"></i> Pay ₹<?php echo number_format($instalment['amount'], 2); ?>
                    </button>
                    
                    <!-- Payment Confirmation Modal -->
                    <div class="modal fade" id="paymentModal<?php echo $num; ?>" tabindex="-1" aria-labelledby="paymentModalLabel<?php echo $num; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius: 15px; border: none;">
                                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                                    <h5 class="modal-title" id="paymentModalLabel<?php echo $num; ?>">
                                        <i class="fas fa-credit-card"></i> Confirm Payment
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding: 30px;">
                                    <div class="text-center mb-4">
                                        <div class="mb-3">
                                            <i class="fas fa-money-bill-wave" style="font-size: 48px; color: #28a745;"></i>
                                        </div>
                                        <h4 style="color: #1e3c72; margin-bottom: 10px;"><?php echo $instalment['name']; ?> Payment</h4>
                                        <p class="text-muted">Please review the payment details below</p>
                                    </div>
                                    
                                    <div class="payment-details" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span style="color: #6c757d; font-weight: 500;">Instalment:</span>
                                            <span style="color: #333; font-weight: 600;"><?php echo $instalment['name']; ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span style="color: #6c757d; font-weight: 500;">Amount:</span>
                                            <span style="color: #28a745; font-weight: 700; font-size: 20px;">₹<?php echo number_format($instalment['amount'], 2); ?></span>
                                        </div>
                                        <?php if ($admission): ?>
                                        <div class="d-flex justify-content-between">
                                            <span style="color: #6c757d; font-weight: 500;">Student:</span>
                                            <span style="color: #333; font-weight: 600;"><?php echo htmlspecialchars($admission['student_name']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="alert alert-info" style="border-radius: 10px; margin-bottom: 0;">
                                        <i class="fas fa-info-circle"></i> You will be redirected to Razorpay secure payment gateway to complete the transaction.
                                    </div>
                                </div>
                                <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 20px 30px;">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    <button type="button" class="btn btn-success" id="confirmPayButton<?php echo $num; ?>" 
                                            data-instalment="<?php echo $num; ?>" 
                                            data-amount="<?php echo $instalment['amount']; ?>"
                                            style="border-radius: 10px; padding: 10px 30px;">
                                        <i class="fas fa-lock"></i> Proceed to Pay
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Pay All 3 Terms Modal - Always Available -->
        <div class="modal fade" id="payAllModal" tabindex="-1" aria-labelledby="payAllModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 15px; border: none;">
                    <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0;">
                        <h5 class="modal-title" id="payAllModalLabel">
                            <i class="fas fa-credit-card"></i> Pay All 3 Terms
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 30px;">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-money-bill-wave" style="font-size: 48px; color: #28a745;"></i>
                            </div>
                            <h4 style="color: #1e3c72; margin-bottom: 10px;">Pay Pending Terms Together</h4>
                            <p class="text-muted">Pay pending instalment fees in a single transaction</p>
                        </div>
                        
                        <div class="payment-details" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: #6c757d; font-weight: 500;">1st Term:</span>
                                <span style="color: #333; font-weight: 600;">₹<?php echo number_format(INSTALMENT_1, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: #6c757d; font-weight: 500;">2nd Term:</span>
                                <span style="color: #333; font-weight: 600;">₹<?php echo number_format(INSTALMENT_2, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: #6c757d; font-weight: 500;">3rd Term:</span>
                                <span style="color: #333; font-weight: 600;">₹<?php echo number_format(INSTALMENT_3, 2); ?></span>
                            </div>
                            <hr style="margin: 15px 0;">
                            <div class="d-flex justify-content-between">
                                <span style="color: #1e3c72; font-weight: 700; font-size: 18px;">Total Amount:</span>
                                <span style="color: #28a745; font-weight: 700; font-size: 24px;">
    ₹<?php echo number_format($pending_total, 2); ?>
</span>

                            </div>
                        </div>
                        
                        <div class="alert alert-info" style="border-radius: 10px; margin-bottom: 0;">
                            <i class="fas fa-info-circle"></i> You will be redirected to Razorpay secure payment gateway to complete the transaction.
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 20px 30px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-success" id="confirmPayAllBtn" 
                                style="border-radius: 10px; padding: 10px 30px;">
                            <i class="fas fa-lock"></i> Proceed to Pay ₹<?php echo number_format($pending_total, 2); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Enable/disable pay buttons based on checkboxes
        <?php foreach ($instalments as $num => $instalment): ?>
            <?php if (!$instalment['paid'] && $instalment['unlocked']): ?>
                document.getElementById('confirmCheck<?php echo $num; ?>').addEventListener('change', function() {
                    document.getElementById('payButton<?php echo $num; ?>').disabled = !this.checked;
                });
        
                // Handle modal confirmation button
                document.getElementById('confirmPayButton<?php echo $num; ?>').addEventListener('click', function() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal<?php echo $num; ?>'));
                    modal.hide();
                    const instalment = this.getAttribute('data-instalment');
                    const amount = this.getAttribute('data-amount');
                    initiatePayment(instalment, amount, 'single');
                });
            <?php endif; ?>
        <?php endforeach; ?>
        
        function initiatePayment(instalment, amount, type) {
            type = type || 'single';
            // Create order via AJAX
            fetch('create_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: amount * 100, // Amount in paise
                    user_id: <?php echo $user_id; ?>,
                    instalment_number: instalment,
                    admission_id: <?php echo $admission ? $admission['id'] : 'null'; ?>,
                    payment_type: type
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
                        description: type === 'all' ? 'Hostel Fees Payment - All 3 Terms' : 'Hostel Fees Payment - Instalment ' + instalment,
                        order_id: data.order_id,
                        handler: function(response) {
                            // Payment successful, verify on server
                            verifyPayment(response, instalment, type);
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
                    alert('Unable to process payment: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Unable to start payment. Please refresh the page and try again.');
            });
        }
        
        // Pay All 3 Terms button handler
        document.getElementById('confirmPayAllBtn').addEventListener('click', function() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('payAllModal'));
    modal.hide();

    const pendingAmount = <?php echo $pending_total; ?>;

    initiatePayment(0, pendingAmount, 'all');
});

        
        function verifyPayment(response, instalment, paymentType) {
            paymentType = paymentType || 'single';
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
                    instalment_number: instalment,
                    payment_type: paymentType
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
                    // Show success message
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success alert-dismissible fade show';
                    successMsg.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2);';
                    successMsg.innerHTML = '<i class="fas fa-check-circle"></i> <strong>Payment Successful!</strong><br>Your payment has been processed and receipt has been generated.<br><a href="fees-history.php" style="color: white; text-decoration: underline;">Download Receipt</a>';
                    document.body.appendChild(successMsg);
                    
                    // Force hard refresh to reload payment status from database
                    setTimeout(() => {
                        window.location.href = 'fees-payment.php?payment=success&refresh=' + Date.now();
                    }, 2000);
                } else {
                    // Payment verification failed
                    alert('Payment verification failed: ' + result.data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Unable to verify payment. Please contact support if the amount was deducted.');
            });
        }
    </script>
</body>
</html>
