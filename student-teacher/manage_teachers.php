<?php
include 'config.php';
checkUserAccess([USER_TYPE_ADMIN]);

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] == 'add') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $subject = $_POST['subject'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO teachers (name, email, password_hash, subject) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password_hash, $subject);
    $stmt->execute();
    $stmt->close();
} elseif (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    $sql = "DELETE FROM teachers WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $stmt->close();
}
header("Location: admin_dashboard.php");
exit();
?>