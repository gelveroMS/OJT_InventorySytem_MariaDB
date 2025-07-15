<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
include 'config.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Asset_Masterlist_" . date('Ymd_His') . ".xls");

echo "<table border='1'>";
echo "<tr>
  <th>ID</th>
  <th>Asset Tag</th>
  <th>Tag Number</th>
  <th>Brand Model</th>
  <th>Serial Number</th>
  <th>Type of OS</th>
  <th>Processor</th>
  <th>Type of Hard Disk</th>
  <th>Size of Disk</th>
  <th>Office Version</th>
  <th>IP Address</th>
  <th>Section</th>
  <th>Type of User</th>
  <th>User</th>
  <th>Email Address</th>
  <th>Email Password</th>
  <th>Date Purchased</th>
  <th>UPS</th>
  <th>Recovery Type</th>
  <th>Unit Type</th>
  <th>Timestamp</th>
</tr>";

$result = $conn->query("SELECT * FROM assets ORDER BY id ASC");

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
      echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
  }
}

echo "</table>";
?>
