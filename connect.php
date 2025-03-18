<?php
define('DB_HOST', 'MySQL-8.2');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'StudWork');

try{
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch (Exception $ex){
    echo "Ошибка подключения: ".$ex->getMessage();
}

function loger($method, $path, $code){
    $log = date('H:i:s')." - $method to '$path', $code\n";
    file_put_contents('log.txt', $log, FILE_APPEND);
}