<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $month = $_POST['month'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO budget_targets (user_id, category, amount, month) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $user_id, $category, $amount, $month);

    if ($stmt->execute()) {
        $message = "✅ Target set successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Financial Target</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            /* Static gradient background */
            background: linear-gradient(135deg, #7dccf0ff, #91a3f3ff, #fae392ff);
            height: 100vh;
        }

        .container {
            width: 450px;
            margin: 80px auto;
            background: #fff;
            border-radius: 15px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        form label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
            color: #444;
        }

        form input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            background: #f9f9f9;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        form input:focus, select:focus {
            border-color: #007BFF;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,123,255,0.4);
            outline: none;
        }

        button {
            width: 100%;
            background: linear-gradient(135deg, #007BFF, #00c6ff);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #0056b3, #0099cc);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }

        .message {
            text-align: center;
            margin-top: 18px;
            font-weight: bold;
            color: green;
            font-size: 16px;
        }

        .back-link {
            display: block;
            margin-top: 25px;
            text-align: center;
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🎯 Set Budget Goal</h2>
    <form method="POST" action="">
        <label>Category:</label>
        <select name="category" required>
            <option value="">--Select Category--</option>
            <option value="Food">🍔 Food</option>
            <option value="Transport">🚌 Transport</option>
            <option value="Rent">🏠 Rent</option>
            <option value="Entertainment">🎬 Entertainment</option>
            <option value="Utilities">💡 Utilities</option>
            <option value="Others">📦 Others</option>
        </select>

        <label>Target Amount:</label>
        <input type="number" step="0.01" name="amount" placeholder="Enter amount (₹)" required>

        <label>Month:</label>
        <input type="month" name="month" required>

        <button type="submit">✅ Set Target</button>
    </form>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
