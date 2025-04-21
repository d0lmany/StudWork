import * as functions from './index.functions.js';

document.addEventListener('DOMContentLoaded', async () => {
    let userData = await fetch('api/users/'+functions.getCookie('id'));
    userData = await userData.json();
    document.getElementById('name').innerText = 'С возвращением, '+userData.full_name+'!';
    document.getElementById('email').innerText = userData.email;
    document.getElementById('city').innerText = userData.city;
    document.querySelector('.img').setAttribute('style', "background-image: url('content/avatars/"+userData.path+"')");
    document.getElementById('desc').innerText = userData.description?? 'Вы не предоставили описание.';
    document.getElementById('yo').innerText = getYo(userData.birthday);
    document.getElementById('created').innerText = userData.created_at.split(' ', 1);
    fetch('api/'+functions.getCookie('role')+'s/filtered', {
        method: 'POST',
        headers: functions.headers,
        body: JSON.stringify({user_id: Number(functions.getCookie('id'))})
    })
    .then(r => r.json())
    .then(r => {
        r = r[0];
        if (functions.getCookie('role') == 'employer') {
            document.getElementById('org_name').innerText = r.org_name;
            document.getElementById('address').innerText = r.address;
            document.getElementById('code').innerText = r.code;
        } else {
            const edu = JSON.parse(encodeURIComponent(r.edu));
            document.getElementById('ei').innerText = edu.ei;
            document.getElementById('level').innerText = edu.level;
            document.getElementById('specialization').innerText = edu.specialization;
            document.getElementById('yog').innerText = edu.yog;
            fetch('api/target/filtered', {
                    method: 'POST',
                    headers: functions.headers,
                    body: JSON.stringify({id: edu.faculty})
                })
                .then(re => re.json())
                .then(re => {
                    re = re[0];
                    document.getElementById('faculty').innerText = re.name;
                })
        }
        const status = document.getElementById('status');
        switch (r.is_verified) {
            case 0:
                status.setAttribute('class', 'not-confirmed');
                status.innerText = 'Ожидает подтверждения';
                break;
            case 1:
                status.setAttribute('class', 'bad-confirmed');
                status.innerText = 'Не подтверждён';
                break;
            case 2:
                status.setAttribute('class', 'confirmed');
                status.innerText = 'Подтверждён';
                break;
        }
    })
    document.getElementById('close').addEventListener('click', () => {
        document.getElementById('overlay').className = '';
    })
    document.getElementById('favorites').addEventListener('click', async () => {
        document.getElementById('overlay').className = 'show';
        const listRe = await functions.getFavoriteList();
        const list = await JSON.parse(listRe.list);
        //если ты работодатель, в листе студенты, иначе - вакансии
        const box = document.getElementById('body');
        const elements = [];
        if (functions.getCookie('role') == 'employer') {
            for (const item of list) {
                let student = await fetch('api/students/'+item);
                student = await student.json();
                let user = await fetch('api/users/'+student.user_id);
                user = await user.json();
                const element = {...user, ...student};
                elements.push(element);
            }
            box.innerHTML = '';
            elements.forEach(item => {
                const card = document.createElement('article');
                const edu = JSON.parse(item.education);
                card.innerHTML = `
                    <div class="flex col gap15">
                        <h3>${edu.specialization}</h3>
                        <div class="flex">
                            <div class="img" style="background-image:url('content/avatars/${item.path??'none.svg'}')"></div>
                            <div class="flex col">
                                <b>${item.full_name}</b>
                                <span>${item.city}</span>
                                <span>${edu.ei}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex col">
                        <a href='resume?id=${item.id}' class="alt button">Резюме</a>
                        <button class="red-bg-alt" onclick='delFromFavs(${listRe.id}, ${item.id}, ${JSON.stringify(list)})'>Удалить</button>
                    </div>`;
                box.append(card);

                window.delFromFavs = function(idList, id, list) {
                    card.remove();
                    const newList = (JSON.parse(`[${list}]`)).filter(item => item != id);
                    if (functions.setFavoriteList(idList, newList)) {
                        openAlert('Избранное', 'Элемент успешно удалён.');
                    } else {
                        openAlert('Ошибка', 'Не удалось удалить элемент.');
                    }
                }
            });
        }
    });
});

function getYo(date) {
    const bd = new Date(date), today = new Date();
    let age = today.getFullYear() - bd.getFullYear();
    const tmonth = today.getMonth(), bmonth = bd.getMonth();
    if (tmonth < bmonth || (tmonth === bmonth && today.getDate() < bd.getDate())) {
        age--;
    }
    if (age >= 11 && age <= 14) {
        return age + ' лет';
    }
    if ((age % 10) === 1) {
        return age + ' год';
    }
    if ((age % 10) >= 2 && (age % 10) <= 4) {
        return age + ' года';
    }
    return age + ' лет';
}