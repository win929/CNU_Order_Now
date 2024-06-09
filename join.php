<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입 페이지</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <div class="container">
                <input class="inputbox" type="text" name="id" placeholder="아이디">
                <input class="inputbox" type="password" name="password" placeholder="비밀번호">
                <input class="inputbox" type="text" name="name" placeholder="이름">
                <input class="inputbox" type="text" name="phonenumber" placeholder="전화번호">
                <input class="button1" type="submit" value="회원가입">
            </div>
        </form>
        <?php
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
                $input_name = $_POST['name'];
                $input_phonenumber = $_POST['phonenumber'];

                // SQL 쿼리: CUSTOMER 테이블에 새로운 사용자 추가
                $sql = "INSERT INTO CUSTOMER (CNO, PASSWORD, NAME, PHONENUMBER) VALUES (:id, :password, :name, :phonenumber)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id', $input_id);
                $stmt->bindParam(':password', $input_password);
                $stmt->bindParam(':name', $input_name);
                $stmt->bindParam(':phonenumber', $input_phonenumber);

                // 쿼리 실행
                $stmt->execute();

                // 회원가입 성공
                echo "<script>alert('회원가입 성공: 로그인 페이지로 이동합니다.')</script>";
                header("Location: login.php");
                exit();

            } catch (PDOException $e) {
                // 예외 처리
                echo "연결 실패: " . $e->getMessage();
            }
        }
        ?>
    </div>
</body>
</html>
