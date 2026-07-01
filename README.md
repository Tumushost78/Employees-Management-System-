# Employees-Management-System
default credentails <br>

username: umuhungu <br>
password:umuhungu <br>

![image](https://github.com/Tumushost78/Employees-Management-System-/blob/main/Screenshot_2026-07-01_15_28_42.png)
![image](https://github.com/Tumushost78/Employees-Management-System-/blob/main/Screenshot_2026-07-01_15_29_00.png)
![image](https://github.com/Tumushost78/Employees-Management-System-/blob/main/Screenshot_2026-07-01_15_32_13.png)

change the credential to configure database
backend/connect.php
```php
<?php

<?php
$host = "your hostname ";

$username = "user name ";

$password = "your password here";

$dbname = "management";
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
```
create database called management
```sql
create schema management;
use  management;
exit;
mysql -u username -p management < path/to/managemen.sql

```
Install composer for generating pdf inside backend/
```bash
composer require dompdf/dompdf:^3.1
```







