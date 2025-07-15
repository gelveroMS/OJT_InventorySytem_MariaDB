<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "inventory_db";

// Connect to MySQL
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Create DB if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Import assets.sql
$assetsSql = file_get_contents("assets.sql");
if ($conn->multi_query($assetsSql)) {
    do {
        // flush results
        $conn->store_result();
    } while ($conn->more_results() && $conn->next_result());
    echo "✅ assets.sql imported successfully.<br>";
} else {
    echo "⚠️ Error importing assets.sql: " . $conn->error . "<br>";
}

// Import users.sql
$usersSql = file_get_contents("users.sql");
if ($conn->multi_query($usersSql)) {
    do {
        $conn->store_result();
    } while ($conn->more_results() && $conn->next_result());
    echo "✅ users.sql imported successfully.<br>";
} else {
    echo "⚠️ Error importing users.sql: " . $conn->error . "<br>";
}

echo "<br><strong>🎉 Setup complete! You can now use the system. Delete or rename setup.php for security.</strong>";
?>
