<?php
session_start();
include '../db.php';

if (!isset($_SESSION['restaurant_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$restaurant_id = $_SESSION['restaurant_id'];
$table_id = isset($_POST['table_id']) ? intval($_POST['table_id']) : 0;
$table_number = isset($_POST['table_number']) ? intval($_POST['table_number']) : 0;
$seats = isset($_POST['seats']) ? intval($_POST['seats']) : 0;
$status = isset($_POST['status']) ? intval($_POST['status']) : 0;
$price = isset($_POST['price']) && !empty($_POST['price']) ? floatval($_POST['price']) : 0.00;

// Validate data
if ($table_id <= 0 || $table_number <= 0 || $seats <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit();
}

$stmt = $conn->prepare("SELECT id FROM restaurant_tables WHERE id = ? AND restaurant_id = ?");
$stmt->bind_param("ii", $table_id, $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Table not found or not authorized']);
    exit();
}
$stmt->close();

$stmt = $conn->prepare("
    SELECT id FROM restaurant_tables 
    WHERE restaurant_id = ? AND table_number = ? AND id != ?
");
$stmt->bind_param("iii", $restaurant_id, $table_number, $table_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Table number already exists']);
    exit();
}
$stmt->close();
$stmt = $conn->prepare("
    UPDATE restaurant_tables 
    SET table_number = ?, seats = ?, status = ?, price = ? 
    WHERE id = ? AND restaurant_id = ?
");
$stmt->bind_param("iiidii", $table_number, $seats, $status, $price, $table_id, $restaurant_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Table updated successfully']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to update table: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>