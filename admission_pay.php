<?php
include('includes/config.php');


if (!isset($_GET['admission_id'])) {
    die("Invalid Access");
}

$admission_id = intval($_GET['admission_id']);

// Get admission details
$result = $mysqli->query("SELECT * FROM new_admission WHERE id = $admission_id");
$admission = $result->fetch_assoc();

if (!$admission) {
    die("Admission not found");
}

$amount = $admission['amount'] * 100; // Razorpay expects paise
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pay Admission Fee</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body onload="payNow()">

<script>
function payNow() {
    var options = {
        "key": "<?= RAZORPAY_KEY_ID ?>",
        "amount": "<?= $amount ?>",
        "currency": "INR",
        "name": "AAC Hostel",
        "description": "Hostel Admission Fee",
        "image": "",
        "handler": function (response){
            // send payment data to callback
            var form = document.createElement("form");
            form.method = "POST";
            form.action = "razorpay_callback.php";

            var fields = {
                razorpay_payment_id: response.razorpay_payment_id,
                admission_id: "<?= $admission_id ?>"
            };

            for (var key in fields) {
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        },
        "prefill": {
            "name": "<?= $admission['student_name'] ?>",
            "contact": "<?= $admission['phone'] ?>"
        },
        "modal": {
            "ondismiss": function(){
                // Redirect to admission form when user clicks "Yes, exit"
                window.location.href = "admission_form.php";
            }
        },
        "theme": {
            "color": "#3399cc"
        }
    };
    var rzp = new Razorpay(options);
    rzp.open();
}
</script>

</body>
</html>
