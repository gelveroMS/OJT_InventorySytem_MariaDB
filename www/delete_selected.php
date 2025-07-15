<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    echo 'unauthorized';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = json_decode($_POST['selected_ids'], true);

    if (is_array($ids) && count($ids) > 0) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $stmt = $conn->prepare("DELETE FROM assets WHERE id IN ($placeholders)");
        if (!$stmt) {
            echo 'prep_error';
            exit;
        }

        $stmt->bind_param($types, ...$ids);
        if ($stmt->execute()) {
            // ✅ Use session username
            $username = $_SESSION['user'];
            $logAction = "Deleted asset ID: " . implode(', ', $ids);
            $conn->query("INSERT INTO history_log (username, action) VALUES ('$username', '$logAction')");

            // ✅ Limit logs to 15
            $conn->query("DELETE FROM history_log WHERE id NOT IN (SELECT id FROM (SELECT id FROM history_log ORDER BY timestamp DESC LIMIT 15) AS t)");

            echo 'success';
        } else {
            echo 'exec_error';
        }
    } else {
        echo 'invalid_input';
    }
} else {
    echo 'bad_method';
}
?>
