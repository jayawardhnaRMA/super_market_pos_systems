<?php
// Branches.php: Manage branch information
include '../includes/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branchID = $_POST['branch_id']; // Add a field for BranchID in the form
    $branchName = $_POST['branch_name'];
    $location = $_POST['location'];
    $managerName = $_POST['manager_name'];

    $query = "INSERT INTO Branch (BranchID, BranchName, Location, ManagerName) VALUES (?, ?, ?, ?)";
    $stmt = odbc_prepare($conn, $query);

    if (!odbc_execute($stmt, [$branchID, $branchName, $location, $managerName])) {
        die("Error inserting branch: " . odbc_errormsg());
    }
}


// Fetch all branches
$query = "SELECT * FROM Branch";
$stmt = odbc_exec($conn, $query);
if (!$stmt) {
    die("Error fetching branches: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branches Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="branches-container">
        <h1>Manage Branches</h1>
        <form method="POST" class="branch-form">
            <input type="text" name="branch_name" placeholder="Branch Name" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="text" name="manager_name" placeholder="Manager Name">
            <input type="number" name="branch_id" placeholder="Branch ID" required>
            <button type="submit">Add Branch</button>
            

        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Branch Name</th>
                    <th>Location</th>
                    <th>Manager Name</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmt)): ?>
                <tr>
                    <td><?= $row['BranchID'] ?></td>
                    <td><?= $row['BranchName'] ?></td>
                    <td><?= $row['Location'] ?></td>
                    <td><?= $row['ManagerName'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
