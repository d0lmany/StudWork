<?php
session_start();
// Функция для редиректа с сообщением
function redirectWithError($message) {
    $_SESSION['error_message'] = $message; // Сохраняем сообщение в сессии
    foreach ($_POST as $a){
        echo "$a<hr>";
    }
    #header('Location: /'); // Редирект на основную страницу
    exit();
}
// Получаем данные
$fname = trim($_POST["fname"]) ?? '';
$lname = trim($_POST["lname"]) ?? '';
$pat = trim($_POST["pat"] ?? '');
$email = trim($_POST["email"]) ?? '';
$pas1 = trim($_POST["pas1"]) ?? '';
$pas2 = trim($_POST["pas2"]) ?? '';
$date = trim($_POST["date"]) ?? '';
$city = trim($_POST["city"]) ?? '';
$role = trim($_POST["role"]) ?? '';
if (
    empty($fname) ||
    empty($lname) ||
    empty($email) ||
    empty($pas1) ||
    empty($pas2) ||
    empty($date) ||
    empty($city) ||
    empty($role)
) {
    redirectWithError("Не все необходимые поля были заполнены.");
}
// Валидация данных
$errors = [];
if (!preg_match('/^[А-Яа-я\'\-]+$/u', $fname)) {
    $errors[] = "Имя должно содержать только кириллицу.";
}
if (!preg_match('/^[А-Яа-я\'\-]+$/u', $lname)) {
    $errors[] = "Фамилия должна содержать только кириллицу.";
}
if (!preg_match('/^[А-Яа-я\'\-]+$/u', $city)) {
    $errors[] = "Город должен содержать только кириллицу.";
}
// Проверка email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некорректный email.";
}
// Проверка пароля
if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[&_-])[A-Za-z\d&_-]{8,}$/', $pas1)) {
    $errors[] = "Пароль должен содержать минимум 8 символов, включая заглавные и строчные буквы, цифры и спец. символы (&, _, -).";
}
// Проверка совпадения паролей
if ($pas1 !== $pas2) {
    $errors[] = "Пароли не совпадают.";
}
// Проверка возраста (старше 16 лет)
$birthDate = new DateTime($date);
$today = new DateTime();
$age = $today->diff($birthDate)->y;
if ($age < 16) {
    $errors[] = "Вы должны быть старше 16 лет.";
}
// Если есть ошибки, выводим их и делаем редирект
if (!empty($errors)) {
    redirectWithError("Данные введены некорректно: " . implode(" ", $errors));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="./assets/css/inputPages.css">
    <script src="./assets/js/inputPages.js"></script>
</head>
<body>
    <main>
        <div class="card">
            <h1>Здравствуйте, <?php echo $fname; ?>!</h1>
            <form method="post" id="form" enctype="multipart/form-data">
                    <input type="file" id="avatar" accept="image/*">
                <?php if($role == "student"): ?>
                    <select id="level" required>
                        <option value="">Уровень образования</option>
                        <option value="sve">СПО</option>
                        <option value="bachelor">Бакалавриат</option>
                        <option value="specialty">Специалитет</option>
                        <option value="magistracy">Магистратура</option>
                        <option value="postgraduate">Аспирантура</option>
                        <option value="ape">ДПО</option>
                    </select>
                    <input type="text" id="specialization" placeholder="Специальность" required>
                    <input type="text" id="faculty" placeholder="Факультет" required>
                    <div id="errorMessages"></div>
                    <input type="text" id="ei" placeholder="Образовательное учреждение" required>
                    <input type="text" id="skill" placeholder="Навыки (нажмите Enter для добавления)">
                    <div id="skillbox"></div>
                    <p>*Чтобы удалить навык - кликните по нему</p>
                    <textarea id="about" placeholder="Расскажите о себе"></textarea>
                <?php elseif($role == "employer"): ?>
                    <input type="text" id="orgname" placeholder="Название организации" required>
                    <input type="text" id="code" placeholder="ИНН или ОГРН" required>
                    <input type="text" id="address" placeholder="Адрес организации" required>
                    <textarea id="about" placeholder="Расскажите об организации"></textarea>
                <?php endif; ?>
                <div class="flex">
                    <a href="/" class="button red-bg">Отменить регистрацию</a>
                    <button>Подтвердить</button>
                </div>
                <p>Как только мы подтвердим всю введённую информацию - пришлём уведомление вам на почту!</p>
            </form>
        </div>
    </main>
    <?php require "./components/footer.html" ?>
</body>
</html>