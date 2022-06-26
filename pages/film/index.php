<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:22
 * @var $connection PDO
 */
try{
    /**
     * Prepare query film limit 50 rows
     */
    $statement = $connection->prepare("select * from film order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsFilm = $statement->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori
     */
    $stmKategori = $connection->prepare("select * from kategori");
    $isOk = $stmKategori->execute();
    $resultKategori = $stmKategori->fetchAll(PDO::FETCH_ASSOC);
    
    /*
     * Ambil data negara
     */
    $stmNegara = $connection->prepare("select * from negara");
    $isOk = $stmNegara->execute();
    $resultNegara = $stmNegara->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Transoform hasil query dari table film dan kategori
     * Gabungkan data berdasarkan kolom id kategori
     * Jika id kategori tidak ditemukan, default "tidak diketahui'
     */
    $finalResults = [];
    $idsKetegori = array_column($resultKategori, 'id');
    $idsNegara = array_column($resultNegara, 'id');
    foreach ($resultsFilm as $film){
        /*
         * Default kategori 'Tidak diketahui'
         */
        $kategori = [
            'id' => $film['kategori'],
            'nama' => 'Tidak diketahui'
        ];
        /*
         * Default negara 'Tidak diketahui'
         */
        $negara = [
            'id' => $film['negara'],
            'nama' => 'Tidak diketahui'
        ];
        /*
         * Cari kategori berd id
         */
        $findByIdKategori = array_search($film['kategori'], $idsKetegori);
        /*
         * Cari negara berd id
         */
        $findByIdNegara = array_search($film['negara'], $idsNegara);

        /*
         * Jika id kategori ditemukan
         */
        if($findByIdKategori !== false){
            $findDataKategori = $resultKategori[$findByIdKategori];
            $kategori = [
                'id' => $findDataKategori['id'],
                'nama' => $findDataKategori['nama']
            ];
        }
        
        /*
         * Jika id negara ditemukan
         */
        if($findByIdNegara !== false){
            $findDataNegara = $resultNegara[$findByIdNegara];
            $negara = [
                'id' => $findDataNegara['id'],
                'nama' => $findDataNegara['nama']
            ];
        }

        $finalResults[] = [
            'id' => $film['id'],
            'judul' => $film['judul'],
            'sutradara' => $film['sutradara'],
            'tanggal' => $film['tanggal'],
            'created_at' => $film['created_at'],
            'kategori' => $kategori,
            'negara' => $negara,
            'deskripsi' => $film['deskripsi']
        ];
    }

    $reply['data'] = $finalResults;
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
/*
 * Query OK
 * set status == true
 * Output JSON
 */
$reply['status'] = true;
echo json_encode($reply);