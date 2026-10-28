<?php
include 'config.php'; // Connect to database
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $source = $_POST['source']; // Personal or Shared

    if (!empty($amount) && !empty($category) && !empty($date) && !empty($source)) {
        $stmt = $conn->prepare("INSERT INTO income (user_id, amount, category, date, source) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $user_id, $amount, $category, $date, $source);
        
        if ($stmt->execute()) {
            echo "<script>alert('Income added successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error: Unable to add income.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill all fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Income - BudgetNest</title>
  <style>
    /* Background */
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #c9ffbf, #ffafbd);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    /* Form Container */
    .form-container {
      background: #fff;
      max-width: 450px;
      width: 90%;
      padding: 35px 25px;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      animation: fadeIn 0.8s ease-in-out;
    }

    .form-container h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
      font-size: 24px;
    }

    /* Inputs */
    input[type="number"],
    input[type="date"],
    select {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      background: #f9f9f9;
      transition: all 0.3s ease;
    }

    input:focus, select:focus {
      border-color: #4caf50;
      box-shadow: 0 0 8px rgba(76,175,80,0.2);
      background: #fff;
      outline: none;
    }

    /* Button */
    button {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #43cea2, #185a9d);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }

    button:hover {
      background: linear-gradient(135deg, #185a9d, #43cea2);
      transform: translateY(-2px);
      box-shadow: 0 8px 18px rgba(0,0,0,0.25);
    }

    /* Back to Dashboard Button */
    .back-link {
      display: inline-block;
      margin-top: 18px;
      text-align: center;
      color: white;
      text-decoration: none;
      font-size: 15px;
      padding: 10px 20px;
      border-radius: 8px;
      background: #007BFF;
      transition: 0.3s;
    }

    .back-link:hover {
      background: #0056b3;
    }

    /* Animation */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>💰 Add Income</h2>
    <form action="add-income.php" method="POST">
      <input type="number" name="amount" placeholder="Enter Amount" required>

      <select name="category" required>
        <option value="" disabled selected>Select Category</option>
        <option value="Salary">Salary</option>
        <option value="Freelance">Freelance</option>
        <option value="Business">Business</option>
        <option value="Investments">Investments</option>
        <option value="Others">Others</option>
      </select>

      <input type="date" name="date" required>

      <select name="source" required>
        <option value="" disabled selected>Income Type</option>
        <option value="Personal">Personal</option>
        <option value="Shared">Shared</option>
      </select>

      <button type="submit" name="submit">➕ Add Income</button>
    </form>
    <div style="text-align: center;">
      <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
  </div>

</body>
</html>
