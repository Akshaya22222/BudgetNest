<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set Savings Goal</title>
  <style>
    /* Background gradient */
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #56d4f7ff, #ea68ddff);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    /* Glassmorphic card */
    .card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 40px 30px;
      width: 360px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
      animation: fadeIn 1s ease-in-out;
    }

    .card h2 {
      margin-bottom: 25px;
      font-size: 24px;
      font-weight: 600;
      color: #fff;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    /* Input and select styling */
    .form-control {
      width: 100%;
      padding: 12px 14px;
      margin: 10px 0;
      border: none;
      border-radius: 12px;
      outline: none;
      font-size: 15px;
      background: rgba(255, 255, 255, 0.85);
      box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15);
      transition: all 0.3s ease;
    }

    .form-control:focus {
      background: #fff;
      box-shadow: 0 0 8px rgba(0, 114, 255, 0.6);
      transform: scale(1.02);
    }

    select.form-control {
      appearance: auto; /* Show only on dropdowns */
      cursor: pointer;
    }

    input[type="date"].form-control {
      color: #333;
    }

    /* Buttons */
    .btn {
      display: inline-block;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-success {
      background: #28a745;
      color: #fff;
      margin-top: 10px;
    }

    .btn-success:hover {
      background: #218838;
      transform: translateY(-2px);
    }

    .btn-back {
      background: #007bff;
      color: #fff;
      margin-top: 15px;
    }

    .btn-back:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }

    /* Fade-in animation */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Set Savings Goal</h2>
    <form method="POST" action="save_goal.php">
      <input type="number" name="goal_amount" placeholder="Goal Amount" class="form-control" required>
      <input type="text" name="purpose" placeholder="Purpose (e.g., Emergency Fund, Vacation)" class="form-control" required>
      
      <select name="category" class="form-control" required>
        <option value="">Select Category</option>
        <option value="Travel">Travel</option>
        <option value="Education">Education</option>
        <option value="Emergency">Emergency</option>
        <option value="Shopping">Shopping</option>
      </select>
      
      <input type="date" name="target_date" class="form-control" required>
      
      <button type="submit" class="btn btn-success">Set Goal</button>
    </form>
    <a href="dashboard.php">
      <button class="btn btn-back">← Back to Dashboard</button>
    </a>
  </div>
</body>
</html>
