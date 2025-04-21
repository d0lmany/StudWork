export var currentData;
let currentModalHandler = null, currentSendHandler = null, currentInputHandler = null, currentCancelHandler = null;
export const headers = { "Content-Type": "application/json" };

export async function getCards(box, searchText = null) {
    const target = getCookie('role') == 'student' ? 'vacancies' : 'students';
    try {
        const body = target == 'vacancies' ? { target: getEducation().faculty } : {};
        const re = await fetch('api/' + target + '/filtered', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(body)
        });
        const data = await re.json();
        data.reverse();
        if (searchText) {
            let filtered;
            if (target == 'vacancies') {
                filtered = data.filter(item => {
                    const search = searchText.toLowerCase();
                    return (
                        item.name?.toLowerCase().includes(search) ||
                        item.requirements?.toLowerCase().includes(search) ||
                        item.description?.toLowerCase().includes(search)
                    );
                });
            } else {
                filtered = data.filter(item => {
                    const education = JSON.parse(decodeURIComponent(item.education));
                    const search = searchText.toLowerCase();
                    return (
                        education.ei?.toLowerCase().includes(search) ||
                        education.specialization?.toLowerCase().includes(search)
                    );
                });
            }
            if (filtered) {
                console.table(filtered)
                setCards(box, filtered);
            } else {
                box.innerHTML = '<h1>Не удалось найти искомый элемент</h1>';
            }
        } else {
            setCards(box, data);
        }
    } catch (error) {
        console.error('Ошибка при загрузке вакансий: ', error);
        box.innerHTML = '<h1>Произошла ошибка при загрузке содержимого. Попробуйте позже</h1>';
    }
}

async function setCards(box, elements) {
    if (elements.length > 0) {
        box.innerHTML = '';
        if (getCookie('role') == 'student') {
            const cardsPromises = elements.map(async element => {
                const user = await getUserEntity(element.employer_id);
                element.org_name = user.org_name;
                element.path = user.path;
                element.city = user.city;
                const card = document.createElement('article');
                const salary = element.salary ?? 0;
                const fSalary = salary >= 3 ? salary.toString().slice(0, -3) + ' ' + salary.toString().slice(-3) : salary.toString();
                card.innerHTML = `
                <div class='flex'><h3>${element.name}</h3><button class="context-button" data-id="${element.id}"><img src='./assets/svg/card-menu.svg'></button></div>
                <div class='flex gap50'>
                    <div class='img' style='background-image:url(${'content/avatars/' + element.path})'></div>
                    <div>
                        <span><b>${element.org_name}</b></span>
                        <span>${element.city}</span>
                        <span>${fSalary == '0' ? 'Без оплаты' : fSalary + ' &#x20BD;'}</span>
                    </div>
                    <div>
                        <span>с ${element.internship_period_start}</span>
                        <span>по ${element.internship_period_end}</span>
                    </div>
                </div>
                <div class='flex'>
                    <a href='/vacancy?id=${element.id}' class='button alt'>Подробнее</a>
                    <button class="response-button" data-id="${element.id}" data-ncl="${element.cover_letter}">Откликнуться</button>
                </div>
                `;

                const contextBtn = card.querySelector('.context-button');
                contextBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    showContext(event, contextBtn.dataset.id);
                });

                const responseBtn = card.querySelector('.response-button');
                responseBtn.addEventListener('click', () => {
                    openResponse(responseBtn.dataset.id, Number(responseBtn.dataset.ncl));
                });

                return card;
            });
            const cards = await Promise.all(cardsPromises);
            currentData = elements;
            cards.forEach(card => box.append(card));
        } else {
            const cardsPromises = elements.map(async element => {
                const user = await getUserEntity(element.user_id);
                const edu = JSON.parse(decodeURIComponent(element.education));
                element.path = user.path;
                element.city = user.city;
                element.name = user.full_name;
                element.full_name = user.full_name;
                const card = document.createElement('article');
                card.innerHTML = `
                <div class='flex'><h3>${edu.specialization}</h3><button class="context-button" data-id="${element.id}"><img src='./assets/svg/card-menu.svg'></button></div>
                <div class='flex gap50'>
                    <div class='img' style='background-image:url(${'content/avatars/' + element.path})'></div>
                    <div>
                        <span><b>${element.full_name}</b></span>
                        <span>${element.city}</span>
                        <span>${edu.ei}</span>
                    </div>
                </div>
                <div class='flex'>
                    <a href='/resume?id=${element.id}' class='button alt'>Подробнее</a>
                    <button class="invite-button" data-id="${element.id}">Пригласить</button>
                </div>
                `;
                const contextBtn = card.querySelector('.context-button');
                contextBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    showContext(event, contextBtn.dataset.id);
                });

                const responseBtn = card.querySelector('.invite-button');
                responseBtn.addEventListener('click', () => {
                    openInvite(responseBtn.dataset.id);
                });

                return card;
            });
            const cards = await Promise.all(cardsPromises);
            currentData = elements;
            cards.forEach(card => box.append(card));
        }
    } else {
        box.innerHTML = '<h1>Не удалось найти подходящие результаты.</h1>';
    }
}

