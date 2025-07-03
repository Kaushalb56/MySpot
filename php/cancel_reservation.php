<?php
session_start();
require 'db.php'; // Make sure PHPMailer is installed via Composer

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = intval($_POST['reservation_id']);

    // Fetch reservation and verify ownership
    $sql = "SELECT r.*, c.name AS customer_name, c.email AS customer_email, res.name AS restaurant_name, res.email AS restaurant_email
            FROM reservations r
            JOIN customers c ON r.customer_id = c.id
            JOIN restaurants res ON r.restaurant_id = res.id
            WHERE r.id = ? AND r.customer_id = ? AND r.status != 'cancelled'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservation_id, $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($reservation = $result->fetch_assoc()) {
        // Update reservation status to 'cancelled'
        $update = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
        $update->bind_param("i", $reservation_id);
        $update->execute();

        // Send email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your-email@gmail.com'; // your email
            $mail->Password   = 'App Password';     // app-specific password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('your-email@gmail.com', 'MySpot');

            // Add recipients
            $mail->addAddress($reservation['customer_email'], $reservation['customer_name']);
            $mail->addAddress($reservation['restaurant_email'], $reservation['restaurant_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Reservation Cancelled - MySpot";
            $mail->Body    = "
                <h3>Reservation Cancelled</h3>
                <p></strong> {$reservation['customer_name']}</p>
                <p><strong>Restaurant:</strong> {$reservation['restaurant_name']}</p>
                <p><strong>Date:</strong> {$reservation['reservation_date']}</p>
                <p><strong>Time:</strong> {$reservation['reservation_time']}</p>
                <p><strong>Status:</strong> Cancelled</p>
                <p>This reservation has been successfully cancelled via MySpot.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Optionally log $mail->ErrorInfo
        }

        header("Location: my_reservations.php?cancel=success");
        exit;
    } else {
        // Not allowed or already cancelled
        header("Location: my_reservations.php?error=unauthorized");
        exit;
    }
} else {
    header("Location: my_reservations.php");
    exit;
}
?>
