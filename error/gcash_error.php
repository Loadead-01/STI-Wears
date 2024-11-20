<?php
    include '../function/session_func.php';
    include_once '../connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
</head>
<body class="bg-light d-flex flex-column">
    <?php include '../header.php' ?>
    
    <div class="container-sm bg-white p-5 mt-5" >
        <p class="fs-2 text-center text-danger" style="font-family: 'moderniz';">ERROR UPLOADING FILE
        </p>
        <p class="fs-5 text-center">Only image type file and file size must not higher than 10mb.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>