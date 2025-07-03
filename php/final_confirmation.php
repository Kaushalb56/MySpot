<?php
session_start();
include 'db.php';

// Load PHPMailer
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check reservation data in session
if (!isset($_SESSION['reservation'])) {
    echo "Missing reservation or order details.";
    exit();
}

$res = $_SESSION['reservation'];
$customer_id = $_SESSION['customer_id'] ?? 1;
$restaurant_id = $res['restaurant_id'] ?? null;
$reservation_date = $res['date'] ?? null;
$reservation_time = $res['time'] ?? null;
$party_size = $res['people'] ?? null;
$table_id = $res['table_id'] ?? null;
$special_instructions = $res['instructions'] ?? '';

$table_number = '';
if ($table_id) {
    $table_res = $conn->query("SELECT table_number FROM restaurant_tables WHERE id = " . intval($table_id));
    if ($table_res && $row = $table_res->fetch_assoc()) {
        $table_number = $row['table_number'];
    }
}

$preorder_cart = [];
if (isset($res['quantity']) && is_array($res['quantity'])) {
    foreach ($res['quantity'] as $menu_item_id => $qty) {
        if ($qty > 0) {
            $preorder_cart[$menu_item_id] = $qty;
        }
    }
}

if (!$restaurant_id || !$reservation_date || !$reservation_time || !$party_size || !$table_id) {
    echo "Incomplete reservation details.";
    exit();
}

$customer_email = '';
$customer_name = '';
if ($customer_id) {
    $cust_res = $conn->query("SELECT email, name FROM customers WHERE id = " . intval($customer_id));
    if ($cust_res && $row = $cust_res->fetch_assoc()) {
        $customer_email = $row['email'];
        $customer_name = $row['name'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $stmt = $conn->prepare("INSERT INTO reservations (customer_id, restaurant_id, table_id, reservation_date, reservation_time, party_size, special_message) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissis", $customer_id, $restaurant_id, $table_id, $reservation_date, $reservation_time, $party_size, $special_instructions);

    if ($stmt->execute()) {
    $reservation_id = $stmt->insert_id;

    if (!empty($preorder_cart)) {
        $insert_order = $conn->prepare("INSERT INTO food_orders (reservation_id, menu_item_id, quantity) VALUES (?, ?, ?)");
        foreach ($preorder_cart as $menu_item_id => $qty) {
            $insert_order->bind_param("iii", $reservation_id, $menu_item_id, $qty);
            $insert_order->execute();
        }
    }

    //$conn->query("UPDATE restaurant_tables SET status=2 WHERE id=$table_id");

    $rest_res = $conn->query("SELECT name, email FROM restaurants WHERE id = " . intval($restaurant_id));
    $restaurant_name = 'Our Restaurant';
    $restaurant_email = '';
    if ($rest_res && $rest_res->num_rows) {
        $row = $rest_res->fetch_assoc();
        $restaurant_name = $row['name'];
        $restaurant_email = $row['email']; // Assuming you store restaurant email
    }

    $preorder_text = "";
    if (!empty($preorder_cart)) {
        $ids = implode(',', array_map('intval', array_keys($preorder_cart)));
        $menu_res = $conn->query("SELECT id, name FROM menu_items WHERE id IN ($ids)");
        $menu_names = [];
        while ($row = $menu_res->fetch_assoc()) {
            $menu_names[$row['id']] = $row['name'];
        }
        foreach ($preorder_cart as $item_id => $qty) {
            $item_name = $menu_names[$item_id] ?? 'Item';
            $preorder_text .= "- $item_name x $qty\n";
        }
    } else {
        $preorder_text = "No pre-ordered items.";
    }

    // ====== Send email to CUSTOMER ======
    if (!empty($customer_email)) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com'; //your email address
            $mail->Password = 'app password';  //your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', $restaurant_name);
            $mail->addAddress($customer_email, $customer_name);

            $mail->isHTML(true);
            $mail->Subject = "Reservation at $restaurant_name";

            $mail->Body = "
                <p>Hello <strong>$customer_name</strong>,</p>
                <p>Thank you for your reservation at <strong>$restaurant_name</strong>.</p>
                <p><strong>Reservation details:</strong><br>
                Date: $reservation_date<br>
                Time: $reservation_time<br>
                Party Size: $party_size<br>
                Table Number: $table_number</p>
                <p><strong>Pre-ordered items:</strong><br><pre>$preorder_text</pre></p>
                <p><strong>Special Instructions:</strong><br>" . nl2br(htmlspecialchars($special_instructions)) . "</p>
                <p>We look forward to welcoming you!<br><br>
                Best regards,<br>
                $restaurant_name Team</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Customer email could not be sent. PHPMailer Error: {$mail->ErrorInfo}");
        }
    }

    // ====== Send email to RESTAURANT ======
    if (!empty($restaurant_email)) {
        $mail_rest = new PHPMailer(true);
        try {
            $mail_rest->isSMTP();
            $mail_rest->Host = 'smtp.gmail.com';
            $mail_rest->SMTPAuth = true;
            $mail_rest->Username = 'bishtk.l568@gmail.com'; //your email address 
            $mail_rest->Password = 'app password';  //your app password
            $mail_rest->SMTPSecure = 'tls';
            $mail_rest->Port = 587;

            $mail_rest->setFrom('your-email@gmail.com', 'MySpot Reservations');
            $mail_rest->addAddress($restaurant_email, $restaurant_name);

            $mail_rest->isHTML(true);
            $mail_rest->Subject = "New Reservation Received";

            $mail_rest->Body = "
                <p>Hello $restaurant_name Team,</p>
                <p>A new reservation has been made. Here are the details:</p>
                <p><strong>Customer Name:</strong> " . htmlspecialchars($customer_name) . "<br>
                <strong>Date:</strong> $reservation_date<br>
                <strong>Time:</strong> $reservation_time<br>
                <strong>Party Size:</strong> $party_size<br>
                <strong>Table Number:</strong> $table_number</p>
                <p><strong>Pre-ordered items:</strong><br><pre>$preorder_text</pre></p>
                <p><strong>Special Instructions:</strong><br>" . nl2br(htmlspecialchars($special_instructions)) . "</p>
                <p>Please prepare accordingly.</p>
                <p>--<br>MySpot System</p>
            ";

            $mail_rest->send();
        } catch (Exception $e) {
            error_log("Restaurant email could not be sent. PHPMailer Error: {$mail_rest->ErrorInfo}");
        }
    }

    unset($_SESSION['reservation']);
    header("Location: customer_dashboard.php?reservation=success");
    exit();
} else {
    $error = "Failed to confirm reservation. Please try again.";
}

}

