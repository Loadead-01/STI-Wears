<?php
// Include session and database connection
include 'function/admin_session_func.php';
require_once 'connect.php';

// Function to calculate total stock
function calculateTotalStock($item_id, $conn2) {
    $totalStockQuery = "SELECT SUM(stock) AS total_stock FROM item_details WHERE item_id = ?";
    $stmt = $conn2->prepare($totalStockQuery);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_stock'] ?? 0;
}

// Function to update item stock
function updateItemStock($item_id, $conn2) {
    $totalStock = calculateTotalStock($item_id, $conn2);
    $updateStockQuery = "UPDATE item SET stock = ? WHERE product_id = ?";
    $stmt = $conn2->prepare($updateStockQuery);
    $stmt->bind_param('ii', $totalStock, $item_id);
    $stmt->execute();
}

// Function to delete item, item details, and item images
function removeItem($item_id, $conn2) {
    // Delete from item_details
    $deleteDetailsQuery = "DELETE FROM item_details WHERE item_id = ?";
    $stmt = $conn2->prepare($deleteDetailsQuery);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();

    // Delete from item_image
    $deleteImageQuery = "DELETE FROM item_image WHERE item_id = ?";
    $stmt = $conn2->prepare($deleteImageQuery);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();

    // Delete from item
    $deleteItemQuery = "DELETE FROM item WHERE product_id = ?";
    $stmt = $conn2->prepare($deleteItemQuery);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
}

// Function to delete specific size
function removeSize($size_id, $conn2) {
    $deleteSizeQuery = "DELETE FROM item_details WHERE id = ?";
    $stmt = $conn2->prepare($deleteSizeQuery);
    $stmt->bind_param('i', $size_id);
    $stmt->execute();
    $getItemIdQuery = "SELECT item_id FROM item_details WHERE id = ?";
    $stmt = $conn2->prepare($getItemIdQuery);
    $stmt->bind_param('i', $size_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $item_id = $row['item_id'];

    // Update the overall stock of the item
    updateItemStock($item_id, $conn2);

    echo "<p>Size ID $size_id has been removed successfully!</p>";
}

// Check if form is submitted to update stock
if (isset($_POST['update_stock'])) {
    $item_id = $_POST['item_id'];

    // Loop through each size and update its stock
    foreach ($_POST['size_stock'] as $id => $stock) {
        $updateSizeStockQuery = "UPDATE item_details SET stock = ? WHERE id = ?";
        $stmt = $conn2->prepare($updateSizeStockQuery);
        $stmt->bind_param('ii', $stock, $id);
        $stmt->execute();
    }

    // Update total stock in item table
    updateItemStock($item_id, $conn2);

    echo "<p>Stock for item ID $item_id updated successfully!</p>";
}

// Check if form is submitted to remove an item
if (isset($_POST['remove_item'])) {
    $item_id = $_POST['item_id'];

    // Remove the item and its associated details and images
    removeItem($item_id, $conn2);

    echo "<p>Item ID $item_id and its associated data have been removed successfully!</p>";
}

// Check if form is submitted to remove a specific size
if (isset($_POST['remove_size'])) {
    $size_id = $_POST['size_id'];

    // Remove the specific size
    removeSize($size_id, $conn2);

    echo "<p>Size ID $size_id has been removed successfully!</p>";
}

// Check if form is submitted to add a new size
if (isset($_POST['add_size'])) {
    $item_id = $_POST['item_id'];
    $new_size = $_POST['new_size'];
    $new_stock = $_POST['new_stock'];
    $new_price = $_POST['new_price'];

    // Insert the new size into item_details
    $addSizeQuery = "INSERT INTO item_details (item_id, size, stock, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn2->prepare($addSizeQuery);
    $stmt->bind_param('isii', $item_id, $new_size, $new_stock, $new_price);
    $stmt->execute();

    // Update total stock in item table
    updateItemStock($item_id, $conn2);

    echo "<p>New size added successfully!</p>";
}

// Fetch all items
$itemQuery = "SELECT product_id, product_name, stock FROM item";
$itemResult = $conn2->query($itemQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
</head>
<body >
    <?php include 'admin_header.php'; ?>
    <div class="container-lg d-flex justify-content-center border"></div>
    <div class="row">
    <h1>Stock Management</h1>

    <div class="table-responsive col-11">
    <table class="table table-bordered table-striped shadow-sm table-white table-hover border"">
        <tr>
            <th>Product Name</th>
            <th>Total Stock</th>
            <th>Actions</th>
        </tr>
        <?php while ($item = $itemResult->fetch_assoc()) { ?>
            <tr>
                <td style="vertical-align: middle;"><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td style="vertical-align: middle;"><?php echo $item['stock']; ?></td>
                <td style="vertical-align: middle;">
                    <!-- Update button to show size-specific stock fields -->
                    <form method="post" action="stock_management.php" style="display:inline-block;">
                        <input type="hidden" name="item_id" value="<?php echo $item['product_id']; ?>">
                        <button type="submit" name="show_update_form" class="btn btn-success">Update Stock</button>
                    </form>

                    <!-- Remove item button -->
                    <form method="post" action="stock_management.php" style="display:inline-block;">
                        <input type="hidden" name="item_id" value="<?php echo $item['product_id']; ?>">
                        <button type="submit" name="remove_item"  class="btn btn-danger fs-6" onclick="return confirm('Are you sure you want to remove this item and all associated data?')">Remove Item</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    </div>

    <?php
    // Show size-specific stock update form if "Update Stock" button is clicked
    if (isset($_POST['show_update_form']) && isset($_POST['item_id'])) {
        $item_id = $_POST['item_id'];

        // Fetch the item's size-specific stock from item_details
        $sizeQuery = "SELECT id, size, stock FROM item_details WHERE item_id = ?";
        $stmt = $conn2->prepare($sizeQuery);
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
        $sizeResult = $stmt->get_result();
        ?>

        <h2>Update Stock for Item ID: <?php echo htmlspecialchars($item_id); ?></h2>
        <form method="post" action="stock_management.php">
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
            <table border="1">
                <tr><th>Size</th><th>Stock</th><th>Actions</th></tr>
                <?php while ($size = $sizeResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($size['size']); ?></td>
                        <td><input type="number" name="size_stock[<?php echo $size['id']; ?>]" value="<?php echo htmlspecialchars($size['stock']); ?>" min="0"></td>
                        <td>
                            <!-- Remove size button -->
                            <form method="post" action="stock_management.php" style="display:inline-block;">
                                <input type="hidden" name="size_id" value="<?php echo $size['id']; ?>">
                                <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                <button type="submit" name="remove_size" onclick="return confirm('Are you sure you want to remove this size?')">Remove Size</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <button type="submit" name="update_stock">Update Stock</button>
        </form>

        <!-- Form to add new size -->
        <h3>Add New Size</h3>
        <form method="post" action="stock_management.php">
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
            <label for="new_size">Size:</label>
            <input type="text" name="new_size" required>
            <label for="new_stock">Stock:</label>
            <input type="number" name="new_stock" required min="0">
            <label for="new_price">Price:</label>
            <input type="number" name="new_price" required min="0">
            <button type="submit" name="add_size">Add Size</button>
        </form>
        
    <?php } ?>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
