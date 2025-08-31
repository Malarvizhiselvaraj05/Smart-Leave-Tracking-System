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

// Accept POST filters
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$year_param = isset($_POST['year']) ? trim($_POST['year']) : '';

// --- Build search SQL (same fields as page) ---
$search_sql = "";
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $search_sql = " AND (
        r.request_id LIKE '%{$s}%' OR
        r.emp_id LIKE '%{$s}%' OR
        u.emp_name LIKE '%{$s}%' OR
        r.leave_type LIKE '%{$s}%' OR
        r.status LIKE '%{$s}%'
    )";
}

// --- Department visibility rules (same logic as page) ---
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

// --- Year / section filter handling (mirror page logic) ---
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
        // match e.g. "AD II"
        $year_sql = " AND UPPER(TRIM(u.department)) LIKE UPPER(TRIM('{$admin_prefix} {$ys}'))";
    } else if (preg_match('/^[A-Za-z]+ I$/i', $year_param) && preg_match('/\bI$/i', $admin_dept_upper) && stripos($admin_dept_upper, strtoupper(explode(' ', $year_param)[0])) === 0) {
        $ys = $conn->real_escape_string($year_param);
        $year_sql = " AND UPPER(TRIM(u.department)) = UPPER(TRIM('{$ys}'))";
    } else {
        $year_sql = "";
    }
}

/* -------------------------
   Build SQL (matching your page access rules),
   but include:
   - active-request filter (match Approval page): AND (r.end_date >= CURDATE() OR r.end_date IS NULL)
   - leaves_taken column via LEFT JOIN aggregate for performance
--------------------------*/

$active_filter = " AND (r.end_date >= CURDATE() OR r.end_date IS NULL) ";

// Build access clause: mirror Approval page logic for 'a' and 's'
if ($access_key == 'a') {
    // admin: show employee (u.access_key = 'e') rows
    $access_where = "u.access_key = 'e'";
} else { // 's'
    $emp_safe = $conn->real_escape_string($emp_id);
    // supervisor: show (u.access_key IN ('a','s') OR r.emp_id = current emp_id)
    $access_where = "(u.access_key IN ('a', 's') OR r.emp_id = '{$emp_safe}')";
}

// Main SQL with LEFT JOIN aggregate for leaves_taken
$sql = "SELECT r.*, u.emp_name, u.department,
               COALESCE(c.count_approved, 0) AS leaves_taken
        FROM xxhp_elms_leave_req_t r
        JOIN xxhp_elms_emp_det_t u ON r.emp_id = u.emp_id
        LEFT JOIN (
            SELECT emp_id, COUNT(*) AS count_approved
            FROM xxhp_elms_leave_req_t
            WHERE status = 'Approved'
            GROUP BY emp_id
        ) c ON c.emp_id = r.emp_id
        WHERE {$access_where}
          {$active_filter}
          {$dept_sql} {$search_sql} {$year_sql}
        ORDER BY r.request_id ASC";

$result = $conn->query($sql);

// --- Build table HTML (safe escaping) ---
$table_rows = '';
$sno = 1;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $emp_name_row = htmlspecialchars($row['emp_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $emp_id_row   = htmlspecialchars($row['emp_id'] ?? '', ENT_QUOTES, 'UTF-8');
        $leave_type   = htmlspecialchars($row['leave_type'] ?? '', ENT_QUOTES, 'UTF-8');
        $reason_raw   = $row['reason'] ?? '';
        $reason       = nl2br(htmlspecialchars($reason_raw, ENT_QUOTES, 'UTF-8'));
        $start_date   = (!empty($row['start_date']) && $row['start_date'] !== '0000-00-00') ? date('d-M-Y', strtotime($row['start_date'])) : '';
        $end_date     = (!empty($row['end_date']) && $row['end_date'] !== '0000-00-00') ? date('d-M-Y', strtotime($row['end_date'])) : '';
        $status       = htmlspecialchars($row['status'] ?: 'Pending', ENT_QUOTES, 'UTF-8');

        // compute inclusive leave days (guard against invalid dates)
        $leave_taken = '';
        if (!empty($row['start_date']) && !empty($row['end_date'])) {
            $sd = strtotime($row['start_date']);
            $ed = strtotime($row['end_date']);
            if ($sd && $ed && $ed >= $sd) {
                $diffDays = floor(($ed - $sd) / 86400) + 1;
                $leave_taken = $diffDays . ' day' . ($diffDays > 1 ? 's' : '');
            }
        }

        // total approved leaves for this employee (from joined column)
        $total_approved_for_emp = isset($row['leaves_taken']) ? (int)$row['leaves_taken'] : 0;

        $table_rows .= '<tr>';
        $table_rows .= '<td class="col-sno" align="center">' . $sno . '</td>';
        $table_rows .= '<td class="col-name">' . $emp_name_row . '</td>';
        $table_rows .= '<td class="col-empid" align="center">' . $emp_id_row . '</td>';
        $table_rows .= '<td class="col-type" align="center">' . $leave_type . '</td>';
        $table_rows .= '<td class="col-reason">' . $reason . '</td>';
        $table_rows .= '<td class="col-start" align="center">' . $start_date . '</td>';
        $table_rows .= '<td class="col-end" align="center">' . $end_date . '</td>';
        $table_rows .= '<td class="col-status" align="center">' . $status . '</td>';
        $table_rows .= '<td class="col-taken" align="center">' . $leave_taken . '</td>';
        // show the total approved leaves count in a new column if you want:
        $table_rows .= '<td class="col-total-approved" align="center">' . $total_approved_for_emp . '</td>';
        $table_rows .= '</tr>';

        $sno++;
    }
} else {
    $table_rows = '<tr><td colspan="10" style="text-align:center; padding:18px;">No records found.</td></tr>';
}

