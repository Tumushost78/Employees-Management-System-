<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

include "connect.php";
$empId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
if (!$empId) die("ID y'umukozi ntiboneka!");

$stmt = $pdo->prepare("
    SELECT e.*, d.department_name, d.department_salary
    FROM employees e
    JOIN department d ON e.department_id = d.department_id
    WHERE e.emp_id = :emp_id
");
$stmt->execute(['emp_id' => $empId]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    die("Nta makuru aboneka ku mukozi ufite ID $empId");
}

// Fetch payment history
$stmt2 = $pdo->prepare("
    SELECT payment_amount, punishment, punishment_desc, money_recieved, payment_date, time_at
    FROM payment_history
    WHERE emp_id = :emp_id
    ORDER BY payment_date ASC
");
$stmt2->execute(['emp_id' => $empId]);
$payments = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_salary = 0;
$total_punishment = 0;
$total_received = 0;
foreach ($payments as $p) {
    $total_salary += $p['payment_amount'];
    $total_punishment += $p['punishment'];
    $total_received += $p['money_recieved'];
}


$html = '<html><head><style>
body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; font-size:12px; margin:0; padding:0; color:#333; }
.header { text-align:center; padding:20px; border-bottom:2px solid #4CAF50; }
.header img { height:60px; }
.header h2 { margin:10px 0 0 0; font-size:20px; }
.employee-details, .summary { padding:20px; margin:20px; border:1px solid #ccc; border-radius:8px; background-color:#f9f9f9; }
.employee-details h3 { margin:0 0 10px 0; color:#2F4F4F; }
.employee-details p { margin:4px 0; }
.summary { display:flex; justify-content:space-around; text-align:center; margin-bottom:30px; }
.summary div { background:#eaf5ea; padding:10px 20px; border-radius:6px; width:30%; box-shadow:1px 1px 4px rgba(0,0,0,0.1); }
.summary div h4 { margin:0; color:#4CAF50; }
.summary div p { margin:5px 0 0 0; font-size:14px; font-weight:bold; }
table { width:95%; margin:20px auto; border-collapse:collapse; font-size:12px; }
th, td { border-bottom:1px solid #ccc; padding:10px; text-align:left; }
th { background-color:#4CAF50; color:white; text-align:center; }
tr:nth-child(even) { background-color:#f9f9f9; }
tfoot td { font-weight:bold; border-top:2px solid #4CAF50; }
.amount { text-align:right; }
</style></head><body>';

$html .= '<div class="header">
            <img src="assets/AT-pro-logo.png" alt="IYUMVIRE LTD">
            <h2>Raporo y\'Umukozi</h2>
          </div>';

$html .= '<div class="employee-details">
            <h3>'.$employee['first_name'].' '.$employee['last_name'].'</h3>
            <p><strong>Departma:</strong> '.$employee['department_name'].' | <strong>Umushahara w’departma:</strong> '.number_format($employee['department_salary'],2).' Frw</p>
            <p><strong>Itariki Yatangiye:</strong> '.$employee['registered_date'].' | 
            <p><strong>Telephone:</strong> '.$employee['phone'].' | <strong>Email:</strong> '.($employee['email'] ?? "N/A").'</p>
            <p><strong>Aho atuye:</strong> '.$employee['location'].' | <strong>ID:</strong> '.$employee['national_id'].'</p>
          </div>';

// Summary
$html .= '<div class="summary">
            <div>
                <h4>Umushahara Wose</h4>
                <p>'.number_format($total_salary,2).' Frw</p>
            </div>
            <div>
                <h4>Amafaranga Yigihano Cyose</h4>
                <p>'.number_format($total_punishment,2).' Frw</p>
            </div>
            <div>
                <h4>Amafaranga Yakiriye</h4>
                <p>'.number_format($total_received,2).' Frw</p>
            </div>
          </div>';


if (count($payments) > 0) {
    $html .= '<table>
                <thead>
                    <tr>
                        <th>Itariki</th>
                        <th>Igihe</th>
                        <th>Umushahara Uhabwa</th>
                        <th>Igihano</th>
                        <th>Igihano Kivuzwe</th>
                        <th>Umushahara Wakiriwe</th>
                    </tr>
                </thead>
                <tbody>';
    foreach ($payments as $p) {
        $html .= '<tr>
                    <td>'.$p['payment_date'].'</td>
                    <td>'.$p['time_at'].'</td>
                    <td class="amount">'.number_format($p['payment_amount'],2).'</td>
                    <td class="amount">'.number_format($p['punishment'],2).'</td>
                    <td>'.$p['punishment_desc'].'</td>
                    <td class="amount">'.number_format($p['money_recieved'],2).'</td>
                  </tr>';
    }
    $html .= '</tbody>
              <tfoot>
                <tr>
                    <td colspan="5">Umushahara Wose Wakiriwe</td>
                    <td class="amount">'.number_format($total_received,2).'</td>
                </tr>
              </tfoot>
              </table>';
} else {
    $html .= '<p style="text-align:center; color:red; margin-top:20px;">Nta mateka y’ubwishyu aboneka kuri uyu mukozi</p>';
}

$html .= '</body></html>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$dompdf->stream("Raporo_yumukozi_$empId.pdf", ["Attachment" => false]);

?>
