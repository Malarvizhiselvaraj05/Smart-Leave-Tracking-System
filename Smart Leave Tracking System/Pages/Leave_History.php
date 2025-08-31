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

$db = new dbconfig();
$conn = $db->getConnection();
if (!$conn) {
    http_response_code(500);
    die("Database connection failed.");
}

$today = date('Y-m-d');
$is_admin_view = in_array($access_key, ['a', 's'], true);

$admin_dept_raw = isset($_SESSION['department']) ? trim((string)$_SESSION['department']) : '';
$admin_dept = $admin_dept_raw !== '' ? strtoupper($admin_dept_raw) : '';

$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$year_param = isset($_GET['year']) ? trim((string)$_GET['year']) : '';

/* -------------------------
   Admin-only filters
--------------------------*/
$dept_sql = '';
$year_sql = '';
$admin_prefix = '';

if ($is_admin_view) {
    if ($admin_dept_raw !== '') {
        $dept_safe = $conn->real_escape_string($admin_dept_raw);
        if ($admin_dept === 'S&H') {
            $dept_sql = " AND ( UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$dept_safe}%')) OR UPPER(TRIM(u.department)) LIKE '% I' )";
        } else {
            if (preg_match('/\bI$/i', $admin_dept)) {
                $dept_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$dept_safe}'))";
            } else {
                $dept_sql = " AND ( UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$dept_safe}%')) AND UPPER(TRIM(u.department)) NOT LIKE '% I' )";
            }
        }
    }

    $sh_buttons = ['AD I','IT I','CSE I','CIVIL I','MECH I','ECE I','EEE I'];
    $prefixes_with_year_buttons = ['AD','IT','CSE','ECE','EEE','CIVIL','MECH'];

    if (preg_match('/^([A-Za-z&]+)/', $admin_dept_raw, $m)) {
        $admin_prefix = strtoupper($m[1]);
    }

    if ($year_param !== '') {
        $year_up = strtoupper($year_param);

        if ($admin_dept === 'S&H' && in_array($year_up, array_map('strtoupper', $sh_buttons), true)) {
            $year_safe = $conn->real_escape_string($year_param);
            $year_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$year_safe}'))";
        } else {
            if (in_array($year_up, ['II','III','IV'], true) && in_array($admin_prefix, $prefixes_with_year_buttons, true)) {
                $year_safe = $conn->real_escape_string($year_up);
                $year_sql = " AND UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$admin_prefix} {$year_safe}'))";
            } else {
                if (
                    preg_match('/^[A-Za-z]+ I$/i', $year_param) &&
                    preg_match('/\bI$/i', strtoupper($admin_dept_raw)) &&
                    stripos($admin_dept_raw, explode(' ', $year_param)[0]) === 0
                ) {
                    $year_safe = $conn->real_escape_string($year_param);
                    $year_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$year_safe}'))";
                }
            }
        }
    }
}

/* -------------------------
   leave_days subquery
--------------------------*/
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
                    AND lr.status = 'Approved') AS leave_days";

/* -------------------------
   Queries with proper binding
--------------------------*/
$search_sql = '';
$search_params = [];
$search_types = '';

if ($search !== '') {
    $search_sql = " AND (
        r.request_id LIKE ? OR
        r.emp_id LIKE ? OR
        u.emp_name LIKE ? OR
        r.leave_type LIKE ? OR
        r.reason LIKE ?
    )";
    $like = "%$search%";
    $search_params = [$like, $like, $like, $like, $like];
    $search_types = str_repeat("s", count($search_params));
}

if ($is_admin_view) {
    $sql = "SELECT r.request_id, r.emp_id, u.emp_name, u.department, r.leave_type, r.reason, r.start_date, r.end_date, r.approved_on,
                   $leave_days_sql
            FROM xxhp_elms_leave_req_t r
            JOIN xxhp_elms_emp_det_t u ON r.emp_id = u.emp_id
            WHERE r.status = 'Approved' AND r.end_date < ? $search_sql $dept_sql $year_sql
            ORDER BY r.start_date DESC";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    if ($search_sql !== '') {
        $stmt->bind_param("s".$search_types, $today, ...$search_params);
    } else {
        $stmt->bind_param("s", $today);
    }
} else {
    $sql = "SELECT r.request_id, r.emp_id, u.emp_name, u.department, r.leave_type, r.reason, r.start_date, r.end_date, r.approved_on,
                   $leave_days_sql
            FROM xxhp_elms_leave_req_t r
            JOIN xxhp_elms_emp_det_t u ON r.emp_id = u.emp_id
            WHERE r.emp_id = ? AND r.status = 'Approved' AND r.end_date < ? $search_sql
            ORDER BY r.start_date DESC";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    if ($search_sql !== '') {
        $stmt->bind_param("ss".$search_types, $emp_id, $today, ...$search_params);
    } else {
        $stmt->bind_param("ss", $emp_id, $today);
    }
}

