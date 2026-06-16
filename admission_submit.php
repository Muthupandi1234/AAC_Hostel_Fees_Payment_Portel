<?php
include('includes/config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $student_name = $_POST['student_name'];
    $department   = $_POST['department'];
    $year         = $_POST['year'];
    $phone        = $_POST['phone'];
    $admission_year = date('Y');
    $amount = 100;

    $stmt = $mysqli->prepare("
        INSERT INTO new_admission
        (student_name, department, year, phone, admission_year, amount, payment_status)
        VALUES (?, ?, ?, ?, ?, ?, 'PENDING')
    ");

    $stmt->bind_param(
        "ssssii",
        $student_name,
        $department,
        $year,
        $phone,
        $admission_year,
        $amount
    );

    if ($stmt->execute()) {
        $admission_id = $stmt->insert_id;

        // redirect to payment page
        header("Location: admission_pay.php?admission_id=" . $admission_id);
        exit;
    } else {
        echo "Error. Please try again.";
    }
}
?>
