<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BudgetNest Dashboard</title>
  <style>
    /* === Global Background with Subtle Gradient Animation === */
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(-45deg, #89f7fe, #66a6ff, #ff9a9e, #fad0c4);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 20px;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* === Dashboard Container (Glassmorphism) === */
    .dashboard-container {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 40px;
      width: 100%;
      max-width: 1100px;
      color: #333;
      box-shadow: 0 8px 30px rgba(0,0,0,0.25);
      animation: fadeInUp 1s ease forwards;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 2.2rem;
      color: #222;
      letter-spacing: 1px;
    }

    /* === Menu Grid === */
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 22px;
    }

    /* === Menu Item with 3D Hover === */
    .menu-item {
      display: block;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      color: #fff;
      padding: 22px;
      border-radius: 14px;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
      font-size: 1rem;
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
      transition: transform .35s, box-shadow .35s;
      position: relative;
      overflow: hidden;
    }

    .menu-item::after {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: rgba(255, 255, 255, 0.15);
      transform: rotate(25deg);
      transition: 0.6s;
    }

    .menu-item:hover {
      transform: translateY(-8px) scale(1.03) rotateX(5deg);
      box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    }

    .menu-item:hover::after {
      left: 120%;
    }

    /* === Sidebar === */
    .sidebar {
      position: fixed;
      top: 0;
      left: -280px;
      width: 260px;
      height: 100vh;
      background: rgba(44, 62, 80, 0.95);
      backdrop-filter: blur(8px);
      box-shadow: 6px 0 25px rgba(0,0,0,.2);
      display: flex;
      flex-direction: column;
      padding: 24px 0;
      transition: all .4s ease;
      z-index: 1200;
    }

    .sidebar.active {
      left: 0;
    }

    .sidebar h2 {
      color: #fff;
      text-align: center;
      margin-bottom: 18px;
      font-size: 20px;
      letter-spacing: .5px;
    }

    .sidebar a {
      display: block;
      color: #d7e3ec;
      text-decoration: none;
      padding: 12px 28px;
      transition: all .3s;
      font-size: 15px;
      border-left: 3px solid transparent;
    }

    .sidebar a:hover {
      background: linear-gradient(90deg, #1abc9c, #16a085);
      color: #fff;
      border-left: 3px solid #0e8c74;
      padding-left: 36px;
    }

    /* === Sidebar Toggle Button === */
    .toggle-btn {
      position: fixed;
      top: 20px;
      left: 20px;
      background: #1abc9c;
      color: #fff;
      border: none;
      padding: 12px 16px;
      border-radius: 10px;
      cursor: pointer;
      z-index: 1300;
      box-shadow: 0 5px 18px rgba(0,0,0,.15);
      transition: transform .25s ease, background .25s;
    }

    .toggle-btn:hover {
      transform: scale(1.05);
      background: #16a085;
    }

    /* Overlay for sidebar */
    #overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      opacity: 0;
      visibility: hidden;
      transition: opacity .3s ease;
      z-index: 1100;
    }

    #overlay.show {
      opacity: 1;
      visibility: visible;
    }

    /* Logout Special Button */
    .menu-item.logout {
      background: crimson !important;
    }

    .menu-item.logout:hover {
      background: #b71c1c !important;
    }
  </style>
</head>
<body>
  <!-- Toggle button -->
  <button class="toggle-btn" id="toggleBtn">☰</button>

  <!-- Sidebar -->
  <nav class="sidebar" id="sidebar">
    <h2>💰 BudgetNest</h2>
    <a href="profile.php">👤 Profile</a>
    <a href="settings.php">⚙️ Settings</a>
    <a href="go-pro.php">⭐ Go Pro</a>
   
    <a href="logout.php">🚪 Logout</a>
  </nav>

  <!-- Overlay -->
  <div id="overlay"></div>

  <!-- Dashboard -->
  <div class="dashboard-container">
    <h1>Welcome to BudgetNest 🎉</h1>
    <div class="menu-grid">
      <a href="add-income.php" class="menu-item">Add Income</a>
      <a href="view-income.php" class="menu-item">View Income</a>
      <a href="add-expense.php" class="menu-item">Add Expense</a>
      <a href="view-expenses.php" class="menu-item">View Expenses</a>
      <a href="set-savings.php" class="menu-item">Set Savings Goal</a>
      <a href="view-savings.php" class="menu-item">View Savings</a>
      <a href="set-remainder.php" class="menu-item">Set Reminders</a>
      <a href="view-reminders.php" class="menu-item">View Reminders</a>
      <a href="set-budget-limit.php" class="menu-item">Set Budget Limit</a>
      <a href="view-edit-limits.php" class="menu-item">View/Edit Budget Limit</a>
      <a href="monthly-trends.php" class="menu-item">Monthly Trends</a>
      <a href="smart-suggestions.php" class="menu-item">Smart Suggestions</a>
      <a href="calendar-view.php" class="menu-item">Calendar View</a>
      <a href="budget-comparison.php" class="menu-item">Budget Comparison</a>
      <a href="set-target.php" class="menu-item">Set Financial Target</a>
      <a href="view-targets.php" class="menu-item">View Financial Targets</a>
      <a href="add-recurring.php" class="menu-item">Add Recurring Transaction</a>
      <a href="view-recurring.php" class="menu-item">View Recurring Transactions</a>
      <a href="set-emergency-fund.php" class="menu-item">Set Emergency Fund</a>
      <a href="view-emergency-fund.php" class="menu-item">View Emergency Fund</a>
      <a href="wallets.php" class="menu-item">Wallets</a>
      <a href="split-bill.php" class="menu-item">Split Bills</a>
      <a href="shared-budget.php" class="menu-item">Shared Budgets</a>
      <a href="savings-challenges.php" class="menu-item">Savings Challenges</a>
      <a href="financial-health.php" class="menu-item">Financial Health</a>
      <a href="privacy-dashboard.php" class="menu-item">Privacy Dashboard</a>
      <a href="export-data.php" class="menu-item">Export Data</a>
      <a href="logout.php" class="menu-item logout">Logout</a>
    </div>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const btn = document.getElementById('toggleBtn');

    function openSidebar() {
      sidebar.classList.add('active');
      overlay.classList.add('show');
    }

    function closeSidebar() {
      sidebar.classList.remove('active');
      overlay.classList.remove('show');
    }

    function toggleSidebar() {
      sidebar.classList.contains('active') ? closeSidebar() : openSidebar();
    }

    btn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });
  </script>
</body>
</html>
