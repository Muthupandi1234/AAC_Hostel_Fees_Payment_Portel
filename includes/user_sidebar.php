<!-- User Sidebar -->
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
    transition: transform 0.3s ease; /* add this */
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

    /* Mobile Sidebar System */
.menu-toggle {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    background: #1e3c72;
    color: white;
    border: none;
    padding: 8px 12px;
    font-size: 18px;
    border-radius: 6px;
    z-index: 1100;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 999;
}

@media (max-width: 768px) {

    .menu-toggle {
        display: block;
    }

    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .sidebar-overlay.active {
        display: block;
    }

    .main-content {
        margin-left: 0;
        padding: 20px;
    }
}
</style>

<button class="menu-toggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="sidebar">

    <div class="sidebar-logo">
        <img src="images/aac_hostel.jpeg" alt="AAC Hostel" style="margin-top: 10px;">
    </div>

    <div class="sidebar-header">
        <h3><i class="fas fa-home"></i> <?php echo SITE_NAME; ?></h3>
    </div>

    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="my-profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'my-profile.php' ? 'active' : ''; ?>"><i class="fas fa-user"></i> My Profile</a></li>
        <li><a href="admission.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admission.php' ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i> Admission</a></li>
        <li><a href="fees-payment.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'fees-payment.php' ? 'active' : ''; ?>"><i class="fas fa-credit-card"></i> Fees Payment</a></li>
        <li><a href="fees-history.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'fees-history.php' ? 'active' : ''; ?>"><i class="fas fa-history"></i> Fees History</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>

</div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
}
</script>
</div>
