<?php 
include 'koneksi.php';
$id = $nim = $nama = $jurusan = "";
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $res = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id=$id");
    $data = mysqli_fetch_assoc($res);
    $nim = $data['nim'];
    $nama = $data['nama'];
    $jurusan = $data['jurusan'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Mahasiswa</title>
</head>
<body>
    <h2>Form <?= $id ? "Edit" : "Tambah" ?> Data</h2>
    <form action="proses.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="text" id="nim" name="nim" placeholder="NIM" value="<?= $nim ?>"><br><br>
        <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" value="<?= $nama ?>"><br><br>
        <input type="text" id="jurusan" name="jurusan" placeholder="Jurusan" value="<?= $jurusan ?>"><br><br>
        <input type="file" id="foto" name="foto"><br><small>*Max 2MB, JPG/PNG</small><br><br>
        <button type="submit" name="simpan">Simpan Data</button>
    </form>

    <script src="script.js"></script>
</body>
</html>