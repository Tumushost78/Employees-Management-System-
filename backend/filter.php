<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1); // temporary, remove in production

include "connect.php";

// Get JSON input safely
$form = json_decode(file_get_contents("php://input"), true);
$token = trim($form['token'] ?? '');
$search_query = trim($form['search_query'] ?? '');
$payment_type = strtoupper(trim($form['payment_type'] ?? 'UNPAID'));

// --- Validate token ---
$stmt_token = $pdo->prepare("SELECT token FROM admin WHERE token = :token_value");
$stmt_token->bindValue(':token_value', $token, PDO::PARAM_STR);
$stmt_token->execute();
$token_db = $stmt_token->fetch(PDO::FETCH_ASSOC);

if (!$token_db) {
    echo json_encode(["ok" => false, "message" => "Invalid token"]);
    exit;
}

// --- Payment filter ---
$payment_condition = $payment_type === 'PAID' 
    ? "(p.payment_status = 'PAID')" 
    : "(p.payment_status IS NULL OR p.payment_status != 'PAID')";

// --- Base SQL ---
$sql_base = "
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
FROM employees e
JOIN department d ON e.department_id = d.department_id
LEFT JOIN payment p ON e.emp_id = p.emp_id
LEFT JOIN attendance a ON e.emp_id = a.emp_id
";

// --- Add search conditions ---
$params = [];
$where = "WHERE $payment_condition";

if ($search_query !== '') {
    if (is_numeric($search_query)) {
        $where .= " AND e.emp_id = :search_term";
        $params[':search_term'] = (int)$search_query;
    } else {
        $where .= " AND (e.first_name LIKE :search_term OR e.last_name LIKE :search_term)";
        $params[':search_term'] = '%' . $search_query . '%';
    }
}

// --- Final query with limit ---
$sql_final = $sql_base . " $where ORDER BY e.emp_id ASC LIMIT 50";

$stmt = $pdo->prepare($sql_final);

// Bind parameters
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
}

// Execute query
try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode(["ok" => false, "message" => "SQL Error: " . $e->getMessage()]);
}
