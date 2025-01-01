<?php
// Sales.php: Manage sales records
include '../includes/db.php';

// Function to calculate total sale amount
function CalculateTotalSaleAmount($unitPrice, $quantity) {
    return $unitPrice * $quantity;
}

// Function to calculate discounted price
function CalculateDiscountedPrice($totalAmount, $discountPercentage) {
    return $totalAmount - ($totalAmount * ($discountPercentage / 100));
}

// Fetch branches for dropdown
$queryBranches = "SELECT BranchID, BranchName FROM Branch";
$stmtBranches = odbc_exec($conn, $queryBranches);
if (!$stmtBranches) {
    die("Error fetching branches: " . odbc_errormsg());
}

// Fetch employees for dropdown
$queryEmployees = "SELECT EmployeeID, First_Name, Last_Name FROM Employee";
$stmtEmployees = odbc_exec($conn, $queryEmployees);
if (!$stmtEmployees) {
    die("Error fetching employees: " . odbc_errormsg());
}

// Handle form submission for adding sales
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_sale'])) {
    $unitPrice = $_POST['unit_price'];
    $quantity = $_POST['quantity'];
    $discountPercentage = $_POST['discount_percentage'];
    $branchID = $_POST['branch_id'];
    $employeeID = $_POST['employee_id'];

    // Calculate total sale amount and discounted price
    $totalAmount = CalculateTotalSaleAmount($unitPrice, $quantity);
    $discountedPrice = CalculateDiscountedPrice($totalAmount, $discountPercentage);

    // Insert sale record
    $querySale = "INSERT INTO Sales (UnitPrice, Quantity, DiscountPercentage, TotalAmount, DiscountedPrice, BranchID, EmployeeID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtSale = odbc_prepare($conn, $querySale);
    odbc_execute($stmtSale, [$unitPrice, $quantity, $discountPercentage, $totalAmount, $discountedPrice, $branchID, $employeeID]);
}

// Fetch all sales
$querySales = "SELECT s.SaleID, s.UnitPrice, s.Quantity, s.DiscountPercentage, s.TotalAmount, s.DiscountedPrice, b.BranchName, e.First_Name, e.Last_Name 
               FROM Sales s
               LEFT JOIN Branch b ON s.BranchID = b.BranchID
               LEFT JOIN Employee e ON s.EmployeeID = e.EmployeeID";
$stmtSales = odbc_exec($conn, $querySales);
if (!$stmtSales) {
    die("Error fetching sales: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="sales-container">
        <h1>Manage Sales</h1>
        <form method="POST" class="sales-form">
            <h2>Add Sale</h2>
            <input type="hidden" name="add_sale" value="1">
            <input type="number" step="0.01" name="unit_price" placeholder="Unit Price" required>
            <input type="number" name="quantity" placeholder="Quantity" required>
            <input type="number" step="0.01" name="discount_percentage" placeholder="Discount (%)" required>
            <select name="branch_id" required>
                <option value="">Select Branch</option>
                <?php while ($row = odbc_fetch_array($stmtBranches)): ?>
                <option value="<?= $row['BranchID'] ?>"><?= $row['BranchName'] ?></option>
                <?php endwhile; ?>
            </select>
            <select name="employee_id" required>
                <option value="">Select Employee</option>
                <?php while ($row = odbc_fetch_array($stmtEmployees)): ?>
                <option value="<?= $row['EmployeeID'] ?>"><?= $row['First_Name'] . ' ' . $row['Last_Name'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Add Sale</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Discount (%)</th>
                    <th>Total Amount</th>
                    <th>Discounted Price</th>
                    <th>Branch</th>
                    <th>Employee</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmtSales)): ?>
                <tr>
                    <td><?= $row['SaleID'] ?></td>
                    <td><?= $row['UnitPrice'] ?></td>
                    <td><?= $row['Quantity'] ?></td>
                    <td><?= $row['DiscountPercentage'] ?></td>
                    <td><?= $row['TotalAmount'] ?></td>
                    <td><?= $row['DiscountedPrice'] ?></td>
                    <td><?= $row['BranchName'] ?></td>
                    <td><?= $row['First_Name'] . ' ' . $row['Last_Name'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
