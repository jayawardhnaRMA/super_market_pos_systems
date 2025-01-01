<?php
include '../includes/db.php'; // Include the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>POS Management System</h1>
        <nav>
            <ul>
                <li><a href="branches.php">Branches</a></li>
                <li><a href="customers.php">Customers</a></li>
                <li><a href="employees.php">Employees</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="sales.php">Sales</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="discounts.php">Discounts</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="logs.php">Logs</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
