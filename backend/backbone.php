<?php
header("Content-Type: application/json");
include "connect.php";
$form = json_decode(file_get_contents("php://input"), true);
$token = $form["token"] ?? '';
if (!$token) {
    echo json_encode(["ok" => false, "message" => "ntago bishoboka"]);
    exit;
}
$search_query =  $form['search_query'] ?? '';
$stmt_token = $pdo->prepare("select token from admin where token = :token_value");
$stmt_token->bindValue(':token_value',$token,PDO::PARAM_STR);

$stmt_token->execute();
$token_db = $stmt_token->fetch(PDO::FETCH_ASSOC);

if(!$token_db){
echo json_encode(["ok"=>false,"message"=>"impossible!"]);
exit;
}

if(!$search_query){
$stmt = $pdo->query("
SELECT
    e.emp_id AS id,
    e.first_name AS f_name,
    e.last_name AS l_name,
    e.national_id,
    e.birth_date,
    e.phone,
    e.location,
    e.photo,
    e.email,
    e.punishment,
    e.punishment_amount,
    d.department_name AS dp_name,
    COALESCE(a.attended_day, 0) AS attended_day,
    d.department_salary,
    (d.department_salary * COALESCE(a.attended_day, 0)) AS salary,
    COALESCE(p.payment_status, 'UNPAID') AS status
FROM department d
JOIN employees e ON d.department_id = e.department_id
LEFT JOIN payment p ON e.emp_id = p.emp_id
LEFT JOIN attendance a ON e.emp_id = a.emp_id
LIMIT 8
");

$info = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($info)
;
}else{
  if(is_numeric($search_query)){
  $search = (int)$search_query;
  $stmt = $pdo->prepare("SELECT
    e.emp_id AS id,
    e.first_name AS f_name,
    e.last_name AS l_name,
    e.national_id,
    e.birth_date,
    e.phone,
    e.location,
    e.photo,
    e.email,
    e.punishment,
    e.punishment_amount,
    d.department_name AS dp_name,
    COALESCE(a.attended_day, 0) AS attended_day,
    d.department_salary,
    (d.department_salary * COALESCE(a.attended_day, 0)) AS salary,
    COALESCE(p.payment_status, 'UNPAID') AS status
FROM employees e
JOIN department d ON d.department_id = e.department_id
LEFT JOIN payment p ON e.emp_id = p.emp_id
LEFT JOIN attendance a ON e.emp_id = a.emp_id
WHERE e.emp_id = :search_term;
");
$stmt->bindParam(':search_term',$search);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
}else{
  $search = '%'.$search_query.'%';
  $stmt = $pdo->prepare("SELECT
    e.emp_id AS id,
    e.first_name AS f_name,
    e.last_name AS l_name,
    e.national_id,
    e.birth_date,
    e.phone,
    e.location,
    e.photo,
    e.email,
    e.punishment,
    e.punishment_amount,
    d.department_name AS dp_name,
    COALESCE(a.attended_day, 0) AS attended_day,
    d.department_salary,
    (d.department_salary * COALESCE(a.attended_day, 0)) AS salary,
    COALESCE(p.payment_status, 'UNPAID') AS status
FROM employees e
JOIN department d ON d.department_id = e.department_id
LEFT JOIN payment p ON e.emp_id = p.emp_id
LEFT JOIN attendance a ON e.emp_id = a.emp_id
WHERE e.first_name LIKE :search_term OR
e.last_name LIKE :search_term");

$stmt->bindParam(':search_term',$search);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);  
}
}