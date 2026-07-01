// showPopup function
function showPopup(message, callback = null) {
    const modal = document.getElementById('modernConfirm');
    const title = document.getElementById('cf-title');
    const msg = document.getElementById('cf-message');
    const yesBtn = document.getElementById('cf-yes');

    title.textContent = "Kumenyesha";
    msg.textContent = message;
    modal.style.display = "flex";

    function closeModal() {
        modal.style.display = "none";
        yesBtn.removeEventListener('click', closeModal);
        if(callback) callback();
    }

    yesBtn.addEventListener('click', closeModal);
}

// discard function
function discard(){
    window.location.href="admin.html";
}

document.addEventListener('DOMContentLoaded', () => {
    loadDepartments();

    const photoInput = document.getElementById('photo-input');
    photoInput.addEventListener('change', function() {
        const file = this.files[0];
        if(file){
            document.getElementById('emp-photo').src = URL.createObjectURL(file);
        }
    });
});
async function loadDepartments() {
    try {
        const res = await fetch('../backend/record.php?action=get_departments', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ token: localStorage.getItem("token") })
        });

        const data = await res.json();
        const select = document.getElementById('emp-department');

        data.forEach(dep => {
            const option = document.createElement('option');
            option.value = dep.department_id;
            option.textContent = dep.department_name;
            select.appendChild(option);
        });

    } catch (err) {
        console.error(err);
        showPopup('Ntibishobotse kubona departema.');
    }
}
async function record() {
    const first_name    = document.getElementById('emp-name').value.trim();
    const last_name     = document.getElementById('emp-name2').value.trim();
    const birth_date    = document.getElementById('emp-dob').value;
    const national_id   = document.getElementById('emp-national-id').value.trim();
    const phone         = document.getElementById('emp-phone').value.trim();
    const email         = document.getElementById('emp-email').value.trim();
    const location      = document.getElementById('emp-location').value.trim();
    const department_id = document.getElementById('emp-department').value;
    const photoFile     = document.getElementById('photo-input').files[0];

    // Validation
    if(!first_name || !last_name || !birth_date || !national_id || !phone || !location || !department_id){
        showPopup('Banza Wuzuze Amakuru Asambwa.');
        return;
    }

    const formData = new FormData();
    formData.append('first_name', first_name);
    formData.append('last_name', last_name);
    formData.append('birth_date', birth_date);
    formData.append('national_id', national_id);
    formData.append('phone', phone);
    formData.append('email', email);
    formData.append('location', location);
    formData.append('department_id', department_id);
    formData.append("token", localStorage.getItem("token"));
    if(photoFile) formData.append('photo', photoFile);

    try {
        const res = await fetch('../backend/record.php?action=record_employee', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        showPopup(data.message, () => {
            if(data.success){
                // Clear input fields
                document.querySelectorAll('#employee-profile input, #employee-profile select')
                    .forEach(el => el.value = '');
                // Reset photo
                document.getElementById('emp-photo').src = 'assets/default.jpg';
            }
        });

    } catch(err) {
        console.error(err);
        showPopup("Habaye ikibazo mu gutunga umukozi");
    }
}
