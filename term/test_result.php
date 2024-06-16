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

$results = [];
$correct_answers = [];
$answer_date = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $answer_date = $_POST['answer_date'];

    // 정답을 먼저 가져오기
    $answer_query = "
        SELECT Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10
        FROM Answer
        WHERE course_id = :course_id
        AND answer_date = TO_DATE(:answer_date, 'YYYY-MM-DD')
        AND type = '1'
    ";
    $answer_stmt = oci_parse($conn, $answer_query);
    oci_bind_by_name($answer_stmt, ":course_id", $course_id);
    oci_bind_by_name($answer_stmt, ":answer_date", $answer_date);
    oci_execute($answer_stmt);
    $correct_answers = oci_fetch_assoc($answer_stmt);
    oci_free_statement($answer_stmt);

    if ($correct_answers) {
        // 학생들의 답안을 가져오기
        $query = "
            SELECT a.student_id, s.student_name, Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10
            FROM Answer a JOIN Students s ON a.student_id = s.student_id
            WHERE a.course_id = :course_id 
              AND a.answer_date = TO_DATE(:answer_date, 'YYYY-MM-DD')
              AND a.type = '0'
            ORDER BY a.student_id ASC
        ";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":course_id", $course_id);
        oci_bind_by_name($stmt, ":answer_date", $answer_date);
        oci_execute($stmt);

        while ($row = oci_fetch_assoc($stmt)) {
            $results[] = $row;
        }
        oci_free_statement($stmt);
    }
}

// 채점하고 Grades에 삽입하는 코드
if (!empty($results)) {
    foreach ($results as $result) {
        $incorrect_count = 0;
        for ($i = 1; $i <= 10; $i++) {
            if ($result['Q' . $i] != $correct_answers['Q' . $i]) {
                $incorrect_count++;
            }
        }
        $score = (10 - $incorrect_count) * 10;
        if ($score >= 95) {
            $rating = 'A+';
        } elseif ($score >= 90) {
            $rating = 'A';
        } elseif ($score >= 85) {
            $rating = 'B+';
        } elseif ($score >= 80) {
            $rating = 'B';
        } elseif ($score >= 75) {
            $rating = 'C+';
        } elseif ($score >= 70) {
            $rating = 'C';
        } elseif ($score >= 60) {
            $rating = 'D';
        } else {
            $rating = 'F';
        }

        // 중복 데이터 확인 후 삽입
        $check_sql = "SELECT COUNT(*) AS CNT FROM Grades WHERE student_id = :student_id AND course_id = :course_id AND test_date = TO_DATE(:test_date, 'YYYY-MM-DD')";
        $check_stmt = oci_parse($conn, $check_sql);
        oci_bind_by_name($check_stmt, ":student_id", $result['STUDENT_ID']);
        oci_bind_by_name($check_stmt, ":course_id", $course_id);
        oci_bind_by_name($check_stmt, ":test_date", $answer_date);
        oci_execute($check_stmt);
        $check_row = oci_fetch_assoc($check_stmt);

        if ($check_row['CNT'] == 0) {
            $insert_grade_sql = "
                INSERT INTO Grades (student_id, course_id, grade, rating, test_date)
                VALUES (:student_id, :course_id, :grade, :rating, TO_DATE(:test_date, 'YYYY-MM-DD'))
            ";
            $insert_grade_stmt = oci_parse($conn, $insert_grade_sql);
            oci_bind_by_name($insert_grade_stmt, ":student_id", $result['STUDENT_ID']);
            oci_bind_by_name($insert_grade_stmt, ":course_id", $course_id);
            oci_bind_by_name($insert_grade_stmt, ":grade", $score);
            oci_bind_by_name($insert_grade_stmt, ":rating", $rating);
            oci_bind_by_name($insert_grade_stmt, ":test_date", $answer_date);
            oci_execute($insert_grade_stmt);
        }
    }
}

