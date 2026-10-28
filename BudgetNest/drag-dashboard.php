<!-- drag-dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Drag & Drop Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f8fb;
      padding: 30px;
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    .dashboard {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
      padding: 20px;
      width: 200px;
      height: 120px;
      text-align: center;
      transition: transform 0.3s ease;
      cursor: move;
    }
    .card a {
      display: block;
      margin-top: 15px;
      text-decoration: none;
      color: #007bff;
      font-weight: 500;
    }
    .card:hover {
      transform: scale(1.03);
    }
  </style>
</head>
<body>
  <h2>Drag & Drop Your Dashboard</h2>

  <div class="dashboard" id="dashboard">
    <div class="card" draggable="true"><a href="add-income.php">➕ Add Income</a></div>
    <div class="card" draggable="true"><a href="add-expense.php">➖ Add Expense</a></div>
    <div class="card" draggable="true"><a href="view-expenses.php">📋 View Expenses</a></div>
    <div class="card" draggable="true"><a href="set-savings-goal.php">🎯 Set Savings Goal</a></div>
    <div class="card" draggable="true"><a href="set-budget-limit.php">📊 Set Budget Limit</a></div>
    <div class="card" draggable="true"><a href="privacy-dashboard.php">🔐 Privacy Dashboard</a></div>
    <div class="card" draggable="true"><a href="budget-compare.php">📈 Budget Compare</a></div>
    <div class="card" draggable="true"><a href="calendar-view.php">🗓️ Calendar View</a></div>
    <div class="card" draggable="true"><a href="logout.php">🚪 Logout</a></div>
  </div>

  <script>
    const cards = document.querySelectorAll('.card');
    const dashboard = document.getElementById('dashboard');

    let draggedCard = null;

    cards.forEach(card => {
      card.addEventListener('dragstart', () => {
        draggedCard = card;
        card.style.opacity = '0.5';
      });

      card.addEventListener('dragend', () => {
        draggedCard = null;
        card.style.opacity = '1';
      });

      card.addEventListener('dragover', e => e.preventDefault());

      card.addEventListener('drop', e => {
        e.preventDefault();
        if (draggedCard && draggedCard !== card) {
          dashboard.insertBefore(draggedCard, card.nextSibling);
        }
      });
    });
  </script>
</body>
</html>
