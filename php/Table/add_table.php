<?php
session_start();
include '../db.php';

if (!isset($_SESSION['restaurant_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$restaurant_id = $_SESSION['restaurant_id'];
$table_number = isset($_POST['table_number']) ? intval($_POST['table_number']) : 0;
$seats = isset($_POST['seats']) ? intval($_POST['seats']) : 0;
$status = isset($_POST['status']) ? intval($_POST['status']) : 0;
$price = isset($_POST['price']) && !empty($_POST['price']) ? floatval($_POST['price']) : 0.00;

// Validate data
if ($table_number <= 0 || $seats <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid table number or seats value']);
    exit();
}

// Check if table number already exists for this restaurant
$stmt = $conn->prepare("SELECT id FROM restaurant_tables WHERE restaurant_id = ? AND table_number = ?");
$stmt->bind_param("ii", $restaurant_id, $table_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Table number already exists']);
    exit();
}
$stmt->close();

$stmt = $conn->prepare("
    INSERT INTO restaurant_tables (restaurant_id, table_number, seats, status, price) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iiiid", $restaurant_id, $table_number, $seats, $status, $price);

if ($stmt->execute()) {
    $newId = $conn->insert_id;
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Table added successfully',
        'id' => $newId,
        'table_number' => $table_number,
        'seats' => $seats,
        'status' => $status,
        'price' => $price
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to add table: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>