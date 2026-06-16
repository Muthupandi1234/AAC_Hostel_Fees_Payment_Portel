<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('includes/config.php');
include('includes/checklogin.php');
check_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$razorpay_order_id   = $input['razorpay_order_id'] ?? '';
$razorpay_payment_id = $input['razorpay_payment_id'] ?? '';
$razorpay_signature  = $input['razorpay_signature'] ?? '';
$user_id             = intval($input['user_id'] ?? 0);
$admission_id        = intval($input['admission_id'] ?? 0);
$instalment_number   = intval($input['instalment_number'] ?? 0);
$payment_type        = $input['payment_type'] ?? 'single';

if (!$razorpay_order_id || !$razorpay_payment_id || !$razorpay_signature || $user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Missing payment data']);
    exit;
}

/* ---------------- Razorpay SDK ---------------- */
require_once 'vendor/autoload.php';
use Razorpay\Api\Api;

$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

/* -------- Verify signature (important) -------- */
try {
    $api->utility->verifyPaymentSignature([
        'razorpay_order_id'   => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature'  => $razorpay_signature
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Signature verification failed']);
    exit;
}

/* -------- Fetch payment from Razorpay ---------- */
$payment = $api->payment->fetch($razorpay_payment_id);

if ($payment->status !== 'captured' && $payment->status !== 'authorized') {
    echo json_encode(['status' => 'error', 'message' => 'Payment not successful']);
    exit;
}

$amount = $payment->amount; // paise

/* ========= FINAL AMOUNT VERIFICATION (ALL_TERMS) ========= */

if ($payment_type === 'all') {

    $termAmounts = [
        1 => INSTALMENT_1 * 100,
        2 => INSTALMENT_2 * 100,
        3 => INSTALMENT_3 * 100
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

    $expected_amount = 0;
    foreach ($termAmounts as $term => $amt) {
        if (!in_array($term, $paid)) {
            $expected_amount += $amt;
        }
    }

    if ($expected_amount !== $amount) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Amount mismatch detected'
        ]);
        exit;
    }
}

$method = $payment->method;
$status = 'success';
$payment_date = date('Y-m-d H:i:s');

/* -------- DUPLICATE CHECK (ONLY FOR SINGLE) -------- */
if ($payment_type !== 'all') {
    $stmt = $mysqli->prepare("SELECT id FROM payments WHERE razorpay_payment_id = ?");
    $stmt->bind_param('s', $razorpay_payment_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Payment already recorded']);
        exit;
    }
    $stmt->close();
}

/* ================= PAY ALL 3 TERMS ================= */
if ($payment_type === 'all') {

    $termAmounts = [
        1 => INSTALMENT_1 * 100,
        2 => INSTALMENT_2 * 100,
        3 => INSTALMENT_3 * 100
    ];

    $mysqli->begin_transaction();

    try {
        $payment_ids = [];

        foreach ($termAmounts as $term => $amt) {
            $stmt = $mysqli->prepare("
                INSERT INTO payments 
                (user_id, admission_id, instalment_number, amount, status,
                 razorpay_order_id, razorpay_payment_id, razorpay_signature,
                 payment_method, payment_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                'iiiissssss',
                $user_id,
                $admission_id,
                $term,
                $amt,
                $status,
                $razorpay_order_id,
                $razorpay_payment_id,
                $razorpay_signature,
                $method,
                $payment_date
            );
            $stmt->execute();
            $payment_ids[] = $mysqli->insert_id;
            $stmt->close();
        }

        /* -------- SINGLE RECEIPT FOR ALL TERMS -------- */
        $receipt_no = 'RCP-ALL-' . date('Y') . '-' . str_pad($payment_ids[0], 6, '0', STR_PAD_LEFT);

        $stmt = $mysqli->prepare("
            INSERT INTO receipts (payment_id, user_id, admission_id, receipt_number, receipt_type)
            VALUES (?, ?, ?, ?, 'ALL_TERMS')
        ");
        $stmt->bind_param('iiis', $payment_ids[0], $user_id, $admission_id, $receipt_no);
        $stmt->execute();
        $stmt->close();

        $mysqli->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'All 3 terms paid successfully',
            'receipt_number' => $receipt_no
        ]);
        exit;

    } catch (Exception $e) {
        $mysqli->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Payment save failed']);
        exit;
    }
}

/* ================= SINGLE INSTALMENT ================= */
$stmt = $mysqli->prepare("
    INSERT INTO payments 
    (user_id, admission_id, instalment_number, amount, status,
     razorpay_order_id, razorpay_payment_id, razorpay_signature,
     payment_method, payment_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'iiiissssss',
    $user_id,
    $admission_id,
    $instalment_number,
    $amount,
    $status,
    $razorpay_order_id,
    $razorpay_payment_id,
    $razorpay_signature,
    $method,
    $payment_date
);
$stmt->execute();
$payment_id = $mysqli->insert_id;
$stmt->close();

$receipt_no = 'RCP-' . date('Y') . '-' . str_pad($payment_id, 6, '0', STR_PAD_LEFT);

$stmt = $mysqli->prepare("
    INSERT INTO receipts (payment_id, user_id, admission_id, receipt_number)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param('iiis', $payment_id, $user_id, $admission_id, $receipt_no);
$stmt->execute();
$stmt->close();

echo json_encode([
    'status' => 'success',
    'message' => 'Payment successful',
    'receipt_number' => $receipt_no
]);
