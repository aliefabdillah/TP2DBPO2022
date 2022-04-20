<?php

include("conf.php");
include("./include/Template.php");
include("./include/DB.php");
include("./include/Divisi.php");
include("./include/Bidang.php");
include("./include/Pengurus.php");

//=============================================================
/* TABEL PENGURUS */
$pengurus = new Pengurus($db_host, $db_user, $db_pass, $db_name);
$pengurus->open();
$bidang = new Bidang($db_host, $db_user, $db_pass, $db_name);
$bidang->open();
$divisi = new Divisi($db_host, $db_user, $db_pass, $db_name);
$divisi->open();

// jika nim kosong
if (!empty($_GET['nim'])) {
    //ambil nim dan ambil data pengurus by nim
    $nim = $_GET['nim'];
    $pengurus->getPengurusById($nim);
}
else{
    echo "<div class='alert alert-danger'> DATA TIDAK ADA.</div>";
}

//mengecek apakah ada id_hapus, jika ada maka memanggil fungsi delete
if (!empty($_GET['id_hapus'])) {
    //memanggil add
    $id = $_GET['id_hapus'];

    // menghapus foto dari local file
    $pengurus->getPengurusById($id);
    $foto_hapus = $pengurus->getResult();
    unlink("assets/".$foto_hapus['foto']);

    //hapus pengurus
    $pengurus->deletePengurus($id);
    header("location:index.php");
}
else if (!empty($_GET['id_edit'])) {
    //memanggil update
    $id_upd = $_GET['id_edit'];
    header("location:form.php?nim_old=$id_upd");
}

/* menampilkan data pengurus */
$data_pengurus = null;
while (list($nim, $nama, $semester, $foto, $id_bidang) = $pengurus->getResult()) {
    // memanggil data nama bidang dari tabel bidang
    $bidang->getBidangById($id_bidang);
    $data_bidang = $bidang->getResult();
    
    // mengambil nama divisi dari tabel divisi
    $divisi->getDivisiById($data_bidang['id_divisi']);
    $data_divisi = $divisi->getResult();

    $data_pengurus .=
    "<tr>
        <td>". $nim ."</td>
        <td>". $nama ."</td>
        <td>". $semester ."</td>
        <td>". $foto ."</td>
        <td>". $data_divisi['nama_divisi'] ."</td>
        <td>". $data_bidang['jabatan'] ."</td>
        <td>
        <a href='detail_pengurus.php?id_edit=" . $nim .  "' class='btn btn-warning'> Update </a>
        <a class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#exampleModal'> Hapus </a>
        </td>
    </tr>
    <!-- Modal -->
        <div class='modal fade' id='exampleModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                <h5 class='modal-title' id='exampleModalLabel'>Hapus Data</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    Apakah anda yakin ingin menghapus data?
                </div>
                <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                <a href='detail_pengurus.php?id_hapus=" . $nim . "'><button type='button' class='btn btn-danger'>Yes</button></a>
                </div>
            </div>
            </div>
        </div>
    "
    ;
}

// close koneksi db
$divisi->close();
$bidang->close();
$pengurus->close();
// menyimpan kode ke dalam html
$tpl = new Template("template/detail_pengurus.html");
$tpl->replace("DATA_PENGURUS", $data_pengurus);
$tpl->write();
//=============================================================
?>