<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forgot Password</title>
  <link rel="stylesheet" href="../CSS/Style_login.php">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="forgot-container">
    
    <?php if ($error): ?>
      <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form id="forgotForm" action="../Main/Forgot_Password_Backend.php" method="POST">
      <div class="title">Forgot Password</div>

      <div class="form-group password-group">
  <label>Register Number <span class="required">*</span></label>
  <div class="password-wrapper">
    <input type="text" name="emp_id" id="emp_id" placeholder="Enter your Register Number" required />
  </div>
  <div class="error" id="empIdError"></div>
</div>


      <div class="form-group password-group">
  <label>New Password <span class="required">*</span></label>
  <div class="password-input-wrapper">
    <input type="password" name="new_password" id="new_password" placeholder="Enter new password" required />
    <span class="toggle-password" onclick="togglePassword('new_password', 'eyeNew')">
      <i class="fas fa-eye" id="eyeNew"></i>
    </span>
  </div>
</div>


      <div class="form-group password-group">
  <label>Confirm Password <span class="required">*</span></label>
  <div class="password-input-wrapper"> 
    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required />
    <span class="toggle-password" onclick="togglePassword('confirm_password', 'eyeConfirm')">
      <i class="fas fa-eye" id="eyeConfirm"></i>
    </span>
  </div>
</div>


      <div class="btn-group">
        <button type="button" class="btn" onclick="goBack()">Back</button>
        <button type="reset" class="btn" id="resetBtn">Reset</button>
        <button type="submit" class="btn">Submit</button>
      </div>
    </form>
  </div>

  <script>
    
    function goBack() {
      window.location.href = "./Login.php";
    }
   
function togglePassword(fieldId, iconId) {
  const field = document.getElementById(fieldId);
  const icon = document.getElementById(iconId);

  if (field.type === "password") {
    field.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    field.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}

</script>
</body>
</html>
