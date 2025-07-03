<?php
include 'db.php';

$id = $_POST['menu_item_id'];
$status = $_POST['is_available'];

$sql = "UPDATE menu_items SET is_available = $status WHERE id = $id";
$conn->query($sql);

echo "success";
