<?php
session_start();
if(isset($_SESSION['user_id'])) {
    // 데이터베이스 연결 정보
    $host = 'localhost';
    $port = '1521';
    $sid = 'XE';
    $username = 'd202002529';
    $password = '1111';
    $dsn = "oci:dbname=(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA =(SID = $sid))) ;charset=AL32UTF8";

    try {
        $conn = new PDO($dsn, $username, $password);
        // 현재 시간
        $currentDateTime = date('Y-m-d H:i:s');
        // SQL 쿼리: 현재 로그인한 사용자의 Cart 테이블의 Orderdatetime을 현재 시간으로 업데이트
        $sql = "UPDATE Cart SET ORDERDATETIME = TO_DATE(:currentDateTime, 'YYYY-MM-DD HH24:MI:SS') WHERE cno = :cno AND ORDERDATETIME IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':currentDateTime', $currentDateTime);
        $stmt->bindValue(':cno', $_SESSION['user_id']);
        $stmt->execute();
        
        echo "주문이 완료되었습니다.";
    } catch(PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "로그인이 필요합니다.";
}
?>
