<?php
include 'function/session_func.php';
require_once('connect.php');

$account_id = $_SESSION['account_id'];

// Fetch cart items for the logged-in user
$sql_cart = "SELECT c.id AS cart_id, i.product_name, c.size AS cart_size, c.quantity, c.price, (c.quantity * c.price) AS total_price, 
             im.image_path, c.item_id
             FROM `user_account`.`cart` c
             JOIN admin_account.item i ON c.item_id = i.product_id
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
$total_items = 0;

// Handle item removal from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    $cart_id = intval($_POST['cart_id']);
    $sql_remove = "DELETE FROM `user_account`.`cart` WHERE id = ? AND account_id = ?";
    $stmt_remove = $conn2->prepare($sql_remove);
    $stmt_remove->bind_param('ii', $cart_id, $account_id);
    $stmt_remove->execute();
    header("Location: cart.php");
    exit;
}

// Handle item update (size and quantity)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $cart_id = intval($_POST['cart_id']);
    $new_size = $_POST['size'];
    $new_quantity = intval($_POST['quantity']);
    $item_id = intval($_POST['item_id']);

    // Check stock availability
    $sql_check_stock = "SELECT stock, price FROM admin_account.item_details WHERE item_id = ? AND size = ?";
    $stmt_stock = $conn2->prepare($sql_check_stock);
    $stmt_stock->bind_param('is', $item_id, $new_size);
    $stmt_stock->execute();
    $result_stock = $stmt_stock->get_result();
    $stock_info = $result_stock->fetch_assoc();

    if ($stock_info && $new_quantity <= $stock_info['stock']) {
        $new_price = $stock_info['price'];
        $sql_update = "UPDATE `user_account`.`cart` SET size = ?, quantity = ?, price = ? WHERE id = ? AND account_id = ?";
        $stmt_update = $conn2->prepare($sql_update);
        $stmt_update->bind_param('siiii', $new_size, $new_quantity, $new_price, $cart_id, $account_id);
        $stmt_update->execute();
        header("Location: cart.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Insufficient stock for Size $new_size.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'header.php' ?>

    <div class="container col-sm-9 mt-4">
        <h2 class="mb-4">Shopping Cart</h2>

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
                            <?php $total_items++; ?>
                            <tr class="border m-1 p-1">
                                <td style="vertical-align: middle;">
                                    <img src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['product_name']; ?>" width="50" height="50" class="img-fluid">
                                    <?php echo htmlspecialchars($row['product_name']); ?>
                                </td>

                                <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['cart_size']); ?></td>
                                <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td style="vertical-align: middle;">PHP <?php echo number_format($row['price'], 2); ?></td>
                                <td style="vertical-align: middle;">PHP <?php echo number_format($row['total_price'], 2); ?></td>
                                <td style="vertical-align: middle;">
                                    <!-- Edit Button to open modal -->
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['cart_id']; ?>">Edit</button>
                                    <!-- Remove item form -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal for editing cart item -->
                            <div class="modal fade" id="editModal<?php echo $row['cart_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['cart_id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?php echo $row['cart_id']; ?>">Edit Item</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                                <input type="hidden" name="action" value="update">

                                                <!-- Size dropdown -->
                                                <label for="size" class="form-label">Size</label>
                                                <select name="size" class="form-select">
                                                    <?php
                                                    // Query available sizes for this item
                                                    $sql_sizes = "SELECT size, stock FROM admin_account.item_details WHERE item_id = ?";
                                                    $stmt_sizes = $conn2->prepare($sql_sizes);
                                                    $stmt_sizes->bind_param('i', $row['item_id']);
                                                    $stmt_sizes->execute();
                                                    $result_sizes = $stmt_sizes->get_result();

                                                    while ($size_row = $result_sizes->fetch_assoc()):
                                                        $selected = ($size_row['size'] === $row['cart_size']) ? 'selected' : '';
                                                        $disabled = ($size_row['stock'] <= 0) ? 'disabled' : '';
                                                    ?>
                                                        <option value="<?php echo $size_row['size']; ?>" <?php echo $selected; ?> <?php echo $disabled; ?>>
                                                            <?php echo $size_row['size'] . ' (Stock: ' . $size_row['stock'] . ')'; ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>

                                                <!-- Quantity input -->
                                                <label for="quantity" class="form-label mt-2">Quantity</label>
                                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" min="1" max="5" class="form-control">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $total_price += $row['total_price']; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="container border p-2 bg-white shadow-sm d-flex justify-content-between align-items-center">
                <p class="m-0 fs-6 small-sm">Total Items : <?php echo $total_items; ?></p>
                <div class="d-flex justify-content-end align-items-center">
                    <p class="m-0">Total Price : PHP <?php echo number_format($total_price, 2); ?></p>
                    <a href="checkout.php" class="btn btn-primary rounded-0 ms-2 px-5 py-1 fs-6">Check Out</a>
                </div>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
