<?php
session_start();
include "db.php";

if (!isset($_SESSION['restaurant_id'])) {
    header("Location: login.php");
    exit();
}

$restaurant_id = $_SESSION['restaurant_id'];
//echo "Restaurant ID: " . $restaurant_id;

$sql = "
    SELECT r.*, 
           c.name AS customer_name, 
           c.email AS customer_email, 
           c.phone AS customer_phone,
           rt.table_number
    FROM reservations r
    LEFT JOIN customers c ON r.customer_id = c.id
    LEFT JOIN restaurant_tables rt ON r.table_id = rt.id
    WHERE r.restaurant_id = ?
    ORDER BY r.reservation_date DESC, r.reservation_time ASC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();
$reservations = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservations - MySpot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../php/Dashboard/css/dash.css">
    <style>
        
        .reservation-cards {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 10px 0;
}

.reservation-card {
    background-color:rgb(236, 236, 236); /* light gray */
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.card-header {
    cursor: pointer;
}


.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
}

.card-body p {
    margin: 6px 0;
    font-size: 14px;
}

.card-actions {
    margin-top: 12px;
}

.card-actions a,
.card-actions span {
    margin-right: 8px;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;
    text-decoration: none;
    color: white;
}

.card-actions .btn-confirm { background-color: #2ecc71; }
.card-actions .btn-cancel { background-color: #e74c3c; }
.card-actions .btn-disabled {
    background-color: #bdc3c7;
    pointer-events: none;
}

        .status-badge {
            padding: 6px 10px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        .status-badge.pending { background-color: #f39c12; }
        .status-badge.confirmed { background-color: #27ae60; }
        .status-badge.cancelled { background-color: #e74c3c; }

        .action-buttons a {
            margin-right: 8px;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
            text-decoration: none;
            color: white;
        }

        .btn-confirm { background-color: #2ecc71; }
        .btn-cancel { background-color: #e74c3c; }
        .btn-disabled {
            background-color: #bdc3c7;
            pointer-events: none;
        }

        .section-title h2 {
            margin: 20px 0 10px 0;
        }
        .toggle-arrow {
    cursor: pointer;
    font-size: 14px;
}

.collapsible {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, padding 0.3s ease;
    padding-top: 0;
    padding-bottom: 0;
}

.collapsible.open {
    max-height: 300px; /* Enough to show all */
    padding-top: 10px;
    padding-bottom: 10px;
}

.filter-buttons {
    display: flex;
    gap: 10px;
}

.filter-buttons button {
    padding: 8px 16px;
    border: none;
    background-color:#8e2441;
    color: white;
    font-size: 14px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.2s ease;
}

.filter-buttons button:hover {
    background-color:rgb(56, 20, 15);
}

.filter-buttons button.active {
    background-color: #2c3e50;
}

/* Wrap sidebar and content in flex layout */
.layout-wrapper {
    display: flex;
    min-height: 100vh;
}

/* Fix spacing issues for main content */
#main-content {
    flex: 1;
    padding: 30px 40px;
    background-color: #fff;
}

    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="../images/MySpot_res.svg" alt="MySpot Logo" style="width: 36px; height: 36px;">
            <span>MySpot</span>
        </div>
        <div class="nav-list">
            <a href="restaurant_dashboard.php" class="nav-item">
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
            <a href="restaurant_reservations.php" class="nav-item active">
                <i class="fas fa-calendar-alt"></i>
                <span>Reservations</span>
            </a>
            </a>
            <a href="manager_profile.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Profile</span>
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
            </div>
        </div>

        <h1 class="dashboard-title" style="font-size: 22px;">Reservations</h1>
        <p class="dashboard-subtitle">Manage all customer reservations here</p>

        <div class="section-title">
            <h2>Reservation List</h2>
        </div>

       <div class="filter-buttons" style="margin-bottom: 20px;">
    <button onclick="filterReservations('all')">All</button>
    <button onclick="filterReservations('confirmed')">Confirmed</button>
    <button onclick="filterReservations('cancelled')">Cancelled</button>
    <button onclick="filterReservations('pending')">Pending</button>
</div>

<div class="reservation-cards" id="reservationList">
    <?php if (count($reservations) === 0): ?>
        <p>No reservations found.</p>
    <?php else: ?>
        <?php foreach ($reservations as $row): ?>
            <?php 
                $status = $row['status'] ?? 'pending'; // default to 'pending' if null
                $status = strtolower(trim($status));
            ?>
            <div class="reservation-card" data-status="<?php echo htmlspecialchars($status); ?>">
                <div class="card-header" onclick="toggleDetails('details-<?php echo $row['id']; ?>', 'arrow-<?php echo $row['id']; ?>')">
                    <h3>
                        <?php echo htmlspecialchars($row['customer_name']); ?> 
                        - <?php echo date('D, M j, Y', strtotime($row['reservation_date'])); ?> 
                        - <?php echo $row['party_size']; ?> guest<?php echo $row['party_size'] > 1 ? 's' : ''; ?>
                    </h3>
                    <i id="arrow-<?php echo $row['id']; ?>" class="fas fa-chevron-down toggle-arrow"></i>
                </div>

                <div class="card-body collapsible" id="details-<?php echo $row['id']; ?>">
                    <hr>
                    <h4>Contact Information</h4>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['customer_phone'] ?? 'N/A'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['customer_email'] ?? 'N/A'); ?></p>
                    <hr>
                    <h4>Table Assignment</h4>
                    <p>Table: <?php echo htmlspecialchars($row['table_number'] ?? 'N/A'); ?></p>
                    <hr>
                    <h4>Special Requests</h4>
                    <p><?php echo nl2br(htmlspecialchars($row['special_message'] ?? 'None')); ?></p>

                    <div class="card-actions">
                        <?php if ($status === 'pending'): ?>
                            <a href="update_reservation_status.php?id=<?php echo $row['id']; ?>&status=confirmed" class="btn-confirm">Confirm</a>
                            <a href="update_reservation_status.php?id=<?php echo $row['id']; ?>&status=cancelled" class="btn-cancel">Cancel</a>
                        <?php else: ?>
                            <span class="btn-disabled"><?php echo ucfirst($status); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
    </div>
</div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        toggleBtn.addEventListener('click', function () {
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
      
    function toggleDetails(id, arrowId) {
        const content = document.getElementById(id);
        const arrow = document.getElementById(arrowId);

        content.classList.toggle('open');

        if (content.classList.contains('open')) {
            arrow.style.transform = "rotate(180deg)";
        } else {
            arrow.style.transform = "rotate(0deg)";
        }
    }

    function filterReservations(status) {
    const cards = document.querySelectorAll('.reservation-card');
    const buttons = document.querySelectorAll('.filter-buttons button');

    // Apply filtering
    cards.forEach(card => {
        if (status === 'all' || card.getAttribute('data-status') === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    // Highlight the active button
    buttons.forEach(btn => btn.classList.remove('active'));
    const activeBtn = Array.from(buttons).find(btn => btn.textContent.toLowerCase() === status);
    if (activeBtn) activeBtn.classList.add('active');
}


    </script>
</body>
</html>
