<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // raw password

    $stmt = $conn->prepare("SELECT id, username, role, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $dbPassword = $row['password'];

        // ✅ Check hashed (password_hash) or MD5
        $isValid = false;
        if (password_verify($password, $dbPassword)) {
            $isValid = true;
        } elseif ($dbPassword === md5($password)) {
            $isValid = true;
        }

        if ($isValid) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];

            if (strtolower($row['role']) == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password');</script>";
        }
    } else {
        echo "<script>alert('Invalid email');</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BudgetNest - Login</title>
  <style>
    /* Animated Gradient Background */
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
      background: linear-gradient(-45deg, #6a11cb, #2575fc, #ff6a00, #ff0099);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Floating background shapes */
    .shape {
      position: absolute;
      border-radius: 50%;
      opacity: 0.4;
      animation: float 10s infinite ease-in-out alternate;
    }
    .shape:nth-child(1) {
      width: 120px; height: 120px;
      background: #ff0099;
      top: 10%; left: 15%;
      animation-duration: 8s;
    }
    .shape:nth-child(2) {
      width: 80px; height: 80px;
      background: #2575fc;
      bottom: 15%; right: 10%;
      animation-duration: 12s;
    }
    .shape:nth-child(3) {
      width: 100px; height: 100px;
      background: #ff6a00;
      bottom: 25%; left: 20%;
      animation-duration: 15s;
    }

    @keyframes float {
      from { transform: translateY(0px); }
      to { transform: translateY(-40px); }
    }

    /* Glassmorphism login card */
    .login-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 40px;
      width: 500px;
      text-align: center;
      color: #fff;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      animation: fadeIn 1.5s ease-in-out;
      transition: 0.4s;
      position: relative;
      z-index: 2;
    }

    .login-container:hover {
      transform: scale(1.02) rotateX(2deg);
      box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-container h2 {
      margin-bottom: 20px;
      font-size: 28px;
      letter-spacing: 1px;
    }

    /* Input styling with animated underline */
    .input-group {
      position: relative;
      margin-bottom: 25px;
    }

    .input-group input {
      width: 75%;
      padding: 12px 40px 12px 15px;
      border: none;
      border-radius: 10px;
      outline: none;
      font-size: 16px;
      color: #333;
      background: rgba(255, 255, 255, 0.9);
      transition: 0.3s;
    }

    .input-group input:focus {
      box-shadow: 0 0 10px rgba(255,255,255,0.6);
    }

    .input-group::after {
      content: "";
      position: absolute;
      left: 15px;
      bottom: 0;
      height: 2px;
      width: 0;
      background: linear-gradient(90deg, #ff6a00, #ee0979);
      transition: width 0.3s;
    }
    .input-group input:focus + .underline,
    .input-group input:valid + .underline {
      width: calc(100% - 30px);
    }

    /* Show password toggle */
    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 18px;
      color: #555;
      transition: 0.3s;
    }

    .toggle-password:hover {
      color: #ff6a00;
      animation: pulse 0.8s infinite;
    }

    @keyframes pulse {
      0% { transform: scale(1) translateY(-50%); }
      50% { transform: scale(1.3) translateY(-50%); }
      100% { transform: scale(1) translateY(-50%); }
    }

    /* Button with glow effect */
    .btn {
      width: 50%;
      padding: 12px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(90deg, #ff6a00, #ee0979);
      color: #fff;
      font-size: 18px;
      cursor: pointer;
      transition: 0.4s;
      box-shadow: 0 0 10px rgba(255,255,255,0.3);
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px rgba(255, 255, 255, 0.9);
    }

    .error {
      color: #ffb3b3;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .register-link {
      margin-top: 15px;
      font-size: 14px;
    }

    .register-link a {
      color: #fff;
      text-decoration: underline;
      transition: 0.3s;
    }

    .register-link a:hover {
      color: #ffeb3b;
    }
  </style>
</head>
<body>
  <!-- Floating shapes -->
  <div class="shape"></div>
  <div class="shape"></div>
  <div class="shape"></div>

  <div class="login-container">
    <h2>Welcome Back</h2>
    <!--<?php if ($error) echo "<p class='error'>$error</p>"; ?>-->
    <form method="POST">
      <div class="input-group">
        <input type="email" name="email" placeholder="Email" required>
      </div>
      <div class="input-group">
        <input type="password" id="password" name="password" placeholder="Password" required>
        <span class="toggle-password" onclick="togglePassword()">👁</span>
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
    <div class="register-link">
      <p>Don’t have an account? <a href="register.php">Register</a></p>
    </div>
  </div>

  <script>
    function togglePassword() {
      const password = document.getElementById("password");
      password.type = password.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>
