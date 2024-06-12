<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professor') {
    header('Location: login.php');
    exit;
}

include 'db.php';

$professor_id = $_SESSION['id'];

$sql = "SELECT course_id, course_name FROM courses WHERE professor_id = :professor_id";

$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":professor_id", $professor_id);
oci_execute($stmt);

$courses = [];
while ($row = oci_fetch_assoc($stmt)) {
    $courses[] = $row;
}

oci_free_statement($stmt);
oci_close($conn);
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>시험 관리</title>
    <style>
        /* 전역 스타일 */
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: 'Noto Sans KR', sans-serif;
            background: #F2F7FF;
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
            background-color: #ffffff;
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
            width: 92%;
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

        .form-group select,
        .form-group input {
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

        .table th,
        .table td {
            padding: 5px;
            border: 1px solid #ccc;
            text-align: center;
            min-width: 50px;
        }

        .table th {
            background-color: #ffffff;
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

            .form-group button,
            .upload-button {
                width: 100%;
                text-align: center;
            }


            .dropdown-content {
                display: none;
                position: absolute;
                background-color: #ffffff;
                min-width: 160px;
                box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
                z-index: 1;
            }

            .dropdown-content a {
                color: black;
                padding: 12px 16px;
                text-decoration: none;
                display: block;
            }

            .dropdown-content a:hover {
                background-color: #3a4db7
            }

            .dropdown:hover .dropdown-content {
                display: block;
            }

            .dropdown:hover .dropbtn {
                background-color: #3a4db7;
                color: white;
            }

            .dropdown-active {
                /* 펼쳐졌을 때의 배경색 */
                background-color: #3a4db7;
                /* 텍스트 색상 */
                color: white;
            }

        }

    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div style="width: 100%; text-align:center; ">
                <h1>6팀</h1>
                <h3>학적 관리 시스템</h3>
            </div>
            <ul>
                <li><a href="professor_main.php">메인페이지</a></li>
                <li><a href="#">수강 관리</a></li>
                <li><a href="#">학적 관리</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropbtn" onclick="toggleDropdown()">시험 관리 &#9662;</a>
                    <ul class="dropdown-content">
                        <li><a href="professor_test.php">답안 채점</a></li>
                        <li><a href="#">시험 결과 조회</a></li>
                    </ul>
                </li>
                <li><a href="#">성적 조회</a></li>
            </ul>
        </div>
        <div class="content">
            <div class="header">
                <div class="welcome"><?php echo htmlspecialchars($_SESSION['name']); ?> 교수님 환영합니다.</div>
                <a href="logout.php" class="logout-button">로그아웃</a>
            </div>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="subject">과목 선택</label>
                    <select name="course_id" id="subject">
                        <?php foreach ($courses as $course) : ?>
                            <option value="<?php echo htmlspecialchars($course['course_id']); ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">날짜 선택</label>
                    <input type="date" id="date" name="answer_date" style="width: 99%;">
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

        document.addEventListener('DOMContentLoaded', (event) => {
            const inputs = document.querySelectorAll('input[type="text"]');
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (input.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });
            });
        });


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
            dropdownContent.style.display = 'none';
            dropdownButton.classList.remove('dropdown-active');
        }

    </script>
</body>

</html>