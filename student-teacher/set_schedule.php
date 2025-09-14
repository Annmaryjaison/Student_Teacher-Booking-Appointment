<?php
include 'config.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: ../index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $start_datetime = $date . ' ' . $start_time;
    $end_datetime = $date . ' ' . $end_time;

    $sql = "INSERT INTO teacher_availability (teacher_id, start_time, end_time) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $teacher_id, $start_datetime, $end_datetime);

    if ($stmt->execute()) {
        echo "Availability added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header("Location: teacher_dashboard.php");
    exit();
}
?>