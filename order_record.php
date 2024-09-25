<?php include 'function/admin_session_func.php';
require_once 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php include 'admin_header.php' ?>
    <div class="sticky-top bg-light py-2 ">
        <nav >
            <ul class="nav nav-tabs d-flex justify-content-evenly">
                <li class="nav-item">
                    <a class="nav-link text-white bg-primary" href="#pending">Pending Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white bg-primary" href="#paid">Paid Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white bg-primary" href="#cancelled">Cancelled Orders</a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="container-lg px-5 mt-4">

    

        <div class="bg-light border shadow-sm mb-5 p-2">
            <p class="bg-light fw-bold m-0">Search Order</p>
            <form action="" method="get" class="d-flex justify-content-center">
                <input type="number" class="col-9 me-1 w-75" name="order_id" placeholder="Enter Order ID" required>
                <button type="submit" class="btn col-2 w-25 btn-primary">Search</button>
            </form>
        </div>

        <?php
        if (isset($_GET['order_id'])) {
            $order_id = $_GET['order_id'];
            $sql_check_order = "SELECT * FROM `order` WHERE `order_id` = '$order_id'";
            $result_check_order = mysqli_query($conn, $sql_check_order);
            if (mysqli_num_rows($result_check_order) > 0) {
                header("Location: order_details.php?order_id=$order_id");
                exit;
            } else {
                echo "<div class='alert alert-danger' role='alert'>Order ID $order_id does not exist!</div>";
            }
        }
        ?>

        <section id="pending">
        <div class="bg-light border shadow-sm mb-5 p-2">
            <p class="bg-light fw-bold">Pending Orders</p>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-white">
                    <tr class="small">
                        <th>Order ID</th>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Order Date</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $sql_pending  = "SELECT * FROM `order` WHERE `status` = 'pending' ORDER BY `order_id` DESC";
                    $result_pending = mysqli_query($conn, $sql_pending);


                    while ($row = mysqli_fetch_assoc($result_pending)) { ?>
                        <tr class="fs-0">
                            <td><?php echo $row['order_id'] ?></td>
                            <td><?php echo $row['ordered_by'] ?></td>
                            <td><?php echo $row['student_id'] ?></td>
                            <td><?php echo $row['ordered_date'] ?></td>
                            <td><?php echo $row['total_price'] ?></td>
                            <td><?php echo "<a href='order_details.php?order_id=" . $row['order_id'] . "' class='btn btn-success fs-6 py-1 px-1'> View Details </a>"; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        </section>

        <section id="paid" >
        <div class="bg-light border shadow-sm mb-5 p-2">

            <p class="bg-light fw-bold">Paid Orders</p>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-white">
                    <tr class="small">
                        <th>Order ID</th>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Order Date</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $sql_pending  = "SELECT * FROM `order` WHERE `status` = 'paid' ORDER BY `order_id` DESC";
                    $result_pending = mysqli_query($conn, $sql_pending);


                    while ($row = mysqli_fetch_assoc($result_pending)) { ?>
                        <tr class="fs-0">
                            <td><?php echo $row['order_id'] ?></td>
                            <td><?php echo $row['ordered_by'] ?></td>
                            <td><?php echo $row['student_id'] ?></td>
                            <td><?php echo $row['ordered_date'] ?></td>
                            <td><?php echo $row['total_price'] ?></td>
                            <td><?php echo "<a href='order_details.php?order_id=" . $row['order_id'] . "' class='btn btn-success fs-6 py-1 px-1'> View Details </a>"; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        </section>


        <section id="cancelled">
        <div class="bg-light border shadow-sm mb-5 p-2">

            <p class="bg-light fw-bold">Cancelled Orders</p>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-white">
                    <tr class="small">
                        <th>Order ID</th>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Order Date</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $sql_pending  = "SELECT * FROM `order` WHERE `status` = 'cancelled' ORDER BY `order_id` DESC";
                    $result_pending = mysqli_query($conn, $sql_pending);


                    while ($row = mysqli_fetch_assoc($result_pending)) { ?>
                        <tr class="fs-0">
                            <td><?php echo $row['order_id'] ?></td>
                            <td><?php echo $row['ordered_by'] ?></td>
                            <td><?php echo $row['student_id'] ?></td>
                            <td><?php echo $row['ordered_date'] ?></td>
                            <td><?php echo $row['total_price'] ?></td>
                            <td><?php echo "<a href='order_details.php?order_id=" . $row['order_id'] . "' class='btn btn-success fs-6 py-1 px-1'> View Details </a>"; ?></td>

                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        </section>
    </div>




    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>