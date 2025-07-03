<?php
session_start();
include 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

$sql = "SELECT r.*, res.name AS restaurant_name, rt.table_number
        FROM reservations r
        JOIN restaurants res ON r.restaurant_id = res.id
        JOIN restaurant_tables rt ON r.table_id = rt.id
        WHERE r.customer_id = ?
        ORDER BY r.reservation_date DESC, r.reservation_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Reservations</title>
    <link rel="stylesheet" href="../css/cdash1.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            margin: 0;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            margin: 0;
        }

        .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    background-color: #ffffff;
    border-bottom: 4px solid #e0e0e0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); /* ✨ Shadow effect */
    position: sticky;
    top: 0;
    z-index: 999;
}


        .navbar .logo {
            font-weight: 600;
            font-size: 20px;
            color: #009688;
        }

        .navbar a {
            color: #333;
            margin-left: 24px;
            text-decoration: none;
        }

        .navbar .btn {
            background: #009688;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
        }
        .reservation-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 15px 0;
            padding: 15px;
            background: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .cancel-btn {
            background: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .cancel-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="customer_dashboard.php" class="logo" style="display: flex; align-items: center; text-decoration: none;">
        <img src="../images/logo-dark.svg" alt="Logo" style="height: 28px; margin-right: 10px;">
        <span style="font-weight: 600; font-size: 20px; color: #009688;">MySpot</span>
    </a>
    <div>
        <a href="restaurants.php">Find Restaurants</a>
        <a href="#"><i class="fa fa-map-marker-alt"></i> haldwani, Uttarakhand</a>
        <a href="my_reservations.php">Reservations</a>
        <a href="../index.html" class="btn">Sign Out</a>
    </div>
</div>

<?php if (isset($_GET['cancel']) && $_GET['cancel'] === 'success'): ?>
<script>
    window.onload = function () {
        const toast = document.createElement('div');
        toast.innerText = "Reservation cancelled successfully.";
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.background = '#28a745';
        toast.style.color = '#fff';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '8px';
        toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
        toast.style.zIndex = 1000;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>
<?php endif; ?>

<div class="container">
    <h2>My Reservations</h2>

    <?php while ($row = $result->fetch_assoc()):
        $reservation_datetime = strtotime($row['reservation_date'] . ' ' . $row['reservation_time']);
        $now = time();
        $hours_diff = ($reservation_datetime - $now) / 3600;
        $can_cancel = $hours_diff > 24;
    ?>
    <div class="reservation-card">
        <h3><?= htmlspecialchars($row['restaurant_name']) ?></h3>

        <p>
            <strong>Status:</strong>
            <?php if ($row['status'] === 'cancelled'): ?>
                <span style="color: red; font-weight: bold;">Cancelled</span>
            <?php elseif ($row['status'] === 'confirmed'): ?>
                <span style="color: green; font-weight: bold;">Confirmed</span>
            <?php else: ?>
                <span style="color: orange; font-weight: bold;"><?= ucfirst($row['status']) ?></span>
            <?php endif; ?>
        </p>

        <p><strong>Date:</strong> <?= $row['reservation_date'] ?> | <strong>Time:</strong> <?= date('h:i A', strtotime($row['reservation_time'])) ?></p>
        <p><strong>People:</strong> <?= $row['party_size'] ?> | <strong>Table #:</strong> <?= $row['table_number'] ?></p>

        <?php if (!empty($row['special_message'])): ?>
            <p><strong>Instructions:</strong> <?= htmlspecialchars($row['special_message']) ?></p>
        <?php endif; ?>

        <?php if ($row['status'] !== 'cancelled' && $can_cancel): ?>
            <form method="POST" action="cancel_reservation.php" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                <input type="hidden" name="reservation_id" value="<?= $row['id'] ?>">
                <button class="cancel-btn" type="submit">Cancel Reservation</button>
            </form>
        <?php elseif ($row['status'] !== 'cancelled'): ?>
            <p style="color: gray;"><em>Cannot cancel within 24 hours of reservation time.</em></p>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>
</body>
</html>
