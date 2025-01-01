<?php
include '../includes/db.php';

// Fetch deleted inventory logs
$queryDeletedLogs = "SELECT InventoryID, StockLevel, ReorderThreshold, Last_Updated, DeletedAt FROM DeletedInventory";
$stmtDeletedLogs = odbc_exec($conn, $queryDeletedLogs);

// Fetch updated inventory logs
$queryUpdatedLogs = "SELECT InventoryID, Action, OldStockLevel, NewStockLevel, OldReorderThreshold, NewReorderThreshold, Timestamp 
                     FROM Logs";
$stmtUpdatedLogs = odbc_exec($conn, $queryUpdatedLogs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="logs-container">
        <h1>Inventory Logs</h1>

        <h2>Deleted Inventory</h2>
        <table>
            <thead>
                <tr>
                    <th>Inventory ID</th>
                    <th>Stock Level</th>
                    <th>Reorder Threshold</th>
                    <th>Last Updated</th>
                    <th>Deleted At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmtDeletedLogs)): ?>
                <tr>
                    <td><?= $row['InventoryID'] ?></td>
                    <td><?= $row['StockLevel'] ?></td>
                    <td><?= $row['ReorderThreshold'] ?></td>
                    <td><?= $row['Last_Updated'] ?></td>
                    <td><?= $row['DeletedAt'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Updated Inventory</h2>
        <table>
            <thead>
                <tr>
                    <th>Inventory ID</th>
                    <th>Action</th>
                    <th>Old Stock Level</th>
                    <th>New Stock Level</th>
                    <th>Old Reorder Threshold</th>
                    <th>New Reorder Threshold</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmtUpdatedLogs)): ?>
                <tr>
                    <td><?= $row['InventoryID'] ?></td>
                    <td><?= $row['Action'] ?></td>
                    <td><?= $row['OldStockLevel'] ?></td>
                    <td><?= $row['NewStockLevel'] ?></td>
                    <td><?= $row['OldReorderThreshold'] ?></td>
                    <td><?= $row['NewReorderThreshold'] ?></td>
                    <td><?= $row['Timestamp'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
