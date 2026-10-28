<?php
// view-recurring.php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM recurring_transactions WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    echo "<script>alert('Recurring transaction deleted!'); window.location.href='view-recurring.php';</script>";
}

// Fetch recurring transactions
$sql = "SELECT * FROM recurring_transactions WHERE user_id=? ORDER BY start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Recurring Transactions</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #FFDEE9 0%, #B5FFFC 100%);
      margin: 0;
      padding: 0;
    }

    .container {
      width: 90%;
      max-width: 1000px;
      margin: 40px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.12);
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #222;
      font-size: 24px;
    }

    .top-actions {
      text-align: center;
      margin-bottom: 20px;
    }

    .top-actions a, .btn-back {
      display: inline-block;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      margin-right: 10px;
      transition: all 0.3s ease;
    }

    .top-actions a:hover, .btn-back:hover {
      background: linear-gradient(135deg, #5a67d8, #6b46c1);
      transform: translateY(-2px);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    table thead {
      background: #4a90e2;
      color: #fff;
    }

    th, td {
      padding: 14px 16px;
      text-align: center;
      font-size: 15px;
    }

    th {
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    tr {
      transition: background 0.3s ease;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    tr:hover {
      background: #eef4ff;
    }

    .btn {
      text-decoration: none;
      padding: 8px 14px;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      margin: 0 3px;
      display: inline-block;
      transition: all 0.3s ease;
    }

    .btn-edit {
      background: #28a745;
      color: #fff;
    }

    .btn-edit:hover {
      background: #218838;
      transform: scale(1.05);
    }

    .btn-delete {
      background: #dc3545;
      color: #fff;
    }

    .btn-delete:hover {
      background: #c82333;
      transform: scale(1.05);
    }

    .pill {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      color: #fff;
    }

    .pill.daily { background: #00c851; }
    .pill.weekly { background: #33b5e5; }
    .pill.monthly { background: #ffbb33; }
  </style>
</head>
<body>
  <div class="container">
    <h2>📋 Recurring Transactions</h2>

    <div class="top-actions">
      <a href="add-recurring.php">➕ Add New</a>
      <a href="dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Amount</th>
          <th>Category</th>
          <th>Frequency</th>
          <th>Start Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td>₹<?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo $row['category']; ?></td>
            <td>
              <span class="pill <?php echo strtolower($row['frequency']); ?>">
                <?php echo ucfirst($row['frequency']); ?>
              </span>
            </td>
            <td><?php echo $row['start_date']; ?></td>
            <td>
              <a href="edit-recurring.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">✏ Edit</a>
              <a href="view-recurring.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this transaction?');">🗑 Delete</a>
            </td>
          </tr>
      <?php } } else { ?>
          <tr>
            <td colspan="6" style="padding:20px;color:#666;">No recurring transactions found.</td>
          </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
