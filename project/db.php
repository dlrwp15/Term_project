<?php
$servername = "localhost";
$username = "root"; // 올바른 사용자 이름으로 변경
$password = "admin"; // 올바른 비밀번호로 변경
$database = "dbuser155428";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
