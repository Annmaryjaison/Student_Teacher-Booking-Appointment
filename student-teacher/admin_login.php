<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT admin_id, password_hash FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['admin_id'];
            $_SESSION['user_type'] = USER_TYPE_ADMIN;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No admin found with that username.";
    }
    $stmt->close();
}
$conn->close();
?>