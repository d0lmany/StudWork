import * as functions from './index.functions.js';
const id = functions.getCookie('id');
const role = functions.getCookie('role');

async function deleteAccount() {
    fetch('api/users/' + id, {
        method: 'DELETE',
        headers: {'Content-type': 'application/json'}
    })
    .then(r => r.json())
    .then(r => {console.table(r); window.location.reload();})
    .catch(er => console.error('Ошибка удаления аккаунта: '+er))
}

async function appellation() {
    let insideId = null;
    fetch('api/'+role+'s/filtered', {
        method: 'POST',
        headers: {'Content-type': 'application/json'},
        body: JSON.stringify({user_id: id})
    })
    .then(r => r.json())
    .then(r => {
        console.table(r);
        insideId = r[0].id;
        return fetch('api/'+role+'s/'+insideId, {
            method: 'PATCH',
            headers: {'Content-type': 'application/json'},
            body: JSON.stringify({is_verified: 0})
        })
    })
    .then(r => r.json())
    .then(r => {console.table(r); window.location.reload();})
    .catch(er => console.error('Ошибка при подаче апелляции: '+er))
}

document.getElementById('deleteAccount').addEventListener('click', deleteAccount);
document.getElementById('appellation').addEventListener('click', appellation);