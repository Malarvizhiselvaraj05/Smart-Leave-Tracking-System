<?php
session_start();
include('../DB_Config/Config.php');

if (!isset($_SESSION['emp_id']) || !isset($_SESSION['access_key'])) {
    header("Location: Login.php");
    exit;
}

$emp_id     = $_SESSION['emp_id'];
$emp_name   = $_SESSION['emp_name'] ?? '';
$access_key = $_SESSION['access_key'];
$emp_dept   = $_SESSION['department'] ?? '';

if (!in_array($access_key, ['a', 's'])) {
    die("Access Denied");
}

$db = new dbconfig();
$conn = $db->getConnection();

// Filters
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$year_param = isset($_POST['year']) ? trim($_POST['year']) : '';

$search_sql = "";
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $search_sql = " AND (
        r.request_id LIKE '%{$s}%' OR
        r.emp_id LIKE '%{$s}%' OR
        u.emp_name LIKE '%{$s}%' OR
        r.leave_type LIKE '%{$s}%'
    )";
}

// Department visibility
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

// Year filter
$sh_buttons = ['AD I','IT I','CSE I','CIVIL I','MECH I','ECE I','EEE I'];
$prefixes_with_year_buttons = ['AD','IT','CSE','ECE','EEE','CIVIL','MECH'];

$year_sql = "";
$admin_prefix = '';
if (preg_match('/^([A-Za-z&]+)/', $admin_dept_raw, $m)) {
    $admin_prefix = strtoupper($m[1]);
}

if ($year_param !== '') {
    $year_up = strtoupper($year_param);

    if ($admin_dept_upper === 'S&H' && in_array($year_up, array_map('strtoupper', $sh_buttons), true)) {
        $ys = $conn->real_escape_string($year_param);
        $year_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$ys}'))";
    } else if (in_array($year_up, ["II","III","IV"], true) && in_array($admin_prefix, $prefixes_with_year_buttons, true)) {
        $ys = $conn->real_escape_string($year_param);
        $year_sql = " AND UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$admin_prefix} {$ys}'))";
    } else if (preg_match('/^[A-Za-z]+ I$/i', $year_param) && preg_match('/\bI$/i', $admin_dept_upper) && stripos($admin_dept_upper, strtoupper(explode(' ', $year_param)[0])) === 0) {
        $ys = $conn->real_escape_string($year_param);
        $year_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$ys}'))";
    }
}

// Access logic
if ($access_key == 'a') {
    $access_where = "u.access_key = 'e'";
} else {
    $emp_safe = $conn->real_escape_string($emp_id);
    $access_where = "(u.access_key IN ('a','s') OR r.emp_id = '{$emp_safe}')";
}

// Only past approved leaves
$sql = "SELECT r.*, u.emp_name, u.department,
               COALESCE(c.count_approved, 0) AS leaves_taken_total
        FROM xxhp_elms_leave_req_t r
        JOIN xxhp_elms_emp_det_t u ON r.emp_id = u.emp_id
        LEFT JOIN (
            SELECT emp_id, COUNT(*) AS count_approved
            FROM xxhp_elms_leave_req_t
            WHERE status='Approved'
            GROUP BY emp_id
        ) c ON c.emp_id = r.emp_id
        WHERE {$access_where}
          AND r.status='Approved'
          AND r.end_date < CURDATE()
          {$dept_sql} {$search_sql} {$year_sql}
        ORDER BY r.start_date DESC";

$result = $conn->query($sql);

