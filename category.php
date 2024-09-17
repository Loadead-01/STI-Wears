<?php
    include "function/session_func.php";
    require_once "connect.php";

    // Initialize variables
    $category = null;
    $items = [];
    $category_id = null;

    // Check if 'category_id' is passed via URL and is numeric
    if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
        // Safely cast the category_id to an integer
        $category_id = intval($_GET['category_id']);
        
        // SQL to fetch the category details
        $sql = 'SELECT category_name FROM `admin_account`.`category` WHERE category_id = ?';
        $stmt = mysqli_prepare($conn2, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $category_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $category = mysqli_fetch_assoc($result);

        // Check if category was found
        if ($category) {
            // SQL to fetch items in this category
            $sql_items = 'SELECT i.product_name, i.stock, i.price, i.product_id, im.image_path
                          FROM item i
                          LEFT JOIN item_image im ON i.product_id = im.item_id
                          WHERE i.category_id = ?';
            $stmt_items = mysqli_prepare($conn2, $sql_items);
            mysqli_stmt_bind_param($stmt_items, 'i', $category_id);
            mysqli_stmt_execute($stmt_items);
            $result_items = mysqli_stmt_get_result($stmt_items);

            // Fetch all items for the category
            while ($row = mysqli_fetch_assoc($result_items)) {
                $items[] = $row;
            }
        } else {
            echo "No category found for ID: " . $category_id . "<br>";
            exit;
        }
    } else {
        echo "No valid category ID in URL.<br>";
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['category_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet" />
</head>
<body class="bg-light justify-content-center">
    <?php include 'header.php'; ?>

    <section id="category-items" class="container mt-5">
        <h2><?php echo htmlspecialchars($category['category_name']); ?></h2>

        <div class="d-flex flex-wrap col-12 border justify-content-center color-white p-5">
            <?php if (!empty($items)) { ?>
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
                                    <a class='btn btn-success' href='item.php?id=<?php echo htmlspecialchars($item['product_id']); ?>'>Buy now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-center">No items available in this category.</p>
            <?php } ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
