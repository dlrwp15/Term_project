<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';
    $id = $_POST['id'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $role = $_POST['role'];

    if ($role == 'student') {
        $sql = "INSERT INTO Students (student_id, password, student_name, department, role) VALUES (:id, :password, :name, :department, :role)";
    } else if ($role == 'professor') {
        $sql = "INSERT INTO Professors (professor_id, password, professor_name, department, role) VALUES (:id, :password, :name, :department, :role)";
    } else {
        echo "Invalid role selected.";
        exit();
    }

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $id);
    oci_bind_by_name($stmt, ":password", $password);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":department", $department);
    oci_bind_by_name($stmt, ":role", $role);
    
    if (oci_execute($stmt)) {
        echo "New record created successfully";
        header("Location: login.php");
        exit();
    } else {
        $e = oci_error($stmt);
        echo "Error: " . htmlentities($e['message']);
    }
    oci_free_statement($stmt);
    oci_close($conn);
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
