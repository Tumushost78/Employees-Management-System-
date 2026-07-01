<?php
header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$token = trim($data['token'] ?? '');

if (!$username || !$token) {
    echo json_encode(["ok" => false, "message" => "Ntago bishobotse"]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username LIMIT 1");
$stmt->bindValue(':username', $username);
$stmt->execute();

$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo json_encode(["ok" => false, "message" => "Ntago byabonetse"]);
    exit;
}

if ($admin['token'] === $token) {
    echo json_encode([
        "ok" => true,
        "message" => "byakunze",
        "username" => $admin['username'],
        "token" => $admin['token']
    ]);
} else {
    echo json_encode(["ok" => false, "message" => "token ntago yabonetse"]);
}
