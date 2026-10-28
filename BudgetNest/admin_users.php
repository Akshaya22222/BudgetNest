<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('User deleted successfully'); window.location.href='admin_users.php';</script>";
}

// ✅ Fetch Users
$result = $conn->query("SELECT id, username, email, role FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users - BudgetNest Admin</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f7fc;
      margin: 0;
      padding: 20px;
    }
    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 20px;
    }
    .table-container {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0px 6px 18px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      overflow: hidden;
      border-radius: 10px;
    }
    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #e0e0e0;
    }
    th {
      background: #34495e;
      color: #fff;
      font-size: 15px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    tr:nth-child(even) {
      background: #f9fafc;
    }
    tr:hover {
      background: #f1f5f9;
      transition: background 0.3s ease;
    }
    .btn {
      padding: 6px 14px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .edit {
      background: #3498db;
      color: white;
    }
    .edit:hover {
      background: #2980b9;
    }
    .delete {
      background: #e74c3c;
      color: white;
    }
    .delete:hover {
      background: #c0392b;
    }
    .back {
      display: inline-block;
      margin: 20px auto 0;
      padding: 10px 18px;
      background: #2c3e50;
      color: white;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.3s ease;
    }
    .back:hover {
      background: #1a252f;
    }
    .actions {
      display: flex;
      justify-content: center;
      gap: 10px;
    }
  </style>
</head>
<body>
  <div class="table-container">
    <h2>👥 Manage Users</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
      </tr>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['role']) ?></td>
          <td class="actions">
            <a class="btn edit" href="edit_user.php?id=<?= $row['id'] ?>">✏ Edit</a>
            <a class="btn delete" href="admin_users.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">🗑 Delete</a>
          </td>
        </tr>
      <?php } ?>
    </table>
    <div style="text-align:center;">
      <a href="admin_dashboard.php" class="back">⬅ Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
