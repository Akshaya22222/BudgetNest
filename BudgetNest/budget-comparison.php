<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? 1; // fallback for testing

$comparison = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $month1 = $_POST['month1'];
    $month2 = $_POST['month2'];

    function getTotals($conn, $user_id, $month) {
        $income_sql = "SELECT SUM(amount) AS total_income FROM income WHERE user_id=? AND DATE_FORMAT(date, '%Y-%m')=?";
        $expense_sql = "SELECT SUM(amount) AS total_expense FROM expense WHERE user_id=? AND DATE_FORMAT(date, '%Y-%m')=?";

        $stmt1 = $conn->prepare($income_sql);
        $stmt1->bind_param("is", $user_id, $month);
        $stmt1->execute();
        $income = $stmt1->get_result()->fetch_assoc()['total_income'] ?? 0;

        $stmt2 = $conn->prepare($expense_sql);
        $stmt2->bind_param("is", $user_id, $month);
        $stmt2->execute();
        $expense = $stmt2->get_result()->fetch_assoc()['total_expense'] ?? 0;

        return ['income' => $income, 'expense' => $expense];
    }

    $comparison['month1'] = getTotals($conn, $user_id, $month1);
    $comparison['month2'] = getTotals($conn, $user_id, $month2);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Budget Comparison</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            padding: 40px;
            margin: 0;
        }
        .container {
            background: #fff;
            max-width: 850px;
            margin: auto;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .container:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 35px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #4e54c8;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        form {
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            font-weight: 600;
            color: #333;
        }
        input[type="month"] {
            padding: 10px 15px;
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s ease;
        }
        input[type="month"]:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 8px rgba(78,84,200,0.4);
        }
        input[type="submit"] {
            padding: 10px 25px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        input[type="submit"]:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
            transform: translateY(-2px);
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        table th, table td {
            border: 1px solid #eee;
            padding: 14px;
            text-align: center;
            font-size: 1rem;
        }
        th {
            background: #4e54c8;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background: #f9f9ff;
        }
        tr:hover td {
            background: #eef2ff;
            transition: 0.3s;
        }
        .difference {
            font-weight: bold;
            color: #d6336c;
        }
        canvas {
            display: block;
            margin: 30px auto;
            max-width: 650px;
        }
        a {
            display: inline-block;
            text-align: center;
            margin-top: 25px;
            text-decoration: none;
            background: #4e54c8;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }
        a:hover {
            background: #373ba1;
        }
    </style>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h2>📊 Compare Budget Between Months</h2>
        <form method="POST">
            <label>Select First Month:</label>
            <input type="month" name="month1" required>
            <label>Select Second Month:</label>
            <input type="month" name="month2" required>
            <br>
            <input type="submit" value="Compare">
        </form>

        <?php if (!empty($comparison)): ?>
        <table>
            <tr>
                <th>Type</th>
                <th><?= htmlspecialchars($_POST['month1']) ?></th>
                <th><?= htmlspecialchars($_POST['month2']) ?></th>
                <th>Difference</th>
            </tr>
            <tr>
                <td>Income</td>
                <td>₹<?= $comparison['month1']['income'] ?></td>
                <td>₹<?= $comparison['month2']['income'] ?></td>
                <td class="difference">₹<?= $comparison['month2']['income'] - $comparison['month1']['income'] ?></td>
            </tr>
            <tr>
                <td>Expenses</td>
                <td>₹<?= $comparison['month1']['expense'] ?></td>
                <td>₹<?= $comparison['month2']['expense'] ?></td>
                <td class="difference">₹<?= $comparison['month2']['expense'] - $comparison['month1']['expense'] ?></td>
            </tr>
            <tr>
                <td>Savings</td>
                <td>₹<?= $comparison['month1']['income'] - $comparison['month1']['expense'] ?></td>
                <td>₹<?= $comparison['month2']['income'] - $comparison['month2']['expense'] ?></td>
                <td class="difference">₹<?= ($comparison['month2']['income'] - $comparison['month2']['expense']) - ($comparison['month1']['income'] - $comparison['month1']['expense']) ?></td>
            </tr>
        </table>

        <canvas id="comparisonChart" width="400" height="200"></canvas>

        <script>
            const ctx = document.getElementById('comparisonChart').getContext('2d');
            const comparisonChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Income', 'Expenses', 'Savings'],
                    datasets: [
                        {
                            label: '<?= htmlspecialchars($_POST['month1']) ?>',
                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                            borderRadius: 8,
                            data: [
                                <?= $comparison['month1']['income'] ?>,
                                <?= $comparison['month1']['expense'] ?>,
                                <?= $comparison['month1']['income'] - $comparison['month1']['expense'] ?>
                            ]
                        },
                        {
                            label: '<?= htmlspecialchars($_POST['month2']) ?>',
                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                            borderRadius: 8,
                            data: [
                                <?= $comparison['month2']['income'] ?>,
                                <?= $comparison['month2']['expense'] ?>,
                                <?= $comparison['month2']['income'] - $comparison['month2']['expense'] ?>
                            ]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Budget Comparison Chart',
                            font: {
                                size: 18,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value;
                                }
                            }
                        }
                    }
                }
            });
        </script>
        <?php endif; ?>

        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
