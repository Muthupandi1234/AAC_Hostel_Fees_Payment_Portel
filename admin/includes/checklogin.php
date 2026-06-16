<?php
if (!function_exists('check_admin_login')) {
    function check_admin_login()
    {
        if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
            header("Location: index.php");
            exit();
        }
    }
}
?>


