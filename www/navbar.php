<?php
if (!isset($_SESSION)) session_start();
?>
<nav class="navbar navbar-expand-lg custom-blue-navbar mb-4">
  <div class="container-fluid d-flex align-items-center">

    <!-- Reload Button only -->
    <button class="btn btn-outline-light btn-sm rounded-circle me-2" onclick="location.reload();" title="Reload">
      <i class="bi bi-arrow-clockwise"></i>
    </button>

    <a class="navbar-brand text-white" href="dashboard.php">ShinDengen Inventory System</a>
    
    <div class="ms-auto d-flex align-items-center">
      <span class="text-white me-3">👤 <?= $_SESSION['user'] ?? 'Guest'; ?></span>
      <a class="btn btn-outline-light me-2" href="new_entry.php">New Entry</a>
      <a class="btn btn-outline-light me-2" href="edit_asset.php">Edit Asset</a>
      <a class="btn btn-outline-light me-2" href="masterlist.php">Master List</a>
      <a class="btn btn-danger btn-sm" href="logout.php">Logout</a>
    </div>
    
  </div>
</nav>

<style>
  .custom-blue-navbar {
    background-color: #003e6f;
  }
  .btn-outline-light i.bi {
    font-size: 1rem;
    vertical-align: middle;
  }
</style>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Ctrl+R and F5 Reload Support -->
<script>
  document.addEventListener("keydown", function (event) {
    if ((event.ctrlKey && event.key === 'r') || event.key === 'F5') {
      event.preventDefault();
      location.reload();
    }
  });
</script>
