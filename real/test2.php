<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// PHP 변수 설정
$title = "학적 관리 시스템";
$name = $_SESSION['name'];
$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - 메인 페이지</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ffffff;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .sidebar h1 {
            font-size: 24px;
            margin: 0 0 20px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            margin-bottom: 20px;
        }
        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .page-heading h3 {
            color: #333;
            padding: 20px 0;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin: 10px;
            padding: 20px;
        }
        .stats-icon {
            font-size: 48px;
            margin-right: 15px;
        }
        .purple { color: purple; }
        .blue { color: blue; }
        .green { color: green; }
        .red { color: red; }
        .text-muted {
            color: #6c757d;
        }
        .font-semibold {
            font-weight: 600;
        }
        .font-extrabold {
            font-weight: 800;
        }
        .mb-0 {
            margin-bottom: 0;
        }
        .mb-2 {
            margin-bottom: .5rem;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-6, .col-lg-3, .col-md-6, .col-xl-8, .col-xl-4 {
            padding: 10px;
        }
        .col-6 {
            flex: 0 0 50%;
        }
        .col-lg-3 {
            flex: 0 0 25%;
        }
        .col-md-6 {
            flex: 0 0 50%;
        }
        .col-xl-8 {
            flex: 0 0 66.66667%;
        }
        .col-xl-4 {
            flex: 0 0 33.33333%;
        }
        .d-flex {
            display: flex;
        }
        .align-items-center {
            align-items: center;
        }
        .justify-content-start {
            justify-content: flex-start;
        }
        .ms-3 {
            margin-left: 1rem;
        }
        .card-header h4 {
            margin: 0;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            color: #212529;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .user-info span {
            font-size: 18px;
        }
        .logout-button {
            padding: 8px 16px;
            background-color: #d9534f;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .logout-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h1>학적 관리 시스템</h1>
    <ul>
        <li><a href="#">학적</a></li>
        <li><a href="#">성적</a></li>
        <li><a href="#">수강</a></li>
        <li><a href="#">기타</a></li>
    </ul>
</div>
<div class="content">
    <div class="user-info">
        <span>안녕하세요, <?php echo htmlspecialchars($name); ?>님</span>
        <a href="logout.php" class="logout-button">로그아웃</a>
    </div>
    <div class="page-heading">
        <h3>프로필 통계</h3>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>프로필 방문</h4>
        </div>
        <div class="card-body">
            <div id="chart-profile-visit"></div>
        </div>
    </div>
</div>
</body>
</html>
