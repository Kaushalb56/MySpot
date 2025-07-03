<?php
session_start();
include '../db.php';

if (!isset($_SESSION['restaurant_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$restaurant_id = $_SESSION['restaurant_id'];

$stmt = $conn->prepare("
    SELECT id, table_number, seats, status, price 
    FROM restaurant_tables 
    WHERE restaurant_id = ? 
    ORDER BY table_number ASC
");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'tables' => $tables]);
$stmt->close();
$conn->close();
?>