<?php
include 'function/session_func.php';  // Ensure session functions are included
require_once 'connect.php';

// Ensure user is logged in and account_id is set in the session
if (!isset($_SESSION['account_id']) || !isset($_SESSION['student_id'])) {
    echo "You must be logged in to proceed.";
    exit;
}

// Get the logged-in user's account ID and student ID from the session
$account_id = $_SESSION['account_id'];
$student_id = $_SESSION['student_id'];

// Fetch cart items for the logged-in user
$sql_cart = "SELECT c.id, i.product_name, c.size, c.quantity, c.price, (c.quantity * c.price) AS total_price
             FROM `user_account`.`cart` c
             JOIN `admin_account`.`item` i ON c.item_id = i.product_id
             WHERE c.account_id = ?";
$stmt_cart = $conn2->prepare($sql_cart);
if (!$stmt_cart) {
    die('Prepare failed: ' . $conn2->error);
}
$stmt_cart->bind_param('i', $account_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();

if ($result_cart->num_rows > 0) {
    // Calculate the total order price
    $total_order_price = 0;
    while ($row = $result_cart->fetch_assoc()) {
        $total_order_price += $row['total_price'];
    }

    // Insert order into the `user_account`.`order` table
    $sql_order = "INSERT INTO `user_account`.`order` (account_id, student_id, ordered_by, section, ordered_date, total_price, status)
                  VALUES (?, ?, ?, ?, NOW(), ?, 'pending')";
    $stmt_order = $conn->prepare($sql_order);
    if (!$stmt_order) {
        die('Prepare failed: ' . $conn->error);
    }

    $ordered_by = $_SESSION['user'];  // Assuming `$_SESSION['user']` contains the full name
    $section = $_SESSION['section'];  // Assuming `$_SESSION['section']` contains the section
    $stmt_order->bind_param('isssd', $account_id, $student_id, $ordered_by, $section, $total_order_price);
    $stmt_order->execute();
    $order_id = $stmt_order->insert_id;

    // Insert order details into `user_account`.`order_detail`, including size
    $stmt_detail = $conn->prepare("INSERT INTO `user_account`.`order_detail` (order_id, product_name, size, quantity, price) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt_detail) {
        die('Prepare failed: ' . $conn->error);
    }

    // Reset the result pointer and insert the order details
    $result_cart->data_seek(0);
    while ($row = $result_cart->fetch_assoc()) {
        $stmt_detail->bind_param('issid', $order_id, $row['product_name'], $row['size'], $row['quantity'], $row['price']);
        $stmt_detail->execute();
    }

    // Clear the cart after checkout
    $sql_clear_cart = "DELETE FROM `user_account`.`cart` WHERE account_id = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    if (!$stmt_clear_cart) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt_clear_cart->bind_param('i', $account_id);
    $stmt_clear_cart->execute();

    // Redirect to the order details page
    header("Location: order.php?order_id=$order_id");
    exit;
} else {
    echo "Your cart is empty.";
}
?>
