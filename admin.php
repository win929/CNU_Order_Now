<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>관리 페이지</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>통계 정보</h1>
        <div class="statistics">
            <h2>카테고리 별 주문 음식 Top 3</h2>
            <!-- 순위, 한식, 양식, 일식 순으로 테이블 생성 -->
            <?php
            // Oracle 데이터베이스 연결 정보
            $host = 'localhost'; // 데이터베이스 호스트
            $port = '1521'; // Oracle 포트
            $sid = 'XE'; // Oracle SID
            $username = 'd202002529'; // 사용자 이름
            $password = '1111'; // 비밀번호

            // PDO DSN 구성
            $dsn = "oci:dbname=(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA =(SID = $sid))) ;charset=AL32UTF8";

            try {
                // PDO 인스턴스 생성
                $conn = new PDO($dsn, $username, $password);

                // SQL 쿼리: 카테고리 별로 주문한 음식 Top 3 조회
                $sql = "WITH FoodCategoryOrder AS (
                            SELECT 
                                C.categoryName,
                                OD.foodName,
                                SUM(OD.quantity) AS totalQuantity,
                                ROW_NUMBER() OVER (PARTITION BY C.categoryName ORDER BY SUM(OD.quantity) DESC) AS rank
                            FROM 
                                OrderDetail OD
                            JOIN 
                                Contains C ON OD.foodName = C.foodName
                            GROUP BY 
                                C.categoryName, OD.foodName
                        )
                        SELECT 
                            categoryName,
                            foodName,
                            totalQuantity
                        FROM 
                            FoodCategoryOrder
                        WHERE 
                            rank <= 3
                        ORDER BY 
                            categoryName, rank";
                $stmt = $conn->prepare($sql);

                // 쿼리 실행
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 카테고리별로 데이터를 분류합니다.
                $categories = [];
                foreach ($result as $item) {
                    $category = $item['CATEGORYNAME'];
                    if (!isset($categories[$category])) {
                        $categories[$category] = [];
                    }
                    $categories[$category][] = $item;
                }

                // 각 카테고리 내에서 주문 수량 기준으로 정렬하고 상위 3개 항목을 추출합니다.
                foreach ($categories as $category => $items) {
                    usort($items, function ($a, $b) {
                        return $b['TOTALQUANTITY'] - $a['TOTALQUANTITY'];
                    });
                    $categories[$category] = array_slice($items, 0, 3);
                }

                // 결과를 출력합니다.
                echo "<table border='1'>";
                echo "<tr><th>순위</th><th>한식</th><th>양식</th><th>일식</th></tr>";

                for ($i = 0; $i < 3; $i++) {
                    echo "<tr>";
                    echo "<td>" . ($i + 1) . "</td>";
                    echo "<td>" . ($categories['한식'][$i]['FOODNAME'] ?? '') . (!empty($categories['한식'][$i]['TOTALQUANTITY']) ? " (" . $categories['한식'][$i]['TOTALQUANTITY'] . "회)" : '') . "</td>";
                    echo "<td>" . ($categories['양식'][$i]['FOODNAME'] ?? '') . (!empty($categories['양식'][$i]['TOTALQUANTITY']) ? " (" . $categories['양식'][$i]['TOTALQUANTITY'] . "회)" : '') . "</td>";
                    echo "<td>" . ($categories['일식'][$i]['FOODNAME'] ?? '') . (!empty($categories['일식'][$i]['TOTALQUANTITY']) ? " (" . $categories['일식'][$i]['TOTALQUANTITY'] . "회)" : '') . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
            ?>
        </div>
        <div class="statistics">
            <h2>카테고리 별 매출 음식 Top 3</h2>
            <!-- 순위, 한식, 양식, 일식 순으로 테이블 생성 -->
            <?php
            try {
                // SQL 쿼리: 카테고리 별로 주문한 음식의 매출 Top 3 조회
                $sql = "WITH FoodCategoryRevenue AS (
                            SELECT 
                                C.categoryName,
                                OD.foodName,
                                SUM(OD.quantity * F.price) AS totalRevenue,
                                ROW_NUMBER() OVER (PARTITION BY C.categoryName ORDER BY SUM(OD.quantity * F.price) DESC) AS rank
                            FROM 
                                OrderDetail OD
                            JOIN 
                                Contains C ON OD.foodName = C.foodName
                            JOIN 
                                Food F ON OD.foodName = F.foodName
                            GROUP BY 
                                C.categoryName, OD.foodName
                        )
                        SELECT 
                            categoryName,
                            foodName,
                            totalRevenue
                        FROM 
                            FoodCategoryRevenue
                        WHERE 
                            rank <= 3
                        ORDER BY 
                            categoryName, rank";
                $stmt = $conn->prepare($sql);

                // 쿼리 실행
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // 카테고리별로 데이터를 분류합니다.
                $categories = [];
                foreach ($result as $item) {
                    $category = $item['CATEGORYNAME'];
                    if (!isset($categories[$category])) {
                        $categories[$category] = [];
                    }
                    $categories[$category][] = $item;
                }

                // 각 카테고리 내에서 매출 기준으로 정렬하고 상위 3개 항목을 추출합니다.
                foreach ($categories as $category => $items) {
                    usort($items, function ($a, $b) {
                        return $b['TOTALREVENUE'] - $a['TOTALREVENUE'];
                    });
                    $categories[$category] = array_slice($items, 0, 3);
                }

                // 결과를 출력합니다.
                echo "<table border='1'>";
                echo "<tr><th>순위</th><th>한식</th><th>양식</th><th>일식</th></tr>";

                for ($i = 0; $i < 3; $i++) {
                    echo "<tr>";
                    echo "<td>" . ($i + 1) . "</td>";
                    echo "<td>" . (!empty($categories['한식'][$i]) ? $categories['한식'][$i]['FOODNAME'] . " (" . number_format($categories['한식'][$i]['TOTALREVENUE']) . "원)" : '') . "</td>";
                    echo "<td>" . (!empty($categories['양식'][$i]) ? $categories['양식'][$i]['FOODNAME'] . " (" . number_format($categories['양식'][$i]['TOTALREVENUE']) . "원)" : '') . "</td>";
                    echo "<td>" . (!empty($categories['일식'][$i]) ? $categories['일식'][$i]['FOODNAME'] . " (" . number_format($categories['일식'][$i]['TOTALREVENUE']) . "원)" : '') . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } catch (PDOException $e) {
                echo 'Error: ' . $e->getMessage();
            }
            ?>
        </div>
    </div>
</body>
</html>
