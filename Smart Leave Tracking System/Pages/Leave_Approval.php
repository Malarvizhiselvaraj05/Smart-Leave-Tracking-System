<?php
session_start();
include('../DB_Config/Config.php');

if (!isset($_SESSION['emp_id']) || !isset($_SESSION['access_key'])) {
    header("Location: Login.php");
    exit;
}

$emp_id     = $_SESSION['emp_id'];
$emp_name   = $_SESSION['emp_name'];
$access_key = $_SESSION['access_key'];
$emp_dept   = $_SESSION['department'] ?? '';

$db = new dbconfig();
$conn = $db->getConnection();
if (!$conn) {
    http_response_code(500);
    die("Database connection failed.");
}

$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$search_sql = "";
if ($search !== '') {
    $search_safe = $conn->real_escape_string($search);
    $search_sql = " AND (
        r.request_id LIKE '%$search_safe%' OR
        r.emp_id LIKE '%$search_safe%' OR
        u.emp_name LIKE '%$search_safe%' OR
        r.leave_type LIKE '%$search_safe%' OR
        r.status LIKE '%$search_safe%'
    )";
}

$admin_dept_raw = trim((string)$emp_dept);
$admin_dept_upper = strtoupper($admin_dept_raw);
$dept_sql = "";

if ($admin_dept_raw !== '') {
    $dept_safe = $conn->real_escape_string($admin_dept_raw);

    if ($admin_dept_upper === 'S&H') {
        $dept_sql = " AND ( UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$dept_safe}%')) OR UPPER(TRIM(u.department)) LIKE '% I' )";
    } else {
        if (preg_match('/\bI$/i', $admin_dept_upper)) {
            $dept_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$dept_safe}'))";
        } else {
            $dept_sql = " AND ( UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$dept_safe}%')) AND UPPER(TRIM(u.department)) NOT LIKE '% I' )";
        }
    }
}

$year_sql = "";
$year_param = isset($_GET['year']) ? trim((string)$_GET['year']) : '';

$sh_buttons = ['AD I','IT I','CSE I','CIVIL I','MECH I','ECE I','EEE I'];
$prefixes_with_year_buttons = ['AD','IT','CSE','ECE','EEE','CIVIL','MECH'];

$admin_prefix = '';
if (preg_match('/^([A-Za-z&]+)/', $admin_dept_raw, $m)) {
    $admin_prefix = strtoupper($m[1]);
}

if ($year_param !== '') {
    if ($admin_dept_upper === 'S&H' && in_array(strtoupper($year_param), array_map('strtoupper', $sh_buttons), true)) {
        $year_safe = $conn->real_escape_string($year_param);
        $year_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$year_safe}'))";
    } else {
        $year_up = strtoupper($year_param);
        if (in_array($year_up, ["II","III","IV"], true) && in_array($admin_prefix, $prefixes_with_year_buttons, true)) {
            $year_safe = $conn->real_escape_string($year_up);
            $year_sql = " AND UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$admin_prefix} {$year_safe}'))";
        } else {
            if (preg_match('/^[A-Za-z]+ I$/i', $year_param) && preg_match('/\bI$/i', $admin_dept_upper) && stripos($admin_dept_upper, strtoupper(explode(' ', $year_param)[0])) === 0) {
                $year_safe = $conn->real_escape_string($year_param);
                $year_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$year_safe}'))";
            } else {
                $year_sql = "";
            }
        }
    }
}

$emp_id_safe = $conn->real_escape_string((string)$emp_id);

# --- leave_days subquery excluding OD ---
$leave_days_sql = "(SELECT COALESCE(SUM(
                      (SELECT COUNT(*)
                       FROM (
                         SELECT ADDDATE(lr.start_date, t4.i*1000 + t3.i*100 + t2.i*10 + t1.i) AS dt
                         FROM 
                           (SELECT 0 i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
                            UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 
                            UNION ALL SELECT 8 UNION ALL SELECT 9) t1,
                           (SELECT 0 i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
                            UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 
                            UNION ALL SELECT 8 UNION ALL SELECT 9) t2,
                           (SELECT 0 i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
                            UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 
                            UNION ALL SELECT 8 UNION ALL SELECT 9) t3,
                           (SELECT 0 i UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 
                            UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 
                            UNION ALL SELECT 8 UNION ALL SELECT 9) t4
                       ) days
                       WHERE days.dt BETWEEN lr.start_date AND lr.end_date
                         AND DAYOFWEEK(days.dt) <> 1
                         AND DATE_FORMAT(days.dt, '%m-%d') NOT IN ('01-01','01-14','10-20','12-25')
                      )
                  ),0)
                  FROM xxhp_elms_leave_req_t lr 
                  WHERE lr.emp_id = r.emp_id 
                    AND lr.status = 'Approved'
                    AND lr.leave_type <> 'OD') AS leave_days";

