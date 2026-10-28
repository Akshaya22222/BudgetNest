<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Total users
$res = $conn->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$users_data = [];
while ($row = $res->fetch_assoc()) {
    $users_data[$row['role']] = $row['count'];
}

// ✅ Income vs Expenses
$res = $conn->query("SELECT 'income' as type, SUM(amount) as total FROM income 
                     UNION ALL 
                     SELECT 'expenses', SUM(amount) FROM expense");
$finance_data = [];
while ($row = $res->fetch_assoc()) {
    $finance_data[$row['type']] = $row['total'] ?? 0;
}

// ✅ Top 5 Expense Categories
$res = $conn->query("SELECT category, SUM(amount) as total FROM expense
                     GROUP BY category ORDER BY total DESC LIMIT 5");
$expense_labels = [];
$expense_values = [];
while ($row = $res->fetch_assoc()) {
    $expense_labels[] = $row['category'];
    $expense_values[] = $row['total'];
}

// ✅ Monthly trend (last 6 months)
$res = $conn->query("
    SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total, 'income' as type 
    FROM income 
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    UNION ALL
    SELECT DATE_FORMAT(date, '%Y-%m'), SUM(amount), 'expense' 
    FROM expense 
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month ASC
");

$trend_income = [];
$trend_expense = [];
$months = [];

while ($row = $res->fetch_assoc()) {
    $month = $row['month'];
    if (!in_array($month, $months)) $months[] = $month;
    if ($row['type'] == 'income') $trend_income[$month] = $row['total'];
    else $trend_expense[$month] = $row['total'];
}

$months = array_slice($months, -6);
$income_values = [];
$expense_values = [];
foreach ($months as $m) {
    $income_values[] = $trend_income[$m] ?? 0;
    $expense_values[] = $trend_expense[$m] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - BudgetNest</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f7fc; margin: 0; padding: 20px; }
    h1 { text-align: center; margin-bottom: 20px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 1200px; margin: auto; }
    .card { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0px 4px 12px rgba(0,0,0,0.1); }
    canvas { width: 100% !important; height: 350px !important; }
    
    /* Management Section */
    .manage-section { max-width: 1200px; margin: 40px auto; }
    .manage-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap: 20px; }
    .manage-card { background: #fff; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0px 4px 12px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .manage-card:hover { transform: translateY(-4px); box-shadow: 0px 6px 16px rgba(0,0,0,0.15); }
    .manage-card h3 { margin-bottom: 10px; }
    .manage-card p { font-size: 14px; color: #555; margin-bottom: 15px; }
    .manage-card a { display: inline-block; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; color: #fff; }
    .btn-blue { background: #3498db; }
    .btn-green { background: #2ecc71; }
    .btn-orange { background: #e67e22; }
    .btn-red { background: #e74c3c; }
  </style>
</head>
<body>
  <h1>📊 Admin Dashboard - BudgetNest</h1>
  <div class="grid">
    <div class="card"><h3>Total Users</h3><canvas id="usersChart"></canvas></div>
    <div class="card"><h3>Income vs Expenses</h3><canvas id="financeChart"></canvas></div>
    <div class="card"><h3>Top 5 Expense Categories</h3><canvas id="expenseChart"></canvas></div>
    <div class="card"><h3>Monthly Trend (Last 6 Months)</h3><canvas id="trendChart"></canvas></div>
  </div>

  <!-- Management Section -->
  <div class="manage-section">
    <h2 style="text-align:center; margin-bottom:20px;">⚙️ Management</h2>
    <div class="manage-grid">
      <div class="manage-card">
        <h3>👥 Manage Users</h3>
        <p>View, edit, or remove users.</p>
        <a href="admin_users.php" class="btn-blue">Go</a>
      </div>
     <div class="manage-card">
       <h3>💰 Monitor Savings</h3>
       <p>Track savings calculated from income and expenses.</p>
       <a href="admin_savings.php" class="btn-orange">Go</a>
     </div>

      <div class="manage-card">
        <h3>📑 Reports</h3>
        <p>Generate and view detailed reports.</p>
        <a href="admin_reports.php" class="btn-green">Go</a>
      </div>
      <div class="manage-card">
        <h3>🚪 Logout</h3>
        <p>End your admin session safely.</p>
        <a href="logout.php" class="btn-red">Logout</a>
      </div>
    </div>
  </div>

  <script>
    new Chart(document.getElementById("usersChart"), {
      type: "doughnut",
      data: {
        labels: <?= json_encode(array_keys($users_data)) ?>,
        datasets: [{ data: <?= json_encode(array_values($users_data)) ?>,
          backgroundColor: ["#3498db", "#2ecc71", "#f1c40f", "#e74c3c"] }]
      }
    });

    new Chart(document.getElementById("financeChart"), {
      type: "bar",
      data: {
        labels: ["Income", "Expenses"],
        datasets: [{
          label: "Amount (₹)",
          data: [<?= $finance_data['income'] ?? 0 ?>, <?= $finance_data['expenses'] ?? 0 ?>],
          backgroundColor: ["#2ecc71", "#e74c3c"]
        }]
      }
    });

    new Chart(document.getElementById("expenseChart"), {
      type: "pie",
      data: {
        labels: <?= json_encode($expense_labels) ?>,
        datasets: [{ data: <?= json_encode($expense_values) ?>,
          backgroundColor: ["#e67e22", "#9b59b6", "#3498db", "#2ecc71", "#f1c40f"] }]
      }
    });

    new Chart(document.getElementById("trendChart"), {
      type: "line",
      data: {
        labels: <?= json_encode($months) ?>,
        datasets: [
          { label: "Income", data: <?= json_encode($income_values) ?>, borderColor: "#2ecc71", fill: false },
          { label: "Expenses", data: <?= json_encode($expense_values) ?>, borderColor: "#e74c3c", fill: false }
        ]
      }
    });
  </script>
</body>
</html>
