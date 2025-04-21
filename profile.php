<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="/assets/css/list.css">
    <link rel="stylesheet" href="/assets/css/profile.css">
    <script type="module" src="/assets/js/profile.js"></script>
</head>
<body>
    <?php require_once 'components/header.php' ?>
    <main>
        <div class="card">
            <h1 id="name"></h1>
            <div class="flex main">
                <section class="card" style="max-width:312px">
                    <h3>Личная информация</h3>
                    <div class="img"></div>
                    <div class="flex"><span>Почта: </span><span id="email"></span></div>
                    <div class="flex"><span>Город: </span><span id="city"></span></div>
                    <div class="flex"><span>Возраст: </span><span id="yo"></span></div>
                    <div class="flex"><span>Статус: </span><span id="status"></span></div>
                </section>
                <section class="card">
                    <h3>Описание</h3>
                    <p id="desc"></p>
                </section>
                <section class="card">
                    <h3>Действия</h3>
                    <button id="favorites"><img src="assets/svg/white-heart.svg">Избранное</button>
                    <button id="changeInfo"><img src="assets/svg/chat.svg">Редактирование</button>
                    <button id="delete" class="red-bg"><img src="assets/svg/delete.svg">Удалить аккаунт</button>
                </section>
                <?php if($_COOKIE['role'] == 'employer'): ?>
                <section class="card" style="justify-content: space-between;">
                    <h3>Организация</h3>
                    <div class="flex"><span>Название организации: </span><span id="org_name"></span></div>
                    <div class="flex"><span>Адрес: </span><span id="address"></span></div>
                    <div class="flex"><span>ИНН/ОГРН: </span><span id="code"></span></div>
                    <div class="flex"><span>Аккаунт создан: </span><span id="created"></span></div>
                </section>
                <?php elseif($_COOKIE['role'] == 'student'): ?>
                <section class="card" style="justify-content: space-between;">
                    <h3>Образование и навыки</h3>
                    <div class="flex"><span>Учебное заведение: </span><span id="ei"></span></div>
                    <div class="flex"><span>Ступень образования: </span><span id="level"></span></div>
                    <div class="flex"><span>Направление: </span><span id="faculty"></span></div>
                    <div class="flex"><span>Специализация: </span><span id="specialization"></span></div>
                    <div class="flex"><span>Год окончания: </span><span id="yog"></span></div>
                    <div class="flex"><span>Аккаунт создан: </span><span id="created"></span></div>
                    <div id="skills"></div>
                </section>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php require_once 'components/footer.html' ?>
    <div id="overlay">
        <div class="card col gap15">
            <div class="flex">
                <h3 class="m-0">Избранное</h3>
                <button class="red-bg" id="close">X</button>
            </div>
            <div id="body">
                <h3>Загружаю...</h3>
            </div>
        </div>
    </div>
    <?php require_once 'components/modules.html' ?>
</body>
</html>