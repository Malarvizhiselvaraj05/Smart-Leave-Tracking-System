<?php
session_start();
include('../DB_Config/Config.php');

if (!isset($_SESSION['emp_id'])) {
    header("Location: login.php");
    exit;
}

$db = new dbconfig();
$conn = $db->getConnection();

$logged_in_emp_id   = $_SESSION['emp_id'];
$logged_in_emp_name = $_SESSION['emp_name'] ?? 'Employee';
$access_key         = $_SESSION['access_key'] ?? 'e';
$admin_dept_raw     = isset($_SESSION['department']) ? trim((string)$_SESSION['department']) : '';
$admin_dept         = $admin_dept_raw !== '' ? strtoupper($admin_dept_raw) : '';

$is_admin = in_array($access_key, ['a', 's'], true);

$requested_emp_id = isset($_GET['emp_id']) ? trim((string)$_GET['emp_id']) : '';

$view_emp_id   = $logged_in_emp_id;
$view_emp_name = $logged_in_emp_name;
$show_balance  = true;
$admin_notice  = '';

/** Helper: can admin view this student's department? */
function can_admin_view_student(string $admin_dept, string $student_dept): bool {
    $admin_dept   = strtoupper(trim($admin_dept));
    $student_dept = strtoupper(trim($student_dept));

    // Allowed prefixes for engineering depts
    $prefixes = ['AD','IT','CSE','EEE','ECE','MECH','CIVIL'];

    // S&H admins: can view FIRST YEAR across all allowed prefixes (e.g., AD I, IT I, ...)
    if (strpos($admin_dept, 'S&H') === 0) {
        foreach ($prefixes as $p) {
            if (preg_match('/^' . preg_quote($p, '/') . '\s+I$/', $student_dept)) {
                return true;
            }
        }
        return false;
    }

    // Non S&H: lock to same prefix, and ONLY II/III/IV
    if (preg_match('/^([A-Z&]+)/', $admin_dept, $m)) {
        $admin_prefix = $m[1];
        if (!in_array($admin_prefix, $prefixes, true)) {
            return false;
        }
        return (bool) preg_match('/^' . preg_quote($admin_prefix, '/') . '\s+(II|III|IV)$/', $student_dept);
    }

    return false;
}

if ($is_admin) {
    if ($requested_emp_id === '') {
        $show_balance = false;
        $admin_notice = "As an administrator you may view students' leave balances. Please enter a student register number to view their balance.";
    } else {
        if ($requested_emp_id === $logged_in_emp_id) {
            // Admin looking up self — disable balance panel (keeps prior behavior)
            $show_balance = false;
            $admin_notice = " ";
        } else {
            // Fetch the requested user's name, department, and access_key
            $view_emp_id = $requested_emp_id;
            $stmtName = $conn->prepare("SELECT emp_name, department, access_key FROM xxhp_elms_emp_det_t WHERE emp_id = ?");
            if ($stmtName) {
                $stmtName->bind_param("s", $view_emp_id);
                $stmtName->execute();
                $resultName = $stmtName->get_result();
                if ($rowName = $resultName->fetch_assoc()) {
                    $view_emp_name = $rowName['emp_name'] ?? '(Unknown Student)';
                    $student_dept  = (string)($rowName['department'] ?? '');
                    $student_acc   = strtolower((string)($rowName['access_key'] ?? ''));

                    // Must be a student (access_key = 'e')
                    if ($student_acc !== 'e') {
                        $show_balance = false;
                        $admin_notice = "❌ You can only view student leave balances.";
                    }
                    // Department scope check
                    elseif (!can_admin_view_student($admin_dept, $student_dept)) {
                        $show_balance = false;
                        $admin_notice = "❌ You don't have permission to view this student's leave balance (different department/year scope).";
                    }
                } else {
                    // Not found
                    $view_emp_name = "(Unknown Student)";
                    $show_balance  = false;
                    $admin_notice  = "❌ There is no student with this register number.";
                }
            } else {
                $view_emp_name = "(Unknown Student)";
                $show_balance  = false;
                $admin_notice  = "❌ Database error while searching for student.";
            }
        }
    }
} else {
    // Non-admins can only see their own balance
    $view_emp_id   = $logged_in_emp_id;
    $view_emp_name = $logged_in_emp_name;
    $show_balance  = true;
}

$defaultLeaveQuota = [
    'casual' => 12,
    'sick'   => 10,
    'others' => 5
];

$leaveData = [];
foreach ($defaultLeaveQuota as $type => $quota) {
    $leaveData[$type] = [
        'leave_type'       => ucfirst($type === 'others' ? 'other' : $type),
        'total_leaves'     => $quota,
        'leaves_taken'     => 0,
        'remaining_leaves' => $quota
    ];
}

