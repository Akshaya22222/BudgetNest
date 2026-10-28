<?php
include 'config.php';

$current = date("Y-m-d H:i");
$sql = "SELECT * FROM reminders WHERE remind_at <= ? ORDER BY remind_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current);
$stmt->execute();
$result = $stmt->get_result();

$reminders = [];
while ($row = $result->fetch_assoc()) {
    $reminders[] = $row;
}
echo json_encode($reminders);
?>
