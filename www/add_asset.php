<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $asset_tag = $_POST['asset_tag'];
  $tag_number = $_POST['tag_number'];
  $brand_model = $_POST['brand_model'];
  $serial_number = $_POST['serial_number'];
  $processor = $_POST['processor'];
  $hdd_ssd_size = $_POST['hdd_ssd_size'];
  $recovery_tape = $_POST['recovery_tape'];
  $office_version = $_POST['office_version'];
  $memory = $_POST['memory'];
  $ip_address = $_POST['ip_address'];
  $user = $_POST['user'];
  $email_address = $_POST['email_address'];

  $sql = "INSERT INTO assets (
      asset_tag, tag_number, brand_model, serial_number, processor,
      hdd_ssd_size, recovery_tape, office_version, memory,
      ip_address, user, email_address
    ) VALUES (
      '$asset_tag', '$tag_number', '$brand_model', '$serial_number', '$processor',
      '$hdd_ssd_size', '$recovery_tape', '$office_version', '$memory',
      '$ip_address', '$user', '$email_address'
    )";

  if ($conn->query($sql) === TRUE) {
    echo "<p style='color:green;'>✅ Asset added successfully!</p>";
  } else {
    echo "<p style='color:red;'>❌ Error: " . $conn->error . "</p>";
  }
}
?>

<h2>Add New Asset</h2>
<form method="POST">
  Asset Tag: <input type="text" name="asset_tag" required><br><br>
  Tag Number: <input type="text" name="tag_number" required><br><br>
  Brand Model: <input type="text" name="brand_model" required><br><br>
  Serial Number: <input type="text" name="serial_number"><br><br>
  Processor: <input type="text" name="processor"><br><br>
  HDD/SSD Size: <input type="text" name="hdd_ssd_size"><br><br>
  Recovery Tape: <input type="text" name="recovery_tape"><br><br>
  Office Version: <input type="text" name="office_version"><br><br>
  Memory: <input type="text" name="memory"><br><br>
  IP Address: <input type="text" name="ip_address"><br><br>
  User: <input type="text" name="user"><br><br>
  Email Address: <input type="email" name="email_address"><br><br>

  <input type="submit" value="Save">
</form>

<a href="dashboard.php">← Back to Dashboard</a>
