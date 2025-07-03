<?php
require 'db.php';

$location = $_GET['location'] ?? '';
$query = $_GET['query'] ?? '';

// Prepare query
$sql = "SELECT * FROM restaurants WHERE 1";
$params = [];

if ($location !== '') {
  $sql .= " AND state_location = ?";
  $params[] = $location;
}
if ($query !== '') {
  $sql .= " AND name LIKE ?";
  $params[] = "%" . $query . "%";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
