<?php
include 'config.php';
header('Content-Type: application/json');

$teacher_id = $_GET['teacher_id'];

$sql = "SELECT availability_id, start_time, end_time FROM teacher_availability WHERE teacher_id = ? AND is_booked = FALSE ORDER BY start_time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}
echo json_encode($slots);
$stmt->close();
$conn->close();
?>