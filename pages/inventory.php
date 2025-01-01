<?php
// Inventory.php: Manage inventory information
include '../includes/db.php';

// Handle add operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_inventory'])) {
    $stockLevel = $_POST['stock_level'];
    $reorderThreshold = $_POST['reorder_threshold'];

    // Generate the next InventoryID if not auto-incremented
    $queryMaxID = "SELECT MAX(InventoryID) AS MaxID FROM Inventory";
    $resultMaxID = odbc_exec($conn, $queryMaxID);

    if (!$resultMaxID) {
        die("Error fetching max InventoryID: " . odbc_errormsg());
    }

    $row = odbc_fetch_array($resultMaxID);
    $nextInventoryID = $row['MaxID'] + 1;

    // Add the new inventory item
    $queryAdd = "INSERT INTO Inventory (InventoryID, StockLevel, ReorderThreshold, Last_Updated) VALUES (?, ?, ?, GETDATE())";
    $stmtAdd = odbc_prepare($conn, $queryAdd);
    if (!odbc_execute($stmtAdd, [$nextInventoryID, $stockLevel, $reorderThreshold])) {
        die("Error adding inventory item: " . odbc_errormsg());
    }
}

// Handle delete operation
if (isset($_GET['deletedID'])) {
    $deleteID = $_GET['deletedID'];

    // Ensure $deleteID is properly defined
    if (empty($deleteID)) {
        die("Error: Invalid Inventory ID.");
    }

    // Log the deleted inventory item
   // Log the deleted inventory item
$queryLogDeleted = "INSERT INTO DeletedInventory (InventoryID, StockLevel, ReorderThreshold, Last_Updated, DeletedAt) 
SELECT InventoryID, StockLevel, ReorderThreshold, Last_Updated, GETDATE() 
FROM Inventory 
WHERE InventoryID = ?";
$stmtLogDeleted = odbc_prepare($conn, $queryLogDeleted);
$resultLogDeleted = odbc_execute($stmtLogDeleted, [$deleteID]);
if (!$resultLogDeleted) {
echo "Query Executed: $queryLogDeleted<br>";
echo "ODBC Error: " . odbc_errormsg();
die("Error logging deleted inventory item.");
}


    // Delete the inventory item
    $queryDelete = "DELETE FROM Inventory WHERE InventoryID = ?";
    $stmtDelete = odbc_prepare($conn, $queryDelete);
    $resultDelete = odbc_execute($stmtDelete, [$deleteID]);
    if (!$resultDelete) {
        echo "Query Executed: $queryDelete<br>";
        echo "ODBC Error: " . odbc_errormsg();
        die("Error deleting inventory item.");
    }
}

// Handle update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $updateID = $_POST['update_id'];
    $newStockLevel = $_POST['stock_level'];
    $newReorderThreshold = $_POST['reorder_threshold'];

    // Fetch old inventory data
    $queryFetchOld = "SELECT StockLevel, ReorderThreshold FROM Inventory WHERE InventoryID = ?";
    $stmtFetchOld = odbc_prepare($conn, $queryFetchOld);
    odbc_execute($stmtFetchOld, [$updateID]);
    $oldData = odbc_fetch_array($stmtFetchOld);

    // Log the update action
    $queryLogUpdate = "INSERT INTO Logs (InventoryID, Action, OldStockLevel, NewStockLevel, OldReorderThreshold, NewReorderThreshold, Timestamp)
                       VALUES (?, 'Update', ?, ?, ?, ?, GETDATE())";
    $stmtLogUpdate = odbc_prepare($conn, $queryLogUpdate);
    odbc_execute($stmtLogUpdate, [$updateID, $oldData['StockLevel'], $newStockLevel, $oldData['ReorderThreshold'], $newReorderThreshold]);

    // Update the inventory record
    $queryUpdate = "UPDATE Inventory SET StockLevel = ?, ReorderThreshold = ?, Last_Updated = GETDATE() WHERE InventoryID = ?";
    $stmtUpdate = odbc_prepare($conn, $queryUpdate);
    if (!odbc_execute($stmtUpdate, [$newStockLevel, $newReorderThreshold, $updateID])) {
        die("Error updating inventory item: " . odbc_errormsg());
    }
}

// Fetch all inventory records
$query = "SELECT InventoryID, StockLevel, ReorderThreshold, Last_Updated FROM Inventory";
$stmt = odbc_exec($conn, $query);

if (!$stmt) {
    die("Error fetching inventory records: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="inventory-container">
        <h1>Manage Inventory</h1>
        <form method="POST" class="inventory-form">
            <h2>Add Inventory</h2>
            <input type="hidden" name="add_inventory" value="1">
            <input type="number" name="stock_level" placeholder="Stock Level" required>
            <input type="number" name="reorder_threshold" placeholder="Reorder Threshold" required>
            <button type="submit">Add Inventory</button>
        </form>

        <form method="POST" class="inventory-form">
            <h2>Update Inventory</h2>
            <input type="number" name="update_id" placeholder="Inventory ID (for Update)" required>
            <input type="number" name="stock_level" placeholder="Stock Level" required>
            <input type="number" name="reorder_threshold" placeholder="Reorder Threshold" required>
            <button type="submit">Update Inventory</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Stock Level</th>
                    <th>Reorder Threshold</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmt)): ?>
                <tr>
                    <td><?= $row['InventoryID'] ?></td>
                    <td><?= $row['StockLevel'] ?></td>
                    <td><?= $row['ReorderThreshold'] ?></td>
                    <td><?= $row['Last_Updated'] ?></td>
                    <td>
                        <a href="?deletedID=<?= $row['InventoryID'] ?>" onclick="return confirm('Are you sure you want to delete this inventory item?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
