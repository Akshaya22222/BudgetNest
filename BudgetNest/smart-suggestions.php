<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

$currentMonth = date('m');
$currentYear = date('Y');

// Fetch total income
$incomeStmt = $conn->prepare("SELECT SUM(amount) FROM income WHERE user_id=? AND MONTH(date)=? AND YEAR(date)=?");
$incomeStmt->bind_param("iii", $user_id, $currentMonth, $currentYear);
$incomeStmt->execute();
$incomeStmt->bind_result($totalIncome);
$incomeStmt->fetch();
$incomeStmt->close();

// Fetch total expense
$expenseStmt = $conn->prepare("SELECT SUM(amount) FROM expense WHERE user_id=? AND MONTH(date)=? AND YEAR(date)=?");
$expenseStmt->bind_param("iii", $user_id, $currentMonth, $currentYear);
$expenseStmt->execute();
$expenseStmt->bind_result($totalExpense);
$expenseStmt->fetch();
$expenseStmt->close();

// Threshold
$threshold = 0.8 * $totalIncome;

// Fetch top category
$categoryStmt = $conn->prepare("SELECT category, SUM(amount) as total 
                                FROM expense 
                                WHERE user_id=? AND MONTH(date)=? AND YEAR(date)=? 
                                GROUP BY category 
                                ORDER BY total DESC LIMIT 1");
$categoryStmt->bind_param("iii", $user_id, $currentMonth, $currentYear);
$categoryStmt->execute();
$categoryStmt->bind_result($topCategory, $topAmount);
$categoryStmt->fetch();
$categoryStmt->close();

// Advanced saving recommendation
$recommendedSavings = $totalIncome - $totalExpense;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Suggestions</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #dfe9f3, #ffffff, #e0f7fa);
            background-size: 400% 400%;
            animation: gradientMove 18s ease infinite;
            margin: 0;
            padding: 50px 20px;
            text-align: center;
            color: #333;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h2 {
            font-size: 30px;
            color: #222;
            margin-bottom: 35px;
            text-shadow: 1px 2px 4px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }

        /* Suggestion card */
        .suggestion-card {
            max-width: 720px;
            margin: 20px auto;
            padding: 28px;
            border-radius: 16px;
            font-size: 17px;
            font-weight: 500;
            text-align: left;
            box-shadow: 0 8px 28px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.7s ease forwards;
        }

        .suggestion-card:nth-child(2) { animation-delay: 0.2s; }
        .suggestion-card:nth-child(3) { animation-delay: 0.4s; }
        .suggestion-card:nth-child(4) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .suggestion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 32px rgba(0,0,0,0.12);
        }

        /* Variants */
        .alert {
            background: linear-gradient(135deg, #ffebee, #ffcdd2);
            border-left: 6px solid #e53935;
        }

        .tip {
            background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
            border-left: 6px solid #00838f;
        }

        .saving {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            border-left: 6px solid #2e7d32;
        }

        /* Button */
        a.button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #007BFF, #0056b3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            margin-top: 35px;
            box-shadow: 0 6px 18px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        a.button:hover {
            background: linear-gradient(135deg, #0056b3, #003f7f);
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0, 123, 255, 0.4);
        }
    </style>
</head>
<body>

    <h2>💡 Smart Suggestions</h2>

    <!-- Monthly Threshold Alert -->
    <div class="suggestion-card alert">
        <?php
        if ($totalExpense > $threshold) {
            echo "⚠️ Alert: You've spent more than 80% of your income this month!";
        } else {
            echo "✅ Good job! Your expenses are under control.";
        }
        ?>
    </div>

    <!-- Personalized Category Tips -->
    <?php if ($topCategory): ?>
    <div class="suggestion-card tip">
        💡 You're spending the most in <b><?= htmlspecialchars($topCategory) ?></b>.  
        Try setting a limit or tracking this category more closely.
    </div>
    <?php endif; ?>

    <!-- Advanced Savings Recommendation -->
    <div class="suggestion-card saving">
        <?php
        if ($recommendedSavings > 0) {
            echo "💰 You can potentially save ₹" . number_format($recommendedSavings, 2) . " this month. 
                  Consider adding this to your savings goal!";
        } else {
            echo "🔍 Your expenses are higher than income this month. 
                  Try cutting costs in non-essential areas.";
        }
        ?>
    </div>

    <!-- Back to Dashboard -->
    <a href="dashboard.php" class="button">← Back to Dashboard</a>

</body>
</html>
