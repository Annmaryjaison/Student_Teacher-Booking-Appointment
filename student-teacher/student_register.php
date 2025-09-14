<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // SQL query from the previous response
    $sql = "INSERT INTO students (name, email, password_hash) VALUES (?, ?, ?)";
    
    // Check if the prepare() call was successful
    if (!($stmt = $conn->prepare($sql))) {
        // This line will print the exact reason for the error.
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    } else {
        // Continue with the rest of your code
        $stmt->bind_param("sss", $name, $email, $password_hash);
        
        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Execution failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $stmt->close();
    }
    
    $conn->close();
}
?>