<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT teacher_id, password_hash FROM teachers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['teacher_id'];
            $_SESSION['user_type'] = 'teacher';
            header("Location: teacher_dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No teacher found with that email.";
    }
    $stmt->close();
}
$conn->close();
?>