<?php
$host = "sql113.infinityfree.com";

$username = "if0_40294956";

$password = "iJkqJhqLR1";

$dbname = "if0_40294956_management";

try{
  $dns = "mysql:host=$host;dbname=$dbname";
  $pdo = new PDO($dns,$username,$password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  $path = "log/erro.txt";
  $dir = dirname($path);
  if(!is_dir($dir)){
    mkdir($dir,0755,true);
  }
  $err_message = date("Y-m-d H:i:s")." - ".$e->getMessage().PHP_EOL;
 file_put_contents($path,$err_message,FILE_APPEND);
}
?>