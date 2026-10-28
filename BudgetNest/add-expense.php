<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $user_id = $_SESSION['user_id'];

    // Insert into expense table
    $stmt = $conn->prepare("INSERT INTO expense (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $user_id, $amount, $category, $date);
    $stmt->execute();

    if ($stmt->error) {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    } else {
        $message = "<div class='alert alert-success'>✅ Expense added successfully!</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Expense</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #a6d7f3ff, #f2a9e3ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 18px;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.15);
            width: 420px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0px 12px 28px rgba(0,0,0,0.25);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 15px;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: #6c63ff;
            box-shadow: 0px 0px 6px rgba(108, 99, 255, 0.4);
            outline: none;
        }

        button {
            background: linear-gradient(135deg, #ff4d4d, #d90429);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.4s ease, transform 0.2s;
        }

        button:hover {
            background: linear-gradient(135deg, #e60000, #9b0000);
            transform: scale(1.03);
        }

        .alert {
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 500;
            animation: fadeIn 0.8s ease-in-out;
        }
        .alert-success {
            background: #eafaf1;
            color: #137333;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #fdecea;
            color: #a71d2a;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            text-decoration: none;
            color: white;
            background: linear-gradient(135deg, #007BFF, #0056b3);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s, transform 0.2s;
        }
        .back-link a:hover {
            background: linear-gradient(135deg, #0056b3, #00408a);
            transform: scale(1.05);
        }

        /* Animation for alerts */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <form method="POST" class="card">
        <h2>➕ Add Expense</h2>

        <!-- Show success/error messages -->
        <?php if (!empty($message)) echo $message; ?>

        <input type="number" name="amount" placeholder="Enter Amount (₹)" required>
        
        <select name="category" required>
            <option value="">-- Select Category --</option>
            <option value="Food">🍔 Food</option>
            <option value="Transport">🚌 Transport</option>
            <option value="Entertainment">🎮 Entertainment</option>
            <option value="Groceries">🛒 Groceries</option>
            
            <option value="Utilities">💡 Utilities</option>
            <option value="Shopping">🛍 Shopping</option>
            <option value="Health">💊 Health</option>
            <option value="Other">📌 Other</option>
        </select>

        <input type="date" name="date" required>
        
        <button type="submit">Add Expense</button>

        <div class="back-link">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </form>
</body>
</html>