$restaurant = $conn->query("SELECT name FROM restaurants WHERE id = " . intval($restaurant_id))->fetch_assoc();

$menu_items = [];
if (!empty($preorder_cart)) {
    $ids = implode(',', array_map('intval', array_keys($preorder_cart)));
    $menu_result = $conn->query("SELECT id, name, price FROM menu_items WHERE id IN ($ids)");
    while ($row = $menu_result->fetch_assoc()) {
        $menu_items[$row['id']] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirm Reservation</title>
    <link rel="stylesheet" href="cc.css">
    <style>
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #ffffff;
            border-bottom: 4px solid #e0e0e0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .navbar .logo {
            font-weight: 600;
            font-size: 20px;
            color: #009688;
            display: flex;
            align-items: center;
        }
        .navbar .logo img {
            height: 28px;
            margin-right: 10px;
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
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        .summary, .cart {
            margin-bottom: 30px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .total {
            font-weight: bold;
            font-size: 1.1em;
            text-align: right;
        }
        button.confirm-btn {
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        button.confirm-btn:hover {
            background: #219150;
        }
        .instructions {
            font-style: italic;
            color: #555;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="customer_dashboard.php" class="logo">
            <img src="../images/logo-dark.svg" alt="Logo">
            MySpot
        </a>
        <div>
            <a href="restaurants.php">Find Restaurants</a>
            <a href="#"><i class="fa fa-map-marker-alt"></i> Haldwani, Uttarakhand</a>
            <a href="../index.html" class="btn">Sign Out</a>
        </div>
    </div>
    <div class="container">
        <h2>Review & Confirm Your Reservation</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="summary">
            <h3>Reservation Summary</h3>
            <p><strong>Restaurant:</strong> <?= htmlspecialchars($restaurant['name'] ?? 'Unknown') ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($reservation_date) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($reservation_time) ?></p>
            <p><strong>Party Size:</strong> <?= htmlspecialchars($party_size) ?></p>
           <p><strong>Table Number:</strong> <?= htmlspecialchars($table_number) ?></p>
            <?php if (!empty($special_instructions)): ?>
                <p class="instructions"><strong>Special Instructions:</strong><br><?= nl2br(htmlspecialchars($special_instructions)) ?></p>
            <?php endif; ?>
        </div>

        <div class="cart">
            <h3>Pre-Ordered Items</h3>
            <?php if (!empty($preorder_cart)):
                $grand_total = 0;
                foreach ($preorder_cart as $item_id => $qty):
                    $item = $menu_items[$item_id];
                    $total = $item['price'] * $qty;
                    $grand_total += $total;
            ?>
                <div class="cart-item">
                    <span><?= htmlspecialchars($item['name']) ?> x <?= $qty ?></span>
                    <span>Rs. <?= number_format($total, 2) ?></span>
                </div>
            <?php endforeach; ?>
                <p class="total">Total: Rs. <?= number_format($grand_total, 2) ?></p>
            <?php else: ?>
                <p><em>No food items were pre-ordered.</em></p>
            <?php endif; ?>
        </div>
    
        <form method="POST" action="">
            <button type="submit" name="confirm" class="confirm-btn">Confirm Reservation</button>
        </form>
        
    </div>
</body>
</html>
