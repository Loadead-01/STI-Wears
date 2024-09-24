<?php
include 'function/session_func.php';
require_once('connect.php');


// Assume logged-in user's account id is stored in session
$account_id = $_SESSION['account_id'];

// Fetch cart items for the logged-in user
$sql_cart = "SELECT c.id, i.product_name, c.size, c.quantity, c.price, (c.quantity * c.price) AS total_price, im.image_path
             FROM `user_account`.`cart` c
             JOIN item i ON c.item_id = i.product_id
             JOIN admin_account.item_image im ON i.product_id = im.item_id
             WHERE c.account_id = ?";
$stmt_cart = $conn2->prepare($sql_cart);
if (!$stmt_cart) {
    die('Prepare failed: ' . $conn2->error);
}
$stmt_cart->bind_param('i', $account_id);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();
$total_price = 0;



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
    header("Location: cart.php");
    if ($stmt_remove->error) {
        die('Execute failed: ' . $stmt_remove->error);
    }
}
$total_items = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light ">
    <?php include 'header.php' ?>

    <div class="container col-sm-9 mt-4">
        <h2 class="mb-4">Shopping Cart</h1>

            <?php if ($result_cart->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped shadow-sm table-white table-hover border">
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
                                <?php $total_items++ ?>
                                <tr class="border m-1 p-1">
                                    <td style="vertical-align: middle;">
                                        <img src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['product_name']; ?>" width="50" height="50" class="img-fluid">
                                        <?php echo htmlspecialchars($row['product_name']); ?>
                                    </td>
                                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['size']); ?></td>
                                    <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td style="vertical-align: middle;">PHP <?php echo number_format($row['price'], 2); ?></td>
                                    <td style="vertical-align: middle;">PHP <?php echo number_format($row['total_price'], 2); ?></td>
                                    <td style="vertical-align: middle;">
                                        <form method="POST">
                                            <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php $total_price += $row['total_price'];
                            endwhile; ?>
                        </tbody>
                    </table>
                </div>


                <div class="container border p-2 bg-white shadow-sm d-flex justify-content-between align-items-center">
                    <p class="m-0 fs-6 small-sm">Total Items : <?php echo $total_items; ?></p>

                    <div class="d-flex justify-content-end align-items-center">
                        <p class=" m-0 ">Total Price : PHP <?php echo $total_price ?></p>

                        <a href="checkout.php" class="btn btn-primary rounded-0 ms-2 px-5 py-1 fs-6">Check Out</a>


                    </div>
                </div>

            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>