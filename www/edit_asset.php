<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$error = "";
$asset = null;

if (isset($_POST['selected_ids'])) {
  $_SESSION['edit_queue'] = json_decode($_POST['selected_ids'], true);
}

if (isset($_POST['update'])) {
  $email_address = empty(trim($_POST['email_address'])) ? 'n/a' : $_POST['email_address'];

  $stmt = $conn->prepare("UPDATE assets SET asset_tag=?, tag_number=?, brand_model=?, serial_number=?, type_os_type=?, processor=?, type_of_hard_disk=?, size_of_disk=?, memory=?, office_version=?, ip_address=?, section=?, type_of_user=?, user=?, email_address=?, email_password=?, date_purchased=?, ups=?, recovery_type=?, unit_type=?, timestamp=CURRENT_TIMESTAMP WHERE id=?");
  $stmt->bind_param("ssssssssssssssssssssi", 
    $_POST['asset_tag'], $_POST['tag_number'], $_POST['brand_model'], $_POST['serial_number'], 
    $_POST['type_os_type'], $_POST['processor'], $_POST['type_of_hard_disk'], $_POST['size_of_disk'], $_POST['memory'],
    $_POST['office_version'], $_POST['ip_address'], $_POST['section'], $_POST['type_of_user'], 
    $_POST['user'], $email_address, 
    $_POST['email_password'], $_POST['date_purchased'], 
    $_POST['ups'], $_POST['recovery_type'], $_POST['unit_type'], $_POST['id']
  );
  $stmt->execute();

  // ✅ Log the update to history_log
  $username = $_SESSION['user'];
  $assetId = $_POST['id'];
  $logAction = "Edited asset ID $assetId";
  $conn->query("INSERT INTO history_log (username, action) VALUES ('$username', '$logAction')");

  // ✅ Keep only the 15 most recent entries in history_log
  $conn->query("
    DELETE FROM history_log 
    WHERE id NOT IN (
      SELECT id FROM (
        SELECT id FROM history_log ORDER BY timestamp DESC LIMIT 15
      ) AS keep_ids
    )
  ");

  $success = "✅ Asset ID $assetId successfully updated.";

  if (isset($_SESSION['edit_queue'])) {
    if (($key = array_search($assetId, $_SESSION['edit_queue'])) !== false) {
      unset($_SESSION['edit_queue'][$key]);
      $_SESSION['edit_queue'] = array_values($_SESSION['edit_queue']);
    }
  }
}

if (!empty($_SESSION['edit_queue'])) {
  $next_id = $_SESSION['edit_queue'][0];
} elseif (isset($_POST['manual_id'])) {
  $next_id = $_POST['manual_id'];
} else {
  $next_id = null;
}

if ($next_id) {
  $stmt = $conn->prepare("SELECT * FROM assets WHERE id = ?");
  $stmt->bind_param("i", $next_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $asset = $result->fetch_assoc();
  } else {
    $error = "❌ Asset with ID $next_id not found.";
  }
} elseif (!isset($success)) {
  $error = "ℹ️ No asset selected. Enter an ID below.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Asset</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: rgb(196, 196, 196);
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

  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
  <h2 class="mb-3">✏️ Edit Asset</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-info"><?= $error ?></div>
  <?php elseif (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <?php if (!$asset): ?>
    <form method="POST" class="mb-4">
      <div class="mb-3">
        <label for="manual_id" class="form-label">Enter Asset ID to Edit:</label>
        <input type="number" name="manual_id" id="manual_id" class="form-control" required>
      </div>
      <button type="submit" class="btn text-white" style="background-color: #002366;">🔍 Load Asset</button>
    </form>
  <?php endif; ?>

  <?php if ($asset): ?>
  <form method="POST">
    <input type="hidden" name="id" value="<?= $asset['id'] ?>">

    <?php
    function selectField($label, $name, $options, $selected) {
      echo "<div class='mb-3'><label class='form-label'>$label</label><select name='$name' class='form-select' required>";
      foreach ($options as $opt) {
        $isSelected = $opt == $selected ? "selected" : "";
        echo "<option value='$opt' $isSelected>$opt</option>";
      }
      echo "</select></div>";
    }

    function inputField($label, $name, $value, $type = 'text') {
      $required = $name === 'email_address' ? '' : 'required';
      $placeholder = $name === 'email_address' ? "placeholder='n/a'" : '';
      echo "<div class='mb-3 position-relative'><label class='form-label'>$label</label><div class='input-group'>
              <input type='$type' name='$name' id='$name' class='form-control' value='" . htmlspecialchars($value) . "' $required $placeholder>";
      if ($type === 'password') {
        echo "<span class='input-group-text'><i class='fa fa-eye' style='cursor:pointer' onclick='togglePassword(\"$name\")'></i></span>";
      }
      echo "</div></div>";
    }

    inputField("Asset Tag", "asset_tag", $asset['asset_tag']);
    inputField("Tag Number", "tag_number", $asset['tag_number']);
    inputField("Brand Model", "brand_model", $asset['brand_model']);
    inputField("Serial Number", "serial_number", $asset['serial_number']);
    selectField("Type of OS", "type_os_type", ["WINDOWS XP", "WINDOWS 7", "WINDOWS 10", "WINDOWS 11"], $asset['type_os_type']);
    selectField("Processor", "processor", ["Intel Core i7", "Intel Core i5", "Intel Core i3"], $asset['processor']);
    selectField("Type of Hard Disk", "type_of_hard_disk", ["HDD", "SSD"], $asset['type_of_hard_disk']);
    selectField("Size of Disk", "size_of_disk", ["120 GB", "250 GB", "500 GB", "1 TB"], $asset['size_of_disk']);
    selectField("Memory", "memory", ["4 GB", "8 GB", "16 GB", "24 GB"], $asset['memory']);
    selectField("Office Version", "office_version", ["MICROSOFT OFFICE 2010", "MICROSOFT OFFICE 2013", "OFFICE 365", "N/A"], $asset['office_version']);
    inputField("IP Address", "ip_address", $asset['ip_address']);
    inputField("Section", "section", $asset['section']);
    selectField("Type of User", "type_of_user", ["LAN USER", "EMAIL USER", "EMAIL AND INTERNET USER", "ADMINISTRATOR"], $asset['type_of_user']);
    inputField("User", "user", $asset['user']);
    inputField("Email Address", "email_address", $asset['email_address']);
    inputField("Email Password", "email_password", $asset['email_password'], "password");
    ?>

    <div class="mb-3">
      <label class="form-label">Date Purchased</label>
      <input type="date" name="date_purchased" class="form-control" value="<?= htmlspecialchars($asset['date_purchased']) ?>" required>
    </div>

    <?php
    selectField("UPS", "ups", ["YES", "NO"], $asset['ups']);
    selectField("Recovery Type", "recovery_type", ["DVD", "RECOVERY FLASHDRIVE"], $asset['recovery_type']);
    selectField("Unit Type", "unit_type", ["DESKTOP", "LAPTOP", "SERVERS"], $asset['unit_type']);
    ?>

    <button type="submit" name="update" class="btn btn-primary w-100">✅ Update & Continue</button>
  </form>
  <?php endif; ?>
</div>

<script>
function togglePassword(fieldId) {
  const field = document.getElementById(fieldId);
  const icon = field.nextElementSibling.querySelector("i");
  if (field.type === "password") {
    field.type = "text";
    icon.classList.replace("fa-eye", "fa-eye-slash");
  } else {
    field.type = "password";
    icon.classList.replace("fa-eye-slash", "fa-eye");
  }
}
</script>

</body>
</html>
