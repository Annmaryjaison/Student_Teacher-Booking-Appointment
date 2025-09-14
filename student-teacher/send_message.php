<?php
include 'config.php'; // Include your database connection and session start
session_start();

// Ensure the user is a logged-in student
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'student') {
    header("Location: ../index.html");
    exit();
}

// Get the student's ID from the session
$sender_id = $_SESSION['user_id'];
// Get form data
$recipient_id = $_POST['teacher_id'];
$message_text = $_POST['message_text'];

// Check if all fields are filled
if (empty($recipient_id) || empty($message_text)) {
    // Redirect back with an error message
    header("Location: student_dashboard.php?message=error");
    exit();
}

// Prepare SQL statement to insert the message
$stmt = $conn->prepare("INSERT INTO messages (sender_id, recipient_id, message_text) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $sender_id, $recipient_id, $message_text);

if ($stmt->execute()) {
    // Message sent successfully, redirect with a success message
    header("Location: student_dashboard.php?message=success");
} else {
    // Error saving the message
    $conn->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
    header("Location: student_dashboard.php?message=error");
}

$stmt->close();
$conn->close();
exit();
?>