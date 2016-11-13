<?php
// Старт сессии
session_start();
global $dbh;
include_once 'module.php';

// Параметры потключения к базе
$db_host = 'localhost';
$db_login = 'root';
$db_passwd = '';
$db_name = 'auth';

// Подключение
try {
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_login, $db_passwd, array(
    PDO::ATTR_PERSISTENT => true));        
} 
catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
