<?php
include('includes/config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $payment_id = $_POST['razorpay_payment_id'];
    $admission_id = intval($_POST['admission_id']);

    // Update payment status
    $stmt = $mysqli->prepare("
        UPDATE new_admission
        SET payment_status='PAID',
            razorpay_payment_id=?
        WHERE id=?
    ");
    $stmt->bind_param("si", $payment_id, $admission_id);

    if ($stmt->execute()) {
        header("Location: success.php?admission_id=" . $admission_id);
        exit;
    } else {
        echo "Payment update failed";
    }
}
?>
