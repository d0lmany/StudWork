<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | StudWork</title>
    <link rel="stylesheet" href="./assets/css/semantic.css">
</head>
<body>
    <?php
    session_start();
    if (isset($_POST['login']) && isset($_POST['password'])) {
        require_once './components/modules.php';
        $login = $_POST['login'];
        $password = $_POST['password'];
        try {
            $stmt = Methods::fetch($_SERVER['SERVER_NAME'] . '/api/users/token', [
                'method' => 'POST',
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode(['email' => $login, 'password' => $password])
            ]);
            if ($stmt['status'] == 201) {
                session_unset();
                $_SESSION['isAuth'] = true;
                sleep(1);
                $token = json_decode($stmt['body'], true)['token'];
                $_SESSION['end'] = $token;
                setcookie('end', $token, 0, '', '', true);
                header('Location: /');
            } else {
                Methods::msg('Ошибка', 'Возможно, пароль или логин введены неверно');
            }
        } catch (Exception $e) {
            Methods::breakSession();
            $_SESSION['errors'] = ['CSRF panic!'];
            Methods::msg('Ошибка', 'Неизвестная ошибка: '.$e);
        }
    }
    ?>
    <main>
        <form class="card col gap15" method="post">
            <h1 class="m-0">Вход</h1>
            <input type="text" name="login" placeholder="Почта" required>
            <input type="text" name="password" placeholder="Пароль" required>
            <div class="flex">
                <a href="/#registrationForm" class="button red-bg-alt">Перейти к регистрации</a>
                <button type="submit">Войти</button>
            </div>
        </form>
    </main>
    <?php require "./components/footer.html" ?>
    <style>
        h1 {
            text-align: center;
        }
        form {
            width: 50%;
        }
        a {
            text-decoration: none;
        }
        main {
            display: flex;
            justify-content: center;
            margin: 125px auto;
        }
        a.button, button {
            font-size: 18px;
        }
    </style>
</body>

</html>