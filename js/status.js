document.addEventListener("DOMContentLoaded", async function () {
    const token = localStorage.getItem('token');
    const parent_data = document.getElementById('parent_table');
    const search_employee = document.getElementById('search_employee');
    const paymentTypeSelector = document.getElementById('payment_type');

    if (!search_employee) console.error("Search input with id 'search_employee' not found!");

    async function fetchEmployees(search_query = '', payment_type = 'UNPAID') {
        try {
            const dataObject = { search_query, token, payment_type };
            console.log("Fetching employees:", dataObject); // debug

            const request = await fetch('../backend/filter.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataObject)
            });

            const responseText = await request.text();
            console.log("Raw response:", responseText); // debug

            const response = JSON.parse(responseText);

            parent_data.innerHTML = '';

            if (!Array.isArray(response) || response.length === 0) {
                parent_data.innerHTML = `
                    <tr>
                        <td colspan="9" style="text-align:center; color:red; font-weight:bold;">
                            Ntago uwomukozi ahari
                        </td>
                    </tr>
                `;
                return;
            }

            response.forEach((item) => {
                const status_color = item.status === "UNPAID" ? 'red' : 'green';
                const row = `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.f_name} ${item.l_name}</td>
                        <td>${item.location}</td>
                        <td style="color: ${status_color};">${item.status}</td>
                        <td>${item.dp_name}</td>
                        <td>${item.attended_day}</td>
                        <td>${item.department_salary}</td>
                        <td>${item.salary}</td>
                        <td><button onclick="get_specifically(${item.id})" class="btn-view">ibindi</button></td>
                    </tr>`;
                parent_data.innerHTML += row;
            });

        } catch (err) {
            console.error('Error:', err);
            parent_data.innerHTML = `
                <tr>
                    <td colspan="9" style="text-align:center; color:red; font-weight:bold;">
                        Unexpected error occurred
                    </td>
                </tr>
            `;
        }
    }

    // Initial load
    fetchEmployees();

    // Search input
    search_employee?.addEventListener("keyup", () => {
        const search_query = search_employee.value || '';
        const payment_type = paymentTypeSelector.value.toUpperCase();
        fetchEmployees(search_query, payment_type);
    });

    paymentTypeSelector?.addEventListener("change", () => {
        const search_query = search_employee.value || '';
        const payment_type = paymentTypeSelector.value.toUpperCase();
        fetchEmployees(search_query, payment_type);
    });
});

function get_specifically(emp_id) {
    const token = localStorage.getItem('token');
    const url = `profile.html?emp_id=${encodeURIComponent(emp_id)}&token=${encodeURIComponent(token)}`;
    window.location.href = url;
}
