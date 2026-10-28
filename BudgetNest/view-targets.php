<?php
session_start();
include 'config.php';

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's budget goal
$stmt = $conn->prepare("SELECT * FROM budget_targets WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Budget Goal</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #a1c4fd, #c2e9fb, #ff9a9e, #fad0c4);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            width: 90%;
            max-width: 900px;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
            font-size: 28px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }
        thead {
            background: #007BFF;
            color: #fff;
            border-radius: 10px;
        }
        thead th {
            padding: 15px;
            text-align: left;
        }
        tbody tr {
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background: #ffffff;
        }
        td {
            padding: 14px 18px;
            font-size: 15px;
            color: #333;
        }
        td:first-child {
            font-weight: bold;
            color: #444;
        }
        .back {
            display: block;
            margin-top: 25px;
            text-decoration: none;
            background: #007BFF;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            width: max-content;
        }
        .back:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎯 Your Budget Goal</h2>
        <?php if($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Goal Name</th>
                    <th>Target Amount</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td>₹<?= number_format($row['amount'], 2) ?></td>
                    <td><?= date('d M Y', strtotime($row['month'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center; font-size:16px; color:#555;">No budget goal found. Please set one first.</p>
        <?php endif; ?>
        <a class="back" href="dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
