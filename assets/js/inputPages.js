document.addEventListener("DOMContentLoaded", function () {
    const regex = /^[А-ЯЁа-яё\s\-\.]+$/, regexSkill = /^[a-zA-Zа-яА-ЯёЁ0-9\s#-]+$/u, regexOrg = /^(?!\s+$)[a-zA-Zа-яА-ЯёЁ\s\-'"]+$/u; let skills = [];
    const errorDiv = document.getElementById('errorMessages'), skill = document.getElementById("skill");
    if (document.getElementsByName('fromHere')[0].value == 'student') {
        skill.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                const value = skill.value.trim();
                addChips(value);
                skill.value = "";
            }
        })
    }
    function addChips(value) {
        let chips = document.createElement("div"), skillBox = document.getElementById("skillBox");
        chips.addEventListener("click", function () {
            skills = skills.filter(item => item !== value)
            this.remove();
        })
        if (regexSkill.test(value)) {
            skills.push(value);
            chips.innerText = value;
            skillBox.append(chips)
        }
    }
    document.getElementById("form").addEventListener("submit", function (event) {
        event.preventDefault();
        if (document.getElementsByName('fromHere')[0].value == 'student') {
            const specialization = document.getElementById("specialization").value.trim(), faculty = document.getElementById("faculty").value.trim(),
                ei = document.getElementById("ei").value.trim();

            if (!regex.test(specialization)) {
                errorDiv.innerHTML = "Поле \"Специальность\" допускает только кириллические символы, тире, точки и пробелы.";
                return;
            }
            if (!regex.test(ei)) {
                errorDiv.innerHTML = "Поле \"Образовательное учреждение\" допускает только кириллические символы, тире, точки и пробелы.";
                return;
            }
            document.getElementById('skills').value = JSON.stringify(skills);
            subm();
        } else {
            const orgName = document.getElementById('orgName').value.trim(), code = document.getElementById('code').value.trim();
            if (!regexOrg.test(orgName)) {
                errorDiv.innerHTML = "Поле \"Название организации\" допускает только кириллицу, латиницу, тире и кавычки.";
                return;
            }
            if (!isNumeric(code)) {
                errorDiv.innerHTML = "Поле \"ИНН или ОГРН\" допускает только числовые значения.";
                return;
            }
            subm();
        }

    })
    function subm() {
        errorDiv.innerText = "Подождите..."; errorDiv.style.color = "black";
        setTimeout(() => {
            document.getElementById("form").submit();
        }, 500);
    }
    const isNumeric = value => 
        !isNaN(value) && 
        !isNaN(parseFloat(value)) && 
        (typeof value === 'number' || 
         (typeof value === 'string' && value.trim() !== ''));
})