<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Save emergency fund goal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['set_goal'])) {
    $goal_amount = $_POST['goal_amount'];
    $purpose = $_POST['purpose'];
    $target_date = $_POST['target_date'];

    $stmt = $conn->prepare("INSERT INTO emergency_funds (user_id, goal_amount, purpose, target_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $goal_amount, $purpose, $target_date);

    if ($stmt->execute()) {
        $message = "✅ Emergency fund goal set successfully!";
    } else {
        $message = "❌ Error setting goal.";
    }
}

// Add contribution
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_contribution'])) {
    $amount = $_POST['amount'];

    $stmt = $conn->prepare("UPDATE emergency_funds SET saved_amount = saved_amount + ? WHERE user_id = ?");
    $stmt->bind_param("di", $amount, $user_id);

    if ($stmt->execute()) {
        $message = "💰 Contribution added successfully!";
    } else {
        $message = "❌ Error adding contribution.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Emergency Fund Tracker</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #36d1dc, #5b86e5);
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
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    h2 {
      color: #333;
      margin-bottom: 20px;
      font-size: 26px;
      font-weight: 600;
    }
    h3 {
      color: #444;
      margin-top: 25px;
      font-size: 18px;
      border-bottom: 2px solid #eee;
      padding-bottom: 5px;
      text-align: left;
    }
    form {
      margin-top: 15px;
    }
    input {
      width: 90%;
      padding: 12px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
      transition: 0.3s ease;
    }
    input:focus {
      border-color: #5b86e5;
      box-shadow: 0px 0px 5px rgba(91,134,229,0.4);
      outline: none;
    }
    button {
      width: 95%;
      padding: 12px;
      margin-top: 10px;
      border-radius: 8px;
      border: none;
      background: linear-gradient(135deg, #56ab2f, #a8e063);
      color: white;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background: linear-gradient(135deg, #43cea2, #185a9d);
      transform: scale(1.03);
    }
    p {
      margin-top: 15px;
    }
    a {
      display: inline-block;
      margin-top: 12px;
      text-decoration: none;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
    }
    a:hover {
      background: linear-gradient(135deg, #764ba2, #667eea);
      transform: scale(1.05);
    }
    .message {
      font-weight: bold;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .success {
      background: #e0f8e9;
      color: #2e7d32;
    }
    .error {
      background: #fdecea;
      color: #c62828;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🚑 Emergency Fund Tracker</h2>

    <?php if ($message): ?>
        <p class="message <?php echo (strpos($message, '✅') !== false || strpos($message, '💰') !== false) ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>
    <?php endif; ?>

    <h3>🎯 Set Goal</h3>
    <form method="POST">
        <input type="number" name="goal_amount" placeholder="Goal Amount" required>
        <input type="text" name="purpose" placeholder="Purpose (e.g. Medical, Job Loss)" required>
        <input type="date" name="target_date" required>
        <button type="submit" name="set_goal">Set Goal</button>
    </form>

    <h3>💵 Add Contribution</h3>
    <form method="POST">
        <input type="number" name="amount" placeholder="Contribution Amount" required>
        <button type="submit" name="add_contribution">Add Contribution</button>
    </form>

    <p><a href="view-emergency-fund.php">📊 View Progress</a></p>
    <p><a href="dashboard.php">⬅ Back to Dashboard</a></p>
  </div>
</body>
</html>
