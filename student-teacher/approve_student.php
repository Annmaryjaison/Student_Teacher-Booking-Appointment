<?php
include 'config.php';
checkUserAccess([USER_TYPE_ADMIN]);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $student_id = $_GET['id'];
    
    if ($action == 'approve') {
        $status = 'approved';
    } elseif ($action == 'reject') {
        $status = 'rejected';
    } else {
        die("Invalid action.");
    }

    $sql = "UPDATE students SET status = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $student_id);
    $stmt->execute();
    $stmt->close();
}
header("Location: admin_dashboard.php");
exit();
?>