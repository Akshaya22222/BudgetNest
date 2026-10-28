<?php
session_start();
include 'config.php'; // use your DB connection file
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$msg = "";

/* Add wallet */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_wallet'])) {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $balance = $_POST['balance'] !== "" ? (float)$_POST['balance'] : 0;

    $stmt = $conn->prepare("INSERT INTO wallets (user_id, name, type, balance) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", $user_id, $name, $type, $balance);
    if ($stmt->execute()) $msg = "✅ Wallet added successfully!";
    else $msg = "❌ Error adding wallet: " . $conn->error;
    $stmt->close();
}

/* Delete wallet */
if (isset($_GET['delete'])) {
    $wid = (int)$_GET['delete'];
    if ($msg === "") {
        $stmt = $conn->prepare("DELETE FROM wallets WHERE id=? AND user_id=?");
        $stmt->bind_param("ii", $wid, $user_id);
        if ($stmt->execute()) $msg = "🗑️ Wallet deleted.";
        else $msg = "❌ Error deleting wallet: " . $conn->error;
        $stmt->close();
    }
}

/* Fetch wallets */
$w = $conn->prepare("SELECT id, name, type, balance, created_at FROM wallets WHERE user_id=? ORDER BY created_at DESC");
$w->bind_param("i", $user_id);
$w->execute();
$wallets = $w->get_result();
$w->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Wallets / Accounts</title>
<style>
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #74ebd5, #ACB6E5);
    margin: 0;
    padding: 40px;
  }
  .wrap {
    max-width: 1100px;
    margin: 0 auto;
  }
  h1 {
    color: #fff;
    text-align: center;
    margin-bottom: 30px;
    font-size: 32px;
    letter-spacing: 1px;
  }
  .grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 24px;
  }
  .card {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(8px);
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    padding: 28px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(0,0,0,0.18);
  }
  h3 {
    margin-top: 0;
    color: #2c3e50;
    margin-bottom: 18px;
  }
  label {
    display: block;
    font-size: 14px;
    margin: 10px 0 6px;
    color: #333;
  }
  input, select, button {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 15px;
    outline: none;
    margin-bottom: 14px;
    transition: all .2s ease;
  }
  input:focus, select:focus {
    border-color: #4096ff;
    box-shadow: 0 0 0 3px rgba(64,150,255,0.15);
  }
  button {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: #fff;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: transform .15s ease, background .3s ease;
  }
  button:hover {
    transform: scale(1.03);
    background: linear-gradient(135deg, #3a7bd5, #3a6073);
  }
  .msg {
    margin-bottom: 20px;
    text-align: center;
    font-weight: 600;
    padding: 12px;
    border-radius: 10px;
    background: #eafaf1;
    color: #0a7a28;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 12px;
    overflow: hidden;
  }
  th, td {
    padding: 14px 12px;
    border-bottom: 1px solid #f0f0f0;
    text-align: left;
    font-size: 14px;
  }
  th {
    background: #f7faff;
    color: #2c3e50;
    font-weight: 600;
  }
  tr:nth-child(even) {
    background: #fafafa;
  }
  tr:hover {
    background: #f1f7ff;
  }
  .pill {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 999px;
    background: #eef5ff;
    color: #1c5cff;
    font-weight: 600;
    font-size: 13px;
  }
  .actions a {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    background: #ff4d4f;
    font-size: 13px;
    font-weight: 600;
    transition: transform 0.15s ease, opacity .2s;
  }
  .actions a:hover {
    opacity: 0.9;
    transform: scale(1.05);
  }
  .back {
    display: inline-block;
    margin-top: 18px;
    text-decoration: none;
    color: #2c3e50;
    font-weight: 700;
    transition: color .2s;
  }
  .back:hover {
    color: #1c5cff;
  }
</style>
</head>
<body>
<div class="wrap">
  <h1>💰 Wallets / Accounts</h1>
  <?php if($msg): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <div class="grid">
    <div class="card">
      <h3>Add Wallet</h3>
      <form method="post">
        <label>Wallet Name</label>
        <input type="text" name="name" placeholder="e.g., Cash, HDFC Bank, GPay" required>
        <label>Type</label>
        <select name="type" required>
          <option value="Cash">Cash</option>
          <option value="Bank">Bank</option>
          <option value="UPI/Wallet">UPI/Wallet</option>
          <option value="Other">Other</option>
        </select>
        <label>Starting Balance (optional)</label>
        <input type="number" step="0.01" name="balance" placeholder="0.00">
        <button type="submit" name="add_wallet">+ Add Wallet</button>
      </form>
      <a class="back" href="dashboard.php">← Back to Dashboard</a>
    </div>
    <div class="card">
      <h3>Your Wallets</h3>
      <table>
        <thead>
          <tr><th>Name</th><th>Type</th><th>Balance</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php if($wallets->num_rows===0): ?>
            <tr><td colspan="5" style="text-align:center;color:#666;">No wallets yet. Add your first one.</td></tr>
          <?php else: while($row=$wallets->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><span class="pill"><?= htmlspecialchars($row['type']) ?></span></td>
              <td>₹<?= number_format((float)$row['balance'],2) ?></td>
              <td><?= htmlspecialchars($row['created_at']) ?></td>
              <td class="actions">
                <a href="wallets.php?delete=<?= (int)$row['id'] ?>" onclick="return confirm('Delete this wallet? This cannot be undone.')">Delete</a>
              </td>
            </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
