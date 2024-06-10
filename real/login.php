<?php
session_start();
include 'db.php'; // 데이터베이스 연결 파일 포함

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $password = $_POST['password'];

    // 테이블이 교수인지 학생인지 확인
    $sql_professor = "SELECT * FROM Professors WHERE professor_id = ?";
    $sql_student = "SELECT * FROM Students WHERE student_id = ?";

    $stmt_professor = $conn->prepare($sql_professor);
    $stmt_professor->bind_param("s", $id);
    $stmt_professor->execute();
    $result_professor = $stmt_professor->get_result();

    if ($row_professor = $result_professor->fetch_assoc()) {
        if (password_verify($password, $row_professor['password'])) {
            $_SESSION['name'] = $row_professor['name'];
            $_SESSION['role'] = $row_professor['role'];
            $_SESSION['id'] = $row_professor['professor_id'];
            $_SESSION['department'] = $row_professor['department'];
            
            header('Location: professor_main.php');
            exit;
        }
    } else {
        $stmt_student = $conn->prepare($sql_student);
        $stmt_student->bind_param("s", $id);
        $stmt_student->execute();
        $result_student = $stmt_student->get_result();

        if ($row_student = $result_student->fetch_assoc()) {
            if (password_verify($password, $row_student['password'])) {
                $_SESSION['name'] = $row_student['name'];
                $_SESSION['role'] = $row_student['role'];
                $_SESSION['id'] = $row_student['student_id'];
                $_SESSION['department'] = $row_student['department'];
                
                header('Location: student_main.php');
                exit;
            }
        } else {
            $error_message = "아이디 또는 비밀번호가 틀렸습니다.";
        }
    }

    $stmt_professor->close();
    if (isset($stmt_student)) $stmt_student->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>학적 관리 시스템</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h1>로그인</h1>
            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="id">아이디</label>
                    <input style="width:90%;" placeholder="아이디" type="text" id="id" name="id" required>
                    <label style="margin-top: 10px;" for="password">비밀번호</label>
                    <input style="width:90%;" placeholder="비밀번호" type="password" id="password" name="password" required>
                </div>
                <?php if (isset($error_message)): ?>
                    <div style="color: red; margin-bottom: 10px;"><?= $error_message; ?></div>
                <?php endif; ?>
                <button style="width:100%;" type="submit" class="login-button">로그인</button>
                <div class="aDiv" style="width: 100%; background-color: red; text-align: left;">
                    <a href="register.php">회원가입</a>
                    <a href="#">비밀번호 찾기</a>
                </div>
            </form>
        </div>
        <div class="login-info">
            <p>아이디는 학번입니다</p>
        </div>
    </div>
</body>
</html>
