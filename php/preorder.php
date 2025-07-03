<?php
session_start();
include 'db.php';

if (!isset($_POST['restaurant_id'])) {
    header("Location: ../index.html");
    exit();
}
$restaurant_id = (int)$_POST['restaurant_id'];

$_SESSION['restaurant_id'] = $restaurant_id; 

// Sanitize POST data
$date = htmlspecialchars($_POST['date'] ?? '');
$time = htmlspecialchars($_POST['time'] ?? '');
$people = (int)($_POST['people'] ?? 2);
$table_id = (int)($_POST['table_id'] ?? 0);

if (!$table_id) {
    header("Location: select_table.php?date=$date&time=$time&people=$people");
    exit();
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Fetch restaurant info
$restaurant_query = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
$restaurant_query->bind_param("i", $restaurant_id);
$restaurant_query->execute();
$restaurant_result = $restaurant_query->get_result();
$restaurant = $restaurant_result->fetch_assoc();

// Fetch selected table info
$table_query = $conn->prepare("SELECT table_number, seats FROM restaurant_tables WHERE id = ?");
$table_query->bind_param("i", $table_id);
$table_query->execute();
$table_result = $table_query->get_result();
$table = $table_result->fetch_assoc();

// Fetch menu items grouped by category
$menu_items = [];
$categories_query = $conn->query("SELECT DISTINCT category FROM menu_items WHERE restaurant_id = $restaurant_id ORDER BY category ASC");

while ($cat_row = $categories_query->fetch_assoc()) {
    $category = $cat_row['category'];
    $stmt = $conn->prepare("SELECT id, name, description,image, price FROM menu_items WHERE restaurant_id = ? AND category = ? AND is_available = 1 ORDER BY name ASC");
    $stmt->bind_param("is", $restaurant_id, $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu_items[$category] = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Pre-order Food</title>
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- CSS: Replace your <style> tag content with this -->
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif;
        background-color: #f9fafb;
        color: #111827;
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
    .container {
        max-width: 960px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    h2 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #111827;
    }

    .reservation-summary {
        background-color: #ecfdf5;
        border-left: 4px solid #10b981;
        padding: 16px 24px;
        margin-bottom: 30px;
        border-radius: 12px;
        line-height: 1.6;
        color: #065f46;
    }

    .menu-category {
        margin-bottom: 40px;
    }

    .menu-category h3 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #0f172a;
        border-bottom: 2px solid #10b981;
        padding-bottom: 4px;
    }

    .menu-item {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .item-info {
        flex: 1;
        margin-right: 16px;
    }

    .item-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #1e293b;
    }

    .item-desc {
        font-size: 14px;
        color: #6b7280;
    }

    .item-actions {
        text-align: right;
    }

    .item-price {
        font-size: 16px;
        font-weight: 600;
        color: #10b981;
        margin-bottom: 8px;
        display: inline-block;
    }

    .qty-input {
        width: 60px;
        padding: 6px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        text-align: center;
        background: white;
    }

    .note {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 4px;
    }

    textarea {
        width: 100%;
        height: 100px;
        margin-top: 30px;
        padding: 14px;
        font-size: 15px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        resize: vertical;
    }

    .btn-submit {
        display: inline-block;
        margin-top: 30px;
        background-color: #10b981;
        border: none;
        padding: 14px 32px;
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        float: right;
    }

    .btn-submit:hover {
        background-color: #059669;
    }
    .order-summary {
    position: fixed;
    top: 100px;
    right: 30px;
    width: 280px;
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    font-size: 14px;
    color: #1f2937;
    z-index: 999;
}

.order-summary h4 {
    margin-top: 0;
    font-size: 16px;
    font-weight: 600;
    color: #10b981;
    margin-bottom: 16px;
}

#orderList {
    list-style: none;
    padding: 0;
    margin: 0 0 12px 0;
    max-height: 180px;
    overflow-y: auto;
}

#orderList li {
    margin-bottom: 8px;
}

