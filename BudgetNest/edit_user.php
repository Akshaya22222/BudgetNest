<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include 'config.php';

// ✅ Fetch user
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// ✅ Update user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $role, $id);
    $stmt->execute();

    echo "<script>alert('User updated successfully'); window.location.href='admin_users.php';</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit User</title>
  <style>
    body { font-family: Arial; background: #f4f7fc; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 350px; }
    input, select, button { width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; }
    button { background: #3498db; color: white; border: none; cursor: pointer; }
    button:hover { background: #2980b9; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Edit User</h2>
    <form method="POST">
      <input type="text" name="username" value="<?= $user['username'] ?>" required>
      <select name="role" required>
        <option value="user" <?= $user['role']=='user'?'selected':'' ?>>User</option>
        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
      </select>
      <button type="submit">Update</button>
    </form>
  </div>
</body>
</html>
