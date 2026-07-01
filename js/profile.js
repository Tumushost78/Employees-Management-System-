async function profile_data() {
    try {
        const Params = new URLSearchParams(window.location.search);
        const emp_id = Params.get('emp_id');
        const token = Params.get('token');

        if (!emp_id || !token) {
            console.error("Employee ID or token missing in URL.");
            return;
        }
        const response = await fetch('../backend/profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ emp_id, token })
        });

        if (!response.ok) {
            throw new Error(`Server returned status ${response.status}`);
        }

        const data = await response.json();

        if (!data) {
            console.error("No profile data returned.");
            return;
        }

    
        const emp = Array.isArray(data) ? data[0] : data;
        document.getElementById('emp-photo').src = emp.photo || 'assets/default.jpg';
        document.getElementById('emp-name-display').textContent = `${emp.f_name || ''} ${emp.l_name || ''}`;
        document.getElementById('emp-name').value = emp.l_name;
        document.getElementById('emp-name2').value = emp.f_name;
        document.getElementById('emp-dob').value = emp.birth_date || '';
        document.getElementById('emp-national-id').value = emp.national_id || '';
        document.getElementById('emp-phone').value = emp.phone || '';
        document.getElementById('emp-email').value = emp.email || '';
        document.getElementById('emp-department').value = emp.dp_name || '';
        document.getElementById('emp-location').value = emp.location || '';
        document.getElementById('emp-start').value = emp.registered_date;
        document.getElementById('emp-attend').value = emp.attended_day;
        const salary = parseFloat(emp.salary) || 0;
        document.getElementById('emp-salary').value = salary.toLocaleString('en-US');
        document.getElementById("emp-commitment").value = emp.punishment_amount;
        document.getElementById("p-description").value = emp.punishment;
     const payment_status = document.getElementById('emp-position-display');
      if (emp.status =="UNPAID") {
     payment_status.textContent=emp.status;
     payment_status.style.color= 'red';
     }else{
     payment_status.textContent=emp.status;
    payment_status.style.color= 'green';
     }
    } catch (err) {
        console.error('Error fetching profile:', err);
      
    }
}

profile_data();
function modernConfirm(message) {
    return new Promise(resolve => {

        const overlay = document.getElementById("modernConfirm");
        const msg = document.getElementById("cf-message");
        const yesBtn = document.getElementById("cf-yes");
        const noBtn = document.getElementById("cf-no");

        msg.textContent = message;
        overlay.style.display = "flex";

        const close = (result) => {
            overlay.style.display = "none";
            yesBtn.onclick = null;
            noBtn.onclick = null;
            resolve(result);
        };

        yesBtn.onclick = () => close(true);
        noBtn.onclick = () => close(false);
    });
}
async function runWithConfirm(message, asyncFunction) {
    const ok = await modernConfirm(message);
    if (!ok) return;

    await asyncFunction();
}

async function DeleteClicked() {
    await runWithConfirm("Urashaka Gusiba Uyu Umukozi ?", async () => {
        const Params = new URLSearchParams(window.location.search);
        const emp_id = Params.get('emp_id');
        const token = Params.get('token');

        try {
            const req = await fetch('../backend/actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ emp_id, token, action: 'delete' })
            });

            if (!req.ok) {
                await modernConfirm('Ntago Bishobotse kubera internet');
                return;
            }

            const result = await req.json();
            await modernConfirm(result.message);

            if (result.success) { 
                window.location.href = "admin.html"; 
            }

        } catch (err) {
            console.error(err);
            await modernConfirm('Habaye ikibazo mu gusiba umukozi.');
        }
    });
}

async function saveClicked() {
    await runWithConfirm("Urashaka Guhindura Amakuru Y'Umukozi ?", async () => {

        const params = new URLSearchParams(window.location.search);
        const emp_id = params.get('emp_id');
        const token = params.get('token');

        const dataObject = {
            emp_id,
            token,
            action: 'update',
            first_name: document.getElementById('emp-name').value.trim(),
            last_name: document.getElementById('emp-name2').value.trim(),
            birth_date: document.getElementById('emp-dob').value,
            national_id: document.getElementById('emp-national-id').value.trim(),
            phone: document.getElementById('emp-phone').value.trim(),
            email: document.getElementById('emp-email').value.trim(),
            location: document.getElementById('emp-location').value.trim(),
            punishment_amount: document.getElementById("emp-commitment").value,
            punishment_desc: document.getElementById("p-description").value.trim()
        };

        try {

            const response = await fetch('../backend/actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataObject)
            });

            if (!response.ok) {
                await modernConfirm('Ntago bishobotse kubera internet.');
                return;
            }

            const result = await response.json();

            await modernConfirm(result.message || "Operation completed.");

            if (result.success) {
                profile_data();  // Refresh profile data
            }

        } catch (error) {
            console.error(error);
            await modernConfirm('Habaye ikibazo mu guhindura amakuru y’umukozi.');
        }
    });
}

async function payClicked() {
    await runWithConfirm("Urashaka Kwishyura Umukozi ?", async () => {

        const params = new URLSearchParams(window.location.search);
        const emp_id = params.get('emp_id');
        const token = params.get('token');

        const dataObject = {
            emp_id,
            token,
            action: 'pay'
        };

        try {
            const response = await fetch('../backend/actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataObject)
            });

            if (!response.ok) {
                await modernConfirm('Ntago bishobotse kubera internet.');
                return;
            }

            const result = await response.json();

            if (result.success) {
            const mgs = `${result.message}
Umushahara Wose: ${result.gross_amount} Frw
Igihango: ${result.punishment_amount} Frw
Umushahara Arabwakiriye: ${result.money_recieved} Frw`;


                await modernConfirm(mgs);

                profile_data(); // refresh after payment
            } 
            else {
                await modernConfirm(result.message);
            }

        } catch (error) {
            console.error(error);
            await modernConfirm('Habaye ikibazo ..');
        }

    });
}

async function attendClicked() {
    await runWithConfirm("Uyumunsi Uyumukozi yitabiriye ?", async () => {

        const params = new URLSearchParams(window.location.search);
        const emp_id = params.get('emp_id');
        const token = params.get('token');

        const dataObject = {
            emp_id,
            token,
            action: 'attended'
        };

        try {
            const response = await fetch('../backend/actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataObject)
            });

            if (!response.ok) {
                await modernConfirm('Ntago bishobotse kubera internet.');
                return;
            }

            const result = await response.json();

            // Show backend message
            await modernConfirm(result.message);

            // Refresh profile data after attendance
            profile_data();

        } catch (error) {
            console.error(error);
            await modernConfirm('Habaye ikibazo mu gukurikirana attendance.');
        }

    });
}
async function historyClicked() {
    const params = new URLSearchParams(window.location.search);
    const emp_id = params.get('emp_id');
    const token = params.get('token');

    if (!emp_id || !token) {
        await modernConfirm("ID y'umukozi cyangwa token ntiboneka!");
        return;
    }

    window.open(`../backend/generate_pdf.php?emp_id=${encodeURIComponent(emp_id)}&token=${encodeURIComponent(token)}`, '_blank');
}
