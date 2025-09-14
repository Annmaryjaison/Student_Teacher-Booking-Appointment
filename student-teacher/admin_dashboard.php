<?php
include 'config.php';
checkUserAccess([USER_TYPE_ADMIN]);?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f7fa;
      margin: 0;
      padding: 20px;
      color: #333;
    }

    h1 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 10px;
    }

    a.logout {
      display: block;
      text-align: center;
      margin-bottom: 30px;
      color: #dee2e3ff;
      text-decoration: none;
      font-weight: bold;
    }

    .container {
      max-width: 1000px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    h2 {
      color: #34495e;
      border-bottom: 2px solid #ecf0f1;
      padding-bottom: 5px;
    }

    h3 {
      margin-top: 20px;
      color: #2980b9;
    }

    form input[type="text"],
    form input[type="email"],
    form input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    form button {
      padding: 10px 20px;
      background-color: #27ae60;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    form button:hover {
      background-color: #219150;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    table th, table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    table th {
      background-color: #ecf0f1;
      color: #2c3e50;
    }

    table tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    table a {
      color: #3498db;
      text-decoration: none;
      font-weight: bold;
    }

    table a:hover {
      text-decoration: underline;
    }
    .header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.header h1 {
  margin: 0;
}

.logout {
  background-color: #e74c3c;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
}


.logout:hover {
  background-color: #c0392b;
}
  </style>
</head>
<body>
  
  <div class="header">
  <h1>Admin Dashboard</h1>
  
</div>

  <div class="container">
    <div id="manageTeachers">
      <h2>Manage Teachers</h2>
      <h3>Add New Teacher</h3>
      <form action="manage_teachers.php" method="post">
        <input type="hidden" name="action" value="add" />
        <input type="text" name="name" placeholder="Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="text" name="subject" placeholder="Subject" required />
        <button type="submit">Add Teacher</button>
      </form>

      <h3>Existing Teachers</h3>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT * FROM teachers");
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['name'] . "</td>";
              echo "<td>" . $row['email'] . "</td>";
              echo "<td>" . $row['subject'] . "</td>";
              echo "<td>
                      <a href='edit_teacher.php?id=" . $row['teacher_id'] . "'>Edit</a> |
                      <a href='manage_teachers.php?action=delete&id=" . $row['teacher_id'] . "'>Delete</a>
                    </td>";
              echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <div id="approveStudents">
      <h2>Student Registration Approval</h2>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT * FROM students WHERE status = 'pending'");
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['name'] . "</td>";
              echo "<td>" . $row['email'] . "</td>";
              echo "<td>" . $row['status'] . "</td>";
              echo "<td>
                      <a href='approve_student.php?action=approve&id=" . $row['student_id'] . "'>Approve</a> |
                      <a href='approve_student.php?action=reject&id=" . $row['student_id'] . "'>Reject</a>
                    </td>";
              echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <div>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</body>

</html>
    