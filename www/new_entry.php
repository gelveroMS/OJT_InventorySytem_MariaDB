<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
include 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $requiredFields = [
    'asset_tag', 'tag_number', 'brand_model', 'serial_number',
    'type_os_type', 'processor', 'type_of_hard_disk', 'size_of_disk', 'memory',
    'office_version', 'ip_address', 'section', 'type_of_user', 'user',
    'email_password', 'date_purchased', 'ups', 'recovery_type', 'unit_type'
  ];

  $missingFields = [];
  foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
      $missingFields[] = $field;
    }
  }

  $email_address = empty($_POST['email_address']) ? 'n/a' : $_POST['email_address'];

  if (!empty($missingFields)) {
    $error = "⚠️ Missing fields: " . implode(', ', $missingFields);
  } else {
    $stmt = $conn->prepare("INSERT INTO assets (
      asset_tag, tag_number, brand_model, serial_number,
      type_os_type, processor, type_of_hard_disk, size_of_disk, memory,
      office_version, ip_address, section, type_of_user, user,
      email_address, email_password, date_purchased, ups,
      recovery_type, unit_type
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssssssssssssss",
      $_POST['asset_tag'], $_POST['tag_number'], $_POST['brand_model'], $_POST['serial_number'],
      $_POST['type_os_type'], $_POST['processor'], $_POST['type_of_hard_disk'], $_POST['size_of_disk'], $_POST['memory'],
      $_POST['office_version'], $_POST['ip_address'], $_POST['section'], $_POST['type_of_user'], $_POST['user'],
      $email_address, $_POST['email_password'], $_POST['date_purchased'], $_POST['ups'],
      $_POST['recovery_type'], $_POST['unit_type']
    );

    if ($stmt->execute()) {
      $new_id = $conn->insert_id;
      $username = $_SESSION['user'];
      $action = "Added new asset ID $new_id";
      $conn->query("INSERT INTO history_log (username, action) VALUES ('$username', '$action')");
      $conn->query("DELETE FROM history_log WHERE id NOT IN (SELECT id FROM (SELECT id FROM history_log ORDER BY timestamp DESC LIMIT 15) AS temp)");
      header("Location: masterlist.php");
      exit;
    } else {
      $error = "❌ Database error: " . $stmt->error;
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Asset Entry</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: rgb(196, 196, 196);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    @keyframes fadeSlideIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .container {
      background: white;
      border-radius: 10px;
      padding: 30px;
      margin: 30px auto;
      max-width: 850px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
      animation: fadeSlideIn 0.3s ease-out;
    }

    h2 {
      font-weight: bold;
      color: #333;
      margin-bottom: 25px;
    }
    .form-label {
      font-weight: 600;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
  <h2>➕ New Entry</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="row g-3">
      <div class="col-md-6"><label class="form-label">Asset Tag</label><input type="text" name="asset_tag" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Tag Number</label><input type="text" name="tag_number" class="form-control" required></div>

      <div class="col-md-6"><label class="form-label">Brand Model</label><input type="text" name="brand_model" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Serial Number</label><input type="text" name="serial_number" class="form-control" required></div>

      <div class="col-md-6">
        <label class="form-label">Type of OS</label>
        <select name="type_os_type" class="form-select" required>
          <option value="">Select</option>
          <option>WINDOWS XP</option><option>WINDOWS 7</option><option>WINDOWS 10</option><option>WINDOWS 11</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Processor</label>
        <select name="processor" class="form-select" required>
          <option value="">Select</option>
          <option>Intel Core i3</option><option>Intel Core i5</option><option>Intel Core i7</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Type of Hard Disk</label>
        <select name="type_of_hard_disk" class="form-select" required>
          <option value="">Select</option>
          <option>HDD</option><option>SSD</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Size of Disk</label>
        <select name="size_of_disk" class="form-select" required>
          <option value="">Select</option>
          <option>120 GB</option><option>250 GB</option><option>500 GB</option><option>1 TB</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Memory</label>
        <select name="memory" class="form-select" required>
          <option value="">Select</option>
          <option>4 GB</option><option>8 GB</option><option>16 GB</option><option>24 GB</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Office Version</label>
        <select name="office_version" class="form-select" required>
          <option value="">Select</option>
          <option>MICROSOFT OFFICE 2010</option><option>MICROSOFT OFFICE 2013</option><option>OFFICE 365</option><option>N/A</option>
        </select>
      </div>

      <div class="col-md-6"><label class="form-label">IP Address</label><input type="text" name="ip_address" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Section</label><input type="text" name="section" class="form-control" required></div>

      <div class="col-md-6">
        <label class="form-label">Type of User</label>
        <select name="type_of_user" class="form-select" required>
          <option value="">Select</option>
          <option>LAN USER</option><option>EMAIL USER</option><option>EMAIL AND INTERNET USER</option><option>ADMINISTRATOR</option>
        </select>
      </div>

      <div class="col-md-6"><label class="form-label">User</label><input type="text" name="user" class="form-control" required></div>

      <div class="col-md-6"><label class="form-label">Email Address</label><input type="text" name="email_address" class="form-control" placeholder="Leave blank for n/a"></div>
      <div class="col-md-6"><label class="form-label">Email Password</label><input type="text" name="email_password" class="form-control" required></div>
      <div class="col-md-6"><label class="form-label">Date Purchased</label><input type="date" name="date_purchased" class="form-control" required></div>

      <div class="col-md-6">
        <label class="form-label">UPS</label>
        <select name="ups" class="form-select" required>
          <option value="">Select</option>
          <option>YES</option><option>NO</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Recovery Type</label>
        <select name="recovery_type" class="form-select" required>
          <option value="">Select</option>
          <option>DVD</option><option>RECOVERY FLASHDRIVE</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Unit Type</label>
        <select name="unit_type" class="form-select" required>
          <option value="">Select</option>
          <option>DESKTOP</option><option>LAPTOP</option><option>SERVERS</option>
        </select>
      </div>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
