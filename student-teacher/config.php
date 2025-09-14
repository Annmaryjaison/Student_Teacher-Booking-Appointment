


<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_teacher_booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // This line will print the error message if the connection fails
    die("Connection failed: " . $conn->connect_error);
}

// Define user types for clarity
define('USER_TYPE_ADMIN', 'admin');
define('USER_TYPE_TEACHER', 'teacher');
define('USER_TYPE_STUDENT', 'student');

// Check user type for access control in each module
function checkUserAccess($allowedTypes) {
    if (!isset($_SESSION['user_type']) || !in_array($_SESSION['user_type'], $allowedTypes)) {
        header("Location: ../index.html");
        exit();
    }
}
?>