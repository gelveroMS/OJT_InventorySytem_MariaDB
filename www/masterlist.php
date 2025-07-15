<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
include 'config.php';

$result = $conn->query("SELECT * FROM assets ORDER BY id ASC");

function getDistinctValues($conn, $column) {
  $res = $conn->query("SELECT DISTINCT `$column` FROM assets ORDER BY `$column` ASC");
  $values = [];
  while ($row = $res->fetch_assoc()) {
    $values[] = $row[$column];
  }
  return $values;
}
$osOptions = getDistinctValues($conn, 'type_os_type');
$processorOptions = getDistinctValues($conn, 'processor');
$hardDiskOptions = getDistinctValues($conn, 'type_of_hard_disk');
$diskSizeOptions = getDistinctValues($conn, 'size_of_disk');
$memoryOptions = getDistinctValues($conn, 'memory');
$officeOptions = getDistinctValues($conn, 'office_version');
$userTypeOptions = getDistinctValues($conn, 'type_of_user');
$upsOptions = getDistinctValues($conn, 'ups');
$recoveryOptions = getDistinctValues($conn, 'recovery_type');
$unitTypeOptions = getDistinctValues($conn, 'unit_type');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Asset Master List</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: rgb(196, 196, 196); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
    .container { background: white; border-radius: 10px; padding: 20px; margin: 20px auto; max-width: 98vw; height: 90vh; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); display: flex; flex-direction: column; }
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; }
    .header-bar h2 { font-weight: bold; color: #333; margin: 0; }
    .button-group { display: flex; align-items: center; gap: 10px; flex-wrap: nowrap; }
    .table-wrapper { flex-grow: 1; overflow: auto; border: 2px solid #ccc; }
    table { width: 100%; border-collapse: collapse; font-size: 0.73rem; white-space: nowrap; }
    thead th { background-color: #343a40; color: white; text-align: center; vertical-align: middle; padding: 6px; position: sticky; top: 0; z-index: 2; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    tbody td { text-align: center; vertical-align: middle; padding: 4px; border-top: 1px solid #dee2e6; background-color: white; }
    tbody tr:nth-child(even) td { background-color: #f9f9f9; }
    tbody tr:hover td { background-color: #e9ecef; }
    .selected-row td { background-color: #b6dbfc !important; color: #000; }
    select.filter-select { font-size: 0.7rem; padding: 2px 5px; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
  <div class="header-bar">
    <h2>📋 Master List</h2>
    <div class="button-group d-flex flex-wrap align-items-center gap-2">
      <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="🔍 Search..." style="width: 180px;">
      <button id="editSelected" class="btn btn-primary btn-md" disabled>✏️ Batch Edit Selected</button>
      <button id="deleteSelected" class="btn btn-danger btn-md" disabled>🗑️ Delete Selected</button>
      <form method="POST" action="export_excel.php" class="d-inline">
        <button type="submit" class="btn btn-success btn-md">📁 Export to Excel</button>
      </form>
    </div>
  </div>

  <div class="table-wrapper">
    <form id="batchEditForm" method="POST" action="edit_asset.php">
      <input type="hidden" name="selected_ids" id="selected_ids">
      <table class="table table-bordered table-hover align-middle" id="masterTable">
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>Asset Tag</th>
            <th>Tag Number</th>
            <th>Brand Model</th>
            <th>Serial Number</th>
            <th>Type of OS<br><select class="filter-select" data-column="6"><option value="">All</option><?php foreach ($osOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>Processor<br><select class="filter-select" data-column="7"><option value="">All</option><?php foreach ($processorOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>Type of Hard Disk<br><select class="filter-select" data-column="8"><option value="">All</option><?php foreach ($hardDiskOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>Size of Disk<br><select class="filter-select" data-column="9"><option value="">All</option><?php foreach ($diskSizeOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>Memory<br><select class="filter-select" data-column="10"><option value="">All</option><option>4 GB</option><option>8 GB</option><option>16 GB</option><option>24 GB</option></select></th>
            <th>Office Version<br><select class="filter-select" data-column="11"><option value="">All</option><?php foreach ($officeOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>IP Address</th>
            <th>Section</th>
            <th>Type of User<br><select class="filter-select" data-column="14"><option value="">All</option><?php foreach ($userTypeOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>User</th>
            <th>Email Address</th>
            <th>Email Password</th>
            <th>Date Purchased</th>
            <th>UPS<br><select class="filter-select" data-column="19"><option value="">All</option><?php foreach ($upsOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>Recovery Type<br><select class="filter-select" data-column="20"><option value="">All</option><?php foreach ($recoveryOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>Unit Type<br><select class="filter-select" data-column="21"><option value="">All</option><?php foreach ($unitTypeOptions as $opt) echo "<option>$opt</option>"; ?></select></th>
            <th>Timestamp</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr data-id="<?= $row['id'] ?>">
                <td><input type="checkbox" class="row-check" value="<?= $row['id'] ?>"></td>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['asset_tag']) ?></td>
                <td><?= htmlspecialchars($row['tag_number']) ?></td>
                <td><?= htmlspecialchars($row['brand_model']) ?></td>
                <td><?= htmlspecialchars($row['serial_number']) ?></td>
                <td><?= htmlspecialchars($row['type_os_type']) ?></td>
                <td><?= htmlspecialchars($row['processor']) ?></td>
                <td><?= htmlspecialchars($row['type_of_hard_disk']) ?></td>
                <td><?= htmlspecialchars($row['size_of_disk']) ?></td>
                <td><?= htmlspecialchars($row['memory']) ?></td>
                <td><?= htmlspecialchars($row['office_version']) ?></td>
                <td><?= htmlspecialchars($row['ip_address']) ?></td>
                <td><?= htmlspecialchars($row['section']) ?></td>
                <td><?= htmlspecialchars($row['type_of_user']) ?></td>
                <td><?= htmlspecialchars($row['user']) ?></td>
                <td><?= htmlspecialchars($row['email_address']) ?></td>
                <td><?= htmlspecialchars($row['email_password']) ?></td>
                <td><?= htmlspecialchars($row['date_purchased']) ?></td>
                <td><?= htmlspecialchars($row['ups']) ?></td>
                <td><?= htmlspecialchars($row['recovery_type']) ?></td>
                <td><?= htmlspecialchars($row['unit_type']) ?></td>
                <td><?= htmlspecialchars($row['timestamp']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="23" class="text-center text-muted">No records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </form>
  </div>
</div>

<script>
  const rowCheckboxes = document.querySelectorAll('.row-check');
  const editButton = document.getElementById('editSelected');
  const deleteButton = document.getElementById('deleteSelected');
  const selectedInput = document.getElementById('selected_ids');
  const form = document.getElementById('batchEditForm');

  function toggleButton() {
    const selected = [...rowCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
    editButton.disabled = selected.length === 0;
    deleteButton.disabled = selected.length === 0;
  }

  rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      cb.closest('tr').classList.toggle('selected-row', cb.checked);
      toggleButton();
    });
  });

  document.querySelectorAll('#masterTable tbody tr').forEach(row => {
    row.addEventListener('click', function (e) {
      if (e.target.type === 'checkbox') return;
      const checkbox = this.querySelector('.row-check');
      checkbox.checked = !checkbox.checked;
      this.classList.toggle('selected-row', checkbox.checked);
      toggleButton();
    });
  });

  editButton.addEventListener('click', function (e) {
    e.preventDefault();
    const selected = [...rowCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
    if (selected.length > 0) {
      selectedInput.value = JSON.stringify(selected);
      form.submit();
    }
  });

  deleteButton.addEventListener('click', function () {
    const selected = [...rowCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
    if (selected.length === 0) return;
    if (!confirm("Are you sure you want to delete the selected assets?")) return;

    const formData = new FormData();
    formData.append('selected_ids', JSON.stringify(selected));

    fetch('delete_selected.php', {
      method: 'POST',
      body: formData,
      credentials: 'include'
    })
    .then(response => response.text())
    .then(data => {
      if (data.trim() === 'success') {
        selected.forEach(id => {
          const row = document.querySelector(`tr[data-id="${id}"]`);
          if (row) row.remove();
        });
        alert('✅ Selected assets deleted successfully.');
        location.reload();
      } else {
        alert('❌ Failed to delete selected records. Server says: ' + data);
      }
    });
  });

  function filterTable() {
    const selects = document.querySelectorAll("select.filter-select");
    const rows = document.querySelectorAll("#masterTable tbody tr");

    rows.forEach(row => {
      let visible = true;
      selects.forEach(select => {
        const columnIndex = parseInt(select.getAttribute("data-column"));
        const filterValue = select.value.toLowerCase();
        const cellText = row.children[columnIndex].textContent.toLowerCase();
        if (filterValue && cellText !== filterValue) {
          visible = false;
        }
      });
      row.style.display = visible ? "" : "none";
    });
  }

  document.querySelectorAll("select.filter-select").forEach(select => {
    select.addEventListener("change", filterTable);
  });

  document.getElementById('searchInput').addEventListener('keyup', function () {
    const searchTerm = this.value.toLowerCase();
    document.querySelectorAll("#masterTable tbody tr").forEach(row => {
      const match = [...row.children].some(td => td.textContent.toLowerCase().includes(searchTerm));
      row.style.display = match ? "" : "none";
    });
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
