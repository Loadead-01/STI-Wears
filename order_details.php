<?php
include 'function/admin_session_func.php';
include 'connect.php';
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$sql_order = "SELECT o.total_price, o.ordered_by, o.ordered_date, o.status, o.student_id, d.size, d.quantity, d.price, i.product_name, i.product_id
FROM `user_account`.`order` o
JOIN `user_account`.`order_detail` d ON o.order_id = d.order_id
JOIN `admin_account`.`item` i ON d.product_name = i.product_name
WHERE o.order_id = ?";

$stmt_order = $conn2->prepare($sql_order);
if (!$stmt_order) {
    die('Prepare failed: ' . $conn2->error);
}
$stmt_order->bind_param('i', $order_id);
$stmt_order->execute();
$result = $stmt_order->get_result();

if ($result->num_rows > 0) {
    // Fetch all rows into an array
    $order_items = [];
    while ($row = $result->fetch_assoc()) {
        $order_items[] = $row;
    }
    // Use the first row for general order details
    $order = $order_items[0];
    // Format order date (assuming it's stored as DATETIME)
    $order_date = date('F j, Y, g:i a', strtotime($order['ordered_date']));
} else {
    echo "Order not found or you do not have permission to view this order.";
    exit;
}

// Handle status update and quantity deduction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];

    // Check if the current order status is not already 'paid'
    if ($order['status'] !== 'paid') {
        // Update order status
        $sql_status = "UPDATE `user_account`.`order` SET `status` = ? WHERE order_id = ?";
        if ($stmt_status = $conn->prepare($sql_status)) {
            $stmt_status->bind_param("si", $status, $order_id);
            if ($stmt_status->execute()) {
                echo "Order status updated to Paid.<br>";
            } else {
                echo "Error updating status: " . $stmt_status->error;
            }
        }

        // Deduct quantity from each item size and update overall stock
        foreach ($order_items as $item) {
            $quantity_ordered = $item['quantity'];
            $size = $item['size'];
            $product_id = $item['product_id']; // Get the product ID

            // Update stock based on size in the `item_details` table
            $sql_update_stock = "UPDATE `admin_account`.`item_details` SET `stock` = `stock` - ? 
                                 WHERE `item_id` = ? AND `size` = ?";
            if ($stmt_update = $conn2->prepare($sql_update_stock)) {
                $stmt_update->bind_param("iis", $quantity_ordered, $product_id, $size);
                if ($stmt_update->execute()) {
                    echo "Stock updated for product: " . $item['product_name'] . " (Size: " . $size . ")<br>";
                } else {
                    echo "Error updating stock: " . $stmt_update->error . "<br>";
                }
            } else {
                echo "Prepare failed for stock update: " . $conn2->error . "<br>";
            }
        }

        // **Sum up all sizes' stock from the `item_details` table**
        $sql_sum_stock = "SELECT SUM(stock) AS total_stock FROM `admin_account`.`item_details` WHERE item_id = ?";
        if ($stmt_sum_stock = $conn2->prepare($sql_sum_stock)) {
            $stmt_sum_stock->bind_param("i", $product_id);
            $stmt_sum_stock->execute();
            $result_sum_stock = $stmt_sum_stock->get_result();
            $total_stock = $result_sum_stock->fetch_assoc()['total_stock'];

            // Update the overall stock in the `item` table
            $sql_update_item_stock = "UPDATE `admin_account`.`item` SET `stock` = ? WHERE `product_id` = ?";
            if ($stmt_update_item_stock = $conn2->prepare($sql_update_item_stock)) {
                $stmt_update_item_stock->bind_param("ii", $total_stock, $product_id);
                if ($stmt_update_item_stock->execute()) {
                    echo "Total stock updated for product: " . $item['product_name'] . "<br>";
                } else {
                    echo "Error updating item stock: " . $stmt_update_item_stock->error . "<br>";
                }
            } else {
                echo "Prepare failed for item stock update: " . $conn2->error . "<br>";
            }
        } else {
            echo "Error fetching total stock: " . $conn2->error . "<br>";
        }
    } else {
        echo "Order has already been paid and cannot be updated.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STI Wears</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'admin_header.php' ?>

    <h3>Order number: <?php echo $order_id; ?></h3>
    <h3>Order By: <?php echo $order['ordered_by']; ?></h3>
    <h3>Total Price: <?php echo $order['total_price']; ?></h3>
    <h3>Order Status: <?php echo $order['status']; ?></h3>
    <h3>Order Date: <?php echo $order_date; ?></h3>
    
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['size']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo $item['price']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <form action="" method="POST">
        <?php if ($order['status'] !== 'paid'): ?>
            <input type="submit" name="status" value="paid" class="btn btn-success">
        <?php else: ?>
            <p class="text-danger">Order has already been marked as paid.</p>
        <?php endif; ?>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
