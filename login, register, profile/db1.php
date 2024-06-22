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
$conn = oci_connect('your_username', 'your_password', $tns, 'AL32UTF8');

if (!$conn) {
    $e = oci_error();
    echo "Sorry, we are experiencing technical difficulties.";
    exit;
}
?>
