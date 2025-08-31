<?php
include('../DB_Config/Config.php');
$db = new dbconfig();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? '';
    $emp_id     = $_POST['emp_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date   = $_POST['end_date'] ?? '';
    $reason     = $_POST['reason'] ?? '';
    $leave_type = $_POST['leave_type'] ?? '';

   
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date   = date('Y-m-d', strtotime($end_date));

    $status     = 'Pending'; 
    $created_on = date('Y-m-d');

    if ($request_id && $emp_id && $start_date && $end_date && $reason && $leave_type) {
        $sql = "INSERT INTO xxhp_elms_leave_req_t (
                    request_id, emp_id, start_date, end_date, reason, status, created_on, leave_type,
                    attribute_category, attribute1, attribute2, attribute3, attribute4, attribute5,
                    attribute6, attribute7, attribute8, attribute9, attribute10,
                    attribute11, attribute12, attribute13, attribute14, attribute15
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?,
                    '0', '0', '0', '0', '0', '0',
                    '0', '0', '0', '0', '0',
                    '0', '0', '0', '0', '0',
                    -1, CURDATE(), -1, CURDATE(), 1001
                )";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $request_id, $emp_id, $start_date, $end_date, $reason, $status, $created_on, $leave_type);

        if ($stmt->execute()) {
            echo "Leave request inserted successfully.";
            
        } else {
            echo "Error inserting leave request: " . $conn->error;
        }
    } else {
        echo "Please fill in all required fields.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
