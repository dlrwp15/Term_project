<?php
session_start(); // 세션 시작

include 'db.php'; // 데이터베이스 연결 파일 포함

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

if ($user_role == 'professor') {
    $user = oci_fetch_assoc($stmt);
} else {
    $user = oci_fetch_assoc($stmt);
}

oci_free_statement($stmt);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사용자 정보 확인</title>
    <link rel="stylesheet" href="styles1.css">
</head>
<body>
    <div class="profile-container">
        <h1>사용자 정보</h1>
        <table>
            <tr>
                <th>아이디</th>
                <td><?= htmlspecialchars($user_role == 'professor' ? $user['PROFESSOR_ID'] : $user['STUDENT_ID']); ?></td>
            </tr>
            <tr>
                <th>이름</th>
                <td><?= htmlspecialchars($user_role == 'professor' ? $user['PROFESSOR_NAME'] : $user['STUDENT_NAME']); ?></td>
            </tr>
            <tr>
                <th>학과</th>
                <td><?= htmlspecialchars($user['DEPARTMENT']); ?></td>
            </tr>
            <tr>
                <th>역할</th>
                <td><?= htmlspecialchars($user['ROLE']); ?></td>
            </tr>
            <tr>
                <th>전화번호</th>
                <td><?= htmlspecialchars($user['PHONE_NUMBER']); ?></td>
            </tr>
            <tr>
                <th>생년월일</th>
                <td><?= htmlspecialchars($user['BIRTH']); ?></td>
            </tr>
            <tr>
                <th>주소</th>
                <td><?= htmlspecialchars($user['ADDRESS']); ?></td>
            </tr>
        </table>
        <a href="edit_profile.php">정보 수정</a>
        <a href="logout.php">로그아웃</a>
    </div>
</body>
</html>
