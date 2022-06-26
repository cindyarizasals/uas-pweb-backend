<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 02/06/2022
 * Time: 20:07
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'PATCH'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'PATCH method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$id = $formData['id'] ?? '';
$judul = $formData['judul'] ?? '';
$sutradara = $formData['sutradara'] ?? '';

$deskripsi = $formData['deskripsi'] ?? '';
$tanggal = $formData['tanggal'] ?? date('Y-m-d');
$idKategori = $formData['kategori'] ?? 0;
$idNegara = $formData['negara'] ?? 0;

/**
 * Validation int value
 */


/**
 * Validation empty fields
 */
$isValidated = true;

if(empty($id)){
    $reply['error'] = 'id harus diisi';
    $isValidated = false;
}
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM film where id = :id";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id', $id);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan id '.$id;
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE film SET judul = :judul, sutradara = :sutradara, tanggal = :tanggal, deskripsi = :deskripsi, kategori = :kategori, negara = :negara
WHERE id = :id";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id", $id);
    $statement->bindValue(":judul", $judul);
    $statement->bindValue(":sutradara", $sutradara);
    $statement->bindValue(":tanggal", $tanggal);
    $statement->bindValue(":deskripsi", $deskripsi);
    $statement->bindValue(":kategori", $idKategori, PDO::PARAM_INT);
    $statement->bindValue(":negara", $idNegara, PDO::PARAM_INT);
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
 * Get data
 */
$stmSelect = $connection->prepare("SELECT * FROM film where id = :id");
$stmSelect->bindValue(':id', $id);
$stmSelect->execute();
$datafilm = $stmSelect->fetch(PDO::FETCH_ASSOC);

/*
 * Ambil data kategori berdasarkan kolom kategori
 */
$dataFinal = [];
if($datafilm) {
    $stmKategori = $connection->prepare("select * from kategori where id = :id");
    $stmKategori->bindValue(':id', $datafilm['kategori']);
    $stmKategori->execute();
    $resultKategori = $stmKategori->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat kategori 'Tidak diketahui'
     */
    $kategori = [
        'id' => $datafilm['kategori'],
        'nama' => 'Tidak diketahui'
    ];
    if ($resultKategori) {
        $kategori = [
            'id' => $resultKategori['id'],
            'nama' => $resultKategori['nama']
        ];
    }

    /*
     * Transoform hasil query dari table film dan kategori
     * Gabungkan data berdasarkan kolom id kategori
     * Jika id kategori tidak ditemukan, default "tidak diketahui'
     */
    $dataFinal = [
        'id' => $datafilm['id'],
        'judul' => $datafilm['judul'],
        'sutradara' => $datafilm['sutradara'],
        'tanggal' => $datafilm['tanggal'],

        'created_at' => $datafilm['created_at'],
        'kategori' => $kategori,
        'deskripsi' => $datafilm['deskripsi'],
    ];
}

/*
 * Ambil data negara berdasarkan kolom negara
 */
$dataFinal = [];
if($datafilm) {
    $stmNegara = $connection->prepare("select * from negara where id = :id");
    $stmNegara->bindValue(':id', $datafilm['negara']);
    $stmNegara->execute();
    $resultNegara = $stmNegara->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat negara 'Tidak diketahui'
     */
    $negara = [
        'id' => $datafilm['negara'],
        'nama' => 'Tidak diketahui'
    ];
    if ($resultNegara) {
        $negara = [
            'id' => $resultNegara['id'],
            'nama' => $resultNegara['nama']
        ];
    }

    /*
     * Transoform hasil query dari table film dan negara
     * Gabungkan data berdasarkan kolom id negara
     * Jika id negara tidak ditemukan, default "tidak diketahui'
     */
    $dataFinal = [
        'id' => $datafilm['id'],
        'judul' => $datafilm['judul'],
        'sutradara' => $datafilm['sutradara'],
        'tanggal' => $datafilm['tanggal'],
        'negara' => $negara,
        'created_at' => $datafilm['created_at'],
        'kategori' => $kategori,
        'deskripsi' => $datafilm['deskripsi'],
    ];
}

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
echo json_encode($reply);