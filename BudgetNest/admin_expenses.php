<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Delete expense
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM expense WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_expenses.php");
    exit();
}

// ✅ Fetch all expenses with user info
$query = "SELECT e.id, e.category, e.amount, e.date, u.username 
          FROM expense e 
          JOIN users u ON e.user_id = u.id 
          ORDER BY e.date DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Expenses - Admin</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f4f7fc;
        margin: 0;
        padding: 20px;
    }
    .container {
        max-width: 1100px;
        margin: auto;
        background: #fff;
        padding: 25px;
        border-radius: 14px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        animation: fadeIn 0.6s ease-in-out;
    }
    h1 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        overflow: hidden;
        border-radius: 10px;
    }
    th, td {
        padding: 14px;
        text-align: center;
        border-bottom: 1px solid #e0e0e0;
    }
    th {
        background: #3498db;
        color: #fff;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    tr:nth-child(even) {
        background: #f9fafc;
    }
    tr:hover {
        background: #eef6fd;
        transition: background 0.3s ease;
    }
    .btn {
        padding: 6px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-block;
    }
    .btn-delete {
        background: #e74c3c;
        color: white;
    }
    .btn-delete:hover {
        background: #c0392b;
        transform: scale(1.05);
    }
    .back {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        background: #2c3e50;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 500;
        transition: background 0.3s ease;
    }
    .back:hover {
        background: #1a252f;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
</head>
<body>
<div class="container">
    <h1>💰 Manage Expenses</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): 
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td>₹<?= number_format($row['amount'], 2) ?></td>
                    <td><?= $row['date'] ?></td>
                    <td>
                        <a href="admin_expenses.php?delete=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this expense?')">🗑 Delete</a>
                    </td>
                </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="6">No expenses found.</td></tr>
        <?php endif; ?>
    </table>
    <div style="text-align:center;">
        <a href="admin_dashboard.php" class="back">⬅ Back to Admin Dashboard</a>
    </div>
</div>
</body>
</html>
