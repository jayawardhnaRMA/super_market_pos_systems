<?php
// reports.php: Generate and display sales reports
include '../includes/db.php';

// Fetch sales by branch
$querySalesByBranch = "SELECT b.BranchID, b.BranchName, SUM(s.TotalAmount) AS TotalSales 
                        FROM Sales s
                        JOIN Branch b ON s.BranchID = b.BranchID
                        GROUP BY b.BranchID, b.BranchName";
$stmtSalesByBranch = odbc_exec($conn, $querySalesByBranch);
if (!$stmtSalesByBranch) {
    die("Error fetching sales by branch: " . odbc_errormsg());
}

// Fetch sales by employee
$querySalesByEmployee = "SELECT e.EmployeeID, CONCAT(e.First_Name, ' ', e.Last_Name) AS EmployeeName, SUM(s.TotalAmount) AS TotalSales 
                         FROM Sales s
                         JOIN Employee e ON s.EmployeeID = e.EmployeeID
                         GROUP BY e.EmployeeID, e.First_Name, e.Last_Name";

$stmtSalesByEmployee = odbc_exec($conn, $querySalesByEmployee);
if (!$stmtSalesByEmployee) {
    die("Error fetching sales by employee: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="reports-container">
        <h1>Sales Reports</h1>

        <h2>Sales by Branch</h2>
        <table>
            <thead>
                <tr>
                    <th>Branch Name</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmtSalesByBranch)): ?>
                <tr>
                    <td><?= $row['BranchName'] ?></td>
                    <td><?= number_format($row['TotalSales'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Sales by Employee</h2>
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmtSalesByEmployee)): ?>
                <tr>
                    <td><?= $row['EmployeeName'] ?></td>
                    <td><?= number_format($row['TotalSales'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
