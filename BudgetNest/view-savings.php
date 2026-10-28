<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch savings goals
$sql = "SELECT goal_amount, purpose, category, target_date FROM savings_goals WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Savings Goals</title>
  <style>
    /* Background */
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #00c6ff, #0072ff);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    /* Card container */
    .container {
      width: 90%;
      max-width: 900px;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      animation: fadeIn 1s ease-in-out;
    }

    h2 {
      text-align: center;
      color: #fff;
      margin-bottom: 25px;
      font-size: 28px;
      text-shadow: 0 2px 6px rgba(0,0,0,0.4);
    }

    /* Table styling */
    table {
      width: 100%;
      border-collapse: collapse;
      overflow: hidden;
      border-radius: 12px;
    }

    th, td {
      padding: 14px;
      text-align: center;
      font-size: 15px;
    }

    th {
      background: #004d40;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    tr {
      background: rgba(255, 255, 255, 0.8);
      transition: all 0.3s ease;
    }

    tr:nth-child(even) {
      background: rgba(255, 255, 255, 0.6);
    }

    tr:hover {
      background: #e0f7fa;
      transform: scale(1.01);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Back link styled as button */
    .back-link {
      display: inline-block;
      margin: 25px auto 0;
      padding: 12px 20px;
      text-decoration: none;
      font-weight: 600;
      color: #fff;
      background: #007bff;
      border-radius: 12px;
      text-align: center;
      transition: all 0.3s ease;
    }

    .back-link:hover {
      background: #0056b3;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.25);
    }

    /* Animation */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Your Savings Goals</h2>

  <?php if ($result->num_rows > 0): ?>
  <table>
    <tr>
      <th>Goal Amount</th>
      <th>Purpose</th>
      <th>Category</th>
      <th>Set Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td>₹<?php echo number_format($row['goal_amount'], 2); ?></td>
      <td><?php echo htmlspecialchars($row['purpose']); ?></td>
      <td><?php echo htmlspecialchars($row['category']); ?></td>
      <td><?php echo htmlspecialchars($row['target_date']); ?></td>

    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
      <p style="text-align: center; color: #fff;">No savings goals found.</p>
  <?php endif; ?>

  <div style="text-align:center;">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
  </div>
</div>
</body>
</html>
