<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Handle Create Challenge
if (isset($_POST['save_challenge'])) {
    $name = $_POST['challenge_name'];
    $target = $_POST['target_amount'];
    $daily = $_POST['daily_amount'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO savings_challenges 
        (user_id, challenge_name, target_amount, daily_amount, start_date, end_date, saved_amount, status) 
        VALUES (?, ?, ?, ?, ?, ?, 0, 'ongoing')");
    $stmt->bind_param("isddss", $user_id, $name, $target, $daily, $start, $end);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('New Savings Challenge Created! 🎯'); window.location='savings-challenges.php';</script>";
    exit();
}

// Handle Save Today
if (isset($_POST['save_today'])) {
    $challenge_id = (int)$_POST['challenge_id'];
    $stmt = $conn->prepare("UPDATE savings_challenges 
        SET saved_amount = saved_amount + daily_amount 
        WHERE id=? AND user_id=? AND status='ongoing'");
    $stmt->bind_param("ii", $challenge_id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('✅ Today\'s savings added! Keep it up 💪'); window.location='savings-challenges.php';</script>";
    exit();
}

// Fetch all challenges for this user
$stmt = $conn->prepare("SELECT * FROM savings_challenges WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>💰 Savings Challenges</title>
<style>
  /* --- CSS same as your enhanced version --- */
  body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    padding: 30px;
    text-align: center;
  }
  h1 { font-size: 2.5rem; margin-bottom: 25px; text-shadow: 1px 1px 5px rgba(0,0,0,0.2); }
  .card { background: #fff; color: #1f2937; border-radius: 20px; padding: 25px 30px; margin: 20px auto; width: 420px; box-shadow: 0 12px 25px rgba(0,0,0,0.15); transition: transform 0.3s, box-shadow 0.3s; position: relative; }
  .card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
  h2, h3 { margin-bottom: 15px; font-weight: 700; }
  input[type="text"], input[type="number"], input[type="date"] { width: 90%; padding: 12px 10px; margin: 8px 0; border-radius: 10px; border: 1px solid #ccc; outline: none; font-size: 1rem; }
  button { width: 95%; padding: 12px 0; margin-top: 12px; border: none; border-radius: 12px; background: linear-gradient(90deg, #34d399, #3b82f6); color: #fff; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 1rem; }
  button:hover { background: linear-gradient(90deg, #3b82f6, #34d399); }
  .progress-container { background: #e5e7eb; border-radius: 25px; overflow: hidden; margin: 15px 0; height: 25px; }
  .progress-bar { height: 100%; width: 0%; background: linear-gradient(90deg, #10b981, #3b82f6); text-align: right; padding-right: 10px; line-height: 25px; color: #fff; font-weight: 600; border-radius: 25px; transition: width 1s ease-in-out; }
  .update-link { position: absolute; top: 15px; right: 20px; text-decoration: none; color: #3b82f6; font-weight: 600; transition: color 0.3s; }
  .update-link:hover { color: #16a34a; }
  .popup { background: rgba(59, 130, 246, 0.9); color: #fff; padding: 12px 15px; border-radius: 12px; margin-top: 12px; display: none; font-weight: 600; animation: fadeIn 0.5s ease-in-out; }
  @keyframes fadeIn { from {opacity:0; transform: translateY(-10px);} to {opacity:1; transform: translateY(0);} }
  @media (max-width: 480px) { .card { width: 90%; padding: 20px; } button, input { width: 95%; } }
</style>
</head>
<body>

<h1>💰 Savings Challenges</h1>

<div class="card">
  <h2>Start a New Challenge</h2>
  <form method="post">
    <input type="text" name="challenge_name" placeholder="Challenge Name" required>
    <input type="number" step="0.01" name="target_amount" placeholder="Target Amount (₹)" required>
    <input type="number" step="0.01" name="daily_amount" placeholder="Daily Saving Amount (₹)" required>
    <input type="date" name="start_date" required>
    <input type="date" name="end_date" required>
    <button type="submit" name="save_challenge">Start Challenge 🚀</button>
  </form>
</div>

<h2>Your Challenges</h2>
<?php while ($row = $result->fetch_assoc()): 
    $progress = ($row['saved_amount'] / $row['target_amount']) * 100;
    if ($progress >= 100 && $row['status'] == 'ongoing') {
        $stmtUpdate = $conn->prepare("UPDATE savings_challenges SET status='completed' WHERE id=? AND user_id=?");
        $stmtUpdate->bind_param("ii", $row['id'], $user_id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
        $row['status'] = 'completed';
    }
?>
<div class="card">
  <a href="update-challenge.php" class="update-link">Update</a>
  <h3><?= htmlspecialchars($row['challenge_name']); ?></h3>
  <p>Target: ₹<?= $row['target_amount']; ?> | Saved: ₹<?= $row['saved_amount']; ?></p>
  <div class="progress-container">
    <div class="progress-bar" style="width: <?= min($progress,100); ?>%;"><?= round($progress,1); ?>%</div>
   
  </div>
  <?php if ($row['status'] == 'ongoing'): ?>
    <form method="post">
      <input type="hidden" name="challenge_id" value="<?= $row['id']; ?>">
      <button type="submit" name="save_today">Save Today's Amount 💵</button>
    </form>
  <?php else: ?>
    <p style="font-weight:600; color:#10b981;">✅ Completed!</p>
  <?php endif; ?>
   
  <div class="popup" id="popup<?= $row['id']; ?>">
    🎉 Keep going! You're making great progress!
  </div>
  
</div>

<?php endwhile; ?>

<script>
document.querySelectorAll('.progress-bar').forEach(bar => {
  let width = parseFloat(bar.style.width);
  let popup = bar.parentElement.nextElementSibling?.querySelector('.popup');
  if (!popup) return;

  if (width >= 50 && width < 100) {
    popup.innerText = "🔥 You're halfway there!";
    popup.style.display = "block";
  } else if (width >= 100) {
    popup.innerText = "🏆 Challenge Completed!";
    popup.style.display = "block";
  }
});
</script>
<!-- Back to Dashboard Button -->
<div style="margin: 30px 0;">
    <a href="dashboard.php" 
       style="display:inline-block; padding:12px 25px; background: linear-gradient(90deg, #3b82f6, #34d399); 
              color:#fff; font-weight:700; border-radius:12px; text-decoration:none; transition:0.3s;">
        ← Back to Dashboard
    </a>
</div>

</body>
</html>
