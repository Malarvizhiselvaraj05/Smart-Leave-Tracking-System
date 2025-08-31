<?php
session_start();
include('../DB_Config/Config.php');


if (empty($_SESSION['emp_id'])) {
    header("Location: login.php");
    exit;
}

$emp_id     = (string)($_SESSION['emp_id'] ?? '');
$emp_name   = (string)($_SESSION['emp_name'] ?? '');
$access_key = (string)($_SESSION['access_key'] ?? '');


if (in_array($access_key, ['a','s'], true)) {
    header("Location: Leave_Approval.php"); 
    exit;
}

$db = new dbconfig();
$conn = $db->getConnection();
if (!$conn) {
    http_response_code(500);
    die("Database connection failed.");
}

$sql = "SELECT request_id, start_date, end_date, leave_type, reason, status, rejection_reason, created_on 
        FROM xxhp_elms_leave_req_t 
        WHERE emp_id = ?
        ORDER BY created_on DESC";

$search = $_GET['search'] ?? '';

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    
    http_response_code(500);
    die("Failed to prepare statement.");
}
$stmt->bind_param("s", $emp_id);
if (!$stmt->execute()) {
    http_response_code(500);
    die("Failed to execute query.");
}
$result = $stmt->get_result();
if ($result === false) {
    http_response_code(500);
    die("Failed to fetch result set.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Leave Requests</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../CSS/Style_Leave_Req_List.php">
</head>
<body>

<header class="top-nav" role="banner">
  <div class="header-left" title="Leave Management System">
<div class="logo-wrap" title="College Logo">
  <img src="../Images/College_logo.webp" alt="College Logo" class="site-logo" />
</div>
    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($emp_name); ?>!</span>
  </div>

  <nav class="nav-menu" role="navigation" aria-label="Primary">
    
    <?php if (!in_array($access_key, ['a','s'], true)): ?>
      <a href="./Leave_Req_List.php">📄 Leave Requests</a>
      <a href="./Leave_History.php">📜 History</a>
    <?php endif; ?>

    <?php if (in_array($access_key, ['a','s'], true)): ?>
      <a href="./Leave_Approval.php">✅ Leave Approval</a>
    <?php endif; ?>
      <a href="./Leave_Balance.php">📅 Leave Balance</a>
    <a href="../Main/Logout.php" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
  </nav>
</header>

<main class="main-content" role="main">
  <div class="content-wrapper">
    <div style="text-align: left; margin-top: 20px;">
      <div class="page-title">My Leave Requests</div>
    </div>

    <div class="top-bar-container" role="region" aria-label="Actions and search">
      <?php if (!in_array($access_key, ['a','s'], true)): ?>
      <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <form action="Leave_Request.php" method="get" style="margin: 0;">
          <button type="submit" class="btn-request-leave" id="reqestleaveBtn">Request Leave</button>
        </form>

        
      </div>
      <?php endif; ?>

      <form action="Leave_Req_List.php" method="get" class="search-bar-container" style="margin:0;">
        <label for="searchInput" class="sr-only" style="position:absolute;clip:rect(0 0 0 0);height:1px;width:1px;overflow:hidden;">Search leave requests</label>
        <input type="text" id="searchInput" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" class="search-input" aria-label="Search leave requests">
        <button type="submit" class="btn-medium-action" aria-label="Submit search">Search</button>
      </form>
    </div>

    <div class="leave-info" style="margin-top:18px;">
      <table class="leave-table" role="table" aria-label="My leave requests">
        <thead>
          <tr>
            <th scope="col">Register Number</th>
            <th scope="col">Name</th>
            <th scope="col">Start Date</th>
            <th scope="col">End Date</th>
            <th scope="col">Leave Type</th>
            <th scope="col">Reason</th>
            <th scope="col">Status</th>
            <th scope="col">Created On</th>
             
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($emp_id); ?></td>
                <td><?php echo htmlspecialchars($emp_name); ?></td>
                <td><?php echo date('d-M-Y', strtotime($row['start_date'])); ?></td>
                <td><?php echo date('d-M-Y', strtotime($row['end_date'])); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($row['leave_type'])); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['reason'])); ?></td>
                <td class="status-<?php echo htmlspecialchars($row['status']); ?>">
  <?php 
    if ($row['status'] === 'Rejected' && !empty($row['rejection_reason'])) {
        echo "Rejected - " . htmlspecialchars($row['rejection_reason']);
    } else {
        echo ucfirst(htmlspecialchars($row['status']));
    }
  ?>
</td>

                <td><?php echo date('d-M-Y', strtotime($row['created_on'])); ?></td>
                 
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="9" style="text-align:center; padding:18px;">No leave requests found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<script>
(function () {
  const hamburger = document.getElementById('hamburgerBtn');
  const sideMenu = document.getElementById('sideMenu');

  if (!hamburger || !sideMenu) return;

  function openMenu() {
    sideMenu.classList.add('show');
    sideMenu.setAttribute('aria-hidden', 'false');
    hamburger.setAttribute('aria-expanded', 'true');
  }
  function closeMenu() {
    sideMenu.classList.remove('show');
    sideMenu.setAttribute('aria-hidden', 'true');
    hamburger.setAttribute('aria-expanded', 'false');
  }
  hamburger.addEventListener('click', function (e) {
    const expanded = hamburger.getAttribute('aria-expanded') === 'true';
    if (expanded) closeMenu(); else openMenu();
  });

  document.addEventListener('click', function (e) {
    if (!sideMenu.contains(e.target) && !hamburger.contains(e.target) && sideMenu.classList.contains('show')) {
      closeMenu();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sideMenu.classList.contains('show')) closeMenu();
  });
})();

(function () {
  const searchInput = document.getElementById('searchInput');
  if (!searchInput) return;
  const rows = () => document.querySelectorAll('.leave-table tbody tr');

  function applyFilter() {
    const filter = searchInput.value.trim().toLowerCase();
    rows().forEach(row => {
      if (row.querySelector('td') && row.querySelector('td').getAttribute('colspan')) return;
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? '' : 'none';
    });
  }

  let timer;
  searchInput.addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(applyFilter, 150);
  });

  window.addEventListener('DOMContentLoaded', applyFilter);
})();

.status-Approved { color: green !important; }
.status-Rejected { color: red !important; }
.status-Pending { color: orange !important; }

</script>

</body>
</html>
