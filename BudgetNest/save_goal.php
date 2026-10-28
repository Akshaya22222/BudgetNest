<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $goal_amount = trim($_POST['goal_amount']);
    $purpose = trim($_POST['purpose']);
    $category = trim($_POST['category']);
    $target_date = trim($_POST['target_date']);

    if (empty($goal_amount) || empty($purpose) || empty($category) || empty($target_date)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO savings_goals (user_id, goal_amount, purpose, category, target_date) 
            VALUES ('$user_id', '$goal_amount', '$purpose', '$category', '$target_date')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Savings Goal set successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}
?>
