<?php
# проверяем сессию
session_start();
require_once './components/modules.php';
if (isset($_COOKIE['end'])) {
    # актуален ли токен?
    $stmt = Methods::fetch($_SERVER['SERVER_NAME'].'/api/1/auth', [
        'method' => 'POST',
        'headers' => ['Content-Type' => 'application/json'],
        'body' => json_encode(['token' => $_COOKIE['end']])
    ]);
    ## если актуален - записываем юзера в куки и сессию + данные юзера соответственно роли
    $body = json_decode($stmt['body'], true);
    if (!isset($body['error'])) {
        $_SESSION['isAuth'] = 'true';
        $_SESSION['id'] = $body['id'];
        $_SESSION['city'] = $body['city'];
        $_SESSION['role'] = $body['role'];
        $_SESSION['end'] = $body['token'];
        switch ($_SESSION['role']) {
            case 'student':
                $stmt = Methods::fetch($_SERVER['SERVER_NAME'].'/api/students/filtered', [
                    'method' => 'POST',
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode(['user_id' => $_SESSION['id']])
                ]);
                $response = json_decode($stmt['body'], true)[0];
                $_SESSION['education'] = $response['education'];
                $_SESSION['is_verified'] = $response['is_verified'];
                break;
            case 'employer':
                $stmt = Methods::fetch($_SERVER['SERVER_NAME'].'/api/employers/filtered', [
                    'method' => 'POST',
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => json_encode(['user_id' => $_SESSION['id']])
                ]);
                $response = json_decode($stmt['body'], true)[0];
                $_SESSION['is_verified'] = $response['is_verified'];
                break;
            case 'admin':
                $_SESSION['is_verified'] = 2;
                break;
        }
        foreach ($_SESSION as $key => $value) {
            setcookie($key, (string)$value, 0, '/', '', true);
        }
    } else { ## если не актуален - сбрасываем сессию (внизу так же)
        Methods::breakSession();
    }
} else {
    Methods::breakSession();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/semantic.css">
    <title>Главная | StudWork</title>
    <?php #стиль и скрипт если авторизованы
    if ($_SESSION['isAuth'] == 'true' && $_SESSION['is_verified'] == '2') {
        echo '<script defer type="module" src="./assets/js/index.js"></script>';
        echo '<link rel="stylesheet" href="assets/css/index.css">';
    } else {
        echo '<script defer src="./assets/js/unsigned.js"></script>';
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    <script>
        function checkAuth(event) {
            <?php if ($_SESSION['isAuth'] == "false"): ?>
                event.preventDefault();
                openModal();
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <?php
    # размещаем шапку
    require_once './components/header.php';
    # генерируем страницу в соответствии с юзерскими данными
    if ($_SESSION['isAuth'] == 'true') {
        switch ($_SESSION['is_verified']) {
            case '0':
                require_once './components/unsigned.html';
                Methods::msg('Рады видеть вас снова!', 'Ваш аккаунт создан, однако, не был подтверждён администраторами, вернитесь позже!');
                break;
            case '1':
                require_once './components/fail_verified.html';
                break;
            case '2':
                echo "<main class='flex gap15'>";
                if ($_SESSION['role'] != 'admin') {
                    require_once './components/search.html';
                }
                echo "</main>";
                break;
        }
    } else {
        require_once './components/unsigned.html';
    }
    # размещаем подвал
    require_once './components/footer.html';
    # чекаем ошибки из сессии
    if (isset($_SESSION['errors'])) {
        $errors = implode(' ', $_SESSION['errors']);
        unset($_SESSION['errors']);
        require_once "./components/modules.html";
        echo <<< LOL
        <script>
        const errorString = '$errors';
        openAlert("Произошла ошибка", errorString);
        </script>
        LOL;
    }
    ?>
</body>
</html>