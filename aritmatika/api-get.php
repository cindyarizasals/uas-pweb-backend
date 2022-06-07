<?php
//ambil data dari value array
$var1 = $_GET['var1'];
$var2 = $_GET['var2'];
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