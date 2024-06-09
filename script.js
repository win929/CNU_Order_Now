function addToCart(foodName) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "addToCart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                alert(xhr.responseText);
            } else {
                // 서버에서 반환한 구체적인 오류 메시지를 출력
                alert("장바구니에 추가하는 도중 오류가 발생했습니다. 상태 코드: " + xhr.status + ", 응답 텍스트: " + xhr.responseText);
            }
        }
    };
    xhr.onerror = function () {
        // 네트워크 오류나 요청이 실패했을 때
        alert("요청 중 네트워크 오류가 발생했습니다.");
    };
    xhr.send("foodName=" + encodeURIComponent(foodName));
}

document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.button3');
    var currentCategory = getCurrentCategory();

    function getCurrentCategory() {
        // URL에서 카테고리 쿼리 스트링 값을 가져오는 함수
        var params = new URLSearchParams(window.location.search);
        return params.get('category');
    }

    function setActiveButton() {
        // 현재 카테고리에 해당하는 버튼에 active 클래스 추가
        buttons.forEach(function(button) {
            if (button.getAttribute('data-category') === currentCategory) {
                button.classList.add('active');
            }
        });
    }

    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            // 모든 버튼에서 active 클래스 제거
            buttons.forEach(function(btn) {
                btn.classList.remove('active');
            });

            // 클릭된 버튼에 active 클래스 추가
            button.classList.add('active');

            // 선택된 카테고리로 페이지 이동
            var category = button.getAttribute('data-category');
            if (category) {
                location.href = '?category=' + category;
            }
        });
    });

    // 페이지 로드 시 현재 카테고리에 맞는 버튼에 active 클래스를 적용
    setActiveButton();

    document.getElementById('searchButton').addEventListener('click', function() {
        var foodName = document.getElementById('foodName').value;
        var minPrice = document.getElementById('minPrice').value;
        var maxPrice = document.getElementById('maxPrice').value;
    
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'search.php?foodName=' + foodName + '&minPrice=' + minPrice + '&maxPrice=' + maxPrice, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById('searchResult').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });
});

function updateQuantity(foodName, change) {
    // AJAX를 사용하여 수량 업데이트 요청을 보냅니다.
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'updateQuantity.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            location.reload(); // 페이지를 새로고침하여 변경된 내용을 반영합니다.
        }
    };
    xhr.send('foodName=' + foodName + '&change=' + change);
}

function updateOrderDateTime() {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'updateOrderDateTime.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("주문이 완료되었습니다!"); // 성공 메시지
            location.reload(); // 페이지 새로고침
        }
    };
    xhr.send(); // 여기서는 별도의 데이터를 보내지 않습니다.
}

function searchOrderList() {
    var start = document.getElementById('start').value;
    var end = document.getElementById('end').value;
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "searchOrder.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('order-list').innerHTML = xhr.responseText;
        }
    }
    xhr.send("start=" + start + "&end=" + end);
}
