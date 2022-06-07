<?php
/*
 * Ambil RAW data request
 */
$body = file_get_contents('php://input');
// convert JSON ke array
$arrayBody = json_decode($body, true);
//ambil data dari value array
$var1 = $arrayBody['var1'];
$var2 = $arrayBody['var2'];
// Diproses
$hasil = $var1 * $var2;

$data = [
    'perkalian' => [
        'var1' => $var1,
        'var2' => $var2,
        'hasil' => $hasil
    ]
];
header('Content-Type: application/json');
echo json_encode($data);