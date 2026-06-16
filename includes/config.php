<?php
// ===============================
// Database Configuration
// ===============================
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "hostel_fees";

// Create MySQLi connection (ONLY ONCE)
if (!isset($mysqli)) {
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }
}

// ===============================
// Razorpay Configuration (Test Mode)
// ===============================
if (!defined('RAZORPAY_KEY_ID')) {
    define('RAZORPAY_KEY_ID', 'rzp_test_S6yLa6qnxeXIrE');
}

if (!defined('RAZORPAY_KEY_SECRET')) {
    define('RAZORPAY_KEY_SECRET', '6oP995arqny2yZR356G7znF2');
}

// ===============================
// Site Configuration
// ===============================
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'AAC-Hostel payment portal');
}

if (!defined('INSTALMENT_1')) {
    define('INSTALMENT_1', 15000);
}

if (!defined('INSTALMENT_2')) {
    define('INSTALMENT_2', 13000);
}

if (!defined('INSTALMENT_3')) {
    define('INSTALMENT_3', 10000);
}

// ===============================
// Timezone
// ===============================
date_default_timezone_set('Asia/Kolkata');
?>
