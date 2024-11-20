<?php
include 'function/admin_session_func.php';
include 'connect.php';
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$sql_order = "SELECT o.total_price, o.gcash_receipt, o.ordered_by, o.ordered_date, im.image_path, o.date_display, o.payment_method, o.status, o.student_id, d.size, d.quantity, d.price, i.product_name, i.product_id
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
    header("Location: order_error.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $_POST['status'];

    if ($status == 'paid') {
        $sql_statss = "UPDATE `user_account`.`order` SET `status` = ? WHERE `order_id` = ?";
        $stmt_status = $conn2->prepare($sql_statss);
        $stmt_status->bind_param('si', $status, $order_id);
        $stmt_status->execute();

        //each item in order
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

        //sum stock
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
                    header("Location: #");
                } else {
                    echo "Error updating item stock: " . $stmt_update_item_stock->error . "<br>";
                }
            } else {
                echo "Prepare failed for item stock update: " . $conn2->error . "<br>";
            }
        } else {
            echo "Error fetching total stock: " . $conn2->error . "<br>";
        }
    } else if ($status == 'cancelled') {

        $sql_status = "UPDATE `user_account`.`order` SET `status` = ? WHERE `order_id` = ?";
        $stmt_status = $conn2->prepare($sql_status);
        $stmt_status->bind_param('si', $status, $order_id);
        $stmt_status->execute();
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

                <h6 style="text-align: center; vertical-align: center; background-color: #FFE10F !important;" class="p-2 m-0 border fw-bold">Order Number</h6>

                <p class="d-flex justify-content-center m-0 align-items-center p-3 fs-1" style="vertical-align: middle;"> <?php echo $order_id ?> </p>

            </div>
            <div class="col-sm-6 bg-white white border shadow-sm p-0 mt-3 mt-sm-0 ">
                <h6 style="text-align: center; vertical-align: center; background-color: #FFE10F !important;" class="p-2 m-0 border fw-bold">Order Details</h6>

                <div class="p-2">
                    <p class="m-0"><strong>Total Price: </strong><?php echo number_format($order['total_price'], 2); ?></p>
                    <p class="m-0"><strong>Order Status: </strong><?php echo htmlspecialchars($order['status'], 2); ?></p>
                    <p class="m-0"><strong>Payment Method: </strong><?php echo htmlspecialchars($order['payment_method'], 2); ?></p>


                    <div class="modal fade" id="receipt" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-body">
                                    <img src="<?php echo $order['gcash_receipt'] ?>" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="m-0"><strong>Ordered By: </strong> <?php echo htmlspecialchars($order['ordered_by']); ?></p>
                    <p class="m-0"><strong>Student ID : </strong> <?php echo htmlspecialchars($order['student_id']); ?> </p>
                    <p class="m-0"><strong>Ordered Date: </strong> <?php echo htmlspecialchars($order['date_display']); ?></p>
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
        echo '<p class="small m-3 text-black-50" style="text-align: center;">Order automatically cancelled after a day of unsuccessfull payment, or if admin cancelled order</p>';
    } else {
        echo '<p class="small m-3 text-black-50" style="text-align: center;">Print and give the payment receipt to the customer</p>';
    }
    ?>

    <div class="container  ">
        <div class=" mt-4 m-0">
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

        <?php


        if ($order['status'] != "cancelled") { ?>
            <form method="POST" class=" d-flex justify-content-end mx-3">

                <?php
                if ($order['payment_method'] == "gcash") {
                    echo '<button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#receipt">
                                            Gcash Receipt
                                        </button>';
                }
                if ($order['status'] !== 'paid') { ?>

                    <button type="button" class="btn btn-success me-3" data-bs-toggle="modal" data-bs-target="#confirmation">
                        paid
                    </button>

                    <div class="modal fade" id="confirmation" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-body">
                                    Mark the order as paid?
                                </div>
                                <div class="modal-footer p-0">
                                    <!-- <input type="hidden" name="status" value="paid" class="btn btn-success"> -->
                                    <button type="submit" name="status" value="paid" class="btn btn-success">yes</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancell">
                        cancel
                    </button>

                    <div class="modal fade" id="cancell" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-body">
                                    Mark the order as cancelled?
                                </div>
                                <div class="modal-footer p-0">
                                    <!-- <input type="hidden" name="status" value="cancelled" class="btn btn-success"> -->
                                    <button type="submit" name="status" value="cancelled" class="btn btn-success">yes</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } elseif ($order['status'] == "paid") { ?>
                    <p class="text-danger m-0 " style="vertical-align: middle;">Order has been marked as paid.</p>
                <?php } elseif ($order['status'] == "cancelled") { ?>
                    <p class="text-danger m-0 " style="vertical-align: middle;">Order has been marked as cancelled</p>
                <?php } ?>
            </form>
        <?php } else if ($order['status'] == "cancelled") { ?>
            <div class="text-end">
                <p class='text-danger m-0' style='vertical-align: middle;'> Order has already been marked as cancelled. </p>
            </div>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>