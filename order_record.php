<?php include 'function/admin_session_func.php';
require_once 'connect.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $sql_check_order = "SELECT * FROM `order` WHERE `order_id` = '$order_id'";
    $result_check_order = mysqli_query($conn, $sql_check_order);
    if (mysqli_num_rows($result_check_order) > 0) {
        header("Location: order_details.php?order_id=$order_id");
        exit;
    } else {
        header("Location: order_error.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'poppins', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 0;
        }

        @font-face {
            font-family: moderniz;
            src: url(font/Moderniz.otf);
        }

        html {
            scroll-padding-top: 145px;
        }
    </style>
</head>

<body>

    <div class="sticky-top">
        <nav class="navbar navbar-expand-sm color-blue sticky-top col-12" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important; font-weight: bold !important; ">
            <div class="container-xxl align-items-center ">
                <a id="logo" class="navbar-brand mx-4 px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="admin_dashboard.php">STI Wears</a>
                <div class="d-flex col-6 justify-content-end align-items-center">
                    <p class=" col-auto m-0 h-100 align-items-center d-none d-sm-block me-3"> <?php echo $_SESSION['admin']; ?> | <?php echo "admin"; ?> </p>

                    <button class="btn btn-light col-3 px-1 me-2" style="min-width: 75px; max-width:75px; max-height: 50px;" type="offcanvas" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"> Menu</button>
                    <a href="function/logout_func.php" class="btn btn-warning col-3 px-1" style="min-width: 75px; max-width:75px; max-height: 50px;">Logout</a>

                </div>
            </div>
        </nav>


        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">Admin Panel Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <hr>
                <div class="row gy-2 px-2">
                    <a href="admin_dashboard.php" class="btn btn-primary"> Dashboard </a>

                    <a href="add_item.php" class="btn btn-primary"> Add new item </a>
                    <a href="stock_management.php" class="btn btn-primary"> Stock Management </a> <!-- Restock, Remove item -->
                    <a href="order_record.php" class="btn btn-primary"> Order </a> <!-- Pending orders! -->

                </div>
            </div>
        </div>


        <div class="bg-light">
            <nav>
                <ul class="nav nav-tabs d-flex justify-content-evenly align-items-center">
                    <li class="nav-item m-2">
                        <a class="nav-link text-white bg-primary btn" href="#pending">Pending Orders</a>
                    </li>
                    <li class="nav-item m-2">
                        <a class="nav-link text-white bg-primary btn" href="#paid">Paid Orders</a>
                    </li>
                    <li class="nav-item m-2">
                        <a class="nav-link text-white bg-primary btn" href="#cancelled">Cancelled Orders</a>
                    </li>
                </ul>
            </nav>
        </div>

    </div>

    <div class="container-lg px-5 mt-4">



        <div class="bg-light border shadow-sm mb-5 p-2">
            <p class="bg-light fw-bold m-0">Search Order</p>
            <form action="" method="get" class="d-flex justify-content-center">
                <input type="number" class="col-9 me-1 w-75" name="order_id" placeholder="Enter Order ID" required>
                <button type="submit" class="btn col-2 w-25 btn-primary">Search</button>
            </form>
        </div>

        <section id="pending">
            <div class="bg-light border shadow-sm mb-5 p-2">
                <p class="bg-light fw-bold">Pending Orders</p>
                <script>
                    $(document).ready(function() {
                        var pendingCount = 5;
                        $("#more_pending").click(function() {
                            pendingCount += 5;
                            $("#table_pending").load("ajax_orderLoad/load_pending.php", {
                                pendingNewCount: pendingCount
                            });
                        });
                    });
                </script>
                <div class="table-responsive mb-4">
                    <table id="table_pending" class="table table-striped table-white">
                        <tr class="small">
                            <th style="vertical-align: middle" ;>Order ID</th>

                            <th style="vertical-align: middle" ;>Student Name</th>
                            <th style="vertical-align: middle" ;>Payment Method</th>
                            <th style="vertical-align: middle" ;>Order Date</th>
                            <th style="vertical-align: middle" ;>Total Price</th>
                            <th style="vertical-align: middle" ;>Action</th>
                        </tr>
                        <?php
                        $sql_pending  = "SELECT * FROM `order` WHERE `status` = 'pending' ORDER BY `order_id` DESC";
                        $result_pending = mysqli_query($conn, $sql_pending);


                        while ($row = mysqli_fetch_assoc($result_pending)) { ?>
                            <tr class="fs-0">
                                <td style="vertical-align: middle" ;><?php echo $row['order_id'] ?></td>

                                <td style="vertical-align: middle" ;><?php echo $row['ordered_by'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['payment_method'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['date_display'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['total_price'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo "<a href='order_details.php?order_id=" . $row['order_id'] . "' class='btn btn-success fs-6 py-1 px-1'> View Details </a>"; ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>
                <div class="text-start">
                    <button id="more_pending" class="btn btn-primary text-end">View More</button>
                </div>
            </div>
        </section>

        <section id="paid">
            <div class="bg-light border shadow-sm mb-5 p-2">
                <script>
                    $(document).ready(function() {
                        var paidCount = 5;
                        $("#more_paid").click(function() {
                            paidCount += 5;
                            $("#table_paid").load("ajax_orderLoad/load_paid.php", {
                                paidNewCount: paidCount
                            });
                        });
                    });
                </script>
                <p class="bg-light fw-bold">Paid Orders</p>
                <div class="table-responsive mb-4">
                    <table id="table_paid" class="table table-striped table-white">
                        <tr class="small">
                            <th style="vertical-align: middle" ;>Order ID</th>
                            <th style="vertical-align: middle" ;>Student Name</th>
                            <th style="vertical-align: middle" ;>Payment Method</th>
                            <th style="vertical-align: middle" ;>Order Date</th>
                            <th style="vertical-align: middle" ;>Total Price</th>
                            <th style="vertical-align: middle" ;>Action</th>
                        </tr>
                        <?php
                        $sql_pending  = "SELECT * FROM `order` WHERE `status` = 'paid' ORDER BY `order_id` DESC LIMIT 5";
                        $result_pending = mysqli_query($conn, $sql_pending);


                        while ($row = mysqli_fetch_assoc($result_pending)) { ?>
                            <tr class="fs-0">
                                <td style="vertical-align: middle" ;><?php echo $row['order_id'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['ordered_by'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['payment_method'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['date_display'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['total_price'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo "<a href='order_details.php?order_id=" . $row['order_id'] . "' class='btn btn-success fs-6 py-1 px-1'> View Details </a>"; ?></td>
                            </tr>
                        <?php } ?>
                    </table>

                </div>
                <div class="text-start">
                    <button id="more_paid" class="btn btn-primary text-end">View More</button>
                </div>
            </div>
        </section>


        <section id="cancelled" class="pt-4">
            <div class="bg-light border shadow-sm mb-5 p-2">
                <script>
                    $(document).ready(function() {
                        var cancellCount = 5;
                        $("#more_cancell").click(function() {
                            cancellCount = cancellCount + 5;
                            $("#table_cancell").load("ajax_orderLoad/load_cancell.php", {
                                cancellNewCount: cancellCount
                            });
                        });
                    });
                </script>

                <p class="bg-light fw-bold">Cancelled Orders</p>
                <div class="table-responsive mb-4">
                    <table id="table_cancell" class="table table-striped table-white">
                        <tr class="small">
                            <th style="vertical-align: middle" ;>Order ID</th>
                            <th style="vertical-align: middle" ;>Student Name</th>
                            <th style="vertical-align: middle" ;>Payment Method</th>
                            <th style="vertical-align: middle" ;>Order Date</th>
                            <th style="vertical-align: middle" ;>Total Price</th>
                            <th style="vertical-align: middle" ;>Action</th>
                        </tr>


                        <?php
                        $sql_pending  = "SELECT * FROM `order` WHERE `status` = 'cancelled' ORDER BY `order_id` DESC LIMIT 5";
                        $result_pending = mysqli_query($conn, $sql_pending);
                        while ($row = mysqli_fetch_assoc($result_pending)) { ?>
                            <tr class="fs-0">
                                <td style="vertical-align: middle" ;><?php echo $row['order_id'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['ordered_by'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['payment_method'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['date_display'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo $row['total_price'] ?></td>
                                <td style="vertical-align: middle" ;><?php echo "<a href='order_details.php?order_id=" . $row['order_id'] . "' class='btn btn-success fs-6 py-1 px-1'> View Details </a>"; ?></td>

                            </tr>
                        <?php } ?>
                    </table>

                </div>
                <div class="text-start">
                    <button id="more_cancell" class="btn btn-primary text-end">View More</button>
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