<?php
include 'function/session_func.php';
require_once('connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $item_id = intval($_POST['item_id']);
    $account_id = intval($_POST['account_id']);
    $size = $_POST['size'];
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    if ($action === 'add_to_cart') {
        $sql = "INSERT INTO `user_account`.`cart` (account_id, item_id, size, quantity, price) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iisis', $account_id, $item_id, $size, $quantity, $price);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    }
}
?>