export function openInvite(id) {
    const modal = document.getElementById('invite-overlay');
    const vacanciesBox = document.getElementById('currentVacancy');
    const cancelBtn = document.getElementById('resetInvite');
    const sendBtn = document.getElementById('sendInvite');
    
    modal.style.display = 'flex';
    fetch('api/employers/filtered', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({user_id: getCookie('id')})
    })
    .then(r => r.json())
    .then(r => {
        r = r[0];
        fetch('api/vacancies/filtered', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({employer_id: r.user_id})
        })
        .then(re => re.json())
        .then(re => {
            re.forEach(vacancy => {
                const opt = document.createElement('option');
                opt.setAttribute('value', vacancy.id);
                opt.innerText = vacancy.name+' - '+(vacancy.salary == 0?'Без оплаты':vacancy.salary);
                vacanciesBox.append(opt);
            })
        })
        .catch(er => console.error('error for get vacancies: ', er))
    })
    .catch(er => console.error('error for get employers: ', er))
    if (currentCancelHandler) {
        cancelBtn.removeEventListener('click', currentCancelHandler);
    }
    currentCancelHandler = () => {
        modal.style.display = 'none';
        vacanciesBox.innerHTML = '<option value="">Предлагаемая вакансия</option>';
    };
    cancelBtn.addEventListener('click', currentCancelHandler);

    if (currentSendHandler) {
        sendBtn.removeEventListener('click', currentSendHandler);
    }
    currentSendHandler = () => {
        if (vacanciesBox.value) {
            invite(id, vacanciesBox.value);
            modal.style.display = 'none';
        } else {
            pushUp('Внимание', 'Для приглашения надо выбрать вакансию.');
        }
    };
    sendBtn.addEventListener('click', currentSendHandler);
}

