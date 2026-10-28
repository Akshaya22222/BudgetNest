<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT goal_amount, saved_amount, purpose, target_date FROM emergency_funds WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$fund = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Emergency Fund</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea, #764ba2);
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
      width: 450px;
      text-align: center;
      animation: fadeIn 0.6s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    h2 {
      color: #333;
      font-size: 26px;
      margin-bottom: 20px;
    }
    p {
      margin: 8px 0;
      color: #444;
      font-size: 15px;
      text-align: left;
    }
    strong {
      color: #222;
    }
    .progress-container {
      margin-top: 15px;
      background: #e9ecef;
      border-radius: 10px;
      overflow: hidden;
      height: 22px;
      width: 100%;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    .progress-bar {
      height: 22px;
      line-height: 22px;
      background: linear-gradient(90deg, #43cea2, #185a9d);
      text-align: right;
      padding-right: 10px;
      font-size: 13px;
      font-weight: bold;
      color: #fff;
      transition: width 0.6s ease;
    }
    a {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      background: linear-gradient(135deg, #36d1dc, #5b86e5);
      color: #fff;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
    }
    a:hover {
      background: linear-gradient(135deg, #5b86e5, #36d1dc);
      transform: scale(1.05);
    }
    .links {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🚑 Your Emergency Fund</h2>

    <?php if ($fund): 
        $progress = ($fund['saved_amount'] / $fund['goal_amount']) * 100;
        if ($progress > 100) $progress = 100; // cap progress
    ?>
        <p><strong>Purpose:</strong> <?= htmlspecialchars($fund['purpose']) ?></p>
        <p><strong>Goal Amount:</strong> ₹<?= $fund['goal_amount'] ?></p>
        <p><strong>Saved Amount:</strong> ₹<?= $fund['saved_amount'] ?></p>
        <p><strong>Target Date:</strong> <?= $fund['target_date'] ?></p>
        <p><strong>Progress:</strong> <?= round($progress, 2) ?>%</p>

        <div class="progress-container">
            <div class="progress-bar" style="width: <?= $progress ?>%;">
                <?= round($progress, 1) ?>%
            </div>
        </div>
    <?php else: ?>
        <p>No emergency fund goal set yet. <a href="set-emergency-fund.php">➕ Set Now</a></p>
    <?php endif; ?>

    <div class="links">
      <a href="set-emergency-fund.php">💵 Add Contribution</a>
      <a href="dashboard.php">⬅ Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