if ($access_key == 'a') {
    $sql = "SELECT r.*, u.emp_name, u.department,
                   $leave_days_sql
            FROM xxhp_elms_leave_req_t r
            JOIN xxhp_elms_emp_det_t u ON r.emp_id = u.emp_id
            WHERE u.access_key = 'e'
              AND (r.end_date >= CURDATE() OR r.end_date IS NULL)
              $search_sql $dept_sql $year_sql
            ORDER BY r.request_id ASC";
} elseif ($access_key == 's') {
    $sql = "SELECT r.*, u.emp_name, u.department,
                   $leave_days_sql
            FROM xxhp_elms_leave_req_t r
            JOIN xxhp_elms_emp_det_t u ON r.emp_id = u.emp_id
            WHERE (u.access_key IN ('a', 's') OR r.emp_id = '$emp_id_safe')
              AND (r.end_date >= CURDATE() OR r.end_date IS NULL)
              $search_sql $dept_sql $year_sql
            ORDER BY r.request_id ASC";
} else {
    echo "<h3 style='color:red; text-align:center;'>Access Denied</h3>";
    exit;
}

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Approval List</title>
  <link rel="stylesheet" href="../CSS/Style_Leave_Approval.php">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    .status {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 12px;
      font-weight: 700;
      font-size: 0.9rem;
      text-decoration: none;
    }
    .status.approved { background: #2e7d32; color: #fff; }
    .status.rejected { background: #c62828; color: #fff; }
    .status.pending  { background: #f0ad4e; color: #111; }
    .status.unknown  { background: #6b6b6b; color: #fff; }
    .status-link { text-decoration: none; }
    
    .year-filter-btn, .special-btn {
      padding:6px 10px;
      border-radius:6px;
      border:1px solid #ccc;
      background: #005A9C;
      cursor:pointer;
      color: white;
    }
    .year-filter-btn.active, .special-btn.active {
      background:#005A9C;
      border-color:#999;
      color: white;
    }

    .table-container { overflow-x:auto; }
    .main-table { width:100%; border-collapse:collapse; min-width:980px; }
    .main-table th, .main-table td { padding:8px 10px; border-bottom:1px solid #eee; text-align:left; }
  </style>
</head>
<body>

<header class="top-nav" role="banner">
  <div class="header-left" title="Leave Management System">
    <div class="logo-wrap" title="College Logo">
      <img src="../Images/College_logo.webp" alt="College Logo" class="site-logo" />
    </div>

    <div class="brand-title">
      Welcome, <?= htmlspecialchars((string)$emp_name); ?>!
      <?php
        if (strtoupper(trim((string)$emp_dept)) === 'S&H') {
            echo '<span class="badge"></span>';
        } else {
            if ($admin_dept_raw !== '') {
                echo '<span class="badge"></span>';
            }
        }
      ?>
    </div>
  </div>

  <nav class="nav-menu" role="navigation" aria-label="Primary">
    <a href="./Leave_History.php">📜 History</a>
    <?php if (in_array($access_key, ['a', 's'])): ?>
      <a href="./Leave_Approval.php" class="active">✅ Leave Approval</a>
    <?php endif; ?>
    <a href="./Leave_Balance.php">📅 Leave Balance</a>
    <a href="../Main/Logout.php" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
  </nav>
</header>

<div class="content">
  <div class="table-container">

    <form method="GET" style="margin-bottom: 10px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <input type="text" name="search" placeholder="🔍 Search..." value="<?= htmlspecialchars((string)$search) ?>" class="search-input">
      <button type="submit" class="btn">Search</button>

      <?php
        if ($admin_dept_upper === 'S&H') {
            foreach ($sh_buttons as $btn) {
                $active_class = (strcasecmp($year_param, $btn) === 0) ? 'active' : '';
                echo "<button type=\"submit\" name=\"year\" value=\"" . htmlspecialchars($btn) . "\" class=\"special-btn {$active_class}\">" . htmlspecialchars($btn) . "</button>\n";
            }
        } elseif (in_array($admin_prefix, $prefixes_with_year_buttons, true)) {
            $years = ['II','III','IV'];
            foreach ($years as $y) {
                $active_class = (strcasecmp($year_param, $y) === 0) ? 'active' : '';
                echo "<button type=\"submit\" name=\"year\" value=\"" . htmlspecialchars($y) . "\" class=\"year-filter-btn {$active_class}\">{$y}-Year</button>\n";
            }
        }
      ?>
    </form>

    <div class="action-buttons" style="margin-bottom:12px;">
      <form action="../Main/Export_Leave_Approval.php" method="POST" style="display:inline-block;">
        <?php if (!empty($search)): ?>
          <input type="hidden" name="search" value="<?= htmlspecialchars((string)$search) ?>">
        <?php endif; ?>
        <?php if (!empty($_GET['year'])): ?>
          <input type="hidden" name="year" value="<?= htmlspecialchars((string)($_GET['year'] ?? '')) ?>">
        <?php endif; ?>
        <button type="submit" class="btn">⬇ Export</button>
      </form>
    </div>

    <table class="main-table" role="table" aria-label="Leave approvals">
      <thead>
        <tr>
          <th>Register No</th>
          <th>Name</th>
          <th>Year</th>
          <th>Leave Type</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Created On</th>
          <th>Leaves Taken</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()):
              $req_raw = $row['request_id'] ?? '';
              $req_encoded = urlencode($req_raw);

              $emp_col = $row['emp_id'] ?? '';
              $reg = htmlspecialchars((string)$emp_col);
              $name = htmlspecialchars((string)($row['emp_name'] ?? ''));
              $dept = htmlspecialchars((string)($row['department'] ?? ''));
              $ltype = htmlspecialchars((string)($row['leave_type'] ?? ''));
              $start = !empty($row['start_date']) ? htmlspecialchars(date('d-M-Y', strtotime($row['start_date']))) : '';
              $end = !empty($row['end_date']) ? htmlspecialchars(date('d-M-Y', strtotime($row['end_date']))) : '';
              $reason = nl2br(htmlspecialchars((string)($row['reason'] ?? '')));
              $status_raw = (string)($row['status'] ?? 'Pending');
              $status_norm = strtolower(trim($status_raw));
              $status_class = in_array($status_norm, ['approved','pending','rejected']) ? $status_norm : 'unknown';
              $created = !empty($row['created_on']) ? htmlspecialchars(date('d-M-Y', strtotime($row['created_on']))) : '';

              $today = date('Y-m-d'); 
              $end_date_val = !empty($row['end_date']) ? date('Y-m-d', strtotime($row['end_date'])) : '';
              $clickable = in_array($status_norm, ['pending', 'rejected']) && ($end_date_val === '' || $end_date_val >= $today);

              $leave_taken = (int)($row['leave_days'] ?? 0);
          ?>
            <tr>
              <td><?= $reg ?></td>
              <td><?= $name ?></td>
              <td><?= $dept ?></td>
              <td><?= $ltype ?></td>
              <td><?= $start ?></td>
              <td><?= $end ?></td>
              <td><?= $reason ?></td>
              <td>
                <?php if ($status_norm === 'approved'): ?>
                  <span class="status approved"><?= htmlspecialchars($status_raw) ?></span>
                <?php elseif ($clickable): ?>
                  <a class="status <?= $status_class ?> status-link" href="Leave_Approval_Form.php?request_id=<?= $req_encoded ?>">
                    <?= htmlspecialchars($status_raw) ?>
                  </a>
                <?php else: ?>
                  <span class="status <?= $status_class ?>"><?= htmlspecialchars($status_raw) ?></span>
                <?php endif; ?>
              </td>
              <td><?= $created ?></td>
              <td><?= $leave_taken ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="10" style="text-align:center; padding:18px;">No leave requests found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

  </div>
</div>

</body>
</html>
