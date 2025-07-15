<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Inventory System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Inside <style> -->
  <style>
    body {
      background: url('asaka_image1.png') no-repeat center center fixed;
      background-size: cover;
      background-color: #c4c4c4;
      margin: 0;
      padding: 0;
    }

    .main-container {
      max-width: 1100px;
      margin: 30px auto;
      background-color: #ffffff;
      padding: 40px 30px 60px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.25);
      position: relative;

    /* ✅ Animation styling */
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 0.3s ease-out forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .welcome-header {
      font-size: 2rem;
      font-weight: bold;
      color: #002366;
      margin-bottom: 10px;
    }

    .lead-text {
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: #555;
    }

    .feature-buttons {
      display: flex;
      justify-content: flex-start;
      flex-wrap: wrap;
      gap: 20px;
    }

    .feature-buttons a {
      text-decoration: none;
      flex: 1 1 200px;
    }

    .btn-feature {
      background-color: #003e6f;
      color: white;
      padding: 18px;
      text-align: center;
      border-radius: 10px;
      font-size: 1.1rem;
      box-shadow: 0 5px 10px rgba(0,0,0,0.2);
      transition: 0.3s;
    }

    .btn-feature:hover {
      background-color: #002366;
      transform: translateY(-3px);
    }

    .clock-container {
      position: absolute;
      top: 20px;
      right: 30px;
      text-align: right;
      color: #555;
      font-weight: 500;
    }

    .clock-container .time {
      font-size: 1.5rem;
      font-weight: bold;
      color: #003e6f;
    }

    .clock-container .date {
      font-size: 0.95rem;
      color: #666;
    }

    .log-box {
      margin-top: 40px;
    }

    .log-box h4 {
      color: #003e6f;
      font-weight: bold;
    }

    .list-group-item span {
      font-size: 0.8rem;
    }

    @media (max-width: 768px) {
      .clock-container {
        position: static;
        text-align: center;
        margin-top: 20px;
      }

      .feature-buttons {
        justify-content: center;
      }
    }
  </style>

</head>
<body>

<?php include 'navbar.php'; ?>

<div class="main-container">
  <div class="clock-container">
    <div class="time" id="current-time">--:--:--</div>
    <div class="date" id="current-date">-- -- ----</div>
  </div>

  <div class="welcome-header">📋 Welcome to MOBILE LEGENDS</div>
  <p class="lead-text">Easily manage your company’s assets using the functions below.</p>

  <div class="feature-buttons">
    <a href="new_entry.php">
      <div class="btn-feature">➕ Add New Entry</div>
    </a>
    <a href="edit_asset.php">
      <div class="btn-feature">🛠️ Edit Existing Assets</div>
    </a>
    <a href="masterlist.php">
      <div class="btn-feature">📑 View Master List</div>
    </a>
  </div>

  <!-- ✅ Recent Edit History Logs -->
  <div class="log-box">
    <h4>🕘 Recent Edit History</h4>
    <ul class="list-group">
      <?php
      $history_result = $conn->query("SELECT * FROM history_log ORDER BY timestamp DESC LIMIT 5");
      if ($history_result && $history_result->num_rows > 0):
        while ($log = $history_result->fetch_assoc()):
      ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= htmlspecialchars($log['username']) ?> - <?= htmlspecialchars($log['action']) ?>
            <span class="badge bg-secondary"><?= date("F j, Y g:i A", strtotime($log['timestamp'])) ?></span>
          </li>
      <?php
        endwhile;
      else:
        echo "<li class='list-group-item text-muted'>No recent activity found.</li>";
      endif;
      ?>
    </ul>
  </div>

</div>

<script>
  function updateClock() {
    const now = new Date();
    const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const time = now.toLocaleTimeString();
    const date = now.toLocaleDateString(undefined, optionsDate);

    document.getElementById("current-time").textContent = time;
    document.getElementById("current-date").textContent = date;
  }

  setInterval(updateClock, 1000);
  updateClock();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
