<?php
$dbuser = "root";
$dbpassword = "";
$dbserver = "localhost";
$dbname = "pweb-backend";

$dsn = "mysql:host={$dbserver};dbname={$dbname}";

$connection = null;
try{
    $connection = new PDO($dsn, $dbuser, $dbpassword);
}catch (Exception $exception){
    $response['error'] = $exception->getMessage();
    echo json_encode($response);
    die();
}