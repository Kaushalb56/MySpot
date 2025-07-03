<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "myspot_db";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if ($conn) {
   // echo "Connected to database successfully!";
} else {
    echo "Failed to connect to database.";
}
?>
