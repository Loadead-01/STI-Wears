<?php
    include "function/session_func.php";
    require_once "connect.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
    <link href="style.css" rel="stylesheet" />
</head>

<body class="bg-light justify-content-center">
    <?php include 'header.php'; ?>

    <section id="new-item" class="border border-danger justify-content-center d-flex">
        <div class="container-lg row p-2 m-0 d-flex justify-content-center mt-5 gx-4">

            <?php
            // Fetch all departments
            $sql1 = 'SELECT * FROM `admin_account`.`department`'; //get everything (d.name, d.id) from department table
            $result1 = mysqli_query($conn2, $sql1); //sehnd the request from sql1
            $departments = mysqli_fetch_all($result1, MYSQLI_ASSOC); //retrive the data 
            ?>

            <section class="justify-content-center d-flex">
                <div class="container-lg row p-2 m-0 d-flex justify-content-center mt-5 gx-4">
                    <?php foreach ($departments as $department) { ?><!--  every row in department -->
                        <div class="col-sm-6 row d-flex justify-content-center m-0 p-0">
                            <p class="color-yellow m-0 p-0 text-center border border-primary" style="height: 25px;"><?php echo htmlspecialchars($department['department_name']); ?></p>

                            <div class="row border d-flex justify-content-between px-5 py-3 color-white border h-100 ">
                                <?php
                                // Fetch categories related to the current department
                                $sql2 = 'SELECT * FROM `admin_account`.`category` WHERE `department_id` = ?'; //request 
                                $stmt = mysqli_prepare($conn2, $sql2); //connect, query/request (param)
                                mysqli_stmt_bind_param($stmt, 'i', $department['department_id']);  //statement, datatype, array
                                mysqli_stmt_execute($stmt);
                                $result2 = mysqli_stmt_get_result($stmt);
                                $categories = mysqli_fetch_all($result2, MYSQLI_ASSOC);
                                ?>

                                <?php if (!empty($categories)) { ?>
                                    <?php foreach ($categories as $category) { ?>
                                        <a  href="category.php?category_id=<?php echo urlencode($category['category_id']); ?>" class="col-md-5 border m-2 bg-light text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($category['category_name']) ?>
                                        </a>
                                
                                    <?php } ?>
                                <?php } else { ?>
                                    <p class="text-center">No categories found for this department.</p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
            <hr class="m-4">
            <h4>New Item</h4>
            <div class="d-flex flex-wrap col-12 border justify-content-center color-white p-5">
                <?php
                // SQL query to get product details along with their image paths from item_image
                $sql3 = 'SELECT i.product_name, i.stock, i.price, i.product_id, im.image_path 
                            FROM item i 
                            LEFT JOIN item_image im ON i.product_id = im.item_id';
                $result3 = mysqli_query($conn2, $sql3);
                $items = mysqli_fetch_all($result3, MYSQLI_ASSOC);
                ?>

                <?php foreach ($items as $item) { ?>
                    <div class="card col-4 border m-2" style="min-width: 235px; min-height: 100px">
                        <div class="row m-0 p-0">
                            <div class="col-sm-6 p-0">
                                <!-- Dynamically load the image from the database -->
                                <img src="<?php echo htmlspecialchars($item['image_path'] ?? 'assets/default_image.png'); ?>" class="border border-success img-fluid" alt="Item Image">
                            </div>
                            <div class="col-sm-6 d-flex align-items-center justify-content-center p-0 m-0">
                                <div class="card-body px-2 p-0">
                                    <h5 class="lh-1 fs-md-1"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                    <hr>
                                    <p class="lh-1 text-grey fs-6 p-0 m-0">Stock: <?php echo htmlspecialchars($item['stock']); ?></p>
                                    <p class="fs-6 lh-1 text-grey p-0 m-0">PHP <?php echo htmlspecialchars($item['price']); ?></p>
                                    <?php echo "<a class='btn btn-success' href='item.php?id=" . $item['product_id'] . "'>Buy now</a>"; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>