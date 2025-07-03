<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<div class="sidebar" id="sidebar">
        <div class="logo">
            <span>MySpot</span>
        </div>
        <div class="nav-list">
            <a href="restaurant_dashboard.php" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="table_manage.php" class="nav-item">
                <i class="fas fa-chair"></i>
                <span>Tables</span>
            </a>
            <a href="menu_manage.php" class="nav-item">
                <i class="fas fa-utensils"></i>
                <span>Menu</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Reservations</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                <?php if ($new_notifications > 0): ?>
                <span class="badge"><?php echo $new_notifications; ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-credit-card"></i>
                <span>Payments</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
    </div>
    
    <div class="main-content" id="main-content">
        <div class="header">
            <button class="toggle-btn" id="toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user-avatar"><span>M</span></div>
                <span style="margin-left: 10px;">Manager</span>
            </div>
        </div>