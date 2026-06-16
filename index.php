<?php
session_start();
include('includes/config.php');

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .creator-name {
            position: absolute;
            top: 20px;
            right: 20px;
            opacity: 0.8;
            font-size: 0.9rem;
            color: white;
            z-index: 2;
        }
        
        .logo-image {
            position: absolute;
            top: 70px;
            left: 40px;
            max-width: 110px;
            width: 110px;
            height: 110px;
            border-radius: 0%;
            object-fit: cover;
            z-index: 2;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .hostel-logo-image {
            position: absolute;
            top: 70px;
            right: 40px;
            max-width: 110px;
            width: 110px;
            height: 110px;
            border-radius: 0%;
            object-fit: cover;
            z-index: 2;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
            animation: fadeInUp 0.8s ease;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.95;
            animation: fadeInUp 1s ease;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            animation: fadeInUp 1.2s ease;
            justify-content: center;
        }
        
        .btn-hero {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            border: 2px solid white;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-hero-primary {
            background: white;
            color: #667eea;
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            color: #667eea;
        }
        
        .btn-hero-outline {
            background: transparent;
            color: white;
        }
        
        .btn-hero-outline:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e3c72;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .section-subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 60px;
        }
        
        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
        }
        
        .feature-card h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .feature-card p {
            color: #6c757d;
            line-height: 1.8;
        }
        
        /* Info Section */
        .info-section {
            padding: 80px 0;
            background: white;
        }
        
        .info-card {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .info-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
        }
        
        .info-list {
            list-style: none;
            padding: 0;
        }
        
        .info-list li {
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            font-size: 1.1rem;
        }
        
        .info-list li:last-child {
            border-bottom: none;
        }
        
        .info-list li i {
            margin-right: 15px;
            width: 25px;
        }
        
        /* Footer */
        .footer {
            background: #1e3c72;
            color: white;
            padding: 40px 0;
            text-align: center;
        }

        .creator-link {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s ease;
}

.creator-link:hover {
    color: #0d6efd;
    text-decoration: underline;
}
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .btn-hero {
                width: 100%;
                text-align: center;
            }
        }

        .hero-subtitle {
    font-size: 18px;
    font-weight: 500;
    color: #f7f4f4;
    margin-top: 15px;
    min-height: 30px;
}

.highlight {
    color: #0d6efd;
    font-weight: bold;
}

.cursor {
    display: inline-block;
    width: 2px;
    background: #000;
    margin-left: 3px;
    animation: blink 0.7s infinite;
}

@keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0; }
    100% { opacity: 1; }
}
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <img src="images/aac.png" alt="Logo" class="logo-image">
        <img src="images/aac_hostel.jpeg" alt="AAC Hostel" class="hostel-logo-image">
        <div class="creator-name">
    <a href="https://www.linkedin.com/in/linkedin.com/in/muthupandi07" target="_blank" class="creator-link">
        @ Muthupandi K (24MCA510)
    </a>
</div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    <i class="fas fa-graduation-cap"></i> <?php echo SITE_NAME; ?>
                </h1>
                <!-- <p class="hero-subtitle">
                    Complete your hostel admission and pay fees online securely. Fast, easy, and convenient payment system.
                </p> -->

                <p class="hero-subtitle">
    <span id="typing-text"></span>
</p>

                <div class="hero-buttons">
                    <a href="login.php" class="btn-hero btn-hero-primary">
                        <i class="fas fa-sign-in-alt"></i> Student Login
                    </a>
                    <a href="admin/index.php" class="btn-hero btn-hero-outline">
                        <i class="fas fa-user-shield"></i> Admin Login
                    </a>
                    <a href="create-account.php" class="btn-hero btn-hero-outline">
                        <i class="fas fa-user-plus"></i> Create Account
                    </a>
                    <!-- <a href="admission_form.php" class="btn-hero btn-hero-outline">
                        <i class="fas fa-user-plus"></i> Admission NOW
                    </a> -->
                </div>
                
            </div>
            
        </div>
        
    </section>
    
    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose Our Portal?</h2>
            <p class="section-subtitle">Experience seamless hostel fee management</p>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4>Secure Payments</h4>
                        <p>All transactions are secured with Razorpay's industry-leading encryption technology.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h4>Quick & Easy</h4>
                        <p>Complete your admission and payment in just a few clicks. No hassle, no queues.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h4>Instant Receipts</h4>
                        <p>Download your payment receipts instantly in PDF format after successful payment.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4>Multiple Payment Options</h4>
                        <p>Pay using Credit/Debit cards, UPI, Net Banking, or any other supported method.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h4>Payment History</h4>
                        <p>View all your previous payments and track your payment status anytime.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile Friendly</h4>
                        <p>Access the portal from any device - desktop, tablet, or mobile phone.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="info-card">
                        <h3><i class="fas fa-info-circle"></i> Fee Structure</h3>
                        <ul class="info-list">
                            <li><i class="fas fa-check-circle"></i> 1st Term: ₹15,000</li>
                            <li><i class="fas fa-check-circle"></i> 2nd Term: ₹13,000</li>
                            <li><i class="fas fa-check-circle"></i> 3rd Term: ₹10,000</li>
                        </ul>
                        <p style="margin-top: 30px; opacity: 0.9;">
                            <i class="fas fa-shield-alt"></i> All payments are processed securely through Razorpay. 
                            Your financial information is protected with bank-level security.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            <p style="margin-top: 10px; opacity: 0.8;">
                <i class="fas fa-envelope"></i> support@hostel.com | 
                <i class="fas fa-phone"></i> +91 1234567890
            </p>
            
        </div>
    </footer>

    <script>
const text = "To join the hostel, create your account and complete the admission fee to secure your stay.";
const typingElement = document.getElementById("typing-text");

let index = 0;

function typeEffect() {
    if (index < text.length) {
        typingElement.innerHTML += text.charAt(index);
        index++;
        setTimeout(typeEffect, 40);
    } else {
        typingElement.innerHTML += '<span class="cursor"></span>';
    }
}

window.onload = typeEffect;
</script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


