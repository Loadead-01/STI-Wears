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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .size-btn.selected {
            background-color: #007bff;
            color: white;
        }

        .color-blue {
            background-color: #ffcc14 !important;
            color: #444 !important;
            font-weight: bold !important;
        }
    </style>
</head>

<body class="bg-light">
    <?php include 'header.php'; ?>
    <div id="cartNotif" class="message-container position-fixed bottom-0 end-0 p-3 rounded-2" style="display: none;">
        <div class="bg-white border shadow-sm rounded-3">
            <div class="toast-header border-bottom p-2 color-blue rounded-top-3">
                <strong class="me-auto"><i class="bi bi-bell-fill"></i>Notification</strong>
                
            </div>

            <div id="message" class="toast-body p-2">
                Item added to cart successfully
            </div>
        </div>
    </div>
    <div id="quantityNotif" class="message-container position-fixed bottom-0 end-0 p-3 rounded-2" style="display: none;">
        <div class="bg-white border shadow-sm rounded-3">
            <div class="toast-header border-bottom p-2 color-blue rounded-top-3">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i>Notification</strong>

                
            </div>

            <div id="message" class="toast-body p-2">
                order quantity must not be greater than 5
            </div>
        </div>
    </div>
    <div id="sizeNotif" class="message-container position-fixed bottom-0 end-0 p-3 rounded-2" style="display: none;">
        <div class="bg-white border shadow-sm rounded-3">
            <div class="toast-header border-bottom p-2 color-blue rounded-top-3">
            <strong class="me-auto"><i class="bi bi-bell-fill"></i>Notification</strong>

                
            </div>

            <div id="message" class="toast-body p-2">
                Please make sure to select an item size
            </div>
        </div>
    </div>

    <div class="p-3">
        <div class="container-lg mt-4 p-3 bg-white border shadow-sm">
            <div class="row">
                <div class="col-sm-6 d-flex align-items-center">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Product Image" class="img-fluid border shadow-sm h-100 w-100">
                </div>
                <div class="col-sm-6 m-sm-0 mt-4">
                    <h2 class="text-dark"><?php echo $item_name; ?></h2>
                    <form id="order-form" method="POST">
                        <input type="hidden" name="price" id="price-input">
                        <input type="hidden" name="size" id="size-input" required>
                        <input type="hidden" name="action" id="action-input">
                        <hr>

                        <div class="mb-3 lh-1 m-0">
                            <p class="text-secondary"><strong>Select Size : </strong></p>
                            <?php foreach ($sizes as $size): ?>
                                <?php if ($size['stock'] > 0) { ?>
                                    <button type="button" class="btn btn-outline-primary m-1 size-btn"
                                        data-size="<?php echo htmlspecialchars($size['size']); ?>"
                                        data-price="<?php echo htmlspecialchars($size['price']); ?>"
                                        data-stock="<?php echo htmlspecialchars($size['stock']); ?>">
                                        <?php echo strtoupper(htmlspecialchars($size['size'])); ?>
                                    </button>
                                <?php } ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <p class="lh-1 m-0 text-secondary"><strong>Price :</strong> PHP <span id="price-display">0.00</span></p>
                            <p class="lh-1 m-0 text-secondary"><strong>Stock Available :</strong> <span id="stock-display">0</span></p>
                        </div>
                        <hr>


                        <P class="text-secondary"><strong>Product Description </strong></P>

                        <p aria-disabled="true">
                            <span class="placeholder col-6" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-1" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-3" style="cursor: default !important;"></span>
                        </p>
                        <p aria-disabled="true">
                            <span class="placeholder col-3" style="cursor: default !important;"></span>
                            <span class="placeholder col-4" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-3" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-5" style="cursor: default !important;"></span>
                        </p>
                        <hr>

                        <div class="mb-3">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" max="5" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="add-to-cart">Add to Cart</button>
                            <button type="button" class="btn btn-success" id="place-order">Order Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const sizeButtons = document.querySelectorAll('.size-btn');
        const priceDisplay = document.getElementById('price-display');
        const stockDisplay = document.getElementById('stock-display');
        const priceInput = document.getElementById('price-input');
        const sizeInput = document.getElementById('size-input');

        sizeButtons.forEach(button => {
            button.addEventListener('click', function() {
                sizeButtons.forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');

                const size = this.getAttribute('data-size');
                const price = this.getAttribute('data-price');
                const stock = this.getAttribute('data-stock');

                priceDisplay.textContent = `${price}`;
                stockDisplay.textContent = stock;
                priceInput.value = price;
                sizeInput.value = size;
            });
        });

        // add to cart
        $('#add-to-cart').on('click', function() {
            const size = sizeInput.value;
            const price = priceInput.value;
            const quantity = $('#quantity').val();
            const cartNotif = $("#cartNotif")
            const quantityNotif = $('#quantityNotif');
            const sizeNotif = $('#sizeNotif');



            // Validation
            if (!size || quantity <= 0) {
                sizeNotif
                    .show();
                hideMessageAfterDelay(sizeNotif);
                return;
            }

            if (quantity > 5) {
                quantityNotif
                    .show();
                hideMessageAfterDelay(quantityNotif);
                return;
            }

            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: {
                    action: 'add_to_cart',
                    item_id: '<?php echo $item_id; ?>',
                    account_id: '<?php echo $account_id; ?>',
                    size: size,
                    quantity: quantity,
                    price: price
                },
                success: function(response) {
                    cartNotif
                        //.find("#message").html('Item successfully added to cart!')
                        .show();
                    hideMessageAfterDelay(cartNotif);

                },
                error: function(xhr, status, error) {
                    messageContainer
                        .removeClass()
                        .addClass('alert alert-danger')
                        .html('Failed to add item to cart: ' + error)
                        .show();
                    hideMessageAfterDelay(messageContainer);
                }
            });
        });

        // if buy now click
        $('#place-order').on('click', function() {
            const size = sizeInput.value;
            const price = priceInput.value;
            const quantity = $('#quantity').val();
            const cartNotif = $("#cartNotif")
            const quantityNotif = $('#quantityNotif');
            const sizeNotif = $('#sizeNotif');


            if (!size || quantity <= 0) {
                sizeNotif
                    .show();
                hideMessageAfterDelay(sizeNotif);
                return;
            }

            if (quantity > 5) {
                quantityNotif
                    .show();
                hideMessageAfterDelay(quantityNotif);
                return;
            }

            $.ajax({
                url: 'checkout_data.php',
                method: 'POST',
                data: {
                    item_id: '<?php echo $item_id; ?>',
                    size: size,
                    quantity: quantity,
                    price: price
                },
                success: function(response) {
                    messageContainer
                        .removeClass()
                        .addClass('alert alert-danger')
                        .html('test ' + size + quantity)
                        .show
                    hideMessageAfterDelay(messageContainer);
                    //window.location.href = 'checkout.php';
                },
                error: function(xhr, status, error) {
                    messageContainer
                        .removeClass()
                        .addClass('alert alert-danger')
                        .html('Failed to set checkout data: ' + error)
                        .show();
                    hideMessageAfterDelay(messageContainer);
                }
            });

            // go to order details
            window.location.href = `checkout.php?item_id=<?php echo $item_id; ?>&size=${size}&quantity=${quantity}&price=${price}`;
        });

        function hideMessageAfterDelay(container) {
            setTimeout(() => {
                container.fadeOut();
            }, 3000);
        }
    </script>
</body>

</html>