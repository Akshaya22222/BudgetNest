<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered. Please login instead.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Registration failed. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BudgetNest - Register</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background: linear-gradient(-45deg, #6a11cb, #2575fc, #ff6b6b, #ff9f1c);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
    }

    @keyframes gradientBG {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }

    /* Floating finance icons */
    .icon {
      position: absolute;
      font-size: 2.5rem;
      opacity: 0.15;
      animation: floatIcons 12s linear infinite;
    }

    .icon:nth-child(1) { top: 10%; left: 15%; animation-delay: 0s; }
    .icon:nth-child(2) { top: 30%; right: 10%; animation-delay: 2s; }
    .icon:nth-child(3) { bottom: 15%; left: 20%; animation-delay: 4s; }
    .icon:nth-child(4) { bottom: 25%; right: 25%; animation-delay: 6s; }

    @keyframes floatIcons {
      0% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(20deg); }
      100% { transform: translateY(0) rotate(0deg); }
    }

    /* Container */
    .container {
      position: relative;
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(14px);
      border-radius: 20px;
      padding: 2.5rem;
      width: 370px;
      color: #fff;
      box-shadow: 0 8px 32px rgba(0,0,0,0.25);
      text-align: center;
      z-index: 2;
      animation: fadeIn 1.5s ease forwards;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .container h2 {
      font-size: 2rem;
      margin-bottom: 1.2rem;
    }

    /* Inputs */
    form input[type="text"],
    form input[type="email"], 
    form input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      font-size: 1rem;
      transition: 0.3s;
    }

    form input:focus {
      background: rgba(255, 255, 255, 0.35);
      box-shadow: 0 0 10px #ffdd57;
      outline: none;
    }

    /* Show password custom checkbox */
    .show-password {
      display: flex;
      align-items: center;
      margin: 12px 0 18px;
      font-size: 0.9rem;
      color: #f0f0f0;
      cursor: pointer;
    }

    .show-password input[type="checkbox"] {
      display: none;
    }

    .show-password label {
      position: relative;
      padding-left: 28px;
      cursor: pointer;
      user-select: none;
    }

    .show-password label::before {
      content: "";
      position: absolute;
      left: 0;
      top: 2px;
      width: 20px;
      height: 20px;
      border: 2px solid #ffdd57;
      border-radius: 6px;
      background: rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
    }

    .show-password input[type="checkbox"]:checked + label::before {
      background: linear-gradient(135deg, #ffdd57, #ff9f1c);
      border-color: #ff9f1c;
    }

    .show-password input[type="checkbox"]:checked + label::after {
      content: "✔";
      position: absolute;
      left: 4px;
      top: 0px;
      font-size: 14px;
      color: #222;
    }

    /* Button */
    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, #ffdd57, #ff9f1c);
      color: #222;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.3s;
    }

    button:hover {
      transform: translateY(-3px) scale(1.03);
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    /* Error */
    .error {
      color: #ff6b6b;
      margin-bottom: 1rem;
      animation: shake 0.4s ease;
    }

    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      50% { transform: translateX(5px); }
      75% { transform: translateX(-5px); }
      100% { transform: translateX(0); }
    }

    .container p {
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .container a {
      color: #ffdd57;
      text-decoration: none;
      transition: color 0.3s;
    }

    .container a:hover {
      color: #fff;
    }
  </style>
</head>
<body>
  <!-- Floating finance icons -->
  <div class="icon">💰</div>
  <div class="icon">📊</div>
  <div class="icon">💳</div>
  <div class="icon">🏦</div>

  <div class="container">
    <h2>Register</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" id="register-password" placeholder="Password" required>

      <div class="show-password">
        <input type="checkbox" id="showPass" onclick="togglePassword('register-password')">
        <label for="showPass">Show Password</label>
      </div>

      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="index.php">Login</a></p>
  </div>

  <script>
    function togglePassword(id) {
      const field = document.getElementById(id);
      field.type = field.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>

