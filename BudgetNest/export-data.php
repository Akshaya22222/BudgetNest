<?php
session_start();
include 'config.php'; // or use db.php if that's your connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="budget_data.csv"');

$output = fopen("php://output", "w");
fputcsv($output, array('Type', 'Amount', 'Category', 'Date'));

// Export Income
$incomeQuery = "SELECT amount, category, date FROM income WHERE user_id = ?";
$incomeStmt = $conn->prepare($incomeQuery);
$incomeStmt->bind_param("i", $user_id);
$incomeStmt->execute();
$incomeResult = $incomeStmt->get_result();

while ($row = $incomeResult->fetch_assoc()) {
    fputcsv($output, array('Income', $row['amount'], $row['category'], $row['date']));
}

// Export Expenses
$expenseQuery = "SELECT amount, category, date FROM expense WHERE user_id = ?";
$expenseStmt = $conn->prepare($expenseQuery);
$expenseStmt->bind_param("i", $user_id);
$expenseStmt->execute();
$expenseResult = $expenseStmt->get_result();

while ($row = $expenseResult->fetch_assoc()) {
    fputcsv($output, array('Expense', $row['amount'], $row['category'], $row['date']));
}

fclose($output);
exit;
?>
