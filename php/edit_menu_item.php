<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $image_path = '';

    // Handle image upload if a new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image_path = $targetPath;
        }
    }

    // Build update query
    $query = "UPDATE menu_items SET 
                name = '$name',
                description = '$description',
                price = $price,
                category = '$category'";
    
    if ($image_path !== '') {
        $query .= ", image = '$image_path'";
    }

    $query .= " WHERE id = $id";

    if ($conn->query($query)) {
        header('Location: menu_manage.php');
        exit();
    } else {
        echo "Error updating item: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
