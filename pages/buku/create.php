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
$isbn = $_POST['isbn'] ?? '';
$judul = $_POST['judul'] ?? '';
$pengarang = $_POST['pengarang'] ?? '';
$jumlah = $_POST['jumlah'] ?? 0;
$abstrak = $_POST['abstrak'] ?? '';
$tanggal = $_POST['tanggal'] ?? date('Y-m-d');

/**
 * Validation int value
 */
$jumlahFilter = filter_var($jumlah, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($jumlahFilter === false){
    $reply['error'] = "Jumlah harus format INT";
    $isValidated = false;
}
if(empty($isbn)){
    $reply['error'] = 'ISBN harus diisi';
    $isValidated = false;
}
if(empty($judul)){
    $reply['error'] = 'JUDUL harus diisi';
    $isValidated = false;
}
if(empty($pengarang)){
    $reply['error'] = 'PENGARANG harus diisi';
    $isValidated = false;
}
if(empty($abstrak)){
    $reply['error'] = 'ABSTRAK harus diisi';
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
    $query = "INSERT INTO buku (isbn, judul, pengarang, jumlah, tanggal, abstrak) 
VALUES (:isbn, :judul, :pengarang, :jumlah, :tanggal, :abstrak)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":isbn", $isbn);
    $statement->bindValue(":judul", $judul);
    $statement->bindValue(":pengarang", $pengarang);
    $statement->bindValue(":jumlah", $jumlah, PDO::PARAM_INT);
    $statement->bindValue(":tanggal", $tanggal);
    $statement->bindValue(":abstrak", $abstrak);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
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
$getResult = "SELECT * FROM buku WHERE isbn = :isbn";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id', $isbn);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);