function invite(id, vacancyId) {
    const body = {
        student_id: id,
        employer_id: Number(getCookie('id')),
        vacancy_id: vacancyId
    };
    fetch('api/invite_lists', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(r => {
        if (r.message == 'was added') {
            pushUp('Приглашение', 'Приглашение успешно отправлено!');
        } else {
            pushUp('Ошибка', 'Не удалось отправить приглашение: ' + r.error);
        }
    })
    .catch(error => pushUp('Ошибка', 'Не удалось отправить приглашение: ' + error))
}

function openResponse(id, ncl) {
    const modal = document.getElementById('cover-letter-overlay');
    const letter = document.getElementById('letter');
    const sendBtn = document.getElementById('send');
    const cancelBtn = document.getElementById('cancel');
    const tipElement = document.getElementById('tip');

    if (currentModalHandler) {
        modal.removeEventListener('click', currentModalHandler);
    }
    if (currentSendHandler) {
        sendBtn.removeEventListener('click', currentSendHandler);
    }
    if (currentInputHandler) {
        letter.removeEventListener('input', currentInputHandler);
    }
    if (currentCancelHandler) {
        cancelBtn.removeEventListener('click', currentCancelHandler);
    }

    modal.style.display = 'flex';
    letter.value = '';

    if (ncl) {
        tipElement.style.display = 'block';
        letter.setAttribute('required', 'true');
        letter.setAttribute('placeholder', 'Обязательное поле');
        sendBtn.textContent = 'Отправить';
    } else {
        tipElement.style.display = 'none';
        letter.removeAttribute('required');
        letter.setAttribute('placeholder', 'Необязательное поле');
        sendBtn.textContent = 'Отклик без резюме';
    }

    currentCancelHandler = () => {
        modal.style.display = 'none';
    };

    currentInputHandler = () => {
        const hasText = letter.value.trim().length > 0;
        sendBtn.textContent = (ncl || hasText)
            ? 'Отправить'
            : 'Отклик без резюме';
    };

    currentSendHandler = async () => {
        const trimmedLetter = letter.value.trim();

        if (ncl && !trimmedLetter) {
            pushUp('', 'На эту вакансию требуется сопроводительное письмо.');
            return;
        }
        response(id, trimmedLetter || null);
        modal.style.display = 'none';
    };

    cancelBtn.addEventListener('click', currentCancelHandler);
    letter.addEventListener('input', currentInputHandler);
    sendBtn.addEventListener('click', currentSendHandler);

    currentModalHandler = (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    };
    modal.addEventListener('click', currentModalHandler);
}

async function response(id, cover_letter) {
    const body = {
        user_id: getCookie('id'),
        vacancy_id: id,
        cover_letter: cover_letter ?? null,
        status: 'Отправлен'
    };
    fetch('api/responses', {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(body)
    })
        .then(r => r.json())
        .then(r => {
            if (r.message == 'was added') {
                pushUp('Отклик', 'Отклик успешно отправлен!');
            } else {
                pushUp('Ошибка', 'Не удалось отправить отклик: ' + r.error)
            }
        })
        .catch(r => pushUp('Ошибка', 'Не удалось отправить отклик: ' + r))
}

export async function getFilteredCardsEmployers(box, data, salary = '', city = '') {

    const filteredData = data.filter(item => {
        const meetsSalaryCondition = salary == '' || item.salary >= salary;
        const meetsCityCondition = city == '' || item.city === city;

        return meetsSalaryCondition && meetsCityCondition;
    });
    setCards(box, filteredData);
}

export async function getFilteredCardsStudents(box, data, target = '', city = '') {
    const filteredData = data.filter(item => {
        const education = JSON.parse(decodeURIComponent(item.education));
        const meetsTargetCondition = target == '' || education.faculty == target;
        const meetsCityCondition = city == '' || item.city == city;

        return meetsTargetCondition && meetsCityCondition;
    });
    setCards(box, filteredData);
}

function showContext(event, id) {
    const menu = document.querySelector('.context-menu');
    const scrollX = window.scrollX || window.pageXOffset;
    const scrollY = window.scrollY || window.pageYOffset;
    menu.style.left = `${event.clientX + scrollX}px`;
    menu.style.top = `${event.clientY + scrollY}px`;
    menu.style.display = 'block';
    const hideMenu = () => {
        menu.style.display = 'none';
        menu.removeEventListener('mouseleave', hideMenu);
    };
    menu.addEventListener('mouseleave', hideMenu);
    menu.setAttribute('data-id', id);
    document.getElementById('addToFavorites').addEventListener('click', () => { addToFavorites(menu.getAttribute('data-id')) });
    document.getElementById('report').addEventListener('click', () => { report(menu.getAttribute('data-id'), 'vacancies') });
}

export async function addToFavorites(id) {
    try {
        const response = await fetch('api/favorites_lists/filtered', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ owner_id: getCookie('id') })
        });
        const data = await response.json();
        if (!data || !data[0]) {
            throw new Error('Произошла ошибка при добавлении в избранное');
        }
        id = Number(id);
        const listId = data[0].id;
        let favoritesList = data[0].list ? JSON.parse(data[0].list) : [];
        if (!favoritesList.includes(id)) {
            favoritesList.push(id);
        } else {
            pushUp('Избранное', 'Эта карточка уже в списке избранного');
            return;
        }
        const updateResponse = await fetch(`api/favorites_lists/${listId}`, {
            method: 'PATCH',
            headers: headers,
            body: JSON.stringify({
                list: JSON.stringify(favoritesList)
            })
        });
        const result = await updateResponse.json();
        if (result.message === 'is updated') {
            pushUp('Избранное', 'Добавлено в избранное');
        } else {
            pushUp('Ошибка', result.error);
        }
    } catch (error) {
        pushUp('Ошибка', error);
    }
}

