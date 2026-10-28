<?php
session_start();
include 'config.php';

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;
$msg = "";

// Fetch existing reminder data
$stmt = $conn->prepare("SELECT * FROM reminders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    die("Reminder not found or you don't have permission to edit it.");
}

$row = $result->fetch_assoc();
$title = $row['title'];
$description = $row['description'];
$datetime = $row['reminder_time'];
$dt = new DateTime($datetime);
$date = $dt->format('Y-m-d');
$time = $dt->format('H:i');

$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $remind_date = $_POST['remind_date'] ?? '';
    $remind_time = $_POST['remind_time'] ?? '';

    if(!$title || !$remind_date || !$remind_time){
        $msg = "Error: All fields are required.";
    } else {
        $remind_at = $remind_date . ' ' . $remind_time;

        $update = $conn->prepare("UPDATE reminders SET title=?, description=?, reminder_time=? WHERE id=? AND user_id=?");
        $update->bind_param("sssii", $title, $description, $remind_at, $id, $user_id);

        if($update->execute()){
            $msg = "✅ Reminder updated successfully!";
            header("Location: view-reminders.php");
            exit();
        } else {
            $msg = "Error updating reminder.";
        }

        $update->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Reminder</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; padding: 0; background: linear-gradient(135deg, #ff9a9e, #fad0c4, #a1c4fd, #c2e9fb); background-size: 400% 400%; animation: gradientShift 15s ease infinite; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        form { background: #ffffff; padding: 35px 40px; border-radius: 16px; box-shadow: 0 12px 35px rgba(0,0,0,0.15); width: 100%; max-width: 520px; animation: fadeIn 1s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 25px; font-size: 26px; }
        label { display: block; margin: 14px 0 6px; font-weight: 600; color: #333; }
        input[type="text"], input[type="date"], input[type="time"], textarea { width: 100%; padding: 12px 15px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 10px; transition: all 0.3s ease; font-size: 15px; }
        input:focus, textarea:focus { border-color: #007bff; box-shadow: 0 0 10px rgba(0, 123, 255, 0.2); outline: none; }
        textarea { resize: vertical; }
        button { width: 100%; background: linear-gradient(135deg, #007bff, #00c6ff); color: white; padding: 14px; border: none; border-radius: 10px; cursor: pointer; font-size: 16px; font-weight: 600; margin-top: 10px; transition: all 0.3s ease; }
        button:hover { background: linear-gradient(135deg, #0056b3, #0096c7); transform: scale(1.03); }
        a { display: block; margin-top: 18px; text-decoration: none; color: #007bff; text-align: center; font-weight: 600; transition: color 0.3s; }
        a:hover { color: #0056b3; }
        .message { text-align: center; margin-top: 12px; color: green; font-weight: bold; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>✏️ Edit Reminder</h2>
        <label>Reminder Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" required>

        <label>Description:</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>

        <label>Date:</label>
        <input type="date" name="remind_date" value="<?= $date ?>" required>

        <label>Time:</label>
        <input type="time" name="remind_time" value="<?= $time ?>" required>

        <button type="submit">✅ Update Reminder</button>
        <a href="view-reminders.php">← Back to View Reminders</a>
        <div class="message"><?= $msg ?></div>
    </form>
</body>
</html>
