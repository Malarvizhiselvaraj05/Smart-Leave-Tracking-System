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

$today = date('Y-m-d');

// Fetch approved leave records that ended before today
$sql = "SELECT request_id, leave_type, start_date, end_date, reason
        FROM xxhp_elms_leave_req_t
        WHERE emp_id = ?
          AND status = 'Approved'
          AND end_date < ?
        ORDER BY start_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $emp_id, $today);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $title = $row['leave_type'] . " - " . $row['reason'];
    $start = $row['start_date'];
    $end = date('Y-m-d', strtotime($row['end_date'] . ' +1 day')); // Make end inclusive

    $events[] = [
        'title' => $title,
        'start' => $start,
        'end'   => $end
    ];
}

header('Content-Type: application/json');
echo json_encode($events);
?>