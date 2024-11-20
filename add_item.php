<?php

include 'function/admin_session_func.php'; 
require_once('connect.php');



$user_id = $_SESSION['admin'];
function getCategories($conn2)
{
    $sql = "SELECT category_id, category_name FROM `admin_account`.`category`";
    $result = $conn2->query($sql);
    $categories = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    } else {
        echo "Error fetching categories: " . $conn2->error;
    }

    return $categories;
}

$categories = getCategories($conn2);
if ($categories === null) {
    $categories = []; // Default to an empty array if fetching failed
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Extract form data
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $sizes = $_POST['sizes'];
    $image_name = $_FILES['item_image']['name'];
    $image_path = 'uploads/' . $image_name;

    // Calculate total stock and minimum price
    $total_stock = 0;
    $min_price = PHP_INT_MAX;

    foreach ($sizes as $size) {
        $total_stock += (int)$size['stock'];
        $min_price = min($min_price, (float)$size['price']);
    }

    // Insert into item table
    $stmt = $conn2->prepare("INSERT INTO item (product_name, stock, price, date, creator, category_id) VALUES (?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("sdisi", $product_name, $total_stock, $min_price, $user_id, $category_id);
    $stmt->execute();
    $item_id = $stmt->insert_id;

    // Insert into item_details table
    $stmt = $conn2->prepare("INSERT INTO item_details (item_id, size, stock, price) VALUES (?, ?, ?, ?)");
    foreach ($sizes as $size) {
        $stmt->bind_param("isii", $item_id, $size['size'], $size['stock'], $size['price']);
        $stmt->execute();
    }

    // Handle image upload
    if (move_uploaded_file($_FILES['item_image']['tmp_name'], $image_path)) {
        $stmt = $conn2->prepare("INSERT INTO item_image (item_id, image_name, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $item_id, $image_name, $image_path);
        $stmt->execute();
    } else {
        // Handle file upload error
    }

    // Redirect or show success message
    header('Location: admin_dashboard.php');
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add New Item</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />

    <style>
        .category-btn {
            margin: 5px;
        }

        .category-container {
            margin-bottom: 20px;
        }

        .selected-category {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <?php include 'admin_header.php'  ?>


    <div class="container mt-4">
        <!-- Include header -->


        <!-- Logout button -->


        <h1>Add New Item</h1>

        <!-- Add New Item Form -->
        <form method="POST" enctype="multipart/form-data">
            <!-- Product Name -->
            <div class="mb-3">
                <label for="product_name" class="form-label">Product Name:</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>

            <!-- Category Section -->
            <div class="mb-3 category-container">
                <label class="form-label">Category:</label>
                <div id="category-buttons">
                    <?php
                    foreach ($categories as $category) {
                        echo "<button type='button' class='btn btn-outline-primary category-btn' data-id='{$category['category_id']}'>{$category['category_name']}</button>";
                    }
                    ?>
                </div>
                <input type="hidden" id="selected_category_id" name="category_id" required>
            </div>

            <!-- Size Section -->
            <div id="size-container">
                <div class="row mb-3 size-row">
                    <div class="col-md-4">
                        <label for="size" class="form-label">Size:</label>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="XXS">XXS</button>
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="XS">XS</button>
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="S">S</button>
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="M">M</button>
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="L">L</button>
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="XL">XL</button>
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="XXL">XXL</button>
                            <button type="button" class="btn btn-outline-secondary size-btn" data-size="XXXL">XXXL</button>
                            <input type="hidden" class="selected-size" name="sizes[0][size]" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stock:</label>
                        <input type="number" class="form-control" name="sizes[0][stock]" required>
                    </div>
                    <div class="col-md-4">
                        <label for="price" class="form-label">Price:</label>
                        <input type="text" class="form-control" name="sizes[0][price]" required>
                    </div>
                    <!-- Remove button -->
                    <div class="col-12 mt-2">
                        <button type="button" class="btn btn-danger remove-size-btn">Remove</button>
                    </div>
                </div>
            </div>

            <!-- Add More Sizes Button -->
            <button type="button" class="btn btn-secondary mb-3" id="add-size-btn">Add Another Size</button>

            <!-- Image Upload Section -->
            <div class="mb-3">
                <label for="item_image" class="form-label">Upload Item Image:</label>
                <input type="file" class="form-control" id="item_image" name="item_image">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <!-- Script to handle adding and removing size rows, selecting sizes, and category selection -->
    <script>
        let sizeIndex = 1;

        // Category button selection handler
        document.getElementById('category-buttons').addEventListener('click', function(e) {
            if (e.target.classList.contains('category-btn')) {
                // Remove 'selected' class from previously selected buttons
                document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('selected-category'));

                // Add 'selected' class to the clicked button
                e.target.classList.add('selected-category');

                // Set the selected category ID in the hidden input field
                document.getElementById('selected_category_id').value = e.target.getAttribute('data-id');
            }
        });

        // Size selection handler
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('size-btn')) {
                e.preventDefault();

                // If the button is already selected or disabled, do nothing
                if (e.target.classList.contains('disabled')) {
                    return;
                }

                // Deselect other buttons
                e.target.closest('.btn-group').querySelectorAll('.size-btn').forEach(btn => {
                    btn.classList.remove('selected');
                });

                // Select clicked button
                e.target.classList.add('selected');

                // Set the value in the hidden input field
                const sizeInput = e.target.closest('.btn-group').querySelector('.selected-size');
                sizeInput.value = e.target.getAttribute('data-size');

                // Disable already selected sizes
                document.querySelectorAll('.size-btn').forEach(btn => {
                    if (btn.classList.contains('selected')) {
                        btn.classList.add('disabled');
                    } else {
                        btn.classList.remove('disabled');
                    }
                });
            }
        });

        // Add more size rows
        document.getElementById('add-size-btn').addEventListener('click', function() {
            const sizeContainer = document.getElementById('size-container');
            const newSizeRow = document.createElement('div');
            newSizeRow.classList.add('row', 'mb-3', 'size-row');
            newSizeRow.innerHTML = `
        <div class="col-md-4">
            <label for="size" class="form-label">Size:</label>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="XXS">XXS</button>
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="XS">XS</button>
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="S">S</button>
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="M">M</button>
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="L">L</button>
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="XL">XL</button>
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="XXL">XXL</button>
                <button type="button" class="btn btn-outline-secondary size-btn" data-size="XXXL">XXXL</button>
                <input type="hidden" class="selected-size" name="sizes[${sizeIndex}][size]" required>
            </div>
        </div>
        <div class="col-md-4">
            <label for="stock" class="form-label">Stock:</label>
            <input type="number" class="form-control" name="sizes[${sizeIndex}][stock]" required>
        </div>
        <div class="col-md-4">
            <label for="price" class="form-label">Price:</label>
            <input type="text" class="form-control" name="sizes[${sizeIndex}][price]" required>
        </div>
        <div class="col-12 mt-2">
            <button type="button" class="btn btn-danger remove-size-btn">Remove</button>
        </div>
    `;
            sizeContainer.appendChild(newSizeRow);
            sizeIndex++;
        });

        // Remove size rows
        document.getElementById('size-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-size-btn')) {
                e.target.closest('.size-row').remove();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>