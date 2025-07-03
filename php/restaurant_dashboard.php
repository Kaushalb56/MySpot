<?php
session_start();
include "db.php";
$restaurant_id = $_SESSION['restaurant_id'];
$sql = "SELECT * FROM restaurant_tables WHERE restaurant_id = ?";
// $rea
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

$sql_restaurant = "SELECT name FROM restaurants WHERE id = ?";
$stmt_rest = $conn->prepare($sql_restaurant);
$stmt_rest->bind_param("i", $restaurant_id);
$stmt_rest->execute();
$result_rest = $stmt_rest->get_result();
$restaurant = $result_rest->fetch_assoc();

$restaurant_name = $restaurant['name'];
$restaurant_initial = strtoupper(substr($restaurant_name, 0, 1));


$sql_reservation = "SELECT COUNT(id) AS total FROM reservations WHERE restaurant_id =? AND status ='confirmed'";
$stmt_reser = $conn->prepare($sql_reservation);
$stmt_reser->bind_param("i", $restaurant_id);
$stmt_reser->execute();
$result_reser = $stmt_reser->get_result();
$today_reservations = $result_reser->fetch_assoc();

$sql_reservations = "SELECT COUNT(id) AS total FROM reservations WHERE restaurant_id =? AND status ='pending'";
$stmt_resers = $conn->prepare($sql_reservations);
$stmt_resers->bind_param("i", $restaurant_id);
$stmt_resers->execute();
$result_resers = $stmt_resers->get_result();
$new_notifications = $result_resers->fetch_assoc();

// Logic Add kr lena 
$available_tables = 0;
$occupied_tables = 0;
$reserved_tables = 0;
$all_tables = array();

while ($row = $result->fetch_assoc()) {
    $all_tables[] = $row;
    if ($row['status'] == 0) {
        $available_tables++;
    } elseif ($row['status'] == 1) {
        $occupied_tables++;
    } else {
        $reserved_tables++;
    }
}
// Aur yaha pr bhi kuch logic add kr lena
$today_revenue = 0.00;

$unpaid_orders = 2;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySpot Restaurant Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../php/Dashboard/css/dash.css">
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="../images/MySpot_res.svg" alt="MySpot Logo" style="width: 36px; height: 36px;">
            <span>MySpot</span>
        </div>
        <div class="nav-list" style="display: flex; flex-direction: column; min-height: 100vh;">
    <!-- Top Navigation Items -->
    <div>
        <a href="restaurant_dashboard.php" class="nav-item active">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="./Table/all-table.php" class="nav-item">
            <i class="fas fa-chair"></i>
            <span>Tables</span>
        </a>
        <a href="menu_manage.php" class="nav-item">
            <i class="fas fa-utensils"></i>
            <span>Menu</span>
        </a>
        <a href="restaurant_reservations.php" class="nav-item">
            <i class="fas fa-calendar-alt"></i>
            <span>Reservations</span>
        </a>
        <a href="manager_profile.php" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Profile</span>
        </a>
    </div>

    <!-- Logout Button with Padding -->
    <div style="margin-top: auto; padding-bottom: 40px;">
        <a href="logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>



    </div>

    <div class="main-content" id="main-content">
        <div class="header">
            <button class="toggle-btn" id="toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="user-info" style="margin-left: auto;">
    <a href="manager_profile.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
        <span style="margin-right: 10px;"><?php echo htmlspecialchars($restaurant_name); ?></span>
        <div class="user-avatar"><span><?php echo $restaurant_initial; ?></span></div>
    </a>
</div>

        </div>

        <h1 class="dashboard-title" style="font-size: 22px;">Dashboard</h1>
        <p class="dashboard-subtitle">Welcome to MySpot restaurant management</p>

        <div class="stats-container">
            <div class="stat-card available">
                <div class="stat-icon available">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $available_tables; ?></h3>
                    <p>Available Tables</p>
                </div>
            </div>
            
            <div class="stat-card reserved">
                <div class="stat-icon reserved">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $reserved_tables; ?></h3>
                    <p>Reserved Tables</p>
                </div>
            </div>
            
            <div class="stat-card occupied">
                <div class="stat-icon occupied">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $occupied_tables; ?></h3>
                    <p>Occupied Tables</p>
                </div>
            </div>
            
            <div class="stat-card revenue">
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>Rs.<?php echo number_format($today_revenue, 2); ?></h3>
                    <p>Today's Revenue</p>
                </div>
            </div>
        </div>

        <div class="dashboard-layout">
            <div class="left-column">
                <!-- Table Status -->
                <div class="section-title">
                    <h2>Table Status</h2>
                </div>
                
                <div class="tables-grid">
                    <?php foreach ($all_tables as $table): 
                        if ($table['status'] == 0) {
                            $status = "available";
                            $status_text = "AVAILABLE";
                        } elseif ($table['status'] == 1) {
                            $status = "occupied";
                            $status_text = "OCCUPIED";
                        } else {
                            $status = "reserved";
                            $status_text = "RESERVED";
                        }
                    ?>
                    <div class="table-card <?php echo $status; ?>">
                        <div class="table-icon">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="table-number">Table <?php echo $table['table_number']; ?></div>
                        <div class="table-seats">
                            <i class="fas fa-chair"></i> <?php echo $table['seats']; ?> seats
                        </div>
                        <div class="status-label <?php echo $status; ?>">
                            <?php echo $status_text; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="right-column">
                <!-- Today's Overview -->
                <div class="section-title">
                    <h2>Total Overview</h2>
                </div>
                
                <div class="overview-container">
                    <div class="overview-card reservations">
                        <div class="overview-icon reservations">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="overview-info">
                            <h3><?php echo $today_reservations['total']; ?></h3>
                            <p>Reservations</p>
                        </div>
                    </div>
                    
                    <div class="overview-card orders">
                        <div class="overview-icon orders">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="overview-info">
                            <h3><?php echo $today_reservations['total']; ?></h3>
                            <p>Unpaid Orders</p>
                        </div>
                    </div>
                    
                    <div class="overview-card notifications">
                        <div class="overview-icon notifications">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="overview-info">
                            <h3><?php echo $new_notifications['total']; ?></h3>
                            <p>New Notifications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
        });
        
        function checkWidth() {
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('expanded');
            } else {
                sidebar.classList.add('expanded');
            }
        }
        checkWidth();
        window.addEventListener('resize', checkWidth);
    </script>
</body>
</html>