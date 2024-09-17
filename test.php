<?php
    if (isset($_POST['submit']) && isset ($_FILES['item_image'])) {
        print_r($_FILES['item_image']);
    } else {
        echo  "No file selected";

    }
     
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="test.php"
    method="post" enctype="multipart/form-data">
    <input type="file" name="item_image">
    <input type="submit" name="submit" value="Upload">
    </form>
</body>
</html>