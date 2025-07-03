<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['lat']) && isset($data['lng'])) {
    $_SESSION['user_lat'] = $data['lat'];
    $_SESSION['user_lng'] = $data['lng'];
}
