<?php
session_start();
include 'config.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch only reminders for the logged-in user
$stmt = $conn->prepare("SELECT * FROM reminders WHERE user_id = ? ORDER BY reminder_time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reminders</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4, #a1c4fd, #c2e9fb);
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
            max-width: 1100px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            padding: 40px;
            transition: all 0.3s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
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
            font-size: 16px;
        }

        tbody tr {
            background: #fafafa;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }

        td {
            padding: 14px 18px;
            font-size: 15px;
            color: #333;
            vertical-align: top;
        }

        td:first-child {
            font-weight: bold;
            color: #444;
        }

        .actions a {
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-right: 8px;
            transition: all 0.2s ease-in-out;
            display: inline-block;
        }

        .edit-btn {
            background-color: #28a745;
            color: white;
        }

        .edit-btn:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            background: #007BFF;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .back:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

    </style>
</head>
<body>

<div class="container">
    <h2>📅 View Your Reminders</h2>
    <?php if($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Reminder Title</th>
                <th>Description</th>
                <th>Reminder Date & Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($row['reminder_time'])) ?></td>

                    <td class="actions">
                        <a class="edit-btn" href="edit-remainder.php?id=<?= $row['id'] ?>">Edit</a>
                        <a class="delete-btn" href="delete-reminder.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete this reminder?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p style="text-align:center; font-size:18px; color:#555;">You have no reminders yet.</p>
    <?php endif; ?>

    <a class="back" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
