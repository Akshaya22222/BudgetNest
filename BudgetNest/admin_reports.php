<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include 'config.php';

// ✅ Fetch all data
$users    = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$incomes  = $conn->query("SELECT u.username, i.source, i.amount, i.date 
                          FROM income i 
                          JOIN users u ON i.user_id = u.id 
                          ORDER BY i.date DESC");
$expenses = $conn->query("SELECT u.username, e.category, e.amount, e.date 
                          FROM expense e 
                          JOIN users u ON e.user_id = u.id 
                          ORDER BY e.date DESC");
$reminders = $conn->query("SELECT u.username, r.title, r.description, r.reminder_time 
                           FROM reminders r 
                           JOIN users u ON r.user_id = u.id 
                           ORDER BY r.reminder_time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Reports - BudgetNest</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: #f4f7fc;
      margin: 0; padding: 20px;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      background: #fff;
      padding: 25px;
      border-radius: 14px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; margin-bottom: 20px; color: #2c3e50; }
    .tabs { display: flex; justify-content: center; gap: 15px; margin-bottom: 20px; }
    .tab-btn {
      padding: 10px 18px;
      border: none;
      border-radius: 8px;
      background: #16a085;
      color: #fff;
      cursor: pointer;
      font-weight: 500;
      transition: background 0.3s;
    }
    .tab-btn:hover, .tab-btn.active { background: #13836b; }
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.6s ease; }

    table {
      width: 100%; border-collapse: collapse; margin-top: 15px;
      border-radius: 10px; overflow: hidden;
    }
    th, td {
      padding: 12px; text-align: center;
      border-bottom: 1px solid #e0e0e0;
    }
    th {
      background: #16a085; color: #fff;
      text-transform: uppercase; font-size: 14px;
    }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #eefaf7; transition: 0.3s; }
    td { font-size: 15px; color: #2c3e50; }
    .back {
      display: inline-block; margin-top: 20px;
      text-decoration: none; background: #2c3e50; color: #fff;
      padding: 10px 18px; border-radius: 8px; font-weight: 500;
    }
    .back:hover { background: #1a252f; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📑 Admin Reports</h2>
    <div class="tabs">
      <button class="tab-btn active" onclick="openTab('users')">👥 Users</button>
      <button class="tab-btn" onclick="openTab('income')">💰 Income</button>
      <button class="tab-btn" onclick="openTab('expense')">📉 Expenses</button>
      <button class="tab-btn" onclick="openTab('reminders')">⏰ Reminders</button>
    </div>

    <!-- Users -->
    <div id="users" class="tab-content active">
      <h3>👥 User Report</h3>
      <table>
        <tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created At</th></tr>
        <?php while ($row = $users->fetch_assoc()) { ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= ucfirst($row['role']) ?></td>
            <td><?= $row['created_at'] ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>

    <!-- Income -->
    <div id="income" class="tab-content">
      <h3>💰 Income Report</h3>
      <table>
        <tr><th>User</th><th>Source</th><th>Amount</th><th>Date</th></tr>
        <?php while ($row = $incomes->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['source']) ?></td>
            <td>₹<?= number_format($row['amount'], 2) ?></td>
            <td><?= $row['date'] ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>

    <!-- Expenses -->
    <div id="expense" class="tab-content">
      <h3>📉 Expense Report</h3>
      <table>
        <tr><th>User</th><th>Category</th><th>Amount</th><th>Date</th></tr>
        <?php while ($row = $expenses->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td>₹<?= number_format($row['amount'], 2) ?></td>
            <td><?= $row['date'] ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>

    <!-- Reminders -->
    <div id="reminders" class="tab-content">
      <h3>⏰ Reminder Report</h3>
      <table>
        <tr><th>User</th><th>Title</th><th>Description</th><th>Remind At</th></tr>
        <?php while ($row = $reminders->fetch_assoc()) { ?>
          <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= $row['reminder_time'] ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>

    <div style="text-align:center;">
      <a href="admin_dashboard.php" class="back">⬅ Back to Dashboard</a>
    </div>
  </div>

  <script>
    function openTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.getElementById(tabId).classList.add('active');
      event.target.classList.add('active');
    }
  </script>
</body>
</html>
