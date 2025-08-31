<?php
session_start();
include('../DB_Config/Config.php');

if (empty($_SESSION['emp_id']) || empty($_SESSION['access_key'])) {
    header("Location: login.php");
    exit;
}

$db = new dbconfig();
$conn = $db->getConnection();
$emp_id     = (string)($_SESSION['emp_id'] ?? '');
$emp_name   = (string)($_SESSION['emp_name'] ?? '');
$access_key = (string)($_SESSION['access_key'] ?? '');

 
if (!in_array($access_key, ['a', 's'], true)) {
    
    echo "<h3 style='color:red; text-align:center; margin-top:60px;'>Access Denied</h3>";
    exit;
}

 
$request_id_get  = isset($_GET['request_id']) ? trim($_GET['request_id']) : '';
$request_id_post = isset($_POST['request_id']) ? trim($_POST['request_id']) : '';
 
$request_id = $request_id_post !== '' ? $request_id_post : $request_id_get;

$requestData = [];

 
if ($request_id !== '') {
    $sql = "SELECT * FROM xxhp_elms_leave_req_t WHERE request_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Prepare failed (SELECT): " . $conn->error);
        http_response_code(500);
        die("Database error.");
    }
    $stmt->bind_param("s", $request_id);
    if (!$stmt->execute()) {
        error_log("Execute failed (SELECT): " . $stmt->error);
        http_response_code(500);
        die("Database error.");
    }
    $result = $stmt->get_result();
    $requestData = $result->fetch_assoc() ?: [];

    if (empty($requestData) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "<script>alert('Invalid request ID.'); window.location.href='Leave_Approval.php';</script>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($request_id_post)) {
        echo "<script>alert('Missing request identifier.'); window.location.href='Leave_Approval.php';</script>";
        exit;
    }

    $status = (string)($_POST['status'] ?? 'Pending');
    $allowed = ['Pending', 'Approved', 'Rejected'];

    if (!in_array($status, $allowed, true)) {
        echo "<script>alert('Invalid status selected.'); window.location.href='Leave_Approval.php';</script>";
        exit;
    }

     $rej_reason_raw = isset($_POST['rejection_reason']) ? trim((string)$_POST['rejection_reason']) : '';

     if ($status === 'Rejected' && $rej_reason_raw === '') {
        echo "<script>alert('Please enter a reason for rejection.'); history.back();</script>";
        exit;
    }

 
    if ($status === 'Rejected') {
         $updateSql = "UPDATE xxhp_elms_leave_req_t
                      SET status = ?, rejection_reason = ?, approved_on = CURDATE()
                      WHERE request_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        if ($updateStmt === false) {
            error_log("Prepare failed (UPDATE rejected): " . $conn->error);
            http_response_code(500);
            die("Database error.");
        }
        $updateStmt->bind_param("sss", $status, $rej_reason_raw, $request_id_post);
    } else {
         $updateSql = "UPDATE xxhp_elms_leave_req_t
                      SET status = ?, rejection_reason = NULL, approved_on = CURDATE()
                      WHERE request_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        if ($updateStmt === false) {
            error_log("Prepare failed (UPDATE non-rejected): " . $conn->error);
            http_response_code(500);
            die("Database error.");
        }
        $updateStmt->bind_param("ss", $status, $request_id_post);
    }

    if (!$updateStmt->execute()) {
        error_log("Execute failed (UPDATE): " . $updateStmt->error);
        echo "<script>alert('Failed to submit approval. Please try again.'); window.location.href='Leave_Approval.php';</script>";
        exit;
    }

    echo "<script>alert('Approval submitted successfully.'); window.location.href='Leave_Approval.php';</script>";
    exit;
}


