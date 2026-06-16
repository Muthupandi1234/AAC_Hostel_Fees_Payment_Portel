<?php
// Simple autoloader for Razorpay and other libraries
spl_autoload_register(function ($class) {
    // Razorpay namespace mapping
    if (strpos($class, 'Razorpay\\') === 0) {
        $file = __DIR__ . '/../razorpay-php/src/' . str_replace('\\', '/', substr($class, 9)) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
    
    // Mpdf namespace mapping (for PDF generation)
    if (strpos($class, 'Mpdf\\') === 0) {
        // Fallback - mPDF not available, silently return
        // DOMPDF or HTML table fallback will be used instead
        return;
    }
});

// Load Razorpay main class if it exists
$razorpayPath = __DIR__ . '/../razorpay-php/Razorpay.php';
if (file_exists($razorpayPath)) {
    require_once $razorpayPath;
}
?>
