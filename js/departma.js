document.addEventListener("DOMContentLoaded",async function(){
 try {
 const emp_id = null;
 const action = "departma_get";
const token = localStorage.getItem('token');
const dataObject = {token,emp_id,action};
const request  = await fetch('../backend/actions.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(dataObject)
})
if(!request.ok){
  throw new Error('Error searching.....');
}
const reponse = await request.json();
const parent_data = document.getElementById('parent_table');
parent_data.innerHTML = '';
reponse.forEach((item) => {
const row = `<tr id="row-${item.department_id}">
  <td>${item.department_id}</td>
  <td>${item.department_name}</td>
  <td>${item.department_salary}</td>
  <td>${item.total_employees}</td>
  <td>${item.total_department_salary}</td>
  <td>${item.total_attended_days}</td>
  <td><button onclick="editDepartment(${item.department_id}, '${item.department_name}', ${item.department_salary})" class="btn-view">Hindura</button></td>
  <td><button onclick="openDeleteModal(${item.department_id})" class="btn-del">Gusiba</button></td>

</tr>`;


 
 parent_data.innerHTML += row;
  
});

} catch (err) {
  console.error('Error:', err);
} 
})
// Open modal with pre-filled data
function editDepartment(deptId, deptName, deptSalary){
    document.getElementById('edit_department_id').value = deptId;
    document.getElementById('edit_department_name').value = deptName;
    document.getElementById('edit_department_salary').value = deptSalary;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal(){
    document.getElementById('editModal').style.display = 'none';
}

function showToast(message, success=true){
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.backgroundColor = success ? '#4CAF50' : '#f44336';
    toast.style.display = 'block';
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

function editDepartment(deptId, deptName, deptSalary){
    document.getElementById('edit_department_id').value = deptId;
    document.getElementById('edit_department_name').value = deptName;
    document.getElementById('edit_department_salary').value = deptSalary;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal(){
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const deptId = document.getElementById('edit_department_id').value;
    const deptName = document.getElementById('edit_department_name').value;
    const deptSalary = document.getElementById('edit_department_salary').value;
    const token = localStorage.getItem('token');

    try{
        const response = await fetch('../backend/actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                token,
                emp_id: deptId,
                department_name: deptName,
                department_salary: deptSalary,
                action: 'update_department'
            })
        });
        const data = await response.json();
        showToast(data.message, data.success);
        if(data.success){
            const row = document.getElementById(`row-${deptId}`);
            row.cells[1].textContent = deptName;
            row.cells[2].textContent = deptSalary;
            closeModal();
        }
    } catch(err){
        console.error(err);
        showToast('Hari ikibazo!', false);
    }
});

// Delete Department
async function deleteDepartment(deptId){
    if(!confirm("Urashaka koko gusiba iyi department?")) return;
    const token = localStorage.getItem('token');
    try{
        const response = await fetch('../backend/actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({token, emp_id: deptId, action: 'delete_department'})
        });
        const data = await response.json();
        showToast(data.message, data.success);
        if(data.success){
            document.getElementById(`row-${deptId}`).remove();
        }
    } catch(err){
        console.error(err);
        showToast('Hari ikibazo!', false);
    }
}
let departmentToDelete = null;

function openDeleteModal(deptId){
    departmentToDelete = deptId;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal(){
    departmentToDelete = null;
    document.getElementById('deleteModal').style.display = 'none';
}

// When user clicks "Delete" inside the modal
document.getElementById('confirmDelete').addEventListener('click', async function(){
    if(!departmentToDelete) return;
    const token = localStorage.getItem('token');
    try {
        const response = await fetch('../backend/actions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({token, emp_id: departmentToDelete, action: 'delete_department'})
        });
        const data = await response.json();
        showToast(data.message, data.success);
        if(data.success){
            document.getElementById(`row-${departmentToDelete}`).remove();
        }
        closeDeleteModal();
    } catch(err){
        console.error(err);
        showToast('Hari ikibazo!', false);
        closeDeleteModal();
    }
});

function add_department(){
    document.getElementById('add_department_name').value = '';
    document.getElementById('add_department_salary').value = '';
    document.getElementById('addModal').style.display = 'flex';
}

function closeAddModal(){
    document.getElementById('addModal').style.display = 'none';
}

document.getElementById('addForm').addEventListener('submit', async function(e){
    e.preventDefault();

    const deptName = document.getElementById('add_department_name').value.trim();
    const deptSalary = document.getElementById('add_department_salary').value.trim();
    const token = localStorage.getItem('token');

    try{
        const res = await fetch('../backend/actions.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({
                token,
                department_name: deptName,
                department_salary: deptSalary,
                action: 'add_department'
            })
        });

        const data = await res.json();
        showToast(data.message, data.success);
    } catch(err){
        console.error(err);
        showToast('Departma Yagiyemo  refreshing', true);
          closeAddModal();

    }
});


