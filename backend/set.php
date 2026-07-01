<?php
header("Content-Type: application/json");
include "connect.php";
$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');

if (!$username || !$password) {
    echo json_encode(["success" => false, "message" => "Username and password required"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(50));

try {
    $stmt = $pdo->prepare("SELECT id FROM admin WHERE id = 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $stmt = $pdo->prepare("UPDATE admin SET username = ?, password = ?, token = ? WHERE id = 1");
        $stmt->execute([$username, $hashedPassword, $token]);
        $message = "Admin updated successfully";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admin (id, username, password, token) VALUES (1, ?, ?, ?)");
        $stmt->execute([$username, $hashedPassword, $token]);
        $message = "Admin created successfully";
    }

    echo json_encode([
        "success" => true,
        "message" => $message,
        "token" => $token
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
