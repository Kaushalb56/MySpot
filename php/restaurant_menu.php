<?php
// restaurant_menu.php
if (!isset($restaurant_id) || $restaurant_id <= 0) {
    echo "<p>Invalid restaurant ID for menu.</p>";
    return;
}

include 'db.php';

$sql = "SELECT category, id, name, description, price, is_available, image 
        FROM menu_items 
        WHERE restaurant_id = ? 
        ORDER BY category, name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

$menu_by_category = [];
while ($row = $result->fetch_assoc()) {
    $menu_by_category[$row['category']][] = $row;
}
?>

<style>
/* Keep styles consistent with restaurant.php theme */

.restaurant-menu {
    max-width: 900px;
    margin: 40px auto 60px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
     margin-left: 20px;  /* pushes it left with some space */
    margin-right: auto;
    /* Align container to left with some padding */
    padding-left: 20px;
    text-align: left;
}

.menu-category {
    margin-bottom: 40px;
}

.menu-category h2 {
    border-bottom: 2px solid rgb(28, 138, 150); /* orange accent */
    padding-bottom: 6px;
    margin-bottom: 20px;
    font-weight: 700;
    color:rgb(28, 138, 150);
}

/* Change menu items container to flex row */
.menu-category {
    /* Add this wrapper div inside PHP for items container */
}

.menu-items-row {
    display: flex;
    gap: 20px; /* spacing between items */
    flex-wrap: wrap; /* wrap to next line if too wide */
}

.menu-item {
    display: flex;
    flex-direction: column; /* stack image above details */
    align-items: flex-start;
    width: 240px;  /* fixed width per item to align nicely */
    padding-bottom: 20px;
    border-bottom: none; /* no border below items in horizontal */
}

.menu-item-image {
    width: 100%;
    height: 140px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 10px;
    background-color: #f5f5f5;
    box-shadow: 0 1px 4px rgb(0 0 0 / 0.1);
}

.menu-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.menu-item-details {
    width: 100%;
}

.menu-item-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.menu-item-description {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 8px;
    font-style: italic;
}

.menu-item-meta {
    display: flex;
    align-items: center;
    gap: 12px;
}

.menu-item-price {
    font-weight: 700;
    color: #2c3e50;
}

.menu-item-available {
    background-color: #27ae60;
    color: white;
    font-size: 0.85rem;
    padding: 4px 10px;
    border-radius: 12px;
    user-select: none;
}

.menu-item-unavailable {
    background-color: #c0392b;
    color: white;
    font-size: 0.85rem;
    padding: 4px 10px;
    border-radius: 12px;
    user-select: none;
}

</style>

<div class="restaurant-menu">
    <?php if (empty($menu_by_category)): ?>
        <p>No menu items available for this restaurant.</p>
    <?php else: ?>
        <?php foreach ($menu_by_category as $category => $items): ?>
            <div class="menu-category">
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <div class="menu-items-row">
                    <?php foreach ($items as $item): ?>
                        <div class="menu-item">
                            <div class="menu-item-image">
                                <?php if (!empty($item['image']) && file_exists("../uploads/" . $item['image'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <img src="../images/placeholder_food.png" alt="No Image">
                                <?php endif; ?>
                            </div>
                            <div class="menu-item-details">
                                <div class="menu-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="menu-item-description"><?php echo htmlspecialchars($item['description']); ?></div>
                                <div class="menu-item-meta">
                                    <div class="menu-item-price">Rs. <?php echo number_format($item['price'], 2); ?></div>
                                    <?php if ($item['is_available']): ?>
                                        <div class="menu-item-available">Available</div>
                                    <?php else: ?>
                                        <div class="menu-item-unavailable">Unavailable</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

