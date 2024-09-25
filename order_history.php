<?php
include 'function/session_func.php';
require_once 'connect.php';

$sql = "SELECT * FROM `order` WHERE `account_id` = '" . $_SESSION['account_id'] . "' ORDER BY `order_id` DESC";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head >
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
</head>

<body class="bg-light">
    <?php include 'header.php' ?>
    <div class="container mt-5">
    <h5 class="bg-white border shadow-sm p-3">Order History</h5>
    <div class="container-lg d-flex justify-content-center p-0">

        <div class="col-12 border bg-white d-flex p-0 justify-content-center ">
        <div class="table-responsive m-0 p-0 col-12">
            <table class="table table-dark table-striped p-0">
                <tr>
                    <th class="" style="vertical-align: middle;">Order Number</th>
                    <th class=""  style="vertical-align: middle;">Status</th>
                    <th class=""  style="vertical-align: middle;">Payment Method</th>
                    <th class=""  style="vertical-align: middle;">total price</th>
                    <th class=""  style="vertical-align: middle;" >action</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($result)) {  ?>
    <tr>
        <td class="small"  style="vertical-align: middle;"><?php echo $row['order_id']; ?></td>
        <td class="small"  style="vertical-align: middle;">
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
        <td class="small"  style="vertical-align: middle;"><?php echo $row['payment_method']; ?></td>
        <td class="small"  style="vertical-align: middle;"><?php echo $row['total_price']; ?></td>
        <td class="small"  style="vertical-align: middle;"><?php echo "<a href='order.php?order_id=" . $row['order_id'] . "' class='btn btn-success fs-6 py-1 px-1'> View Details </a>"; ?></td>
    </tr>
<?php } ?>


            </table>
        </div>
        </div>
    </div>
    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>