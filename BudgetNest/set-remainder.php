<?php
session_start();
include 'config.php';

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

// Handle form submission
if(isset($_POST['submit'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reminder_time = $_POST['reminder_time'] ?? '';

    if(!$title || !$reminder_time){
        $msg = "Error: Title and Reminder Time are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reminders (user_id, title, description, reminder_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $description, $reminder_time);

        if($stmt->execute()){
            $msg = "✅ Reminder set successfully!";
        } else {
            $msg = "Error: Could not set reminder. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Reminder</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
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
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 16px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 500px;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 25px; font-size: 26px; }
        label { display: block; margin: 14px 0 6px; font-weight: 600; color: #333; }
        input[type="text"], input[type="datetime-local"], textarea {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 15px;
        }
        input:focus, textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0,123,255,0.2);
            outline: none;
        }
        textarea { resize: vertical; }
        button {
            width: 100%;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        button:hover { background: linear-gradient(135deg, #0056b3, #0096c7); transform: scale(1.03); }
        .message { text-align: center; margin-top: 12px; font-weight: bold; color: green; }
        .back { display: block; margin-top: 18px; text-decoration: none; color: #007bff; text-align: center; font-weight: 600; transition: color 0.3s; }
        .back:hover { color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📌 Set Reminder</h2>

        <?php if($msg): ?>
            <div class="message"><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Reminder Title:</label>
            <input type="text" name="title" placeholder="Enter title" required>

            <label>Description (optional):</label>
            <textarea name="description" rows="4" placeholder="Enter description"></textarea>

            <label>Date & Time:</label>
            <input type="datetime-local" name="reminder_time" required>

            <button type="submit" name="submit">✅ Set Reminder</button>
        </form>

        <a class="back" href="dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
