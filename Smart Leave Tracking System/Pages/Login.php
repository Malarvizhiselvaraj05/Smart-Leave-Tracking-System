<?php
session_start();
include('../DB_Config/Config.php');

$db = new dbconfig();
$conn = $db->getConnection();

$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emp_id = trim($_POST['emp_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $query = "SELECT emp_id, emp_name, password, access_key, department 
              FROM xxhp_elms_emp_det_t 
              WHERE emp_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($password === $row['password']) {
            session_regenerate_id(true);

            $department_full = trim($row['department'] ?? '');

            if (preg_match('/^([A-Za-z]+)/', $department_full, $m)) {
                $dept_code = strtoupper($m[1]); 
            } else {
                $dept_code = strtoupper($department_full);
            }

            $_SESSION['emp_id']     = $row['emp_id'];
            $_SESSION['emp_name']   = $row['emp_name'];
            $_SESSION['access_key'] = $row['access_key'];
            $_SESSION['department'] = $department_full;   
            $_SESSION['dept_code']  = $dept_code;         

            header("Location: Leave_Req_List.php");
            exit;
        } else {
            $errorMessage = "❌ Invalid Register Number or Password!";
        }
    } else {
        $errorMessage = "❌ Invalid Register Number or Password!";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="../CSS/Style_login.php">
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Flex container to hold left + right sections */
.main-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    margin-left: -300px;
    gap: 200px; /* space between left and right */
}

/* Left side */
/* Left side */
.left-info {
    flex: 0 0 320px;   /* fixed reasonable width */
    text-align: center;
    padding: 20px;
}

.left-info img {
    max-width: 200px;  /* smaller image */
    border-radius: 12px;
    margin-bottom: 15px;
}

.left-info h2 {
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 8px;
}

.left-info p {
    font-size: 18px;
    line-height: 1.4;
    color: #444;
}



</style>
</head>
<body>

<div class="main-container">

    <!-- Left image + description -->
    <div class="left-info">
        <img src="../Images/AIM.png" alt="AIM">
        <h2>Association of Intelligence Minds</h2>
        <p>
            To become a center for excellence in the field of Artificial Intelligence by promoting knowledge based education, innovation and cutting edge research in artificial engineering and data science.
        </p>
    </div>

    <!-- Right login form (your existing code inside login-container) -->
    <div class="login-container">
        <div class="logo">
            <img src="../Images/College_logo.webp" alt="Logo" width="100">
            <h3> Smart Leave Tracking System</h3>
        </div>

        <h2 class="title">Login</h2>

        <form method="POST">
            <?php if (!empty($errorMessage)): ?>
                <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label>Register Number <span class="required">*</span></label>
                <input type="text" name="emp_id" placeholder="Enter your Register number" required 
                       value="<?php echo htmlspecialchars($_POST['emp_id'] ?? '') ?>">
            </div>

            <div class="form-group password-group">
                <label>Password <span class="required">*</span></label>
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="password" placeholder="Enter your Password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <p class="forgot-password">
                <a href="forgot_password.php">Forgot Password❔</a>
            </p>

            <button type="submit" class="submit-btn">Login</button>
        </form>
    </div>

</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.classList.remove("fa-eye");         
        eyeIcon.classList.add("fa-eye-slash");       
    } else {
        passwordInput.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>

