function openModal() {
    const modal = document.getElementById('authModal');
    modal.style.display = 'flex';
}
function closeModal() {
    const modal = document.getElementById('authModal');
    modal.style.display = 'none';
}
document.addEventListener("DOMContentLoaded", function(){
    document.getElementById('closeModal').addEventListener('click', closeModal);
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', checkAuth);
    });
    document.getElementById('registrationForm').addEventListener('submit', function (event) {
        event.preventDefault();
        document.getElementById('errorMessages').innerHTML = '';
        const fname = document.getElementById('fname').value.trim();
        const lname = document.getElementById('lname').value.trim();
        const pat = document.getElementById('pat').value.trim();
        const email = document.getElementById('email').value.trim();
        const pas1 = document.getElementById('pas1').value.trim();
        const pas2 = document.getElementById('pas2').value.trim();
        const date = document.getElementById('date').value;
        const city = document.getElementById('city').value.trim();
        const role = document.getElementById('role').value;
        const cyrillicRegex = /^[А-ЯЁа-яё'-]+$/, emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[&_-])[A-Za-z\d&_-]{8,}$/;
        const cityRegex = /^[А-ЯЁа-яё\s-]+$/;
        if (!cyrillicRegex.test(fname)) {
            showError('Имя должно содержать только кириллицу.');
            return;
        }
        if (!cyrillicRegex.test(lname)) {
            showError('Фамилия должна содержать только кириллицу.');
            return;
        }
        if (pat && !cyrillicRegex.test(pat)) {
            showError('Отчество должно содержать только кириллицу.');
            return;
        }
        if (!emailRegex.test(email)) {
            showError('Введите корректный email.');
            return;
        }
        if (emailUnique()) {
            return;
        }
        if (!passwordRegex.test(pas1)) {
            showError('Пароль должен содержать минимум 8 символов, включая заглавные и строчные буквы, цифры и спец. символы (&, _, -).');
            return;
        }
        if (pas1 !== pas2) {
            showError('Пароли не совпадают.');
            return;
        }
        const birthDate = new Date(date);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        if (age < 16 || (age === 16 && today.getMonth() < birthDate.getMonth()) || (age === 16 && today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
            showError('Вы должны быть старше 16 лет.');
            return;
        }
        if (!cityRegex.test(city)) {
            showError('Город должен содержать только кириллицу.');
            return;
        }
        if (!role) {
            showError('Выберите роль.');
            return;
        }
        this.submit();
    });
})
async function emailUnique(email) {
    fetch('api/users/filtered', {
        method: 'POST',
        headers: { 'Content-type': 'application/json' },
        body: JSON.stringify({email: email})
    })
    .then(r => r.json())
    .then(r => {
        if (r.length > 0) {
            showError('Такой email уже занят');
            return true;
        }
    })
    return false;
}
function showError(message) {
    const errorDiv = document.getElementById('errorMessages');
    errorDiv.innerHTML = message;
}