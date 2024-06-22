<?php
// 데이터베이스 연결 설정
$tns = "
(DESCRIPTION =
    (ADDRESS_LIST =
        (ADDRESS = (PROTOCOL = TCP)(HOST = your_host)(PORT = your_port))
    )
    (CONNECT_DATA =
        (SERVICE_NAME = your_service_name)
    )
)";
$conn = oci_connect('dbuser학번', 'ce1234', 'azza.gwangju.ac.kr/orcl', 'AL32UTF8');

if (!$conn) {
    $e = oci_error();
    echo "연결에 실패하였습니다.";
    exit;
}
?>
