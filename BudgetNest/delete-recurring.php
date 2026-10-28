<?php
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if ID is passed in URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Delete recurring transaction
    $stmt = $conn->prepare("DELETE FROM recurring_transactions WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Recurring transaction deleted successfully!'); window.location.href='view-recurring.php';</script>";
    } else {
        echo "<script>alert('❌ Error deleting recurring transaction.'); window.location.href='view-recurring.php';</script>";
    }
} else {
    header("Location: view-recurring.php");
    exit();
}
?>
