<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная | StudWork</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <?php if (!isset($_SESSION['isAuth'])): ?>
        <?php echo '<script src="./assets/js/unsigned.js"></script' ?>
    <?php endif; ?>
    <script>
        function checkAuth(event) {
            <?php if (!isset($_SESSION['isAuth'])): ?>
                event.preventDefault();
                openModal();
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <?php require "./components/header.php" ?>
        <?php
        if (isset($_SESSION['error_message'])){
            echo "<script>alert('{$_SESSION['error_message']}')</script>";
            if (isset($_COOKIE)) {
                foreach ($_COOKIE as $name => $value) {
                    setcookie($name, '', time() - 3600, '/');
                }
                $_COOKIE = [];
            }
        }
        ?>
    <?php if (isset($_SESSION['isAuth'])): ?>
        <?php require "./components/search.html" ?>
    <?php else: ?>
        <?php require "./components/unsigned.html" ?>
    <?php endif; ?>

    <?php require "./components/footer.html" ?>

    <div id="authModal" class="modal">
        <div class="modal-content">
            <p>Не так быстро!<br>Нужен аккаунт</p>
            <a href="login" class="button">Войти</a>
            <a href="#registrationForm" class="button" onclick="closeModal()" style="margin:10px 0">Зарегистрироваться</a>
            <button id="closeModal">Закрыть</button>
        </div>
    </div>
</body>
</html>