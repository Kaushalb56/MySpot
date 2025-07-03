<?php
include 'db.php';
session_start();

$restaurant_id = $_SESSION['restaurant_id'];
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$category = $_POST['category'];
$is_available = 1;

$imageName = $_FILES['image']['name'];
$imagePath = "../uploads/" . basename($imageName);
move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

$sql = "INSERT INTO menu_items (restaurant_id, name, description, price, category, image, is_available) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issdssi", $restaurant_id, $name, $description, $price, $category, $imagePath, $is_available);

if ($stmt->execute()) {
    header("Location: ../php/menu_manage.php");
} else {
    echo "Error: " . $stmt->error;
}
?>