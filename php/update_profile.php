<?php
session_start();
include 'db.php';

$restaurant_id = $_SESSION['restaurant_id'];

// Sanitize inputs
$name = mysqli_real_escape_string($conn, $_POST['name']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$state_location = mysqli_real_escape_string($conn, $_POST['state_location']);
$opening_time = $_POST['opening_time'];
$closing_time = $_POST['closing_time'];

$image_path = null;

// Handle image upload if a file is selected
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_name = uniqid('restaurant_') . '_' . basename($_FILES['image']['name']);
    $target_dir = '../uploads/';
    $target_file = $target_dir . $image_name;

    // Create uploads directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($image_tmp, $target_file)) {
    $image_path = '../uploads/' . $image_name;
    $image_path = mysqli_real_escape_string($conn, $image_path); // ✅ Add this
}

}

// Build SQL update query
$sql = "UPDATE restaurants SET 
            name = '$name',
            description = '$description',
            state_location = '$state_location',
            opening_time = '$opening_time',
            closing_time = '$closing_time'";

if ($image_path) {
    $sql .= ", image = '$image_path'";
}

$sql .= " WHERE id = $restaurant_id";

// Execute query
if ($conn->query($sql) === TRUE) {
    $_SESSION['success'] = "Profile updated successfully.";
} else {
    $_SESSION['error'] = "Error updating profile: " . $conn->error;
}

// Redirect back to profile page
header("Location: manager_profile.php");
exit;
?>
