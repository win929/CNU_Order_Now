<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>로그인 페이지</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <div class="container">
                <input class="inputbox" type="text" name="id" placeholder="아이디">
                <input class="inputbox" type="password" name="password" placeholder="비밀번호">
                <input class="button1" type="submit" value="로그인">
            </div>
        </form>
        <?php
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

                // 사용자 입력 값
                $input_id = $_POST['id'];
                $input_password = $_POST['password'];

                // SQL 쿼리: CUSTOMER 테이블에서 CNO와 PASSWORD를 조회
                $sql = "SELECT CNO, PASSWORD FROM CUSTOMER WHERE CNO = :id AND PASSWORD = :password";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $input_id);
                $stmt->bindParam(':password', $input_password);

                // 쿼리 실행
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // 로그인 성공
                    $_SESSION['user_id'] = $user['CNO']; // 세션에 사용자 아이디 저장

                    if ($user['CNO'] == 'c0') {
                        // 관리자 페이지로 이동
                        header('Location: admin.php');
                    } else {
                        // 일반 사용자 페이지로 이동
                        header('Location: menu_page.php');
                    }
                    exit();
                } else {
                    // 로그인 실패
                    echo "<script>alert('로그인 실패: 아이디 또는 비밀번호가 잘못되었습니다.')</script>";
                }

            } catch (PDOException $e) {
                // 예외 처리
                echo "연결 실패: " . $e->getMessage();
            }

            // 데이터베이스 연결 종료
            $conn = null;
        }
        ?>
    </div>
</body>
</html>
