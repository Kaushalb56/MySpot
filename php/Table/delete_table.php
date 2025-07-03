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

if ($table_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid table ID']);
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

$stmt = $conn->prepare("DELETE FROM restaurant_tables WHERE id = ? AND restaurant_id = ?");
$stmt->bind_param("ii", $table_id, $restaurant_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Table deleted successfully']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to delete table: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>