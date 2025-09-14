<?php
include 'config.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'teacher') {
    header("Location: ../index.html");
    exit();
}
$teacher_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 30px;
        }

        .dashboard-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 20px;
        }

        h1 {
            color: #333;
            font-size: 2.5em;
            margin: 0;
        }
        
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.1s;
        }

        .logout-btn:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        h2 {
            color: #555;
            font-size: 1.8em;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        
        .form-section {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .availability-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #666;
        }

        input[type="date"],
        input[type="time"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="date"]:focus,
        input[type="time"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            font-weight: bold;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }
        
        .appointments-list {
            list-style-type: none;
            padding: 0;
        }

        .appointment-item {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
        }

        .appointment-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .appointment-details {
            font-size: 1em;
            color: #333;
        }

        .appointment-status {
            font-weight: 600;
        }

        .pending-actions a {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .approve-btn {
            background-color: #007bff;
            color: white;
            margin-right: 5px;
        }

        .approve-btn:hover {
            background-color: #0056b3;
        }

        .cancel-btn {
            background-color: #6c757d;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #5a6268;
        }

        .no-appointments {
            color: #6c757d;
            font-style: italic;
        }

    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome, Teacher!</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="form-section">
            <h2>Set Your Availability</h2>
            <form action="set_schedule.php" method="post" class="availability-form">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" required>
                </div>
                <div class="form-group">
                    <label for="end_time">End Time:</label>
                    <input type="time" id="end_time" name="end_time" required>
                </div>
                <button type="submit">Add Availability</button>
            </form>
        </div>

        <h2>Your Appointments</h2>
        <ul class="appointments-list">
        <?php
        $sql = "SELECT a.appointment_id, s.name as student_name, ta.start_time, ta.end_time, a.status 
                    FROM appointments a
                    JOIN students s ON a.student_id = s.student_id
                    JOIN teacher_availability ta ON a.availability_id = ta.availability_id
                    WHERE a.teacher_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li class='appointment-item'>";
                echo "<div class='appointment-details'>";
                echo "<strong>Student:</strong> " . htmlspecialchars($row['student_name']) . "<br>";
                echo "<strong>Time:</strong> " . htmlspecialchars(date('h:i A', strtotime($row['start_time']))) . " to " . htmlspecialchars(date('h:i A', strtotime($row['end_time']))) . "<br>";
                echo "<strong>Status:</strong> <span class='appointment-status'>" . htmlspecialchars(ucfirst($row['status'])) . "</span>";
                echo "</div>";
                
                if ($row['status'] == 'pending') {
                    echo "<div class='pending-actions'>";
                    echo "<a href='manage_appointment.php?action=approve&id=" . htmlspecialchars($row['appointment_id']) . "' class='approve-btn'>Approve</a>";
                    echo "<a href='manage_appointment.php?action=cancel&id=" . htmlspecialchars($row['appointment_id']) . "' class='cancel-btn'>Cancel</a>";
                    echo "</div>";
                }
                echo "</li>";
            }
        } else {
            echo "<p class='no-appointments'>No appointments found.</p>";
        }
        $stmt->close();
        ?>

<h2>Messages from Students</h2>
<ul class="appointments-list">
    <?php
    // Prepare and execute the query to fetch messages for the logged-in teacher
    $sql_messages = "SELECT m.message_id, m.message_text, m.sent_at, s.name as student_name 
                     FROM messages m
                     JOIN students s ON m.sender_id = s.student_id
                     WHERE m.recipient_id = ?
                     ORDER BY m.sent_at DESC";
    $stmt_messages = $conn->prepare($sql_messages);
    $stmt_messages->bind_param("i", $teacher_id);
    $stmt_messages->execute();
    $result_messages = $stmt_messages->get_result();

    if ($result_messages->num_rows > 0) {
        while ($row_msg = $result_messages->fetch_assoc()) {
            echo "<li class='appointment-item'>";
            echo "<div class='appointment-details'>";
            echo "<strong>From:</strong> " . htmlspecialchars($row_msg['student_name']) . "<br>";
            echo "<strong>Date:</strong> " . htmlspecialchars(date('M d, Y h:i A', strtotime($row_msg['sent_at']))) . "<br>";
            echo "<strong>Message:</strong> " . nl2br(htmlspecialchars($row_msg['message_text']));
            echo "</div>";
            
           
        }
    } else {
        echo "<p class='no-appointments'>No messages found.</p>";
    }
    $stmt_messages->close();
    ?>
</ul>

        </ul>
    </div>
</body>
</html>
