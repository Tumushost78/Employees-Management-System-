
const primaryColor = '#4834d4'
const warningColor = '#f0932b'
const successColor = '#6ab04c'
const dangerColor = '#eb4d4b'

const themeCookieName = 'theme'
const themeDark = 'dark'
const themeLight = 'light'

const body = document.getElementsByTagName('body')[0]

function setCookie(cname, cvalue, exdays) {
  var d = new Date()
  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000))
  var expires = "expires="+d.toUTCString()
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/"
}

function getCookie(cname) {
  var name = cname + "="
  var ca = document.cookie.split(';')
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1)
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length)
    }
  }
  return ""
}

loadTheme()

function loadTheme() {
	var theme = getCookie(themeCookieName)
	body.classList.add(theme === "" ? themeLight : theme)
}

function switchTheme() {
	if (body.classList.contains(themeLight)) {
		body.classList.remove(themeLight)
		body.classList.add(themeDark)
		setCookie(themeCookieName, themeDark)
	} else {
		body.classList.remove(themeDark)
		body.classList.add(themeLight)
		setCookie(themeCookieName, themeLight)
	}
}

function collapseSidebar() {
	body.classList.toggle('sidebar-expand')
}

window.onclick = function(event) {
	openCloseDropdown(event)
}

function closeAllDropdown() {
	var dropdowns = document.getElementsByClassName('dropdown-expand')
	for (var i = 0; i < dropdowns.length; i++) {
		dropdowns[i].classList.remove('dropdown-expand')
	}
}

function openCloseDropdown(event) {
	if (!event.target.matches('.dropdown-toggle')) {
		// 
		// Close dropdown when click out of dropdown menu
		// 
		closeAllDropdown()
	} else {
		var toggle = event.target.dataset.toggle
		var content = document.getElementById(toggle)
		if (content.classList.contains('dropdown-expand')) {
			closeAllDropdown()
		} else {
			closeAllDropdown()
			content.classList.add('dropdown-expand')
		}
	}
}

document.addEventListener("DOMContentLoaded",async function(){
 try {
const token = localStorage.getItem('token');
const dataObject = {search_query:'',token};
const request  = await fetch('../backend/backbone.php', {
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
  const status_color = item.status=="UNPAID"?'red':'green';
const row =`	<tr>
									<td>${item.id}</td>
									<td>${item.f_name} ${item.l_name}</td>
									<td>${item.location}</td>
									<td style="color: ${status_color};">${item.status}</td>
									<td>${item.dp_name}</td>
									<td>${item.attended_day}</td>
									<td>${item.department_salary}</td>
									<td>${item.salary}</td>
									<td><button onclick="get_specifically(${item.id})" class="btn-view"> ibindi</button></td>
								</tr>`; 
 
 parent_data.innerHTML += row;
  
});

} catch (err) {
  console.error('Error:', err);
} 
  
})
function get_specifically(emp_id){
  const token = localStorage.getItem('token');
  const url = `profile.html?emp_id=${encodeURIComponent(emp_id)}&token=${encodeURIComponent(token)}`;
  window.location.href= url;
  
}

async function get_employee_info() {
const abakozi_bose = document.getElementById('abakozi_bose');
const Imishahara_yose = document.getElementById('Imishahara_yose');
const paid = document.getElementById('paid');
const not_paid = document.getElementById('not_paid');
  try {
 const token = localStorage.getItem("token")
const dataObject = {token};
const request =  await fetch('../backend/summary.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(dataObject)
})
if(!request.ok){
  throw new Error('unexpected error...');
}
const reponse = await request.json();
if(reponse.ok){
abakozi_bose.textContent=reponse.bose;  
Imishahara_yose.textContent = reponse.imishahara_yose ? reponse.imishahara_yose: "0.00" ;
paid.textContent= reponse.unpaid_employees? reponse.unpaid_employees: "0";
not_paid.textContent = reponse.money_not_paid ? reponse.money_not_paid : "0.00";
  
}
  } catch (err) {
    console.error('Error:', err);
  }
}
get_employee_info();
setInterval(get_employee_info,8000);

const search_employee = document.getElementById('search_employee');
search_employee.addEventListener("keyup",async function(){
const search_query = search_employee.value;
if(search_query.length == 0){
  return;
}
try {
const token = localStorage.getItem('token');
const dataObject = {search_query,token};
const request  = await fetch('../backend/backbone.php', {
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
if(reponse.length == 0){
  parent_data.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align:center; color:red; font-weight:bold;">
                        Ntago uwomukozi ahari
                    </td>
                </tr>
            `;
            return;
            
}
parent_data.innerHTML = '';
reponse.forEach((item) => {
  const status_color = item.status=="UNPAID"?'red':'green';
const row =`	<tr>
									<td>${item.id}</td>
									<td>${item.f_name} ${item.l_name}</td>
									<td>${item.location}</td>
									<td style="color: ${status_color};">${item.status}</td>
									<td>${item.dp_name}</td>
									<td>${item.attended_day}</td>
									<td>${item.department_salary}</td>
									<td>${item.salary}</td>
									<td><button onclick="get_specifically(${item.id})" class="btn-view"> ibindi</button></td>
								</tr>`; 
 
 parent_data.innerHTML += row;
  
});

} catch (err) {
  console.error('Error:', err);
  
}

})