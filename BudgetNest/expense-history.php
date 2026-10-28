<?php
include 'config.php';
session_start();

// Handle delete request
if (isset($_GET['delete'])) {
    $expense_id = $_GET['delete'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $expense_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch expenses
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, category, amount, date, description FROM expenses WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Expense History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 85%;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 30px;
            transition: 0.4s;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #007BFF;
            color: white;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 7px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #c0392b;
        }

        @media(max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Expense History</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>₹<?= number_format($row['amount'], 2) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this expense?');">
                        <button class="delete-btn">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
