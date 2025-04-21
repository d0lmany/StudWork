import * as functions from './index.functions.js';

const root = Number((new URLSearchParams(window.location.search)).get('id'));
if (!root) {
    window.location.href = '/';
}
let edu, skills;
document.addEventListener('DOMContentLoaded', async () => {
    //fetches
    let student = await fetch('api/students/filtered', {
        method: 'POST',
        headers: functions.headers,
        body: JSON.stringify({id: root})
    });
    if (student.ok) {
        student = await student.json();
        student = await student[0];
        edu = JSON.parse(student.education);
        skills = JSON.parse(student.skills);
    } else {
        alert('Непредвиденная ошибка');
        window.location.href = '/';
    }
    let user = await fetch('api/users/filtered', {
        method: 'POST',
        headers: functions.headers,
        body: JSON.stringify({id: student.user_id})
    });
    if (user.ok) {
        user = await user.json();
        user = await user[0];
    } else {
        alert('Непредвиденная ошибка');
        window.location.href = '/';
    }
    //boxes
    const specialization = document.querySelectorAll('.specialization');
    const fio = document.getElementById('fio');
    const img = document.querySelector('.img');
    const city = document.getElementById('city');
    const yo = document.getElementById('yo');
    const status = document.getElementById('status');
    const desc = document.getElementById('desc');
    const ei = document.getElementById('ei');
    const level = document.getElementById('level');
    const faculty = document.getElementById('faculty');
    const yog = document.getElementById('yog');
    const skillsBox = document.getElementById('skills');
    //render
    specialization.forEach(el => {el.innerText = edu.specialization});
    fio.innerText = user.full_name;
    img.setAttribute('style', "background-image: url('content/avatars/"+user.path+"')");
    city.innerText = user.city;
    yo.innerText = getYo(user.birthday);
    switch (student.is_verified) {
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
    desc.innerText = user.description?? 'Пользователь не предоставил описания.';
    ei.innerText = edu.ei;
    level.innerText = edu.level;
    fetch('api/target/filtered', {
        method: 'POST',
        headers: functions.headers,
        body: JSON.stringify({id: edu.faculty})
    })
    .then(r => r.json())
    .then(r => {
        r = r[0];
        faculty.innerText = r.name;
    })
    yog.innerText = edu.yog;
    skills.forEach(el => {
        const a = document.createElement('div');
        a.innerText = el;
        skillsBox.append(a);
    });
    //buttons
    document.getElementById('invite').addEventListener('click', () => {functions.openInvite(root)})
    document.getElementById('favorite').addEventListener('click', () => {functions.addToFavorites(root)})
    document.getElementById('chat').addEventListener('click', async () => {
        let list = await functions.getInviteList();
        list = await list.filter(el => {
            return el.student_id == root;
        });
        if (list.length == 0) {
            openAlert('Чат', 'Чтобы открыть чат - требуется приглашение.');
        } else {
            window.location.href = 'chats?id='+root;
        }
    });
    document.getElementById('claim').addEventListener('click', () => {functions.report(root, 'students')});
})
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