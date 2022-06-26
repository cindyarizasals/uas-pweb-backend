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
$id = $_GET['id'] ?? '';

if(empty($id)){
    $reply['error'] = 'id tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM film where id = :id";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id', $id);
    $statement->execute();
    $dataFilm = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori berdasarkan kolom kategori
     */
    if($dataFilm) {
        $stmKategori = $connection->prepare("select * from kategori where id = :id");
        $stmKategori->bindValue(':id', $dataFilm['kategori']);
        $stmKategori->execute();
        $resultKategori = $stmKategori->fetch(PDO::FETCH_ASSOC);
        /*
         * Defulat kategori 'Tidak diketahui'
         */
        $kategori = [
            'id' => $dataFilm['kategori'],
            'nama' => 'Tidak diketahui'
        ];
        if ($resultKategori) {
            $kategori = [
                'id' => $resultKategori['id'],
                'nama' => $resultKategori['nama']
            ];
        }
        $stmNegara = $connection->prepare("select * from negara where id = :id");
        $stmNegara->bindValue(':id', $dataFilm['negara']);
        $stmNegara->execute();
        $resultNegara = $stmNegara->fetch(PDO::FETCH_ASSOC);
        /*
         * Defulat negara 'Tidak diketahui'
         */
        $negara = [
            'id' => $dataFilm['negara'],
            'nama' => 'Tidak diketahui'
        ];
        if ($resultNegara) {
            $negara = [
                'id' => $resultNegara['id'],
                'nama' => $resultNegara['nama']
            ];
        }

        /*
         * Transoform hasil query dari table buku dan kategori
         * Gabungkan data berdasarkan kolom id kategori
         * Jika id kategori tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id' => $dataFilm['id'],
            'judul' => $dataFilm['judul'],
            'sutradara' => $dataFilm['sutradara'],
            'tanggal' => $dataFilm['tanggal'],
            'created_at' => $dataFilm['created_at'],
            'kategori' => $kategori,
            'negara' => $negara,
            'deskripsi' => $dataFilm['deskripsi'],
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
    $reply['error'] = 'Data tidak ditemukan id '.$id;
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