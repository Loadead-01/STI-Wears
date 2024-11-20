<?php
include 'function/admin_session_func.php';
include 'connect.php';

$sql = "SELECT * FROM `user_account`.`order`  WHERE `status` = 'pending' ORDER BY `order_id` DESC ";
$result = $conn->query($sql);

$sql_cancel_orders = "SELECT order_id 
                      FROM `user_account`.`order` 
                      WHERE ordered_date < (CURRENT_TIMESTAMP - INTERVAL 24 HOUR) 
                      AND status = 'pending' 
                      AND payment_method = 'cashier'";

$stmt_cancel_orders = $conn2->prepare($sql_cancel_orders);
$stmt_cancel_orders->execute();
$result_cancel_orders = $stmt_cancel_orders->get_result();

while ($row = $result_cancel_orders->fetch_assoc()) {
    $order_id = $row['order_id'];
    $sql_update_status = "UPDATE `user_account`.`order` SET status = 'cancelled' WHERE order_id = ?";
    $stmt_update_status = $conn2->prepare($sql_update_status);
    $stmt_update_status->bind_param('i', $order_id);
    $stmt_update_status->execute();
}

$month = 11;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $month = isset($_POST['month']) ? $_POST['month'] : 11;
}

$month_name = '';
switch ($month) {
    case 1:
        $month_name = "January";
        break;
    case 2:
        $month_name = "February";
        break;
    case 3:
        $month_name = "March";
        break;
    case 4:
        $month_name = "April";
        break;
    case 5:
        $month_name = "May";
        break;
    case 6:
        $month_name = "June";
        break;
    case 7:
        $month_name = "July";
        break;
    case 8:
        $month_name = "August";
        break;
    case 9:
        $month_name = "September";
        break;
    case 10:
        $month_name = "October";
        break;
    case 11:
        $month_name = "November";
        break;
    case 12:
        $month_name = "December";
        break;
}

$sql_orders = "SELECT status, COUNT(*) AS order_count 
FROM `order` 
WHERE MONTH(ordered_date) = ? AND YEAR(ordered_date) = YEAR(CURRENT_DATE)
GROUP BY status";

$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param('i', $month);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

$pending_count = $paid_count = $cancell_count = 0;

while ($row = $result_orders->fetch_assoc()) {
    switch ($row['status']) {
        case 'pending':
            $pending_count = $row['order_count'];
            break;
        case 'paid':
            $paid_count = $row['order_count'];
            break;
        case 'cancelled':
            $cancell_count = $row['order_count'];
            break;
    }
}





?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STI Wears</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
</head>

<body class="bg-light">
    <?php include 'admin_header.php';  ?>

    <div class="p-3">
    <div class="container">
        <div class="row">
            <section class="mt-4 col-sm-6 col-12">
                <div class="container p-0">

                    <div class="row ">
                        <h5 class="text-center p-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;"><?php echo $month_name; ?> sales record</h4>
                    </div>
                    <div class="row">
                        <div class="div border shadow-sm bg-white p-0 col-4">
                            <p class="text-center border p-2 bg-light"><strong>Pending</strong></p>
                            <h1 class="text-center m-4"><?php echo "$pending_count" ?></h1>
                        </div>
                        <div class="div border shadow-sm bg-white col-4 p-0">
                            <p class="text-center border p-2 bg-light"><strong>Paid</strong></p>
                            <h1 class="text-center m4"><?php echo "$paid_count" ?></h1>
                        </div>
                        <div class="div border shadow-sm bg-white col-4 p-0">
                            <p class="text-center border p-2 bg-light"><strong>Cancelled</strong></p>
                            <h1 class="text-center m4"><?php echo "$cancell_count" ?></h1>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <form action="admin_dashboard.php" method="post" class="lh-1 m-0">
                            <div class="row">
                                <select class="form-select" id="month" name="month">
                                    <option value="" selected disabled>Select month</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <input class="btn btn-success" type="submit" value="Go">
                            </div>
                        </form>
                    </div>
                </div>

            </section>

            <section id="stockAlert" class="mt-4 p-0 col-sm-6 col-12">
                <div class="container p-0 rounded-4 border shadow-sm">

                    <h5 class="text-center p-2" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important; font-weight: bold !important;"> Low stock items</h4>

                    <div class="table-responsive">
                        <table class="table table-striped shadow-sm table-white table-hover border">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Size</th>
                                    <th>Quantity</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                               
                                $stock_sql = "SELECT i.product_name, d.size, d.stock, d.price, im.image_path
                            FROM admin_account.item i
                            JOIN admin_account.item_details d ON i.product_id = d.item_id
                            LEFT JOIN admin_account.item_image im ON i.product_id = im.item_id
                            WHERE d.stock <= 20";

                                $stmt_stock_sql = $conn2->prepare($stock_sql);
                                $stmt_stock_sql->execute();
                                $result_stock_sql = $stmt_stock_sql->get_result();

                                while ($row = $result_stock_sql->fetch_assoc()) { ?>
                                    <tr class="border m-1 p-1">
                                        <td style="vertical-align: middle;">
                                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" width="50" height="50" class="img-fluid">
                                            <?php echo htmlspecialchars($row['product_name']); ?>
                                        </td>
                                        <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['size']); ?></td>
                                        <td style="vertical-align: middle;"><?php echo htmlspecialchars($row['stock']); ?></td>


                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>

                </div>

            </section>
        </div>
    </div>

   

    <section>
        
        <div class="container p-0" >
        <hr>
        <h4 class="text-center p-2 mt-5 border shadow-sm" style=" background-color: #f2f2f2 !important;  color: #333333 !important; font-weight: bold !important;">Recent Orders</h4>
            <div class="table-responsive m-0 p-0">
                <table class="table table-striped shadow-sm table-white table-hover border mt-4">
                    <tr>
                        <th>Order Number</th>
                        <th>Buyer Name</th>
                        <th>Student ID</th>
                        <th>Order Status</th>
                        <th>Total Price</th>
                        <th>Payment Method</th>
                        <th>Action</th>
                    </tr>
                    <?php

                    $break = 0;
                    while ($row = $result->fetch_assoc()) {
                        $break++;
                        echo "<tr>";
                        echo "<td>" . $row["order_id"] . "</td>";
                        echo "<td>" . $row["ordered_by"] . "</td>";
                        echo "<td>" . $row["student_id"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "<td>" . $row["total_price"] . "</td>";
                        echo "<td>" . $row["payment_method"] . "</td>";
                        echo "<td><a class='btn btn-success' href='order_details.php?order_id=" . $row["order_id"] . "'>View Details</a></td>";
                        echo "</tr>";

                        if ($break >= 5) {
                            break;
                        }
                    }
                    ?>
                </table>
            </div>
            <a class='btn btn-success' href='order_record.php'> View More Orders </a>
        </div>
    </section>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>