document.addEventListener("DOMContentLoaded", function () {
    const regex = /^[А-Яа-я'-]+$/; let skills = [];
    const errorDiv = document.getElementById('errorMessages'), skill = document.getElementById("skill");
    skill.addEventListener("keydown", function(event){
        if(event.key === "Enter"){
            event.preventDefault();
            const value = skill.value.trim();
            addChips(value);
            skill.value = "";
        }
    })
    function addChips(value){
        let chips = document.createElement("div"), skillbox = document.getElementById("skillbox");
        skills.push(value);
        chips.addEventListener("click", function(){
            skills = skills.filter(item => item !== value)
            this.remove();
        })
        chips.innerText = value; skillbox.append(chips)
    }
    document.getElementById("form").addEventListener("submit", function (event) {
        event.preventDefault();
        const specialization = document.getElementById("specialization").value.trim(), faculty = document.getElementById("faculty").value.trim(),
            ei = document.getElementById("ei").value.trim();

        if (!regex.test(specialization)) {
            errorDiv.innerHTML = "Поле \"Специальность\" допускает только кириллические символы.";
            return;
        }
        if (!regex.test(faculty)) {
            errorDiv.innerHTML = "Поле \"Факультет\" допускает только кириллические символы.";
            return;
        }
        if (!regex.test(ei)) {
            errorDiv.innerHTML = "Поле \"Образовательное учреждение\" допускает только кириллические символы.";
            return;
        }
        //all ok
        errorDiv.innerText = "Подождите..."; errorDiv.style.color = "black";
        const data = new FormData(this);
        fetch("api/user",{
            method: "POST",
            body: data
        })
        .then(r => r.json())
        .then(data => {
            if (data.success){
                //
            }
        })
    })
})