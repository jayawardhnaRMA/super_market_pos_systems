<?php
// Payments.php: Manage payments
include '../includes/db.php';

// Fetch customers for dropdown
$queryCustomers = "SELECT CustomerID, First_Name, Last_Name FROM Customer";
$stmtCustomers = odbc_exec($conn, $queryCustomers);
if (!$stmtCustomers) {
    die("Error fetching customers: " . odbc_errormsg());
}

// Handle form submission for adding payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerID = $_POST['customer_id'];
    $amount = $_POST['amount'];
    $paymentDate = $_POST['payment_date'];
    $paymentMethod = $_POST['payment_method'];

    // Insert payment record
    $query = "INSERT INTO Payment (CustomerID, Amount, PaymentDate, PaymentMethod) VALUES (?, ?, ?, ?)";
    $stmt = odbc_prepare($conn, $query);

    if (!odbc_execute($stmt, [$customerID, $amount, $paymentDate, $paymentMethod])) {
        die("Error inserting payment: " . odbc_errormsg());
    }
}

// Fetch all payments
$queryPayments = "SELECT p.PaymentID, p.Amount, p.PaymentDate, p.PaymentMethod, c.First_Name, c.Last_Name 
                  FROM Payment p
                  LEFT JOIN Customer c ON p.CustomerID = c.CustomerID";
$stmtPayments = odbc_exec($conn, $queryPayments);
if (!$stmtPayments) {
    die("Error fetching payments: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="payments-container">
        <h1>Manage Payments</h1>
        <form method="POST" class="payment-form">
            <h2>Add Payment</h2>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php while ($row = odbc_fetch_array($stmtCustomers)): ?>
                <option value="<?= $row['CustomerID'] ?>">
                    <?= $row['First_Name'] . ' ' . $row['Last_Name'] ?>
                </option>
                <?php endwhile; ?>
            </select>
            <input type="number" step="0.01" name="amount" placeholder="Amount" required>
            <input type="date" name="payment_date" required>
            <select name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="Online">Online</option>
            </select>
            <button type="submit">Add Payment</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Payment Method</th>
                    <th>Customer</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmtPayments)): ?>
                <tr>
                    <td><?= $row['PaymentID'] ?></td>
                    <td><?= $row['Amount'] ?></td>
                    <td><?= $row['PaymentDate'] ?></td>
                    <td><?= $row['PaymentMethod'] ?></td>
                    <td><?= $row['First_Name'] . ' ' . $row['Last_Name'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
