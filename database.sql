create database kuliahweb;

use kuliahweb;

create table buku (
    isbn varchar(15) primary key,
    judul varchar(255),
    pengarang varchar(255),
    jumlah int,
    tanggal date,
    abstrak text
)

insert into buku values ("1234567891", "Buku 1", "Budi 1", 12, "2022-01-01", "Abstrak 1.....");
insert into buku values ("1234567892", "Buku 2", "Budi 2", 12, "2022-01-02", "Abstrak 2.....");
insert into buku values ("1234567893", "Buku 3", "Budi 3", 12, "2022-01-03", "Abstrak 3.....");
insert into buku values ("1234567894", "Buku 4", "Budi 4", 12, "2022-01-04", "Abstrak 4.....");
insert into buku values ("1234567895", "Buku 5", "Budi 5", 12, "2022-01-05", "Abstrak 5.....");