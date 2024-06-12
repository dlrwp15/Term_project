<?php
// Oracle 데이터베이스 연결 시도
$conn = oci_connect('dbuser155428', 'ce1234', 'azza.gwangju.ac.kr/orcl', 'AL32UTF8');

// 연결 성공 여부 확인
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// 필요한 경우 사용 후 연결 종료
// oci_close($conn);
?>