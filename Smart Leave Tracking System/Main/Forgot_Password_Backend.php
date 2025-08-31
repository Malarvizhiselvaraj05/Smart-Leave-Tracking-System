<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once("../DB_Config/config.php");

    $emp_id = trim($_POST['emp_id'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($emp_id) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../Pages/Forgot_Password.php");
        exit();
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../Pages/Forgot_Password.php");
        exit();
    }

    $db = new dbconfig();
    $conn = $db->getConnection();

    $checkQuery = "SELECT emp_id FROM xxhp_elms_emp_det_t WHERE TRIM(emp_id) = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Email ID not found.";
        $stmt->close();
        $conn->close();
        header("Location: ../Pages/forgot_password.php");
        exit();
    }
    $stmt->close();

    $updateQuery = "UPDATE xxhp_elms_emp_det_t SET password = ? WHERE TRIM(emp_id) = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $new_password, $emp_id);

    if ($updateStmt->execute()) {
        $_SESSION['success'] = "Password updated successfully!";
        header("Location: ../Pages/Forgot_Password.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update password.";
        header("Location: ../Pages/Forgot_Password.php");
        exit();
    }

    $updateStmt->close();
    $conn->close();
} else {
    header("Location: ../Pages/Forgot_Password.php");
    exit();
}
?>
