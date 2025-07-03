<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $table = ($role == "customer") ? "customers" : "restaurants";
    $sql = "SELECT * FROM $table WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($role == "restaurant") {
    $_SESSION['restaurant_id'] = $user['id'];
    
} else {
    $_SESSION['customer_id'] = $user['id'];  // ✅ ADD THIS LINE
    header("Location: customer_dashboard.php");
}


            if ($role == "restaurant") {
                $_SESSION['restaurant_id'] = $user['id'];
                
                // Check if restaurant setup is done
                if (empty($user['image']) || empty($user['cuisine'])) {
                    header("Location: restaurant_setup.php");
                } else {
                    header("Location: restaurant_dashboard.php");
                }
            } else {
                 $_SESSION['customer_id'] = $user['id'];
                  $_SESSION['user_name'] = $user['name']; // ✅ Add this line for welcome toast
                  unset($_SESSION['welcome_shown']); 
                header("Location: customer_dashboard.php");
            }
            exit;
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "No user found!";
    }
}
?>
