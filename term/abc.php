<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professor') {
    header('Location: login.php');
    exit;
}

$professor_id = isset($_SESSION['id']) ? $_SESSION['id'] : 'default_id';
$sql = "
    SELECT 
        c.course_id, 
        c.course_name, 
        COUNT(g.student_id) AS enrollment_count
    FROM 
        Courses c
    LEFT JOIN 
        Grades g ON c.course_id = g.course_id
    WHERE 
        c.professor_id = :professor_id
    GROUP BY 
        c.course_id, c.course_name
";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":professor_id", $professor_id);
oci_execute($stmt);

$courses = [];
while ($row = oci_fetch_assoc($stmt)) {
    $courses[] = $row;
}
oci_free_statement($stmt);

if ($conn) {
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>강의 관리</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #F2F7FF;
            height: 100vh;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: fixed;
            height: 100vh;
        }

        .sidebar h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 96%;
        }

        .sidebar ul li {
            width: 100%;
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            padding: 10px;
            display: block;
            width: 100%;
            border-radius: 4px;
            text-align: center;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #435ebe;
            color: #ffffff;
            font-weight: bold;
        }

        .dropdown-content {
            display: block;
            padding-left: 20px;
        }

        .dropdown-content a {
            color: #333;
            padding: 10px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover,
        .dropdown-content a.active {
            background-color: #435ebe;
            color: #ffffff;
        }

        .content {
            margin-left: 300px;
            padding: 20px;
            flex-grow: 1;
        }

        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header .welcome {
            font-size: 20px;
        }

        .header .logout-button {
            padding: 8px 16px;
            background-color: #435ebe;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .header .logout-button:hover {
            background-color: #3a4db7;
        }

        .courses-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .courses-table th,
        .courses-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .courses-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        footer {
            text-align: right;
            padding: 10px;
            background-color: #435ebe;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div style="width: 100%; text-align:center;">
                <h1>6팀</h1>
                <h3>학적 관리 시스템</h3>
            </div>
            <ul>
                <li><a href="professor_main.php">메인페이지</a></li>
                <li><a href="#">수강 관리</a></li>
                <li><a href="#">학생 관리</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropbtn" onclick="toggleDropdown()">시험 관리 &#9662;</a>
                    <ul class="dropdown-content">
                        <li><a href="test.php">답안 입력</a></li>
                        <li><a href="test_result.php">시험 결과 조회</a></li>
                    </ul>
                </li>
                <li><a href="grade.php">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome">
                    <?php echo htmlspecialchars($_SESSION['name']); ?> 교수님 환영합니다.
                </div>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            <h2>수강 관리</h2>

            <table class="courses-table">
                <thead>
                    <tr>
                        <th>과목 코드</th>
                        <th>과목명</th>
                        <th>수강 인원</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course) : ?>
                        <tr>
                            <td><?= htmlspecialchars($course['COURSE_ID']); ?></td>
                            <td><?= htmlspecialchars($course['COURSE_NAME']); ?></td>
                            <td><?= htmlspecialchars($course['ENROLLMENT_COUNT']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function toggleDropdown() {
            var dropdownContent = document.querySelector(".dropdown-content");
            var dropdownButton = document.querySelector(".dropdown > .dropbtn");

            if (dropdownContent.style.display === 'block') {
                dropdownContent.style.display = 'none';
                dropdownButton.classList.remove('dropdown-active');
            } else {
                dropdownContent.style.display = 'block';
                dropdownButton.classList.add('dropdown-active');
            }
        }
    </script>
    <footer>
        2024 데이터베이스 실습 / 6팀 텀 프로젝트
    </footer>
</body>

</html>
