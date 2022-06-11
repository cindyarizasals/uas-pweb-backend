<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 09/06/2022
 * Time: 16:19
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

$dataFinal = [];
$isbn = $_GET['isbn'] ?? '';

if(empty($isbn)){
    $reply['error'] = 'ISBN tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM buku where isbn = :isbn";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':isbn', $isbn);
    $statement->execute();
    $dataBuku = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori berdasarkan kolom kategori
     */
    if($dataBuku) {
        $stmKategori = $connection->prepare("select * from kategori where id = :id");
        $stmKategori->bindValue(':id', $dataBuku['kategori']);
        $stmKategori->execute();
        $resultKategori = $stmKategori->fetch(PDO::FETCH_ASSOC);
        /*
         * Defulat kategori 'Tidak diketahui'
         */
        if (!$resultKategori) {
            $resultKategori = [
                'id' => $dataBuku['kategori'],
                'nama' => 'Tidak diketahui'
            ];
        }

        /*
         * Transoform hasil query dari table buku dan kategori
         * Gabungkan data berdasarkan kolom id kategori
         * Jika id kategori tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal[] = [
            'isbn' => $dataBuku['isbn'],
            'judul' => $dataBuku['judul'],
            'pengarang' => $dataBuku['pengarang'],
            'tanggal' => $dataBuku['tanggal'],
            'jumlah' => $dataBuku['jumlah'],
            'created_at' => $dataBuku['created_at'],
            'kategori' => $resultKategori,
        ];
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Show response
 */
if(!$dataFinal){
    $reply['error'] = 'Data tidak ditemukan ISBN '.$isbn;
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Otherwise show data
 */
$reply['status'] = true;
$reply['data'] = $dataFinal;
echo json_encode($reply);