if ($conn) {
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>시험 결과 조회</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans KR', sans-serif;
            background-color: #F2F7FF;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            display: flex;
            flex: 1;
            height: 100%;
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
            height: 100%;
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

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }

        select,
        input[type="date"],
        button {
            width: calc(100% - 16px);
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

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .results-table th,
        .results-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .results-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            cursor: pointer;
        }

        .results-table td.incorrect {
            color: red;
        }

        footer {
            text-align: right;
            padding: 10px;
            background-color: #435ebe;
            color: white;
            margin-top: 30px;
        }
    </style>

    <!-- 정렬하는 스크립트 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableHeaders = document.querySelectorAll('.results-table th.sortable');
            let sortOrder = {};

            tableHeaders.forEach(header => {
                 // 0: 정렬x, 1: 오름차순, -1: 내림차순
                sortOrder[header.innerText] = 0;
                header.addEventListener('click', function() {
                    const column = header.innerText;
                    const rows = Array.from(document.querySelectorAll('.results-table tbody tr:not(.answer-row)'));
                    rows.sort((a, b) => {
                        const aText = a.querySelector(`td[data-column="${column}"]`).innerText;
                        const bText = b.querySelector(`td[data-column="${column}"]`).innerText;
                        if (sortOrder[column] === 0 || sortOrder[column] === -1) {
                            return isNaN(aText - bText) ? aText.localeCompare(bText) : aText - bText;
                        } else {
                            return isNaN(bText - aText) ? bText.localeCompare(aText) : bText - aText;
                        }
                    });
                    sortOrder[column] = sortOrder[column] === 1 ? -1 : 1;
                    const tbody = document.querySelector('.results-table tbody');
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    </script>

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
                    <a href="javascript:void(0);" class="dropbtn dropdown-active" onclick="toggleDropdown()">시험 관리 &#9662;</a> <!-- 드롭다운 메뉴가 기본적으로 활성화된 상태로 설정 -->
                    <ul class="dropdown-content">
                        <li><a href="test.php">답안 입력</a></li>
                        <li><a href="test_result.php" class="active">시험 결과 조회</a></li>
                    </ul>
                </li>
                <li><a href="grade.php">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome">
                    <?php echo htmlspecialchars($_SESSION['name']); ?> 교수님
                </div>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            <h2>시험 결과 조회</h2>
            <form action="test_result.php" method="post">
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
                <button type="submit">조회</button>
            </form>

            <div>
                <h3>시험 날짜: <?= htmlspecialchars($answer_date); ?></h3>
            </div>

            <?php if (!empty($results)) : ?>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th class="sortable">학번</th>
                            <th class="sortable">이름</th>
                            <th>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th>Q5</th>
                            <th>Q6</th>
                            <th>Q7</th>
                            <th>Q8</th>
                            <th>Q9</th>
                            <th>Q10</th>
                            <th class="sortable">틀린 개수</th>
                            <th class="sortable">점수</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="answer-row">
                            <td colspan="2">정답</td>
                            <?php for ($i = 1; $i <= 10; $i++) : ?>
                                <td><?= htmlspecialchars($correct_answers['Q' . $i]); ?></td>
                            <?php endfor; ?>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php foreach ($results as $result) : ?>
                            <tr>
                                <td data-column="학번"><?= htmlspecialchars($result['STUDENT_ID']); ?></td>
                                <td data-column="이름"><?= htmlspecialchars($result['STUDENT_NAME']); ?></td>
                                <?php
                                $incorrect_count = 0;
                                for ($i = 1; $i <= 10; $i++) :
                                    $is_incorrect = $result['Q' . $i] != $correct_answers['Q' . $i];
                                    if ($is_incorrect) {
                                        $incorrect_count++;
                                    }
                                ?>
                                    <td class="<?= $is_incorrect ? 'incorrect' : ''; ?>">
                                        <?= htmlspecialchars($result['Q' . $i]); ?>
                                    </td>
                                <?php endfor; ?>
                                <td data-column="틀린 개수"><?= $incorrect_count; ?></td>
                                <td data-column="점수"><?= (10 - $incorrect_count) * 10; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
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
</body>
<footer>
    2024 데이터베이스 실습 / 6팀 텀 프로젝트
</footer>

</html>