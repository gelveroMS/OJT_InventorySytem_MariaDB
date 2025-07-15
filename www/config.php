<?php
$host = "192.168.1.172";   // Host runs the database locally
$port = 3307;          // MariaDB port (match your my.ini)
$user = "lanuser";        // Database username
$password = "1234";    // Database password
$dbname = "inventory_db"; // Name of your database

$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
