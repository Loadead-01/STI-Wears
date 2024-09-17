<?php
include 'function/session_func.php';
require_once('connect.php');

// Assume logged-in user's account id is stored in session
$account_id = $_SESSION['account_id'];

// Fetch cart items for the logged-in user
$sql_cart = "SELECT c.id, i.product_name, c.size, c.quantity, c.price, (c.quantity * c.price) AS total_price
             FROM `user_account`.`cart` c
             JOIN item i ON c.item_id = i.product_id
             WHERE c.account_id = ?";
$stmt_cart = $conn2->prepare($sql_cart);
if (!$stmt_cart) {
    die('Prepare failed: ' . $conn2->error);
}
$stmt_cart->bind_param('i', $account_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include'header.php'?>

<div class="container mt-4">
    <h1>Your Cart</h1>
    
    <?php if ($result_cart->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_cart->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['size']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>PHP <?php echo number_format($row['price'], 2); ?></td>
                        <td>PHP <?php echo number_format($row['total_price'], 2); ?></td>
                        <td>
                            <form method="POST" action="remove_cart_item.php">
                                <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>

                <?php $total_price += $row['total_price']; endwhile; ?>
            </tbody>
        </table>
        <h3>Total Price : <?php echo $total_price ?></h3>
        <form method="POST" action="checkout.php">
            <button type="submit" class="btn btn-primary">Proceed to Checkout</button>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

</body>
</html>
