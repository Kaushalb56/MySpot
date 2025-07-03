<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];

    // Use the correct name field based on the selected role
    if ($role == 'customer') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $sql = "INSERT INTO customers (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)";
    } elseif ($role == 'restaurant') {
        $name = $_POST['restaurant_name'];
        $email = $_POST['restaurant_email'];
        $phone = $_POST['restaurant_phone'];
        $address = $_POST['restaurant_address'];
        $sql = "INSERT INTO restaurants (name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)";
    } else {
        die("Invalid role.");
    }

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $password);

    if ($stmt->execute()) {
        header("Location: ../index.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>