// --- Prepare small summary of active filters to show in header ---
$filters = [];
if ($search !== '') $filters[] = 'Search: ' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
if ($year_param !== '') $filters[] = 'Year/Section: ' . htmlspecialchars($year_param, ENT_QUOTES, 'UTF-8');
if ($admin_dept_raw !== '') $filters[] = 'Department: ' . htmlspecialchars($admin_dept_raw, ENT_QUOTES, 'UTF-8');
$filters_summary = $filters ? implode(' | ', $filters) : 'All records';

// ========== Header image embedding ==========
$header_img_file = __DIR__ . '/../Images/header.png'; // change if you use another path
$header_img_src = '';
if (file_exists($header_img_file) && is_readable($header_img_file)) {
    $img_data = file_get_contents($header_img_file);
    $mime = 'image/png';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected = finfo_file($finfo, $header_img_file);
        finfo_close($finfo);
        if (!empty($detected)) $mime = $detected;
    }
    $base64 = base64_encode($img_data);
    $header_img_src = 'data:' . $mime . ';base64,' . $base64;
}
// ========== end header image embedding ==========

// Force print orientation to landscape A4
$page_css_orientation = 'A4 landscape';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Leave Approvals Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    /* Force A4 landscape for print */
    @page { size: <?= $page_css_orientation ?>; margin: 10mm; }
    @media print {
      body { -webkit-print-color-adjust: exact; }
      .no-print { display: none !important; }
    }

    body { font-family: Arial, Helvetica, sans-serif; color:#111; margin: 0; padding: 10px; }
    .no-print .controls { margin-bottom: 10px; }
    .btn {
  background: #004687;
  color: white;
  border: none;
  padding: 10px 18px;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);}

    .header { display:flex; flex-direction:column; align-items:center; gap:6px; margin-bottom: 8px; }
    .header .banner { max-width: 100%; height: auto; max-height: 120px; object-fit: contain; }
    .header .title { font-size:16pt; font-weight:700; text-align:center; }
    .header .sub { font-size:10pt; color:#333; text-align:center; }


    .filters { font-size:10pt; color:#333; margin-bottom:8px; text-align:left; }

    table { border-collapse: collapse; width: 100%; table-layout: fixed; page-break-inside: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    th, td { border:1px solid #444; padding:6px; vertical-align: top; font-size:9pt; }
    th { background: #f0f0f0; font-weight:700; text-align:center; }
    td { word-wrap: break-word; overflow-wrap: break-word; }
    tr { page-break-inside: avoid; page-break-after: auto; }

    /* Column widths */
    .col-sno { width:4%; text-align:center; }
    .col-name { width:18%; }
    .col-empid { width:10%; text-align:center; }
    .col-type { width:10%; text-align:center; }
    .col-reason { width:28%; }
    .col-start, .col-end { width:8%; text-align:center; }
    .col-status, .col-taken, .col-total-approved { width:7%; text-align:center; }

    .footer { margin-top:8px; font-size:8.5pt; color:#555; text-align:right; }
  </style>
</head>
<body>

  <div class="no-print controls">
    <button class="btn" onclick="window.history.back()">← Back</button>
    <button class="btn" onclick="window.print()">Print / Save as PDF</button>
  </div>

  <div class="header" role="banner" aria-label="Institute banner">
    <?php if ($header_img_src): ?>
      <img src="<?= $header_img_src ?>" alt="Institute Header" class="banner">
    <?php else: ?>
      <!-- Fallback text header if image missing -->
      <div class="title">NADAR SARASWATHI COLLEGE OF ENGINEERING & TECHNOLOGY</div>
      <div class="sub">Approved by AICTE, New Delhi & Affiliated to Anna University, Chennai</div>
    <?php endif; ?>
    
  </div>

  <div class="filters"><strong>Department:</strong> <?= $filters_summary ?></div>

  <table role="table" aria-label="Leave approvals">
    <thead>
      <tr>
        <th class="col-sno">SNo</th>
        <th class="col-name">Employee Name</th>
        <th class="col-empid">Employee ID</th>
        <th class="col-type">Leave Type</th>
        <th class="col-reason">Reason</th>
        <th class="col-start">Start Date</th>
        <th class="col-end">End Date</th>
        <th class="col-status">Status</th>
        <th class="col-taken">Leave Taken</th>
        <th class="col-total-approved">Total Approved</th>
      </tr>
    </thead>
    <tbody>
      <?= $table_rows ?>
    </tbody>
  </table>

  <div class="footer">Generated by: <?= htmlspecialchars($emp_name ?: 'System', ENT_QUOTES, 'UTF-8') ?> &nbsp; | &nbsp;<?= date('d-M-Y H:i') ?></div>

  <script>
    // Auto-open print dialog (delay to allow image layout)
    (function(){
      window.onload = function(){
        setTimeout(function(){
          try {
            window.print();
          } catch(e) {
            console.warn('Auto print failed', e);
          }
        }, 600);
      };
    })();
  </script>

</body>
</html>
