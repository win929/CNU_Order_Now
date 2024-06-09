<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // 데이터베이스 연결 정보
    $host = 'localhost';
    $port = '1521';
    $sid = 'XE';
    $username = 'd202002529';
    $password = '1111';

    // PDO DSN 구성
    $dsn = "oci:dbname=(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA =(SID = $sid))) ;charset=AL32UTF8";

    try {
        $conn = new PDO($dsn, $username, $password);

        // 사용자 입력 날짜 받기
        $start = $_POST['start'];
        $end = $_POST['end'];

        // SQL 쿼리: 날짜 범위에 맞는 주문 조회
        $sql = "SELECT TO_CHAR(c.orderDateTime, 'YYYY-MM-DD') AS order_date, 
                    od.foodName AS food_name, 
                    od.quantity, 
                    od.totalPrice / od.quantity AS price
                FROM OrderDetail od
                JOIN Cart c ON od.id = c.id
                WHERE c.cno = :user_id 
                AND c.orderDateTime BETWEEN TO_DATE(:start_date, 'YYYY-MM-DD') AND TO_DATE(:end_date, 'YYYY-MM-DD') + 1";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute(array(':user_id' => $_SESSION['user_id'], ':start_date' => $start, ':end_date' => $end));

        // 결과를 그룹화된 형태로 저장
        $orders = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $orderDate = $row['ORDER_DATE'];
            $orders[$orderDate][] = $row;
        }

        // 결과를 HTML 테이블로 출력
        echo "<table>";
        echo "<tr><th>날짜</th><th>음식 이름</th><th>개수</th><th>가격</th></tr>";
        foreach ($orders as $orderDate => $orderDetails) {
            $totalPrice = 0;
            $isFirstItem = true; // 날짜를 출력했는지 여부를 추적
            foreach ($orderDetails as $detail) {
                $price = $detail['PRICE'] * $detail['QUANTITY'];
                if ($isFirstItem) {
                    echo "<tr><td>{$orderDate}</td><td>{$detail['FOOD_NAME']}</td><td>{$detail['QUANTITY']}</td><td>{$price}</td></tr>";
                    $isFirstItem = false; // 첫 항목 출력 후, 플래그를 false로 설정
                } else {
                    echo "<tr><td></td><td>{$detail['FOOD_NAME']}</td><td>{$detail['QUANTITY']}</td><td>{$price}</td></tr>";
                }
                $totalPrice += $price;
            }
            echo "<tr><td colspan='3'><strong>총 금액</strong></td><td><strong>{$totalPrice}</strong></td></tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo '오류: ' . $e->getMessage();
    }
} else {
    echo '로그인이 필요합니다.';
}
?>
