<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Резюме</title>
    <link rel="stylesheet" href="/assets/css/list.css">
    <script type="module" src="/assets/js/resume.js"></script>
</head>
<body>
    <?php require_once 'components/header.php' ?>
    <main>
        <div class="card">
            <div class="flex">
                <h1 class="specialization"></h1>
                <h2 id="fio"></h2>
            </div>
            <div class="flex main">
                <section class="card" style="max-width:312px">
                    <h3>Личная информация</h3>
                    <div class="img"></div>
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
                    <button id="invite"><img src="assets/svg/invite.svg">Пригласить</button>
                    <button id="favorite"><img src="assets/svg/white-heart.svg">В избранное</button>
                    <button id="chat"><img src="assets/svg/chat.svg">В чат</button>
                    <button id="claim" class="red-bg"><img src="assets/svg/white-flag.svg">Пожаловаться</button>
                </section>
                <section class="card" style="justify-content: space-between;">
                    <h3>Образование и навыки</h3>
                    <div class="flex"><span>Учебное заведение: </span><span id="ei"></span></div>
                    <div class="flex"><span>Ступень образования: </span><span id="level"></span></div>
                    <div class="flex"><span>Направление: </span><span id="faculty"></span></div>
                    <div class="flex"><span>Специализация: </span><span class="specialization"></span></div>
                    <div class="flex"><span>Год окончания: </span><span id="yog"></span></div>
                    <div id="skills"></div>
                </section>
            </div>
        </div>
    </main>
    <?php require_once 'components/footer.html' ?>
    <?php require_once 'components/modules.html' ?>
    <div id="push">
        <h3></h3>
        <p></p>
        <div class="flex">
            <button id="pd">Ок</button>
        </div>
    </div>
    <div id="invite-overlay">
    <div class="card col gap15">
        <h3>Пригласить на вакансию</h3>
        <select id="currentVacancy">
            <option value="">Предлагаемая вакансия</option>
        </select>
        <div class="flex">
            <button id="resetInvite">Отмена</button>
            <button id="sendInvite">Пригласить</button>
        </div>
    </div>
</div>
</body>
</html>