<?php
session_start();
include('../DB_Config/Config.php');


if (!isset($_SESSION['emp_id'])) {
    header("Location: login.php");
    exit;
}

$db = new dbconfig();
$conn = $db->getConnection();

$emp_id     = $_SESSION['emp_id'];
$emp_name   = $_SESSION['emp_name'];
$access_key = $_SESSION['access_key'];
$defaultLeaveQuota = [
    'casual' => 12,
    'sick'   => 10,
    'other'  => 5,
    'od'     => 5    
];

$usedLeaves = [];
foreach ($defaultLeaveQuota as $type => $quota) {
    $usedLeaves[$type] = 0;
}

$sqlLeaveUsed = "SELECT leave_type, start_date, end_date 
                 FROM xxhp_elms_leave_req_t 
                 WHERE emp_id = ? AND status = 'approved'";
$stmt = $conn->prepare($sqlLeaveUsed);
$stmt->bind_param("s", $emp_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $type = strtolower($row['leave_type']);
    if (!isset($defaultLeaveQuota[$type])) continue;

    $start = new DateTime($row['start_date']);
    $end   = new DateTime($row['end_date']);
    $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {
        if ($dt->format('w') != 0) { 
            $usedLeaves[$type]++;
        }
    }
}

$errorMessage = '';
$today_date = date('d-M-Y');

$sql = "SELECT MAX(request_id) AS max_request FROM xxhp_elms_leave_req_t";
$result = $conn->query($sql);
$nextNum = 1;

if ($result && $row = $result->fetch_assoc()) {
    if (!empty($row['max_request'])) {
        $num = intval(substr($row['max_request'], 4));
        $nextNum = $num + 1;
    }
}

$new_request_id = "REQ-" . str_pad($nextNum, 3, "0", STR_PAD_LEFT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $leave_type = $_POST['leave_type'] ?? '';

    if (empty($start_date) || empty($end_date) || empty($reason) || empty($leave_type)) {
        $errorMessage = "❌ All fields are required.";
    } else {
        $startObj = DateTime::createFromFormat('d-M-Y', $start_date);
        $endObj = DateTime::createFromFormat('d-M-Y', $end_date);

        if (!$startObj || !$endObj) {
            $errorMessage = "❌ Invalid date format.";
        } else {
            $startForDB = $startObj->format('Y-m-d');
            $endForDB = $endObj->format('Y-m-d');

            $stmt = $conn->prepare("
                INSERT INTO xxhp_elms_leave_req_t 
                (request_id, emp_id, start_date, end_date, reason, status, created_on, leave_type)
                VALUES (?, ?, ?, ?, ?, 'pending', CURRENT_DATE, ?)
            ");
            $stmt->bind_param("ssssss", $new_request_id, $emp_id, $startForDB, $endForDB, $reason, $leave_type);

            if ($stmt->execute()) {
                $_SESSION['successMessage'] = "✅ Leave request submitted successfully! Your request ID is: <strong>$new_request_id</strong>";
                header("Location: Leave_Req_List.php");
                exit;
            } else {
                $errorMessage = "❌ Failed to submit leave request.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Request Leave</title>
<link rel="stylesheet" href="../CSS/Style_Leave_Req.php">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
    <a href="./Leave_Req_List.php">📄 Leave Requests</a>
    <a href="./Leave_History.php">📜 History</a>
    <?php if (in_array($access_key, ['a', 's'])): ?>
      <a href="./Leave_Approval.php">✅ Leave Approval</a>
    <?php endif; ?>
    <a href="./Leave_Balance.php">📅 Leave Balance</a>
    <a href="../Main/Logout.php" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
  </nav>
</header>

<main class="main-content" role="main">
  <div class="content-wrapper">

    <div class="form-container">

    <?php if (!empty($errorMessage)): ?>
      <div class="error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid" novalidate>
      <div class="form-group">
        <label>Request ID</label>
        <input type="text" name="request_id" value="<?php echo htmlspecialchars($new_request_id); ?>" readonly>
      </div>

      <div class="form-group">
        <label>Register Number</label>
        <input type="text" name="emp_id" value="<?php echo htmlspecialchars($emp_id); ?>" readonly>
      </div>

      <div class="form-group">
        <label>Start Date</label>
        <input type="text" id="start_date" name="start_date" required value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>">
      </div>

      <div class="form-group">
        <label>End Date</label>
        <input type="text" id="end_date" name="end_date" required value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>">
      </div>

      <div class="form-group">
        <label>Leave Type</label>
        <select name="leave_type" required>
          <option value="">-- Select Leave Type --</option>

          <option value="sick" <?php 
            if (isset($_POST['leave_type']) && $_POST['leave_type']=='sick') echo 'selected';
            if ($usedLeaves['sick'] >= $defaultLeaveQuota['sick']) echo ' disabled';
          ?>>Sick Leave <?php if($usedLeaves['sick']>0) ; ?></option>

          <option value="casual" <?php 
            if (isset($_POST['leave_type']) && $_POST['leave_type']=='casual') echo 'selected';
            if ($usedLeaves['casual'] >= $defaultLeaveQuota['casual']) echo ' disabled';
          ?>>Casual Leave <?php if($usedLeaves['casual']>0) ; ?></option>

          

          <option value="od" <?php 
            if (isset($_POST['leave_type']) && $_POST['leave_type']=='od') echo 'selected';
            if ($usedLeaves['od'] >= $defaultLeaveQuota['od']) echo ' disabled';
          ?>>OD (On-Duty) <?php if($usedLeaves['od']>0) ; ?></option>

          <option value="other" <?php 
            if (isset($_POST['leave_type']) && $_POST['leave_type']=='other') echo 'selected';
            if ($usedLeaves['other'] >= $defaultLeaveQuota['other']) echo ' disabled';
          ?>>Other <?php if($usedLeaves['other']>0) ; ?></option>

        </select>
      </div>

      <div class="form-group">
        <label>Created On</label>
        <input type="text" id="created_on" name="created_on" readonly value="<?php echo $today_date; ?>">
      </div>

      <div class="form-group full-width">
        <label>Reason</label>
        <textarea name="reason" rows="3" required><?php echo isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : ''; ?></textarea>
      </div>

      <div class="btn-group full-width">
        <button class="btn" type="submit">Submit Request</button>
        <button class="btn" type="button" onclick="location.href='Leave_Req_List.php'">Back</button>
      </div>
    </form>

    </div>

  </div>
</main>

<script>


(function(){
    
    const now = new Date();
    const cutoff = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 9, 00, 0);
    const baseMin = (now >= cutoff)
        ? new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1) 
        : new Date(now.getFullYear(), now.getMonth(), now.getDate());    

    let endPicker; 

    let startPicker = flatpickr("#start_date", {
        dateFormat: "d-M-Y",
        altInput: false,
        allowInput: true,
        minDate: baseMin,
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                const chosen = selectedDates[0];
                const minForEnd = (chosen > baseMin) ? chosen : baseMin;
                endPicker.set('minDate', minForEnd);
            } else {
                endPicker.set('minDate', baseMin);
            }
        }
    });

    endPicker = flatpickr("#end_date", {
        dateFormat: "d-M-Y",
        altInput: false,
        allowInput: true,
        minDate: baseMin
    });

})();
</script>

</body>
</html>
