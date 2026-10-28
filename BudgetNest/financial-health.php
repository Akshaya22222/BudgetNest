<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Current month & year
$year  = (int)date('Y');
$month = (int)date('n');
$monthName = date('F Y');

// Helper: fetch sum by month
function monthly_sum(mysqli $conn, int $user_id, string $table, int $year, int $month): float {
    $sql = "SELECT COALESCE(SUM(amount),0) AS total 
            FROM {$table} 
            WHERE user_id=? AND YEAR(date)=? AND MONTH(date)=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return 0.0;
    $stmt->bind_param("iii", $user_id, $year, $month);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : ['total' => 0];
    $stmt->close();
    return (float)($row['total'] ?? 0);
}

// Fetch totals
$total_income   = monthly_sum($conn, $user_id, 'income', $year, $month);
$total_expenses = monthly_sum($conn, $user_id, 'expense', $year, $month);
$savings        = $total_income - $total_expenses;

// Score calculation
$score   = 0;
$message = "Start tracking your finances!";
$color   = "#ef4444"; // red by default

if ($total_income > 0) {
    $savings_rate = ($savings / $total_income) * 100;

    if ($savings_rate >= 30) {
        $score = 90; $message = "Excellent! Your finances are very healthy 🎉"; $color = "#16a34a";
    } elseif ($savings_rate >= 20) {
        $score = 75; $message = "Good job! You’re managing well 👍"; $color = "#ca8a04";
    } elseif ($savings_rate >= 10) {
        $score = 60; $message = "Fair. Try saving a bit more 💡"; $color = "#f97316";
    } else {
        $score = 40; $message = "Caution: expenses are high relative to income ⚠️"; $color = "#ef4444";
    }
} else {
    if ($total_expenses > 0) {
        $score = 20; $message = "No income recorded this month. Add income to get an accurate score."; $color = "#ef4444";
    } else {
        $score = 0; $message = "No income/expenses recorded this month. Start tracking to see insights!"; $color = "#64748b";
    }
}