export async function report(id, entity) {
    try {
        const response = await fetch('api/claims', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({
                owner_id: getCookie('id'),
                entity: entity,
                object_id: id
            })
        });
        const data = await response.json();
        if (data.message === 'was added') {
            pushUp('Жалоба', 'Жалоба успешно отправлена');
        } else {
            pushUp('Ошибка', data.error);
        }
    } catch (error) {
        pushUp('Ошибка', error);
    }
}

async function getUserEntity(id) {
    const who = getCookie('role') == 'student';
    try {
        if (who) {
            const re = await fetch(`api/employers/${id}`);
            const data = await re.json();
            const user = await fetch(`api/users/${data.user_id}`);
            const result = await user.json();
            result.org_name = data.org_name;
            return result;
        } else {
            const re = await fetch(`api/users/${id}`);
            const data = await re.json();
            return data;
        }
    } catch (error) {
        console.error('Ошибка при получении пользователя: ', error);
        return null;
    }
}

export function pushUp(header, content, buttons = null) {
    const push = document.getElementById('push');
    push.querySelector('h3').innerText = header;
    push.querySelector('p').innerText = content;
    if (buttons) push.querySelector('.flex').innerHTML = buttons + '<button id=\'pd\'>Ок</button>';
    push.querySelector('#pd').addEventListener('click', pushDown)
    push.style.animation = 'push .75s ease-out forwards';
}

function pushDown() {
    document.getElementById('push').style.animation = 'push .75s ease-in reverse forwards';
}

function getEducation() {
    const edu = JSON.parse(decodeURIComponent(getCookie('education')));
    return edu
}

export async function getFavoriteList() {
    try {
        const response = await fetch('api/favorites_lists/filtered', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({owner_id: Number(getCookie('id'))})
        });
        const data = await response.json();
        return data[0] || null;
    } catch (error) {
        console.error('Error in getFavoriteList:', error);
        return 'error';
    }
}

export async function setFavoriteList(id, newList) {
    try {
        const response = await fetch(`api/favorites_lists/${id}`, {
            method: 'PATCH',
            headers: headers,
            body: JSON.stringify({ list: JSON.stringify(newList) })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Failed to update favorites list');
        }

        if (data.message === 'is updated') {
            console.log('Favorites list updated successfully');
            return true;
        } else {
            console.warn('Favorites list was not updated:', data.message);
            return false;
        }
    } catch (error) {
        console.error('Error in setFavoriteList:', error.message);
        return false;
    }
}

export async function getInviteList() {
    try {
        const re = await fetch(`api/${getCookie('role')}s/filtered`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({user_id: Number(getCookie('id'))})
        });
        const data = await re.json();
        const body = JSON.stringify(getCookie('role') == 'employer'? {employer_id: Number(data[0].id)}: {student_id: Number(data[0].id)});
        const reInvite = await fetch('api/invite_lists/filtered', {
            method: 'POST',
            headers: headers,
            body: body
        });
        const inviteData = await reInvite.json();
        return inviteData;
    } catch (er) {
        console.error('Error in getInviteList:', er);
        return 'error';
    }
}

export function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift(); else return null;
}