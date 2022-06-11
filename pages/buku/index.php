<?php
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:22
 * @var $connection PDO
 */
try{
    /**
     * Prepare query buku limit 50 rows
     */
    $statement = $connection->prepare("select * from buku order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsBuku = $statement->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data kategori
     */
    $stmKategori = $connection->prepare("select * from kategori");
    $isOk = $stmKategori->execute();
    $resultKategori = $stmKategori->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Transoform hasil query dari table buku dan kategori
     * Gabungkan data berdasarkan kolom id kategori
     * Jika id kategori tidak ditemukan, default "tidak diketahui'
     */
    $finalResults = [];
    $idsKetegori = array_column($resultKategori, 'id');
    foreach ($resultsBuku as $buku){
        /*
         * Default kategori 'Tidak diketahui'
         */
        $kategori = [
            'id' => $buku['kategori'],
            'nama' => 'Tidak diketahui'
        ];
        /*
         * Cari kategori berd id
         */
        $findByIdKategori = array_search($buku['kategori'], $idsKetegori);

        /*
         * Jika id ditemukan
         */
        if($findByIdKategori !== false){
            $findDataKategori = $resultKategori[$findByIdKategori];
            $kategori = [
                'id' => $findDataKategori['id'],
                'nama' => $findDataKategori['nama']
            ];
        }

        $finalResults[] = [
            'isbn' => $buku['isbn'],
            'judul' => $buku['judul'],
            'pengarang' => $buku['pengarang'],
            'tanggal' => $buku['tanggal'],
            'jumlah' => $buku['jumlah'],
            'created_at' => $buku['created_at'],
            'kategori' => $kategori,
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