<?php header("Content-type: text/css"); ?>

:root {
  --primary-color: #007bff;
  --primary-color-hover: #007bff;
  --background-overlay: rgba(255, 255, 255, 0.92);
  --shadow-color: rgba(0, 0, 0, 0.3);
  --font-family: Arial, sans-serif;
  --container-width: 350px;
  --container-padding: 30px;
}

body {
  margin: 0;
  padding: 0;
  font-family: var(--font-family);
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(
        rgba(255, 255, 255, 0.1),   /* very light overlay */
        rgba(255, 255, 255, 0.1)
      ),
      url('../Images/Login.jpg') center/cover no-repeat fixed;
}



.login-wrapper {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.outside-text {
  font-size: 30px;
  font-family: 'Dancing Script', cursive;
  color: black;
  text-align: center;
  font-weight: 700;
  margin-bottom: 60px;
  
}

.login-container,
.forgot-container {
  width: var(--container-width);
  max-width: 90%;
  color: black;
  padding: var(--container-padding);
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(10px) saturate(180%);
  -webkit-backdrop-filter: blur(10px) saturate(180%);
  border-radius: 12px;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  text-align: center;
  position: relative;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  margin-bottom: 20px;
}

.login-container:hover,
.forgot-container:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px var(--shadow-color);
}

.logo h3 {
  color: black;
  font-family: 'Poppins', cursive;
  font-size: 20px;
  margin-top: 10px;
}

.logo img {
  max-width: 300px; /* or any bigger size */
  width: auto;
  height: auto;
  display: block;
  margin: 0 auto 20px auto; 
}

.title {
  font-size: 22px;
  margin-bottom: 20px;
  color: black;
  font-weight: bold;
  
}

.form-group {
  text-align: left;
  margin: 10px 0;
  
}

.form-group label {
  display: block;
  font-weight: bold;
  color: black;
  font-size: 16px;
  margin-bottom: 5px;
  
}

.required {
  color: red;
  margin-left: 2px;
  font-weight: 900;
}

input[type="text"],
input[type="password"] {
  width: 100%;
  padding: 10px;
  margin-bottom: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  box-sizing: border-box;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input[type="text"]:focus,
input[type="password"]:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 5px var(--primary-color);
  outline: none;
}

.forgot-password {
  text-align: right;
  margin-bottom: 10px;
}

.forgot-password a {
  font-size: 14px;
  color: #363434ff;
  text-decoration: none;
}

.forgot-password a:hover {
  color: #353030ff;
  text-decoration: underline;
}

.submit-btn,
.btn {
  background-color: #569feef9;
  color: white;
  border: none;
  padding: 10px 18px;
  font-size: 16px;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 10px;
  transition: background-color 0.3s ease, transform 0.2s ease;
  width: auto;
  min-width: 80px;
}

.submit-btn:hover,
.btn:hover {
  background-color: #2888eff9;
  transform: scale(1.02);
}

.btn-group {
  display: flex;
  justify-content: space-between;
  gap: 10px;
  margin-top: 15px;
}

.error,
.error-msg {
  color: red;
  font-size: 14px;
  margin: 5px 0;
}

.success-msg {
  color: limegreen;
  font-size: 14px;
  margin: 5px 0;
}

@media (max-width: 768px) {
  .login-container,
  .forgot-container {
    width: 90%;
  }
}

@media (max-width: 480px) {
  .title {
    font-size: 18px;
  }
  .submit-btn,
  .btn {
    font-size: 14px;
  }
  .logo img {
    max-width: 60px;
  }
}
.password-group {
  position: relative;
  margin: 10px 0;
  text-align: left;
}

.password-input-wrapper {
  position: relative;
  width: 100%;
}

.password-input-wrapper input[type="password"],
.password-input-wrapper input[type="text"] {
  width: 100%;
  padding-right: 36px; /* Space for the icon */
  box-sizing: border-box;
  padding: 10px;
  margin-bottom: 5px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 18px;
  color: #333;
  z-index: 10;
  width: 24px; /* Add this for consistent icon width */
  text-align: center; /* Center the icon inside the span */
}

