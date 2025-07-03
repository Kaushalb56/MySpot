<?php
include 'db.php';
session_start();

$restaurant_id = $_SESSION['restaurant_id'];

// Save image, cuisine, timings
$imageName = $_FILES['image']['name'];
$imagePath = "../uploads/" . basename($imageName);
move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

$cuisine = $_POST['cuisine'];
$opening = $_POST['opening_time'];
$closing = $_POST['closing_time'];

$sql = "UPDATE restaurants SET image=?, cuisine=?, opening_time=?, closing_time=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $imagePath, $cuisine, $opening, $closing, $restaurant_id);
$stmt->execute();

// Save table seats
$seatsArray = $_POST['seats'];
$tableNumber = 1;

foreach ($seatsArray as $seats) {
    $sql = "INSERT INTO restaurant_tables (restaurant_id, table_number, seats) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $restaurant_id, $tableNumber, $seats);
    $stmt->execute();
    $tableNumber++;
}

header("Location: restaurant_dashboard.php");
?>
