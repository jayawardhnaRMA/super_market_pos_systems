<?php
// Products.php: Manage product information
include '../includes/db.php';

// Fetch all suppliers for the dropdown
$querySuppliers = "SELECT SupplierID, Name FROM Supplier";
$stmtSuppliers = odbc_exec($conn, $querySuppliers);
if (!$stmtSuppliers) {
    die("Error fetching suppliers: " . odbc_errormsg());
}

// Fetch all categories for the dropdown
$queryCategories = "SELECT CategoryID, CategoryName FROM Category";
$stmtCategories = odbc_exec($conn, $queryCategories);
if (!$stmtCategories) {
    die("Error fetching categories: " . odbc_errormsg());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product_name'];
    $basePrice = $_POST['base_price'];
    $stock = $_POST['stock'];
    $supplierID = $_POST['supplier_id'];
    $categoryID = $_POST['category_id'];

    // Check if SupplierID exists
    $queryCheckSupplier = "SELECT COUNT(*) AS Count FROM Supplier WHERE SupplierID = ?";
    $stmtCheckSupplier = odbc_prepare($conn, $queryCheckSupplier);
    odbc_execute($stmtCheckSupplier, [$supplierID]);

    $row = odbc_fetch_array($stmtCheckSupplier);
    if ($row['Count'] == 0) {
        die("Error: SupplierID does not exist.");
    }

    // Generate the next ProductID
    $queryMaxID = "SELECT MAX(ProductID) AS MaxID FROM Product";
    $resultMaxID = odbc_exec($conn, $queryMaxID);

    if (!$resultMaxID) {
        die("Error fetching max ProductID: " . odbc_errormsg());
    }

    $row = odbc_fetch_array($resultMaxID);
    $nextProductID = $row['MaxID'] + 1; // Increment the max ID by 1

    // Insert the new product
    $query = "INSERT INTO Product (ProductID, ProductName, BasePrice, Stock, SupplierID, CategoryID) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = odbc_prepare($conn, $query);

    if (!odbc_execute($stmt, [$nextProductID, $productName, $basePrice, $stock, $supplierID, $categoryID])) {
        die("Error inserting product: " . odbc_errormsg());
    }
}

// Fetch all products
$query = "SELECT p.ProductID, p.ProductName, p.BasePrice, p.Stock, c.CategoryName, s.Name AS SupplierName
          FROM Product p
          LEFT JOIN Category c ON p.CategoryID = c.CategoryID
          LEFT JOIN Supplier s ON p.SupplierID = s.SupplierID";
$stmt = odbc_exec($conn, $query);
if (!$stmt) {
    die("Error fetching products: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="products-container">
        <h1>Manage Products</h1>
        <form method="POST" class="product-form">
            <input type="text" name="product_name" placeholder="Product Name" required>
            <input type="number" step="0.01" name="base_price" placeholder="Base Price" required>
            <input type="number" name="stock" placeholder="Stock" required>
            <select name="supplier_id" required>
                <option value="">Select Supplier</option>
                <?php while ($row = odbc_fetch_array($stmtSuppliers)): ?>
                <option value="<?= $row['SupplierID'] ?>"><?= $row['Name'] ?></option>
                <?php endwhile; ?>
            </select>
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php while ($row = odbc_fetch_array($stmtCategories)): ?>
                <option value="<?= $row['CategoryID'] ?>"><?= $row['CategoryName'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Add Product</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Base Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmt)): ?>
                <tr>
                    <td><?= $row['ProductID'] ?></td>
                    <td><?= $row['ProductName'] ?></td>
                    <td><?= $row['BasePrice'] ?></td>
                    <td><?= $row['Stock'] ?></td>
                    <td><?= $row['CategoryName'] ?></td>
                    <td><?= $row['SupplierName'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
