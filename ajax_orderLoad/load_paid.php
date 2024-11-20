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
    include '../connect.php';

    $paidNewCount = $_POST['paidNewCount'];

    $sql_pending  = "SELECT * FROM `order` WHERE `status` = 'paid' ORDER BY `order_id` DESC LIMIT $paidNewCount";
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