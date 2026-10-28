<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $budget_name = $_POST['budget_name'];
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("INSERT INTO shared_budget (user_id, budget_name, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $budget_name, $amount);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shared Budget</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #6dd5ed, #2193b0);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: #fff;
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      width: 420px;
      text-align: center;
      transition: transform 0.3s ease;
    }
    .container:hover {
      transform: translateY(-5px);
    }
    h2 {
      margin-bottom: 20px;
      color: #333;
      font-size: 26px;
      font-weight: 600;
    }
    form input {
      width: 90%;
      padding: 12px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
      transition: all 0.2s ease-in-out;
    }
    form input:focus {
      border-color: #2193b0;
      outline: none;
      box-shadow: 0px 0px 5px rgba(33,147,176,0.5);
    }
    button {
      width: 95%;
      padding: 12px;
      margin-top: 12px;
      border-radius: 8px;
      border: none;
      background: linear-gradient(135deg, #56ab2f, #a8e063);
      color: white;
      font-size: 16px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
    }
    button:hover {
      background: linear-gradient(135deg, #4caf50, #6dd5ed);
      transform: scale(1.03);
    }
    .budget-list {
      margin-top: 25px;
      text-align: left;
    }
    .budget-list h3 {
      margin-bottom: 10px;
      color: #444;
      font-size: 18px;
      border-bottom: 2px solid #eee;
      padding-bottom: 5px;
    }
    .budget-item {
      background: #f8f9fa;
      padding: 12px;
      margin: 6px 0;
      border-radius: 8px;
      font-size: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .budget-item:hover {
      transform: translateX(5px);
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .budget-item b {
      color: #2193b0;
    }
    a {
      display: inline-block;
      margin-top: 18px;
      text-decoration: none;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
    }
    a:hover {
      background: linear-gradient(135deg, #764ba2, #667eea);
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>💰 Shared Budget</h2>
    <form method="POST">
      <input type="text" name="budget_name" placeholder="Budget Name (e.g., Room Rent)" required>
      <input type="number" name="amount" placeholder="Budget Amount" required>
      <button type="submit">➕ Add Shared Budget</button>
    </form>

    <div class="budget-list">
      <h3>📋 Existing Shared Budgets:</h3>
      <?php
      $user_id = $_SESSION['user_id'];
      $result = $conn->query("SELECT * FROM shared_budget WHERE user_id=$user_id ORDER BY id DESC");
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<div class='budget-item'><b>" . htmlspecialchars($row['budget_name']) . "</b> <span>₹" . $row['amount'] . "</span></div>";
          }
      } else {
          echo "<p>No shared budgets yet.</p>";
      }
      ?>
    </div>

    <a href="dashboard.php">⬅ Back to Dashboard</a>
  </div>
</body>
</html>
