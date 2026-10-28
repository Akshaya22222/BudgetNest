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
    $limit = $_POST['limit'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO budget_limits (user_id, category, limit_amount) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE limit_amount = VALUES(limit_amount)");
    $stmt->bind_param("iss", $user_id, $category, $limit);

    if ($stmt->execute()) {
        $message = "✅ Budget limit set successfully!";
    } else {
        $message = "❌ Error setting budget limit.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Budget Limit</title>
    <style>
        /* Background */
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #985ed7ff, #6598efff, #f67c64ff, #e2619bff);
            background-size: 400% 400%;
            animation: gradientBG 12s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Container with glassmorphism */
        .container {
            width: 380px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 35px;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            animation: fadeIn 1.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            font-size: 24px;
            letter-spacing: 1px;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: bold;
            color: #f9f9f9;
            font-size: 14px;
        }

        select, input[type="number"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            margin-top: 6px;
            background: rgba(255, 255, 255, 0.85);
            font-size: 15px;
            transition: all 0.3s ease;
        }

        select:focus, input[type="number"]:focus {
            outline: none;
            box-shadow: 0 0 8px #6a11cb;
        }

        button {
            width: 100%;
            margin-top: 28px;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(135deg, #ff512f, #dd2476);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        }

        .message {
            margin-top: 18px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            color: #fff;
        }

        .back-link {
            display: block;
            margin-top: 22px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            color: #fff;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #ffe259;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>💰 Set Budget Limit</h2>
    <form method="post">
        <label for="category">Category</label>
        <select name="category" id="category" required>
            <option value="">-- Select Category --</option>
            <option value="Food">Food</option>
            <option value="Entertainment">Entertainment</option>
            <option value="Groceries">Groceries</option>
            <option value="Transport">Transport</option>
            <option value="Utilities">Utilities</option>
            <option value="Shopping">Shopping</option>
            <option value="Health">Health</option>
            <option value="Others">Others</option>
        </select>

        <label for="limit">Monthly Limit (₹)</label>
        <input type="number" name="limit" id="limit" min="1" required>

        <button type="submit">Set Limit</button>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
    </form>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
