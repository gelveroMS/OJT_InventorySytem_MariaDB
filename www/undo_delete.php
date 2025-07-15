<?php
session_start();
include 'config.php';

if (!isset($_SESSION['deleted_records'])) {
  echo '⚠️ No records to undo.';
  exit;
}

$records = $_SESSION['deleted_records'];
$success = true;

foreach ($records as $row) {
    $columns = implode(", ", array_keys($row));
    $placeholders = implode(", ", array_fill(0, count($row), '?'));
    $stmt = $conn->prepare("INSERT INTO assets ($columns) VALUES ($placeholders)");
    $types = str_repeat("s", count($row));
    $stmt->bind_param($types, ...array_values($row));
    if (!$stmt->execute()) {
        $success = false;
        break;
    }
}

if ($success) {
    unset($_SESSION['deleted_records']);
    echo 'success';
} else {
    echo '❌ Failed to undo.';
}
?>
