<!DOCTYPE html>
<html lang='ko'>
<head>
    <title>주문 목록 페이지</title>
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
    </div>
    <div class="statistics">
        <div class="search-container">
            <input type="date" id="start" name="start">
            <div id='div1'>~</div>
            <input type="date" id="end" name="end">
            <button class="button2" onclick="searchOrderList()">조회</button>
        </div>
        <div id="order-list">
            <!-- 여기에 주문 목록이 표시 -->
        </div>
    </div>
</body>
</html>