.order-total {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    border-top: 1px solid #e5e7eb;
    padding-top: 10px;
    text-align: right;
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
            <a href="../index.html" class="btn">Sign Out</a>
        </div>
    </div>
<div class="container">
    <h2>Pre-order Food</h2>

    <div class="reservation-summary">
        <strong>Restaurant:</strong> <?= htmlspecialchars($restaurant['name']) ?><br />
        <strong>Date:</strong> <?= date("l, F j, Y", strtotime($date)) ?><br />
        <strong>Time:</strong> <?= date("g:i A", strtotime($time)) ?><br />
        <strong>People:</strong> <?= $people ?><br />
        <strong>Table Number:</strong> <?= htmlspecialchars($table['table_number']) ?> (Seats: <?= $table['seats'] ?>)
    </div>

    <?php if (!empty($menu_items)): ?>
    <form method="post" action="place_preorder.php">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>" />
        <input type="hidden" name="date" value="<?= $date ?>" />
        <input type="hidden" name="time" value="<?= $time ?>" />
        <input type="hidden" name="people" value="<?= $people ?>" />
        <input type="hidden" name="table_id" value="<?= $table_id ?>" />

        <?php foreach ($menu_items as $category => $items): ?>
            <div class="menu-category">
                <h3><?= htmlspecialchars($category) ?></h3>
                <?php foreach ($items as $item): ?>
                    <div class="menu-item" style="align-items: flex-start;">
    <div style="display: flex; gap: 16px;">
        <div style="width: 100px; height: 100px; flex-shrink: 0; border-radius: 10px; overflow: hidden; background-color: #f3f4f6;">
            <?php if (!empty($item['image']) && file_exists("../uploads/" . $item['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <img src="../images/placeholder_food.png" alt="No Image" style="width: 100%; height: 100%; object-fit: cover;">
            <?php endif; ?>
        </div>
        <div class="item-info">
            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
            <div class="item-desc"><?= htmlspecialchars($item['description']) ?></div>
        </div>
    </div>
    <div class="item-actions">
        <span class="item-price">Rs. <?= number_format($item['price'], 2) ?></span><br/>
        <label for="qty_<?= $item['id'] ?>" class="sr-only">Quantity</label>
        <input 
            type="number" 
            min="0" 
            max="20" 
            value="0" 
            name="quantity[<?= $item['id'] ?>]" 
            id="qty_<?= $item['id'] ?>" 
            class="qty-input"
        />
        <div class="note">Max 20</div>
    </div>
</div>

                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <label for="instructions"><strong>Additional Instructions (optional):</strong></label>
        <textarea name="instructions" id="instructions" placeholder="Any special requests or notes..."></textarea>

        <button type="submit" name="place_preorder" class="btn-submit">Continue</button>

    </form>
    <?php else: ?>
        <p>No menu items available for pre-order at this time.</p>
    <?php endif; ?>
</div>
<div class="order-summary" id="orderSummary">
    <h4>Your Order</h4>
    <ul id="orderList">
        <li>No items selected.</li>
    </ul>
    <div class="order-total">
        Total: <strong id="orderTotal">Rs. 0.00</strong>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
<script>
    const qtyInputs = document.querySelectorAll('.qty-input');
    const orderList = document.getElementById('orderList');
    const orderTotal = document.getElementById('orderTotal');

    const items = {
        <?php
        // We'll pass item prices and names to JS for live summary updates
        foreach ($menu_items as $category => $items) {
            foreach ($items as $item) {
                echo $item['id'] . ": { name: `" . addslashes($item['name']) . "`, price: " . $item['price'] . " },";
            }
        }
        ?>
    };

    function updateOrderSummary() {
        let total = 0;
        let listHTML = '';
        let hasItem = false;

        qtyInputs.forEach(input => {
            const qty = parseInt(input.value);
            const id = input.id.replace('qty_', '');
            if (qty > 0 && items[id]) {
                const itemTotal = qty * items[id].price;
                total += itemTotal;
                listHTML += `<li>${items[id].name} x${qty} - Rs. ${itemTotal.toFixed(2)}</li>`;
                hasItem = true;
            }
        });

        orderList.innerHTML = hasItem ? listHTML : '<li>No items selected.</li>';
        orderTotal.innerHTML = `Rs. ${total.toFixed(2)}`;
    }

    qtyInputs.forEach(input => {
        input.addEventListener('input', updateOrderSummary);
    });

    // Initial call
    updateOrderSummary();
</script>
