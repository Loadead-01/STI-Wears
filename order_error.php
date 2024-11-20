<?php
    include 'function/admin_session_func.php';
    include_once 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
</head>
<body class="bg-light d-flex flex-column">
    <?php include 'admin_header.php' ?>
    
    <div class="container-sm bg-white p-5 mt-5" >
        <p class="fs-2 text-center text-danger" style="font-family: 'moderniz';">Order not found
        </p>
        <p class="fs-5 text-center">Kindly double check the order ID</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>