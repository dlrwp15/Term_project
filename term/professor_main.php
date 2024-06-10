<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'professor') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <title>교수 메인 페이지</title>
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
            overflow-x: hidden;
            /* 가로 스크롤 방지 */
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

        .info-container {
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            font-size: 50px;
            font-weight: 700;
            /* 높이를 화면의 절반으로 설정 */
            height: 50vh;
            background-color: #435EBE;
            /* 모서리를 둥글게 */
            border-radius: 8px;
            /* 하단 여백 */
            margin-bottom: 20px;
            color: white;
        }

        .info-container.active {
            display: flex;
        }

        .slider-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .slider-buttons button {
            padding: 10px;
            margin: 0 5px;
            font-size: 16px;
            cursor: pointer;
            background-color: #435ebe;
            color: #ffffff;
            border: none;
            border-radius: 4px;
        }

        .slider-buttons button:hover {
            background-color: #3a4db7;
        }

        footer {
            display: flex;
            justify-content: center;
            margin: 50px;
            font-size: 20px;
            text-align: center;
        }

        @media (max-width: 750px) {
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

            .info-container {
                font-size: 16px;
                height: 40vh;
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
                background-color: #f1f1f1
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
                <li><a href="#">학생 관리</a></li>
                <li class="dropdown">
                    <a href="javascript:void(0);" class="dropbtn" onclick="toggleDropdown()">시험 &#9662;</a>
                    <ul class="dropdown-content">
                        <li><a href="professor_test.php">시험 관리</a></li>
                        <li><a href="#">시험 조회</a></li>
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
            <div class="info-container active" id="info1">
                텀 프로젝트 - 학적 관리 시스템
            </div>
            <div class="info-container" id="info2">
                6팀 - 정현호, 장익제, 이조운
            </div>
            <div class="info-container" id="info3">
                사용 언어 - PHP, JavaScript, Oracle
            </div>
            <div class="slider-buttons">
                <button onclick="showInfo('info1')">1</button>
                <button onclick="showInfo('info2')">2</button>
                <button onclick="showInfo('info3')">3</button>
            </div>
        </div>
    </div>
    <script>
        function showInfo(infoId) {
            const infos = document.querySelectorAll('.info-container');
            infos.forEach(info => {
                info.classList.remove('active');
            });
            document.getElementById(infoId).classList.add('active');
        }

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