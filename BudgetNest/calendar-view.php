<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$events = [];

/* ---------------- INCOME ---------------- */
$res = $conn->prepare("SELECT id, source AS title, amount, date FROM income WHERE user_id=?");
$res->bind_param("i", $user_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => "Income: " . $row['title'],
        'start' => $row['date'],
        'description' => "Amount: ₹" . number_format($row['amount'], 2),
        'color' => '#28a745' // green
    ];
}

/* ---------------- EXPENSES ---------------- */
$res = $conn->prepare("SELECT id, category AS title, amount, date FROM expense WHERE user_id=?");
$res->bind_param("i", $user_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => "Expense: " . $row['title'],
        'start' => $row['date'],
        'description' => "Amount: ₹" . number_format($row['amount'], 2),
        'color' => '#ffc107' // yellow
    ];
}

/* ---------------- SAVINGS GOALS ---------------- */
$res = $conn->prepare("SELECT id, purpose, target_date, goal_amount, category 
                       FROM savings_goals WHERE user_id=?");
$res->bind_param("i", $user_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => "Goal: " . $row['purpose'],
        'start' => $row['target_date'],
        'description' => "Target Amount: ₹" . number_format($row['goal_amount'], 2) . 
                         "\nCategory: " . $row['category'],
        'color' => '#17a2b8' // teal for goals
    ];
}

/* ---------------- REMINDERS ---------------- */
$res = $conn->prepare("SELECT id, title, description, reminder_time FROM reminders WHERE user_id=?");
$res->bind_param("i", $user_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => "Reminder: " . $row['title'],
        'start' => $row['reminder_time'],
        'description' => $row['description'],
        'color' => '#dc3545' // red
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calendar View</title>

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <style>
        /* Background */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        /* Calendar container */
        #calendar {
            width: 95%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 25px;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Back link */
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            font-weight: bold;
            color: #fff;
            background: rgba(0,0,0,0.4);
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.3s ease;
        }
        .back-link:hover { background: rgba(0,0,0,0.7); }

        /* FullCalendar */
        .fc .fc-toolbar-title { font-size: 1.5rem; font-weight: bold; color: #333; }
        .fc .fc-button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none; border-radius: 6px;
            color: #fff; font-weight: 500;
            padding: 6px 12px;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .fc .fc-button:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
            transform: translateY(-2px);
        }
        .fc .fc-daygrid-day-number { font-weight: bold; color: #333; }
        .fc .fc-daygrid-day:hover { background: rgba(102, 126, 234, 0.08); transition: background 0.3s ease; }

        /* Events */
        .fc-event {
            border: none !important;
            border-radius: 6px !important;
            font-size: 0.9rem !important;
            padding: 4px 6px !important;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        .fc-event:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div>
        <div id="calendar"></div>
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                },
                events: <?php echo json_encode($events); ?>,
                eventClick: function(info) {
                    alert("📌 " + info.event.title + "\n\n" + (info.event.extendedProps.description || "No details available"));
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
