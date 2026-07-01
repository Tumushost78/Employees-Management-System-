<?php
header("Content-Type: text/plain");
include "connect.php";

// Default test admin credentials
$defaultUsername = "admin";
$defaultPassword = "123456"; // you can change this

try {
    // Check if admin with ID = 1 already exists
    $stmt = $pdo->prepare("SELECT id, username, token FROM admin WHERE id = 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        echo "Admin user already exists!\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Password: $defaultPassword\n"; // for testing purposes
        echo "Token: " . $admin['token'] . "\n";
        exit;
    }

    // Admin does not exist, create new one
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(50));

    $stmt = $pdo->prepare("
        INSERT INTO admin (id, username, password, token)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([1, $defaultUsername, $hashedPassword, $token]);

    echo "Test admin created successfully!\n";
    echo "Username: $defaultUsername\n";
    echo "Password: $defaultPassword\n";
    echo "Token: $token\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