// Build table
$table_rows = '';
$sno = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emp_name_row = htmlspecialchars($row['emp_name']);
        $emp_id_row   = htmlspecialchars($row['emp_id']);
        $leave_type   = htmlspecialchars($row['leave_type']);
        $reason       = nl2br(htmlspecialchars($row['reason'] ?? ''));
        $start_date   = $row['start_date'] ? date('d-M-Y', strtotime($row['start_date'])) : '';
        $end_date     = $row['end_date'] ? date('d-M-Y', strtotime($row['end_date'])) : '';

        // inclusive days
        $leave_taken = '';
        if ($row['start_date'] && $row['end_date']) {
            $sd = strtotime($row['start_date']);
            $ed = strtotime($row['end_date']);
            if ($ed >= $sd) {
                $days = floor(($ed - $sd)/86400) + 1;
                $leave_taken = $days . ' day' . ($days>1?'s':'');
            }
        }

        $total_approved = (int)($row['leaves_taken_total'] ?? 0);

        $table_rows .= "<tr>
            <td>{$sno}</td>
            <td>{$emp_name_row}</td>
            <td>{$emp_id_row}</td>
            <td>{$leave_type}</td>
            <td>{$reason}</td>
            <td>{$start_date}</td>
            <td>{$end_date}</td>
            <td>{$leave_taken}</td>
            <td>{$total_approved}</td>
        </tr>";
        $sno++;
    }
} else {
    $table_rows = '<tr><td colspan="9" style="text-align:center;padding:15px;">No leave history found.</td></tr>';
}

$filters = [];
if ($search !== '') $filters[] = "Search: " . htmlspecialchars($search);
if ($year_param !== '') $filters[] = "Year/Section: " . htmlspecialchars($year_param);
if ($admin_dept_raw !== '') $filters[] = "Department: " . htmlspecialchars($admin_dept_raw);
$filters_summary = $filters ? implode(" | ", $filters) : "All records";

// Header logo
$header_img_file = __DIR__ . '/../Images/header.png';
$header_img_src = '';
if (file_exists($header_img_file)) {
    $mime = mime_content_type($header_img_file);
    $base64 = base64_encode(file_get_contents($header_img_file));
    $header_img_src = "data:$mime;base64,$base64";
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Leave History Report</title>
<style>
@page { size: A4 landscape; margin: 10mm; }
body { font-family: Arial, sans-serif; }
.btn { background:#004687;color:#fff;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;margin:4px;}
.no-print {margin-bottom:10px;}
table {border-collapse:collapse;width:100%;}
th,td {border:1px solid #444;padding:6px;font-size:9pt;}
th {background:#eee;}
</style>
</head>
<body>
<div class="no-print">
  <button class="btn" onclick="history.back()">← Back</button>
  <button class="btn" onclick="print()">Print / Save PDF</button>
</div>

<div style="text-align:center">
  <?php if($header_img_src): ?>
    <img src="<?= $header_img_src ?>" style="max-height:100px;">
  <?php else: ?>
    <h2>Nadar Saraswathi College of Engineering & Technology</h2>
  <?php endif; ?>
  <h3>Leave History Report</h3>
  <div style="font-size:10pt;color:#555;"><?= $filters_summary ?></div>
</div>

<table>
<thead>
<tr>
  <th>SNo</th>
  <th>Employee Name</th>
  <th>Employee ID</th>
  <th>Leave Type</th>
  <th>Reason</th>
  <th>Start Date</th>
  <th>End Date</th>
  <th>Leave Taken</th>
  <th>Total Approved</th>
</tr>
</thead>
<tbody>
<?= $table_rows ?>
</tbody>
</table>

<div style="text-align:right;font-size:9pt;margin-top:8px;">
  Generated by: <?= htmlspecialchars($emp_name) ?> | <?= date('d-M-Y H:i') ?>
</div>
<style>
@page { size: A4 landscape; margin: 10mm; }
body { font-family: Arial, sans-serif; }
.btn { background:#004687;color:#fff;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;margin:4px;}
.no-print {margin-bottom:10px;}

table {border-collapse:collapse;width:100%;}
th,td {border:1px solid #444;padding:6px;font-size:9pt;}
th {background:#eee;}

/* HIDE buttons and other "no-print" things when exporting */
@media print {
  .no-print {
    display: none !important;
  }
}
</style>

</body>
</html>
