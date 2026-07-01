<?php
header("Content-Type: application/json");
include "connect.php";

// Read input
$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (!$username || !$password) {
    echo json_encode(["ok" => false, "message" => "Nta Jambo banga ruinjiye"]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
$stmt->bindValue(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode(["ok" => false, "message" => "ntago aribyo Ongera ugerageze"]);
    exit;
}

if (password_verify($password, $admin['password'])) {
    echo json_encode([
        "ok" => true,
        "message" => "byakunze",
        "username" => $admin['username'],
        "token" => $admin['token']
    ]);
} else {
    echo json_encode(["ok" => false, "message" => "ntago aribyo Ongera ugerageze"]);
}
?>
