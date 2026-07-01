<?php
header("Content-Type: application/json");
include "connect.php";
$pdo->exec("SET time_zone = '+02:00'");
$form = json_decode(file_get_contents("php://input"), true);
$search_query = $form['search_query'] ?? '';
$token = $form['token'];
$emp_id = $form['emp_id'];
$action = $form['action'];
$stmt_token = $pdo->prepare("SELECT token FROM admin WHERE token = :token_value");
$stmt_token->bindValue(':token_value', $token, PDO::PARAM_STR);
$stmt_token->execute();
$token_db = $stmt_token->fetch(PDO::FETCH_ASSOC);

if (!$token_db) {
    echo json_encode(["success" => false, "message" => "impossible!"]);
    exit;
}

if ($action === 'delete') {
  $stmt1 = $pdo->prepare("select photo from employees where emp_id = :emp_id");
  
  $stmt1->bindValue(':emp_id',$emp_id,PDO::PARAM_INT);
  
  $stmt1->execute();
  
  $row = $stmt1->fetch(PDO::FETCH_ASSOC);
  
  if(!$stmt1->rowCount() > 0){
    
   echo json_encode(["success" => false, "message" => "Ntago bishobotse Gusiba Umukozi"]);
   
  exit;
  }
  $photo = $row['photo'] == "../assets/default.jpg"? null : $row['photo'];
  if($photo && file_exists($photo)){
    
    unlink($photo);
    
  }
    $stmt = $pdo->prepare("DELETE FROM employees WHERE emp_id = :emp_id");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Umukozi Yasibwe"]);
    } else {
        echo json_encode(["success" => false, "message" => "Ntago bishobotse Gusiba Umukozi"]);
    }
}elseif($action == 'update'){
 $stmt = $pdo->prepare('
    UPDATE employees
    SET first_name = :first_name,
        last_name = :last_name,
        birth_date = :birth_date,
        national_id = :national_id,
        phone = :phone,
        email = :email,
        location = :location,
        punishment_amount = :punishment_amount,
        punishment = :punishment
    WHERE emp_id = :emp_id
');
$stmt->bindValue(':first_name', $form['first_name'], PDO::PARAM_STR);
$stmt->bindValue(':last_name', $form['last_name'], PDO::PARAM_STR);
$stmt->bindValue(':birth_date', $form['birth_date'], PDO::PARAM_STR);
$stmt->bindValue(':national_id', $form['national_id'], PDO::PARAM_STR);
$stmt->bindValue(':phone', $form['phone'], PDO::PARAM_STR);
$stmt->bindValue(':email', $form['email'], PDO::PARAM_STR);
$stmt->bindValue(':location', $form['location'], PDO::PARAM_STR);
$stmt->bindValue(':punishment_amount', $form['punishment_amount'], PDO::PARAM_STR);
$stmt->bindValue(':punishment', $form['punishment_desc'], PDO::PARAM_STR);
$stmt->bindValue(':emp_id', $form['emp_id'], PDO::PARAM_INT);
$stmt->execute();
if($stmt->rowCount()>0){
 echo json_encode(["success"=>true,'message'=>'Amakuru Yahinduwe Neza']);
}else{
  echo json_encode(["success"=>true,'message'=>'Amakuru Ntago Byashobotse Guhindura Amakuru']);
}
  
}
else if($action == "pay"){
    $stmt = $pdo->prepare("SELECT payment_status FROM payment WHERE emp_id = :emp_id");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();
    $payRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payRow) {
        echo json_encode(["success"=>false, "message"=>"UyuMukozi Ntabashije kuboneka"]);
        exit;
    }

    if ($payRow['payment_status'] === "PAID") {
        echo json_encode(["success"=>false, "message"=>"Uyumukozi  yarishyuwe"]);
        exit;
    }

    
    $stmt = $pdo->prepare("
        SELECT a.attended_day, d.department_salary, e.punishment_amount, e.punishment
        FROM attendance a
        JOIN employees e ON e.emp_id = a.emp_id
        JOIN department d ON d.department_id = e.department_id
        WHERE a.emp_id = :emp_id
    ");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["success"=>false, "message"=>"Ntaminsi Yakoze"]);
        exit;
    }

    $attended_day  = $row['attended_day'];
    $salary            = $row['department_salary'];
    $punishment_amount = $row['punishment_amount'];
    $punishment_desc   = $row['punishment'];

    
    $gross_amount = $attended_day * $salary;    
    $net_amount   = $gross_amount - $punishment_amount; 
    if ($net_amount < 0) $net_amount = 0;
    
    if($attended_day == 0){
       echo json_encode(["success"=>false, "message"=>"Uyu Mukozi Ntaminsi Yakoze "]);
       exit;
  
    }
  
    $stmt = $pdo->prepare("
        INSERT INTO payment_history
        (emp_id, payment_amount, punishment, punishment_desc, money_recieved)
        VALUES
        (:emp_id, :payment_amount, :punishment, :punishment_desc, :money_recieved)
    ");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->bindValue(':payment_amount', $gross_amount, PDO::PARAM_STR);
    $stmt->bindValue(':punishment', $punishment_amount, PDO::PARAM_STR);
    $stmt->bindValue(':punishment_desc', $punishment_desc, PDO::PARAM_STR);
    $stmt->bindValue(':money_recieved', $net_amount, PDO::PARAM_STR);
    $stmt->execute();

  
    $stmt = $pdo->prepare("UPDATE attendance SET attended_day = 0 WHERE emp_id = :emp_id");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt = $pdo->prepare("
        UPDATE employees
        SET punishment_amount = 0,
            punishment = 'Ntagihango afite'
        WHERE emp_id = :emp_id
    ");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $pdo->prepare("
        UPDATE payment SET payment_status = 'PAID'
        WHERE emp_id = :emp_id
    ");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success"           => true,
        "message"           => "Umukozi yishyuwe neza",
        "gross_amount"      => number_format($gross_amount, 2),
        "punishment_amount" => number_format($punishment_amount, 2),
        "punishment_desc"   => $punishment_desc,
        "money_recieved"    => number_format($net_amount, 2)
    ]);
    exit;

}else if($action == "attended") {

    // Get today's date from MySQL
    $stmt = $pdo->query("SELECT CURRENT_DATE as today");
    $today = $stmt->fetch(PDO::FETCH_ASSOC)['today'];

    // Check if employee exists
    $stmt = $pdo->prepare("SELECT emp_id FROM employees WHERE emp_id = :emp_id");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        echo json_encode(["success" => false, "message" => "Umukozi ntabonetse"]);
        exit;
    }

    // Check payment status
    $stmt = $pdo->prepare("SELECT payment_status FROM payment WHERE emp_id = :emp_id");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment && $payment['payment_status'] === "PAID") {
        // Mark as UNPAID
        $stmt = $pdo->prepare("UPDATE payment SET payment_status = 'UNPAID', paid_date = NULL WHERE emp_id = :emp_id");
        $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Check attendance record for today
    $stmt = $pdo->prepare("SELECT id, attended_day, attended_date FROM attendance WHERE emp_id = :emp_id");
    $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
    $stmt->execute();
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$attendance) {
        // No attendance yet, insert with attended_day = 1
        $stmt = $pdo->prepare("INSERT INTO attendance (emp_id, attended_day, attended_date) VALUES (:emp_id, 1, :today)");
        $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
        $stmt->bindValue(':today', $today, PDO::PARAM_STR);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Kwitabira kwanditswe!"]);
        exit;
    } else {
        // If already attended today, prevent duplicate
        if ($attendance['attended_date'] == $today) {
            echo json_encode(["success" => false, "message" => "Ntago Bishoboka ko Wa Kwitabira Incuro Irenze Imwe Kumunsi Umwe"]);
            exit;
        }

        // Otherwise, increment attended_day and set today's date
        $stmt = $pdo->prepare("UPDATE attendance SET attended_day = attended_day + 1, attended_date = :today WHERE emp_id = :emp_id");
        $stmt->bindValue(':emp_id', $emp_id, PDO::PARAM_INT);
        $stmt->bindValue(':today', $today, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Kwitabira kwanditswe"]);
        exit;
    }
}else if ($action === 'departma_get') {
    $stmt = $pdo->query("
        SELECT 
            d.department_id,
            d.department_name,
            d.department_salary,
            COUNT(e.emp_id) AS total_employees,
            COALESCE(SUM(a.attended_day), 0) AS total_attended_days,
            COALESCE(SUM(a.attended_day * d.department_salary), 0) AS total_department_salary
        FROM department d
        LEFT JOIN employees e ON d.department_id = e.department_id
        LEFT JOIN attendance a ON e.emp_id = a.emp_id
        GROUP BY d.department_id, d.department_name, d.department_salary
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} 

else if ($action === 'add_department') {
    $stmt = $pdo->prepare("INSERT INTO department (department_name, department_salary) VALUES (:name, :salary)");
    $stmt->execute([
        ':name' => $form['department_name'],
        ':salary' => $form['department_salary']
    ]);
    echo json_encode(["success" => true, "message" => "Byakunze !"]);
} 
else if ($action === 'update_department') {
    $stmt = $pdo->prepare("UPDATE department SET department_name = :name, department_salary = :salary WHERE department_id = :id");
    $stmt->execute([
        ':name' => $form['department_name'],
        ':salary' => $form['department_salary'],
        ':id' => $form['emp_id']
    ]);
    echo json_encode(["success" => true,"message"=>"byangenze neza!"]);
} 
// 
else if ($action === 'delete_department') {
    $stmt = $pdo->prepare("DELETE FROM department WHERE department_id = :id");
    $stmt->execute([':id' => $form['emp_id']]);
    echo json_encode(["success" => true,"message"=>"Departma Yakuwemo"]);
}else if($action == "abaje") {
    $sql = "
        SELECT
            e.emp_id,
            e.first_name,
            e.last_name,
            d.department_name,
            d.department_salary,
            a.attended_day,
            a.attended_date,
            (d.department_salary * a.attended_day) AS total_salary
        FROM attendance a
        JOIN employees e ON a.emp_id = e.emp_id
        JOIN department d ON e.department_id = d.department_id
        WHERE a.attended_date = CURRENT_DATE
    ";
    $params = [];
    if(!empty($search_query)) {
        $sql .= " AND (e.first_name LIKE :search OR e.last_name LIKE :search OR d.department_name LIKE :search)";
        $params[':search'] = "%$search_query%";
    }

    $sql .= " ORDER BY e.first_name, e.last_name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
    exit;
}
else {
    echo json_encode(["success" => false, "message" => "Unknown action"]);
}

?>
