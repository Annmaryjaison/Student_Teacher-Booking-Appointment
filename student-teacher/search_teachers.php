<?php
include 'config.php';
header('Content-Type: application/json');

$query = isset($_GET['q']) ? $_GET['q'] : '';

$sql = "SELECT teacher_id, name, subject FROM teachers WHERE name LIKE ? OR subject LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$teachers = [];
while ($row = $result->fetch_assoc()) {
    $teachers[] = $row;
}
echo json_encode($teachers);
$stmt->close();
$conn->close();
?>