if (!$stmt->execute()) {
    http_response_code(500);
    die("Execute failed: " . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Leave History</title>
  <link rel="stylesheet" href="../CSS/Style_Leave_History.php">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    .table-wrapper { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; min-width:980px; }
    th, td { padding:10px 12px; border-bottom:1px solid #eee; text-align:left; }
    .page-title { margin:10px 0 14px; font-size:20px; color:#0b2540; }
    .search-input { padding:8px 10px; border-radius:6px; border:1px solid #ccc; }
    .btn { padding:8px 10px; border-radius:6px; background: #004687; color:white; border:none; cursor:pointer; }
    .special-btn, .year-filter-btn { padding:6px 10px; border-radius:6px; border:1px solid #ccc; background:#005A9C; color:white; }
  </style>
</head>
<body>
<header class="top-nav" role="banner">
  <div class="header-left" title="Leave Management System">
    <div class="logo-wrap"><img src="../Images/College_logo.webp" alt="College Logo" class="site-logo" /></div>
    <div class="brand-title"> Welcome, <?= htmlspecialchars($emp_name); ?>!</div>
  </div>

  <nav class="nav-menu" role="navigation">
    <?php if ($is_admin_view): ?>
        <a href="./Leave_History.php" class="active">📜 History</a>
        <a href="./Leave_Approval.php">✅ Leave Approval</a>
    <?php else: ?>
        <a href="./Leave_Req_List.php">📄 Leave Requests</a>
        <a href="./Leave_History.php" class="active">📜 History</a>
    <?php endif; ?>
    <a href="./Leave_Balance.php">📅 Leave Balance</a>
    <a href="../Main/Logout.php" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
  </nav>
</header>

<main class="content" style="padding:18px;">
  <h2 class="page-title">Leave History</h2>
  <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:15px;">
    <form method="GET" style="margin-bottom:15px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <input type="text" name="search" placeholder="🔍 Search..." value="<?= htmlspecialchars($search) ?>" class="search-input">
      <button type="submit" class="btn">Search</button>

      <?php if ($is_admin_view): ?>
        <?php
          if ($admin_dept === 'S&H') {
              foreach ($sh_buttons as $btn) {
                  $active = (strcasecmp($year_param, $btn) === 0) ? 'active' : '';
                  echo '<button type="submit" name="year" value="' . htmlspecialchars($btn) . '" class="special-btn ' . $active . '">' . htmlspecialchars($btn) . '</button>';
              }
          } elseif (in_array($admin_prefix, $prefixes_with_year_buttons, true)) {
              foreach (['II','III','IV'] as $y) {
                  $active = (strcasecmp($year_param, $y) === 0) ? 'active' : '';
                  echo '<button type="submit" name="year" value="' . htmlspecialchars($y) . '" class="year-filter-btn ' . $active . '">' . $y . '-Year</button>';
              }
          }
        ?>
      <?php endif; ?>
    </form>

    <?php if ($access_key !== 'e'): ?>
    <form method="POST" action="Export_Leave_History.php" style="margin-bottom:15px; margin-right:150px;">
        <button type="submit" name="export" class="btn">⬇️ Export</button>
    </form>
    <?php endif; ?>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <?php if ($is_admin_view): ?><th>Register Number</th><?php endif; ?>
          <th>Name</th>
          <th>Year</th>
          <th>Leave Type</th>
          <th>Reason</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Approved On</th>
          <th>Leaves Taken</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
              $emp_col = htmlspecialchars((string)($row['emp_id'] ?? ''));
              $emp_name_row = htmlspecialchars((string)($row['emp_name'] ?? ''));
              $year = htmlspecialchars((string)($row['department'] ?? ''));
              $leave_type = htmlspecialchars((string)($row['leave_type'] ?? ''));
              $reason = nl2br(htmlspecialchars((string)($row['reason'] ?? '')));
              $start = !empty($row['start_date']) ? htmlspecialchars(date('d-M-Y', strtotime($row['start_date']))) : '';
              $end = !empty($row['end_date']) ? htmlspecialchars(date('d-M-Y', strtotime($row['end_date']))) : '';
              $approved_on = !empty($row['approved_on']) ? htmlspecialchars(date('d-M-Y', strtotime($row['approved_on']))) : '';
              $leaves_taken = (int)($row['leave_days'] ?? 0);
        ?>
            <tr>
              <?php if ($is_admin_view): ?><td><?php echo $emp_col; ?></td><?php endif; ?>
              <td><?php echo $emp_name_row; ?></td>
              <td><?php echo $year; ?></td>
              <td><?php echo $leave_type; ?></td>
              <td style="max-width:320px;"><?php echo $reason; ?></td>
              <td><?php echo $start; ?></td>
              <td><?php echo $end; ?></td>
              <td><?php echo $approved_on; ?></td>
              <td><?php echo $leaves_taken; ?></td>
            </tr>
        <?php
            endwhile;
          else:
        ?>
          <tr>
            <td colspan="<?php echo $is_admin_view ? 9 : 8; ?>" style="text-align:center; padding:18px;">
              No past approved leave records found.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
