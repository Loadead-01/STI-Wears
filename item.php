<?php
include 'function/session_func.php'; // Ensure session management is handled
require_once('connect.php');

// Assume logged-in user's account id is stored in session
$account_id = $_SESSION['account_id'];
$student_id = $_SESSION['student_id']; // Assuming you have student_id in the session
$ordered_by = $_SESSION['user']; // Assuming you store the user's name in the session
$section = $_SESSION['section']; // Assuming you have section info in the session

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {
    // Query to get item details based on item_id from admin_account (conn2)
    $sql = "SELECT i.product_name, d.size, d.stock, d.price, im.image_path
            FROM admin_account.item i
            JOIN admin_account.item_details d ON i.product_id = d.item_id
            JOIN admin_account.item_image im ON i.product_id = im.item_id
            WHERE i.product_id = ?";

    $stmt = $conn2->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $sizes = [];
        $image_path = ''; // Initialize image path variable
        $item_name = ''; // Initialize item name variable

        while ($row = $result->fetch_assoc()) {
            $item_name = $row['product_name']; // Set product name
            $sizes[] = $row; // Add size info to the array
            if (empty($image_path)) {
                // Set the first image as the display image
                $image_path = $row['image_path'];
            }
        }
    } else {
        echo "No item details found.";
        exit;
    }
} else {
    echo "Invalid item ID.";
    exit;
}


// Handle form submission for placing order or adding to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $size = $_POST['size'];
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $total_price = $quantity * $price;
    

    if (isset($_POST['action']) && $_POST['action'] == 'add_to_cart') {
        // Add to cart functionality using user_account (conn)
        $sql_cart = "INSERT INTO `user_account`.`cart` (account_id, item_id, size, quantity, price) VALUES (?, ?, ?, ?, ?)";
        $stmt_cart = $conn->prepare($sql_cart);
        if (!$stmt_cart) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt_cart->bind_param('iisis', $account_id, $item_id, $size, $quantity, $price);
        $stmt_cart->execute();
        if ($stmt_cart->error) {
            die('Execute failed: ' . $stmt_cart->error);
        }
        header("Location: cart.php"); // Redirect to cart page
        exit;
    } else {
        // Place order functionality using user_account (conn)
        $sql_order = "INSERT INTO `user_account`.`order` (account_id, student_id, ordered_by, section, total_price, status) 
                      VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt_order = $conn->prepare($sql_order);
        if (!$stmt_order) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt_order->bind_param('iissd', $account_id, $student_id, $ordered_by, $section, $total_price);
        $stmt_order->execute();
        if ($stmt_order->error) {
            die('Execute failed: ' . $stmt_order->error);
        }
        $order_id = $stmt_order->insert_id;

        // Insert into order_detail table in user_account (conn)
        $sql_detail = "INSERT INTO `user_account`.`order_detail` (order_id, size, quantity, price, product_name) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        if (!$stmt_detail) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt_detail->bind_param('isids', $order_id, $size, $quantity, $price, $item_name);
        $stmt_detail->execute();
        if ($stmt_detail->error) {
            die('Execute failed: ' . $stmt_detail->error);
        }

        header("Location: order.php?order_id=$order_id");
        exit;
    } 
} 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .size-btn.selected {
            background-color: #007bff;
            /* Blue color */
            color: white;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container-xl mt-4">
        <div class="row">
            <div class="col-6">
                <!-- Display the item image -->
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Product Image" class="img-fluid">
            </div>
            <div class="col-6">
                <h1><?php echo htmlspecialchars($item_name); ?></h1>

                <!-- Form for ordering or adding to cart -->
                <form id="order-form" method="POST">
                    <input type="hidden" name="price" id="price-input">
                    <input type="hidden" name="size" id="size-input" required> <!-- Hidden input for size -->
                    <input type="hidden" name="action" id="action-input"> <!-- Hidden input for action -->
                    <hr>
                    <div class="mb-3">
                        <h5>Select Size:</h5>
                        <?php foreach ($sizes as $size): ?>
                            <button type="button" class="btn btn-outline-primary m-1 size-btn"
                                data-size="<?php echo htmlspecialchars($size['size']); ?>"
                                data-price="<?php echo htmlspecialchars($size['price']); ?>"
                                data-stock="<?php echo htmlspecialchars($size['stock']); ?>">
                                <?php echo strtoupper(htmlspecialchars($size['size'])); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-3">
                        <p><strong>Price:</strong> PHP <span id="price-display">0.00</span></p>
                        <p><strong>Stock Available:</strong> <span id="stock-display">0</span></p>
                    </div>

                    <div class="mb-3">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" required>
                    </div>

                    <button type="button" class="btn btn-primary" id="add-to-cart">Add to Cart</button>
                    <button type="submit" class="btn btn-success" id="place-order">Order Now</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Update price and stock when size buttons are clicked
        const sizeButtons = document.querySelectorAll('.size-btn');
        const priceDisplay = document.getElementById('price-display');
        const stockDisplay = document.getElementById('stock-display');
        const priceInput = document.getElementById('price-input');
        const sizeInput = document.getElementById('size-input');
        const actionInput = document.getElementById('action-input');

        sizeButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Remove 'selected' class from all buttons
                sizeButtons.forEach(btn => btn.classList.remove('selected'));

                // Add 'selected' class to the clicked button
                this.classList.add('selected');

                const size = this.getAttribute('data-size');
                const price = this.getAttribute('data-price');
                const stock = this.getAttribute('data-stock');

                priceDisplay.textContent = `${price}`;
                stockDisplay.textContent = stock;
                priceInput.value = price;
                sizeInput.value = size; // Set the selected size in hidden input
            });
        });

        document.getElementById('add-to-cart').addEventListener('click', function () {
            actionInput.value = 'add_to_cart';
            document.getElementById('order-form').submit();
        });
    </script>

</body>

</html>
