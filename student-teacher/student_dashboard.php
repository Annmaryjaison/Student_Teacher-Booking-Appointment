<?php
include 'config.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'student') {
    header("Location: ../index.html");
    exit();
}
$student_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
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

        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        input[type="text"] {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
            color: #333;
        }

        .appointment-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .not-found {
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome, Student!</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="form-section">
            <h2>Find a Teacher</h2>
            <form id="searchForm" class="search-form">
                <input type="text" id="searchTeacher" placeholder="Search by name or subject">
                <button type="submit">Search</button>
            </form>
            <div id="searchResults"></div>
        </div>
        
        <h2>Your Appointments</h2>
        <ul class="appointments-list">
            <?php
            $sql = "SELECT a.status, t.name AS teacher_name, ta.start_time, ta.end_time 
                    FROM appointments a
                    JOIN teachers t ON a.teacher_id = t.teacher_id
                    JOIN teacher_availability ta ON a.availability_id = ta.availability_id
                    WHERE a.student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<li class='appointment-item'>";
                    echo "<strong>Teacher:</strong> " . htmlspecialchars($row['teacher_name']) . "<br>";
                    echo "<strong>Time:</strong> " . htmlspecialchars(date('h:i A', strtotime($row['start_time']))) . " to " . htmlspecialchars(date('h:i A', strtotime($row['end_time']))) . "<br>";
                    echo "<strong>Status:</strong> <span class='appointment-status'>" . htmlspecialchars(ucfirst($row['status'])) . "</span>";
                    echo "</li>";
                }
            } else {
                echo "<li class='no-appointments'>No appointments found.</li>";
            }
            $stmt->close();
            ?>

<h2>Send a Message</h2>
    <div class="form-section">
        <form action="send_message.php" method="post">
            <label for="teacher_id" style="font-weight: bold; display: block; margin-bottom: 5px;">To:</label>
            <select name="teacher_id" id="teacher_id" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd; margin-bottom: 15px;" required>
                <option value="">Select a Teacher</option>
                <?php
                // Fetch and populate teacher list
                $sql_teachers = "SELECT teacher_id, name FROM teachers ORDER BY name";
                $result_teachers = $conn->query($sql_teachers);
                if ($result_teachers->num_rows > 0) {
                    while($row_teacher = $result_teachers->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row_teacher['teacher_id']) . "'>" . htmlspecialchars($row_teacher['name']) . "</option>";
                    }
                }
                ?>
            </select>

            <label for="message_text" style="font-weight: bold; display: block; margin-bottom: 5px;">Message:</label>
            <textarea name="message_text" id="message_text" rows="5" placeholder="Type your message here..." style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd; resize: vertical; box-sizing: border-box;"></textarea>

            <button type="submit" style="background-color: #007bff; color: white; padding: 12px 25px; border: none; border-radius: 6px; font-size: 1.1em; cursor: pointer; transition: background-color 0.3s, transform 0.1s; font-weight: bold; margin-top: 15px;">Send Message</button>
        </form>
    </div>
    
    <h2>Sent Messages</h2>
    <ul class="appointments-list">
        <?php
        // Fetch and display sent messages
        $sql_sent = "SELECT m.message_text, m.sent_at, t.name AS teacher_name 
                     FROM messages m
                     JOIN teachers t ON m.recipient_id = t.teacher_id
                     WHERE m.sender_id = ?
                     ORDER BY m.sent_at DESC";
        $stmt_sent = $conn->prepare($sql_sent);
        $stmt_sent->bind_param("i", $student_id);
        $stmt_sent->execute();
        $result_sent = $stmt_sent->get_result();

        if ($result_sent->num_rows > 0) {
            while ($row_sent = $result_sent->fetch_assoc()) {
                echo "<li class='appointment-item'>";
                echo "<strong>To:</strong> " . htmlspecialchars($row_sent['teacher_name']) . "<br>";
                echo "<strong>Date:</strong> " . htmlspecialchars(date('M d, Y h:i A', strtotime($row_sent['sent_at']))) . "<br>";
                echo "<strong>Message:</strong> " . nl2br(htmlspecialchars($row_sent['message_text']));
                echo "</li>";
            }
        } else {
            echo "<li class='no-appointments'>You have not sent any messages yet.</li>";
        }
        $stmt_sent->close();
        ?>
    

        </ul>
        
        <script src="student.js"></script>
    </div>
</body>
<?php
if (isset($_GET['message'])) {
    if ($_GET['message'] == 'success') {
        echo '<div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">Message sent successfully!</div>';
    } elseif ($_GET['message'] == 'error') {
        echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">Error sending message. Please try again.</div>';
    }
}
?>
</html>
