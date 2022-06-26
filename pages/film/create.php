<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:25
 *
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(400);
    $reply['error'] = 'POST method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data POST
 */
$judul = $_POST['judul'] ?? '';
$sutradara = $_POST['sutradara'] ?? '';
$negara = $_POST['negara'] ?? 0;
$deskripsi = $_POST['deskripsi'] ?? '';
$tanggal = $_POST['tanggal'] ?? date('Y-m-d');
$kategori = $_POST['kategori'] ?? 0;

/**
 * Validation int value
 */

/**
 * Validation empty fields
 */
$isValidated = true;

if(empty($judul)){
    $reply['error'] = 'JUDUL harus diisi';
    $isValidated = false;
}
if(empty($sutradara)){
    $reply['error'] = 'sutradara harus diisi';
    $isValidated = false;
}
if(empty($deskripsi)){
    $reply['error'] = 'deskripsi harus diisi';
    $isValidated = false;
}
/*
 * Jika filter gagal
 */
if(!$isValidated){
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $query = "INSERT INTO film (judul, sutradara, tanggal, deskripsi, kategori, negara) 
VALUES (:judul, :sutradara, :tanggal, :deskripsi, :kategori, :negara)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":judul", $judul);
    $statement->bindValue(":sutradara", $sutradara);
    $statement->bindValue(":negara", $negara, PDO::PARAM_INT);
    $statement->bindValue(":tanggal", $tanggal);
    $statement->bindValue(":deskripsi", $deskripsi);
    $statement->bindValue(":kategori", $kategori, PDO::PARAM_INT);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
    $id = $connection->lastInsertId();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/*
 * Get last data
 */
$getResult = "SELECT * FROM film WHERE id = :id";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id', $id);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/*
 * Get kategori
 */
$stmKategori = $connection->prepare("SELECT * FROM kategori where id = :id");
$stmKategori->bindValue(':id', $result['kategori']);
$stmKategori->execute();
$resultKategori = $stmKategori->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat kategori 'Tidak diketahui'
 */
$kategori = [
    'id' => $result['kategori'],
    'nama' => 'Tidak diketahui'
];
if ($resultKategori) {
    $kategori = [
        'id' => $resultKategori['id'],
        'nama' => $resultKategori['nama']
    ];
}

/*
 * Get negara
 */
$stmNegara = $connection->prepare("SELECT * FROM negara where id = :id");
$stmNegara->bindValue(':id', $result['negara']);
$stmNegara->execute();
$resultNegara = $stmNegara->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat negara 'Tidak diketahui'
 */
$negara = [
    'id' => $result['negara'],
    'nama' => 'Tidak diketahui'
];
if ($resultNegara) {
    $negara = [
        'id' => $resultNegara['id'],
        'nama' => $resultNegara['nama']
    ];
}

/*
 * Transform result
 */
$dataFinal = [
    'id' => $result['id'],
    'judul' => $result['judul'],
    'sutradara' => $result['sutradara'],
    'tanggal' => $result['tanggal'],
    
    'created_at' => $result['created_at'],
    'kategori' => $kategori,
    'negara' => $negara,
];

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);