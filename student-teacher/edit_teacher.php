<?php
include 'config.php';
checkUserAccess([USER_TYPE_ADMIN]);

$teacher = null;
if (isset($_GET['id'])) {
    $teacher_id = $_GET['id'];
    $sql = "SELECT * FROM teachers WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];

    $sql = "UPDATE teachers SET name = ?, email = ?, subject = ? WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $subject, $teacher_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #0078D4;
            outline: none;
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #0078D4;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #005ea2;
        }

        .error-message {
            text-align: center;
            color: #d8000c;
            background-color: #ffdddd;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Teacher</h1>

        <?php if ($teacher): ?>
        <form action="edit_teacher.php" method="post">
            <input type="hidden" name="id" value="<?php echo $teacher['teacher_id']; ?>">

            <div class="form-group">
                <label for="name">👤 Name</label>
                <input type="text" id="name" name="name" value="<?php echo $teacher['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" value="<?php echo $teacher['email']; ?>" required>
            </div>

            <div class="form-group">
                <label for="subject">📚 Subject</label>
                <input type="text" id="subject" name="subject" value="<?php echo $teacher['subject']; ?>" required>
            </div>

            <button type="submit" class="btn-primary">Update Teacher</button>
        </form>
        <?php else: ?>
        <p class="error-message">⚠️ Teacher not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>