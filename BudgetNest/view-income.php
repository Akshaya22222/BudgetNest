<?php
session_start();
include 'config.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch income data
$sql = "SELECT id, amount, category, date FROM income WHERE user_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("❌ SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Income</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #f1f8e9);
            padding: 30px;
            margin: 0;
        }

        h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        table {
            width: 85%;
            margin: auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 6px 20px rgba(0,0,0,0.1);
            animation: fadeIn 0.9s ease-in-out;
        }

        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 15px;
        }

        th {
            background: linear-gradient(135deg, #43a047, #2e7d32);
            color: white;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: rgba(222, 252, 186, 1);
            transition: background 0.3s ease;
        }

        td {
            color: #333;
        }

        .delete-button {
            background: linear-gradient(135deg, #e53935, #b71c1c);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: transform 0.2s ease, background 0.3s ease;
        }

        .delete-button:hover {
            background: linear-gradient(135deg, #c62828, #8e0000);
            transform: scale(1.05);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 35px;
        }

        .back-link a {
            text-decoration: none;
            padding: 12px 25px;
            background: linear-gradient(135deg, #1976d2, #0d47a1);
            color: white;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: transform 0.2s ease, background 0.3s ease;
        }

        .back-link a:hover {
            background: linear-gradient(135deg, #0d47a1, #002171);
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<h2>💰 Income History</h2>

<table>
    <tr>
        <th>Amount</th>
        <th>Category</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php if ($result && $result->num_rows > 0) { ?>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td>₹<?php echo $row['amount']; ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo $row['date']; ?></td>
            <td>
                <form method="POST" action="delete-income.php" onsubmit="return confirm('Delete this income entry?');">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="delete-button">Delete</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="4" style="color:#777; font-style:italic;">No income records found</td>
        </tr>
    <?php } ?>
</table>

<div class="back-link">
    <a href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
