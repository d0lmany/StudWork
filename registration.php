<?php
session_start();
require_once "./components/modules.php";
$regex = '/^[А-ЯЁа-яё\s\-\.]+$/u';
$kernelToken = 'xlfibkgejhl4324rgsdfloe5slf76vndkuhskg';

if (isset($_POST['fromIndex'])) {
    $fname = trim($_POST["fname"]) ?? '';
    $lname = trim($_POST["lname"]) ?? '';
    $pat = trim($_POST["pat"] ?? '');
    $email = trim($_POST["email"]) ?? '';
    $pas1 = trim($_POST["pas1"]) ?? '';
    $pas2 = trim($_POST["pas2"]) ?? '';
    $date = trim($_POST["date"]) ?? '';
    $city = trim($_POST["city"]) ?? '';
    $role = trim($_POST["role"]) ?? '';
    if (empty($fname) || empty($lname) || empty($email) || empty($pas1) || empty($pas2)
    || empty($date) || empty($city) || empty($role)) {
        echo '<script>window.location.href = "/"</script>';
        exit;
    }
    $errors = [];
    if (!preg_match($regex, $fname)) {
        $errors[] = "Имя должно содержать только кириллицу.";
    }
    if (!preg_match($regex, $lname)) {
        $errors[] = "Фамилия должна содержать только кириллицу.";
    }
    if (!preg_match($regex, $pat) && $pat != '') {
        $errors[] = "Отчество должно содержать только кириллицу.";
    }
    if (!preg_match($regex, $city)) {
        $errors[] = "Город должен содержать только кириллицу.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный email.";
    }
    $checkEmail = Methods::fetch($_SERVER['SERVER_NAME'].'/api/users/filtered', [
        'method' => 'POST',
        'headers' => [
            'Authorization' => 'Bearer '.$kernelToken,
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode(['email' => $email])
    ]);
    if (empty($checkEmail['body'])) {
        $errors[] = "Такой email уже занят.";
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[&_-])[A-Za-z\d&_-]{8,}$/', $pas1)) {
        $errors[] = "Пароль должен содержать минимум 8 символов, включая заглавные и строчные буквы, цифры и спец. символы (&, _, -).";
    }
    if ($pas1 !== $pas2) {
        $errors[] = "Пароли не совпадают.";
    }
    $birthDate = new DateTime($date);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    if ($age < 16) {
        $errors[] = "Вы должны быть старше 16 лет.";
    }
    if (count($errors) > 0) {
        $_SESSION['errors'] = $errors;
        header("Location: /");
        exit;
    }
    $processedUserData = [
        'full_name' => "$lname $fname $pat",
        'email' => $email,
        'password' => $pas1,
        'city' => $city,
        'role' => $role,
        'birthday' => $date
    ];
    $_SESSION['processedUserData'] = $processedUserData;
    unset($errors);
} elseif (isset($_POST['fromHere'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errors'] = ['Недействительный CSRF токен'];
        header("Location: /");
        exit;
    }

    if ($_POST['fromHere'] == 'student') {
        $level = trim($_POST['level'])?? '';
        $specialization = trim($_POST['specialization'])?? '';
        $faculty = trim($_POST['faculty'])?? '';
        $yog = trim($_POST['yog'])?? '';
        $ei = trim($_POST['ei'])?? '';
        $skills = trim($_POST['skills'])?? '';

        $errors = [];
        if (!preg_match($regex, $specialization)) {
            $errors[] = "\"Специализация\" должна содержать только кириллицу.";
        }
        if ($faculty == '') {
            $errors[] = "\"Факультет\" не выбран.";
        }
        if (!preg_match($regex, $ei)) {
            $errors[] = "\"Образовательное учреждение\" должно содержать только кириллицу.";
        }
        if (filter_var($yog, FILTER_VALIDATE_INT) === false) {
            $errors[] = "\"Год окончания\" должно содержать только цифры.";
        }
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            header("Location: /");
            exit;
        }
        
        $description = htmlspecialchars(trim($_POST['description']))?? null;
        $processedUserData = $_SESSION['processedUserData'];
        $processedUserData['description'] =  $description;
        $processedUserData['token'] =  null;

        $response = Methods::fetch($_SERVER['SERVER_NAME'].'/api/users', [
            'method' => 'POST',
            'headers' => [
                'Authorization' => 'Bearer '.$kernelToken,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($processedUserData)
        ]);
        $edu = [
            'ei' => $ei,
            'yog' => $yog,
            'level' => $level,
            'faculty' => $faculty,
            'specialization' => $specialization
        ];
        if ($response['status'] !== 201) {
            $_SESSION['errors'] = ['Ошибка при создании пользователя'];
            header("Location: /");
            exit;
        }

        $id = json_decode($response['body'], true)['id'];
        if (isset($_FILES['avatar'])) {
            $avatar = $_FILES['avatar'];
            if ($avatar['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
                $path = './content/avatars/'.$id.'.'.$ext;
                move_uploaded_file($avatar['tmp_name'], $path);
            }
        }
        $processedRoleData = [
            'user_id' => $id,
            'education' => json_encode($edu, JSON_UNESCAPED_UNICODE),
            'skills' => $skills
        ];
        $response = Methods::fetch($_SERVER['SERVER_NAME'].'/api/students',[
            'method' => 'POST',
            'headers' => [
                'Authorization' => 'Bearer '.$kernelToken,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($processedRoleData)
        ]);
        if ($response['status'] !== 201) {
            $_SESSION['errors'] = ['Ошибка при создании профиля студента'];
            header("Location: /");
            exit;
        }
        $_SESSION['redirect'] = ['reg' => 'Профиль студента успешно создан!'];
        header("Location: redirect");
    } elseif (($_POST['fromHere'] == 'employer')) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['errors'] = ['Недействительный CSRF токен'];
            header("Location: /");
            exit;
        }# TODO: сделай кстати чтоб имя файла аватарки сохранялось в бд

        $orgName = trim($_POST['orgName'])?? '';
        $code = trim($_POST['code'])?? '';
        $address = trim($_POST['address'])?? '';

        $errors = [];
        if (!preg_match('/^(?!\s+$)[a-zA-Zа-яА-ЯёЁ\s\\-\'"]+$/u', $orgName)) {
            $errors[] = "\"Название организации\" должно содержать только буквы, пробелы, тире и кавычки.";
        }
        if (filter_var($code, FILTER_VALIDATE_INT) === false) {
            $errors[] = "\"ИНН или ОГРН\" должен содержать только цифры.";
        }
        if (!preg_match('/^[0-9а-яА-ЯёЁ\s\-,.]+$/u', $address)) {
            $errors[] = "\"Адрес организации\" должен содержать только буквы, цифры, точки, запятые, пробелы, тире.";
        }
        if (count($errors) > 0) {
            $_SESSION['errors'] = $errors;
            header("Location: /");
            exit;
        }

        $description = htmlspecialchars(trim($_POST['description']))?? null;
        $processedUserData = $_SESSION['processedUserData'];
        $processedUserData['description'] =  $description;
        $processedUserData['token'] =  null;

        $response = Methods::fetch($_SERVER['SERVER_NAME'].'/api/users', [
            'method' => 'POST',
            'headers' => [
                'Authorization' => 'Bearer '.$kernelToken,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($processedUserData)
        ]);
        if ($response['status'] !== 201) {
            $_SESSION['errors'] = ['Ошибка при создании пользователя'];
            header("Location: /");
            exit;
        }

        $id = json_decode($response['body'], true)['id'];
        if (isset($_FILES['avatar'])) {
            $avatar = $_FILES['avatar'];
            if ($avatar['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
                $path = './content/avatars/'.$id.'.'.$ext;
                move_uploaded_file($avatar['tmp_name'], $path);
            }
        }
        $processedRoleData = [
            'user_id' => $id,
            'org_name' => $orgName,
            'address' => $address,
            'code' => $code
        ];
        $response = Methods::fetch($_SERVER['SERVER_NAME'].'/api/employers',[
            'method' => 'POST',
            'headers' => [
                'Authorization' => 'Bearer '.$kernelToken,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($processedRoleData)
        ]);
        if ($response['status'] !== 201) {
            $_SESSION['errors'] = ['Ошибка при создании профиля работодателя'];
            header("Location: /");
            exit;
        }
        $_SESSION['redirect'] = ['reg' => 'Профиль работодателя успешно создан!'];
        header("Location: redirect");
    }
} else {
    $_SESSION['errors'] = ['Ошибка - не разрешённое перенаправление.'];
    header("Location: /");
    exit;
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
                    <input type="file" name="avatar" accept="image/*">
                    <input type="hidden" name="csrf_token" value='<?php echo $_SESSION["csrf_token"] ?>'>
                <?php if($role == "student"): ?>
                    <select name="level" id="level" required>
                        <option value="">Уровень образования</option>
                        <option value="СПО">СПО</option>
                        <option value="Бакалавриат">Бакалавриат</option>
                        <option value="Специалитет">Специалитет</option>
                        <option value="Магистратура">Магистратура</option>
                        <option value="Аспирантура">Аспирантура</option>
                        <option value="ДПО">ДПО</option>
                    </select>
                    <input type="text" id="specialization" name="specialization" placeholder="Специальность" required>
                    <select name="faculty" id="faculty" required>
                        <option value="">Факультет/Направление</option>
                    </select>
                    <script>
                        fetch('api/target/filtered',{
                            method: 'POST',
                            headers: { "Content-Type": "application/json" }
                        })
                        .then(r => r.json())
                        .then(r => {
                            r.forEach(opt => {
                                const option = document.createElement('option');
                                option.setAttribute('value', opt.id);
                                option.innerText = opt.name;
                                document.getElementById('faculty').append(option);
                            });
                        })
                        .catch(er => alert('Ошибка при получении факультетов: '+er+'\nПопробуйте позже'))
                    </script>
                    <input type="number" name="yog" id="yog" placeholder="Год окончания" required>
                    <div id="errorMessages"></div>
                    <input type="text" id="ei" name="ei" placeholder="Образовательное учреждение" required>
                    <input type="text" id="skill" placeholder="Навыки (нажмите Enter для добавления)">
                    <div id="skillBox"></div>
                    <p>*Чтобы удалить навык - кликните по нему</p>
                    <textarea id="about" name="description" placeholder="Расскажите о себе"></textarea>
                    <input type="hidden" name="fromHere" value="student">
                    <input type="hidden" id="skills" name="skills" value="">
                <?php elseif($role == "employer"): ?>
                    <div id="errorMessages"></div>
                    <input type="text" id="orgName" name="orgName" placeholder="Название организации" required>
                    <input type="text" id="code" name="code" placeholder="ИНН или ОГРН" required>
                    <input type="text" id="address" name="address" placeholder="Адрес организации" required>
                    <textarea id="about" name="description" placeholder="Расскажите об организации"></textarea>
                    <input type="hidden" name="fromHere" value="employer">
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