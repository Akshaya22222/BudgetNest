<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$year = (int)date("Y");

// Fetch monthly income & expenses
$months = [];
for ($m = 1; $m <= 12; $m++) {
    $sqlIncome = "SELECT COALESCE(SUM(amount),0) AS total FROM income WHERE user_id=? AND YEAR(date)=? AND MONTH(date)=?";
    $stmt = $conn->prepare($sqlIncome);
    $stmt->bind_param("iii", $user_id, $year, $m);
    $stmt->execute();
    $income = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

    $sqlExpense = "SELECT COALESCE(SUM(amount),0) AS total FROM expense WHERE user_id=? AND YEAR(date)=? AND MONTH(date)=?";
    $stmt = $conn->prepare($sqlExpense);
    $stmt->bind_param("iii", $user_id, $year, $m);
    $stmt->execute();
    $expense = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

    $months[] = ["income" => $income, "expense" => $expense];
}
$stmt->close();

// Fetch category-wise expenses
$sql = "SELECT category, COALESCE(SUM(amount),0) AS total 
        FROM expense
        WHERE user_id=? AND YEAR(date)=? 
        GROUP BY category";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $year);
$stmt->execute();
$res = $stmt->get_result();
$categories = [];
$catAmounts = [];
while ($row = $res->fetch_assoc()) {
    $categories[] = $row['category'];
    $catAmounts[] = $row['total'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  /* General reset & body */
  body {
    margin: 0;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: #e2e8f0;
    padding: 24px;
    color: #1e293b;
  }

  .container {
    max-width: 1000px;
    margin: 0 auto;
  }

  /* Card styling */
  .card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 30px 25px;
    margin-bottom: 25px;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }

  h2 {
    font-size: 22px;
    margin-bottom: 20px;
    font-weight: 700;
    background: linear-gradient(90deg, #3b82f6, #16a34a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .back {
    display: inline-block;
    text-decoration: none;
    font-weight: 600;
    color: #2563eb;
    margin-top: 10px;
    transition: color 0.3s;
  }

  .back:hover {
    color: #1d4ed8;
  }

  .no-data {
    text-align: center;
    color: #64748b;
    font-size: 16px;
    margin: 20px 0;
  }

  /* Chart container */
  canvas {
    border-radius: 16px;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .container {
      padding: 0 15px;
    }
    h2 {
      font-size: 20px;
    }
    .card {
      padding: 20px;
    }
  }
</style>

</head>
<body>
<div class="container">
  <div class="card">
    <h2>Monthly Income vs Expenses (<?= $year ?>)</h2>
    <canvas id="lineChart"></canvas>
  </div>

  <div class="card">
    <h2>Category-wise Expenses (<?= $year ?>)</h2>
    <?php if (!empty($categories)): ?>
      <canvas id="pieChart"></canvas>
    <?php else: ?>
      <p class="no-data">No expenses data available for <?= $year ?>.</p>
    <?php endif; ?>
  </div>

  <a href="dashboard.php" class="back">← Back to Dashboard</a>
</div>

<script>
const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
const income = <?= json_encode(array_column($months,'income')) ?>;
const expenses = <?= json_encode(array_column($months,'expense')) ?>;

new Chart(document.getElementById("lineChart"), {
  type: "line",
  data: {
    labels: months,
    datasets: [
      {label:"Income", data: income, borderColor:"#16a34a", fill:false, tension:0.3},
      {label:"Expenses", data: expenses, borderColor:"#dc2626", fill:false, tension:0.3}
    ]
  }
});

<?php if (!empty($categories)): ?>
new Chart(document.getElementById("pieChart"), {
  type: "pie",
  data: {
    labels: <?= json_encode($categories) ?>,
    datasets: [{
      label: "Expenses",
      data: <?= json_encode($catAmounts) ?>,
      backgroundColor: [
        "#3b82f6","#ef4444","#f59e0b","#10b981","#8b5cf6",
        "#ec4899","#14b8a6","#f97316","#6366f1","#22c55e"
      ]
    }]
  }
});
<?php endif; ?>
</script>
</body>
</html>
