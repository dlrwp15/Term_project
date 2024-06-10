<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php'; // 데이터베이스 연결 파일 포함
    $id = $_POST['id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];
    $department = $_POST['department'];
    $role = $_POST['role'];

    if ($role == 'student') {
        $sql = "INSERT INTO Students (student_id, password, name, department, role) VALUES (?, ?, ?, ?, ?)";
    } else if ($role == 'professor') {
        $sql = "INSERT INTO Professors (professor_id, password, name, department, role) VALUES (?, ?, ?, ?, ?)";
    } else {
        echo "Invalid role selected.";
        exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $id, $password, $name, $department, $role); // 's' for string
    
    if ($stmt->execute()) {
        echo "New record created successfully";
        header("Location: login.php"); // Redirect to login page after successful registration
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="post">
            Id: <input type="text" name="id" required><br>
            Password: <input type="password" name="password" required><br>
            Name: <input type="text" name="name" required><br>
            Department: <input type="text" name="department"><br>
            <select name="role">
                <option value="#">-----</option>
                <option value="student">학생</option>
                <option value="professor">교수</option>
            </select><br>
            <input type="submit" value="회원가입">
        </form>
    </div>
</body>
</html>
