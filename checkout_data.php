<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : null;
    $size = isset($_POST['size']) ? $_POST['size'] : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;

    // Store data in session
    $_SESSION['checkout_data'] = [
        'item_id' => $item_id,
        'size' => $size,
        'quantity' => $quantity,
        'price' => $price
    ];

    // Return a success response
    echo json_encode(['status' => 'success']);
} else {
    // Return an error response
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>