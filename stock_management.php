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
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <h1>Stock Management</h1>

    <!-- Display all items in a table -->
    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Total Stock</th>
            <th>Actions</th>
        </tr>
        <?php while ($item = $itemResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo $item['stock']; ?></td>
                <td>
                    <!-- Update button to show size-specific stock fields -->
                    <form method="post" action="stock_management.php" style="display:inline-block;">
                        <input type="hidden" name="item_id" value="<?php echo $item['product_id']; ?>">
                        <button type="submit" name="show_update_form">Update Stock</button>
                    </form>

                    <!-- Remove item button -->
                    <form method="post" action="stock_management.php" style="display:inline-block;">
                        <input type="hidden" name="item_id" value="<?php echo $item['product_id']; ?>">
                        <button type="submit" name="remove_item" onclick="return confirm('Are you sure you want to remove this item and all associated data?')">Remove Item</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

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

        echo "<h2>Update Stock for Item ID: $item_id</h2>";
        echo '<form method="post" action="stock_management.php">';
        echo '<input type="hidden" name="item_id" value="' . $item_id . '">';
        echo '<table border="1">';
        echo '<tr><th>Size</th><th>Stock</th><th>Actions</th></tr>';

        // Display size-specific stock inputs
        while ($size = $sizeResult->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($size['size']) . '</td>';
            echo '<td><input type="number" name="size_stock[' . $size['id'] . ']" value="' . $size['stock'] . '" min="0"></td>';
            echo '<td>';
            echo '<form method="post" action="stock_management.php" style="display:inline-block;">';
            echo '<input type="hidden" name="size_id" value="' . $size['id'] . '">';
            echo '<input type="hidden" name="item_id" value="' . $item_id . '">';
            echo '<button type="submit" name="remove_size" onclick="return confirm(\'Are you sure you want to remove this size?\')">Remove Size</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '<button type="submit" name="update_stock">Update Stock</button>';
        echo '</form>';

        // Form to add new size
        echo '<h3>Add New Size</h3>';
        echo '<form method="post" action="stock_management.php">';
        echo '<input type="hidden" name="item_id" value="' . $item_id . '">';
        echo '<label for="new_size">Size:</label>';
        echo '<input type="text" name="new_size" required>';
        echo '<label for="new_stock">Stock:</label>';
        echo '<input type="number" name="new_stock" required min="0">';
        echo '<label for="new_price">Price:</label>';
        echo '<input type="number" name="new_price" required min="0">';
        echo '<button type="submit" name="add_size">Add Size</button>';
        echo '</form>';
    }
    ?>
</body>
</html>
