<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professor') {
    header('Location: login.php');
    exit;
}


include 'db.php';
require_once 'SimpleXLSX.php';
use Shuchkin\SimpleXLSX;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file']['tmp_name'];

        if ($xlsx = SimpleXLSX::parse($file)) {
            if (isset($_POST['course_id'])) {
                $course_id = $_POST['course_id'];
            } else {
                echo "과목 ID가 선택되지 않았습니다.";
                exit;
            }

            $answer_date = date('Y-m-d');

            // 첫 번째 행은 헤더이므로 제외하고 처리
            $rows = $xlsx->rows();
            array_shift($rows);

            foreach ($rows as $row) {
                // 각 셀의 데이터를 문자열로 변환하여 읽기
                $Q1 = isset($row[0]) ? strval($row[0]) : '';
                $Q2 = isset($row[1]) ? strval($row[1]) : '';
                $Q3 = isset($row[2]) ? strval($row[2]) : '';
                $Q4 = isset($row[3]) ? strval($row[3]) : '';
                $Q5 = isset($row[4]) ? strval($row[4]) : '';
                $Q6 = isset($row[5]) ? strval($row[5]) : '';
                $Q7 = isset($row[6]) ? strval($row[6]) : '';
                $Q8 = isset($row[7]) ? strval($row[7]) : '';
                $Q9 = isset($row[8]) ? strval($row[8]) : '';
                $Q10 = isset($row[9]) ? strval($row[9]) : '';
                $student_id = isset($row[10]) ? strval($row[10]) : '';

                echo "Student ID: $student_id, Q1: $Q1, Q2: $Q2, Q3: $Q3, Q4: $Q4, Q5: $Q5, Q6: $Q6, Q7: $Q7, Q8: $Q8, Q9: $Q9, Q10: $Q10\n";

                // student_id가 students 테이블에 존재하는지 확인
                $student_check_sql = "SELECT COUNT(*) FROM students WHERE student_id = ?";
                $student_check_stmt = $conn->prepare($student_check_sql);
                $student_check_stmt->bind_param('s', $student_id);
                $student_check_stmt->execute();
                $student_check_stmt->bind_result($student_count);
                $student_check_stmt->fetch();
                $student_check_stmt->close();

                if ($student_count > 0) {
                    $sql = "INSERT INTO answer (student_id, course_id, answer_date, Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10, type) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sssssssssssss', $student_id, $course_id, $answer_date, $Q1, $Q2, $Q3, $Q4, $Q5, $Q6, $Q7, $Q8, $Q9, $Q10);

                    if ($stmt->execute()) {
                        echo "Inserted: $student_id, $course_id, $answer_date, $Q1, $Q2, $Q3, $Q4, $Q5, $Q6, $Q7, $Q8, $Q9, $Q10\n";
                    } else {
                        echo "Error inserting: " . $stmt->error . "\n";
                    }

                    $stmt->close();
                } else {
                    echo "Student ID $student_id does not exist in the students table.\n";
                }
            }
        } else {
            echo SimpleXLSX::parseError();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>시험 관리</title>
    <style>
        /* 전역 스타일 */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans KR', sans-serif;
            background: #f5f5f5;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex-grow: 1;
            display: flex;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #f0f1f5;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
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

        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: #435ebe;
            color: #ffffff;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .form-group select, .form-group input {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: #435ebe;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #3a4db7;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            padding: 5px;
            border: 1px solid #ccc;
            text-align: center;
            min-width: 50px;
        }

        .table th {
            background-color: #f0f1f5;
        }

        .upload-area {
            width: 100%;
            height: 100px;
            background-color: #d9d9d9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            font-size: 20px;
            color: #333;
            border: 2px dashed #ccc;
        }

        .upload-area.dragover {
            background-color: #e3e3e3;
            border-color: #333;
        }

        .upload-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #435ebe;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }

        .upload-button:hover {
            background-color: #3a4db7;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px;
            }

            .sidebar ul li a {
                font-size: 16px;
            }

            .content {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-group button, .upload-button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h1>6팀</h1>
            <ul>
                <li><a href="professor_main.php">메인페이지</a></li>
                <li><a href="#">수강 관리</a></li>
                <li><a href="#">학적 관리</a></li>
                <li><a href="professor_test.php" class="active">시험 관리</a></li>
                <li><a href="#">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome"><?php echo htmlspecialchars($_SESSION['name']); ?> 교수님 환영합니다.</div>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            <div class="form-group">
                <label for="subject">과목 선택</label>
                <select id="subject" name="course_id">
                    <?php foreach ($courses as $course) : ?>
                        <option value="<?php echo htmlspecialchars($course['course_id']); ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date">날짜 선택</label>
                <input type="date" id="date" name="date" style="width: 99%;">
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>문제</th>
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
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>답안</td>
                            <td><input type="text" name="answer1" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer2" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer3" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer4" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer5" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer6" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer7" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer8" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer9" style="width: 30px;" maxlength="1"></td>
                            <td><input type="text" name="answer10" style="width: 30px;" maxlength="1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="upload-area" id="upload-area">
                    드래그 앤 드롭으로 파일을 업로드하세요
                </div>
                <input type="file" name="file" id="file" style="display: none;">
                <button class="upload-button" type="submit">등록</button>
            </form>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('file');

        uploadArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (event) => {
            event.preventDefault();
            uploadArea.classList.remove('dragover');

            const files = event.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
            }
        });

        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
    </script>
</body>

</html>