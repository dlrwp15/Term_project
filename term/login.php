<?php
session_start(); // 세션 시작: 사용자의 로그인 상태를 유지하기 위해 사용됩니다.

include 'db.php'; // 데이터베이스 연결 파일 포함: DB 연결을 위한 설정 파일을 포함합니다.

// POST 요청이 있는지 확인
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 사용자로부터 전달받은 ID 저장
    $id = $_POST['id']; 

    // 사용자로부터 전달받은 비밀번호 저장
    $password = $_POST['password']; 

    // 교수용 테이블에서 사용자 조회를 위한 SQL 쿼리
    $sql_professor = "SELECT * FROM Professors WHERE professor_id = ?";
    
    // 학생용 테이블에서 사용자 조회를 위한 SQL 쿼리
    $sql_student = "SELECT * FROM Students WHERE student_id = ?";

    // 교수 정보 조회를 위한 SQL 쿼리 준비 및 실행
    $stmt_professor = $conn->prepare($sql_professor);
    $stmt_professor->bind_param("s", $id);
    $stmt_professor->execute();
    $result_professor = $stmt_professor->get_result();

    // 교수 정보가 존재하는 경우
    if ($row_professor = $result_professor->fetch_assoc()) {
        if (password_verify($password, $row_professor['password'])) { // 비밀번호 검증
            // 세션 변수 설정
            $_SESSION['name'] = $row_professor['name'];
            $_SESSION['role'] = $row_professor['role'];
            $_SESSION['id'] = $row_professor['professor_id'];
            $_SESSION['department'] = $row_professor['department'];
            
            // 교수 메인 페이지로
            header('Location: professor_main.php'); 
            exit;
        }
    } else {
        // 학생 정보 조회를 위한 SQL 쿼리 준비 및 실행
        $stmt_student = $conn->prepare($sql_student);
        $stmt_student->bind_param("s", $id);
        $stmt_student->execute();
        $result_student = $stmt_student->get_result();

        // 학생 정보가 존재하는 경우
        if ($row_student = $result_student->fetch_assoc()) {
            if (password_verify($password, $row_student['password'])) { // 비밀번호 검증
                // 세션 변수 설정
                $_SESSION['name'] = $row_student['name'];
                $_SESSION['role'] = $row_student['role'];
                $_SESSION['id'] = $row_student['student_id'];
                $_SESSION['department'] = $row_student['department'];
                
                 // 학생 메인 페이지로
                header('Location: student_main.php');
                exit;
            }
        } else {
            $error_message = "아이디 또는 비밀번호가 틀렸습니다."; // 로그인 실패 메시지 설정
        }
    }

    // DB 연결 종료
    $stmt_professor->close();
    if (isset($stmt_student)) $stmt_student->close();
    $conn->close();
}
?>
