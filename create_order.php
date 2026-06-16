<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/config.php');
include('includes/checklogin.php');
check_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request. Please try again.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);


$user_id = isset($input['user_id']) ? intval($input['user_id']) : 0;
$instalment_number = isset($input['instalment_number']) ? intval($input['instalment_number']) : 0;
$receipt_type = $input['payment_type'] ?? 'single';

$admission_id = isset($input['admission_id']) ? intval($input['admission_id']) : null;

if ($user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Payment amount or user information is invalid. Please refresh the page and try again.']);
    exit();
}

// instalment_number can be 0 (admission fee) or 1-3 (instalments)

/* ================= BACKEND AMOUNT CALC ================= */

if ($receipt_type === 'all') {

    $term_amounts = [
        1 => INSTALMENT_1,
        2 => INSTALMENT_2,
        3 => INSTALMENT_3
    ];

    $stmt = $mysqli->prepare("
        SELECT instalment_number
        FROM payments
        WHERE user_id = ?
        AND status = 'success'
        AND instalment_number > 0
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $paid = [];
    while ($r = $res->fetch_assoc()) {
        $paid[] = (int)$r['instalment_number'];
    }
    $stmt->close();

    $amount = 0;
    foreach ($term_amounts as $term => $amt) {
        if (!in_array($term, $paid)) {
            $amount += $amt;
        }
    }

    $amount = $amount * 100; // convert to paise

} else {
    // SINGLE instalment — frontend amount allowed
    $amount = intval($input['amount'] ?? 0);
}

if ($instalment_number < 0 || $instalment_number > 3) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid payment option selected. Please try again.']);
    exit();
}

// Include Razorpay PHP SDK
// Try Composer autoload first, then manual installation
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
} elseif (file_exists('razorpay-php/src/Razorpay.php')) {
    require_once 'razorpay-php/src/Razorpay.php';
} elseif (file_exists('razorpay-php/Razorpay.php')) {
    require_once 'razorpay-php/Razorpay.php';
} else {
    echo json_encode(['status' => 'error', 'message' => 'Payment system is not available. Please contact support.']);
    exit();
}

use Razorpay\Api\Api;

try {
    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
    
    $orderData = [
        'receipt' => 'receipt_' . time(),
        'amount' => $amount, // Amount in paise
        'currency' => 'INR',
        'notes' => [
            'user_id' => $user_id,
            'admission_id' => $admission_id,
            'instalment_number' => $instalment_number,
            'payment_for' => 'Hostel Fees - Instalment ' . $instalment_number
        ]
    ];
    
    $razorpayOrder = $api->order->create($orderData);
    
    // Store order ID in session temporarily (optional)
    $_SESSION['razorpay_order_id'] = $razorpayOrder['id'];
    
    echo json_encode([
        'status' => 'success',
        'order_id' => $razorpayOrder['id'],
        'amount' => $amount
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>

