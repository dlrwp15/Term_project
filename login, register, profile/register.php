<?php
include 'db.php'; // 데이터베이스 연결 파일 포함

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 비밀번호 해시화
    $department = $_POST['department'];
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $birth = $_POST['birth'];
    $address = $_POST['address'];
    $role = $_POST['role'];

    if ($role == 'student') {
        $sql = "INSERT INTO Students (student_id, password, department, student_name, role, phone_number, birth, address) VALUES (:id, :password, :department, :name, :role, :phone_number, TO_DATE(:birth, 'YYYY-MM-DD'), :address)";
    } else if ($role == 'professor') {
        $sql = "INSERT INTO Professors (professor_id, password, department, professor_name, role, phone_number, birth, address) VALUES (:id, :password, :department, :name, :role, :phone_number, TO_DATE(:birth, 'YYYY-MM-DD'), :address)";
    }

    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_bind_by_name($stmt, ':password', $password);
    oci_bind_by_name($stmt, ':department', $department);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':role', $role);
    oci_bind_by_name($stmt, ':phone_number', $phone_number);
    oci_bind_by_name($stmt, ':birth', $birth);
    oci_bind_by_name($stmt, ':address', $address);

    if (oci_execute($stmt)) {
        $success_message = "회원가입이 완료되었습니다!";
    } else {
        $error_message = "회원가입에 실패했습니다. 다시 시도해 주세요.";
    }

    oci_free_statement($stmt);
}

oci_close($conn);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>회원가입</h2>
        <?php if (isset($success_message)): ?>
            <div style="color: green;"><?= $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div style="color: red;"><?= $error_message; ?></div>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div class="input-group">
                <label for="role">역할</label>
                <select id="role" name="role" required>
                    <option value="student">학생</option>
                    <option value="professor">교수</option>
                </select>
            </div>
            <div class="input-group">
                <label for="id">아이디</label>
                <input type="text" id="id" name="id" required>
            </div>
            <div class="input-group">
                <label for="password">비밀번호</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="department">학과</label>
                <input type="text" id="department" name="department" required>
            </div>
            <div class="input-group">
                <label for="name">이름</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-group">
                <label for="phone_number">전화번호</label>
                <input type="text" id="phone_number" name="phone_number" required>
            </div>
            <div class="input-group">
                <label for="birth">생년월일</label>
                <input type="date" id="birth" name="birth" required>
            </div>
            <div class="input-group">
                <label for="address">주소</label>
                <input type="text" id="address" name="address" required>
            </div>
            <button type="submit">회원가입</button>
        </form>
        <div>
            <a href="login.php">로그인 페이지로 돌아가기</a>
        </div>
    </div>
</body>
</html>
