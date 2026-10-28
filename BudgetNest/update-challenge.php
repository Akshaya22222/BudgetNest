<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Delete challenge
if (isset($_POST['delete'])) {
    $challenge_id = (int)$_POST['challenge_id'];
    $stmt = $conn->prepare("DELETE FROM savings_challenges WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $challenge_id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Challenge deleted successfully!'); window.location='update-challenge.php';</script>";
    exit;
}

// Update progress
if (isset($_POST['update'])) {
    $challenge_id = (int)$_POST['challenge_id'];
    $amount = (float)$_POST['amount'];

    $stmt = $conn->prepare("UPDATE savings_challenges 
                            SET saved_amount = saved_amount + ? 
                            WHERE id = ? AND user_id = ?");
    $stmt->bind_param("dii", $amount, $challenge_id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Progress updated successfully!'); window.location='update-challenge.php';</script>";
    exit;
}

// Fetch all challenges with error handling
$stmt = $conn->prepare("SELECT * FROM savings_challenges WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Error fetching challenges: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Savings Challenges</title>
<style>
  /* General Body */
  body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      margin: 0;
      padding: 20px;
      text-align: center;
  }

  h2 {
      margin-bottom: 25px;
      font-size: 2rem;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.2);
  }

  /* Card */
  .card {
      background: #fff;
      color: #1f2937;
      border-radius: 20px;
      padding: 25px 30px;
      margin: 20px auto;
      max-width: 450px;
      box-shadow: 0 12px 25px rgba(0,0,0,0.15);
      transition: transform 0.3s, box-shadow 0.3s;
      position: relative;
  }

  .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
  }

  h3 {
      margin-bottom: 10px;
      font-weight: 700;
  }

  p {
      margin: 5px 0;
  }

  /* Progress Bar */
  .progress {
      background: #e5e7eb;
      border-radius: 25px;
      overflow: hidden;
      height: 25px;
      margin: 10px 0;
  }

  .progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #10b981, #3b82f6);
      text-align: right;
      padding-right: 10px;
      line-height: 25px;
      color: #fff;
      font-weight: 600;
      border-radius: 25px;
      transition: width 1s ease-in-out;
  }

  /* Input & Button */
  input[type=number] {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1rem;
      outline: none;
  }

  button {
      border: none;
      padding: 12px;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 700;
      font-size: 1rem;
      margin-top: 10px;
      transition: 0.3s;
  }

  .update-btn {
      background: linear-gradient(90deg, #34d399, #3b82f6);
      color: #fff;
  }

  .update-btn:hover {
      background: linear-gradient(90deg, #3b82f6, #34d399);
  }

  .delete-btn {
      background: linear-gradient(90deg, #f87171, #ef4444);
      color: #fff;
  }

  .delete-btn:hover {
      background: linear-gradient(90deg, #ef4444, #f87171);
  }

  /* Responsive */
  @media (max-width: 480px) {
      .card { width: 90%; padding: 20px; }
      input, button { width: 95%; }
  }
</style>
</head>
<body>

<h2>Manage Savings Challenges</h2>

<?php while ($row = $result->fetch_assoc()): 
    $progress = ($row['saved_amount'] / $row['target_amount']) * 100;
?>
<div class="card">
    <h3><?= htmlspecialchars($row['challenge_name']); ?></h3>
    <p>Target: ₹<?= $row['target_amount']; ?></p>
    <p>Saved: ₹<?= $row['saved_amount']; ?></p>

    <div class="progress">
        <div class="progress-bar" style="width: <?= min($progress,100); ?>%;">
            <?= round($progress); ?>%
        </div>
    </div>

    <!-- Update Form -->
    <form method="POST">
        <input type="hidden" name="challenge_id" value="<?= $row['id']; ?>">
        <input type="number" step="0.01" name="amount" placeholder="Enter amount saved today" required>
        <button type="submit" name="update" class="update-btn">Update Progress</button>
    </form>

    <!-- Delete Form -->
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this challenge?');">
        <input type="hidden" name="challenge_id" value="<?= $row['id']; ?>">
        <button type="submit" name="delete" class="delete-btn">Delete Challenge</button>
        <a href="savings-challenges.php" style="display:block; margin-top:10px; color:#3b82f6; font-weight:600; text-decoration:none;">← Back to Challenges</a>
    </form>
</div>
<?php endwhile; ?>

</body>
</html>
