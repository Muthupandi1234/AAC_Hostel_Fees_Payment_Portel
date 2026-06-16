<?php
include('../includes/config.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

$where = "";
if ($search !== "") {
    $safe = $mysqli->real_escape_string($search);
    $where = "WHERE 
    a.student_name LIKE '%$safe%' OR
    a.reg_no LIKE '%$safe%' OR
    p.razorpay_payment_id LIKE '%$safe%' OR
    p.amount LIKE '%$safe%' OR
    p.status LIKE '%$safe%' OR
    p.instalment_number LIKE '%$safe%' OR
    DATE_FORMAT(p.created_at, '%d %b %Y') LIKE '%$safe%' OR
    DATE_FORMAT(p.created_at, '%Y-%m-%d') LIKE '%$safe%'";

}

$result = $mysqli->query("
    SELECT p.*, a.student_name, a.reg_no
    FROM payments p
    LEFT JOIN admission a ON p.admission_id = a.id
    $where
    ORDER BY p.created_at DESC
    LIMIT 10
");

$instalment_names = [
    0 => 'Admission Fee',
    1 => '1st Term',
    2 => '2nd Term',
    3 => '3rd Term'
];

$i = 1;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        $inst = isset($instalment_names[$row['instalment_number']])
            ? $instalment_names[$row['instalment_number']]
            : 'N/A';

        $status = ucfirst($row['status']);
        $statusClass = $row['status'] === 'success' ? 'success' : 'pending';

        echo "<tr>
            <td>{$i}</td>
            <td>" . htmlspecialchars($row['student_name']) . "</td>
            <td>" . htmlspecialchars($row['reg_no']) . "</td>
            <td>{$inst}</td>
            <td><strong>₹" . number_format($row['amount'] / 100, 2) . "</strong></td>
            <td><small>{$row['razorpay_payment_id']}</small></td>
            <td>" . date('d M Y, h:i A', strtotime($row['created_at'])) . "</td>
            <td><span class='status-badge {$statusClass}'>{$status}</span></td>
        </tr>";

        $i++;
    }
} else {
    echo "<tr>
        <td colspan='8' class='text-center text-muted'>No records found</td>
    </tr>";
}
