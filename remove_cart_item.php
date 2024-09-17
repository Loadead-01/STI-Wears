<?php
include 'function/session_func.php';
require_once('connect.php');

// Assume logged-in user's account id is stored in session
$account_id = $_SESSION['account_id'];

// Get cart ID from POST request
$cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;

if ($cart_id > 0) {
    // Delete item from cart
    $sql_remove = "DELETE FROM `user_account`.`cart` WHERE id = ? AND account_id = ?";
    $stmt_remove = $conn2->prepare($sql_remove);
    if (!$stmt_remove) {
        die('Prepare failed: ' . $conn2->error);
    }
    $stmt_remove->bind_param('ii', $cart_id, $account_id);
    $stmt_remove->execute();
    if ($stmt_remove->error) {
        die('Execute failed: ' . $stmt_remove->error);
    }
}

header("Location: cart.php");
exit;
?>
