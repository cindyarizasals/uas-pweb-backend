<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:21
 */
$dbuser = "root";
$dbpassword = "";
$dbserver = "localhost";
$dbname = "kuliahweb";

$dsn = "mysql:host={$dbserver};dbname={$dbname}";

$connection = null;
try{
    $connection = new PDO($dsn, $dbuser, $dbpassword);
}catch (Exception $exception){
    die($exception->getMessage());
}