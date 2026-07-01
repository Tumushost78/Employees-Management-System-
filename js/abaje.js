document.addEventListener("DOMContentLoaded", function () {
    const token = localStorage.getItem('token');
    const parent_table = document.getElementById('parent_table');
    const search_employee = document.getElementById('search_employee');

    async function fetchAttendance(search_query = '') {
        try {
            const dataObject = {
                token: token,
                emp_id: null,
                action: 'abaje',
                search_query: search_query
            };

            const response = await fetch('../backend/actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataObject)
            });

            if (!response.ok) throw new Error('Failed to fetch attendance data');

            const result = await response.json();
            parent_table.innerHTML = '';

            if (result.length === 0) {
                parent_table.innerHTML = `
                    <tr>
                        <td colspan="10" style="text-align:center; color:red; font-weight:bold;">
                            Ntago uwomukozi ahari uyu munsi
                        </td>
                    </tr>`;
                return;
            }

            result.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.emp_id}</td>
                    <td>${item.first_name} ${item.last_name}</td>
                    <td>${item.department_name}</td>
                    <td>${item.department_salary}</td>
                    <td>${item.attended_day}</td>
                    <td>${item.total_salary}</td>
                    <td>${item.attended_date}</td>
                `;
                // Add the "ibindi" button dynamically
                const btnTd = document.createElement('td');
                const btn = document.createElement('button');
                btn.textContent = 'ibindi';
                btn.classList.add('btn-view');
                btn.addEventListener('click', () => get_specifically(item.emp_id));
                btnTd.appendChild(btn);
                row.appendChild(btnTd);

                parent_table.appendChild(row);
            });

        } catch (err) {
            console.error('Error:', err);
        }
    }

    // Initial fetch
    fetchAttendance();

    // Real-time search
    search_employee.addEventListener("keyup", () => {
        const query = search_employee.value.trim();
        fetchAttendance(query);
    });
});

// Redirect to employee profile
function get_specifically(emp_id) {
    const token = localStorage.getItem('token');
    const url = `profile.html?emp_id=${encodeURIComponent(emp_id)}&token=${encodeURIComponent(token)}`;
    window.location.href = url;
}
