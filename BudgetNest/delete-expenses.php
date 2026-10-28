<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Ensure the user owns this expense
    $stmt = $conn->prepare("DELETE FROM expense WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        header("Location: view-expenses.php");
        exit();
    } else {
        echo "Error deleting expense.";
    }
}
?>
