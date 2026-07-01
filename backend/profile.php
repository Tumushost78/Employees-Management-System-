<?php
header("Content-Type: application/json");
include "connect.php";

// Get POST data
$form = json_decode(file_get_contents("php://input"), true);
$token = trim($form['token']);
$emp_id = $form['emp_id'];
if (!$token || !$emp_id) {
    echo json_encode(["ok" => false, "message" => "Missing token or employee ID."]);
    exit;
}

$stmt_token = $pdo->prepare("SELECT token FROM admin WHERE token = :token_value");
$stmt_token->bindValue(':token_value', $token, PDO::PARAM_STR);
$stmt_token->execute();
$token_db = $stmt_token->fetch(PDO::FETCH_ASSOC);

if (!$token_db) {
    echo json_encode(["ok" => false, "message" => "Invalid token."]);
    exit;
}
  $stmt = $pdo->prepare("SELECT
    e.emp_id AS id,
    e.first_name AS f_name,
    e.last_name AS l_name,
    e.national_id,
    e.birth_date,
    e.phone,
    e.location,
    e.registered_date,
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
WHERE e.emp_id = :emp_id
");
$stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
$stmt->execute();

$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    echo json_encode(["ok" => false, "message" => "Employee not found."]);
    exit;
}

echo json_encode($employee);
?>
