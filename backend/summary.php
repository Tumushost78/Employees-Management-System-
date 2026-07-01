<?php
header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"), true);
$token = $data["token"] ?? '';
if (!$token) {
    echo json_encode(["ok" => false, "message" => "ntago bishoboka"]);
    exit;
}

$stmt_token = $pdo->prepare("SELECT token FROM admin WHERE token = :token_value");
$stmt_token->bindValue(':token_value', $token, PDO::PARAM_STR);
$stmt_token->execute();
$token_db = $stmt_token->fetch(PDO::FETCH_ASSOC);

if (!$token_db) {
    echo json_encode(["ok" => false, "message" => "impossible!"]);
    exit;
}
$stmt_count = $pdo->query("SELECT COUNT(emp_id) AS abakozi_bose FROM employees");
$result = $stmt_count->fetch(PDO::FETCH_ASSOC);
$money_status_stmt = $pdo->query("
    SELECT 
        SUM(
            CASE 
                WHEN COALESCE(p.payment_status, 'UNPAID') = 'PAID'
                THEN d.department_salary * COALESCE(a.attended_day, 0)
                ELSE 0
            END
        ) AS money_paid,

        SUM(
            CASE 
                WHEN COALESCE(p.payment_status, 'UNPAID') = 'UNPAID'
                THEN d.department_salary * COALESCE(a.attended_day, 0)
                ELSE 0
            END
        ) AS money_not_paid,

        COUNT(
            CASE
                WHEN COALESCE(p.payment_status, 'UNPAID') = 'UNPAID'
                THEN 1
                ELSE NULL
            END
        ) AS unpaid_employees
    FROM employees e
    JOIN department d ON e.department_id = d.department_id
    LEFT JOIN attendance a ON e.emp_id = a.emp_id
    LEFT JOIN payment p ON e.emp_id = p.emp_id
");

$money = $money_status_stmt->fetch(PDO::FETCH_ASSOC);

// Total expected salary
$total_salaries = $money['money_paid'] + $money['money_not_paid'];

$org_data = [
    "ok" => true,
    "bose" => $result["abakozi_bose"],
    "imishahara_yose" => $total_salaries,

    // REPLACED: money_paid → unpaid_employees
    "unpaid_employees" => $money['unpaid_employees'],

    "money_not_paid" => $money['money_not_paid']
];

echo json_encode($org_data);
?>