if ($show_balance) {
    $sql = "SELECT leave_type, start_date, end_date 
            FROM xxhp_elms_leave_req_t 
            WHERE emp_id = ? AND status = 'approved'";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $view_emp_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $type = strtolower((string)$row['leave_type']);
            if ($type === 'other' || $type === 'others') $type = 'others';

            if (!isset($leaveData[$type])) {
                $leaveData[$type] = [
                    'leave_type'       => ucfirst($type === 'others' ? 'other' : $type),
                    'total_leaves'     => $defaultLeaveQuota['others'],
                    'leaves_taken'     => 0,
                    'remaining_leaves' => $defaultLeaveQuota['others']
                ];
            }

            $start = new DateTime($row['start_date']);
            $end   = new DateTime($row['end_date']);
            $end->modify('+1 day'); // inclusive
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end);

            $days = 0;
            foreach ($period as $dt) {
                if ($dt->format('w') != 0) { // exclude Sundays
                    $days++;
                }
            }

            $leaveData[$type]['leaves_taken'] += $days;
            $leaveData[$type]['remaining_leaves'] =
                $leaveData[$type]['total_leaves'] - $leaveData[$type]['leaves_taken'];
        }
    } else {
        $show_balance = false;
        $admin_notice = "❌ Could not fetch leave data (database error).";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Leave Balance</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="../CSS/Style_Leave_Balance.php">
    <style>
      

    </style>
</head>
<body>

<?php
$currentFile = basename($_SERVER['PHP_SELF']);
function nav_class($file) {
    global $currentFile;
    return ($currentFile === $file) ? 'active' : '';
}
?>

<header class="top-nav" role="banner">
    <div class="header-left" title="Leave Management System">
<div class="logo-wrap" title="College Logo">
  <img src="../Images/College_logo.webp" alt="College Logo" class="site-logo" />
</div>
    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($logged_in_emp_name); ?>!</span>
  </div>

  <nav class="nav-menu" role="navigation" aria-label="Primary">
    <?php if (! $is_admin): ?>
      <a class="<?php echo nav_class('Leave_Req_List.php'); ?>" href="./Leave_Req_List.php">📄 Leave Requests</a>
    <?php endif; ?>

    <a class="<?php echo nav_class('Leave_History.php'); ?>" href="./Leave_History.php">📜 History</a>

    <?php if ($is_admin): ?>
      <a class="<?php echo nav_class('Leave_Approval.php'); ?>" href="./Leave_Approval.php">✅ Leave Approval</a>
    <?php endif; ?>
      <a href="./Leave_Balance.php">📅 Leave Balance</a>
    <a href="../Main/Logout.php" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
  </nav>
</header>

<main class="main-content" role="main">
  <div class="content-wrapper">

    <?php if ($is_admin): ?>
      <form class="search-form" method="get" aria-label="Search Student leave balance">
        <label for="emp_id">🔍 View leave balance for Register Number:</label>
        <div class="search-row">
          <input type="text" name="emp_id" id="emp_id" placeholder="Enter Register Number"
                 value="<?php echo htmlspecialchars($requested_emp_id ?? ''); ?>">
          <button type="submit" class="btn-primary">Search</button>
          <button type="button" class="btn-primary" onclick="document.getElementById('emp_id').value=''; this.closest('form').submit();">Reset</button>
        </div>
        <small style="display:block; margin-top:6px; color:#666;">
          Enter student's ID to view their balance.
        </small>
      </form>
    <?php endif; ?>

    <?php if (!empty($admin_notice)): ?>
      <div class="notice"><?php echo htmlspecialchars($admin_notice); ?></div>
    <?php endif; ?>

    <div class="page-header">
      <p class="sub">
        <?php if ($is_admin && !$show_balance): ?>
        <?php else: ?>
          For <strong><?php echo htmlspecialchars($view_emp_name); ?></strong>
          (ID: <strong><?php echo htmlspecialchars($view_emp_id); ?></strong>)
        <?php endif; ?>
      </p>
    </div>

    <?php if ($show_balance): ?>
      <div class="actions-row">
        <button class="btn" type="button" onclick="location.href='Leave_Req_List.php'">🔙 Back</button>
      </div>

      <div class="table-wrap">
        <table class="balance-table" role="table" aria-label="Leave balance table">
          <thead>
            <tr>
              <th>Leave Type</th>
              <th>Total Leaves</th>
              <th>Leaves Taken</th>
              <th>Remaining Leaves</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($leaveData as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                <td><?php echo htmlspecialchars($row['total_leaves']); ?></td>
                <td><?php echo htmlspecialchars($row['leaves_taken']); ?></td>
                <td><?php echo htmlspecialchars(max(0, $row['remaining_leaves'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <?php
          $totalLeaves = $leavesTaken = $leavesRemaining = 0;
          foreach ($leaveData as $row) {
              $totalLeaves     += (int)$row['total_leaves'];
              $leavesTaken     += (int)$row['leaves_taken'];
              $leavesRemaining += (int)$row['remaining_leaves'];
          }
          ?>
          <tfoot>
            <tr class="total-summary-row">
              <td style="text-align: right; font-weight: bold;">Total:</td>
              <td><strong><?php echo $totalLeaves; ?></strong></td>
              <td><strong><?php echo $leavesTaken; ?></strong></td>
              <td><strong><?php echo max(0, $leavesRemaining); ?></strong></td>
            </tr>
          </tfoot>
        </table>
      </div>
    <?php endif; ?>

  </div>
</main>

<script>
  (function(){
    const input = document.getElementById('emp_id');
    if (!input) return;
    input.addEventListener('keydown', function(e){
      if (e.key === 'Enter') {
        e.preventDefault();
        this.form.submit();
      }
    });
  })();
</script>

</body>
</html>
