<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id']; // Ensure session is started and user is logged in

// Handle updates
if (isset($_POST['update'])) {
    $category = $_POST['category'];
    $limit = $_POST['limit'];
    $query = "UPDATE budget_limits SET limit_amount=? WHERE category=? AND user_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dsi", $limit, $category, $user_id);
    $stmt->execute();
}

// Fetch current limits
$sql = "SELECT category, limit_amount FROM budget_limits WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View/Edit Budget Limits</title>
    <style>
        /* Attractive stationary gradient background */
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #b580ecff, #719be3ff, #ea9b63ff, #ea6ba8ff);
            background-attachment: fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Center container card */
        .container {
            width: 480px;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 22px;
        }

        form {
            margin-bottom: 18px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.08);
        }

        .limit-row {
            margin-bottom: 12px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }

        input[type=number] {
            padding: 10px;
            width: 80%;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type=number]:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.4);
            outline: none;
        }

        button {
            padding: 10px 20px;
            width: 100%;
            background: linear-gradient(135deg, #ff512f, #dd2476);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        }

        .dashboard-link {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #333;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .dashboard-link:hover {
            color: #6a11cb;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>✏️ Edit Budget Limits</h2>
    <?php while($row = $result->fetch_assoc()) { ?>
        <form method="POST">
            <div class="limit-row">
                <label><?php echo ucfirst($row['category']); ?> Limit</label>
                <input type="number" name="limit" value="<?php echo $row['limit_amount']; ?>" required>
                <input type="hidden" name="category" value="<?php echo $row['category']; ?>">
            </div>
            <button type="submit" name="update">Update Limit</button>
        </form>
    <?php } ?>
    <a class="dashboard-link" href="dashboard.php">← Back to Dashboard</a>
</div>
</body>
</html>
