<?php

include("conf.php");
include("./include/Template.php");
include("./include/DB.php");
include("./include/Divisi.php");
include("./include/Bidang.php");
include("./include/Pengurus.php");

//=============================================================
// instansiasi class 
$bidang = new Bidang($db_host, $db_user, $db_pass, $db_name);
$bidang->open();
$divisi = new Divisi($db_host, $db_user, $db_pass, $db_name);
$divisi->open();
$index = new Pengurus($db_host, $db_user, $db_pass, $db_name);
$index->open();

// mengambil data pengurus
$index->getPengurus();

// menampilkan data pengurus dalam bentuk card
$data_index = null;
while (list($nim, $nama, $semester, $foto, $id_bidang) = $index->getResult()) {
    $bidang->getBidangById($id_bidang);
    $data_bidang = $bidang->getResult();

    $divisi->getDivisiById($data_bidang['id_divisi']);
    $data_divisi = $divisi->getResult();

    $data_index .=
    "
    <div class='col-md-3 col-sm-12 mb-3 justify-content-center'>
        <div class='card shadow bg-light p-3' style='width: 15rem;'>
            <img src='assets/$foto' class='card-img-top'>
            <div class='card-body'>
                <p class='card-text'>". $nama ."</p>
                <p class='card-text'>". $data_bidang['jabatan']. " ". $data_divisi['nama_divisi'] ."</p>
                <a href='./detail_pengurus.php?nim=$nim'><button class='btn btn-sm btn-primary' type='submit'>Detail Pengurus</button></a>
            </div>
        </div>
    </div>
    ";
    
}

// tutup koneksi db
$divisi->close();
$bidang->close();
$index->close();

$tpl = new Template("template/index.html");
$tpl->replace("DATA_INDEX", $data_index);
$tpl->write();
//=============================================================
?>