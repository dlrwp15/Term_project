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
$course_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];

    // 학생들의 점수 정보를 가져오기
    $query = "
        SELECT 
            g.student_id, 
            s.student_name, 
            SUM(g.grade) AS total_score, 
            AVG(g.grade) AS average_score,
            RANK() OVER (ORDER BY AVG(g.grade) DESC) AS rank
        FROM 
            Grades g
            JOIN Students s ON g.student_id = s.student_id
        WHERE 
            g.course_id = :course_id 
        GROUP BY 
            g.student_id, s.student_name
        ORDER BY 
            average_score DESC
    ";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":course_id", $course_id);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        // 등급 계산
        $average_score = $row['AVERAGE_SCORE'];
        if ($average_score >= 95) {
            $row['RATING'] = 'A+';
        } elseif ($average_score >= 90) {
            $row['RATING'] = 'A';
        } elseif ($average_score >= 85) {
            $row['RATING'] = 'B+';
        } elseif ($average_score >= 80) {
            $row['RATING'] = 'B';
        } elseif ($average_score >= 75) {
            $row['RATING'] = 'C+';
        } elseif ($average_score >= 70) {
            $row['RATING'] = 'C';
        } elseif ($average_score >= 60) {
            $row['RATING'] = 'D';
        } else {
            $row['RATING'] = 'F';
        }

        $results[] = $row;
    }
    oci_free_statement($stmt);
}

if ($conn) {
    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>성적 조회</title>
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
                    <a href="javascript:void(0);" class="dropbtn">시험 관리 &#9662;</a>
                    <ul class="dropdown-content" style="display: none;">
                        <li><a href="test.php">답안 입력</a></li>
                        <li><a href="test_result.php">시험 결과 조회</a></li>
                    </ul>
                </li>
                <li><a href="grade.php" class="active">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome">
                    <?php echo htmlspecialchars($_SESSION['name']); ?> 교수님
                </div>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            <h2>성적 조회</h2>
            <form action="grade.php" method="post">
                <label for="course_id">과목 선택:</label>
                <select name="course_id" id="course_id">
                    <?php foreach ($courses as $course) : ?>
                        <option value="<?= htmlspecialchars($course['COURSE_ID']); ?>" <?= $course_id == $course['COURSE_ID'] ? 'selected' : ''; ?>><?= htmlspecialchars($course['COURSE_NAME']); ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($courses)) : ?>
                        <option value="">등록된 과목이 없습니다.</option>
                    <?php endif; ?>
                </select>
                <button type="submit">조회</button>
            </form>

            <?php if (!empty($results)) : ?>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>학번</th>
                            <th>이름</th>
                            <th>점수 총합</th>
                            <th>점수 평균</th>
                            <th>등급</th>
                            <th>순위</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result) : ?>
                            <tr>
                                <td><?= htmlspecialchars($result['STUDENT_ID']); ?></td>
                                <td><?= htmlspecialchars($result['STUDENT_NAME']); ?></td>
                                <td><?= htmlspecialchars($result['TOTAL_SCORE']); ?></td>
                                <td><?= round($result['AVERAGE_SCORE'], 2); ?></td>
                                <td><?= htmlspecialchars($result['RATING']); ?></td>
                                <td><?= htmlspecialchars($result['RANK']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
<footer>
    2024 데이터베이스 실습 / 6팀 텀 프로젝트
</footer>
<script>
    document.querySelector(".dropdown .dropbtn").addEventListener("click", function() {
        var dropdownContent = document.querySelector(".dropdown-content");
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    });
</script>

</html>
