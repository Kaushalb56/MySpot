<?php
include 'db.php';
session_start();

date_default_timezone_set('Asia/Kolkata'); // Adjust to your server's/local timezone

$q = $_GET['q'] ?? '';
$location = $_GET['location'] ?? '';

// Build SQL query
$sql = "SELECT id, name, image, state_location, cuisine, opening_time, closing_time, address FROM restaurants WHERE 1";
if (!empty($q)) {
    $q = $conn->real_escape_string($q);
    $sql .= " AND (name LIKE '%$q%' OR cuisine LIKE '%$q%' OR address LIKE '%$q%')";
}
if (!empty($location)) {
    $location = $conn->real_escape_string($location);
    $sql .= " AND state_location = '$location'";
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);
$restaurants = [];

while ($row = $result->fetch_assoc()) {
    $now = new DateTime();
$opening = DateTime::createFromFormat('H:i:s', $row['opening_time']);
$closing = DateTime::createFromFormat('H:i:s', $row['closing_time']);

$is_open = false;
if ($opening && $closing) {
    // Set today's date for opening and closing
    $opening->setDate($now->format('Y'), $now->format('m'), $now->format('d'));
    $closing->setDate($now->format('Y'), $now->format('m'), $now->format('d'));

    if ($closing <= $opening) {
        // Overnight closing (e.g., 22:00 - 04:00)
        $closing->modify('+1 day');
    }

    $is_open = ($now >= $opening && $now <= $closing);
}


    $row['is_open'] = $is_open;
    $restaurants[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Restaurants - MySpot</title>
  <link rel="stylesheet" href="../css/cdash1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay">
    <h1>Find the Best Restaurants</h1>
    <p>Explore restaurants, filter by location and discover your next favorite meal!</p>
    <form class="search-bar" action="restaurants.php" method="GET">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search for restaurants, cuisines, or dishes">
      </div>
      <div class="location-box">
        <i class="fas fa-map-marker-alt"></i>
        <select name="location">
          <option value="">Select Location</option>
          <?php
          $locations = ['New Delhi', 'Mumbai', 'Bangalore', 'Dehradun', 'Haldwani'];
          foreach ($locations as $loc) {
              $selected = ($location === $loc) ? 'selected' : '';
              echo "<option value=\"$loc\" $selected>$loc</option>";
          }
          ?>
        </select>
      </div>
      <button type="submit">Search</button>
    </form>
  </div>
</section>

<!-- Restaurant List -->
<section class="featured">
  <div class="featured-header">
    <h2><?php echo count($restaurants); ?> restaurants found</h2>
  </div>

  <div class="restaurant-grid">
    <?php if (count($restaurants) === 0): ?>
      <p style="padding: 20px;">No restaurants match your criteria.</p>
    <?php else: ?>
      <?php foreach ($restaurants as $res): ?>
        <div class="restaurant-card" >
          <a href="restaurant.php?id=<?php echo $res['id']; ?>"style="text-decoration: none;">
            <div class="restaurant-image">
              <img src="../uploads/<?php echo htmlspecialchars($res['image']); ?>" alt="<?php echo htmlspecialchars($res['name']); ?>">
            </div>
            <div class="restaurant-info">
              <h3><?php echo htmlspecialchars($res['name']); ?></h3>
              <p class="cuisine"><?php echo htmlspecialchars($res['cuisine']); ?></p>
              <p class="location">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo htmlspecialchars($res['address']); ?>
              </p>
              <p class="status <?php echo $res['is_open'] ? 'open' : 'closed'; ?>">
                <i class="fas fa-circle"></i> <?php echo $res['is_open'] ? 'Open Now' : 'Closed Now'; ?>
              </p>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

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
    if (lightLogo) lightLogo.style.display = 'none';
    if (darkLogo) darkLogo.style.display = 'block';
    links.forEach(link => link.style.color = '#333');
    if (locationText) locationText.style.color = '#555';
    if (brandText) brandText.style.color = '#333';
  } else {
    navbar.classList.remove('scrolled');
    if (lightLogo) lightLogo.style.display = 'block';
    if (darkLogo) darkLogo.style.display = 'none';
    links.forEach(link => link.style.color = '#fff');
    if (locationText) locationText.style.color = '#eee';
    if (brandText) brandText.style.color = '#fff';
  }
});
</script>
