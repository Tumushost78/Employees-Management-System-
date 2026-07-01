
const sign_in_btn = document.querySelector("#sign-in-btn");
const container = document.querySelector(".container");
const usernameInput = document.getElementById("username");
const passwordInput = document.getElementById("password");
const message = document.getElementById("message");
const login = document.getElementById("login");
function clearAll() {
    usernameInput.value = "";
    passwordInput.value = "";

    setTimeout(() => {
        message.textContent = "Admin";
        message.style.color = "black";
        login.disabled = false;
    }, 3000);
}

sign_in_btn.addEventListener('click', () => {
    container.classList.remove("sign-up-mode");
});

login.addEventListener("click", function(e) {
    e.preventDefault();

    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    if (username === "" || password === "" || password.length < 4) {
        message.textContent = "ntago aribyo Ongera ugerageze";
        message.style.color = "red";
        login.disabled = true;
        clearAll();
        return;
    }
    const dataObject = { username, password };

    async function verify() {
        try {
            const result = await fetch('../backend/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dataObject)
            });

            if (!result.ok) {
                message.textContent = "Ikibazo ⚠️";
                message.style.color = "red";
                login.disabled = true;
                clearAll();
                return;
            }

            const response = await result.json();

            if (!response.ok) {
               message.textContent =response.message;
                message.style.color = "red";
                login.disabled = true;
                clearAll();
            } else {
                message.textContent = response.message;
                message.style.color = "green";
                localStorage.setItem("username",response.username)
                localStorage.setItem("token",response.token);
                usernameInput.value='';
                 passwordInput.value ='';
    
                window.location.href="admin.html";
            }

        } catch (error) {
            message.textContent = "Server Error ⚠️";
            message.style.color = "red";
            login.disabled = true;
            clearAll();
        }
    }

    verify();
});


