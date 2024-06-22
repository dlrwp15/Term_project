<?php
session_start();

include 'db.php';

// 로그인 확인
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// 사용자 정보 가져오기
$user_id = $_SESSION['id'];
$user_role = $_SESSION['role'];

if ($user_role == 'professor') {
    $sql = "SELECT * FROM Professors WHERE professor_id = :id";
} else {
    $sql = "SELECT * FROM Students WHERE student_id = :id";
}

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":id", $user_id);
oci_execute($stmt);

$user = oci_fetch_assoc($stmt);

oci_free_statement($stmt);

// 폼 제출 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $phone_number = $_POST['phone_number'];
    $birth = $_POST['birth'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    if ($user_role == 'professor') {
        $update_sql = "UPDATE Professors SET professor_name = :name, department = :department, phone_number = :phone_number, birth = TO_DATE(:birth, 'YYYY-MM-DD'), address = :address, password = :password WHERE professor_id = :id";
    } else {
        $update_sql = "UPDATE Students SET student_name = :name, department = :department, phone_number = :phone_number, birth = TO_DATE(:birth, 'YYYY-MM-DD'), address = :address, password = :password WHERE student_id = :id";
    }

    $stmt = oci_parse($conn, $update_sql);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":department", $department);
    oci_bind_by_name($stmt, ":phone_number", $phone_number);
    oci_bind_by_name($stmt, ":birth", $birth);
    oci_bind_by_name($stmt, ":address", $address);
    oci_bind_by_name($stmt, ":password", $password);
    oci_bind_by_name($stmt, ":id", $user_id);

    if (oci_execute($stmt)) {
        echo "<script>alert('업데이트 성공'); window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('업데이트 실패');</script>";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>정보 수정</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <div class="profile-container">
        <h1>정보 수정</h1>
        <form action="edit_profile.php" method="post">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['PROFESSOR_NAME'] ?? $user['STUDENT_NAME']); ?>" required>

            <label for="department">학과</label>
            <input type="text" id="department" name="department" value="<?= htmlspecialchars($user['DEPARTMENT']); ?>" required>

            <label for="phone_number">전화번호</label>
            <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['PHONE_NUMBER']); ?>" required>

            <label for="birth">생년월일</label>
            <input type="date" id="birth" name="birth" value="<?= htmlspecialchars($user['BIRTH']); ?>" required>

            <label for="address">주소</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['ADDRESS']); ?>" required>

            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" value="<?= htmlspecialchars($user['PASSWORD']); ?>" required>

            <button type="submit">수정</button>
        </form>
        <a href="profile.php">돌아가기</a>
    </div>
</body>
</html>
