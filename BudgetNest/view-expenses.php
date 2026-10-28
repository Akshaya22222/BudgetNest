<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch expenses for the current user
$sql = "SELECT id, amount, category, date FROM expense WHERE user_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Expenses</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #f9fbe7);
            margin: 0;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #1565c0;
            font-size: 28px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        table {
            width: 85%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 6px 18px rgba(0,0,0,0.1);
            animation: fadeIn 0.8s ease-in-out;
        }

        th, td {
            padding: 14px 18px;
            text-align: center;
            font-size: 15px;
            color: #333;
        }

        th {
            background: linear-gradient(135deg, #42a5f5, #1565c0);
            color: white;
            text-transform: uppercase;
            font-size: 15px;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #a4d1ffff;
            transition: 0.3s ease;
        }

        .delete-button {
            background: linear-gradient(135deg, #ef5350, #c62828);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.3s ease;
        }

        .delete-button:hover {
            background: linear-gradient(135deg, #d32f2f, #8e0000);
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

<h2>💸 Expense History</h2>

<table>
    <tr>
        <th>Amount</th>
        <th>Category</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0) { ?>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td>₹<?php echo $row['amount']; ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo $row['date']; ?></td>
            <td>
                <form method="POST" action="delete-expenses.php" onsubmit="return confirm('Delete this expense?');">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="delete-button">Delete</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="4" style="color:#777; font-style:italic;">No expenses found</td>
        </tr>
    <?php } ?>
</table>

<div class="back-link">
    <a href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
