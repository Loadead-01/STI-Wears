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
<body>
    <?php include 'admin_header.php'; ?>

    <div class="container my-5">
        <div class="text-center mb-4">

        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Total Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = $itemResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['stock']; ?></td>
                            <td>
                                <form method="post" action="stock_management.php" class="d-inline">
                                    <input type="hidden" name="item_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" name="show_update_form" class="btn btn-success btn-sm">Update Stock</button>
                                </form>
                                <form method="post" action="stock_management.php" class="d-inline">
                                    <input type="hidden" name="item_id" value="<?php echo $item['product_id']; ?>">
                                    <button type="submit" name="remove_item" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item and all associated data?')">Remove Item</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php
        if (isset($_POST['show_update_form']) && isset($_POST['item_id'])) {
            $item_id = $_POST['item_id'];
            $sizeQuery = "SELECT id, size, stock FROM item_details WHERE item_id = ?";
            $stmt = $conn2->prepare($sizeQuery);
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $sizeResult = $stmt->get_result();
            ?>
            <div class="mt-5">
                <h5 class="text-start">Update Stock for Item ID: <?php echo htmlspecialchars($item_id); ?></h5>
                <form method="post" action="stock_management.php" class="my-3">
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
                    <table class="table table-bordered table-hover shadow-sm">
                        <thead class="table-dark">
                            <tr><th>Size</th><th>Stock</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($size = $sizeResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($size['size']); ?></td>
                                    <td><input type="number" class="form-control" name="size_stock[<?php echo $size['id']; ?>]" value="<?php echo htmlspecialchars($size['stock']); ?>" min="0"></td>
                                    <td>
                                        <form method="post" action="stock_management.php" class="d-inline">
                                            <input type="hidden" name="size_id" value="<?php echo $size['id']; ?>">
                                            <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                            <button type="submit" name="remove_size" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this size?')">Remove Size</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="text-end">
                        <button type="submit" name="update_stock" class="btn btn-primary">Update Stock</button>
                    </div>
                </form>

                <div class="mt-4">
                    <h5 class="text-start">Add New Size </h5>
                    <form method="post" action="stock_management.php" class="text-center">
                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
                        <div class="row g-3 justify-content-center">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="new_size" placeholder="Size" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="new_stock" placeholder="Stock" required min="0">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="new_price" placeholder="Price" required min="0">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="add_size" class="btn btn-success">Add Size</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
