<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM reminders WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redirect back to view-reminders.php
header("Location: view-reminders.php");
exit;
?>
