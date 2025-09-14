<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT student_id, password_hash, status FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($row['status'] !== 'approved') {
            echo "Your account is not yet approved by the administrator. Please wait.";
        } elseif (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['student_id'];
            $_SESSION['user_type'] = USER_TYPE_STUDENT;
            header("Location: student_dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No student found with that email.";
    }
    $stmt->close();
}
$conn->close();
?>