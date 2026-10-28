<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch monthly income
$incomeQuery = "SELECT MONTH(date) AS month, SUM(amount) AS total_income 
                FROM income 
                WHERE user_id = ? 
                GROUP BY MONTH(date)";
$incomeStmt = $conn->prepare($incomeQuery);
$incomeStmt->bind_param("i", $user_id);
$incomeStmt->execute();
$incomeResult = $incomeStmt->get_result();

$incomeData = [];
while ($row = $incomeResult->fetch_assoc()) {
    $incomeData[(int)$row['month']] = $row['total_income'];
}

// Fetch monthly expenses
$expenseQuery = "SELECT MONTH(date) AS month, SUM(amount) AS total_expense 
                 FROM expense 
                 WHERE user_id = ? 
                 GROUP BY MONTH(date)";
$expenseStmt = $conn->prepare($expenseQuery);
$expenseStmt->bind_param("i", $user_id);
$expenseStmt->execute();
$expenseResult = $expenseStmt->get_result();

$expenseData = [];
while ($row = $expenseResult->fetch_assoc()) {
    $expenseData[(int)$row['month']] = $row['total_expense'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Trends</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #97e9f4ff, #94d0fcff, #c8a7f9ff); /* soft pastel blend */
    background-attachment: fixed;
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;  /* animated gradient */
    text-align: center;
    margin: 0;
    padding: 40px 20px;
    color: #333;
}

/* Smooth gradient animation */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.container {
    max-width: 950px;
    margin: auto;
    background: rgba(255, 255, 255, 0.9); /* translucent glass effect */
    border-radius: 20px;
    padding: 40px 35px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
    backdrop-filter: blur(10px);  /* glassmorphism */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.container:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
}


h2 {
    font-size: 26px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 30px;
    letter-spacing: 0.5px;
}

canvas {
    background: #f9fbfc;
    border-radius: 14px;
    padding: 20px;
    box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.05);
    width: 100% !important;
    height: 450px !important;
    transition: all 0.4s ease-in-out;
}

.back-btn {
    display: inline-block;
    margin-top: 30px;
    text-decoration: none;
    color: #fff;
    background: linear-gradient(135deg, #007BFF, #0056b3);
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.4px;
    box-shadow: 0 6px 18px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
}

.back-btn:hover {
    background: linear-gradient(135deg, #0056b3, #004080);
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 8px 22px rgba(0, 86, 179, 0.4);
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Monthly Income vs Expenses</h2>
        <canvas id="trendChart"></canvas>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <script>
        const incomeData = <?php echo json_encode($incomeData); ?>;
        const expenseData = <?php echo json_encode($expenseData); ?>;

        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", 
                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        const incomeValues = months.map((_, index) => incomeData[index + 1] || 0);
        const expenseValues = months.map((_, index) => expenseData[index + 1] || 0);

        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income',
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderRadius: 6,
                        data: incomeValues
                    },
                    {
                        label: 'Expenses',
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderRadius: 6,
                        data: expenseValues
                    }
                ]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1500,   // smooth grow on load
                    easing: 'easeOutCubic'
                },
                hover: {
                    animationDuration: 0 // disable hover vibration
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        labels: { font: { size: 14 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return "₹" + context.formattedValue;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: "rgba(0,0,0,0.05)" }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>
