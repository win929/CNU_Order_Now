<?php
session_start();

// Oracle 데이터베이스 연결
$host = 'localhost';
$port = '1521';
$sid = 'XE';
$username = 'd202002529';
$password = '1111';
$dsn = "oci:dbname=(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$sid))) ;charset=AL32UTF8";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요한 기능입니다.'); location.href='login_page.php';</script>";
    exit;
}

// POST로 전달된 foodName을 받음
$foodName = $_POST['foodName'];
$cno = $_SESSION['user_id'];

try {
    $conn = new PDO($dsn, $username, $password);

    // 사용자의 최근 Cart 찾기
    $stmt = $conn->prepare("SELECT * FROM (SELECT id FROM Cart WHERE cno=:cno AND OrderDateTime IS NULL ORDER BY orderDateTime DESC) WHERE ROWNUM <= 1");
    $stmt->bindParam(':cno', $cno);
    $stmt->execute();
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart) {
        $cartId = $cart['ID'];
    } else {
        // Cart ID를 숫자 순으로 생성
        $stmt = $conn->prepare("SELECT MAX(TO_NUMBER(SUBSTR(id, 2))) AS max_id FROM Cart");
        $stmt->execute();
        $max_id = $stmt->fetch(PDO::FETCH_ASSOC)['MAX_ID'];
        $new_id_num = $max_id ? $max_id + 1 : 1;
        $cartId = 'C' . str_pad($new_id_num, 3, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("INSERT INTO Cart (id, cno) VALUES (:id, :cno)");
        $stmt->bindParam(':id', $cartId);
        $stmt->bindParam(':cno', $cno);
        $stmt->execute();
    }

    // 해당 음식이 Cart에 이미 있는지 확인
    $stmt = $conn->prepare("SELECT * FROM OrderDetail WHERE id=:id AND foodName=:foodName");
    $stmt->bindParam(':id', $cartId);
    $stmt->bindParam(':foodName', $foodName);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // 이미 있으면 수량과 totalPrice 업데이트
        $newQuantity = $item['QUANTITY'] + 1;
        $stmt = $conn->prepare("SELECT price FROM Food WHERE foodName=:foodName");
        $stmt->bindParam(':foodName', $foodName);
        $stmt->execute();
        $foodPrice = $stmt->fetch(PDO::FETCH_ASSOC)['PRICE'];
        $newTotalPrice = $item['TOTALPRICE'] + $foodPrice;

        $stmt = $conn->prepare("UPDATE OrderDetail SET quantity=:quantity, totalPrice=:totalPrice WHERE id=:id AND foodName=:foodName");
        $stmt->bindParam(':quantity', $newQuantity);
        $stmt->bindParam(':totalPrice', $newTotalPrice);
        $stmt->bindParam(':id', $cartId);
        $stmt->bindParam(':foodName', $foodName);
        $stmt->execute();
    } else {
        // ITEMNO를 같은 id 중 가장 큰 값보다 1 더 크게 설정
        $stmt = $conn->prepare("SELECT NVL(MAX(itemNo), 0) + 1 AS new_item_no FROM OrderDetail WHERE id=:id");
        $stmt->bindParam(':id', $cartId);
        $stmt->execute();
        $new_item_no = $stmt->fetch(PDO::FETCH_ASSOC)['NEW_ITEM_NO'];

        // 새로운 항목 추가
        $stmt = $conn->prepare("INSERT INTO OrderDetail (id, itemNo, quantity, totalPrice, foodName) VALUES (:id, :itemNo, 1, (SELECT price FROM Food WHERE foodName=:foodName), :foodName)");
        $stmt->bindParam(':id', $cartId);
        $stmt->bindParam(':itemNo', $new_item_no);
        $stmt->bindParam(':foodName', $foodName);
        $stmt->execute();
    }

    echo "장바구니에 추가되었습니다.";
} catch (PDOException $e) {
    echo $e->getMessage();
}
?>
