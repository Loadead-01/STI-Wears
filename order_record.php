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

    <div class="container-lg">
        <p>Pending Orders</p>
        <div class="table-responsive">
            <table class="table tabler-striped">
                <tr>
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


                while ($row_pending = mysqli_fetch_assoc($result_pending)) { ?>
                    <tr>
                        <td><?php echo $row_pending['order_id'] ?></td>
                        <td><?php echo $row_pending['ordered_by'] ?></td>
                        <td><?php echo $row_pending['student_id'] ?></td>
                        <td><?php echo $row_pending['ordered_date'] ?></td>
                        <td><?php echo $row_pending['total_price'] ?></td>
                        <td><?php echo $row_pending['order_id'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">Active</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
  </li>
</ul>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>