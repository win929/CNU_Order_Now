<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>메뉴 선택 페이지</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo '사용자: ' . $_SESSION['user_id'];
    }
    ?>
    <div class="container">
        <div class="container">
            <button class="button1" onclick="location.href='order.php'">음식 주문</button>
            <button class="button1" onclick="location.href='cart.php'">장바구니</button>
            <button class="button1" onclick="location.href='order_list.php'">주문내역</button>
        </div>
    </div>
</body>
</html>
