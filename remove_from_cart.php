<?php
include 'function/session_func.php';
require_once('connect.php');

// Ensure the cart_id and account_id are provided
if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
    $account_id = $_SESSION['account_id'];

    // Remove the item from the cart
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
    echo "Item removed successfully";
}
?>
