<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bill Splitter</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      padding: 35px 30px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      width: 420px;
      text-align: center;
      animation: fadeIn 0.6s ease-in-out;
    }

    h2 {
      margin-bottom: 25px;
      color: #333;
      font-size: 24px;
      font-weight: 600;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
      outline: none;
      transition: 0.3s;
    }

    input:focus {
      border-color: #4CAF50;
      box-shadow: 0 0 8px rgba(76,175,80,0.3);
    }

    button {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #4CAF50, #2e7d32);
      color: white;
      border: none;
      cursor: pointer;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      margin-top: 10px;
      transition: 0.3s;
    }

    button:hover {
      background: linear-gradient(135deg, #2e7d32, #1b5e20);
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }

    .result {
      margin-top: 20px;
      background: #f9f9f9;
      padding: 14px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 500;
      color: #333;
      box-shadow: inset 0 2px 6px rgba(0,0,0,0.05);
    }

    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      background: linear-gradient(135deg, #333, #111);
      color: #fff;
      padding: 10px 20px;
      border-radius: 8px;
      transition: 0.3s;
      font-size: 14px;
    }

    a:hover {
      background: linear-gradient(135deg, #111, #000);
      transform: scale(1.05);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>💰 Bill Splitter</h2>
    <form method="POST">
      <input type="number" name="total_amount" placeholder="Enter total bill amount" required>
      <input type="number" name="num_people" placeholder="Number of people" required>
      <button type="submit">Split Bill</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $total = $_POST['total_amount'];
        $people = $_POST['num_people'];

        if ($people > 0) {
            $share = $total / $people;
            echo "<div class='result'>Each person should pay: <b>₹" . number_format($share, 2) . "</b></div>";
        } else {
            echo "<div class='result'>⚠ Number of people must be greater than 0.</div>";
        }
    }
    ?>

    <a href="dashboard.php">⬅ Back to Dashboard</a>
  </div>
</body>
</html>
