<?php
// process-recurring.php
// This script checks recurring transactions and inserts them into expenses when due.

include 'config.php'; // your database connection file
session_start();

// Get all recurring transactions
$sql = "SELECT * FROM recurring_transactions";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $user_id = $row['user_id'];
        $amount = $row['amount'];
        $category = $row['category'];
        $start_date = $row['start_date'];
        $frequency = $row['frequency']; // daily, weekly, monthly
        $last_added = $row['last_added']; // last date added to expenses

        // If never added, treat start_date as last_added
        if (empty($last_added)) {
            $last_added = $start_date;
        }

        $today = date('Y-m-d');
        $next_due = null;

        // Calculate next due date
        if ($frequency == 'daily') {
            $next_due = date('Y-m-d', strtotime($last_added . ' +1 day'));
        } elseif ($frequency == 'weekly') {
            $next_due = date('Y-m-d', strtotime($last_added . ' +1 week'));
        } elseif ($frequency == 'monthly') {
            $next_due = date('Y-m-d', strtotime($last_added . ' +1 month'));
        }

        // If today >= next due, insert into expenses
        if ($today >= $next_due) {
            $insert = $conn->prepare("INSERT INTO expenses (user_id, amount, category, date) VALUES (?, ?, ?, ?)");
            $insert->bind_param("idss", $user_id, $amount, $category, $today);
            $insert->execute();

            // Update last_added date
            $update = $conn->prepare("UPDATE recurring_transactions SET last_added = ? WHERE id = ?");
            $update->bind_param("si", $today, $id);
            $update->execute();
        }
    }
}

echo "Recurring transactions processed successfully.";
?>
