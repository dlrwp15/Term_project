<?php
session_start();
session_unset(); // 모든 세션 변수 제거
session_destroy(); // 세션 파기
echo "<script>
        alert('이용해 주셔서 감사합니다.');
        window.location.href = 'login.php';
      </script>";
exit();
