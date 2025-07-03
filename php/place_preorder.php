<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: customer_dashboard.php');
    exit;
}

// CSRF Check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

$quantities = $_POST['quantity'] ?? [];
$hasItems = false;

foreach ($quantities as $qty) {
    if ((int)$qty > 0) {
        $hasItems = true;
        break;
    }
}

if (!$hasItems) {
    die("No items selected for pre-order.");
}

// Get restaurant name
$restaurant_id = (int)$_SESSION['restaurant_id'];
$stmt = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$_SESSION['reservation'] = [
    'restaurant_id' => $restaurant_id,
    'restaurant_name' => $row['name'] ?? 'Unknown',
    'customer_id' => $_SESSION['customer_id'] ?? null,
    'date' => $_POST['date'],
    'time' => $_POST['time'],
    'people' => (int)$_POST['people'],
    'table_id' => (int)$_POST['table_id'],
    'instructions' => trim($_POST['instructions'] ?? ''),
    'quantity' => $quantities
];

header("Location: final_confirmation.php");
exit;
?>
