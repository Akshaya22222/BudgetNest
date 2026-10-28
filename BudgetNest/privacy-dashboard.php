<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch total income and expense for the current month
$currentMonth = date('m');
$currentYear = date('Y');

$incomeQuery = $conn->prepare("SELECT SUM(amount) AS total_income FROM income WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ?");
$incomeQuery->bind_param("iii", $user_id, $currentMonth, $currentYear);
$incomeQuery->execute();
$incomeResult = $incomeQuery->get_result();
$total_income = $incomeResult->fetch_assoc()['total_income'] ?? 0;

$expenseQuery = $conn->prepare("SELECT SUM(amount) AS total_expense FROM expense WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ?");
$expenseQuery->bind_param("iii", $user_id, $currentMonth, $currentYear);
$expenseQuery->execute();
$expenseResult = $expenseQuery->get_result();
$total_expense = $expenseResult->fetch_assoc()['total_expense'] ?? 0;

// Delete logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['data_type'] ?? '';

    $allowed = ['income', 'expense', 'savings', 'reminders'];
    if (in_array($type, $allowed)) {
        $stmt = $conn->prepare("DELETE FROM $type WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $message = ucfirst($type) . " data deleted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Privacy Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            margin: 0;
            padding: 40px;
        }

        .dashboard-container {
            max-width: 750px;
            margin: auto;
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 35px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #4e54c8;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        .message {
            text-align: center;
            color: #27ae60;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .summary {
            text-align: center;
            background: linear-gradient(135deg, #e0c3fc, #8ec5fc);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .summary h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.3rem;
        }

        .summary p {
            font-size: 18px;
            margin: 8px 0;
            font-weight: 600;
        }

        .summary p strong {
            color: #4e54c8;
        }

        .section {
            margin: 20px 0;
        }

        form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            padding: 15px 20px;
            border-radius: 10px;
            background: #f9f9ff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        form:hover {
            transform: translateX(5px);
        }

        .section span {
            font-weight: bold;
            color: #333;
        }

        button {
            padding: 10px 18px;
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #e63950, #cc2b2b);
            transform: scale(1.05);
        }

        .back-link {
            display: block;
            margin-top: 25px;
            text-align: center;
            text-decoration: none;
            background: #4e54c8;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .back-link:hover {
            background: #373ba1;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>🔒 Privacy Dashboard</h2>

        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <div class="summary">
            <h3>This Month's Summary</h3>
            <p><strong>Total Income:</strong> ₹<?php echo number_format($total_income, 2); ?></p>
            <p><strong>Total Expense:</strong> ₹<?php echo number_format($total_expense, 2); ?></p>
        </div>

        <div class="section">
            <form method="POST">
                <span>Delete all Income Data</span>
                <input type="hidden" name="data_type" value="income">
                <button type="submit">Delete</button>
            </form>

            <form method="POST">
                <span>Delete all Expense Data</span>
                <input type="hidden" name="data_type" value="expense">
                <button type="submit">Delete</button>
            </form>

            

            <form method="POST">
                <span>Delete all Reminders</span>
                <input type="hidden" name="data_type" value="reminders">
                <button type="submit">Delete</button>
            </form>
        </div>

        <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
