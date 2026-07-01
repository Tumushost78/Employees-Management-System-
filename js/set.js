
function showPopup(title, message) {
    const confirmBox = document.getElementById('modernConfirm');
    const cfTitle = document.getElementById('cf-title');
    const cfMessage = document.getElementById('cf-message');
    const cfYes = document.getElementById('cf-yes');

    cfTitle.textContent = title;
    cfMessage.textContent = message;

    confirmBox.style.display = 'flex';

    // Click Yego to close popup
    cfYes.onclick = () => {
        confirmBox.style.display = 'none';
    };
}

// Function to save username and password
function save() {
    const username = document.getElementById('emp-name').value.trim();
    const password = document.getElementById('emp-name2').value;
    const confirmPassword = document.getElementById('emp-dob').value;

    if (!username) {
        showPopup("Ikosa", "Nyamuneka shyiramo izina banga!");
        return;
    }

    if (!password) {
        showPopup("Ikosa", "Nyamuneka shyiramo ijambo banga!");
        return;
    }
    if(password.length < 4){
      showPopup("Ikosa", "Ijambo banga rigomba kuba ari inyuguti 4 cyangwa kuzamura!");
      return;
    }

    if (password !== confirmPassword) {
        showPopup("Ikosa", "Ijambo banga ntabwo rihuye!");
        return;
    }
    const data = {username, password };

    fetch('../backend/set.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
    })
    .then(result => {
        if (result.success) {
             document.getElementById('emp-name').value = '';
            document.getElementById('emp-name2').value = '';
             document.getElementById('emp-dob').value = '';
            showPopup("Byagenze neza", "Ijambo banga ryahinduwe neza!");
            document.getElementById('emp-name-display').textContent = username;
        } else {
            showPopup("Ikosa", "Habaye ikibazo: " + result.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        showPopup("Ikosa", "Habaye ikibazo mu guhindura ijambo banga!");
    });
}

function discard() {
    document.getElementById('emp-name').value = '';
    document.getElementById('emp-name2').value = '';
    document.getElementById('emp-dob').value = '';
    window.location.href = "admin.html";
}
