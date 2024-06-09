<!DOCTYPE html>
<html lang="ko">
<head>
    <title>음식 주문 페이지</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo '사용자: ' . $_SESSION['user_id'];
    }
    ?>
    <div class="button-container">
        <button class="button2" onclick="location.href='menu_page.php'">뒤로가기</button>
        <button class="button2" onclick="location.href='cart.php'">장바구니</button>
    </div>
    <div class="order-container">
        <div class="category-container">
            <!-- 각 카테고리 버튼의 onclick 이벤트에 해당 카테고리를 GET 파라미터로 전달 -->
            <button class="button3" data-category="한식">한식</button>
            <button class="button3" data-category="양식">양식</button>
            <button class="button3" data-category="일식">일식</button>
            <button class="button3" data-category="검색">검색</button>
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

                // 클릭된 카테고리에 따라 음식 목록 조회
                if (isset($_GET['category'])) {
                    if ($_GET['category'] != "검색") {
                        $category = $_GET['category'];
    
                        // SQL 쿼리: 카테고리 별 음식 조회
                        $sql = "SELECT 
                                    F.foodName,
                                    F.price
                                FROM 
                                    Food F
                                INNER JOIN Contains C ON F.foodName = C.foodName    
                                WHERE 
                                    C.categoryName = :category";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':category', $category);
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
                    } else {
                        // 검색어가 입력된 경우
                        echo "<div class='search-container'>";
                        echo "<input type='text' id='foodName' placeholder='음식 이름'>";
                        echo "<input type='text' id='minPrice' placeholder='최소 가격'>";
                        echo "<div id='div1'>~</div>";
                        echo "<input type='text' id='maxPrice' placeholder='최대 가격'>";
                        echo "<button id='searchButton' onclick='searchFood()'>검색</button>";
                        echo "</div>";
    
                        // 검색 버튼 클릭 시 음식 목록 조회
                        echo "<table id='searchResult'>";
                        echo "<tr><th>음식 이름</th><th>가격</th><th>주문</th></tr>";
                        echo "</table>";
                    }
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            ?>
        </div>
    </div>
</body>
</html>
