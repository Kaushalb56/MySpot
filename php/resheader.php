<?php
include "db.php";

$restaurant_id = $_SESSION['restaurant_id'];

// Fetch restaurant name
$sql_restaurant = "SELECT name FROM restaurants WHERE id = ?";
$stmt_rest = $conn->prepare($sql_restaurant);
$stmt_rest->bind_param("i", $restaurant_id);
$stmt_rest->execute();
$result_rest = $stmt_rest->get_result();
$restaurant = $result_rest->fetch_assoc();

$restaurant_name = $restaurant['name'];
$restaurant_initial = strtoupper(substr($restaurant_name, 0, 1));
?>

<!-- resheader.php -->
<style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

/* Header container */
.header {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    background-color: #fff;
    padding: 10px 20px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

/* Toggle button */
.toggle-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    margin-right: auto;
}

/* User info container */
.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

/* Notification bell icon */
.notification-icon {
    position: relative;
    font-size: 20px;
    color: #666;
    cursor: pointer;
}

.notification-icon:hover {
    color: #000;
}

/* User avatar circle with initial */
.user-avatar {
    width: 36px;
    height: 36px;
    background-color: #007bff;
    color: white;
    font-weight: bold;
    font-size: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;
}

/* Username text */
.user-info span {
    font-weight: 600;
    font-size: 16px;
    user-select: none;
}
</style>

<div class="header">
    <button class="toggle-btn" id="toggle-btn">
        <i class="fas fa-bars"></i>
    </button>
    <div class="user-info">
        <div class="notification-icon">
            <i class="fas fa-bell"></i>
        </div>
        <a href="manager_profile.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
            <div class="user-avatar"><span><?php echo $restaurant_initial; ?></span></div>
            <span style="margin-left: 10px;"><?php echo htmlspecialchars($restaurant_name); ?></span>
        </a>
    </div>
</div>

<script>
    // You can keep this toggle script if needed in pages that include the header
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');

    if(toggleBtn){
        toggleBtn.addEventListener('click', () => {
            if(sidebar) sidebar.classList.toggle('expanded');
        });

        function checkWidth() {
            if (window.innerWidth <= 992) {
                if(sidebar) sidebar.classList.remove('expanded');
            } else {
                if(sidebar) sidebar.classList.add('expanded');
            }
        }
        checkWidth();
        window.addEventListener('resize', checkWidth);
    }
</script>
