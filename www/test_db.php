<?php
$host = "127.0.0.1";
$port = "3307"; // or your actual port
$user = "root";
$password = "1234";
$dbname = "inventory_db";

$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully!";
?>
