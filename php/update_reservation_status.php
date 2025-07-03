<?php
session_start();
include 'db.php';

// Load PHPMailer
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['restaurant_id']) || !isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: restaurant_reservations.php");
    exit();
}

$reservation_id = $_GET['id'];
$new_status = $_GET['status'];
$restaurant_id = $_SESSION['restaurant_id'];

// Fetch reservation info
$sql = "SELECT r.*, c.email AS customer_email, c.name AS customer_name, rest.name AS restaurant_name 
        FROM reservations r
        JOIN customers c ON r.customer_id = c.id
        JOIN restaurants rest ON r.restaurant_id = rest.id
        WHERE r.id = ? AND r.restaurant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $reservation_id, $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: restaurant_reservations.php?error=notfound");
    exit();
}

$reservation = $result->fetch_assoc();

// Update reservation status
$update_sql = "UPDATE reservations SET status = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $new_status, $reservation_id);
$update_stmt->execute();

// Send email to customer
$customer_email = $reservation['customer_email'];
$customer_name = $reservation['customer_name'];
$res_date = $reservation['reservation_date'];
$res_time = $reservation['reservation_time'];
$party_size = $reservation['party_size'];
$restaurant_name = $reservation['restaurant_name'];

if (!empty($customer_email)) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // your Gmail
        $mail->Password = 'your app password'; // your Gmail app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-emal@gmail.com', $restaurant_name);
        $mail->addAddress($customer_email, $customer_name);

        $mail->isHTML(true);
        $mail->Subject = "Reservation " . ucfirst($new_status) . " at $restaurant_name";

        if ($new_status === 'confirmed') {
            $body = "
                <p>Hello <strong>$customer_name</strong>,</p>
                <p>Your reservation at <strong>$restaurant_name</strong> has been <strong style='color:green;'>CONFIRMED</strong>.</p>
                <p><strong>Date:</strong> $res_date<br>
                   <strong>Time:</strong> $res_time<br>
                   <strong>Party Size:</strong> $party_size</p>
                <p>We look forward to seeing you!</p>
                <p>Regards,<br>$restaurant_name Team</p>
            ";
        } else {
            $body = "
                <p>Hello <strong>$customer_name</strong>,</p>
                <p>We regret to inform you that your reservation at <strong>$restaurant_name</strong> has been <strong style='color:red;'>CANCELLED</strong>.</p>
                <p><strong>Date:</strong> $res_date<br>
                   <strong>Time:</strong> $res_time</p>
                <p>Please feel free to book again. Sorry for the inconvenience.</p>
                <p>Regards,<br>$restaurant_name Team</p>
            ";
        }

        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
    }
}

header("Location: restaurant_reservations.php?status=updated");
exit();
