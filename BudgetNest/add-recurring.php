<?php
// add-recurring.php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $frequency = $_POST['frequency'];
    $start_date = $_POST['start_date'];

    $sql = "INSERT INTO recurring_transactions (user_id, amount, category, frequency, start_date) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsss", $user_id, $amount, $category, $frequency, $start_date);
    $stmt->execute();

    echo "<script>alert('Recurring Transaction Added Successfully!'); window.location.href='view-recurring.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Recurring Transaction</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .card {
      background: #fff;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
      width: 420px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }

    h2 {
      margin-bottom: 25px;
      color: #333;
      font-size: 22px;
      letter-spacing: 0.5px;
    }

    label {
      display: block;
      text-align: left;
      margin: 12px 0 6px;
      font-weight: 600;
      font-size: 14px;
      color: #444;
    }

    input, select {
      width: 100%;
      padding: 12px 14px;
      margin-bottom: 18px;
      border-radius: 10px;
      border: 1px solid #ddd;
      outline: none;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    input:focus, select:focus {
      border-color: #4facfe;
      box-shadow: 0 0 0 3px rgba(79,172,254,0.2);
    }

    button {
      background: linear-gradient(135deg, #4facfe, #00c6ff);
      color: #fff;
      border: none;
      padding: 14px;
      width: 100%;
      border-radius: 10px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background: linear-gradient(135deg, #00c6ff, #4facfe);
      transform: translateY(-2px);
    }

    a.back {
      display: inline-block;
      margin-top: 18px;
      text-decoration: none;
      padding: 10px 18px;
      background: #6c757d;
      color: #fff;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    a.back:hover {
      background: #565e64;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>➕ Add Recurring Transaction</h2>
    <form method="POST">
      <label for="amount">Amount</label>
      <input type="number" name="amount" step="0.01" placeholder="Enter amount" required>

      <label for="category">Category</label>
      <select name="category" required>
        <option value="Food">Food</option>
        <option value="Transport">Transport</option>
        <option value="Rent">Rent</option>
        <option value="Utilities">Utilities</option>
        <option value="Entertainment">Entertainment</option>
        <option value="Other">Other</option>
      </select>

      <label for="frequency">Frequency</label>
      <select name="frequency" required>
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
      </select>

      <label for="start_date">Start Date</label>
      <input type="date" name="start_date" required>

      <button type="submit">Add Transaction</button>
    </form>
    <a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
  </div>
</body>
</html>
