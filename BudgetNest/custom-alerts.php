<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

// Fetch alerts for the user
$stmt = $conn->prepare("SELECT * FROM alerts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Custom Alerts & Predictions</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .alert-box { 
        margin: 15px auto; padding: 15px; border-radius: 8px; 
        width: 80%; font-size: 16px; font-weight: bold; 
        transition: 0.3s; 
    }
    .success { background: #d4edda; color: #155724; border-left: 6px solid #28a745; }
    .warning { background: #fff3cd; color: #856404; border-left: 6px solid #ffc107; }
    .prediction { background: #cce5ff; color: #004085; border-left: 6px solid #007bff; }
    h2 { text-align:center; color:#333; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Custom Alerts & Predictions</h2>

    <?php while($row = $result->fetch_assoc()) { ?>
      <div class="alert-box <?php echo $row['alert_type']; ?>">
        <p><b><?php echo ucfirst($row['feature']); ?>:</b> <?php echo $row['message']; ?></p>
        <small><?php echo $row['created_at']; ?></small>
      </div>
    <?php } ?>
  </div>
</body>
</html>
