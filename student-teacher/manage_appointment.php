<?php
include 'config.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: ../index.html");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $appointment_id = $_GET['id'];

    if ($action == 'approve') {
        $status = 'confirmed';
    } elseif ($action == 'cancel') {
        $status = 'canceled';
    } else {
        die("Invalid action.");
    }

    $sql = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();
    $stmt->close();
}
header("Location: teacher_dashboard.php");
exit();
?>