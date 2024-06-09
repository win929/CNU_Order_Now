<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo '로그인이 필요합니다.';
    exit;
}

// Oracle 데이터베이스 연결 정보
$host = 'localhost';
$port = '1521';
$sid = 'XE';
$username = 'd202002529';
$password = '1111';

// PDO DSN 구성
$dsn = "oci:dbname=(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA =(SID = $sid))) ;charset=AL32UTF8";

try {
    // PDO 인스턴스 생성
    $conn = new PDO($dsn, $username, $password);

    $user_id = $_SESSION['user_id'];
    $foodName = $_POST['foodName'];
    $change = (int)$_POST['change'];

    // OrderDetail에서 해당 아이템의 수량 업데이트
    $sql = "UPDATE OrderDetail OD
            SET OD.quantity = OD.quantity + :change,
                OD.totalPrice = (SELECT F.price FROM Food F WHERE F.foodName = OD.foodName) * (OD.quantity + :change)
            WHERE OD.foodName = :foodName
            AND OD.id = (SELECT C.id FROM Cart C WHERE C.cno = :cno)";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':change', $change, PDO::PARAM_INT);
    $stmt->bindValue(':foodName', $foodName);
    $stmt->bindValue(':cno', $user_id);
    $stmt->execute();

    // 수량이 0개인 아이템 삭제
    $sql_delete = "DELETE FROM OrderDetail 
                   WHERE quantity = 0 
                   AND foodName = :foodName 
                   AND id = (SELECT C.id FROM Cart C WHERE C.cno = :cno)";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bindValue(':foodName', $foodName);
    $stmt_delete->bindValue(':cno', $user_id);
    $stmt_delete->execute();

    echo '성공적으로 업데이트되었습니다.';

} catch (PDOException $e) {
    echo "Failed to connect to the database: " . $e->getMessage();
}
?>
