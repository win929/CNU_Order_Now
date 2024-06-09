<?php
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

    // GET 파라미터로부터 검색 조건 가져오기
    $foodName = isset($_GET['foodName']) ? $_GET['foodName'] : '';
    $minPrice = !empty($_GET['minPrice']) ? $_GET['minPrice'] : 0;
    $maxPrice = !empty($_GET['maxPrice']) ? $_GET['maxPrice'] : 1000000;

    // SQL 쿼리: 음식 이름과 가격 범위에 따른 검색
    $sql = "SELECT 
                F.foodName,
                F.price
            FROM 
                Food F
            WHERE 
                F.foodName LIKE :foodName
                AND F.price >= :minPrice
                AND F.price <= :maxPrice";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':foodName', '%' . $foodName . '%');
    $stmt->bindValue(':minPrice', $minPrice, PDO::PARAM_INT);
    $stmt->bindValue(':maxPrice', $maxPrice, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 결과를 테이블로 출력
    echo "<table>";
    echo "<tr><th>음식 이름</th><th>가격</th><th>주문</th></tr>";
    foreach ($result as $item) {
        echo "<tr>";
        echo "<td>{$item['FOODNAME']}</td>";
        echo "<td>{$item['PRICE']}원</td>";
        echo "<td><button class='button4' onclick='addToCart(\"{$item['FOODNAME']}\")'>장바구니 담기</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Failed to connect to the database: " . $e->getMessage();
}
?>
