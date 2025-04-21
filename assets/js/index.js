import * as functions from './index.functions.js';

const box = document.querySelector('.cards');

functions.getCards(box);
document.getElementById('searches').addEventListener('click', () => {
    functions.getCards(box, document.getElementById('text').value.trim());
});

document.getElementById('reset').addEventListener('click', () => {
    functions.getCards(box);
});

if (functions.getCookie('role') == 'employer') {
    document.getElementById('salary').remove();
    const form = document.getElementById('thatForm');
    const targetNode = document.createElement('select');
    const targets = await fetch('api/target/filtered', {
        method: 'POST',
        headers: functions.headers,
        body: JSON.stringify([])
    });
    targetNode.innerHTML = '<option value="">Направление</option>';
    targetNode.setAttribute('id', 'targetInput');
    targetNode.style.order = -1;
    const data = await targets.json();
    data.forEach(element => {
        const opt = document.createElement('option');
        opt.setAttribute('value', element.id);
        opt.innerText = element.name;
        targetNode.append(opt);
    });
    form.append(targetNode);
}

document.getElementById('filters').addEventListener('click', () => {
    if (functions.getCookie('role') == 'student') {
        functions.getFilteredCardsEmployers(box, functions.currentData,
            document.getElementById('salary').value, document.getElementById('location').value);
    } else {
        functions.getFilteredCardsStudents(box, functions.currentData,
            document.getElementById('targetInput').value, document.getElementById('location').value);
    }
});