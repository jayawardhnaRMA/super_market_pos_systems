<?php
// customers.php: Manage customer information with memberships
include '../includes/db.php';

// Handle add operation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_customer'])) {
    $firstName = $_POST['customer_first_name'];
    $lastName = $_POST['customer_last_name'];
    $email = $_POST['customer_email'];
    $membership = $_POST['membership']; // Gold, Silver, or Platinum

    // Add the new customer
    $queryAdd = "INSERT INTO Customer (First_Name, Last_Name, Email, Purchasing_Level) VALUES (?, ?, ?, ?)";
    $stmtAdd = odbc_prepare($conn, $queryAdd);
    if (!odbc_execute($stmtAdd, [$firstName, $lastName, $email, $membership])) {
        die("Error adding customer: " . odbc_errormsg());
    }
}

// Fetch all customers
$query = "SELECT CustomerID, First_Name, Last_Name, Email, Purchasing_Level FROM Customer";
$stmt = odbc_exec($conn, $query);

if (!$stmt) {
    die("Error fetching customer: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="customers-container">
        <h1>Manage Customers</h1>
        <form method="POST" class="customers-form">
            <h2>Add Customer</h2>
            <input type="hidden" name="add_customer" value="1">
            <input type="text" name="customer_first_name" placeholder="First Name" required>
            <input type="text" name="customer_last_name" placeholder="Last Name" required>
            <input type="email" name="customer_email" placeholder="Customer Email" required>
            <select name="Purchasing_Level" required>
                <option value="Gold">Gold Membership</option>
                <option value="Silver">Silver Membership</option>
                <option value="Platinum">Platinum Membership</option>
            </select>
            <button type="submit">Add Customer</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Membership</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmt)): ?>
                <tr>
                    <td><?= $row['CustomerID'] ?></td>
                    <td><?= $row['First_Name'] ?></td>
                    <td><?= $row['Last_Name'] ?></td>
                    <td><?= $row['Email'] ?></td>
                    <td><?= $row['Purchasing_Level'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