function fmt_date_safe($date) {
    if (empty($date) || $date === '0000-00-00') return '';
    $ts = strtotime($date);
    return $ts ? date('d-M-Y', $ts) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Approval Form</title>
  <link rel="stylesheet" href="../CSS/Style_Leave_App_Form.php">
  <meta name="viewport" content="width=device-width,initial-scale=1">
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
    <a href="./Leave_History.php">📜 History</a>
    <?php if (in_array($access_key, ['a', 's'], true)): ?>
      <a href="./Leave_Approval.php">✅ Leave Approval</a>
    <?php endif; ?>
    <a href="./Leave_Balance.php">📅 Leave Balance</a>
    <a href="../Main/Logout.php" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</a>
  </nav>
</header>

<main class="main-content" role="main" style="padding-top:90px;">
  <div class="content-wrapper" style="max-width:920px; margin:-100px auto;">

    <div class="form-container" style="background:#fff; padding:18px; font-size: 16px; order-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06);">
      <h2 style="margin-top:0;">Leave Request Details</h2>

      <form method="POST" novalidate>
        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">

        <div class="form-row" style="display:flex; gap:40px; flex-wrap:wrap;">
          <p style="flex:1 1 220px;"><label>Request ID:</label><br>
            <input type="text" value="<?= htmlspecialchars($requestData['request_id'] ?? '') ?>" readonly style="width:100%; padding:8px; border-radius:6px;">
          </p>

          <p style="flex:1 1 220px;"><label>Register Number:</label><br>
            <input type="text" value="<?= htmlspecialchars($requestData['emp_id'] ?? '') ?>" readonly style="width:100%; padding:8px; border-radius:6px;">
          </p>
        </div>

        <div class="form-row" style="display:flex; gap:40px; flex-wrap:wrap; margin-top:8px;">
          <p style="flex:1 1 220px;"><label>Leave Type:</label><br>
            <input type="text" value="<?= htmlspecialchars($requestData['leave_type'] ?? '') ?>" readonly style="width:100%; padding:8px; border-radius:6px;">
          </p>

          <p style="flex:1 1 220px;"><label>Start Date:</label><br>
            <input type="text" value="<?= fmt_date_safe($requestData['start_date'] ?? '') ?>" readonly style="width:100%; padding:8px; border-radius:6px;">
          </p>
        </div>

        <div class="form-row" style="display:flex; gap:40px; flex-wrap:wrap; margin-top:8px;">
          <p style="flex:1 1 220px;"><label>End Date:</label><br>
            <input type="text" value="<?= fmt_date_safe($requestData['end_date'] ?? '') ?>" readonly style="width:100%; padding:8px; border-radius:6px;">
          </p>

          <p style="flex:1 1 220px;"><label>Created On:</label><br>
            <input type="text" value="<?= fmt_date_safe($requestData['created_on'] ?? '') ?>" readonly style="width:100%; padding:8px; border-radius:6px;">
          </p>
        </div>

        <div style="margin-top:8px; font-size: 19px;">
          <p><label>Reason:</label><br>
          <textarea readonly style="width:100%; min-height:84px; font-size:18px; padding:8px; border-radius:6px;"><?php echo htmlspecialchars($requestData['reason'] ?? ''); ?></textarea></p>
        </div>

        <div style="margin-top:8px; font-size: 19px;">
          <form method="POST" novalidate>
  <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request_id); ?>">

  <div style="margin-top:8px; font-size:19px;">
    <p>
      <label>Status:</label><br>
      <select name="status" id="status" onchange="toggleReasonBox()" style="padding:8px; border-radius:6px;">
        <?php
          $current = $requestData['status'] ?? 'Pending';
          $states = ['Pending','Approved','Rejected'];
          foreach ($states as $s) {
              $sel = ($current === $s) ? 'selected' : '';
              echo '<option value="'.htmlspecialchars($s).'" '.$sel.'>'.htmlspecialchars($s).'</option>';
          }
        ?>
      </select>
    </p>
  </div>

   <div id="rejection-reason-box" style="margin-top:8px; font-size:19px; display:none;">
    <p>
      <label>Rejection Reason:</label><br>
      <textarea name="rejection_reason" id="rejection_reason"
        style="width:100%; min-height:84px; font-size:18px; padding:8px; border-radius:6px;"><?php
          echo htmlspecialchars($requestData['rejection_reason'] ?? '');
      ?></textarea>
    </p>
  </div>

   
  <div class="actions">
  <button type="submit">Submit</button>
  <button type="button" onclick="window.location.href='Leave_Approval.php'">Back</button>
</div>


</form>

<script>
function toggleReasonBox() {
  var status = document.getElementById("status").value;
  var reasonBox = document.getElementById("rejection-reason-box");
  var reasonTA = document.getElementById("rejection_reason");
  if (status === "Rejected") {
    reasonBox.style.display = "block";
    if (reasonTA) reasonTA.required = true;
  } else {
    reasonBox.style.display = "none";
    if (reasonTA) reasonTA.required = false;
  }
}
document.addEventListener("DOMContentLoaded", toggleReasonBox);
</script>
 </p>
     
    </div>

  </div>
</main>

</body>
</html>
