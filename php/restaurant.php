<?php
include 'db.php';
session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Invalid restaurant ID.";
    exit;
}


$sql = "SELECT * FROM restaurants WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows === 0) {
    echo "Restaurant not found.";
    exit;
}

$restaurant = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($restaurant['name']); ?> - MySpot</title>
  <link rel="stylesheet" href="../css/restaurant_profile.css">
  <link rel="stylesheet" href="../css/cdash1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="banner">
  <img src="../uploads/<?= htmlspecialchars($restaurant['image']); ?>" alt="Restaurant Banner">
</div>

<div class="restaurant-profile-container">
  <div class="restaurant-info-section">
    <h1><?= htmlspecialchars($restaurant['name']); ?></h1>
    <p class="meta"><?= htmlspecialchars($restaurant['cuisine']); ?></p>
    <p class="desc"><?= htmlspecialchars($restaurant['description']); ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($restaurant['address']); ?></p>
    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($restaurant['phone'] ?? '+91-999-999-9999'); ?></p>
    <p><i class="far fa-clock"></i>
      <?= date('h:i A', strtotime($restaurant['opening_time'])) . " - " . date('h:i A', strtotime($restaurant['closing_time'])); ?>
    </p>
  </div>

  <div class="reservation-box">
    <h3>Make a Reservation</h3>
    <form action="select_table.php" method="get">
      <input type="hidden" name="restaurant_id" value="<?= $restaurant['id']; ?>">

      <label>Date</label>
      <input type="date" name="date" required min="<?= date('Y-m-d'); ?>">

      <label>Time</label>
      <select name="time" required>
        <?php
        $opening = new DateTime($restaurant['opening_time']);
        $closing = new DateTime($restaurant['closing_time']);
        if ($closing <= $opening) $closing->modify('+1 day');

        $interval = new DateInterval('PT30M');
        $lastReservation = clone $closing;
        $lastReservation->modify('-30 minutes');
        $period = new DatePeriod($opening, $interval, $lastReservation);

        foreach ($period as $time) {
            echo "<option value='" . $time->format('H:i:s') . "'>" . $time->format('h:i A') . "</option>";
        }
        ?>
      </select>

      <label>Party Size</label>
      <select name="people" required>
        <?php for ($i = 1; $i <= 10; $i++) echo "<option value='$i'>$i people</option>"; ?>
      </select>

      <button type="submit">Reserve a Table</button>
    </form>
  </div>
</div>

<?php $restaurant_id = $id; include 'restaurant_menu.php'; ?>
<?php include 'footer.php'; ?>
</body>
</html>
<script>
window.addEventListener('scroll', function () {
  const navbar = document.getElementById('main-navbar');
  const lightLogo = document.querySelector('.logo-img.light');
  const darkLogo = document.querySelector('.logo-img.dark');
  const links = document.querySelectorAll('.nav-links a');
  const locationText = document.querySelector('.location');
  const brandText = document.querySelector('.brand-name');

  if (window.scrollY > 20) {
    navbar.classList.add('scrolled');
    lightLogo.style.display = 'none';
    darkLogo.style.display = 'block';
    links.forEach(link => link.style.color = '#333');
    locationText.style.color = '#555';
    brandText.style.color = '#333';
  } else {
    navbar.classList.remove('scrolled');
    lightLogo.style.display = 'block';
    darkLogo.style.display = 'none';
    links.forEach(link => link.style.color = '#fff');
    locationText.style.color = '#eee';
    brandText.style.color = '#fff';
  }
});
</script>