<?php
include 'function/admin_session_func.php';
include 'connect.php';
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$sql_order = "SELECT o.total_price, o.ordered_by, o.ordered_date, im.image_path, o.payment_method, o.status, o.student_id, d.size, d.quantity, d.price, i.product_name, i.product_id
FROM `user_account`.`order` o
JOIN `user_account`.`order_detail` d ON o.order_id = d.order_id
JOIN `admin_account`.`item` i ON d.product_name = i.product_name
JOIN `admin_account`.`item_image` im ON i.product_id = im.item_id

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
                header("Location: #");
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
<body class="bg-light">
    <?php include 'admin_header.php' ?>

    <div class="container-lg mt-4 d-flex justify-content-center col-10">
        <div class="row d-flex justify-content-center col-12">
            <div class="col-sm-6  bg-white border shadow-sm p-0">

                <h5 style="text-align: center; vertical-align: center;" class="p-2 m-0 border">Order Number</h5>
                <p class="d-flex justify-content-center m-0 align-items-center p-3 fs-1" style="vertical-align: middle;"> <?php echo $order_id ?> </p>

            </div>
            <div class="col-sm-6 bg-white white border shadow-sm p-0 mt-3 mt-sm-0 ">
                <h5 style="text-align: center; vertical-align: center;" class="p-2 m-0 border">Order Details</h5>
                <div class="p-2">
                    <p class="m-0"><strong>Total Price: </strong><?php echo number_format($order['total_price'], 2); ?></p>
                    <p class="m-0"><strong>Order Status: </strong><?php echo htmlspecialchars($order['status'], 2); ?></p>
                    <p class="m-0"><strong>Payment Method: </strong><?php echo htmlspecialchars($order['payment_method'], 2); ?></p>

                    <p class="m-0"><strong>Ordered By: </strong> <?php echo htmlspecialchars($order['ordered_by']); ?></p>
                    <p class="m-0"><strong>Student ID : </strong> <?php echo htmlspecialchars($order['student_id']); ?> </p>
                    <p class="m-0"><strong>Ordered Date: </strong> <?php echo htmlspecialchars($order_date); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php
    if ($order['status'] == "pending") {
        if ($order["payment_method"]  == "cashier") {
            echo '<p class="small m-3 text-black-50" style="text-align: center;">Double check customers payment and information before clicking paid button</p>';
        } elseif ($order['payment_method'] == "gcash") {
            echo '<p class="small m-3 text-black-50" style="text-align: center;">Double check customers payment and information before clicking paid button</p>';

        }
    } elseif ($order['status'] == "cancelled") {
        echo '<p class="small m-3 text-black-50" style="text-align: center;">Order automatically cancelled after a day of unsuccessfull payment</p>';
    } else {
        echo '<p class="small m-3 text-black-50" style="text-align: center;">Order Paid, Print and give the order receipt  to the customer</p>';



    }
    ?>

    <div class="container  ">
    <div class="container mt-4 m-0">
        <table class="table border shadow-sm col-5">
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
                        <td style="vertical-align: middle;">
                            <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $row['product_name']; ?>" width="50" height="50" class="img-fluid">
                            <?php echo htmlspecialchars($item['product_name']); ?>
                        </td>
                        <td style="vertical-align: middle;"><?php echo htmlspecialchars($item['size']); ?></td>
                        <td style="vertical-align: middle;"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td style="vertical-align: middle;">PHP <?php echo number_format($item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    

    <form action="" method="POST" class=" d-flex justify-content-end mx-3">
        <?php if ($order['status'] !== 'paid'): ?>
            <input type="submit" name="status" value="paid" class="btn btn-success">
        <?php else: ?>
            <p class="text-danger m-0 " style="vertical-align: middle;">Order has already been marked as paid.</p>
        <?php endif; ?>
    </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
