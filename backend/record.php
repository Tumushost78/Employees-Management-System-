<?php
header("Content-Type: application/json");
include "connect.php";

$action = $_GET['action'] ?? '';

$input = json_decode(file_get_contents("php://input"), true) ?? [];
$token = $input['token'] ?? ($_POST['token'] ?? null);

if (!$token) {
    echo json_encode(["success" => false, "message" => "Token missing"]);
    exit;
}

// Verify admin token
$stmt = $pdo->prepare("SELECT id FROM admin WHERE token = :token");
$stmt->bindValue(":token", $token, PDO::PARAM_STR);
$stmt->execute();

if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode(["success" => false, "message" => "Invalid token"]);
    exit;
}

// ================================
// ACTIONS
// ================================

if ($action === "get_departments") {
    $stmt = $pdo->query("SELECT department_id, department_name FROM department");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
if ($action === "record_employee") {

    try {
        $first_name    = trim($_POST['first_name'] ?? '');
        $last_name     = trim($_POST['last_name'] ?? '');
        $birth_date    = $_POST['birth_date'] ?? '';
        $national_id   = trim($_POST['national_id'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $location      = trim($_POST['location'] ?? '');
        $department_id = intval($_POST['department_id'] ?? 0);

        if (!$first_name || !$last_name || !$birth_date || !$national_id || !$phone || !$location || !$department_id) {
            echo json_encode(['success'=>false, 'message'=>'Buri shingiro ntiryuzuye.']);
            exit;
        }

        // Default photo
        $photo_path = '../assets/default.jpg';  // required UI path

        // PHOTO UPLOAD if provided
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {

            $allowed = ['image/jpeg','image/png','image/gif'];

            if (!in_array($_FILES['photo']['type'], $allowed)) {
                echo json_encode(['success'=>false, 'message'=>'Ifoto igomba kuba JPG/PNG/GIF.']);
                exit;
            }

            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $newName = uniqid('emp_').".$ext";
            $target = "../assets/$newName";

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $photo_path = $target;
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO employees 
            (department_id, first_name, last_name, birth_date, national_id, location, phone, email, photo)
            VALUES (:department_id, :first_name, :last_name, :birth_date, :national_id, :location, :phone, :email, :photo)
        ");
      $stmt->execute([
    ':department_id' => $department_id,
    ':first_name' => $first_name,
    ':last_name' => $last_name,
    ':birth_date' => $birth_date,
    ':national_id' => $national_id,
    ':location' => $location,
    ':phone' => $phone,
    ':email' => $email,
    ':photo' => $photo_path
]);

    $emp_id = $pdo->lastInsertId();
    $pay = $pdo->prepare("
    INSERT INTO payment(emp_id) VALUES (:emp_id)
");
   $pay->execute([':emp_id' => $emp_id]);
        echo json_encode(['success'=>true, 'message'=>'Umukozi yinjijwe neza!']);
        exit;

    } catch (Exception $e) {

        if (str_contains($e->getMessage(), 'Duplicate')) {
            echo json_encode(['success'=>false, 'message'=>'Indangamuntu cyangwa Telephone biracyariho.']);
        } else {
            echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success'=>false, 'message'=>'Invalid API action']);
exit;
?>