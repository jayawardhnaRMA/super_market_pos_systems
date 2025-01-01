<?php
// Employees.php: Manage employee information
include '../includes/db.php';

// Fetch all branches for the dropdown
$queryBranches = "SELECT BranchID, BranchName FROM Branch";
$stmtBranches = odbc_exec($conn, $queryBranches);
if (!$stmtBranches) {
    die("Error fetching branches: " . odbc_errormsg());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $role = $_POST['role'];
    $branchID = $_POST['branch_id'];

    // Generate the next EmployeeID
    $queryMaxID = "SELECT MAX(EmployeeID) AS MaxID FROM Employee";
    $resultMaxID = odbc_exec($conn, $queryMaxID);

    if (!$resultMaxID) {
        die("Error fetching max EmployeeID: " . odbc_errormsg());
    }

    $row = odbc_fetch_array($resultMaxID);
    $nextEmployeeID = $row['MaxID'] + 1; // Increment the max ID by 1

    // Insert the new employee
    $query = "INSERT INTO Employee (EmployeeID, First_Name, Last_Name, Role, BranchID) VALUES (?, ?, ?, ?, ?)";
    $stmt = odbc_prepare($conn, $query);

    if (!odbc_execute($stmt, [$nextEmployeeID, $firstName, $lastName, $role, $branchID])) {
        die("Error inserting employee: " . odbc_errormsg());
    }
}

// Fetch all employees
$query = "SELECT e.EmployeeID, e.First_Name, e.Last_Name, e.Role, b.BranchName 
          FROM Employee e
          LEFT JOIN Branch b ON e.BranchID = b.BranchID";
$stmt = odbc_exec($conn, $query);
if (!$stmt) {
    die("Error fetching employees: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="employees-container">
        <h1>Manage Employees</h1>
        <form method="POST" class="employee-form">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="role" placeholder="Role" required>
            <select name="branch_id" required>
                <option value="">Select Branch</option>
                <?php while ($row = odbc_fetch_array($stmtBranches)): ?>
                <option value="<?= $row['BranchID'] ?>"><?= $row['BranchName'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Add Employee</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Branch</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmt)): ?>
                <tr>
                    <td><?= $row['EmployeeID'] ?></td>
                    <td><?= $row['First_Name'] ?></td>
                    <td><?= $row['Last_Name'] ?></td>
                    <td><?= $row['Role'] ?></td>
                    <td><?= $row['BranchName'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
