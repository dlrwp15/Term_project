<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professor') {
    header('Location: login.php');
    exit;
}

$professor_id = isset($_SESSION['id']) ? $_SESSION['id'] : 'default_id';
$sql = "SELECT course_id, course_name FROM courses WHERE professor_id = :professor_id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":professor_id", $professor_id);
oci_execute($stmt);

$courses = [];
while ($row = oci_fetch_assoc($stmt)) {
    $courses[] = $row;
}
oci_free_statement($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $handle = fopen($file, 'r');
    fgetcsv($handle, 1000, ','); // 첫 행 건너뛰기

    $course_id = $_POST['course_id'];
    $answer_date = $_POST['answer_date'];

    while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
        $student_id = array_shift($data);
        $answers = $data;

        // 중복 데이터 확인
        $check_sql = "SELECT COUNT(*) AS cnt FROM Answer WHERE student_id = :student_id AND course_id = :course_id AND answer_date = TO_DATE(:answer_date, 'YYYY-MM-DD')";
        $check_stmt = oci_parse($conn, $check_sql);
        oci_bind_by_name($check_stmt, ":student_id", $student_id);
        oci_bind_by_name($check_stmt, ":course_id", $course_id);
        oci_bind_by_name($check_stmt, ":answer_date", $answer_date);
        oci_execute($check_stmt);
        $row = oci_fetch_array($check_stmt);

        if ($row['CNT'] == 0) {
            // 데이터 삽입
            $insert_sql = "INSERT INTO Answer (student_id, course_id, answer_date, Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10) VALUES (:student_id, :course_id, TO_DATE(:answer_date, 'YYYY-MM-DD'), :Q1, :Q2, :Q3, :Q4, :Q5, :Q6, :Q7, :Q8, :Q9, :Q10)";
            $insert_stmt = oci_parse($conn, $insert_sql);
            oci_bind_by_name($insert_stmt, ":student_id", $student_id);
            oci_bind_by_name($insert_stmt, ":course_id", $course_id);
            oci_bind_by_name($insert_stmt, ":answer_date", $answer_date);
            for ($i = 0; $i < 10; $i++) {
                oci_bind_by_name($insert_stmt, ":Q" . ($i + 1), $answers[$i], -1, SQLT_INT);
            }

            oci_execute($insert_stmt);
        }
    }
    fclose($handle);
    oci_close($conn);
}

if ($conn) {
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>답안 채점</title>
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
            width: 100%;
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
            position: relative;
            background-color: #ffffff;
            width: 100%;
            box-shadow: none;
            padding-left: 10px;
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
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }

        select,
        input,
        button {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #45a049;
        }

        .logout-link {
            display: block;
            text-align: right;
            margin-top: 20px;
        }

        #course_id {
            font-size: medium;
            font-weight: 600;
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
                    <a href="javascript:void(0);" class="dropbtn dropdown-active" onclick="toggleDropdown()">시험 관리 &#9662;</a>
                    <ul class="dropdown-content">
                        <li><a href="test.php" class="active">답안 채점</a></li>
                        <li><a href="#">시험 결과 조회</a></li>
                    </ul>
                </li>
                <li><a href="#">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <a href="logout.php" class="logout-link">로그아웃</a>
            <h2>과목과 CSV 파일을 선택하세요</h2>
            <form action="test.php" method="post" enctype="multipart/form-data">
                <label for="course_id">과목 선택:</label>
                <select name="course_id" id="course_id">
                    <?php foreach ($courses as $course) : ?>
                        <option value="<?= htmlspecialchars($course['COURSE_ID']); ?>"><?= htmlspecialchars($course['COURSE_NAME']); ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($courses)) : ?>
                        <option value="">등록된 과목이 없습니다.</option>
                    <?php endif; ?>
                </select>
                <label for="date">날짜 선택:</label>
                <input type="date" id="date" name="answer_date" required>
                <label for="file">CSV 파일 업로드:</label>
                <input type="file" name="file" id="file" required>
                <button type="submit">등록</button>
            </form>
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

        // 페이지 로드 시 드롭다운 초기화
        window.onload = function() {
            var dropdownContent = document.querySelector(".dropdown-content");
            var dropdownButton = document.querySelector(".dropdown > .dropbtn");

            // 초기 상태에서 드롭다운을 닫고, 색상 변경 클래스를 제거
            dropdownContent.style.display = 'block';
            dropdownButton.classList.add('dropdown-active');
        }
    </script>

</body>

</html>
