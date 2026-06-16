<!-- Admin Sidebar -->
<style>
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        width: 260px;
        background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 20px 0 0 0;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        z-index: 1000;
        overflow-y: auto;
    }
    
    .sidebar-logo {
        text-align: center;
        padding: 15px 0;
    }
    
    .sidebar-logo img {
        max-width: 80px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    
    .sidebar-header {
        padding: 0 25px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 20px;
    }
    
    .sidebar-header h3 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
    }
    
    .sidebar-menu li {
        margin: 5px 0;
    }
    
    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 25px;
        color: rgba(255,255,255,0.9);
        text-decoration: none;
        transition: all 0.3s;
        font-size: 14px;
    }
    
    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: rgba(255,255,255,0.1);
        color: white;
        border-left: 3px solid #fff;
    }
    
    .sidebar-menu a i {
        width: 20px;
        margin-right: 12px;
    }

    .main-content {
        margin-left: 260px;
        padding: 30px;
        min-height: 100vh;
    }
/* ===== MOBILE SIDEBAR REDESIGN ===== */
@media (max-width: 768px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        display: flex;
        flex-direction: column;
        padding: 10px 0;
    }

    .sidebar-menu {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px;
        gap: 8px;

        /* Hide Scrollbar Mobile Only */
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .sidebar-menu::-webkit-scrollbar {
        display: none;
    }

    .sidebar-menu li {
        margin: 0;
    }

    .sidebar-menu a {
        padding: 8px 15px;
        font-size: 13px;
        border-radius: 20px;
        background: rgba(255,255,255,0.1);
        border-left: none;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: white;
        color: #1e3c72;
        font-weight: 600;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}
</style>

<div class="sidebar">
    <div class="sidebar-logo">
        <img src="../images/aac_hostel.jpeg" alt="Logo">
    </div>
    <div class="sidebar-header">
        <h3><i class="fas fa-user-shield"></i> Admin Panel</h3>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="students.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'students.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Students</a></li>
        <!-- <li><a href="new_admissions.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'new_admissions.php' ? 'active' : ''; ?>"><i class="fas fa-user-plus"></i> New Admissions</a></li> -->
        <li><a href="payments.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'payments.php' ? 'active' : ''; ?>"><i class="fas fa-credit-card"></i> Payments</a></li>
        <li><a href="receipts.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'receipts.php' ? 'active' : ''; ?>"><i class="fas fa-file-invoice"></i> Receipts</a></li>
        <li><a href="payment_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'payment_reports.php' ? 'active' : ''; ?>"><i class="fas fa-file-download"></i> Payment Reports</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

