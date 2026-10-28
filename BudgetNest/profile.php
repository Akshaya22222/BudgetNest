<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'config.php'; // your DB connection

$user_id = $_SESSION['user_id'];

// fetch user details
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    $user = [
        "username" => "Unknown",
        "email" => "Not Found"
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile - BudgetNest</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #ace5ffff, #f196e5ff);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .profile-container {
      max-width: 420px;
      width: 100%;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0px 10px 25px rgba(0,0,0,0.1);
      overflow: hidden;
      animation: fadeIn 0.8s ease-in-out;
    }

    .profile-header {
      background: linear-gradient(135deg, #34495e, #2c3e50);
      color: #fff;
      padding: 25px 20px;
      text-align: center;
    }

    .profile-header h2 {
      margin: 0;
      font-size: 22px;
      font-weight: 600;
    }

    .profile-header span {
      font-size: 14px;
      color: #dcdde1;
    }

    .profile-body {
      padding: 25px;
      text-align: center;
    }

    .profile-body p {
      font-size: 16px;
      margin: 12px 0;
      color: #2c3e50;
    }

    .profile-body p strong {
      color: #34495e;
    }

    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 22px;
      background: #34495e;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s ease;
    }

    .btn:hover {
      background: #2c3e50;
      transform: translateY(-2px);
      box-shadow: 0px 5px 12px rgba(0,0,0,0.15);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="profile-container">
    <div class="profile-header">
      <h2>👤 My Profile</h2>
      <span>Manage your BudgetNest account</span>
    </div>
    <div class="profile-body">
      <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
      <p><strong>Plan:</strong> Free User</p> <!-- static for now -->
      <a href="dashboard.php" class="btn">⬅ Back to Dashboard</a>
    </div>
  </div>
</body>
</html>
