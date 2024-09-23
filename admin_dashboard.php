<?php 
include 'function/admin_session_func.php' ;
include 'connect.php';

$sql = "SELECT * FROM `user_account`.`order`  WHERE `status` = 'pending' ORDER BY `order_id` DESC ";
$result = $conn->query($sql);


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
<body>
    <?php include 'admin_header.php';  ?>
    
    <div class="container">
    <table class="table">
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
            while ($row = $result->fetch_assoc()){
                $break++;
                echo "<tr>";
                echo "<td>" . $row["order_id"] . "</td>";
                echo "<td>" . $row["ordered_by"] . "</td>";
                echo "<td>" . $row["student_id"] . "</td>";
                echo "<td>" . $row["status"] . "</td>";
                echo "<td>" . $row["total_price"] . "</td>";
                echo "<td>" . $row["payment_method"]. "</td>";
                echo "<td><a class='btn btn-success' href='order_details.php?order_id=" . $row["order_id"] . "'>View Details</a></td>";
                echo "</tr>";
              
                if ($break >= 5) {
                    break;
                }
            }  
        
            ?>
        
    </table>
    <a class='btn btn-success' href='order_management.php'> View More Orders </a>

</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>