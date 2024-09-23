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
    $sql_order = "SELECT o.total_price, o.ordered_by, o.ordered_date, o.student_id, d.size, d.quantity, d.price, i.product_name
                  FROM `user_account`.`order` o
                  JOIN `user_account`.`order_detail` d ON o.order_id = d.order_id
                  JOIN item i ON d.product_name = i.product_name
                  WHERE o.order_id = ? AND o.account_id = ?";
    
    $stmt_order = $conn2->prepare($sql_order);
    if (!$stmt_order) { die('Prepare failed: ' . $conn2->error); }
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>


<div class="container mt-4">
    <h1>Order Details</h1>
    
    
    <p><strong>Total Price:</strong><?php echo number_format($order['total_price'], 2); ?></p>
    <p><strong>Ordered By:</strong> <?php echo htmlspecialchars($order['ordered_by']); ?></p>
    <p><strong>Student ID : </strong> <?php echo htmlspecialchars($order['student_id']); ?> </p>
    <p><strong>Ordered Date:</strong> <?php echo htmlspecialchars($order_date); ?></p>
    <h2>Order Number: <?php echo $order_id; ?></h2>
    <p>Kindly proceed to the cashier, provide your order number, and pay the total price of your order.</p>

    <h2>Order Items:</h2>
    <table class="table table-bordered">
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
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($item['size']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>PHP <?php echo number_format($item['price'], 2); ?></td>
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
