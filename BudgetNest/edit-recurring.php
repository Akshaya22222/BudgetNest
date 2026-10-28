<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch the recurring transaction to edit
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM recurring_transactions WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transaction = $result->fetch_assoc();

    if (!$transaction) {
        echo "<script>alert('Transaction not found!'); window.location.href='view-recurring.php';</script>";
        exit();
    }
} else {
    header("Location: view-recurring.php");
    exit();
}

// ✅ Update on form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $start_date = $_POST['start_date'];
    $frequency = $_POST['frequency'];

    $update = $conn->prepare("UPDATE recurring_transactions SET amount=?, category=?, start_date=?, frequency=? WHERE id=? AND user_id=?");
    $update->bind_param("dsssii", $amount, $category, $start_date, $frequency, $id, $user_id);

    if ($update->execute()) {
        echo "<script>alert('Recurring transaction updated successfully!'); window.location.href='view-recurring.php';</script>";
    } else {
        echo "<script>alert('Error updating transaction.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Recurring Transaction</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            width: 420px;
            animation: fadeIn 0.6s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 22px;
        }

        input, select {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: #007BFF;
            box-shadow: 0 0 8px rgba(0,123,255,0.3);
        }

        button {
            background: linear-gradient(135deg, #007BFF, #0056b3);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 16px;
            font-weight: bold;
        }

        button:hover {
            background: linear-gradient(135deg, #0056b3, #003d80);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #007BFF;
            font-size: 15px;
            transition: 0.3s;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <form method="POST" class="card">
        <h2>✏ Edit Recurring Transaction</h2>
        <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($transaction['amount']) ?>" required>
        
        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Food" <?= $transaction['category']=="Food" ? "selected" : "" ?>>Food</option>
            <option value="Transport" <?= $transaction['category']=="Transport" ? "selected" : "" ?>>Transport</option>
            <option value="Utilities" <?= $transaction['category']=="Utilities" ? "selected" : "" ?>>Utilities</option>
            <option value="Shopping" <?= $transaction['category']=="Shopping" ? "selected" : "" ?>>Shopping</option>
            <option value="Health" <?= $transaction['category']=="Health" ? "selected" : "" ?>>Health</option>
            <option value="Other" <?= $transaction['category']=="Other" ? "selected" : "" ?>>Other</option>
        </select>

        <input type="date" name="start_date" value="<?= htmlspecialchars($transaction['start_date']) ?>" required>

        <select name="frequency" required>
            <option value="Daily" <?= $transaction['frequency']=="Daily" ? "selected" : "" ?>>Daily</option>
            <option value="Weekly" <?= $transaction['frequency']=="Weekly" ? "selected" : "" ?>>Weekly</option>
            <option value="Monthly" <?= $transaction['frequency']=="Monthly" ? "selected" : "" ?>>Monthly</option>
            <option value="Yearly" <?= $transaction['frequency']=="Yearly" ? "selected" : "" ?>>Yearly</option>
        </select>

        <button type="submit">💾 Update Transaction</button>
        <a href="view-recurring.php" class="back-link">← Back to Recurring List</a>
    </form>
</body>
</html>
