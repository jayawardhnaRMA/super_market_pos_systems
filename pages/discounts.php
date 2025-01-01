<?php
// Discounts.php: Manage discounts and promotions
include '../includes/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $discountPercentage = $_POST['discount_percentage'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    $query = "INSERT INTO Discount (Description, DiscountPercentage, StartDate, EndDate) VALUES (?, ?, ?, ?)";
    $stmt = odbc_prepare($conn, $query);

    if (!odbc_execute($stmt, [$description, $discountPercentage, $startDate, $endDate])) {
        die("Error inserting discount: " . odbc_errormsg());
    }
}

// Fetch all discounts
$query = "SELECT * FROM Discount";
$stmt = odbc_exec($conn, $query);
if (!$stmt) {
    die("Error fetching discounts: " . odbc_errormsg());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discounts Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="discounts-container">
        <h1>Manage Discounts</h1>
        <form method="POST" class="discount-form">
            <input type="text" name="description" placeholder="Description" required>
            <input type="number" step="0.01" name="discount_percentage" placeholder="Discount Percentage" required>
            <input type="date" name="start_date" required>
            <input type="date" name="end_date" required>
            <button type="submit">Add Discount</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Discount Percentage</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = odbc_fetch_array($stmt)): ?>
                <tr>
                    <td><?= $row['DiscountID'] ?></td>
                    <td><?= $row['Description'] ?></td>
                    <td><?= $row['DiscountPercentage'] ?></td>
                    <td><?= $row['StartDate'] ?></td>
                    <td><?= $row['EndDate'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
