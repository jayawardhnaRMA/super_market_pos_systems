<?php
$dsn = "POS_DSN"; // Replace with your DSN name
$username = "sa"; // SQL Server username
$password = "Ama0713@"; // SQL Server password

$conn = odbc_connect($dsn, $username, $password);

if (!$conn) {
    die("Connection failed: " . odbc_errormsg());
}
echo "Connected successfully!";
?>
