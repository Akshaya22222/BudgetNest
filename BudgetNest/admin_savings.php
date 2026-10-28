<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Fetch income, expenses, and calculate savings for each user
$query = "
    SELECT u.id, u.username, 
           COALESCE(SUM(i.amount), 0) AS total_income,
           COALESCE(SUM(e.amount), 0) AS total_expense
    FROM users u
    LEFT JOIN income i ON u.id = i.user_id
    LEFT JOIN expense e ON u.id = e.user_id
    GROUP BY u.id, u.username
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Monitor Savings - Admin</title>
<style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f7fc;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      animation: fadeIn 0.6s ease-in-out;
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c3e50;
      font-size: 28px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 14px;
      text-align: center;
      border-bottom: 1px solid #e0e0e0;
    }
    th {
      background: #3498db;
      color: #fff;
      text-transform: uppercase;
      font-size: 14px;
      letter-spacing: 0.5px;
    }
    tr:nth-child(even) {
      background: #f9f9f9;
    }
    tr:hover {
      background: #eef6fc;
      transition: background 0.3s ease;
    }
    td {
      font-size: 15px;
      color: #2c3e50;
    }

    /* Progress bar styling */
    .progress-bar {
      height: 20px;
      background: #e0e0e0;
      border-radius: 10px;
      overflow: hidden;
      position: relative;
    }
    .progress {
      height: 100%;
      background: linear-gradient(90deg, #2ecc71, #27ae60);
      text-align: center;
      color: #fff;
      font-size: 12px;
      font-weight: bold;
      line-height: 20px;
      transition: width 0.6s ease;
    }
    .negative {
      background: linear-gradient(90deg, #e74c3c, #c0392b) !important;
    }

    /* Back button */
    .back {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      background: #2c3e50;
      color: #fff;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 500;
      transition: background 0.3s ease;
    }
    .back:hover {
      background: #1a252f;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>
  <div class="container">
    <h1>💰 Monitor Savings</h1>
    <table>
        <tr>
            <th>User</th>
            <th>Total Income</th>
            <th>Total Expenses</th>
            <th>Net Savings</th>
            <th>Progress</th>
        </tr>
        <?php if ($result->num_rows > 0): 
            while ($row = $result->fetch_assoc()):
                $income = $row['total_income'];
                $expense = $row['total_expense'];
                $savings = $income - $expense;

                // ✅ Calculate percentage of savings relative to income
                $progress = ($income > 0) ? ($savings / $income) * 100 : 0;
                if ($progress < 0) $progress = 0;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td>₹<?= number_format($income, 2) ?></td>
                    <td>₹<?= number_format($expense, 2) ?></td>
                    <td style="color: <?= ($savings < 0 ? 'red' : 'green') ?>;">
                        ₹<?= number_format($savings, 2) ?>
                    </td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress <?= ($savings < 0 ? 'negative' : '') ?>" 
                                 style="width: <?= min(100, $progress) ?>%">
                                <?= round($progress) ?>%
                            </div>
                        </div>
                    </td>
                </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="5">No records found.</td></tr>
        <?php endif; ?>
    </table>
    <div style="text-align:center;">
      <a href="admin_dashboard.php" class="back">⬅ Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
