<!DOCTYPE html>
<html>
<head>
 
 </head>
<body>
<div class="banner">
            <ul>
                    <li><a href="#"><img src="img/1.png" alt="dane" width=950px height=320px></a></li>
                    <li><a href="#"><img src="img/2.jpg" alt="dane" width=950px height=320px></a></li>
                    <li><a href="#"><img src="img/3.jpg" alt="dane" width=950px height=320px></a></li>
                    <li><a href="#"><img src="img/4.jpg" alt="dane" width=950px height=320px></a></li>
                    <li><a href="#"><img src="img/5.jpg" alt="dane" width=950px height=320px></a></li>
            </ul>
        </div>    
        <div id="main_content">
            <div id="latest">
                <h4>최근 게시글</h4>
                <ul>
<!-- 최근 게시 글 DB에서 불러오기 -->
<?php
    $conn = oci_connect("dbuser154135", "ce1234", "192.168.1.3/orcl","AL32UTF8");
    $sql = "select ROW_NUMBER() OVER (ORDER BY r.renum), r.* from review r where rownum <= 5 order by renum desc";
	$stid = oci_parse($conn, $sql);
	oci_execute($stid);
	

    if (!$stid)
        echo "게시판 DB 테이블(board)이 생성 전이거나 아직 게시글이 없습니다!";
    else
    {
?>
		         <li>
					<span>번호</span>
					<span>제목</span>
					<span>작성자</span>
					<span>등록일</span>
				</li>
<?php
        while( $row = oci_fetch_array($stid) )
        {	
            $regist_day = substr($row["REGIST_DAY"], 0, 10);
?>
				<li>
                    <span><?=$row[0]?></span>	
                    <span><?=$row[2]?></span>
					<span><?=$row[6]?></span>
                    <span><?=$regist_day?></span>
                </li>
<?php
        }
    }
?>
            </div>
            <div id="point_rank">
                <h4>게임 평점 랭킹</h4>
                <ul>

<!--<?php
    $rank = 1;
    $sql = "select * from  where rownum <= 5 order by  desc";
    $stid = oci_parse($conn, $sql);
	oci_execute($stid);

    if (!$stid)
        echo "게임 DB 테이블(grade)에 데이터가 없습니다.!";
    else
    {
        while( $row = oci_fetch_array($stid) )
        {
            $name  = $row["name"];        
            $id    = $row["id"];
            $point = $row["point"];
            $name = mb_substr($name, 0, 1)." * ".mb_substr($name, 2, 1);
?>
                <li>
                    <span><?=$rank?></span>
                    <span><?=$name?></span>
                    <span><?=$id?></span>
                    <span><?=$point?></span>
                </li>
<?php
            $rank++;
        }
    }

    oci_close($conn);
?>-->
                </ul>
            </div>
        </div>
    </body>
</html>