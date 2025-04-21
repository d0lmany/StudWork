<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Перенаправление | StudWork</title>
    <link rel="stylesheet" href="./assets/css/semantic.css">
</head>
<body>
    <?php session_start(); require_once './components/header.php' ?>
    <main>
    <?php
    if (isset($_SESSION['redirect'])) {
        echo <<< LOL
        <p style='font-size:26px'>Регистрация завершена! Дождитесь подтверждения аккаунта, обычно это длится от 1 до 3 дней.  Мы оповестим вас по электронной почте, так же вы сможете посещать текущую страницу для получения подробностей</p>
        <a href="/" class="button" style='font-size:22px'>На главную</a>
        LOL;
    } else {
        $_SESSION['errors'] = ['Ошибка - не разрешённое перенаправление.'];
        header("Location: /");
    }
    ?>
    </main>
    <style>
        p{
            text-align: justify;
        }
        main{
            display: flex;
            flex-direction: column;
            align-items: center;
            background: white;
            border-radius: 25px;
            margin: 50px;
            padding: 25px;
        }
        .button{
            text-decoration: none;
        }
    </style>
    <?php require_once './components/footer.html' ?>
</body>
</html>