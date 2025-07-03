<?php
include 'db.php';
session_start();


// At the very top of customer_dashboard.php, after session_start() if any
$reservationSuccess = false;
$showWelcomeToast = false;
$userName = 'Guest';

if (isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
    // Only show welcome toast once per session
    if (!isset($_SESSION['welcome_shown'])) {
        $showWelcomeToast = true;
        $_SESSION['welcome_shown'] = true;
    }
}

if (isset($_GET['reservation']) && $_GET['reservation'] === 'success') {
    $reservationSuccess = true;
}



date_default_timezone_set('Asia/Kolkata'); // Set to your local timezone

// Fetch restaurants
$sql = "SELECT id, name, image, state_location, cuisine, opening_time, closing_time, address FROM restaurants ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);
$featured_restaurants = [];

while ($row = $result->fetch_assoc()) {
    $current_time = new DateTime();
    $opening_time = new DateTime($row['opening_time']);
    $closing_time = new DateTime($row['closing_time']);

    $is_open = false;

    if ($closing_time > $opening_time) {
        // Normal hours (e.g. 10 AM - 10 PM)
        $is_open = $current_time >= $opening_time && $current_time <= $closing_time;
    } else {
        // Overnight (e.g. 7 PM - 2 AM)
        if ($current_time >= $opening_time || $current_time <= $closing_time) {
            $is_open = true;
        }
    }

    $row['is_open'] = $is_open;
    $featured_restaurants[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MySpot - Find Restaurants</title>
    <link rel="stylesheet" href="../css/cdash1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>


<?php include 'header.php'; ?>

<div id="toast" class="toast"></div>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-overlay">
        <h1>Reserve Your Perfect Table</h1>
        <p>Discover and book the best restaurants in your area. Pre-order your meal and skip the wait.</p>
        <form class="search-bar" action="restaurants.php" method="GET">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="q" placeholder="Search for restaurants, cuisines, or dishes">
            </div>
            <div class="location-box">
                <i class="fas fa-map-marker-alt"></i>
                <select name="location">
                    <option value="">Select Location</option>
                    <option value="New Delhi">New Delhi</option>
                    <option value="Mumbai">Mumbai</option>
                    <option value="Bengaluru">Bangalore</option>
                    <option value="Dehradun">Dehradun</option>
                    <option value="Haldwani">Haldwani</option>
                </select>
            </div>
            <button type="submit">Search</button>
        </form>
    </div>
</section>

<!-- Featured Restaurants -->
<section class="featured">
  <div class="featured-header">
    <h2>Featured Restaurants</h2>
    <a href="restaurants.php" class="view-all">View all <i class="fas fa-arrow-right"></i></a>
  </div>
 <div class="restaurant-grid">
  <?php foreach ($featured_restaurants as $res): ?>
  <a href="restaurant.php?id=<?php echo $res['id']; ?>" class="restaurant-card-link"style="text-decoration: none;">
    <div class="restaurant-card">
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
    </div>
  </a>
<?php endforeach; ?>
<div>
  
</section>

<?php include 'footer.php'; ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast');

    <?php if ($reservationSuccess): ?>
      toast.textContent = "Reservation completed successfully! Thank you for booking.";
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
      }, 4000);
    <?php elseif ($showWelcomeToast): ?>
      toast.textContent = "Welcome, <?= htmlspecialchars($userName) ?>!";
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
      }, 4000);
    <?php endif; ?>
  });
</script>


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
