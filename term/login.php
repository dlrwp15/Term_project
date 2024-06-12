<?php
session_start(); // 세션 시작

include 'db.php'; // 데이터베이스 연결 파일 포함

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $password = $_POST['password'];

    // 교수 정보 조회를 위한 쿼리 준비
    $sql_professor = "SELECT * FROM Professors WHERE professor_id = :id";
    $stmt_professor = oci_parse($conn, $sql_professor);
    oci_bind_by_name($stmt_professor, ":id", $id);
    oci_execute($stmt_professor);

    // 교수 정보 검증
    if ($row_professor = oci_fetch_assoc($stmt_professor)) {
        if ($password === $row_professor['PASSWORD']) {
            $_SESSION['name'] = $row_professor['PROFESSOR_NAME'];
            $_SESSION['role'] = $row_professor['ROLE'];
            $_SESSION['id'] = $row_professor['PROFESSOR_ID'];
            $_SESSION['department'] = $row_professor['DEPARTMENT'];
            header('Location: professor_main.php');
            exit;
        }
    }

    // 학생 정보 조회를 위한 쿼리 준비
    $sql_student = "SELECT * FROM Students WHERE student_id = :id";
    $stmt_student = oci_parse($conn, $sql_student);
    oci_bind_by_name($stmt_student, ":id", $id);
    oci_execute($stmt_student);

    // 학생 정보 검증
    if ($row_student = oci_fetch_assoc($stmt_student)) {
        if ($password === $row_student['PASSWORD']) {
            $_SESSION['name'] = $row_student['STUDENT_NAME'];
            $_SESSION['role'] = $row_student['ROLE'];
            $_SESSION['id'] = $row_student['STUDENT_ID'];
            $_SESSION['department'] = $row_student['DEPARTMENT'];
            header('Location: student_main.php');
            exit;
        }
    } else {
        $error_message = "아이디 또는 비밀번호가 틀렸습니다.";
    }

    oci_free_statement($stmt_professor);
    if (isset($stmt_student)) {
        oci_free_statement($stmt_student);
    }
}

oci_close($conn);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>학적 관리 시스템 로그인</title>
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
                <div class="aDiv" style="width: 100%; text-align: left; font-size: 10px">
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
