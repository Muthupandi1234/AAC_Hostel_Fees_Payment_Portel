<?php
if (!function_exists('check_login')) {
    function check_login()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }
    }
}
?>
