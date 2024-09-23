<?php
    include 'function/session_func.php';
    require_once 'connect.php';

    $sql =  "SELECT * FROM `order` WHERE `account_id` = '".$_SESSION['account_id']."'";
    $result = mysqli_query($conn, $sql);
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />
</head>
<body>
    <?php include 'header.php'?>

        <h1>Order History</h1>

    <table>
        <tr>
            <th>Order Number</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>total price</th>
            <th>action</th>
        </tr>
        
        <?php while($row = mysqli_fetch_assoc($result)) {  ?>
            <tr>
            <td><?php echo $row['order_id'] ?></td>
            <td><?php echo $row['status'] ?></td>
            <td><?php echo $row['payment_method'] ?></td>
            <td><?php echo $row['total_price'] ?></td>
            <td><?php echo  "button " ?></td>
            </tr>
        <?php }?>
        
    </table>

    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>
</html>