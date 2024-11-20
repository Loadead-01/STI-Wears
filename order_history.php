<?php
include 'function/session_func.php';
require_once 'connect.php';

// Get filter value from the request (default is 'all')
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Adjust the SQL query based on the filter
$sql = "SELECT * FROM `order` WHERE `account_id` = '" . $_SESSION['account_id'] . "'";
if ($filter === 'paid') {
    $sql .= " AND `status` = 'paid'";
} elseif ($filter === 'pending') {
    $sql .= " AND `status` = 'pending'";
} elseif ($filter === 'cancelled') {
    $sql .= " AND `status` = 'cancelled'";
}
$sql .= " ORDER BY `order_id` DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
</head>

<body class="bg-light">
    <?php include 'header.php' ?>
    <div class="container mt-5">
        <div class="row bg-white border shadow-sm mb-3 p-2">
        <h5 class= "col-6 d-flex justify-content-start align-items-center m-0" style="vertical-align: middle;">Order History</h5>
        <div class="col-6 d-flex justify-content-end text-end align-items-center">
            <!-- Filter Dropdown -->
            <form method="GET" action="">
                <div class="row">
            
                <select name="filter" id="filter" class="form-select m-0" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="paid" <?php echo $filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="cancelled" <?php echo $filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
                </div>
            </form>
        </div>
        </div>
        

        <div class="container-lg d-flex justify-content-center p-0">
            <div class="col-12 border bg-white d-flex p-0 justify-content-center">
                <div class="table-responsive m-0 p-0 col-12">
                    <table class="table table-dark table-striped p-0">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Total Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td class="small"><?php echo $row['order_id']; ?></td>
                                        <td class="small">
                                            <?php
                                            if ($row['status'] == 'paid') {
                                                echo "<span style='color: green;'>Paid</span>";
                                            } elseif ($row['status'] == 'pending') {
                                                echo "<span style='color: yellow;'>Pending</span>";
                                            } elseif ($row['status'] == 'cancelled') {
                                                echo "<span style='color: red;'>Cancelled</span>";
                                            } else {
                                                echo $row['status'];
                                            }
                                            ?>
                                        </td>
                                        <td class="small"><?php echo $row['payment_method']; ?></td>
                                        <td class="small"><?php echo $row['total_price']; ?></td>
                                        <td class="small">
                                            <a href="order.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-success fs-6 py-1 px-1">View Details</a>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center">No orders found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