$clampedScore = max(0, min(100, (int)round($score)));
$expense_ratio = $total_income > 0 ? ($total_expenses / $total_income) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Financial Health Score</title>
<style>
  :root{
    --bg:#f1f5f9;
    --text:#0f172a;
    --muted:#475569;
    --border:#e2e8f0;
    --card:#ffffff;
    --primary:#0ea5e9;
  }
  body{
     font-family:"Segoe UI",sans-serif;
    background:linear-gradient(135deg, #f3b2b2ff, #b0dff6ff);
    color:var(--text);
    padding:32px;
    display:flex; justify-content:center;
  }
  .wrap{
    max-width:1200px; width:100%;
    display:grid; grid-template-columns:380px 1fr;
    gap:28px;
  }
  .card{
    background:var(--card);
    border-radius:18px;
    padding:28px;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
    border:1px solid var(--border);
    
  }
  h1{margin:0 0 8px;font-size:24px;font-weight:700;color:#1e293b}
  .sub{color:var(--muted);font-size:14px;margin:0 0 20px}
  /* Ring */
  .ring{
    width:200px;height:200px;margin:0 auto;position:relative;
    border-radius:50%;display:grid;place-items:center;
    background:conic-gradient(<?= $color ?> <?= $clampedScore ?>%, #e5e7eb 0);
    box-shadow:inset 0 0 20px rgba(0,0,0,.08);
  }
  .ring::after{
    content:"";position:absolute;inset:18px;background:#fff;border-radius:50%;
    box-shadow:inset 0 0 10px rgba(0,0,0,.05);
  }
  .ring-value{position:relative;z-index:1;font-size:44px;font-weight:800;color:#1e293b}
  .status{text-align:center;margin-top:14px;font-weight:600;color:<?= $color ?>;font-size:16px}
  /* Metrics */
  .metrics {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-top: 24px;
}
.metric {
  flex: 1;
  text-align: center;
  padding: 16px;
  border-radius: 14px;
  background: #f8fafc;
  border: 1px solid var(--border);
}

  .metric:hover{background:#e0f2fe;transform:translateY(-3px)}
  .metric .label{color:var(--muted);font-size:12px;margin-bottom:6px}
  .metric .value{font-weight:700;font-size:18px;color:#0f172a}
  /* Panel */
  .panel h2{margin:0 0 14px;font-size:20px;color:#1e293b}
  .note{
    padding:16px;border-radius:12px;background:#f1f5f9;margin-bottom:18px;
    border-left:5px solid var(--primary);
  }
  .note strong{display:block;margin-bottom:6px;font-size:15px;color:#0f172a}
  .note span{font-size:14px;color:var(--muted)}
  .actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:18px}
  .btn{
    padding:12px 18px;border-radius:10px;font-weight:600;
    font-size:14px;text-decoration:none;cursor:pointer;
    border:1px solid var(--border);transition:.3s ease;
  }
  .btn.primary{background:var(--primary);color:#fff}
  .btn.primary:hover{background:#0284c7}
  .btn.secondary{background:#fff;color:#0f172a}
  .btn.secondary:hover{background:#f1f5f9}
  .footer-link{display:block;margin-top:16px;text-decoration:none;font-weight:600;color:#2563eb}
  /* Donuts */
  .donut{
    width:140px;height:140px;border-radius:50%;margin:14px auto;position:relative;
    background:conic-gradient(#ef4444 <?= $expense_ratio ?>%, #22c55e 0);
    box-shadow:inset 0 0 15px rgba(0,0,0,.1);
  }
  .donut::after{content:"";position:absolute;inset:16px;background:#fff;border-radius:50%;}
  .donut-label{position:absolute;inset:0;display:grid;place-items:center;font-weight:700;font-size:15px;color:#1e293b}
  @media(max-width:900px){.wrap{grid-template-columns:1fr}}
</style>
</head>
<body>
  <div class="wrap">
    <!-- Left -->
    <div class="card">
      <h1>Financial Health Score</h1>
      <p class="sub"><?= $monthName ?></p>

      <div class="ring"><div class="ring-value"><?= $clampedScore ?></div></div>
      <div class="status"><?= htmlspecialchars($message) ?></div>

      <div class="metrics">
        <div class="metric"><div class="label">Income</div><div class="value">₹<?= number_format($total_income,2) ?></div></div>
        <div class="metric"><div class="label">Expenses</div><div class="value">₹<?= number_format($total_expenses,2) ?></div></div>
        <div class="metric"><div class="label">Savings</div><div class="value">₹<?= number_format($savings,2) ?></div></div>
      </div>

      <a href="dashboard.php" class="footer-link">← Back to Dashboard</a>
    </div>

    <!-- Right -->
    <div class="card panel">
      <h2>Insights</h2>
      <div class="note">
        <strong>Status:</strong>
        <?= $clampedScore>=75 ? "Healthy ✅" : ($clampedScore>=50 ? "Moderate ⚖️" : "At Risk ⚠️") ?><br>
        <span><?= htmlspecialchars($message) ?></span>
      </div>

      <div class="note" style="text-align:center">
        <strong>Savings Rate</strong>
        <div><?= $total_income>0 ? number_format(($savings/$total_income)*100,2).'%' : '—' ?></div>
      </div>

      <div class="note" style="text-align:center">
        <strong>Income vs Expenses</strong>
        <div class="donut"><div class="donut-label"><?= number_format($expense_ratio,1) ?>%</div></div>
        <small style="color:#475569">Red = Expenses, Green = Remaining Income</small>
      </div>

      <div class="actions">
        <a class="btn primary" href="add-income.php">+ Add Income</a>
        <a class="btn primary" href="add-expense.php">+ Add Expense</a>
        <a class="btn secondary" href="reports.php">View Reports</a>
      </div>
    </div>
  </div>
</body>
</html>
