<?php
include 'config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'student') {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in as a student to book an appointment.']);
    exit();
}

$student_id = $_SESSION['user_id'];
$teacher_id = $data['teacherId'];
$availability_id = $data['availabilityId'];

$conn->begin_transaction();

try {
    // Check if the slot is still available
    $sql_check = "SELECT is_booked FROM teacher_availability WHERE availability_id = ? FOR UPDATE";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $availability_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();

    if ($row['is_booked']) {
        throw new Exception("This slot has already been booked.");
    }

    // Insert new appointment
    $sql_insert = "INSERT INTO appointments (student_id, teacher_id, availability_id, status) VALUES (?, ?, ?, 'pending')";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $student_id, $teacher_id, $availability_id);
    $stmt_insert->execute();

    // Mark the availability slot as booked
    $sql_update = "UPDATE teacher_availability SET is_booked = TRUE WHERE availability_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $availability_id);
    $stmt_update->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Appointment request sent! Awaiting teacher approval.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to book appointment: ' . $e->getMessage()]);
}

$conn->close();
?>