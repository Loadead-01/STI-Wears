<?php
include 'function/session_func.php'; // Ensure session management is handled
require_once('connect.php');

// Get the logged-in user's account_id from the session
$account_id = $_SESSION['account_id'];

// Get the order ID from the query string
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
unset($_SESSION['checkout_data']);

if ($order_id > 0) {
    // Query to get order details, ensuring it belongs to the logged-in user
    $sql_order = "SELECT o.total_price, o.ordered_by, o.ordered_date, o.payment_method, o.status, im.image_path, o.student_id, d.size, d.quantity, d.price, i.product_name
                  FROM `user_account`.`order` o
                  JOIN `user_account`.`order_detail` d ON o.order_id = d.order_id
                  JOIN item i ON d.product_name = i.product_name
             JOIN admin_account.item_image im ON i.product_id = im.item_id

                  WHERE o.order_id = ? AND o.account_id = ?";

    $stmt_order = $conn2->prepare($sql_order);
    if (!$stmt_order) {
        die('Prepare failed: ' . $conn2->error);
    }
    $stmt_order->bind_param('ii', $order_id, $account_id);
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
} else {
    echo "Invalid order ID.";
    exit;
}

// Get the current timestamp
$current_timestamp = time();

// Calculate the timestamp for 24 hours ago
$cancel_timestamp = $current_timestamp - 86400; // 86400 is the number of seconds in 24 hours

// Query to get orders that are more than 24 hours old
$sql_cancel_orders = "SELECT order_id FROM `user_account`.`order` WHERE ordered_date < FROM_UNIXTIME(?) AND status = 'pending' AND payment_method = 'cashier'";
$stmt_cancel_orders = $conn2->prepare($sql_cancel_orders);
$stmt_cancel_orders->bind_param('i', $cancel_timestamp);
$stmt_cancel_orders->execute();
$result_cancel_orders = $stmt_cancel_orders->get_result();

// Update the order status to "cancelled" for orders that are more than 24 hours old
while ($row = $result_cancel_orders->fetch_assoc()) {
    $order_id = $row['order_id'];
    $sql_update_status = "UPDATE `user_account`.`order` SET status = 'cancelled' WHERE order_id = ?";
    $stmt_update_status = $conn2->prepare($sql_update_status);
    $stmt_update_status->bind_param('i', $order_id);
    $stmt_update_status->execute();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include 'header.php'; ?>


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
            echo '<p class="small m-3 text-black-50" style="text-align: center;">Kindly proceed to the cashier, provide your order number, and pay the total price of your order.</p>';
        } elseif ($order['payment_method'] == "gcash") {
            echo '<p class="small m-3 text-black-50" style="text-align: center;">Kindly proceed to the cashier, provide your order number, and present your gcash receipt for validation.</p>';
        }
    } elseif ($order['status'] == "cancelled") {
        echo '<p class="small m-3 text-black-50" style="text-align: center;">Order automatically cancelled after a day of unsuccessfull payment</p>';
    } else {
        echo '<p class="small m-3 text-black-50" style="text-align: center;">Order Paid, Make sure to claim your item at the proware</p>';


    }
    ?>







    <div class="container">
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


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>