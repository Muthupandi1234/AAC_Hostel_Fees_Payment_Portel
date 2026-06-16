<?php
if (!isset($_GET['admission_id'])) {
    die("Invalid access");
}

$admission_id = intval($_GET['admission_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admission Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow text-center">
                <div class="card-body">
                    <h3 class="text-success"><i class="fas fa-check-circle"></i> Admission Successful</h3>
                    <p class="mt-3">Your payment was successful.</p>
                    <p class="text-muted">Your receipt will be downloaded automatically…</p>
                    <div class="spinner-border text-success mt-3 mb-4"></div>
                    
                    <div class="mt-4">
                        <a href="receipt.php?admission_id=<?= $admission_id ?>" class="btn btn-success me-2">
                            <i class="fas fa-download"></i> Download Receipt
                        </a>
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Go to Home
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Auto download receipt and redirect to index after 3 seconds
    window.addEventListener('load', function() {
        setTimeout(function() {
            // Create hidden iframe to download PDF
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = 'receipt.php?admission_id=<?= $admission_id ?>';
            document.body.appendChild(iframe);
            
            // Redirect to index after 3 seconds
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 3000);
        }, 500);
    });
</script>

</body>
</html>
