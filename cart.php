<!DOCTYPE html>
<html lang='ko'>
<head>
    <title>장바구니 페이지</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo '사용자: ' . $_SESSION['user_id'];
    } else {
        echo '로그인이 필요합니다.';
        exit;
    }
    ?>
    <div class="button-container">
        <button class="button2" onclick="location.href='menu_page.php'">뒤로가기</button>
        <button id="payButton" class="button2" onclick="updateOrderDateTime()">결제하기</button>
    </div>
    <div class="statistics">
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

            // 사용자의 장바구니 조회
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT OD.foodName, OD.quantity, OD.totalPrice
                    FROM OrderDetail OD
                    JOIN Cart C ON OD.id = C.id
                    WHERE C.cno = :cno
                    AND C.ORDERDATETIME IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':cno', $user_id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalPrice = 0;

            echo "<table>";
            echo "<tr><th>음식 이름</th><th>개수</th><th>가격</th><th>감소</th><th>증가</th></tr>";
            foreach ($result as $item) {
                $totalPrice += $item['TOTALPRICE'];
                echo "<tr>";
                echo "<td>{$item['FOODNAME']}</td>";
                echo "<td>{$item['QUANTITY']}</td>";
                echo "<td>{$item['TOTALPRICE']}원</td>";
                echo "<td><button onclick='updateQuantity(\"{$item['FOODNAME']}\", -1)'>-</button></td>";
                echo "<td><button onclick='updateQuantity(\"{$item['FOODNAME']}\", 1)'>+</button></td>";
                echo "</tr>";
            }
            echo "<tr><td colspan='2'>총 금액</td><td colspan='3'>{$totalPrice}원</td></tr>";
            echo "</table>";

        } catch (PDOException $e) {
            echo "Failed to connect to the database: " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>
